<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardComparativaService
{

    private DashboardFiltrosService $filtrosService;

    private DashboardIndicadoresService $indicadoresService;


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct(
        DashboardFiltrosService $filtrosService,
        DashboardIndicadoresService $indicadoresService
    ) {

        $this->filtrosService =
            $filtrosService;


        $this->indicadoresService =
            $indicadoresService;
    }


    /* =========================================================
       OBTENER COMPARATIVA
    ========================================================= */

    public function obtenerComparativa(): array
    {

        /* =====================================================
           FILTROS ORIGINALES
        ===================================================== */

        $filtrosOriginales =
            $this->filtrosService
            ->obtenerFiltros();


        $fechaInicio =
            trim(
                (string) (
                    $filtrosOriginales['fecha_registro_inicio']
                    ?? ''
                )
            );


        $fechaFin =
            trim(
                (string) (
                    $filtrosOriginales['fecha_registro_fin']
                    ?? ''
                )
            );


        /* =====================================================
           VALIDAR PERIODO COMPLETO
        ===================================================== */

        if (
            $fechaInicio === ''
            || $fechaFin === ''
        ) {

            return $this->respuestaSinPeriodo();
        }


        $inicio =
            \DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $fechaInicio
            );


        $fin =
            \DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $fechaFin
            );


        if (
            !$inicio
            || !$fin
            || $inicio > $fin
        ) {

            return $this->respuestaSinPeriodo();
        }


        /* =====================================================
           DURACIÓN INCLUSIVA DEL PERIODO
        ===================================================== */

        $diasPeriodo =
            (
                (int)
                $inicio
                    ->diff(
                        $fin
                    )
                    ->format('%a')
            )
            + 1;


        /* =====================================================
           PERIODO ANTERIOR
        ===================================================== */

        $finAnterior =
            $inicio
            ->modify(
                '-1 day'
            );


        $inicioAnterior =
            $finAnterior
            ->modify(
                '-'
                    . ($diasPeriodo - 1)
                    . ' days'
            );


        /* =====================================================
           INDICADORES DEL PERIODO ACTUAL
        ===================================================== */

        $indicadoresActuales =
            $this->indicadoresService
            ->obtenerIndicadores();


        /* =====================================================
           FILTROS DEL PERIODO ANTERIOR
        ===================================================== */

        $filtrosAnterior =
            $filtrosOriginales;


        $filtrosAnterior['fecha_registro_inicio'] =
            $inicioAnterior
            ->format(
                'Y-m-d'
            );


        $filtrosAnterior['fecha_registro_fin'] =
            $finAnterior
            ->format(
                'Y-m-d'
            );


        /* =====================================================
           ESTABLECER TEMPORALMENTE PERIODO ANTERIOR
        ===================================================== */

        $this->filtrosService
            ->establecerFiltros(
                $filtrosAnterior
            );


        /* =====================================================
           INDICADORES DEL PERIODO ANTERIOR
        ===================================================== */

        $indicadoresAnteriores =
            $this->indicadoresService
            ->obtenerIndicadores();


        /* =====================================================
           RESTAURAR FILTROS ORIGINALES
        ===================================================== */

        $this->filtrosService
            ->establecerFiltros(
                $filtrosOriginales
            );


        /* =====================================================
           MÉTRICAS A COMPARAR
        ===================================================== */

        $metricasDefinidas = [

            [
                'clave' =>
                'total',

                'nombre' =>
                'Total de reportes',
            ],

            [
                'clave' =>
                'pendientes',

                'nombre' =>
                'Pendientes',
            ],

            [
                'clave' =>
                'en_proceso',

                'nombre' =>
                'En proceso',
            ],

            [
                'clave' =>
                'finalizados',

                'nombre' =>
                'Finalizados',
            ],

            [
                'clave' =>
                'anonimas',

                'nombre' =>
                'Quejas anónimas',
            ],

            [
                'clave' =>
                'personal_involucrado',

                'nombre' =>
                'Personal involucrado',
            ],

        ];


        /* =====================================================
           CONSTRUIR MÉTRICAS
        ===================================================== */

        $metricas = [];


        foreach (
            $metricasDefinidas
            as $metrica
        ) {

            $clave =
                (string) (
                    $metrica['clave']
                    ?? ''
                );


            $nombre =
                (string) (
                    $metrica['nombre']
                    ?? ''
                );


            if (
                $clave === ''
                || $nombre === ''
            ) {

                continue;
            }


            $actual =
                (int) (
                    $indicadoresActuales[$clave]
                    ?? 0
                );


            $anterior =
                (int) (
                    $indicadoresAnteriores[$clave]
                    ?? 0
                );


            $diferencia =
                $actual
                - $anterior;


            /* =================================================
               VARIACIÓN PORCENTUAL
            ================================================= */

            if (
                $anterior > 0
            ) {

                $variacion =
                    round(
                        (
                            $diferencia
                            / $anterior
                        )
                            * 100,
                        1
                    );

                $variacionDisponible =
                    true;
            } else {

                /*
                 * Si el periodo anterior es 0,
                 * no existe una base válida para calcular
                 * porcentaje de variación.
                 */

                $variacion =
                    null;

                $variacionDisponible =
                    false;
            }


            /* =================================================
               TENDENCIA
            ================================================= */

            if (
                $diferencia > 0
            ) {

                $tendencia =
                    'aumento';
            } elseif (
                $diferencia < 0
            ) {

                $tendencia =
                    'disminucion';
            } else {

                $tendencia =
                    'sin_cambio';
            }


            $metricas[] = [

                'clave' =>
                $clave,

                'nombre' =>
                $nombre,

                'actual' =>
                $actual,

                'anterior' =>
                $anterior,

                'diferencia' =>
                $diferencia,

                'variacion' =>
                $variacion,

                'variacion_disponible' =>
                $variacionDisponible,

                'tendencia' =>
                $tendencia,

            ];
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [

            'disponible' =>
            true,

            'dias_periodo' =>
            $diasPeriodo,

            'periodo_actual' => [

                'inicio' =>
                $inicio
                    ->format(
                        'Y-m-d'
                    ),

                'fin' =>
                $fin
                    ->format(
                        'Y-m-d'
                    ),

            ],

            'periodo_anterior' => [

                'inicio' =>
                $inicioAnterior
                    ->format(
                        'Y-m-d'
                    ),

                'fin' =>
                $finAnterior
                    ->format(
                        'Y-m-d'
                    ),

            ],

            'metricas' =>
            $metricas,

        ];
    }


    /* =========================================================
       RESPUESTA SIN PERIODO
    ========================================================= */

    private function respuestaSinPeriodo(): array
    {

        return [

            'disponible' =>
            false,

            'dias_periodo' =>
            0,

            'periodo_actual' => [

                'inicio' =>
                null,

                'fin' =>
                null,

            ],

            'periodo_anterior' => [

                'inicio' =>
                null,

                'fin' =>
                null,

            ],

            'metricas' =>
            [],

        ];
    }
}
