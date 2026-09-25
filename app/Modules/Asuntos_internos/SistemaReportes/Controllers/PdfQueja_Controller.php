<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;
use App\Modules\Asuntos_internos\SistemaReportes\Services\FormatoQuejaPdfService;

class PdfQueja_Controller extends BaseController
{
    public function descargar(
        int $idReporte
    ) {

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setBody(
                    'La sesion no es valida.'
                );
        }


        try {

            $servicio =
                new FormatoQuejaPdfService();


            $resultado =
                $servicio->generar(
                    $idReporte
                );


            return $this->response
                ->download(
                    $resultado['ruta'],
                    null
                )
                ->setFileName(
                    $resultado['nombre']
                );


        } catch (\InvalidArgumentException $e) {

            return $this->response
                ->setStatusCode(422)
                ->setBody(
                    $e->getMessage()
                );


        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error generando PDF de queja {id}: {mensaje}',
                [
                    'id' =>
                        $idReporte,

                    'mensaje' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setBody(
                    'No fue posible generar el PDF de la queja.'
                );
        }
    }
}
