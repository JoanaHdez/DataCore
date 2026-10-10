<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use Config\Database;
use RuntimeException;
use Throwable;

class CatalogoEstadosMunicipiosService
{
    private const TABLA =
        'municipios_estados';

    /**
     * @return array<int, array{idEstado: string, nombre: string}>
     */
    public function obtenerEstados(): array
    {
        try {

            $db =
                Database::connect(
                    'unidades'
                );

            $db
                ->initialize();

            $sql =
                'SELECT
                    idEstado,
                    MIN(CONVERT(estado USING utf8mb4)) AS nombre
                FROM '
                . self::TABLA
                . '
                WHERE idEstado IS NOT NULL
                    AND TRIM(idEstado) <> \'\'
                    AND estado IS NOT NULL
                    AND TRIM(estado) <> \'\'
                GROUP BY idEstado
                ORDER BY nombre ASC';

            $filas =
                $db
                    ->query(
                        $sql
                    )
                    ->getResultArray();

            return array_values(
                array_map(
                    static fn (array $fila): array => [
                        'idEstado' =>
                        (string) ($fila['idEstado'] ?? ''),

                        'nombre' =>
                        trim(
                            (string) ($fila['nombre'] ?? '')
                        ),
                    ],
                    $filas
                )
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Error consultando estados para SistemaReportes: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );

            throw new RuntimeException(
                'No fue posible consultar el catalogo de estados.',
                0,
                $e
            );
        }
    }

    public function existeEstado(
        string $idEstado
    ): bool {
        try {

            $db =
                Database::connect(
                    'unidades'
                );

            $db
                ->initialize();

            $fila =
                $db
                    ->table(
                        self::TABLA
                    )
                    ->select(
                        'id'
                    )
                    ->where(
                        'idEstado',
                        $idEstado
                    )
                    ->limit(
                        1
                    )
                    ->get()
                    ->getRowArray();

            return $fila !== null;
        } catch (Throwable $e) {

            log_message(
                'error',
                'Error validando estado para SistemaReportes: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );

            throw new RuntimeException(
                'No fue posible validar el estado solicitado.',
                0,
                $e
            );
        }
    }

    /**
     * @return array<int, array{id: int, nombre: string}>
     */
    public function obtenerMunicipiosPorEstado(
        string $idEstado
    ): array {
        try {

            $db =
                Database::connect(
                    'unidades'
                );

            $db
                ->initialize();

            $sql =
                'SELECT
                    MIN(id) AS id,
                    MIN(CONVERT(municipio USING utf8mb4)) AS nombre
                FROM '
                . self::TABLA
                . '
                WHERE idEstado = ?
                    AND municipio IS NOT NULL
                    AND TRIM(municipio) <> \'\'
                GROUP BY municipio
                ORDER BY nombre ASC';

            $filas =
                $db
                    ->query(
                        $sql,
                        [
                            $idEstado,
                        ]
                    )
                    ->getResultArray();

            return array_values(
                array_map(
                    static fn (array $fila): array => [
                        'id' =>
                        (int) ($fila['id'] ?? 0),

                        'nombre' =>
                        trim(
                            (string) ($fila['nombre'] ?? '')
                        ),
                    ],
                    $filas
                )
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Error consultando municipios para SistemaReportes: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );

            throw new RuntimeException(
                'No fue posible consultar el catalogo de municipios.',
                0,
                $e
            );
        }
    }
}
