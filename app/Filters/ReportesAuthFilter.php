<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ReportesAuthFilter implements FilterInterface
{
    private const LIMITE_INACTIVIDAD = 43200;

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
        $sesion = session();

        $sesionValida =
            $sesion->get('reportes_autenticado') === true
            && $sesion->has('usuario_reportes');

        if ($sesionValida) {

            $ultimaActividad = (int) $sesion->get(
                'reportes_ultima_actividad'
            );

            $ahora = time();

            if (
                $ultimaActividad > 0
                && ($ahora - $ultimaActividad) < self::LIMITE_INACTIVIDAD
            ) {

                /*
                 * El ping se utilizará para comunicar
                 * actividad real del navegador.
                 *
                 * Las demás peticiones autenticadas
                 * también cuentan como actividad.
                 */
                $sesion->set(
                    'reportes_ultima_actividad',
                    $ahora
                );

                return null;
            }
        }

        $sesion->remove(
            self::VARIABLES_SESION_REPORTES
        );

        /* =====================================================
           PETICIONES QUE ESPERAN JSON
        ===================================================== */

        $accept = strtolower(
            (string) $request->getHeaderLine('Accept')
        );

        $esPeticionJson = str_contains(
            $accept,
            'application/json'
        );

        $esAjax = strtolower(
            (string) $request->getHeaderLine('X-Requested-With')
        ) === 'xmlhttprequest';

        if ($esPeticionJson || $esAjax) {

            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'La sesión ha expirado por inactividad.',
                    'redirect' => base_url(
                        'asuntos-internos/reportes'
                    ),
                ]);
        }

        return redirect()->to(
            base_url('asuntos-internos/reportes')
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