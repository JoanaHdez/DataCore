<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;
use App\Modules\Asuntos_internos\SistemaReportes\Services\AuthService;

class Auth_Controller extends BaseController
{
    /**
     * =========================================================
     * LOGIN
     * =========================================================
     */
    public function login()
    {
        /*
         * Si ya existe una sesión válida del SistemaReportes,
         * evitamos volver a mostrar el login.
         */
        if (
            session()->get('reportes_autenticado') === true
            && session()->has('usuario_reportes')
        ) {

            return redirect()->to(
                base_url(
                    'asuntos-internos/reportes/nuevo'
                )
            );

        }


        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\auth\login'
        );
    }


    /**
     * =========================================================
     * AUTENTICAR
     * =========================================================
     */
    public function autenticar()
    {
        $nomina =
            trim(
                (string)
                $this->request->getPost(
                    'nomina'
                )
            );


        $curp =
            strtoupper(
                trim(
                    (string)
                    $this->request->getPost(
                        'curp'
                    )
                )
            );


        /* =====================================================
           VALIDACIÓN BÁSICA
        ===================================================== */

        if (
            $nomina === ''
            || $curp === ''
        ) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Ingresa tu nómina y CURP.'
                );

        }


        /* =====================================================
           AUTENTICACIÓN REAL
        ===================================================== */

        try {

            $authService =
                new AuthService();


            $resultado =
                $authService->autenticar(
                    $nomina,
                    $curp
                );


        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error durante autenticación de SistemaReportes: {mensaje}',
                [
                    'mensaje' =>
                        $e->getMessage(),
                ]
            );


            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible iniciar sesión. Inténtalo nuevamente.'
                );

        }


        /* =====================================================
           CREDENCIALES / ACCESO INVÁLIDO
        ===================================================== */

        if (
            empty(
                $resultado['ok']
            )
        ) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $resultado['mensaje']
                    ?? 'No fue posible iniciar sesión.'
                );

        }


        $usuario =
            $resultado['usuario']
            ?? null;


        if (
            !is_array($usuario)
            || empty($usuario)
        ) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible preparar la sesión del usuario.'
                );

        }


        /* =====================================================
           REGENERAR SESIÓN
        ===================================================== */

        /*
         * Regeneramos el identificador de sesión después
         * de iniciar sesión para evitar session fixation.
         */
        session()->regenerate(
            true
        );


        /* =====================================================
           SESIÓN EXCLUSIVA DE SISTEMA REPORTES
        ===================================================== */

        session()->set([
            'usuario_reportes' =>
                [
                    'id_usuario' =>
                        (int) (
                            $usuario['id_usuario']
                            ?? 0
                        ),

                    'plantilla_id' =>
                        (int) (
                            $usuario['plantilla_id']
                            ?? 0
                        ),

                    'perscod' =>
                        $usuario['perscod']
                        ?? null,

                    'nombre' =>
                        $usuario['nombre']
                        ?? '',

                    'nomina' =>
                        $usuario['nomina']
                        ?? '',

                    'area' =>
                        $usuario['area']
                        ?? '',

                    'turno' =>
                        $usuario['turno']
                        ?? '',

                    'rol' =>
                        $usuario['rol']
                        ?? 'usuario',
                ],

            'reportes_autenticado' =>
                true,
        ]);


        /* =====================================================
           REDIRECCIÓN
        ===================================================== */

        return redirect()->to(
            base_url(
                'asuntos-internos/reportes/nuevo'
            )
        );
    }


    /**
     * =========================================================
     * LOGOUT
     * =========================================================
     */
    public function logout()
    {
        /*
         * Eliminamos únicamente las variables
         * pertenecientes a SistemaReportes.
         *
         * No destruimos indiscriminadamente toda la sesión
         * del proyecto DataCore.
         */
        session()->remove([
            'usuario_reportes',
            'reportes_autenticado',
            'reportes_dashboard_autorizado',
        ]);


        /*
         * Regeneramos nuevamente el ID después del logout.
         */
        session()->regenerate(
            true
        );


        return redirect()->to(
            base_url(
                'asuntos-internos/reportes'
            )
        );
    }
}