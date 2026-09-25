<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardCruceService
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
       OBTENER CRUCE
    ========================================================= */

    public function obtenerCruce(
        string $principal = 'sector',
        string $secundaria = 'turno'
    ): array {

        $principal =
            strtolower(
                trim(
                    $principal
                )
            );


        $secundaria =
            strtolower(
                trim(
                    $secundaria
                )
            );


        /* =====================================================
           COMBINACIONES VÁLIDAS
        ===================================================== */

        $combinacionesValidas = [

            'sector' => [
                'turno',
                'estado',
            ],

            'zona' => [
                'turno',
                'estado',
            ],

            'area' => [
                'estado',
            ],

            'turno' => [
                'estado',
            ],

        ];


        if (
            !isset(
                $combinacionesValidas[$principal]
            )
            || !in_array(
                $secundaria,
                $combinacionesValidas[$principal],
                true
            )
        ) {

            $principal =
                'sector';

            $secundaria =
                'turno';
        }


        /* =====================================================
           CONSULTAR DATOS BASE
        ===================================================== */

        $registros =
            $this->obtenerRegistrosBase();


        /* =====================================================
           CONTABILIZAR CRUCE
        ===================================================== */

        $matriz = [];

        $categoriasPrincipal = [];

        $categoriasSecundaria = [];

        $combinacionesContadas = [];


        foreach (
            $registros
            as $registro
        ) {

            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );


            if (
                $idReporte <= 0
            ) {

                continue;
            }


            $valorPrincipal =
                $this->obtenerValorDimension(
                    $principal,
                    $registro
                );


            $valorSecundaria =
                $this->obtenerValorDimension(
                    $secundaria,
                    $registro
                );


            if (
                $valorPrincipal === null
                || $valorSecundaria === null
                || $valorPrincipal === ''
                || $valorSecundaria === ''
            ) {

                continue;
            }


            /* =================================================
               EVITAR DUPLICADOS
            ================================================= */

            $clave =
                $idReporte
                . '|'
                . $principal
                . ':'
                . mb_strtoupper(
                    $valorPrincipal,
                    'UTF-8'
                )
                . '|'
                . $secundaria
                . ':'
                . mb_strtoupper(
                    $valorSecundaria,
                    'UTF-8'
                );


            if (
                isset(
                    $combinacionesContadas[$clave]
                )
            ) {

                continue;
            }


            $combinacionesContadas[$clave] =
                true;


            /* =================================================
               REGISTRAR CATEGORÍAS
            ================================================= */

            $categoriasPrincipal[$valorPrincipal] =
                true;

            $categoriasSecundaria[$valorSecundaria] =
                true;


            /* =================================================
               INICIALIZAR MATRIZ
            ================================================= */

            if (
                !isset(
                    $matriz[$valorPrincipal]
                )
            ) {

                $matriz[$valorPrincipal] =
                    [];
            }


            if (
                !isset(
                    $matriz[$valorPrincipal][$valorSecundaria]
                )
            ) {

                $matriz[$valorPrincipal][$valorSecundaria] =
                    0;
            }


            /* =================================================
               SUMAR
            ================================================= */

            $matriz[$valorPrincipal][$valorSecundaria]++;
        }


        /* =====================================================
           ORDENAR CATEGORÍAS
        ===================================================== */

        $categoriasPrincipal =
            array_keys(
                $categoriasPrincipal
            );


        $categoriasSecundaria =
            array_keys(
                $categoriasSecundaria
            );


        $categoriasPrincipal =
            $this->ordenarCategorias(
                $principal,
                $categoriasPrincipal
            );


        $categoriasSecundaria =
            $this->ordenarCategorias(
                $secundaria,
                $categoriasSecundaria
            );


        /* =====================================================
           CONSTRUIR SERIES
        ===================================================== */

        $series = [];


        foreach (
            $categoriasSecundaria
            as $categoriaSecundaria
        ) {

            $datos = [];


            foreach (
                $categoriasPrincipal
                as $categoriaPrincipal
            ) {

                $datos[] =
                    (int) (
                        $matriz[$categoriaPrincipal][$categoriaSecundaria]
                        ?? 0
                    );
            }


            $series[] = [

                'nombre' =>
                    $categoriaSecundaria,

                'datos' =>
                    $datos,

            ];
        }


        /* =====================================================
           TOTAL
        ===================================================== */

        $total = 0;


        foreach (
            $series
            as $serie
        ) {

            $total +=
                array_sum(
                    $serie['datos']
                    ?? []
                );
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [

            'principal' =>
                $principal,

            'secundaria' =>
                $secundaria,

            'categorias' =>
                $categoriasPrincipal,

            'series' =>
                $series,

            'total' =>
                $total,

            'opciones_principal' => [

                [
                    'valor' =>
                        'sector',

                    'texto' =>
                        'Sector',
                ],

                [
                    'valor' =>
                        'zona',

                    'texto' =>
                        'Zona',
                ],

                [
                    'valor' =>
                        'area',

                    'texto' =>
                        'Área',
                ],

                [
                    'valor' =>
                        'turno',

                    'texto' =>
                        'Turno',
                ],

            ],

            'opciones_secundaria' =>
                $this->obtenerOpcionesSecundarias(
                    $principal
                ),

        ];
    }


    /* =========================================================
       OBTENER REGISTROS BASE
    ========================================================= */

    private function obtenerRegistrosBase(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([
                'r.id_reporte',
                'r.estado_actual',
                'p.area_snapshot',
                'p.turno_snapshot',
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            );


        /* =====================================================
           FILTROS GLOBALES
        ===================================================== */

        $this->filtrosService
            ->aplicarFiltrosReportes(
                $builder,
                'r'
            );


        return $builder
            ->get()
            ->getResultArray();
    }


    /* =========================================================
       OBTENER VALOR DE DIMENSIÓN
    ========================================================= */

    private function obtenerValorDimension(
        string $dimension,
        array $registro
    ): ?string {

        return match ($dimension) {

            'sector' =>
                $this->obtenerSectorDesdeArea(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                ),

            'zona' =>
                $this->obtenerZonaDesdeArea(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                ),

            'area' =>
                $this->normalizarTexto(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                ),

            'turno' =>
                $this->clasificarTurno(
                    (string) (
                        $registro['turno_snapshot']
                        ?? ''
                    )
                ),

            'estado' =>
                $this->normalizarEstado(
                    (string) (
                        $registro['estado_actual']
                        ?? ''
                    )
                ),

            default =>
                null,
        };
    }


    /* =========================================================
       SECTOR DESDE ÁREA
    ========================================================= */

    private function obtenerSectorDesdeArea(
        string $area
    ): ?string {

        $area =
            mb_strtoupper(
                $this->normalizarTexto(
                    $area
                ),
                'UTF-8'
            );


        if (
            $area === ''
        ) {

            return null;
        }


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


        return
            'SECTOR '
            . $numero;
    }


    /* =========================================================
       ZONA DESDE ÁREA
    ========================================================= */

    private function obtenerZonaDesdeArea(
        string $area
    ): ?string {

        $sector =
            $this->obtenerSectorDesdeArea(
                $area
            );


        if (
            $sector === null
        ) {

            return null;
        }


        if (
            !preg_match(
                '/^SECTOR\s+([0-9]+)$/u',
                $sector,
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
            $numero >= 1
            && $numero <= 3
        ) {

            return 'Zona Norte';
        }


        if (
            $numero >= 4
            && $numero <= 7
        ) {

            return 'Zona Poniente';
        }


        if (
            $numero >= 8
            && $numero <= 10
        ) {

            return 'Zona Centro';
        }


        if (
            $numero >= 11
            && $numero <= 15
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


        return null;
    }


    /* =========================================================
       NORMALIZAR ESTADO
    ========================================================= */

    private function normalizarEstado(
        string $estado
    ): ?string {

        $estado =
            $this->normalizarTexto(
                $estado
            );


        return match ($estado) {

            'Pendiente' =>
                'Pendiente',

            'En proceso' =>
                'En proceso',

            'Finalizado' =>
                'Finalizado',

            default =>
                null,
        };
    }


    /* =========================================================
       ORDENAR CATEGORÍAS
    ========================================================= */

    private function ordenarCategorias(
        string $dimension,
        array $categorias
    ): array {

        if (
            $dimension === 'sector'
        ) {

            usort(
                $categorias,
                static function (
                    string $a,
                    string $b
                ): int {

                    preg_match(
                        '/([0-9]+)/',
                        $a,
                        $numeroA
                    );

                    preg_match(
                        '/([0-9]+)/',
                        $b,
                        $numeroB
                    );


                    return
                        ((int) ($numeroA[1] ?? 0))
                        <=>
                        ((int) ($numeroB[1] ?? 0));
                }
            );


            return $categorias;
        }


        if (
            $dimension === 'zona'
        ) {

            $orden = [
                'Zona Norte',
                'Zona Poniente',
                'Zona Centro',
                'Zona Oriente',
            ];


            usort(
                $categorias,
                static function (
                    string $a,
                    string $b
                ) use ($orden): int {

                    return
                        array_search(
                            $a,
                            $orden,
                            true
                        )
                        <=>
                        array_search(
                            $b,
                            $orden,
                            true
                        );
                }
            );


            return $categorias;
        }


        if (
            $dimension === 'turno'
        ) {

            $orden = [
                'Primer turno',
                'Segundo turno',
                'Tercer turno',
                'Alfa',
                'Beta',
                'Diario',
                'No refiere ni fecha ni horario',
            ];


            usort(
                $categorias,
                static function (
                    string $a,
                    string $b
                ) use ($orden): int {

                    return
                        array_search(
                            $a,
                            $orden,
                            true
                        )
                        <=>
                        array_search(
                            $b,
                            $orden,
                            true
                        );
                }
            );


            return $categorias;
        }


        if (
            $dimension === 'estado'
        ) {

            $orden = [
                'Pendiente',
                'En proceso',
                'Finalizado',
            ];


            usort(
                $categorias,
                static function (
                    string $a,
                    string $b
                ) use ($orden): int {

                    return
                        array_search(
                            $a,
                            $orden,
                            true
                        )
                        <=>
                        array_search(
                            $b,
                            $orden,
                            true
                        );
                }
            );


            return $categorias;
        }


        natcasesort(
            $categorias
        );


        return array_values(
            $categorias
        );
    }


    /* =========================================================
       OPCIONES SECUNDARIAS
    ========================================================= */

    private function obtenerOpcionesSecundarias(
        string $principal
    ): array {

        return match ($principal) {

            'sector' => [
                [
                    'valor' =>
                        'turno',

                    'texto' =>
                        'Turno',
                ],

                [
                    'valor' =>
                        'estado',

                    'texto' =>
                        'Estado',
                ],
            ],

            'zona' => [
                [
                    'valor' =>
                        'turno',

                    'texto' =>
                        'Turno',
                ],

                [
                    'valor' =>
                        'estado',

                    'texto' =>
                        'Estado',
                ],
            ],

            'area' => [
                [
                    'valor' =>
                        'estado',

                    'texto' =>
                        'Estado',
                ],
            ],

            'turno' => [
                [
                    'valor' =>
                        'estado',

                    'texto' =>
                        'Estado',
                ],
            ],

            default =>
                [],
        };
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