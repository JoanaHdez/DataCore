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

            $ruta =
                $resultado['ruta']
                ?? '';

            $nombre =
                $resultado['nombre']
                ?? basename(
                    (string) $ruta
                );

            $directorioPdf =
                realpath(
                    WRITEPATH
                    . 'exports/pdf/'
                );

            $rutaReal =
                realpath(
                    (string) $ruta
                );

            $directorioPdf =
                $directorioPdf !== false
                    ? rtrim(
                        $directorioPdf,
                        DIRECTORY_SEPARATOR
                    )
                    . DIRECTORY_SEPARATOR
                    : false;

            if (
                $ruta === ''
                || $directorioPdf === false
                || $rutaReal === false
                || !str_starts_with(
                    $rutaReal,
                    $directorioPdf
                )
                || !is_file(
                    $rutaReal
                )
            ) {

                throw new \RuntimeException(
                    'El archivo PDF no fue generado correctamente.'
                );
            }

            $contenido =
                file_get_contents(
                    $rutaReal
                );

            if (
                $contenido === false
                || $contenido === ''
            ) {

                throw new \RuntimeException(
                    'No fue posible leer el archivo PDF generado.'
                );
            }

            if (
                !unlink(
                    $rutaReal
                )
            ) {

                log_message(
                    'warning',
                    'No fue posible eliminar el archivo temporal PDF de queja: {ruta}',
                    [
                        'ruta' =>
                            $rutaReal,
                    ]
                );
            }

            return $this->response
                ->download(
                    $nombre,
                    $contenido,
                    true
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
