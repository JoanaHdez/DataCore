<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;

class Auth_Controller extends BaseController
{
    public function login()
    {
        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\auth\login'
        );
    }

    public function autenticar()
    {
        $nomina = trim((string) $this->request->getPost('nomina'));
        $curp = strtoupper(
            trim((string) $this->request->getPost('curp'))
        );

        /*Validacion temporal */

        if ($nomina === '' || $curp === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'ingresa tu nómina y CURP.'
                );
        }
        /*usuario temporal*/

        $usuarioTemporal = [
            'nomina' => $nomina,
            'nombre' => 'Usuario de prueba',
            'area' => 'Asuntos Internos',
        ];

        session()->set([
            'usuario_reportes' => $usuarioTemporal,
            'reportes_autenticado' => true,
        ]);

        return redirect()->to(
            base_url('asuntos-internos/reportes/nuevo')
        );
    }

    public function logout()
    {
        session()->remove([
            'usuario_reportes',
            'reportes_autenticado',
        ]);

        return redirect()->to(
            base_url('asuntos-internos/reportes')
        );
    }
}
