<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use CodeIgniter\Database\BaseConnection;

class AuthService
{
    private BaseConnection $dbPlantilla;
    private BaseConnection $dbDataCore;

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
     * ContraseÃ±a:
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
                'mensaje' => 'Ingresa tu nÃ³mina y contraseÃ±a.',
            ];
        }


        /*
         * IMPORTANTE:
         *
         * CURP se utiliza Ãºnicamente para validar contra
         * plantilla_general.plantilla.
         *
         * No se guarda en DataCore.
         * No se agrega a la sesiÃ³n.
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
                'mensaje' => 'NÃ³mina o contraseÃ±a incorrecta.',
            ];
        }


        $plantillaId =
            (int) $persona['ID'];

        $rolLocalActual =
            $this->obtenerClaveRolPorPlantilla(
                $plantillaId
            );


        /*
         * =====================================================
         * DETERMINAR ROL
         * =====================================================
         */

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
                $rolLocalActual !== self::ROL_ADMIN
                && (
                    $tipoNomina !== 'RAMO 33'
                    || $area !== 'COORDINACION DE ASUNTOS INTERNOS'
                )
            ) {

                return [
                    'ok'      => false,
                    'mensaje' =>
                    'No tienes autorizaciÃ³n para ingresar a este sistema.',
                ];
            }


            $rol = self::ROL_USUARIO;

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
                'mensaje' => 'No fue posible preparar la sesiÃ³n del usuario.',
            ];
        }


        $rolSesion =
            $this->obtenerClaveRol(
                (int) (
                    $usuarioLocal['id_rol']
                    ?? 0
                )
            );


        if ($rolSesion === '') {

            return [
                'ok'      => false,
                'mensaje' => 'No fue posible identificar el rol del usuario.',
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
                $this->normalizarNombrePlantilla(
                    (string) ($persona['NOMBRE_COMPLETO'] ?? '')
                ),

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
                $rolSesion,

            ],
        ];
    }


    private function normalizarNombrePlantilla(
        string $nombre
    ): string {
        if (
            $nombre === ''
            || mb_check_encoding(
                $nombre,
                'UTF-8'
            )
        ) {
            return $nombre;
        }

        return mb_convert_encoding(
            $nombre,
            'UTF-8',
            'ISO-8859-1'
        );
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


    private function obtenerClaveRolPorPlantilla(
        int $plantillaId
    ): string {

        if ($plantillaId <= 0) {

            return '';
        }


        $usuario =
            $this->dbDataCore
            ->table('dc_usuarios')
            ->select([
                'id_rol',
            ])
            ->where(
                'plantilla_id',
                $plantillaId
            )
            ->where(
                'activo',
                1
            )
            ->get()
            ->getRowArray();


        return $this->obtenerClaveRol(
            (int) (
                $usuario['id_rol']
                ?? 0
            )
        );
    }


    private function obtenerClaveRol(
        int $idRol
    ): string {

        if ($idRol <= 0) {

            return '';
        }


        $rol =
            $this->dbDataCore
            ->table('dc_roles')
            ->select([
                'clave',
            ])
            ->where(
                'id_rol',
                $idRol
            )
            ->where(
                'activo',
                1
            )
            ->get()
            ->getRowArray();


        $clave =
            trim(
                (string) (
                    $rol['clave']
                    ?? ''
                )
            );


        return in_array(
            $clave,
            [
                self::ROL_ADMIN,
                self::ROL_USUARIO,
            ],
            true
        )
            ? $clave
            : '';
    }

    /**
     * =========================================================
     * VALIDAR AUTORIZACIÃ“N DEL ADMINISTRADOR
     * =========================================================
     *
     * Valida la contraseÃ±a administrativa directamente contra
     * plantilla_general.plantilla.
     *
     * La autorizaciÃ³n corresponde exclusivamente al usuario
     * con rol local de administrador.
     *
     * La CURP:
     * - no se guarda en DataCore
     * - no se guarda en sesiÃ³n
     * - no se registra en logs
     */
    public function validarAutorizacionAdmin(
        string $curp
    ): bool {

        return $this->validarAutorizacionAdministradores(
            $curp
        ) !== null;
    }
    /**
     * =========================================================
     * VALIDAR AUTORIZACIÃ“N DE ADMINISTRADORES
     * =========================================================
     *
     * Valida la contraseÃ±a contra cualquiera de los usuarios
     * locales que actualmente tenga el rol "admin".
     *
     * Devuelve informaciÃ³n del administrador que autorizÃ³
     * para poder registrar correctamente autorizado_por.
     *
     * La CURP:
     * - no se guarda en DataCore
     * - no se guarda en sesiÃ³n
     * - no se registra en logs
     */
    public function validarAutorizacionAdministradores(
        string $curp
    ): ?array {

        $curp =
            strtoupper(
                trim(
                    $curp
                )
            );


        if (
            $curp === ''
        ) {
            return null;
        }


        /* =====================================================
        OBTENER ADMINISTRADORES LOCALES
        ===================================================== */

        $administradores =
            $this->dbDataCore
                ->table('dc_usuarios u')
                ->select([
                    'u.id_usuario',
                    'u.plantilla_id',
                ])
                ->join(
                    'dc_roles r',
                    'r.id_rol = u.id_rol',
                    'inner'
                )
                ->where(
                    'r.clave',
                    self::ROL_ADMIN
                )
                ->where(
                    'r.activo',
                    1
                )
                ->where(
                    'u.activo',
                    1
                )
                ->get()
                ->getResultArray();


        if (
            empty($administradores)
        ) {
            return null;
        }


        /* =====================================================
        VALIDAR CONTRASEÃ‘A CONTRA CADA ADMINISTRADOR
        ===================================================== */

        foreach (
            $administradores
            as $administrador
        ) {

            $idUsuario =
                (int) (
                    $administrador['id_usuario']
                    ?? 0
                );


            $plantillaId =
                (int) (
                    $administrador['plantilla_id']
                    ?? 0
                );


            if (
                $idUsuario <= 0
                || $plantillaId <= 0
            ) {
                continue;
            }


            $persona =
                $this->dbPlantilla
                    ->table('plantilla')
                    ->select([
                        'ID',
                    ])
                    ->where(
                        'ID',
                        $plantillaId
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


            if (
                !$persona
            ) {
                continue;
            }


            /* =================================================
            ADMINISTRADOR IDENTIFICADO
            ================================================= */

            return [
                'id_usuario' =>
                    $idUsuario,

                'plantilla_id' =>
                    $plantillaId,
            ];
        }


        return null;
    }
}
