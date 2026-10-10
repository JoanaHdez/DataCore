<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;
use App\Modules\Asuntos_internos\SistemaReportes\Services\CatalogoEstadosMunicipiosService;
use RuntimeException;
use Throwable;

class Catalogos_Controller extends BaseController
{
    public function estados()
    {
        try {

            $servicio =
                new CatalogoEstadosMunicipiosService();

            $estados =
                $servicio
                    ->obtenerEstados();

            return $this->response
                ->setJSON([
                    'success' =>
                    true,

                    'estados' =>
                    $estados,
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Error entregando catalogo de estados para SistemaReportes: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    'No fue posible consultar el catalogo de estados.',
                ]);
        }
    }

    public function municipios()
    {
        $idEstado =
            trim(
                (string) $this->request
                    ->getGet(
                        'idEstado'
                    )
            );

        if (
            $idEstado === ''
            || ! preg_match(
                '/^\d{1,3}$/',
                $idEstado
            )
        ) {

            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    'El identificador del estado no es valido.',
                ]);
        }

        try {

            $servicio =
                new CatalogoEstadosMunicipiosService();

            if (
                ! $servicio
                    ->existeEstado(
                        $idEstado
                    )
            ) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' =>
                        false,

                        'message' =>
                        'El estado solicitado no existe.',
                    ]);
            }

            $municipios =
                $servicio
                    ->obtenerMunicipiosPorEstado(
                        $idEstado
                    );

            return $this->response
                ->setJSON([
                    'success' =>
                    true,

                    'idEstado' =>
                    $idEstado,

                    'municipios' =>
                    $municipios,
                ]);
        } catch (RuntimeException $e) {

            log_message(
                'error',
                'Error entregando catalogo de municipios para SistemaReportes: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    'No fue posible consultar el catalogo de municipios.',
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Error inesperado entregando catalogo de municipios para SistemaReportes: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    'No fue posible consultar el catalogo de municipios.',
                ]);
        }
    }
}
