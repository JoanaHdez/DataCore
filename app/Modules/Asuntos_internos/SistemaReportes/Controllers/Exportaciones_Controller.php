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

            return $this->response
                ->download(
                    $ruta,
                    null
                )
                ->setFileName(
                    basename(
                        $ruta
                    )
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
