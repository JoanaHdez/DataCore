<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ReportesAuthFilter implements FilterInterface
{
    private const VARIABLES_SESION_REPORTES = [
        'usuario_reportes',
        'reportes_autenticado',
        'reportes_dashboard_autorizado',
    ];

    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        $sesionValida =
            session()->get('reportes_autenticado') === true
            && session()->has('usuario_reportes');

        if ($sesionValida) {
            return null;
        }

        session()->remove(
            self::VARIABLES_SESION_REPORTES
        );

        /* =====================================================
           PETICIONES QUE ESPERAN JSON
        ===================================================== */

        $accept =
            strtolower(
                (string) $request->getHeaderLine(
                    'Accept'
                )
            );

        $esPeticionJson =
            str_contains(
                $accept,
                'application/json'
            );

        $esAjax =
            strtolower(
                (string) $request->getHeaderLine(
                    'X-Requested-With'
                )
            ) === 'xmlhttprequest';

        if (
            $esPeticionJson
            || $esAjax
        ) {

            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'La sesion no es valida.',

                    'redirect' =>
                        base_url(
                            'asuntos-internos/reportes'
                        ),
                ]);
        }

        /* =====================================================
           NAVEGACION NORMAL
        ===================================================== */

        return redirect()
            ->to(
                base_url(
                    'asuntos-internos/reportes'
                )
            );
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No se requiere ninguna accion posterior.
    }
}
