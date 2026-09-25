<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardFelicitacionesService
{

    private $db;

    private DashboardFiltrosService $filtrosService;


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct(
        DashboardFiltrosService $filtrosService
    ) {

        $this->db =
            \Config\Database::connect(
                'datacore'
            );


        $this->filtrosService =
            $filtrosService;
    }


    /* =========================================================
       INDICADORES
    ========================================================= */

    public function obtenerIndicadores(): array
    {

        /* =====================================================
           TOTAL DE FELICITACIONES
        ===================================================== */

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select(
                'COUNT(*) AS total',
                false
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $resultado =
            $builder
            ->get()
            ->getRowArray();


        $total =
            (int) (
                $resultado['total']
                ?? 0
            );


        /* =====================================================
           PERSONAL DISTINTO INVOLUCRADO
        ===================================================== */

        $builderPersonal =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'fp.perscod',
                'fp.plantilla_id',
            ])
            ->join(
                'ai_felicitacion_personal fp',
                'fp.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builderPersonal,
                'f'
            );


        $registrosPersonal =
            $builderPersonal
            ->get()
            ->getResultArray();


        $personas = [];


        foreach (
            $registrosPersonal
            as $registro
        ) {

            $perscod =
                trim(
                    (string) (
                        $registro['perscod']
                        ?? ''
                    )
                );


            $plantillaId =
                (int) (
                    $registro['plantilla_id']
                    ?? 0
                );


            if (
                $perscod !== ''
            ) {

                $clave =
                    'P:'
                    . mb_strtoupper(
                        $perscod,
                        'UTF-8'
                    );

            } elseif (
                $plantillaId > 0
            ) {

                $clave =
                    'I:'
                    . $plantillaId;

            } else {

                continue;
            }


            $personas[$clave] =
                true;
        }


        /* =====================================================
           RESPUESTA COMPATIBLE CON INDICADORES DEL DASHBOARD
        ===================================================== */

        return [

            'total' =>
                $total,

            'quejas' =>
                0,

            'felicitaciones' =>
                $total,

            /*
             * Estos indicadores son exclusivos
             * de Quejas.
             */

            'pendientes' =>
                0,

            'en_proceso' =>
                0,

            'finalizados' =>
                0,

            'anonimas' =>
                0,

            'personal_involucrado' =>
                count(
                    $personas
                ),

        ];
    }


    /* =========================================================
       FELICITACIONES POR SECTOR
    ========================================================= */

    public function obtenerPorSector(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'fp.area_snapshot',
            ])
            ->join(
                'ai_felicitacion_personal fp',
                'fp.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $conteos = [];

        $contados = [];


        for (
            $numero = 1;
            $numero <= 15;
            $numero++
        ) {

            $conteos[$numero] =
                0;
        }


        foreach (
            $registros
            as $registro
        ) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if (
                $idFelicitacion <= 0
            ) {

                continue;
            }


            $numeroSector =
                $this->obtenerNumeroSector(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                );


            if (
                $numeroSector === null
            ) {

                continue;
            }


            $clave =
                $idFelicitacion
                . '|'
                . $numeroSector;


            if (
                isset(
                    $contados[$clave]
                )
            ) {

                continue;
            }


            $contados[$clave] =
                true;


            $conteos[$numeroSector]++;
        }


        $sectores = [];

        $totales = [];


        foreach (
            $conteos
            as $numero => $cantidad
        ) {

            $sectores[] =
                'SECTOR '
                . $numero;


            $totales[] =
                (int) $cantidad;
        }


        return [

            'sectores' =>
                $sectores,

            'totales' =>
                $totales,

            'total' =>
                array_sum(
                    $totales
                ),

        ];
    }


    /* =========================================================
       FELICITACIONES POR ZONA
    ========================================================= */

    public function obtenerPorZona(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'fp.area_snapshot',
            ])
            ->join(
                'ai_felicitacion_personal fp',
                'fp.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $conteos = [

            'Zona Norte' =>
                0,

            'Zona Poniente' =>
                0,

            'Zona Centro' =>
                0,

            'Zona Oriente' =>
                0,

        ];


        $contados = [];


        foreach (
            $registros
            as $registro
        ) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if (
                $idFelicitacion <= 0
            ) {

                continue;
            }


            $numeroSector =
                $this->obtenerNumeroSector(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                );


            if (
                $numeroSector === null
            ) {

                continue;
            }


            $zona =
                $this->obtenerZonaDesdeSector(
                    $numeroSector
                );


            if (
                $zona === null
            ) {

                continue;
            }


            $clave =
                $idFelicitacion
                . '|'
                . $zona;


            if (
                isset(
                    $contados[$clave]
                )
            ) {

                continue;
            }


            $contados[$clave] =
                true;


            $conteos[$zona]++;
        }


        return [

            'zonas' =>
                array_keys(
                    $conteos
                ),

            'totales' =>
                array_values(
                    $conteos
                ),

            'total' =>
                array_sum(
                    $conteos
                ),

        ];
    }


    /* =========================================================
       FELICITACIONES POR TURNO
    ========================================================= */

    public function obtenerPorTurno(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'fp.turno_snapshot',
            ])
            ->join(
                'ai_felicitacion_personal fp',
                'fp.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $orden = [

            'Primer turno',
            'Segundo turno',
            'Tercer turno',
            'Alfa',
            'Beta',
            'Diario',
            'No refiere ni fecha ni horario',

        ];


        $conteos = [];


        foreach (
            $orden
            as $turno
        ) {

            $conteos[$turno] =
                0;
        }


        $contados = [];


        foreach (
            $registros
            as $registro
        ) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if (
                $idFelicitacion <= 0
            ) {

                continue;
            }


            $turno =
                $this->clasificarTurno(
                    (string) (
                        $registro['turno_snapshot']
                        ?? ''
                    )
                );


            if (
                $turno === null
            ) {

                continue;
            }


            $clave =
                $idFelicitacion
                . '|'
                . mb_strtoupper(
                    $turno,
                    'UTF-8'
                );


            if (
                isset(
                    $contados[$clave]
                )
            ) {

                continue;
            }


            $contados[$clave] =
                true;


            $conteos[$turno]++;
        }


        /*
         * Quitamos categorías completamente vacías
         * para no llenar la vista de ceros innecesarios.
         */

        $conteos =
            array_filter(
                $conteos,
                static fn (
                    int $cantidad
                ): bool =>
                    $cantidad > 0
            );


        return [

            'turnos' =>
                array_keys(
                    $conteos
                ),

            'totales' =>
                array_values(
                    $conteos
                ),

            'total' =>
                array_sum(
                    $conteos
                ),

        ];
    }


    /* =========================================================
       DIMENSIÓN
       ÁREA / UNIDAD
    ========================================================= */

    public function obtenerDimension(
        string $dimension = 'area'
    ): array {

        $dimension =
            strtolower(
                trim(
                    $dimension
                )
            );


        if (
            $dimension === 'unidad'
        ) {

            return $this->obtenerPorUnidad();
        }


        return $this->obtenerPorArea();
    }


    /* =========================================================
       FELICITACIONES POR ÁREA
    ========================================================= */

    private function obtenerPorArea(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'fp.area_snapshot',
            ])
            ->join(
                'ai_felicitacion_personal fp',
                'fp.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $conteos = [];

        $contados = [];


        foreach (
            $registros
            as $registro
        ) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if (
                $idFelicitacion <= 0
            ) {

                continue;
            }


            $area =
                $this->normalizarTexto(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                );


            if (
                $area === ''
            ) {

                continue;
            }


            $clave =
                $idFelicitacion
                . '|'
                . mb_strtoupper(
                    $area,
                    'UTF-8'
                );


            if (
                isset(
                    $contados[$clave]
                )
            ) {

                continue;
            }


            $contados[$clave] =
                true;


            if (
                !isset(
                    $conteos[$area]
                )
            ) {

                $conteos[$area] =
                    0;
            }


            $conteos[$area]++;
        }


        arsort(
            $conteos,
            SORT_NUMERIC
        );


        return $this->construirDimension(
            'area',
            'Área',
            $conteos
        );
    }


    /* =========================================================
       FELICITACIONES POR UNIDAD
    ========================================================= */

    private function obtenerPorUnidad(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'fu.no_economico_snapshot',
                'fu.placas_snapshot',
            ])
            ->join(
                'ai_felicitacion_unidades fu',
                'fu.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $conteos = [];

        $contados = [];


        foreach (
            $registros
            as $registro
        ) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if (
                $idFelicitacion <= 0
            ) {

                continue;
            }


            $noEconomico =
                $this->normalizarTexto(
                    (string) (
                        $registro['no_economico_snapshot']
                        ?? ''
                    )
                );


            $placas =
                $this->normalizarTexto(
                    (string) (
                        $registro['placas_snapshot']
                        ?? ''
                    )
                );


            $unidad =
                $noEconomico !== ''
                    ? $noEconomico
                    : $placas;


            if (
                $unidad === ''
            ) {

                continue;
            }


            $clave =
                $idFelicitacion
                . '|'
                . mb_strtoupper(
                    $unidad,
                    'UTF-8'
                );


            if (
                isset(
                    $contados[$clave]
                )
            ) {

                continue;
            }


            $contados[$clave] =
                true;


            if (
                !isset(
                    $conteos[$unidad]
                )
            ) {

                $conteos[$unidad] =
                    0;
            }


            $conteos[$unidad]++;
        }


        arsort(
            $conteos,
            SORT_NUMERIC
        );


        return $this->construirDimension(
            'unidad',
            'Unidad',
            $conteos
        );
    }


    /* =========================================================
       CONSTRUIR RESPUESTA DE DIMENSIÓN
    ========================================================= */

    private function construirDimension(
        string $dimension,
        string $titulo,
        array $conteos
    ): array {

        $etiquetas =
            array_keys(
                $conteos
            );


        $totales =
            array_values(
                $conteos
            );


        $total =
            array_sum(
                $totales
            );


        $porcentajes = [];


        foreach (
            $totales
            as $cantidad
        ) {

            $porcentajes[] =
                $total > 0
                    ? round(
                        (
                            (int) $cantidad
                            / $total
                        ) * 100,
                        1
                    )
                    : 0;
        }


        return [

            'dimension' =>
                $dimension,

            'titulo' =>
                $titulo,

            'etiquetas' =>
                $etiquetas,

            'totales' =>
                $totales,

            'porcentajes' =>
                $porcentajes,

            'total' =>
                $total,

            'opciones' => [

                [
                    'valor' =>
                        'area',

                    'texto' =>
                        'Área',
                ],

                [
                    'valor' =>
                        'unidad',

                    'texto' =>
                        'Unidad',
                ],

            ],

        ];
    }


    /* =========================================================
       OBTENER NÚMERO DE SECTOR
    ========================================================= */

    private function obtenerNumeroSector(
        string $area
    ): ?int {

        $area =
            mb_strtoupper(
                $this->normalizarTexto(
                    $area
                ),
                'UTF-8'
            );


        if (
            !preg_match(
                '/^SECTOR\s+0*([0-9]+)\b/u',
                $area,
                $coincidencias
            )
        ) {

            return null;
        }


        $numero =
            (int) (
                $coincidencias[1]
                ?? 0
            );


        if (
            $numero < 1
            || $numero > 15
        ) {

            return null;
        }


        return $numero;
    }


    /* =========================================================
       ZONA DESDE SECTOR
    ========================================================= */

    private function obtenerZonaDesdeSector(
        int $sector
    ): ?string {

        if (
            $sector >= 1
            && $sector <= 3
        ) {

            return 'Zona Norte';
        }


        if (
            $sector >= 4
            && $sector <= 7
        ) {

            return 'Zona Poniente';
        }


        if (
            $sector >= 8
            && $sector <= 10
        ) {

            return 'Zona Centro';
        }


        if (
            $sector >= 11
            && $sector <= 15
        ) {

            return 'Zona Oriente';
        }


        return null;
    }


    /* =========================================================
       CLASIFICAR TURNO
    ========================================================= */

    private function clasificarTurno(
        string $turno
    ): ?string {

        $turno =
            mb_strtoupper(
                $this->normalizarTexto(
                    $turno
                ),
                'UTF-8'
            );


        if (
            $turno === ''
        ) {

            return 'No refiere ni fecha ni horario';
        }


        if (
            preg_match(
                '/\bPRIMERO\b/u',
                $turno
            )
            || preg_match(
                '/\bPRIMER\b/u',
                $turno
            )
        ) {

            return 'Primer turno';
        }


        if (
            preg_match(
                '/\bSEGUNDO\b/u',
                $turno
            )
        ) {

            return 'Segundo turno';
        }


        if (
            preg_match(
                '/\bTERCERO\b/u',
                $turno
            )
            || preg_match(
                '/\bTERCER\b/u',
                $turno
            )
        ) {

            return 'Tercer turno';
        }


        if (
            preg_match(
                '/\bALFA\b/u',
                $turno
            )
        ) {

            return 'Alfa';
        }


        if (
            preg_match(
                '/\bBETA\b/u',
                $turno
            )
        ) {

            return 'Beta';
        }


        if (
            preg_match(
                '/\bDIARIO\b/u',
                $turno
            )
        ) {

            return 'Diario';
        }


        if (
            str_contains(
                $turno,
                'NO REFIERE'
            )
            || str_contains(
                $turno,
                'SIN TURNO'
            )
        ) {

            return 'No refiere ni fecha ni horario';
        }


        /*
         * Igual que en Quejas:
         * valores como UNICO. quedan fuera.
         */

        return null;
    }


    /* =========================================================
       NORMALIZAR TEXTO
    ========================================================= */

    private function normalizarTexto(
        string $texto
    ): string {

        return trim(
            preg_replace(
                '/\s+/u',
                ' ',
                $texto
            )
            ?? ''
        );
    }

}