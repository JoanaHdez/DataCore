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
    public function generar(
    array $reportes,
    array $secciones = []
    ): string {

        /* =====================================================
        SECCIONES PERMITIDAS
        ===================================================== */

        $seccionesPermitidas = [

            'datos_reporte',
            'identificacion',
            'hechos',
            'ubicacion',
            'personal',
            'unidades',
            'quejoso',
            'clasificacion',
            'observaciones',
            'seguimientos',
            'evidencias',

        ];


        /* =====================================================
        NORMALIZAR SECCIONES
        ===================================================== */

        $secciones =
            array_values(
                array_unique(
                    array_filter(
                        $secciones,
                        static fn($seccion) =>
                            in_array(
                                $seccion,
                                $seccionesPermitidas,
                                true
                            )
                    )
                )
            );


        if (empty($secciones)) {

            throw new \InvalidArgumentException(
                'No se seleccionaron secciones para exportar.'
            );
        }


        /* =====================================================
        CREAR LIBRO
        ===================================================== */

        $spreadsheet =
            new Spreadsheet();


        /*
        * PhpSpreadsheet crea automáticamente
        * una primera hoja vacía.
        */

        $primeraHoja =
            $spreadsheet
            ->getActiveSheet();


        $primeraHojaUtilizada =
            false;


        /* =====================================================
        SECCIONES DE LA HOJA REPORTES
        ===================================================== */

        $seccionesReporte = [

            'datos_reporte',
            'identificacion',
            'hechos',
            'ubicacion',
            'personal',
            'unidades',
            'quejoso',
            'clasificacion',
            'observaciones',

        ];


        $crearReportes =
            count(
                array_intersect(
                    $secciones,
                    $seccionesReporte
                )
            ) > 0;


        /* =====================================================
        HOJA REPORTES
        ===================================================== */

        if ($crearReportes) {

            $hojaReportes =
                $primeraHoja;


            $hojaReportes->setTitle(
                'Reportes'
            );


            $this->crearHojaReportes(
                $hojaReportes,
                $reportes,
                $secciones
            );


            $primeraHojaUtilizada =
                true;
        }


        /* =====================================================
        HOJA SEGUIMIENTOS
        ===================================================== */

        if (
            in_array(
                'seguimientos',
                $secciones,
                true
            )
        ) {

            if (!$primeraHojaUtilizada) {

                $hojaSeguimientos =
                    $primeraHoja;


                $primeraHojaUtilizada =
                    true;

            } else {

                $hojaSeguimientos =
                    $spreadsheet
                    ->createSheet();
            }


            $hojaSeguimientos->setTitle(
                'Seguimientos'
            );


            $this->crearHojaSeguimientos(
                $hojaSeguimientos,
                $reportes
            );
        }


        /* =====================================================
        HOJA EVIDENCIAS
        ===================================================== */

        if (
            in_array(
                'evidencias',
                $secciones,
                true
            )
        ) {

            if (!$primeraHojaUtilizada) {

                $hojaEvidencias =
                    $primeraHoja;


                $primeraHojaUtilizada =
                    true;

            } else {

                $hojaEvidencias =
                    $spreadsheet
                    ->createSheet();
            }


            $hojaEvidencias->setTitle(
                'Evidencias'
            );


            $this->crearHojaEvidencias(
                $hojaEvidencias,
                $reportes
            );
        }


        /* =====================================================
        PRIMERA HOJA ACTIVA
        ===================================================== */

        $spreadsheet->setActiveSheetIndex(
            0
        );


        /* =====================================================
        DIRECTORIO TEMPORAL
        ===================================================== */

        $directorio =
            WRITEPATH
            . 'exports/';


        if (!is_dir($directorio)) {

            $creado =
                mkdir(
                    $directorio,
                    0775,
                    true
                );


            if (
                !$creado
                && !is_dir($directorio)
            ) {

                throw new \RuntimeException(
                    'No fue posible crear el directorio de exportaciones.'
                );
            }
        }


        /* =====================================================
        NOMBRE
        ===================================================== */

        $nombreArchivo =
            'reportes_asuntos_internos_'
            . date('Ymd_His')
            . '.xlsx';


        $ruta =
            $directorio
            . $nombreArchivo;


        /* =====================================================
        GUARDAR
        ===================================================== */

        $writer =
            new Xlsx(
                $spreadsheet
            );


        $writer->save(
            $ruta
        );


        /* =====================================================
        LIBERAR
        ===================================================== */

        $spreadsheet
            ->disconnectWorksheets();


        unset(
            $spreadsheet
        );


        return $ruta;
    }


    /* =========================================================
       HOJA DE REPORTES
    ========================================================= */

    private function crearHojaReportes(
        $hoja,
        array $reportes,
        array $secciones
    ): void {

        /* =====================================================
        DEFINICIÓN DE COLUMNAS POR SECCIÓN
        ===================================================== */

        $definiciones = [

            /* =================================================
            DATOS DEL REPORTE
            ================================================= */

            'datos_reporte' => [

                [
                    'titulo' =>
                        'Prefijo',

                    'campo' =>
                        '__prefijo',
                ],

                [
                    'titulo' =>
                        'Número de folio',

                    'campo' =>
                        '__numero_folio',
                ],

                [
                    'titulo' =>
                        'Fecha de registro',

                    'campo' =>
                        'fecha_registro',
                ],

            ],


            /* =================================================
            IDENTIFICACIÓN
            ================================================= */

            'identificacion' => [

                [
                    'titulo' =>
                        'Folio IP',

                    'campo' =>
                        'folio_ip',
                ],

                [
                    'titulo' =>
                        'Fecha de queja',

                    'campo' =>
                        'fecha_queja',
                ],

                [
                    'titulo' =>
                        'Fecha de acuerdo',

                    'campo' =>
                        'fecha_acuerdo',
                ],

                [
                    'titulo' =>
                        'Expediente',

                    'campo' =>
                        'expediente',
                ],

                [
                    'titulo' =>
                        'Nomenclatura',

                    'campo' =>
                        'nomenclatura',
                ],

                [
                    'titulo' =>
                        'No. de oficio',

                    'campo' =>
                        'no_oficio',
                ],

            ],


            /* =================================================
            HECHOS
            ================================================= */

            'hechos' => [

                [
                    'titulo' =>
                        'Fecha de los hechos',

                    'campo' =>
                        'fecha_hechos',
                ],

                [
                    'titulo' =>
                        'Hora de los hechos',

                    'campo' =>
                        'hora_hechos',
                ],

                [
                    'titulo' =>
                        'Descripción',

                    'campo' =>
                        'descripcion',
                ],

            ],


            /* =================================================
            UBICACIÓN
            ================================================= */

            'ubicacion' => [

                [
                    'titulo' =>
                        'Calle',

                    'campo' =>
                        'calle',
                ],

                [
                    'titulo' =>
                        'Número',

                    'campo' =>
                        'numero',
                ],

                [
                    'titulo' =>
                        'Colonia',

                    'campo' =>
                        'colonia',
                ],

                [
                    'titulo' =>
                        'Entre calle',

                    'campo' =>
                        'entre_calle',
                ],

                [
                    'titulo' =>
                        'Y calle',

                    'campo' =>
                        'y_calle',
                ],

                [
                    'titulo' =>
                        'Municipio',

                    'campo' =>
                        'municipio',
                ],

                [
                    'titulo' =>
                        'Estado',

                    'campo' =>
                        'estado',
                ],

                [
                    'titulo' =>
                        'Sector',

                    'campo' =>
                        'sector',
                ],

                [
                    'titulo' =>
                        'Cuadrante',

                    'campo' =>
                        'cuadrante',
                ],

                [
                    'titulo' =>
                        'ID de cuadra / calle',

                    'campo' =>
                        'id_cuadra',
                ],

                [
                    'titulo' =>
                        'Latitud',

                    'campo' =>
                        'latitud',
                ],

                [
                    'titulo' =>
                        'Longitud',

                    'campo' =>
                        'longitud',
                ],

            ],


            /* =================================================
            PERSONAL
            ================================================= */

            'personal' => [

                [
                    'titulo' =>
                        'Oficial',

                    'campo' =>
                        'oficial',
                ],

                [
                    'titulo' =>
                        'Área',

                    'campo' =>
                        'area',
                ],

                [
                    'titulo' =>
                        'Turno',

                    'campo' =>
                        'turno',
                ],

            ],


            /* =================================================
            UNIDADES
            ================================================= */

            'unidades' => [

                [
                    'titulo' =>
                        'Unidad',

                    'campo' =>
                        'unidad',
                ],

                [
                    'titulo' =>
                        'Placas',

                    'campo' =>
                        'unidad_placas',
                ],

                [
                    'titulo' =>
                        'Marca',

                    'campo' =>
                        'unidad_marca',
                ],

                [
                    'titulo' =>
                        'Submarca',

                    'campo' =>
                        'unidad_submarca',
                ],

                [
                    'titulo' =>
                        'Color',

                    'campo' =>
                        'unidad_color',
                ],

                [
                    'titulo' =>
                        'Estatus de unidad',

                    'campo' =>
                        'unidad_estatus',
                ],

                [
                    'titulo' =>
                        'Servicio / Adscripción',

                    'campo' =>
                        'unidad_servicio_adscripcion',
                ],

                [
                    'titulo' =>
                        'Tipo de vehículo',

                    'campo' =>
                        'unidad_tipo_vehiculo',
                ],

                [
                    'titulo' =>
                        'Origen',

                    'campo' =>
                        'unidad_origen',
                ],

            ],


            /* =================================================
            QUEJOSO
            ================================================= */

            'quejoso' => [

                [
                    'titulo' =>
                        'Quejoso',

                    'campo' =>
                        'quejoso',
                ],

                [
                    'titulo' =>
                        'Edad',

                    'campo' =>
                        'edad',
                ],

                [
                    'titulo' =>
                        'Género',

                    'campo' =>
                        'genero',
                ],

                [
                    'titulo' =>
                        'Teléfono',

                    'campo' =>
                        'telefono',
                ],

                [
                    'titulo' =>
                        'Correo electrónico',

                    'campo' =>
                        'correo',
                ],

            ],


            /* =================================================
            CLASIFICACIÓN
            ================================================= */

            'clasificacion' => [

                [
                    'titulo' =>
                        'Clasificación',

                    'campo' =>
                        'clasificacion',
                ],

                [
                    'titulo' =>
                        'Inspector',

                    'campo' =>
                        'inspector',
                ],

                [
                    'titulo' =>
                        'Investigador',

                    'campo' =>
                        'investigador',
                ],

                [
                    'titulo' =>
                        'Quién emite resolución',

                    'campo' =>
                        'quien_emite_resolucion',
                ],

                [
                    'titulo' =>
                        'Resolución',

                    'campo' =>
                        'resolucion',
                ],

                [
                    'titulo' =>
                        'Motivos',

                    'campo' =>
                        'motivos',
                ],

            ],


            /* =================================================
            OBSERVACIONES
            ================================================= */

            'observaciones' => [

                [
                    'titulo' =>
                        'Observaciones',

                    'campo' =>
                        'observaciones',
                ],

            ],

        ];


        /* =====================================================
        COLUMNAS SELECCIONADAS

        El folio completo siempre se incluye como primera
        columna, independientemente de las secciones elegidas.
        ===================================================== */

        $columnas = [

            [
                'titulo' =>
                    'Folio',

                'campo' =>
                    '__folio_completo',
            ],

        ];


        foreach (
            $secciones
            as $seccion
        ) {

            if (
                !isset(
                    $definiciones[$seccion]
                )
            ) {
                continue;
            }


            foreach (
                $definiciones[$seccion]
                as $definicion
            ) {

                $columnas[] =
                    $definicion;
            }
        }


        /* =====================================================
        ENCABEZADOS
        ===================================================== */

        $encabezados =
            array_map(
                static fn($columna) =>
                    $columna['titulo'],
                $columnas
            );


        $hoja->fromArray(
            $encabezados,
            null,
            'A1'
        );


        /* =====================================================
        REGISTROS
        ===================================================== */

        $fila =
            2;


        foreach (
            $reportes
            as $reporte
        ) {

            $datos =
                [];


            foreach (
                $columnas
                as $columna
            ) {

                $campo =
                    $columna['campo'];


                /* =============================================
                FOLIO COMPLETO
                ============================================= */

                if (
                    $campo ===
                    '__folio_completo'
                ) {

                    $datos[] =
                        $this->obtenerFolioCompleto(
                            $reporte
                        );

                    continue;
                }


                /* =============================================
                PREFIJO
                ============================================= */

                if (
                    $campo ===
                    '__prefijo'
                ) {

                    $datos[] =
                        $this->obtenerPrefijo(
                            $reporte
                        );

                    continue;
                }


                /* =============================================
                NÚMERO DE FOLIO
                ============================================= */

                if (
                    $campo ===
                    '__numero_folio'
                ) {

                    $datos[] =
                        $this->obtenerNumeroFolio(
                            $reporte
                        );

                    continue;
                }


                /* =============================================
                VALOR NORMAL
                ============================================= */

                $datos[] =
                    $this->valor(
                        $reporte,
                        $campo
                    );
            }


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
            $hoja
            ->getHighestColumn();


        $ultimaFila =
            max(
                1,
                $hoja
                ->getHighestRow()
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
            ->setBold(
                true
            );


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
            ->setWrapText(
                true
            );


        /* =====================================================
        FILTRO
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
        ALTURA ENCABEZADO
        ===================================================== */

        $hoja
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                30
            );


        /* =====================================================
        AJUSTAR COLUMNAS
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
                ->setAutoSize(
                    true
                );
        }
    }

    /* =========================================================
       HOJA DE SEGUIMIENTOS
    ========================================================= */

    private function crearHojaSeguimientos(
        $hoja,
        array $reportes
    ): void {

        /* =====================================================
        ENCABEZADOS
        ===================================================== */

        $encabezados = [

            'Folio',

            'Fecha',

            'Tipo de seguimiento',

            'Estado resultante',

            'Observaciones',

        ];


        $hoja->fromArray(
            $encabezados,
            null,
            'A1'
        );


        /* =====================================================
        REGISTROS

        Solo se exporta el último seguimiento
        disponible de cada reporte.
        ===================================================== */

        $fila =
            2;


        foreach (
            $reportes
            as $reporte
        ) {

            $seguimientos =
                $reporte['seguimientos']
                ?? [];


            if (
                !is_array(
                    $seguimientos
                )
                || empty(
                    $seguimientos
                )
            ) {
                continue;
            }


            /*
            * Los seguimientos llegan ordenados:
            *
            * fecha DESC
            * id_seguimiento DESC
            *
            * Por lo tanto, la posición 0 corresponde
            * al seguimiento más reciente.
            */

            $seguimiento =
                $seguimientos[0]
                ?? null;


            if (
                !is_array(
                    $seguimiento
                )
            ) {
                continue;
            }


            $hoja->fromArray(
                [

                    $this->obtenerFolioCompleto(
                        $reporte
                    ),

                    trim(
                        (string) (
                            $seguimiento['fecha']
                            ?? ''
                        )
                    ),

                    trim(
                        (string) (
                            $seguimiento['tipo']
                            ?? ''
                        )
                    ),

                    trim(
                        (string) (
                            $seguimiento['estado_resultante']
                            ?? ''
                        )
                    ),

                    trim(
                        (string) (
                            $seguimiento['observaciones']
                            ?? ''
                        )
                    ),

                ],
                null,
                'A' . $fila
            );


            $fila++;
        }


        /* =====================================================
        DIMENSIONES
        ===================================================== */

        $ultimaFila =
            max(
                1,
                $hoja
                ->getHighestRow()
            );


        /* =====================================================
        ENCABEZADOS
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:E1'
            )
            ->getFont()
            ->setBold(
                true
            );


        $hoja
            ->getStyle(
                'A1:E1'
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
                'A1:E'
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
                'A1:E'
                . $ultimaFila
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_TOP
            )
            ->setWrapText(
                true
            );


        /* =====================================================
        FILTROS
        ===================================================== */

        $hoja->setAutoFilter(
            'A1:E'
            . $ultimaFila
        );


        /* =====================================================
        CONGELAR ENCABEZADO
        ===================================================== */

        $hoja->freezePane(
            'A2'
        );


        /* =====================================================
        ALTURA
        ===================================================== */

        $hoja
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                30
            );


        /* =====================================================
        COLUMNAS
        ===================================================== */

        $hoja
            ->getColumnDimension(
                'A'
            )
            ->setAutoSize(
                true
            );


        $hoja
            ->getColumnDimension(
                'B'
            )
            ->setAutoSize(
                true
            );


        $hoja
            ->getColumnDimension(
                'C'
            )
            ->setAutoSize(
                true
            );


        $hoja
            ->getColumnDimension(
                'D'
            )
            ->setAutoSize(
                true
            );


        $hoja
            ->getColumnDimension(
                'E'
            )
            ->setWidth(
                60
            );
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


        $fila =
            2;


        foreach (
            $reportes
            as $reporte
        ) {

            $evidencias =
                $reporte['evidencias']
                ?? [];


            if (
                !is_array(
                    $evidencias
                )
            ) {
                continue;
            }


            foreach (
                $evidencias
                as $evidencia
            ) {

                if (
                    is_array(
                        $evidencia
                    )
                ) {

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
                        (string)
                        $evidencia;


                    $ruta =
                        '';
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
                $hoja
                    ->getHighestRow()
            );


        /* =====================================================
           ENCABEZADOS
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:C1'
            )
            ->getFont()
            ->setBold(
                true
            );


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
            ->setWrapText(
                true
            );


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
            ->getColumnDimension(
                'A'
            )
            ->setAutoSize(
                true
            );


        $hoja
            ->getColumnDimension(
                'B'
            )
            ->setAutoSize(
                true
            );


        $hoja
            ->getColumnDimension(
                'C'
            )
            ->setWidth(
                60
            );
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
            is_array(
                $valor
            )
            || is_object(
                $valor
            )
        ) {

            return '';
        }


        return trim(
            (string)
            $valor
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
            !empty($reporte['prefijo'])
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


        if (!$folio) {

            return 'QJ';
        }


        $partes =
            explode(
                '-',
                $folio
            );


        if (
            count(
                $partes
            ) > 1
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
            !empty($reporte['numero_folio'])
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


        if (!$folio) {

            return '';
        }


        $partes =
            explode(
                '-',
                $folio
            );


        if (
            count(
                $partes
            ) <= 1
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
            !empty($reporte['folio'])
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
