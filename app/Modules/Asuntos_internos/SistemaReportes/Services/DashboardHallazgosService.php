<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

class DashboardHallazgosService
{
    public function construir(
        array $estadosQuejas,
        array $quejasPorSector,
        array $quejasPorZona,
        array $quejasPorTurno,
        array $dimensionDashboard,
        bool $esFelicitacion
    ): array {

        $hallazgos = [];

        $obtenerMayores =
            static function (
                array $etiquetas,
                array $totales
            ): ?array {

                if (
                    empty($etiquetas)
                    || empty($totales)
                ) {

                    return null;
                }

                $mayorTotal = 0;

                foreach ($totales as $total) {

                    $total =
                        (int) $total;

                    if ($total > $mayorTotal) {

                        $mayorTotal =
                            $total;
                    }
                }

                if ($mayorTotal <= 0) {

                    return null;
                }

                $mayores = [];

                foreach ($totales as $indice => $total) {

                    if ((int) $total !== $mayorTotal) {

                        continue;
                    }

                    $etiqueta =
                        trim(
                            (string) (
                                $etiquetas[$indice]
                                ?? ''
                            )
                        );

                    if ($etiqueta === '') {

                        continue;
                    }

                    $mayores[] =
                        $etiqueta;
                }

                if (empty($mayores)) {

                    return null;
                }

                return [
                    'etiquetas' =>
                        $mayores,

                    'total' =>
                        $mayorTotal,

                    'empate' =>
                        count($mayores) > 1,
                ];
            };

        $formatearEtiquetas =
            static function (
                array $etiquetas
            ): string {

                $cantidad =
                    count($etiquetas);

                if ($cantidad === 0) {

                    return '';
                }

                if ($cantidad === 1) {

                    return (string) $etiquetas[0];
                }

                if ($cantidad === 2) {

                    return
                        $etiquetas[0]
                        . ' y '
                        . $etiquetas[1];
                }

                $ultima =
                    array_pop($etiquetas);

                return
                    implode(', ', $etiquetas)
                    . ' y '
                    . $ultima;
            };

        $sectorMayor =
            $obtenerMayores(
                $quejasPorSector['sectores']
                ?? [],
                $quejasPorSector['totales']
                ?? []
            );

        if ($sectorMayor !== null) {

            $textoSector =
                $formatearEtiquetas(
                    $sectorMayor['etiquetas']
                );

            $total =
                (int) $sectorMayor['total'];

            $hallazgos[] = [
                'tipo' =>
                    'sector',

                'titulo' =>
                    $sectorMayor['empate']
                        ? (
                            $esFelicitacion
                            ? 'Sectores con más felicitaciones'
                            : 'Sectores con mayor concentración'
                        )
                        : (
                            $esFelicitacion
                            ? 'Sector con más felicitaciones'
                            : 'Mayor concentración por sector'
                        ),

                'valor' =>
                    $textoSector,

                'descripcion' =>
                    $this->describirSector(
                        $textoSector,
                        $total,
                        (bool) $sectorMayor['empate'],
                        $esFelicitacion
                    ),
            ];
        }

        $zonaMayor =
            $obtenerMayores(
                $quejasPorZona['zonas']
                ?? [],
                $quejasPorZona['totales']
                ?? []
            );

        if ($zonaMayor !== null) {

            $textoZona =
                $formatearEtiquetas(
                    $zonaMayor['etiquetas']
                );

            $total =
                (int) $zonaMayor['total'];

            $hallazgos[] = [
                'tipo' =>
                    'zona',

                'titulo' =>
                    $zonaMayor['empate']
                        ? (
                            $esFelicitacion
                            ? 'Zonas con más felicitaciones'
                            : 'Zonas con mayor concentración'
                        )
                        : (
                            $esFelicitacion
                            ? 'Zona con más felicitaciones'
                            : 'Zona con mayor concentración'
                        ),

                'valor' =>
                    $textoZona,

                'descripcion' =>
                    $this->describirZona(
                        $textoZona,
                        $total,
                        (bool) $zonaMayor['empate'],
                        $esFelicitacion
                    ),
            ];
        }

        $turnoMayor =
            $obtenerMayores(
                $quejasPorTurno['turnos']
                ?? [],
                $quejasPorTurno['totales']
                ?? []
            );

        if ($turnoMayor !== null) {

            $textoTurno =
                $formatearEtiquetas(
                    $turnoMayor['etiquetas']
                );

            $total =
                (int) $turnoMayor['total'];

            $hallazgos[] = [
                'tipo' =>
                    'turno',

                'titulo' =>
                    $turnoMayor['empate']
                        ? (
                            $esFelicitacion
                            ? 'Turnos con más felicitaciones'
                            : 'Turnos con mayor concentración'
                        )
                        : (
                            $esFelicitacion
                            ? 'Turno con más felicitaciones'
                            : 'Turno con mayor concentración'
                        ),

                'valor' =>
                    $textoTurno,

                'descripcion' =>
                    $this->describirTurno(
                        $textoTurno,
                        $total,
                        (bool) $turnoMayor['empate'],
                        $esFelicitacion
                    ),
            ];
        }

        if (!$esFelicitacion) {

            $estadoMayor =
                $obtenerMayores(
                    $estadosQuejas['estados']
                    ?? [],
                    $estadosQuejas['totales']
                    ?? []
                );

            if ($estadoMayor !== null) {

                $textoEstado =
                    $formatearEtiquetas(
                        $estadoMayor['etiquetas']
                    );

                $total =
                    (int) $estadoMayor['total'];

                $hallazgos[] = [
                    'tipo' =>
                        'estado',

                    'titulo' =>
                        $estadoMayor['empate']
                            ? 'Estados predominantes'
                            : 'Estado predominante',

                    'valor' =>
                        $textoEstado,

                    'descripcion' =>
                        $estadoMayor['empate']
                            ? $textoEstado
                            . ' comparten el mayor número de registros, con '
                            . $total
                            . ' cada uno.'
                            : $total
                            . (
                                $total === 1
                                ? ' registro se encuentra actualmente en estado '
                                : ' registros se encuentran actualmente en estado '
                            )
                            . $textoEstado
                            . '.',
                ];
            }
        }

        $dimensionMayor =
            $obtenerMayores(
                $dimensionDashboard['etiquetas']
                ?? [],
                $dimensionDashboard['totales']
                ?? []
            );

        if ($dimensionMayor !== null) {

            $textoDimension =
                $formatearEtiquetas(
                    $dimensionMayor['etiquetas']
                );

            $total =
                (int) $dimensionMayor['total'];

            $tituloDimension =
                trim(
                    (string) (
                        $dimensionDashboard['titulo']
                        ?? 'Dimensión'
                    )
                );

            $hallazgos[] = [
                'tipo' =>
                    'dimension',

                'titulo' =>
                    $dimensionMayor['empate']
                        ? $tituloDimension
                        . ' — mayor concentración compartida'
                        : $tituloDimension
                        . ' con mayor concentración',

                'valor' =>
                    $textoDimension,

                'descripcion' =>
                    $this->describirDimension(
                        $textoDimension,
                        $total,
                        (bool) $dimensionMayor['empate'],
                        $esFelicitacion
                    ),
            ];
        }

        return array_slice(
            $hallazgos,
            0,
            5
        );
    }

