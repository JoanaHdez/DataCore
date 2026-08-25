<?php

namespace App\Modules\Asuntos_internos\GestionExcel\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use RuntimeException;
use ZipArchive;

class ExcelDateFormatService
{
    private const FORMATO_FECHA = 'dd/mm/yyyy';

    /**
     * Procesa una copia del Excel y cambia únicamente el estilo visual
     * de las celdas que Excel ya reconoce como fechas.
     */
    public function procesar(string $rutaOrigen, string $rutaDestino): array
    {
        if (! is_file($rutaOrigen)) {
            throw new RuntimeException('No se encontró el archivo que se desea procesar.');
        }

        if (! copy($rutaOrigen, $rutaDestino)) {
            throw new RuntimeException('No fue posible crear la copia de procesamiento.');
        }

        $zip = new ZipArchive();

        if ($zip->open($rutaDestino) !== true) {
            throw new RuntimeException('No fue posible abrir la estructura interna del Excel.');
        }

        try {
            $stylesXml = $zip->getFromName('xl/styles.xml');

            if ($stylesXml === false) {
                throw new RuntimeException('El Excel no contiene un archivo de estilos válido.');
            }

            $resultadoEstilos = $this->prepararEstilos($stylesXml);

            $estilosFecha = $resultadoEstilos['estilos_fecha'];
            $nuevosEstilos = $resultadoEstilos['nuevos_estilos'];
            $stylesModificado = $resultadoEstilos['xml'];

            if ($estilosFecha === []) {
                return [
                    'fechas_modificadas' => 0,
                    'hojas_revisadas'    => 0,
                ];
            }

            $totalFechas = 0;
            $hojasRevisadas = 0;

            for ($indice = 0; $indice < $zip->numFiles; $indice++) {
                $nombreEntrada = $zip->getNameIndex($indice);

                if (
                    ! is_string($nombreEntrada)
                    || ! preg_match('#^xl/worksheets/sheet\d+\.xml$#', $nombreEntrada)
                ) {
                    continue;
                }

                $worksheetXml = $zip->getFromName($nombreEntrada);

                if ($worksheetXml === false) {
                    continue;
                }

                $resultadoHoja = $this->actualizarHoja(
                    $worksheetXml,
                    $estilosFecha,
                    $nuevosEstilos
                );

                $hojasRevisadas++;
                $totalFechas += $resultadoHoja['modificadas'];

                if ($resultadoHoja['modificadas'] > 0) {
                    $zip->addFromString($nombreEntrada, $resultadoHoja['xml']);
                }
            }

            if ($totalFechas > 0) {
                $zip->addFromString('xl/styles.xml', $stylesModificado);
            }

            return [
                'fechas_modificadas' => $totalFechas,
                'hojas_revisadas'    => $hojasRevisadas,
            ];
        } finally {
            $zip->close();
        }
    }

    private function prepararEstilos(string $xml): array
    {
        $dom = $this->cargarXml($xml);
        $xpath = new DOMXPath($dom);

        $namespace = $dom->documentElement?->namespaceURI;

        if (! $namespace) {
            throw new RuntimeException('No se encontró el namespace del archivo de estilos.');
        }

        $xpath->registerNamespace('x', $namespace);

        $formatosPersonalizados = [];

        foreach ($xpath->query('//x:numFmts/x:numFmt') ?: [] as $numFmt) {
            if (! $numFmt instanceof DOMElement) {
                continue;
            }

            $id = (int) $numFmt->getAttribute('numFmtId');
            $formatosPersonalizados[$id] = $numFmt->getAttribute('formatCode');
        }

        $cellXfs = $xpath->query('//x:cellXfs')->item(0);

        if (! $cellXfs instanceof DOMElement) {
            throw new RuntimeException('No se encontró la colección de estilos de celdas.');
        }

        $estilosFecha = [];
        $nuevosEstilos = [];

        $xfsOriginales = [];

        foreach ($cellXfs->childNodes as $nodo) {
            if ($nodo instanceof DOMElement && $nodo->localName === 'xf') {
                $xfsOriginales[] = $nodo;
            }
        }

        $nuevoNumFmtId = $this->obtenerNuevoNumFmtId($formatosPersonalizados);
        $this->agregarFormatoPersonalizado($dom, $xpath, $nuevoNumFmtId);

        foreach ($xfsOriginales as $indice => $xf) {
            $numFmtId = (int) $xf->getAttribute('numFmtId');

            if (! $this->esFormatoFecha($numFmtId, $formatosPersonalizados)) {
                continue;
            }

            $estilosFecha[$indice] = true;

            $clon = $xf->cloneNode(true);

            if (! $clon instanceof DOMElement) {
                continue;
            }

            $clon->setAttribute('numFmtId', (string) $nuevoNumFmtId);
            $clon->setAttribute('applyNumberFormat', '1');

            $cellXfs->appendChild($clon);

            $nuevoIndice = count($xfsOriginales) + count($nuevosEstilos);
            $nuevosEstilos[$indice] = $nuevoIndice;
        }

        $cellXfs->setAttribute(
            'count',
            (string) count(
                array_filter(
                    iterator_to_array($cellXfs->childNodes),
                    static fn ($nodo) =>
                        $nodo instanceof DOMElement
                        && $nodo->localName === 'xf'
                )
            )
        );

        return [
            'estilos_fecha'  => $estilosFecha,
            'nuevos_estilos' => $nuevosEstilos,
            'xml'            => $dom->saveXML(),
        ];
    }

