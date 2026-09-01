<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ListadoExcelService
{
    /**
     * Genera el Excel completo de reportes.
     */
    public function generar(array $reportes): string
    {
        $spreadsheet = new Spreadsheet();


        /* =====================================================
           HOJA PRINCIPAL
        ===================================================== */

        $hojaReportes =
            $spreadsheet->getActiveSheet();

        $hojaReportes->setTitle(
            'Reportes'
        );


        $this->crearHojaReportes(
            $hojaReportes,
            $reportes
        );


        /* =====================================================
           HOJA DE EVIDENCIAS
        ===================================================== */

        $hojaEvidencias =
            $spreadsheet->createSheet();


        $hojaEvidencias->setTitle(
            'Evidencias'
        );


        $this->crearHojaEvidencias(
            $hojaEvidencias,
            $reportes
        );


        /*
         * Dejamos abierta la hoja principal.
         */
        $spreadsheet->setActiveSheetIndex(0);


        /* =====================================================
           DIRECTORIO TEMPORAL
        ===================================================== */

        $directorio =
            WRITEPATH . 'exports/';


        if (! is_dir($directorio)) {

            $creado =
                mkdir(
                    $directorio,
                    0775,
                    true
                );


            if (! $creado && ! is_dir($directorio)) {

                throw new \RuntimeException(
                    'No fue posible crear el directorio de exportaciones.'
                );
            }
        }


        /* =====================================================
           NOMBRE DEL ARCHIVO
        ===================================================== */

        $nombreArchivo =
            'reportes_asuntos_internos_'
            . date('Ymd_His')
            . '.xlsx';


        $ruta =
            $directorio
            . $nombreArchivo;


        /* =====================================================
           GENERAR XLSX
        ===================================================== */

        $writer =
            new Xlsx(
                $spreadsheet
            );


        $writer->save(
            $ruta
        );


        /*
         * Liberamos memoria.
         */
        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);


        return $ruta;
    }


    /* =========================================================
       HOJA DE REPORTES
    ========================================================= */

    private function crearHojaReportes(
        $hoja,
        array $reportes
    ): void {

        /* =====================================================
           COLUMNAS
        ===================================================== */

        $columnas = [

            // Datos del reporte
            'Prefijo',
            'Número de folio',
            'Fecha de registro',

            // Identificación
            'Folio IP',
            'Fecha de queja',
            'Fecha de acuerdo',
            'Expediente',
            'Nomenclatura',
            'No. de oficio',

            // Datos de los hechos
            'Fecha de los hechos',
            'Hora de los hechos',
            'Descripción',

            // Ubicación
            'Calle',
            'Número',
            'Colonia',
            'Entre calle',
            'Y calle',
            'Municipio',
            'Estado',
            'Sector',
            'Cuadrante',
            'ID de cuadra / calle',
            'Latitud',
            'Longitud',
            'Origen de ubicación',

            // Personal
            'Oficial',
            'Área',
            'Turno',

            // Unidad
            'Unidad',
            'Placas',
            'Marca',
            'Submarca',
            'Color',
            'Estatus de unidad',
            'Servicio / Adscripción',
            'Tipo de vehículo',
            'Origen',

            // Quejoso
            'Quejoso',
            'Edad',
            'Género',
            'Teléfono',
            'Correo electrónico',

            // Clasificación
            'Clasificación',
            'Inspector',
            'Investigador',
            'Quién emite resolución',
            'Resolución',
            'Motivos',

            // Información adicional
            'Observaciones',

            // Último seguimiento
            'Fecha último seguimiento',
            'Tipo de seguimiento',
            'Estado resultante',
            'Observaciones del seguimiento',
        ];


        /* =====================================================
           ENCABEZADOS
        ===================================================== */

        $hoja->fromArray(
            $columnas,
            null,
            'A1'
        );


        /* =====================================================
           REGISTROS
        ===================================================== */

        $fila = 2;


        foreach ($reportes as $reporte) {

            $datos = [

                /* ===============================
                   DATOS DEL REPORTE
                =============================== */

                $this->obtenerPrefijo(
                    $reporte
                ),

                $this->obtenerNumeroFolio(
                    $reporte
                ),

                $this->valor(
                    $reporte,
                    'fecha_registro'
                ),


                /* ===============================
                   IDENTIFICACIÓN
                =============================== */

                $this->valor(
                    $reporte,
                    'folio_ip'
                ),

                $this->valor(
                    $reporte,
                    'fecha_queja'
                ),

                $this->valor(
                    $reporte,
                    'fecha_acuerdo'
                ),

                $this->valor(
                    $reporte,
                    'expediente'
                ),

                $this->valor(
                    $reporte,
                    'nomenclatura'
                ),

                $this->valor(
                    $reporte,
                    'no_oficio'
                ),


                /* ===============================
                   DATOS DE LOS HECHOS
                =============================== */

                $this->valor(
                    $reporte,
                    'fecha_hechos'
                ),

                $this->valor(
                    $reporte,
                    'hora_hechos'
                ),

                $this->valor(
                    $reporte,
                    'descripcion'
                ),


                /* ===============================
                   UBICACIÓN
                =============================== */

                $this->valor(
                    $reporte,
                    'calle'
                ),

                $this->valor(
                    $reporte,
                    'numero'
                ),

                $this->valor(
                    $reporte,
                    'colonia'
                ),

                $this->valor(
                    $reporte,
                    'entre_calle'
                ),

                $this->valor(
                    $reporte,
                    'y_calle'
                ),

                $this->valor(
                    $reporte,
                    'municipio'
                ),

                $this->valor(
                    $reporte,
                    'estado'
                ),

                $this->valor(
                    $reporte,
                    'sector'
                ),

                $this->valor(
                    $reporte,
                    'cuadrante'
                ),

                $this->valor(
                    $reporte,
                    'id_cuadra'
                ),

                $this->valor(
                    $reporte,
                    'latitud'
                ),

                $this->valor(
                    $reporte,
                    'longitud'
                ),

                $this->valor(
                    $reporte,
                    'origen_ubicacion'
                ),


                /* ===============================
                   PERSONAL
                =============================== */

                $this->valor(
                    $reporte,
                    'oficial'
                ),

                $this->valor(
                    $reporte,
                    'area'
                ),

                $this->valor(
                    $reporte,
                    'turno'
                ),


                /* ===============================
                   UNIDAD
                =============================== */

                $this->valor(
                    $reporte,
                    'unidad'
                ),

                $this->valor(
                    $reporte,
                    'unidad_placas'
                ),

                $this->valor(
                    $reporte,
                    'unidad_marca'
                ),

                $this->valor(
                    $reporte,
                    'unidad_submarca'
                ),

                $this->valor(
                    $reporte,
                    'unidad_color'
                ),

                $this->valor(
                    $reporte,
                    'unidad_estatus'
                ),

                $this->valor(
                    $reporte,
                    'unidad_servicio_adscripcion'
                ),

                $this->valor(
                    $reporte,
                    'unidad_tipo_vehiculo'
                ),

                $this->valor(
                    $reporte,
                    'unidad_origen'
                ),


                /* ===============================
                   QUEJOSO
                =============================== */

                $this->valor(
                    $reporte,
                    'quejoso'
                ),

                $this->valor(
                    $reporte,
                    'edad'
                ),

                $this->valor(
                    $reporte,
                    'genero'
                ),

                $this->valor(
                    $reporte,
                    'telefono'
                ),

                $this->valor(
                    $reporte,
                    'correo'
                ),


                /* ===============================
                   CLASIFICACIÓN
                =============================== */

                $this->valor(
                    $reporte,
                    'clasificacion'
                ),

                $this->valor(
                    $reporte,
                    'inspector'
                ),

                $this->valor(
                    $reporte,
                    'investigador'
                ),

                $this->valor(
                    $reporte,
                    'quien_emite_resolucion'
                ),

                $this->valor(
                    $reporte,
                    'resolucion'
                ),

                $this->valor(
                    $reporte,
                    'motivos'
                ),


                /* ===============================
                   INFORMACIÓN ADICIONAL
                =============================== */

                $this->valor(
                    $reporte,
                    'observaciones'
                ),


                /* ===============================
                   ÚLTIMO SEGUIMIENTO
                =============================== */

                $this->valor(
                    $reporte,
                    'seguimiento_fecha'
                ),

                $this->valor(
                    $reporte,
                    'seguimiento_tipo'
                ),

                $this->valor(
                    $reporte,
                    'seguimiento_estado'
                ),

                $this->valor(
                    $reporte,
                    'seguimiento_observaciones'
                ),
            ];


            $hoja->fromArray(
                $datos,
                null,
                'A' . $fila
            );


            $fila++;
        }


        /* =====================================================
           DIMENSIONES
        ===================================================== */

        $ultimaColumna =
            $hoja->getHighestColumn();


        $ultimaFila =
            max(
                1,
                $hoja->getHighestRow()
            );


        /* =====================================================
           ENCABEZADOS
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . '1'
            )
            ->getFont()
            ->setBold(true);


        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . '1'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        /* =====================================================
           BORDES
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . $ultimaFila
            )
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            );


        /* =====================================================
           ALINEACIÓN
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_TOP
            )
            ->setWrapText(true);


        /* =====================================================
           FILTROS DE EXCEL
        ===================================================== */

        $hoja->setAutoFilter(
            'A1:'
                . $ultimaColumna
                . $ultimaFila
        );


        /* =====================================================
           CONGELAR ENCABEZADO
        ===================================================== */

        $hoja->freezePane(
            'A2'
        );


        /* =====================================================
           ALTURA DEL ENCABEZADO
        ===================================================== */

        $hoja
            ->getRowDimension(1)
            ->setRowHeight(30);


        /* =====================================================
           AJUSTAR COLUMNAS

           Importante:
           No usamos range('A', $ultimaColumna)
           porque existen columnas AA, AB, AC...
        ===================================================== */

        $indiceUltimaColumna =
            Coordinate::columnIndexFromString(
                $ultimaColumna
            );


        for (
            $indice = 1;
            $indice <= $indiceUltimaColumna;
            $indice++
        ) {

            $columna =
                Coordinate::stringFromColumnIndex(
                    $indice
                );


            $hoja
                ->getColumnDimension(
                    $columna
                )
                ->setAutoSize(true);
        }
    }


    /* =========================================================
       HOJA DE EVIDENCIAS
    ========================================================= */

    private function crearHojaEvidencias(
        $hoja,
        array $reportes
    ): void {

        $encabezados = [
            'Folio',
            'Archivo',
            'Ruta / URL',
        ];


        $hoja->fromArray(
            $encabezados,
            null,
            'A1'
        );


        $fila = 2;


        foreach ($reportes as $reporte) {

            $evidencias =
                $reporte['evidencias']
                ?? [];


            if (! is_array($evidencias)) {
                continue;
            }


            foreach ($evidencias as $evidencia) {

                if (is_array($evidencia)) {

                    $archivo =
                        $evidencia['archivo']
                        ?? $evidencia['nombre']
                        ?? '';


                    $ruta =
                        $evidencia['ruta']
                        ?? $evidencia['url']
                        ?? '';
                } else {

                    $archivo =
                        (string) $evidencia;


                    $ruta = '';
                }


                $hoja->fromArray(
                    [
                        $this->obtenerFolioCompleto(
                            $reporte
                        ),

                        $archivo,

                        $ruta,
                    ],
                    null,
                    'A' . $fila
                );


                $fila++;
            }
        }


        /* =====================================================
           DIMENSIONES
        ===================================================== */

        $ultimaFila =
            max(
                1,
                $hoja->getHighestRow()
            );


        /* =====================================================
           ENCABEZADOS
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:C1'
            )
            ->getFont()
            ->setBold(true);


        $hoja
            ->getStyle(
                'A1:C1'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        /* =====================================================
           BORDES
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:C'
                    . $ultimaFila
            )
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            );


        /* =====================================================
           ALINEACIÓN
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:C'
                    . $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_TOP
            )
            ->setWrapText(true);


        /* =====================================================
           FILTROS
        ===================================================== */

        $hoja->setAutoFilter(
            'A1:C'
                . $ultimaFila
        );


        /* =====================================================
           CONGELAR ENCABEZADO
        ===================================================== */

        $hoja->freezePane(
            'A2'
        );


        /* =====================================================
           TAMAÑO COLUMNAS
        ===================================================== */

        $hoja
            ->getColumnDimension('A')
            ->setAutoSize(true);


        $hoja
            ->getColumnDimension('B')
            ->setAutoSize(true);


        $hoja
            ->getColumnDimension('C')
            ->setWidth(60);
    }


    /* =========================================================
       OBTENER VALOR SEGURO
    ========================================================= */

    private function valor(
        array $reporte,
        string $campo
    ): string {

        $valor =
            $reporte[$campo]
            ?? '';


        if (
            is_array($valor)
            || is_object($valor)
        ) {

            return '';
        }


        return trim(
            (string) $valor
        );
    }


    /* =========================================================
       OBTENER PREFIJO
    ========================================================= */

    private function obtenerPrefijo(
        array $reporte
    ): string {

        /*
         * Cuando tengamos los registros reales,
         * QJ será el prefijo definido.
         */
        if (
            ! empty($reporte['prefijo'])
        ) {

            return trim(
                (string)
                $reporte['prefijo']
            );
        }


        /*
         * Compatibilidad temporal con
         * AI-2026-001.
         */
        $folio =
            $this->valor(
                $reporte,
                'folio'
            );


        if (! $folio) {
            return 'QJ';
        }


        $partes =
            explode(
                '-',
                $folio
            );


        if (
            count($partes)
            > 1
        ) {

            return $partes[0];
        }


        return 'QJ';
    }


    /* =========================================================
       OBTENER NÚMERO DE FOLIO
    ========================================================= */

    private function obtenerNumeroFolio(
        array $reporte
    ): string {

        if (
            ! empty($reporte['numero_folio'])
        ) {

            return trim(
                (string)
                $reporte['numero_folio']
            );
        }


        /*
         * Compatibilidad temporal con
         * AI-2026-001.
         */
        $folio =
            $this->valor(
                $reporte,
                'folio'
            );


        if (! $folio) {
            return '';
        }


        $partes =
            explode(
                '-',
                $folio
            );


        if (
            count($partes)
            <= 1
        ) {

            return $folio;
        }


        array_shift(
            $partes
        );


        return implode(
            '-',
            $partes
        );
    }


    /* =========================================================
       OBTENER FOLIO COMPLETO
    ========================================================= */

    private function obtenerFolioCompleto(
        array $reporte
    ): string {

        /*
         * Compatibilidad temporal.
         */
        if (
            ! empty($reporte['folio'])
        ) {

            return trim(
                (string)
                $reporte['folio']
            );
        }


        $prefijo =
            $this->obtenerPrefijo(
                $reporte
            );


        $numero =
            $this->obtenerNumeroFolio(
                $reporte
            );


        if (
            $prefijo
            && $numero
        ) {

            return $prefijo
                . '-'
                . $numero;
        }


        return $numero;
    }
}
