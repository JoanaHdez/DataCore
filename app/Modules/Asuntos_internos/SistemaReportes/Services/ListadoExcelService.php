<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListadoExcelService
{
    private const NO_APLICA = 'NO APLICA';

    public function generar(
        array $reportes,
        array $secciones
    ): string {

        $secciones =
            array_values(
                array_unique(
                    $secciones
                )
            );

        $spreadsheet =
            new Spreadsheet();

        $incluirSeguimientos =
            in_array(
                'seguimientos',
                $secciones,
                true
            );


        $seccionesQuejas =
            array_values(
                array_diff(
                    $secciones,
                    [
                        'seguimientos',
                    ]
                )
            );


        $crearHojaQuejas =
            !empty(
                $seccionesQuejas
            );


        $hoja =
            $spreadsheet
            ->getActiveSheet();


        if (
            $crearHojaQuejas
        ) {

            $hoja->setTitle(
                'Quejas'
            );


            $this->crearHojaQuejas(
                $hoja,
                $reportes,
                $secciones
            );
        }

        if (
            $incluirSeguimientos
        ) {

            if (
                $crearHojaQuejas
            ) {

                $hojaSeguimientos =
                    $spreadsheet
                    ->createSheet();

            } else {

                $hojaSeguimientos =
                    $hoja;
            }

            $hojaSeguimientos->setTitle(
                'Seguimientos'
            );

            $this->crearHojaSeguimientos(
                $hojaSeguimientos,
                $reportes
            );
        }

        $spreadsheet
            ->setActiveSheetIndex(
                0
            );

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

        $ruta =
            $directorio
            . 'quejas_asuntos_internos_'
            . date(
                'Ymd_His'
            )
            . '_'
            . uniqid(
                '',
                false
            )
            . '.xlsx';

        $writer =
            new Xlsx(
                $spreadsheet
            );

        $writer->save(
            $ruta
        );

        $spreadsheet
            ->disconnectWorksheets();

        unset(
            $spreadsheet
        );

        return $ruta;
    }

    private function crearHojaQuejas(
        Worksheet $hoja,
        array $reportes,
        array $secciones
    ): void {

        $maxPersonal =
            $this->maximoElementos(
                $reportes,
                'personal'
            );

        $maxUnidades =
            $this->maximoElementos(
                $reportes,
                'unidades'
            );

        $maxMotivos =
            $this->maximoElementos(
                $reportes,
                'motivos'
            );

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

        if (
            in_array(
                'clasificacion',
                $secciones,
                true
            )
            && $maxMotivos === 0
        ) {

            $maxMotivos =
                1;
        }

        [$columnas, $grupos] =
            $this->construirColumnas(
                $secciones,
                $maxPersonal,
                $maxUnidades,
                $maxMotivos
            );

        if (
            empty(
                $columnas
            )
        ) {

            throw new \RuntimeException(
                'No existen columnas disponibles para exportar.'
            );
        }

        $totalColumnas =
            count(
                $columnas
            );

        $ultimaColumna =
            Coordinate::stringFromColumnIndex(
                $totalColumnas
            );

        $hoja
            ->mergeCells(
                'A1:'
                    . $ultimaColumna
                    . '1'
            );

        $hoja->setCellValue(
            'A1',
            'REPORTE DE QUEJAS'
        );

        foreach (
            $grupos
            as $grupo
        ) {

            $inicio =
                Coordinate::stringFromColumnIndex(
                    $grupo['inicio']
                );

            $fin =
                Coordinate::stringFromColumnIndex(
                    $grupo['fin']
                );

            if (
                $grupo['inicio']
                !== $grupo['fin']
            ) {

                $hoja->mergeCells(
                    $inicio
                        . '2:'
                        . $fin
                        . '2'
                );
            }

            $hoja->setCellValue(
                $inicio
                    . '2',
                mb_strtoupper(
                    $grupo['titulo'],
                    'UTF-8'
                )
            );
        }

        $this->escribirEncabezados(
            $hoja,
            $columnas,
            $grupos
        );

        $fila =
            5;

        foreach (
            $reportes
            as $reporte
        ) {

            foreach (
                $columnas
                as $indice => $columna
            ) {

                $hoja->setCellValue(
                    Coordinate::stringFromColumnIndex(
                        $indice + 1
                    )
                        . $fila,
                    $this->valorReporte(
                        $reporte,
                        $columna
                    )
                );
            }

            $fila++;
        }

        if (
            empty(
                $reportes
            )
        ) {

            $hoja->setCellValue(
                'A5',
                'NO HAY REPORTES PARA EXPORTAR'
            );
        }

        $this->aplicarEstilos(
            $hoja,
            $totalColumnas,
            max(
                5,
                $fila - 1
            )
        );
    }

    private function construirColumnas(
        array $secciones,
        int $maxPersonal,
        int $maxUnidades,
        int $maxMotivos
    ): array {

        $columnas =
            [];

        $grupos =
            [];

        $indice =
            1;

        $agregarGrupo =
            function (
                string $seccion,
                string $titulo,
                array $nuevasColumnas
            ) use (
                &$columnas,
                &$grupos,
                &$indice,
                $secciones
            ): void {

                if (
                    !in_array(
                        $seccion,
                        $secciones,
                        true
                    )
                ) {

                    return;
                }

                $inicio =
                    $indice;

                foreach (
                    $nuevasColumnas
                    as $columna
                ) {

                    $columnas[] =
                        $columna;

                    $indice++;
                }

                $grupos[] = [
                    'titulo' =>
                        $titulo,

                    'inicio' =>
                        $inicio,

                    'fin' =>
                        $indice - 1,

                    'tipo' =>
                        'simple',
                ];
            };

        $inicioDatosReporte =
            $indice;


        $columnas[] =
            $this->columna(
                'simple',
                'tipo_folio',
                'Tipo de queja'
            );


        $columnas[] =
            $this->columna(
                'simple',
                'numero_folio',
                'ID'
            );


        $indice +=
            2;


        if (
            in_array(
                'datos_reporte',
                $secciones,
                true
            )
        ) {

            $columnas[] =
                $this->columna(
                    'simple',
                    'fecha_registro',
                    'Fecha de registro'
                );


            $indice++;
        }


        $grupos[] = [
            'titulo' =>
                'Datos del reporte',

            'inicio' =>
                $inicioDatosReporte,

            'fin' =>
                $indice - 1,

            'tipo' =>
                'simple',
        ];

        $agregarGrupo(
            'identificacion',
            'Identificación del registro',
            [
                $this->columna('simple', 'folio_ip', 'Folio IP'),
                $this->columna('simple', 'folio_imp', 'Folio IMP'),
                $this->columna('simple', 'fecha_queja', 'Fecha de queja'),
                $this->columna('simple', 'fecha_acuerdo', 'Fecha de acuerdo'),
                $this->columna('simple', 'expediente', 'Expediente'),
                $this->columna('simple', 'nomenclatura', 'Nomenclatura'),
                $this->columna('simple', 'no_oficio', 'No. de oficio'),
            ]
        );

        $agregarGrupo(
            'hechos',
            'Datos de los hechos',
            [
                $this->columna('simple', 'fecha_hechos', 'Fecha de los hechos'),
                $this->columna('simple', 'hora_hechos', 'Hora de los hechos'),
                $this->columna('simple', 'descripcion', 'Descripción de los hechos'),
            ]
        );

        $agregarGrupo(
            'ubicacion',
            'Ubicación de los hechos',
            [
                $this->columna('simple', 'calle', 'Calle'),
                $this->columna('simple', 'numero', 'No. Ext.'),
                $this->columna('simple', 'colonia', 'Colonia'),
                $this->columna('simple', 'entre_calle', 'Entre calle'),
                $this->columna('simple', 'y_calle', 'Y calle'),
                $this->columna('simple', 'municipio', 'Ciudad / Municipio'),
                $this->columna('simple', 'estado', 'Estado'),
                $this->columna('simple', 'sector', 'Sector'),
                $this->columna('simple', 'cuadrante', 'Cuadrante'),
                $this->columna('simple', 'id_cuadra', 'ID de cuadra / calle'),
                $this->columna('simple', 'longitud', 'Longitud'),
                $this->columna('simple', 'latitud', 'Latitud'),
                $this->columna('simple', 'coordenadas', 'Coordenadas'),
            ]
        );

        if (
            in_array(
                'personal',
                $secciones,
                true
            )
        ) {

            $inicio =
                $indice;

            for (
                $i = 0;
                $i < $maxPersonal;
                $i++
            ) {

                $columnas[] = $this->columna('personal', 'nombre_snapshot', 'Nombre', $i);
                $columnas[] = $this->columna('personal', 'area_snapshot', 'Área', $i);
                $columnas[] = $this->columna('personal', 'turno_snapshot', 'Turno', $i);
                $columnas[] = $this->columna('personal', 'alias_snapshot', 'Alias', $i);

                $indice +=
                    4;
            }

            $grupos[] = [
                'titulo' => 'Personal involucrado',
                'inicio' => $inicio,
                'fin' => $indice - 1,
                'tipo' => 'personal',
                'cantidad' => $maxPersonal,
            ];
        }

        if (
            in_array(
                'unidades',
                $secciones,
                true
            )
        ) {

            $inicio =
                $indice;

            for (
                $i = 0;
                $i < $maxUnidades;
                $i++
            ) {

                $columnas[] = $this->columna('unidad', 'no_economico_snapshot', 'Unidad', $i);
                $columnas[] = $this->columna('unidad', 'placas_snapshot', 'Placas', $i);

                $indice +=
                    2;
            }

            $grupos[] = [
                'titulo' => 'Unidades involucradas',
                'inicio' => $inicio,
                'fin' => $indice - 1,
                'tipo' => 'unidades',
                'cantidad' => $maxUnidades,
            ];
        }

        $agregarGrupo(
            'quejoso',
            'Datos del quejoso',
            [
                $this->columna('simple', 'es_anonimo', 'Queja anónima'),
                $this->columna('simple', 'numero_anonimo', 'No. numérico'),
                $this->columna('simple', 'quejoso', 'Quejoso'),
                $this->columna('simple', 'edad', 'Edad'),
                $this->columna('simple', 'genero', 'Género'),
                $this->columna('simple', 'telefono', 'Número de teléfono'),
                $this->columna('simple', 'correo', 'Correo electrónico'),
                $this->columna('simple', 'direccion_quejoso', 'Dirección del quejoso'),
                $this->columna('simple', 'canalizacion', 'Canalización'),
                $this->columna('simple', 'canalizacion_otro', 'Otra área de canalización'),
            ]
        );

        $agregarGrupo(
            'direccion_notificacion',
            'Dirección para notificación',
            [
                $this->columna('simple', 'notificacion_pertenece_neza', 'Pertenece a Nezahualcóyotl'),
                $this->columna('simple', 'notificacion_calle', 'Calle'),
                $this->columna('simple', 'notificacion_numero_exterior', 'No. Ext.'),
                $this->columna('simple', 'notificacion_colonia', 'Colonia'),
                $this->columna('simple', 'notificacion_entre_calle', 'Entre calle'),
                $this->columna('simple', 'notificacion_y_calle', 'Y calle'),
                $this->columna('simple', 'notificacion_municipio', 'Ciudad / Municipio'),
                $this->columna('simple', 'notificacion_estado', 'Estado'),
                $this->columna('simple', 'notificacion_sector', 'Sector'),
                $this->columna('simple', 'notificacion_cuadrante', 'Cuadrante'),
                $this->columna('simple', 'notificacion_id_cuadra', 'ID de cuadra / calle'),
                $this->columna('simple', 'notificacion_longitud', 'Longitud'),
                $this->columna('simple', 'notificacion_latitud', 'Latitud'),
                $this->columna('simple', 'notificacion_coordenadas', 'Coordenadas'),
            ]
        );

        if (
            in_array(
                'clasificacion',
                $secciones,
                true
            )
        ) {

            $inicio =
                $indice;

            $columnas[] = $this->columna('simple', 'clasificacion', 'Clasificación');
            $columnas[] = $this->columna('simple', 'inspector', 'Inspector');
            $columnas[] = $this->columna('simple', 'investigador', 'Investigador');
            $columnas[] = $this->columna('simple', 'estado_actual', 'Estado actual');
            $columnas[] = $this->columna('simple', 'sin_sanciones', 'Sin sanciones');
            $columnas[] = $this->columna('simple', 'baja_voluntaria', 'Baja voluntaria');
            $columnas[] = $this->columna('simple', 'desistimiento', 'Desistimiento');
            $indice += 7;

            for (
                $i = 0;
                $i < $maxMotivos;
                $i++
            ) {

                $columnas[] = $this->columna('motivo', 'id_motivo', 'Núm.', $i);
                $columnas[] = $this->columna('motivo', 'motivo', 'Motivo', $i);
                $columnas[] = $this->columna('motivo', 'sancion', 'Sanción', $i);
                $columnas[] = $this->columna('motivo', 'folio_sancion', 'Folio sanción', $i);
                $indice += 4;
            }

            $columnas[] = $this->columna('calculado', 'total_horas_arresto', 'Total de horas de arresto');
            $columnas[] = $this->columna('simple', 'quien_emite_resolucion', 'Quién emite la resolución');
            $columnas[] = $this->columna('simple', 'resolucion', 'Resolución');
            $indice += 3;

            $grupos[] = [
                'titulo' => 'Clasificación y resolución',
                'inicio' => $inicio,
                'fin' => $indice - 1,
                'tipo' => 'motivos',
                'cantidad' => $maxMotivos,
            ];
        }

        $agregarGrupo(
            'observaciones',
            'Observaciones',
            [
                $this->columna('simple', 'observaciones', 'Observaciones'),
            ]
        );

        return [
            $columnas,
            $grupos,
        ];
    }

    private function escribirEncabezados(
        Worksheet $hoja,
        array $columnas,
        array $grupos
    ): void {

        $columnaActual =
            1;

        foreach (
            $grupos
            as $grupo
        ) {

            if (
                $grupo['tipo']
                === 'simple'
            ) {

                for (
                    $i = $grupo['inicio'];
                    $i <= $grupo['fin'];
                    $i++
                ) {

                    $columna =
                        Coordinate::stringFromColumnIndex(
                            $i
                        );

                    $hoja->mergeCells(
                        $columna
                            . '3:'
                            . $columna
                            . '4'
                    );

                    $hoja->setCellValue(
                        $columna
                            . '3',
                        $columnas[$i - 1]['titulo']
                    );
                }

                $columnaActual =
                    $grupo['fin'] + 1;

                continue;
            }

            $camposPorGrupo =
                $grupo['tipo'] === 'unidades'
                    ? 2
                    : (
                        $grupo['tipo'] === 'motivos'
                            ? 4
                            : 4
                    );

            if (
                $grupo['tipo']
                === 'motivos'
            ) {

                for (
                    $i = $grupo['inicio'];
                    $i <= $grupo['inicio'] + 6;
                    $i++
                ) {

                    $columna =
                        Coordinate::stringFromColumnIndex(
                            $i
                        );

                    $hoja->mergeCells(
                        $columna
                            . '3:'
                            . $columna
                            . '4'
                    );

                    $hoja->setCellValue(
                        $columna
                            . '3',
                        $columnas[$i - 1]['titulo']
                    );
                }

                $columnaActual =
                    $grupo['inicio'] + 7;
            }

            for (
                $elemento = 1;
                $elemento <= $grupo['cantidad'];
                $elemento++
            ) {

                $inicioElemento =
                    $columnaActual;

                $finElemento =
                    $columnaActual + $camposPorGrupo - 1;

                $columnaInicio =
                    Coordinate::stringFromColumnIndex(
                        $inicioElemento
                    );

                $columnaFin =
                    Coordinate::stringFromColumnIndex(
                        $finElemento
                    );

                $prefijo =
                    match ($grupo['tipo']) {
                        'unidades' => 'Unidad ',
                        'motivos' => 'Motivo ',
                        default => 'Elemento ',
                    };

                if (
                    $inicioElemento
                    !== $finElemento
                ) {

                    $hoja->mergeCells(
                        $columnaInicio
                            . '3:'
                            . $columnaFin
                            . '3'
                    );
                }

                $hoja->setCellValue(
                    $columnaInicio
                        . '3',
                    $prefijo
                        . $elemento
                );

                for (
                    $i = $inicioElemento;
                    $i <= $finElemento;
                    $i++
                ) {

                    $hoja->setCellValue(
                        Coordinate::stringFromColumnIndex(
                            $i
                        )
                            . '4',
                        $columnas[$i - 1]['titulo']
                    );
                }

                $columnaActual =
                    $finElemento + 1;
            }

            if (
                $grupo['tipo']
                === 'motivos'
            ) {

                for (
                    $i = $columnaActual;
                    $i <= $grupo['fin'];
                    $i++
                ) {

                    $columna =
                        Coordinate::stringFromColumnIndex(
                            $i
                        );

                    $hoja->mergeCells(
                        $columna
                            . '3:'
                            . $columna
                            . '4'
                    );

                    $hoja->setCellValue(
                        $columna
                            . '3',
                        $columnas[$i - 1]['titulo']
                    );
                }

                $columnaActual =
                    $grupo['fin'] + 1;
            }
        }
    }

    private function valorReporte(
        array $reporte,
        array $columna
    ): string {

        $tipo =
            $columna['tipo'];

        if (
            $tipo === 'personal'
        ) {

            return $this->valorRepetible(
                $reporte['personal']
                    ?? [],
                $columna
            );
        }

        if (
            $tipo === 'unidad'
        ) {

            return $this->valorUnidad(
                $reporte,
                $columna
            );
        }

        if (
            $tipo === 'motivo'
        ) {

            return $this->valorMotivo(
                $reporte['motivos']
                    ?? [],
                $columna
            );
        }

        if (
            $tipo === 'calculado'
            && $columna['campo'] === 'total_horas_arresto'
        ) {

            return $this->totalHorasArresto(
                $reporte['motivos']
                    ?? []
            );
        }

        $campo =
            $columna['campo'];

        $valor =
            $reporte[$campo]
            ?? '';

        if (
            in_array(
                $campo,
                [
                    'es_anonimo',
                    'notificacion_pertenece_neza',
                    'sin_sanciones',
                    'baja_voluntaria',
                    'desistimiento',
                ],
                true
            )
        ) {

            return $this->siNo(
                $valor
            );
        }

        return $this->normalizar(
            $valor
        );
    }

    private function valorRepetible(
        array $elementos,
        array $columna
    ): string {

        $elemento =
            $elementos[
                $columna['indice']
            ]
            ?? [];

        return $this->normalizar(
            $elemento[
                $columna['campo']
            ]
            ?? ''
        );
    }

    private function valorUnidad(
        array $reporte,
        array $columna
    ): string {

        $unidades =
            $reporte['unidades']
            ?? [];

        $unidad =
            $unidades[
                $columna['indice']
            ]
            ?? [];

        if (
            empty(
                $unidad
            )
            && (
                $reporte['modalidad_unidad']
                ?? ''
            ) === 'SIN_UNIDAD_OFICINA'
        ) {

            return 'SIN UNIDAD / OFICINA';
        }

        return $this->normalizar(
            $unidad[
                $columna['campo']
            ]
            ?? ''
        );
    }

    private function valorMotivo(
        array $motivos,
        array $columna
    ): string {

        $motivo =
            $motivos[
                $columna['indice']
            ]
            ?? [];

        if (
            $columna['campo']
            === 'sancion'
        ) {

            return $this->normalizar(
                $motivo['sancion_registrada']
                    ?? $motivo['sancion']
                    ?? ''
            );
        }

        return $this->normalizar(
            $motivo[
                $columna['campo']
            ]
            ?? ''
        );
    }

    private function crearHojaSeguimientos(
        Worksheet $hoja,
        array $reportes
    ): void {

        $encabezados = [
            'Folio',
            'Nomenclatura',
            'Fecha',
            'Tipo de seguimiento',
            'Estado resultante',
            'Sanción disciplinaria',
            'Especifique la sanción',
            'Observaciones',
        ];

        $ultimaColumna =
            Coordinate::stringFromColumnIndex(
                count(
                    $encabezados
                )
            );

        $hoja->mergeCells(
            'A1:'
                . $ultimaColumna
                . '1'
        );

        $hoja->setCellValue(
            'A1',
            'SEGUIMIENTOS DE QUEJAS'
        );

        foreach (
            $encabezados
            as $indice => $encabezado
        ) {

            $hoja->setCellValue(
                Coordinate::stringFromColumnIndex(
                    $indice + 1
                )
                    . '2',
                $encabezado
            );
        }

        $fila =
            3;

        foreach (
            $reportes
            as $reporte
        ) {

            $seguimientos =
                $reporte['seguimientos']
                ?? [];

            foreach (
                $seguimientos
                as $seguimiento
            ) {

                $valores = [
                    $this->normalizar($reporte['folio'] ?? ''),
                    $this->normalizar($reporte['nomenclatura'] ?? ''),
                    $this->normalizar($seguimiento['fecha'] ?? ''),
                    $this->normalizar($seguimiento['tipo'] ?? ''),
                    $this->normalizar($seguimiento['estado_resultante'] ?? ''),
                    $this->normalizar($seguimiento['sancion_disciplinaria'] ?? ''),
                    $this->normalizar($seguimiento['sancion_otro'] ?? ''),
                    $this->normalizar($seguimiento['observaciones'] ?? ''),
                ];

                foreach (
                    $valores
                    as $indice => $valor
                ) {

                    $hoja->setCellValue(
                        Coordinate::stringFromColumnIndex(
                            $indice + 1
                        )
                            . $fila,
                        $valor
                    );
                }

                $fila++;
            }
        }

        if (
            $fila === 3
        ) {

            $hoja->setCellValue(
                'A3',
                'NO HAY SEGUIMIENTOS PARA EXPORTAR'
            );
        }

        $this->aplicarEstilosSeguimientos(
            $hoja,
            count(
                $encabezados
            ),
            max(
                3,
                $fila - 1
            )
        );
    }

    private function aplicarEstilos(
        Worksheet $hoja,
        int $totalColumnas,
        int $ultimaFila
    ): void {

        $ultimaColumna =
            Coordinate::stringFromColumnIndex(
                $totalColumnas
            );

        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . '1'
            )
            ->applyFromArray(
                $this->estiloTitulo()
            );

        $hoja
            ->getStyle(
                'A2:'
                    . $ultimaColumna
                    . '2'
            )
            ->applyFromArray(
                $this->estiloGrupo()
            );

        $hoja
            ->getStyle(
                'A3:'
                    . $ultimaColumna
                    . '4'
            )
            ->applyFromArray(
                $this->estiloEncabezado()
            );

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

        $hoja->freezePane(
            'A5'
        );

        $hoja->setAutoFilter(
            'A4:'
                . $ultimaColumna
                . $ultimaFila
        );

        for (
            $i = 1;
            $i <= $totalColumnas;
            $i++
        ) {

            $columna =
                Coordinate::stringFromColumnIndex(
                    $i
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

    private function aplicarEstilosSeguimientos(
        Worksheet $hoja,
        int $totalColumnas,
        int $ultimaFila
    ): void {

        $ultimaColumna =
            Coordinate::stringFromColumnIndex(
                $totalColumnas
            );

        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . '1'
            )
            ->applyFromArray(
                $this->estiloTitulo()
            );

        $hoja
            ->getStyle(
                'A2:'
                    . $ultimaColumna
                    . '2'
            )
            ->applyFromArray(
                $this->estiloEncabezado()
            );

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

        $hoja->freezePane(
            'A3'
        );

        $hoja->setAutoFilter(
            'A2:'
                . $ultimaColumna
                . $ultimaFila
        );

        for (
            $i = 1;
            $i <= $totalColumnas;
            $i++
        ) {

            $hoja
                ->getColumnDimension(
                    Coordinate::stringFromColumnIndex(
                        $i
                    )
                )
                ->setAutoSize(
                    true
                );
        }
    }

    private function columna(
        string $tipo,
        string $campo,
        string $titulo,
        ?int $indice = null
    ): array {

        return [
            'tipo' => $tipo,
            'campo' => $campo,
            'titulo' => $titulo,
            'indice' => $indice,
        ];
    }

    private function maximoElementos(
        array $reportes,
        string $campo
    ): int {

        $maximo =
            0;

        foreach (
            $reportes
            as $reporte
        ) {

            $elementos =
                $reporte[$campo]
                ?? [];

            if (
                is_array(
                    $elementos
                )
            ) {

                $maximo =
                    max(
                        $maximo,
                        count(
                            $elementos
                        )
                    );
            }
        }

        return $maximo;
    }

    private function normalizar(
        mixed $valor
    ): string {

        if (
            is_array(
                $valor
            )
            || is_object(
                $valor
            )
        ) {

            return self::NO_APLICA;
        }

        $texto =
            trim(
                (string) (
                    $valor
                    ?? ''
                )
            );

        return $texto !== ''
            ? $texto
            : self::NO_APLICA;
    }

    private function siNo(
        mixed $valor
    ): string {

        if (
            $valor === ''
            || $valor === null
        ) {

            return self::NO_APLICA;
        }

        return (int) $valor === 1
            ? 'SI'
            : 'NO';
    }

    private function totalHorasArresto(
        array $motivos
    ): string {

        $total =
            0;

        foreach (
            $motivos
            as $motivo
        ) {

            $sancion =
                (string) (
                    $motivo['sancion_registrada']
                    ?? $motivo['sancion']
                    ?? ''
                );

            if (
                preg_match(
                    '/(\d+)\s*horas?/i',
                    $sancion,
                    $coincidencias
                )
            ) {

                $total +=
                    (int) $coincidencias[1];
            }
        }

        return $total > 0
            ? (string) $total
            : self::NO_APLICA;
    }

    private function estiloTitulo(): array
    {
        return [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '173554',
                ],
            ],
        ];
    }

    private function estiloGrupo(): array
    {
        return [
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '0E669F',
                ],
            ],
        ];
    }

    private function estiloEncabezado(): array
    {
        return [
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => '173554',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'EAF4FB',
                ],
            ],
        ];
    }
}