    private function actualizarHoja(
        string $xml,
        array $estilosFecha,
        array $nuevosEstilos
    ): array {
        $dom = $this->cargarXml($xml);
        $xpath = new DOMXPath($dom);

        $namespace = $dom->documentElement?->namespaceURI;

        if (! $namespace) {
            return [
                'modificadas' => 0,
                'xml'         => $xml,
            ];
        }

        $xpath->registerNamespace('x', $namespace);

        $modificadas = 0;

        foreach ($xpath->query('//x:sheetData/x:row/x:c[@s]') ?: [] as $celda) {
            if (! $celda instanceof DOMElement) {
                continue;
            }

            $tipo = $celda->getAttribute('t');

            /*
             * No modifica textos, booleanos, errores ni cadenas compartidas.
             * Las fechas reales de Excel normalmente son valores numéricos
             * acompañados de un estilo de fecha.
             */
            if (in_array($tipo, ['s', 'inlineStr', 'str', 'b', 'e'], true)) {
                continue;
            }

            $estiloOriginal = (int) $celda->getAttribute('s');

            if (
                ! isset($estilosFecha[$estiloOriginal])
                || ! isset($nuevosEstilos[$estiloOriginal])
            ) {
                continue;
            }

            $celda->setAttribute(
                's',
                (string) $nuevosEstilos[$estiloOriginal]
            );

            $modificadas++;
        }

        return [
            'modificadas' => $modificadas,
            'xml'         => $dom->saveXML(),
        ];
    }

    private function agregarFormatoPersonalizado(
        DOMDocument $dom,
        DOMXPath $xpath,
        int $nuevoNumFmtId
    ): void {
        $namespace = $dom->documentElement?->namespaceURI;

        if (! $namespace) {
            throw new RuntimeException('No se encontró el namespace de estilos.');
        }

        $styleSheet = $dom->documentElement;

        if (! $styleSheet instanceof DOMElement) {
            throw new RuntimeException('El archivo de estilos no es válido.');
        }

        $numFmts = $xpath->query('//x:numFmts')->item(0);

        if (! $numFmts instanceof DOMElement) {
            $numFmts = $dom->createElementNS($namespace, 'numFmts');
            $numFmts->setAttribute('count', '0');

            $primerElemento = null;

            foreach ($styleSheet->childNodes as $hijo) {
                if ($hijo instanceof DOMElement) {
                    $primerElemento = $hijo;
                    break;
                }
            }

            if ($primerElemento) {
                $styleSheet->insertBefore($numFmts, $primerElemento);
            } else {
                $styleSheet->appendChild($numFmts);
            }
        }

        $numFmt = $dom->createElementNS($namespace, 'numFmt');
        $numFmt->setAttribute('numFmtId', (string) $nuevoNumFmtId);
        $numFmt->setAttribute('formatCode', self::FORMATO_FECHA);

        $numFmts->appendChild($numFmt);

        $cantidad = 0;

        foreach ($numFmts->childNodes as $nodo) {
            if ($nodo instanceof DOMElement && $nodo->localName === 'numFmt') {
                $cantidad++;
            }
        }

        $numFmts->setAttribute('count', (string) $cantidad);
    }

    private function obtenerNuevoNumFmtId(array $formatos): int
    {
        $mayor = 163;

        foreach (array_keys($formatos) as $id) {
            $mayor = max($mayor, (int) $id);
        }

        return $mayor + 1;
    }

    private function esFormatoFecha(
        int $numFmtId,
        array $formatosPersonalizados
    ): bool {
        $formatosFechaIntegrados = [
            14, 15, 16, 17,
            18, 19, 20, 21, 22,
            27, 28, 29, 30, 31, 32, 33, 34, 35, 36,
            45, 46, 47,
            50, 51, 52, 53, 54, 55, 56, 57, 58,
        ];

        if (in_array($numFmtId, $formatosFechaIntegrados, true)) {
            return true;
        }

        $formato = $formatosPersonalizados[$numFmtId] ?? null;

        if (! is_string($formato) || $formato === '') {
            return false;
        }

        return $this->codigoPareceFecha($formato);
    }

    private function codigoPareceFecha(string $formato): bool
    {
        $formato = strtolower($formato);

        // Elimina textos literales entre comillas y caracteres escapados.
        $formato = preg_replace('/"[^"]*"/', '', $formato) ?? $formato;
        $formato = preg_replace('/\\\\./', '', $formato) ?? $formato;

        $tieneDia = preg_match('/d{1,4}/', $formato) === 1;
        $tieneMes = preg_match('/m{1,4}/', $formato) === 1;
        $tieneAnio = preg_match('/y{2,4}/', $formato) === 1;

        return $tieneAnio && ($tieneMes || $tieneDia);
    }

    private function cargarXml(string $xml): DOMDocument
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;

        $estadoAnterior = libxml_use_internal_errors(true);

        try {
            if (! $dom->loadXML($xml, LIBXML_NONET | LIBXML_COMPACT)) {
                throw new RuntimeException('El archivo contiene XML inválido.');
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($estadoAnterior);
        }

        return $dom;
    }
}
