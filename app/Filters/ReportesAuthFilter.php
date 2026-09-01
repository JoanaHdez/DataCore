<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ReportesAuthFilter implements FilterInterface
{
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


        /* =====================================================
           PETICIONES QUE ESPERAN JSON
        ===================================================== */

        $accept =
            strtolower(
                (string) $request->getHeaderLine('Accept')
            );


        $esPeticionJson =
            str_contains(
                $accept,
                'application/json'
            );


        if ($esPeticionJson) {

            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' =>
                        'La sesión ha expirado. Inicia sesión nuevamente.',
                ]);
        }


        /* =====================================================
           NAVEGACIÓN NORMAL
        ===================================================== */

        return redirect()
            ->to(
                base_url(
                    'asuntos-internos/reportes'
                )
            )
            ->with(
                'error',
                'Inicia sesión para continuar.'
            );
    }


    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No se requiere ninguna acción posterior.
    }
}