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
        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {
            return redirect()
                ->to(base_url('asuntos-internos/reportes'))
                ->with(
                    'error',
                    'Inicia sesión para continuar.'
                );
        }

        return null;
    }


    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No se requiere ninguna acción posterior.
    }
}