<?php

namespace App\Modules\AsuntosInternos\Controllers;

use App\Controllers\BaseController;
use App\Modules\AsuntosInternos\Services\ExcelDateFormatService;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Archivos_Controller extends BaseController
{
    private string $rutaOriginales;
    private string $rutaProcesados;

    public function __construct()
    {
        $this->rutaOriginales = WRITEPATH . 'asuntos-internos/originales/';
        $this->rutaProcesados = WRITEPATH . 'asuntos-internos/procesados/';

        $this->crearDirectorio($this->rutaOriginales);
        $this->crearDirectorio($this->rutaProcesados);
    }

    public function index()
    {
        return view(
            'App\Modules\AsuntosInternos\Views\archivos\index',
            [
                'archivos' => $this->obtenerArchivosProcesados(),
            ]
        );
    }

    public function procesar()
    {
        $archivo = $this->request->getFile('archivo_excel');

        if (! $archivo || ! $archivo->isValid()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Selecciona un archivo de Excel válido.');
        }

        if ($archivo->hasMoved()) {
            return redirect()
                ->back()
                ->with('error', 'El archivo ya fue procesado anteriormente.');
        }

        $extension = strtolower($archivo->getClientExtension());

        if (! in_array($extension, ['xlsx', 'xlsm'], true)) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Por seguridad, esta versión acepta archivos .xlsx y .xlsm.'
                );
        }

        if ($archivo->getSize() > 50 * 1024 * 1024) {
            return redirect()
                ->back()
                ->with('error', 'El archivo no puede superar los 50 MB.');
        }

        $nombreOriginal = $archivo->getClientName();
        $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $extension;

        try {
            $archivo->move($this->rutaOriginales, $nombreSeguro);

            $rutaOriginal = $this->rutaOriginales . $nombreSeguro;

            if (! $this->esExcelValido($rutaOriginal)) {
                @unlink($rutaOriginal);

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'El archivo no contiene una estructura válida de Excel.'
                    );
            }

            $nombreProcesado = pathinfo($nombreOriginal, PATHINFO_FILENAME)
                . '_procesado_'
                . date('Ymd_His')
                . '.'
                . $extension;

            $nombreProcesadoSeguro = bin2hex(random_bytes(16))
                . '.'
                . $extension;

            $rutaProcesada = $this->rutaProcesados . $nombreProcesadoSeguro;

            $servicio = new ExcelDateFormatService();

            $resultado = $servicio->procesar(
                $rutaOriginal,
                $rutaProcesada
            );

            $metadata = [
                'nombre_original'      => $nombreOriginal,
                'nombre_descarga'      => $nombreProcesado,
                'archivo_fisico'       => $nombreProcesadoSeguro,
                'extension'            => $extension,
                'tamano'               => filesize($rutaProcesada) ?: 0,
                'fechas_modificadas'   => $resultado['fechas_modificadas'],
                'hojas_revisadas'      => $resultado['hojas_revisadas'],
                'fecha_procesamiento'  => date('Y-m-d H:i:s'),
                'estado'               => 'completado',
            ];

            file_put_contents(
                $rutaProcesada . '.json',
                json_encode(
                    $metadata,
                    JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_THROW_ON_ERROR
                ),
                LOCK_EX
            );

            // El original se elimina porque el requisito es conservar
            // únicamente el archivo final procesado.
            @unlink($rutaOriginal);

            return redirect()
                ->to(base_url('asuntos-internos/archivos'))
                ->with(
                    'success',
                    sprintf(
                        'Archivo procesado correctamente. Se actualizaron %d fechas.',
                        $resultado['fechas_modificadas']
                    )
                );
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Error procesando Excel: {mensaje}',
                ['mensaje' => $e->getMessage()]
            );

            return redirect()
                ->back()
                ->with(
                    'error',
                    'No fue posible procesar el archivo: ' . $e->getMessage()
                );
        }
    }

    public function descargar(string $archivo)
    {
        $archivo = basename($archivo);
        $ruta = $this->rutaProcesados . $archivo;
        $rutaMetadata = $ruta . '.json';

        if (! is_file($ruta) || ! is_file($rutaMetadata)) {
            throw PageNotFoundException::forPageNotFound(
                'No se encontró el archivo solicitado.'
            );
        }

        $metadata = json_decode(
            file_get_contents($rutaMetadata) ?: '',
            true
        );

        $nombreDescarga = $metadata['nombre_descarga'] ?? $archivo;

        return $this->response
            ->download($ruta, null)
            ->setFileName($nombreDescarga);
    }

    private function obtenerArchivosProcesados(): array
    {
        $archivos = [];

        foreach (glob($this->rutaProcesados . '*.json') ?: [] as $metadataPath) {
            $contenido = file_get_contents($metadataPath);

            if ($contenido === false) {
                continue;
            }

            $metadata = json_decode($contenido, true);

            if (! is_array($metadata)) {
                continue;
            }

            $rutaArchivo = $this->rutaProcesados
                . ($metadata['archivo_fisico'] ?? '');

            if (! is_file($rutaArchivo)) {
                continue;
            }

            $archivos[] = $metadata;
        }

        usort(
            $archivos,
            static fn (array $a, array $b): int =>
                strcmp(
                    $b['fecha_procesamiento'] ?? '',
                    $a['fecha_procesamiento'] ?? ''
                )
        );

        return $archivos;
    }

    private function esExcelValido(string $ruta): bool
    {
        $zip = new \ZipArchive();

        if ($zip->open($ruta) !== true) {
            return false;
        }

        try {
            return $zip->locateName('[Content_Types].xml') !== false
                && $zip->locateName('xl/workbook.xml') !== false
                && $zip->locateName('xl/styles.xml') !== false;
        } finally {
            $zip->close();
        }
    }

    private function crearDirectorio(string $ruta): void
    {
        if (is_dir($ruta)) {
            return;
        }

        if (! mkdir($ruta, 0775, true) && ! is_dir($ruta)) {
            throw new RuntimeException(
                'No fue posible crear el directorio: ' . $ruta
            );
        }
    }
}