<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use Throwable;

class Ubicacion_Controller extends BaseController
{
    /**
     * =========================================================
     * CONSULTAR INFORMACIÓN TERRITORIAL
     * =========================================================
     *
     * Recibe:
     *
     * - lat = latitud
     * - lng = longitud
     *
     * Consulta:
     *
     * prevencion_delito.getDireccionData(longitud, latitud)
     *
     * Devuelve:
     *
     * - sector
     * - cuadrante
     * - id_cuadra
     * - calle
     * - entre_calle
     * - y_calle
     * - colonia
     */
    public function territorio()
    {
        /* =====================================================
           COORDENADAS
        ===================================================== */

        $latitud =
            $this->request->getGet('lat');

        $longitud =
            $this->request->getGet('lng');


        /* =====================================================
           VALIDACIÓN
        ===================================================== */

        if (
            ! is_numeric($latitud)
            || ! is_numeric($longitud)
        ) {

            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'ok' =>
                    false,

                    'matched' =>
                    false,

                    'message' =>
                    'La latitud o longitud no son válidas.',
                ]);
        }


        $latitud =
            (float) $latitud;

        $longitud =
            (float) $longitud;


        if (
            $latitud < -90
            || $latitud > 90
            || $longitud < -180
            || $longitud > 180
        ) {

            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'ok' =>
                    false,

                    'matched' =>
                    false,

                    'message' =>
                    'La latitud o longitud están fuera del rango permitido.',
                ]);
        }


        /* =====================================================
           RESPUESTA INICIAL
        ===================================================== */

        $respuesta = [
            'ok' =>
            true,

            'matched' =>
            false,

            'sector' =>
            '',

            'cuadrante' =>
            '',

            'id_cuadra' =>
            '',

            'calle' =>
            '',

            'entre_calle' =>
            '',

            'y_calle' =>
            '',

            'colonia' =>
            '',

            'source' =>
            'none',

            'message' =>
            'El punto está fuera de la cobertura territorial.',
        ];


        /* =====================================================
           CONEXIÓN TERRITORIAL
        ===================================================== */

        try {

            $dbTerritorio =
                Database::connect(
                    'territorio'
                );


            /*
             * Comprobamos la conexión antes de ejecutar
             * el procedimiento.
             */

            $dbTerritorio
                ->initialize();


            /* =================================================
               PROCEDIMIENTO

               IMPORTANTE:
               getDireccionData recibe:

               1. longitud
               2. latitud
            ================================================= */

            $sql =
                'CALL getDireccionData(?, ?)';


            $query =
                $dbTerritorio->query(
                    $sql,
                    [
                        $longitud,
                        $latitud,
                    ]
                );


            /* =================================================
               RESULTADOS
            ================================================= */

            $filas =
                $query->getResultArray();


            /*
             * El procedimiento puede devolver uno o varios
             * registros.
             *
             * Revisamos todos para conservar el comportamiento
             * del archivo original.
             */

            foreach ($filas as $fila) {

                $sector =
                    $this->valorPorAlias(
                        $fila,
                        [
                            'sector',
                            'SECTOR',
                        ]
                    );


                $cuadrante =
                    $this->valorPorAlias(
                        $fila,
                        [
                            'cuadrante',
                            'CUADRANTE',
                        ]
                    );


                $idCuadra =
                    $this->valorPorAlias(
                        $fila,
                        [
                            'id_cuadra',
                            'ID_CUADRA',
                            'idcuadra',
                            'idCalle',
                            'id_calle',
                            'ID',
                        ]
                    );


                $calle =
                    $this->valorPorAlias(
                        $fila,
                        [
                            'calle',
                            'CALLE',
                        ]
                    );


                $entreCalle =
                    $this->valorPorAlias(
                        $fila,
                        [
                            'entre_calle',
                            'ENTRE_CALLE',
                            'entreCalle',
                        ]
                    );


                $yCalle =
                    $this->valorPorAlias(
                        $fila,
                        [
                            'y_calle',
                            'Y_CALLE',
                            'yCalle',
                        ]
                    );


                $colonia =
                    $this->valorPorAlias(
                        $fila,
                        [
                            'colonia',
                            'COLONIA',
                        ]
                    );


                /*
                 * Solo sustituimos un valor cuando el
                 * procedimiento realmente lo devolvió.
                 */

                if ($sector !== '') {

                    $respuesta['sector'] =
                        $sector;
                }


                if ($cuadrante !== '') {

                    $respuesta['cuadrante'] =
                        $cuadrante;
                }


                if ($idCuadra !== '') {

                    $respuesta['id_cuadra'] =
                        $idCuadra;
                }


                if ($calle !== '') {

                    $respuesta['calle'] =
                        $calle;
                }


                if ($entreCalle !== '') {

                    $respuesta['entre_calle'] =
                        $entreCalle;
                }


                if ($yCalle !== '') {

                    $respuesta['y_calle'] =
                        $yCalle;
                }


                if ($colonia !== '') {

                    $respuesta['colonia'] =
                        $colonia;
                }
            }


            /* =================================================
               ¿HUBO COINCIDENCIA TERRITORIAL?
            ================================================= */

            $respuesta['matched'] =
                $respuesta['sector'] !== ''
                || $respuesta['cuadrante'] !== ''
                || $respuesta['id_cuadra'] !== '';


            if ($respuesta['matched']) {

                $respuesta['source'] =
                    'prevencion_delito.getDireccionData';


                $respuesta['message'] =
                    'Domicilio territorial localizado.';
            }


            /* =================================================
               CERRAR CONEXIÓN
            ================================================= */

            $dbTerritorio->close();


            /* =================================================
               RESPUESTA
            ================================================= */

            return $this->response
                ->setJSON(
                    $respuesta
                );
        } catch (Throwable $e) {

            /* =================================================
               LOG INTERNO
            ================================================= */

            log_message(
                'error',
                'Error consultando territorio para latitud {latitud}, longitud {longitud}: {mensaje}',
                [
                    'latitud' =>
                    $latitud,

                    'longitud' =>
                    $longitud,

                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            /* =================================================
               RESPUESTA AL FRONTEND

               No enviamos información sensible de la BD.
            ================================================= */

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'ok' =>
                    false,

                    'matched' =>
                    false,

                    'sector' =>
                    '',

                    'cuadrante' =>
                    '',

                    'id_cuadra' =>
                    '',

                    'calle' =>
                    '',

                    'entre_calle' =>
                    '',

                    'y_calle' =>
                    '',

                    'colonia' =>
                    '',

                    'source' =>
                    'none',

                    'message' =>
                    'No fue posible consultar la información territorial.',
                ]);
        }
    }


    /**
     * =========================================================
     * OBTENER VALOR POR ALIAS
     * =========================================================
     *
     * Algunos procedimientos pueden devolver nombres de
     * columnas con diferente combinación de mayúsculas,
     * minúsculas o nomenclatura.
     */
    private function valorPorAlias(
        array $fila,
        array $alias
    ): string {

        /* =====================================================
           BÚSQUEDA DIRECTA
        ===================================================== */

        foreach ($alias as $nombre) {

            if (
                array_key_exists(
                    $nombre,
                    $fila
                )
            ) {

                $valor =
                    $this->textoLimpio(
                        $fila[$nombre]
                    );


                if ($valor !== '') {

                    return $valor;
                }
            }
        }


        /* =====================================================
           NORMALIZAR NOMBRES DE COLUMNAS
        ===================================================== */

        $normalizados = [];


        foreach (
            $fila
            as $nombre => $valor
        ) {

            $normalizados[$this->claveNormalizada(
                    (string) $nombre
                )] =
                $this->textoLimpio(
                    $valor
                );
        }


        /* =====================================================
           BÚSQUEDA NORMALIZADA
        ===================================================== */

        foreach ($alias as $nombre) {

            $clave =
                $this->claveNormalizada(
                    (string) $nombre
                );


            if (
                isset(
                    $normalizados[$clave]
                )
                && $normalizados[$clave] !== ''
            ) {

                return $normalizados[$clave];
            }
        }


        return '';
    }


    /**
     * =========================================================
     * NORMALIZAR NOMBRE DE COLUMNA
     * =========================================================
     */
    private function claveNormalizada(
        string $valor
    ): string {

        return strtolower(
            (string) preg_replace(
                '/[^a-zA-Z0-9]/',
                '',
                $this->textoLimpio(
                    $valor
                )
            )
        );
    }


    /**
     * =========================================================
     * LIMPIAR TEXTO
     * =========================================================
     */
    private function textoLimpio(
        mixed $valor
    ): string {

        if ($valor === null) {

            return '';
        }


        $texto =
            trim(
                (string) $valor
            );


        if ($texto === '') {

            return '';
        }


        /*
         * Algunos datos territoriales podrían provenir
         * de tablas antiguas con codificación distinta.
         */

        if (
            ! preg_match(
                '//u',
                $texto
            )
            && function_exists(
                'mb_convert_encoding'
            )
        ) {

            $texto =
                (string) @mb_convert_encoding(
                    $texto,
                    'UTF-8',
                    'UTF-8, ISO-8859-1, Windows-1252'
                );
        }


        return trim(
            (string) preg_replace(
                '/\s+/u',
                ' ',
                $texto
            )
        );
    }
}
