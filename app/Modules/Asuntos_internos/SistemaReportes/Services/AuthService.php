<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use CodeIgniter\Database\BaseConnection;

class AuthService
{
    private BaseConnection $dbPlantilla;
    private BaseConnection $dbDataCore;

    /**
     * ID del administrador oficial del Sistema de Reportes.
     */
    private const ADMIN_PLANTILLA_ID = 758;

    /**
     * Roles locales de DataCore.
     */
    private const ROL_ADMIN   = 'admin';
    private const ROL_USUARIO = 'usuario';


    public function __construct()
    {
        $this->dbPlantilla =
            \Config\Database::connect('plantilla');

        $this->dbDataCore =
            \Config\Database::connect('datacore');
    }


    /**
     * =========================================================
     * AUTENTICAR
     * =========================================================
     *
     * Usuario:
     *     NO_NOMINA
     *
     * Contraseña:
     *     CURP
     *
     * Requisitos:
     *     ESTADO = ACTIVO
     *     TIPO_NOMINA = RAMO 33
     */
    public function autenticar(
        string $nomina,
        string $curp
    ): array {

        $nomina = trim($nomina);
        $curp   = trim($curp);


        if (
            $nomina === ''
            || $curp === ''
        ) {

            return [
                'ok'      => false,
                'mensaje' => 'Ingresa tu nómina y contraseña.',
            ];
        }


        /*
         * IMPORTANTE:
         *
         * CURP se utiliza únicamente para validar contra
         * plantilla_general.plantilla.
         *
         * No se guarda en DataCore.
         * No se agrega a la sesión.
         * No se devuelve al controlador.
         */
        $persona =
            $this->dbPlantilla
            ->table('plantilla')
            ->select([
                'ID',
                'PERSCOD',
                'NOMBRE_COMPLETO',
                'NO_NOMINA',
                'TIPO_NOMINA',
                'AREA',
                'TURNO',
            ])
            ->where('NO_NOMINA', $nomina)
            ->where('CURP', $curp)
            ->where('ESTADO', 'ACTIVO')
            ->get()
            ->getRowArray();


        if (!$persona) {

            return [
                'ok'      => false,
                'mensaje' => 'Nómina o contraseña incorrecta.',
            ];
        }


        $plantillaId =
            (int) $persona['ID'];


        /*
         * =====================================================
         * DETERMINAR ROL
         * =====================================================
         */

        if (
            $plantillaId === self::ADMIN_PLANTILLA_ID
        ) {

            $rol = self::ROL_ADMIN;
        } else {

            $tipoNomina =
                trim(
                    strtoupper(
                        (string) ($persona['TIPO_NOMINA'] ?? '')
                    )
                );


            $area =
                trim(
                    strtoupper(
                        (string) ($persona['AREA'] ?? '')
                    )
                );


            if (
                $tipoNomina !== 'RAMO 33'
                || $area !== 'COORDINACION DE ASUNTOS INTERNOS'
            ) {

                return [
                    'ok'      => false,
                    'mensaje' =>
                    'No tienes autorización para ingresar a este sistema.',
                ];
            }


            $rol = self::ROL_USUARIO;
        }

        /*
         * =====================================================
         * USUARIO LOCAL
         * =====================================================
         */

        $usuarioLocal =
            $this->registrarOActualizarUsuario(
                $plantillaId,
                $rol
            );


        if (!$usuarioLocal) {

            return [
                'ok'      => false,
                'mensaje' => 'No fue posible preparar la sesión del usuario.',
            ];
        }


        /*
         * =====================================================
         * RESULTADO SEGURO
         * =====================================================
         */

        return [
            'ok' => true,

            'usuario' => [

                /*
                 * Identidad local DataCore
                 */
                'id_usuario' =>
                (int) $usuarioLocal['id_usuario'],

                /*
                 * Identidad externa
                 */
                'plantilla_id' =>
                $plantillaId,

                'perscod' =>
                $persona['PERSCOD'] ?? null,

                'nombre' =>
                $persona['NOMBRE_COMPLETO'] ?? '',

                'nomina' =>
                $persona['NO_NOMINA'] ?? '',

                'area' =>
                $persona['AREA'] ?? '',

                'turno' =>
                $persona['TURNO'] ?? '',

                /*
                 * Rol del Sistema de Reportes
                 */
                'rol' =>
                $rol,

            ],
        ];
    }


    /**
     * =========================================================
     * REGISTRAR / ACTUALIZAR USUARIO LOCAL
     * =========================================================
     */
    private function registrarOActualizarUsuario(
        int $plantillaId,
        string $rol
    ): ?array {

        $rolLocal =
            $this->dbDataCore
            ->table('dc_roles')
            ->select([
                'id_rol',
                'clave',
            ])
            ->where('clave', $rol)
            ->where('activo', 1)
            ->get()
            ->getRowArray();


        if (!$rolLocal) {
            return null;
        }


        $usuarios =
            $this->dbDataCore
            ->table('dc_usuarios');


        $usuarioExistente =
            $usuarios
            ->where(
                'plantilla_id',
                $plantillaId
            )
            ->get()
            ->getRowArray();


        $ahora =
            date('Y-m-d H:i:s');


        /*
         * =====================================================
         * USUARIO YA EXISTE
         * =====================================================
         */

        if ($usuarioExistente) {

            $usuarios
                ->where(
                    'id_usuario',
                    $usuarioExistente['id_usuario']
                )
                ->update([
                    'id_rol'       =>
                    (int) $rolLocal['id_rol'],

                    'activo'       =>
                    1,

                    'ultimo_acceso' =>
                    $ahora,

                    'updated_at'   =>
                    $ahora,
                ]);


            return $usuarios
                ->where(
                    'id_usuario',
                    $usuarioExistente['id_usuario']
                )
                ->get()
                ->getRowArray();
        }


        /*
         * =====================================================
         * PRIMER ACCESO
         * =====================================================
         */

        $usuarios->insert([
            'plantilla_id' =>
            $plantillaId,

            'id_rol' =>
            (int) $rolLocal['id_rol'],

            'activo' =>
            1,

            'ultimo_acceso' =>
            $ahora,

            'created_at' =>
            $ahora,

            'updated_at' =>
            $ahora,
        ]);


        $idUsuario =
            $this->dbDataCore->insertID();


        if (!$idUsuario) {
            return null;
        }


        return $usuarios
            ->where(
                'id_usuario',
                $idUsuario
            )
            ->get()
            ->getRowArray();
    }

    /**
     * =========================================================
     * VALIDAR AUTORIZACIÓN DEL ADMINISTRADOR
     * =========================================================
     *
     * Valida la contraseña administrativa directamente contra
     * plantilla_general.plantilla.
     *
     * La autorización corresponde exclusivamente al usuario
     * cuyo ID de plantilla es 758.
     *
     * La CURP:
     * - no se guarda en DataCore
     * - no se guarda en sesión
     * - no se registra en logs
     */
    public function validarAutorizacionAdmin(
        string $curp
    ): bool {

        $curp = strtoupper(
            trim($curp)
        );


        if ($curp === '') {
            return false;
        }


        $administrador =
            $this->dbPlantilla
            ->table('plantilla')
            ->select('ID')
            ->where(
                'ID',
                self::ADMIN_PLANTILLA_ID
            )
            ->where(
                'CURP',
                $curp
            )
            ->where(
                'ESTADO',
                'ACTIVO'
            )
            ->get()
            ->getRowArray();


        return !empty($administrador);
    }
}
