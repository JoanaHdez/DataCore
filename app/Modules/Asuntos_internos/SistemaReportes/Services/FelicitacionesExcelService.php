<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class FelicitacionesExcelService
{
    /* =========================================================
       GENERAR
    ========================================================= */

    public function generar(
        array $felicitaciones,
        array $secciones = []
    ): string {

        /* =====================================================
           SECCIONES PERMITIDAS
        ===================================================== */

        $seccionesPermitidas = [
            'identificacion',
            'datos_felicitacion',
            'personal',
            'unidades',
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


        if (
            empty($secciones)
        ) {

            throw new \InvalidArgumentException(
                'No se seleccionaron secciones para exportar.'
            );
        }


        /* =====================================================
           CREAR LIBRO
        ===================================================== */

        $spreadsheet =
            new Spreadsheet();


        $hoja =
            $spreadsheet
            ->getActiveSheet();


        $hoja->setTitle(
            'Felicitaciones'
        );


        /* =====================================================
           CREAR HOJA
        ===================================================== */

        $this->crearHojaFelicitaciones(
            $hoja,
            $felicitaciones,
            $secciones
        );


        /* =====================================================
           HOJA ACTIVA
        ===================================================== */

        $spreadsheet
            ->setActiveSheetIndex(
                0
            );


        /* =====================================================
           DIRECTORIO
        ===================================================== */

        $directorio =
            WRITEPATH
            . 'exports/';


        if (
            !is_dir(
                $directorio
            )
        ) {

            $creado =
                mkdir(
                    $directorio,
                    0775,
                    true
                );


            if (
                !$creado
                && !is_dir(
                    $directorio
                )
            ) {

                throw new \RuntimeException(
                    'No fue posible crear el directorio de exportaciones.'
                );
            }
        }


        /* =====================================================
           NOMBRE DEL ARCHIVO
        ===================================================== */

        $nombreArchivo =
            'felicitaciones_asuntos_internos_'
            . date(
                'Ymd_His'
            )
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
       CREAR HOJA DE FELICITACIONES
    ========================================================= */

    private function crearHojaFelicitaciones(
        $hoja,
        array $felicitaciones,
        array $secciones
    ): void {

        /* =====================================================
           CALCULAR CANTIDAD MÁXIMA DE ELEMENTOS
        ===================================================== */

        $maxPersonal =
            0;


        $maxUnidades =
            0;


        foreach (
            $felicitaciones
            as $felicitacion
        ) {

            $personal =
                $felicitacion['personal']
                ?? [];


            $unidades =
                $felicitacion['unidades']
                ?? [];


            if (
                is_array(
                    $personal
                )
            ) {

                $maxPersonal =
                    max(
                        $maxPersonal,
                        count(
                            $personal
                        )
                    );
            }


            if (
                is_array(
                    $unidades
                )
            ) {

                $maxUnidades =
                    max(
                        $maxUnidades,
                        count(
                            $unidades
                        )
                    );
            }
        }


        /*
         * Si la sección fue seleccionada pero ningún registro
         * tiene elementos, dejamos al menos un bloque para que
         * el encabezado siga existiendo.
         */

        if (
            in_array(
                'personal',
                $secciones,
                true
            )
            && $maxPersonal === 0
        ) {

            $maxPersonal =
                1;
        }


        if (
            in_array(
                'unidades',
                $secciones,
                true
            )
            && $maxUnidades === 0
        ) {

            $maxUnidades =
                1;
        }


        /* =====================================================
           DEFINIR COLUMNAS
        ===================================================== */

        $columnas =
            [];


        $grupos =
            [];


        $indiceColumna =
            1;


        /* =====================================================
           IDENTIFICACIÓN
        ===================================================== */

        if (
            in_array(
                'identificacion',
                $secciones,
                true
            )
        ) {

            $inicio =
                $indiceColumna;


            $columnas[] = [
                'tipo' => 'tipo_felicitacion',
                'campo' => 'folio',
                'titulo' => 'Tipo de felicitación',
            ];


            $columnas[] = [
                'tipo' => 'simple',
                'campo' => 'numero_folio',
                'titulo' => 'ID',
            ];


            $columnas[] = [
                'tipo' => 'simple',
                'campo' => 'nomenclatura',
                'titulo' => 'Nomenclatura',
            ];


            $columnas[] = [
                'tipo' => 'simple',
                'campo' => 'fecha_registro',
                'titulo' => 'Fecha de registro',
            ];


            $indiceColumna +=
                4;


            $grupos[] = [
                'titulo' =>
                'Identificación de la felicitación',

                'inicio' =>
                $inicio,

                'fin' =>
                $indiceColumna - 1,

                'tipo' =>
                'simple',
            ];
        }


        /* =====================================================
           DATOS DE LA FELICITACIÓN
        ===================================================== */

        if (
            in_array(
                'datos_felicitacion',
                $secciones,
                true
            )
        ) {

            $inicio =
                $indiceColumna;


            $columnas[] = [
                'tipo' => 'simple',
                'campo' => 'nombre_felicitante',
                'titulo' => 'Nombre del felicitante',
            ];


            $columnas[] = [
                'tipo' => 'simple',
                'campo' => 'razon_felicitacion',
                'titulo' => 'Razón de la felicitación',
            ];


            $indiceColumna +=
                2;


            $grupos[] = [
                'titulo' =>
                'Datos de la felicitación',

                'inicio' =>
                $inicio,

                'fin' =>
                $indiceColumna - 1,

                'tipo' =>
                'simple',
            ];
        }


        /* =====================================================
           PERSONAL FELICITADO
        ===================================================== */

        if (
            in_array(
                'personal',
                $secciones,
                true
            )
        ) {

            $inicio =
                $indiceColumna;


            for (
                $numeroElemento = 1;
                $numeroElemento <= $maxPersonal;
                $numeroElemento++
            ) {

                $indicePersonal =
                    $numeroElemento - 1;


                $columnas[] = [
                    'tipo' => 'personal',
                    'indice' => $indicePersonal,
                    'campo' => 'nombre_snapshot',
                    'titulo' => 'Nombre',
                ];


                $columnas[] = [
                    'tipo' => 'personal',
                    'indice' => $indicePersonal,
                    'campo' => 'area_snapshot',
                    'titulo' => 'Área',
                ];


                $columnas[] = [
                    'tipo' => 'personal',
                    'indice' => $indicePersonal,
                    'campo' => 'turno_snapshot',
                    'titulo' => 'Turno',
                ];


                $columnas[] = [
                    'tipo' => 'personal',
                    'indice' => $indicePersonal,
                    'campo' => 'alias_snapshot',
                    'titulo' => 'Alias',
                ];


                $indiceColumna +=
                    4;
            }


            $grupos[] = [
                'titulo' =>
                'Personal felicitado',

                'inicio' =>
                $inicio,

                'fin' =>
                $indiceColumna - 1,

                'tipo' =>
                'personal',

                'cantidad' =>
                $maxPersonal,
            ];
        }


        /* =====================================================
           UNIDADES INVOLUCRADAS
        ===================================================== */

        if (
            in_array(
                'unidades',
                $secciones,
                true
            )
        ) {

            $inicio =
                $indiceColumna;


            for (
                $numeroUnidad = 1;
                $numeroUnidad <= $maxUnidades;
                $numeroUnidad++
            ) {

                $indiceUnidad =
                    $numeroUnidad - 1;


                $columnas[] = [
                    'tipo' => 'unidad',
                    'indice' => $indiceUnidad,
                    'campo' => 'no_economico_snapshot',
                    'titulo' => 'Unidad',
                ];


                $columnas[] = [
                    'tipo' => 'unidad',
                    'indice' => $indiceUnidad,
                    'campo' => 'placas_snapshot',
                    'titulo' => 'Placas',
                ];


                $indiceColumna +=
                    2;
            }


            $grupos[] = [
                'titulo' =>
                'Unidades involucradas',

                'inicio' =>
                $inicio,

                'fin' =>
                $indiceColumna - 1,

                'tipo' =>
                'unidades',

                'cantidad' =>
                $maxUnidades,
            ];
        }


        /* =====================================================
           VALIDAR COLUMNAS
        ===================================================== */

        $totalColumnas =
            count(
                $columnas
            );


        if (
            $totalColumnas === 0
        ) {

            throw new \RuntimeException(
                'No existen columnas disponibles para exportar.'
            );
        }


        $ultimaColumna =
            Coordinate::stringFromColumnIndex(
                $totalColumnas
            );


        /* =====================================================
           FILA 1
           TÍTULO GENERAL
        ===================================================== */

        $hoja
            ->mergeCells(
                'A1:'
                    . $ultimaColumna
                    . '1'
            );


        $hoja
            ->setCellValue(
                'A1',
                'REPORTE DE FELICITACIONES'
            );


        /* =====================================================
           FILA 2
           SECCIONES
        ===================================================== */

        foreach (
            $grupos
            as $grupo
        ) {

            $columnaInicio =
                Coordinate::stringFromColumnIndex(
                    $grupo['inicio']
                );


            $columnaFin =
                Coordinate::stringFromColumnIndex(
                    $grupo['fin']
                );


            $rango =
                $columnaInicio
                . '2:'
                . $columnaFin
                . '2';


            if (
                $grupo['inicio']
                !== $grupo['fin']
            ) {

                $hoja
                    ->mergeCells(
                        $rango
                    );
            }


            $hoja
                ->setCellValue(
                    $columnaInicio
                        . '2',
                    mb_strtoupper(
                        $grupo['titulo'],
                        'UTF-8'
                    )
                );
        }


        /* =====================================================
           FILAS 3 Y 4
           SUBGRUPOS Y CAMPOS
        ===================================================== */

        $columnaActual =
            1;


        foreach (
            $grupos
            as $grupo
        ) {

            /* =================================================
               SECCIONES SIMPLES

               Cada encabezado ocupa verticalmente filas 3 y 4.
            ================================================= */

            if (
                $grupo['tipo']
                === 'simple'
            ) {

                for (
                    $indice = $grupo['inicio'];
                    $indice <= $grupo['fin'];
                    $indice++
                ) {

                    $columna =
                        Coordinate::stringFromColumnIndex(
                            $indice
                        );


                    $hoja
                        ->mergeCells(
                            $columna
                                . '3:'
                                . $columna
                                . '4'
                        );


                    $titulo =
                        $columnas[$indice - 1]['titulo']
                        ?? '';


                    $hoja
                        ->setCellValue(
                            $columna
                                . '3',
                            $titulo
                        );
                }


                $columnaActual =
                    $grupo['fin'] + 1;


                continue;
            }


            /* =================================================
               PERSONAL

               Elemento 1, Elemento 2...
            ================================================= */

            if (
                $grupo['tipo']
                === 'personal'
            ) {

                for (
                    $elemento = 1;
                    $elemento <= $grupo['cantidad'];
                    $elemento++
                ) {

                    $inicioElemento =
                        $columnaActual;


                    $finElemento =
                        $inicioElemento + 3;


                    $columnaInicio =
                        Coordinate::stringFromColumnIndex(
                            $inicioElemento
                        );


                    $columnaFin =
                        Coordinate::stringFromColumnIndex(
                            $finElemento
                        );


                    $hoja
                        ->mergeCells(
                            $columnaInicio
                                . '3:'
                                . $columnaFin
                                . '3'
                        );


                    $hoja
                        ->setCellValue(
                            $columnaInicio
                                . '3',
                            'Elemento '
                                . $elemento
                        );


                    $encabezados = [
                        'Nombre',
                        'Área',
                        'Turno',
                        'Alias',
                    ];


                    foreach (
                        $encabezados
                        as $offset => $encabezado
                    ) {

                        $columna =
                            Coordinate::stringFromColumnIndex(
                                $inicioElemento
                                    + $offset
                            );


                        $hoja
                            ->setCellValue(
                                $columna
                                    . '4',
                                $encabezado
                            );
                    }


                    $columnaActual =
                        $finElemento + 1;
                }


                continue;
            }


            /* =================================================
               UNIDADES

               Unidad 1, Unidad 2...
            ================================================= */

            if (
                $grupo['tipo']
                === 'unidades'
            ) {

                for (
                    $unidad = 1;
                    $unidad <= $grupo['cantidad'];
                    $unidad++
                ) {

                    $inicioUnidad =
                        $columnaActual;


                    $finUnidad =
                        $inicioUnidad + 1;


                    $columnaInicio =
                        Coordinate::stringFromColumnIndex(
                            $inicioUnidad
                        );


                    $columnaFin =
                        Coordinate::stringFromColumnIndex(
                            $finUnidad
                        );


                    $hoja
                        ->mergeCells(
                            $columnaInicio
                                . '3:'
                                . $columnaFin
                                . '3'
                        );


                    $hoja
                        ->setCellValue(
                            $columnaInicio
                                . '3',
                            'Unidad '
                                . $unidad
                        );


                    $hoja
                        ->setCellValue(
                            $columnaInicio
                                . '4',
                            'Unidad'
                        );


                    $hoja
                        ->setCellValue(
                            $columnaFin
                                . '4',
                            'Placas'
                        );


                    $columnaActual =
                        $finUnidad + 1;
                }
            }
        }


        /* =====================================================
           REGISTROS
        ===================================================== */

        $fila =
            5;


        foreach (
            $felicitaciones
            as $felicitacion
        ) {

            $datosFila =
                [];


            foreach (
                $columnas
                as $definicion
            ) {

                /* =============================================
                   CAMPO SIMPLE
                ============================================== */

                if (
                    $definicion['tipo']
                    === 'simple'
                ) {

                    $datosFila[] =
                        $this->valor(
                            $felicitacion,
                            $definicion['campo']
                        );


                    continue;
                }


                /* =============================================
                TIPO DE FELICITACIÓN
                ============================================== */

                if (
                    $definicion['tipo']
                    === 'tipo_felicitacion'
                ) {

                    $datosFila[] =
                        $this->obtenerTipoFelicitacion(
                            $felicitacion
                        );


                    continue;
                }

                /* =============================================
                   PERSONAL
                ============================================== */

                if (
                    $definicion['tipo']
                    === 'personal'
                ) {

                    $personal =
                        $felicitacion['personal']
                        ?? [];


                    $persona =
                        is_array(
                            $personal
                        )
                        ? (
                            $personal[$definicion['indice']]
                            ?? []
                        )
                        : [];


                    $datosFila[] =
                        $this->valor(
                            is_array(
                                $persona
                            )
                                ? $persona
                                : [],
                            $definicion['campo']
                        );


                    continue;
                }


                /* =============================================
                UNIDAD
                ============================================== */

                if (
                    $definicion['tipo']
                    === 'unidad'
                ) {

                    $unidades =
                        $felicitacion['unidades']
                        ?? [];


                    $unidad =
                        is_array(
                            $unidades
                        )
                        ? (
                            $unidades[$definicion['indice']]
                            ?? []
                        )
                        : [];


                    /* =================================================
                    SIN UNIDAD / OFICINA
                    ================================================= */

                    if (
                        !is_array($unidad)
                        || empty($unidad)
                    ) {

                        if (
                            $definicion['campo']
                            === 'no_economico_snapshot'
                        ) {

                            $datosFila[] =
                                'SIN UNIDAD/OFICINA';
                        } else {

                            $datosFila[] =
                                'NO APLICA';
                        }


                        continue;
                    }


                    /* =================================================
                    UNIDAD EXISTENTE
                    ================================================= */

                    $datosFila[] =
                        $this->valor(
                            $unidad,
                            $definicion['campo']
                        );


                    continue;
                }
            }


            $hoja
                ->fromArray(
                    $datosFila,
                    null,
                    'A'
                        . $fila
                );


            $fila++;
        }


        /* =====================================================
           ÚLTIMA FILA
        ===================================================== */

        $ultimaFila =
            max(
                4,
                $hoja
                    ->getHighestRow()
            );


        /* =====================================================
           ESTILO TÍTULO GENERAL
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
            )
            ->setSize(
                14
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


        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . '1'
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setARGB(
                'FF173F6F'
            );


        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . '1'
            )
            ->getFont()
            ->getColor()
            ->setARGB(
                'FFFFFFFF'
            );


        /* =====================================================
           ESTILO SECCIONES
        ===================================================== */

        $hoja
            ->getStyle(
                'A2:'
                    . $ultimaColumna
                    . '2'
            )
            ->getFont()
            ->setBold(
                true
            );


        $hoja
            ->getStyle(
                'A2:'
                    . $ultimaColumna
                    . '2'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $hoja
            ->getStyle(
                'A2:'
                    . $ultimaColumna
                    . '2'
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setARGB(
                'FFDCE6F1'
            );


        /* =====================================================
           ESTILO SUBGRUPOS
        ===================================================== */

        $hoja
            ->getStyle(
                'A3:'
                    . $ultimaColumna
                    . '3'
            )
            ->getFont()
            ->setBold(
                true
            );


        $hoja
            ->getStyle(
                'A3:'
                    . $ultimaColumna
                    . '3'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        /* =====================================================
           ESTILO ENCABEZADOS
        ===================================================== */

        $hoja
            ->getStyle(
                'A4:'
                    . $ultimaColumna
                    . '4'
            )
            ->getFont()
            ->setBold(
                true
            );


        $hoja
            ->getStyle(
                'A3:'
                    . $ultimaColumna
                    . '4'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            )
            ->setWrapText(
                true
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
           DATOS
        ===================================================== */

        if (
            $ultimaFila >= 5
        ) {

            $hoja
                ->getStyle(
                    'A5:'
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
        }


        /* =====================================================
           ALTURAS
        ===================================================== */

        $hoja
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                28
            );


        $hoja
            ->getRowDimension(
                2
            )
            ->setRowHeight(
                25
            );


        $hoja
            ->getRowDimension(
                3
            )
            ->setRowHeight(
                23
            );


        $hoja
            ->getRowDimension(
                4
            )
            ->setRowHeight(
                30
            );


        /* =====================================================
           CONGELAR ENCABEZADOS
        ===================================================== */

        $hoja->freezePane(
            'A5'
        );


        /* =====================================================
           FILTRO

           El filtro se coloca sobre la fila de campos.
        ===================================================== */

        if (
            $ultimaFila >= 5
        ) {

            $hoja->setAutoFilter(
                'A4:'
                    . $ultimaColumna
                    . $ultimaFila
            );
        }


        /* =====================================================
           ANCHO DE COLUMNAS
        ===================================================== */

        for (
            $indice = 1;
            $indice <= $totalColumnas;
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
    OBTENER TIPO DE FELICITACIÓN
    ========================================================= */

    private function obtenerTipoFelicitacion(
        array $felicitacion
    ): string {

        $folio =
            strtoupper(
                trim(
                    (string) (
                        $felicitacion['folio']
                        ?? ''
                    )
                )
            );


        if (
            $folio === ''
        ) {

            return 'NO APLICA';
        }


        /*
        * Ejemplos:
        *
        * FEL-16  -> FEL
        * FEL-8   -> FEL
        *
        * Si en el futuro existe otro tipo:
        *
        * OTRO-20 -> OTRO
        */

        $partes =
            explode(
                '-',
                $folio,
                2
            );


        $tipo =
            trim(
                (string) (
                    $partes[0]
                    ?? ''
                )
            );


        if (
            $tipo === ''
        ) {

            return 'NO APLICA';
        }


        return $tipo;
    }


    /* =========================================================
    VALOR SEGURO
    ========================================================= */

    private function valor(
        array $datos,
        string $campo
    ): string {

        $valor =
            $datos[$campo]
            ?? '';


        if (
            is_array($valor)
            || is_object($valor)
        ) {
            return 'NO APLICA';
        }


        $valor =
            trim(
                (string) $valor
            );


        if (
            $valor === ''
        ) {
            return 'NO APLICA';
        }


        return $valor;
    }
}
