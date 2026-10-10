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
         * Si ya existe una sesiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n vÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡lida del SistemaReportes,
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
           VALIDACIÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œN BÃƒÆ’Ã†â€™Ãƒâ€šÃ‚ÂSICA
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
                    'Ingresa tu nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³mina y CURP.'
                );

        }


        /* =====================================================
           AUTENTICACIÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œN REAL
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
                'Error durante autenticaciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n de SistemaReportes: {mensaje}',
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
                    'No fue posible iniciar sesiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n. IntÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©ntalo nuevamente.'
                );

        }


        /* =====================================================
           CREDENCIALES / ACCESO INVÃƒÆ’Ã†â€™Ãƒâ€šÃ‚ÂLIDO
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
                    ?? 'No fue posible iniciar sesiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n.'
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
                    'No fue posible preparar la sesiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n del usuario.'
                );

        }

        /* =====================================================
           REGENERAR SESIÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œN
        ===================================================== */

        /*
         * Regeneramos el identificador de sesiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n despuÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©s
         * de iniciar sesiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n para evitar session fixation.
         */
        session()->regenerate(
            true
        );


        /* =====================================================
           SESIÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œN EXCLUSIVA DE SISTEMA REPORTES
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
           REDIRECCIÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œN
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
         * Eliminamos ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âºnicamente las variables
         * pertenecientes a SistemaReportes.
         *
         * No destruimos indiscriminadamente toda la sesiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n
         * del proyecto DataCore.
         */
        session()->remove([
            'usuario_reportes',
            'reportes_autenticado',
            'reportes_dashboard_autorizado',
        ]);


        /*
         * Regeneramos nuevamente el ID despuÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©s del logout.
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