    private function describirSector(
        string $texto,
        int $total,
        bool $empate,
        bool $esFelicitacion
    ): string {

        if ($esFelicitacion) {

            return $empate
                ? $texto . ' comparten la mayor cantidad, con ' . $total . ($total === 1 ? ' felicitación cada uno.' : ' felicitaciones cada uno.')
                : $texto . ' registra ' . $total . ($total === 1 ? ' felicitación' : ' felicitaciones') . ' en el periodo seleccionado.';
        }

        return $empate
            ? $texto . ' comparten la mayor concentración, con ' . $total . ($total === 1 ? ' registro cada uno.' : ' registros cada uno.')
            : $texto . ' concentra ' . $total . ($total === 1 ? ' registro' : ' registros') . ' en el periodo seleccionado.';
    }

    private function describirZona(
        string $texto,
        int $total,
        bool $empate,
        bool $esFelicitacion
    ): string {

        $sustantivo =
            $esFelicitacion
                ? ($total === 1 ? ' felicitación' : ' felicitaciones')
                : ($total === 1 ? ' registro' : ' registros');

        return $empate
            ? $texto . ' comparten el valor máximo con ' . $total . $sustantivo . ' cada una.'
            : $texto . ' presenta ' . $total . $sustantivo . '.';
    }

    private function describirTurno(
        string $texto,
        int $total,
        bool $empate,
        bool $esFelicitacion
    ): string {

        $sustantivo =
            $esFelicitacion
                ? ($total === 1 ? ' felicitación' : ' felicitaciones')
                : ($total === 1 ? ' registro' : ' registros');

        return $empate
            ? $texto . ' comparten el valor máximo con ' . $total . $sustantivo . ' cada uno.'
            : $texto . ' reúne ' . $total . $sustantivo . '.';
    }

    private function describirDimension(
        string $texto,
        int $total,
        bool $empate,
        bool $esFelicitacion
    ): string {

        $sustantivo =
            $esFelicitacion
                ? ($total === 1 ? ' asociación con felicitaciones' : ' asociaciones con felicitaciones')
                : ($total === 1 ? ' asociación con reportes' : ' asociaciones con reportes');

        return $empate
            ? $texto . ' comparten el valor máximo con ' . $total . $sustantivo . ' cada uno.'
            : $texto . ' registra ' . $total . $sustantivo . '.';
    }
}
