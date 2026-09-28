<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ReportesAuthFilter implements FilterInterface
{
    private const TIEMPO_INACTIVIDAD = 7200;

    private const VARIABLES_SESION_REPORTES = [
        'usuario_reportes',
        'reportes_autenticado',
        'reportes_dashboard_autorizado',
        'reportes_ultima_actividad',
    ];

    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        $ahora =
            time();


        $sesionValida =
            session()->get('reportes_autenticado') === true
            && session()->has('usuario_reportes');


        if ($sesionValida) {

            $ultimaActividad =
                (int) (
                    session()->get(
                        'reportes_ultima_actividad'
                    )
                    ?? 0
                );


            if (
                $ultimaActividad > 0
                && ($ahora - $ultimaActividad) < self::TIEMPO_INACTIVIDAD
            ) {

                session()->set(
                    'reportes_ultima_actividad',
                    $ahora
                );


                return null;
            }
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
                    'success' => false,
                    'session_expired' => true,
                    'message' =>
                        'Tu sesión ha expirado por inactividad.',
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
            )
            ->with(
                'error',
                'Tu sesión ha expirado por inactividad.'
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
