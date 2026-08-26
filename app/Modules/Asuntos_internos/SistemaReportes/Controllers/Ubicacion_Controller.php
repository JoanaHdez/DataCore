<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;

class Ubicacion_Controller extends BaseController
{
    /**
     * Busca una ubicación por texto.
     */
    public function buscar()
    {
        $consulta = trim(
            (string) $this->request->getGet('q')
        );

        if ($consulta === '') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'La búsqueda está vacía.',
                ]);
        }

        /*
         * Se agrega Nezahualcóyotl para dar prioridad
         * a resultados dentro del municipio.
         */
        $consultaCompleta = $consulta
            . ', Nezahualcóyotl, Estado de México, México';

        $url = 'https://nominatim.openstreetmap.org/search?'
            . http_build_query([
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'limit' => 5,
                'countrycodes' => 'mx',
                'q' => $consultaCompleta,
            ]);

        try {
            $cliente = service('curlrequest');

            $respuesta = $cliente->get(
                $url,
                [
                    'headers' => [
                        'User-Agent' =>
                            'SistemaReportesAsuntosInternos/1.0',
                        'Accept-Language' => 'es',
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 15,
                    'connect_timeout' => 10,
                ]
            );

            $datos = json_decode(
                $respuesta->getBody(),
                true
            );

            if (! is_array($datos)) {
                return $this->response
                    ->setStatusCode(502)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                            'La respuesta del servicio de ubicación no es válida.',
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'results' => $datos,
            ]);

        } catch (\Throwable $e) {
            log_message(
                'error',
                'Error buscando ubicación: {mensaje}',
                [
                    'mensaje' => $e->getMessage(),
                ]
            );

            /*
             * TEMPORAL:
             * mostramos el error real para diagnosticar
             * la conexión con Nominatim.
             */
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
        }
    }


    /**
     * Obtiene la dirección a partir de latitud y longitud.
     */
    public function direccion()
    {
        $latitud = $this->request->getGet('lat');
        $longitud = $this->request->getGet('lon');

        if (
            ! is_numeric($latitud)
            || ! is_numeric($longitud)
        ) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' =>
                        'Las coordenadas no son válidas.',
                ]);
        }

        $url = 'https://nominatim.openstreetmap.org/reverse?'
            . http_build_query([
                'format' => 'jsonv2',
                'lat' => $latitud,
                'lon' => $longitud,
                'addressdetails' => 1,
                'zoom' => 18,
            ]);

        try {
            $cliente = service('curlrequest');

            $respuesta = $cliente->get(
                $url,
                [
                    'headers' => [
                        'User-Agent' =>
                            'SistemaReportesAsuntosInternos/1.0',
                        'Accept-Language' => 'es',
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 15,
                    'connect_timeout' => 10,
                ]
            );

            $datos = json_decode(
                $respuesta->getBody(),
                true
            );

            if (! is_array($datos)) {
                return $this->response
                    ->setStatusCode(502)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                            'La respuesta del servicio de ubicación no es válida.',
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'result' => $datos,
            ]);

        } catch (\Throwable $e) {
            log_message(
                'error',
                'Error obteniendo dirección: {mensaje}',
                [
                    'mensaje' => $e->getMessage(),
                ]
            );

            /*
             * TEMPORAL:
             * mostramos el error real para diagnosticar.
             */
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
        }
    }
}