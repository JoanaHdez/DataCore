<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ListadoExportacionService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ListadoExcelService;

class Exportaciones_Controller extends BaseController
{
    /* =========================================================
       EXPORTAR LISTADO DE QUEJAS
    ========================================================= */

    public function exportarListado()
    {
        /* =====================================================
           VALIDAR SESION
        ===================================================== */

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'La sesion no es valida.',
                ]);
        }


        try {

            /* =================================================
               PREPARAR DATOS DE EXPORTACION
            ================================================= */

            $servicio =
                new ListadoExportacionService();


            $resultado =
                $servicio->preparar(
                    $this->request
                );


            /* =================================================
               GENERAR EXCEL
            ================================================= */

            $excel =
                new ListadoExcelService();


            $ruta =
                $excel->generar(
                    $resultado['reportes']
                    ?? [],
                    $resultado['secciones']
                    ?? []
                );


            /* =================================================
               DESCARGAR
            ================================================= */

            if (
                !$ruta
                || !is_file($ruta)
            ) {

                throw new \RuntimeException(
                    'El archivo de Excel no fue generado correctamente.'
                );
            }

            $nombre =
                basename(
                    $ruta
                );

            $contenido =
                file_get_contents(
                    $ruta
                );

            if (
                $contenido === false
                || $contenido === ''
            ) {

                throw new \RuntimeException(
                    'No fue posible leer el archivo de Excel generado.'
                );
            }

            if (
                !unlink(
                    $ruta
                )
            ) {

                log_message(
                    'warning',
                    'No fue posible eliminar el archivo temporal de exportacion: {ruta}',
                    [
                        'ruta' =>
                            $ruta,
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
                ->setJSON([
                    'success' => false,
                    'message' =>
                        $e->getMessage(),
                ]);


        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error exportando listado: {mensaje}',
                [
                    'mensaje' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                        'No fue posible generar el archivo de Excel.',
                ]);
        }
    }
}
