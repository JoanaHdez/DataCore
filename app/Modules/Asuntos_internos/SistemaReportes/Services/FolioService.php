<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use CodeIgniter\Database\BaseConnection;

class FolioService
{
    protected BaseConnection $db;


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct(
        ?BaseConnection $db = null
    ) {

        $this->db =
            $db
            ?? \Config\Database::connect(
                'datacore'
            );
    }


    /* =========================================================
       GENERAR FOLIO AUTOMÁTICO
    ========================================================= */

    public function generar(
        string $claveFolio
    ): array {

        /* =====================================================
           NORMALIZAR CLAVE
        ===================================================== */

        $claveFolio =
            strtoupper(
                trim(
                    $claveFolio
                )
            );


        /* =====================================================
           VALIDAR CLAVE
        ===================================================== */

        $clavesPermitidas = [
            'QJ',
            'QJV',
            'QJF',
            'FEL',
        ];


        if (
            !in_array(
                $claveFolio,
                $clavesPermitidas,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'La clave de folio solicitada no es válida.'
            );
        }


        /* =====================================================
           OBTENER Y BLOQUEAR CONSECUTIVO
        ===================================================== */

        $registro =
            $this->db
                ->query(
                    "
                    SELECT
                        id_consecutivo,
                        tipo_registro,
                        clave_folio,
                        nomenclatura,
                        ultimo_numero
                    FROM ai_folios_consecutivos
                    WHERE clave_folio = ?
                    FOR UPDATE
                    ",
                    [
                        $claveFolio,
                    ]
                )
                ->getRowArray();


        if (!$registro) {

            throw new \RuntimeException(
                'No existe configuración de folio para la clave solicitada.'
            );
        }


        /* =====================================================
           TIPO GENERAL DEL REGISTRO
        ===================================================== */

        $tipoRegistro =
            strtoupper(
                trim(
                    (string) (
                        $registro['tipo_registro']
                        ?? ''
                    )
                )
            );


        if (
            !in_array(
                $tipoRegistro,
                [
                    'QUEJA',
                    'FELICITACION',
                ],
                true
            )
        ) {

            throw new \RuntimeException(
                'El tipo de registro asociado al folio no es válido.'
            );
        }


        /* =====================================================
           NOMENCLATURA BASE
        ===================================================== */

        $nomenclaturaBase =
            trim(
                (string) (
                    $registro['nomenclatura']
                    ?? ''
                )
            );


        if ($nomenclaturaBase === '') {

            throw new \RuntimeException(
                'La nomenclatura del folio no está configurada.'
            );
        }


        /* =====================================================
           SIGUIENTE CONSECUTIVO
        ===================================================== */

        $ultimoNumero =
            (int) (
                $registro['ultimo_numero']
                ?? 0
            );


        $numeroFolio =
            $ultimoNumero + 1;


        if ($numeroFolio <= 0) {

            throw new \RuntimeException(
                'No fue posible determinar el siguiente número de folio.'
            );
        }


        /* =====================================================
           CONSTRUIR FOLIO
        ===================================================== */

        $folio =
            $claveFolio
            . '-'
            . $numeroFolio;


        /* =====================================================
           NOMENCLATURA
           
           Por ahora conservamos la construcción existente.
           Más adelante la ajustaremos para incluir el año
           automáticamente según fecha_registro.
        ===================================================== */

        $nomenclatura =
            $nomenclaturaBase
            . $numeroFolio;


        /* =====================================================
           ACTUALIZAR CONSECUTIVO
        ===================================================== */

        $actualizado =
            $this->db
                ->table(
                    'ai_folios_consecutivos'
                )
                ->where(
                    'id_consecutivo',
                    (int) $registro['id_consecutivo']
                )
                ->where(
                    'clave_folio',
                    $claveFolio
                )
                ->update([
                    'ultimo_numero' =>
                        $numeroFolio,
                ]);


        if ($actualizado === false) {

            throw new \RuntimeException(
                'No fue posible actualizar el consecutivo del folio.'
            );
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [

            'tipo_registro' =>
                $tipoRegistro,

            'clave_folio' =>
                $claveFolio,

            'numero_folio' =>
                $numeroFolio,

            'prefijo_folio' =>
                $claveFolio,

            'folio' =>
                $folio,

            'nomenclatura' =>
                $nomenclatura,

        ];
    }


    /* =========================================================
       PREVISUALIZAR SIGUIENTE FOLIO
    ========================================================= */

    public function previsualizar(
        string $claveFolio
    ): array {

        /* =====================================================
           NORMALIZAR CLAVE
        ===================================================== */

        $claveFolio =
            strtoupper(
                trim(
                    $claveFolio
                )
            );


        /* =====================================================
           VALIDAR CLAVE
        ===================================================== */

        $clavesPermitidas = [
            'QJ',
            'QJV',
            'QJF',
            'FEL',
        ];


        if (
            !in_array(
                $claveFolio,
                $clavesPermitidas,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'La clave de folio solicitada no es válida.'
            );
        }


        /* =====================================================
           CONSULTAR CONSECUTIVO ACTUAL
        ===================================================== */

        $registro =
            $this->db
                ->table(
                    'ai_folios_consecutivos'
                )
                ->select([
                    'tipo_registro',
                    'clave_folio',
                    'nomenclatura',
                    'ultimo_numero',
                ])
                ->where(
                    'clave_folio',
                    $claveFolio
                )
                ->get()
                ->getRowArray();


        if (!$registro) {

            throw new \RuntimeException(
                'No existe configuración de folio para la clave solicitada.'
            );
        }


        /* =====================================================
           TIPO GENERAL
        ===================================================== */

        $tipoRegistro =
            strtoupper(
                trim(
                    (string) (
                        $registro['tipo_registro']
                        ?? ''
                    )
                )
            );


        /* =====================================================
           SIGUIENTE NÚMERO
        ===================================================== */

        $numeroFolio =
            (int) (
                $registro['ultimo_numero']
                ?? 0
            )
            + 1;


        if ($numeroFolio <= 0) {

            throw new \RuntimeException(
                'No fue posible determinar el siguiente número de folio.'
            );
        }


        /* =====================================================
           NOMENCLATURA BASE
        ===================================================== */

        $nomenclaturaBase =
            trim(
                (string) (
                    $registro['nomenclatura']
                    ?? ''
                )
            );


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [

            'tipo_registro' =>
                $tipoRegistro,

            'clave_folio' =>
                $claveFolio,

            'numero_folio' =>
                $numeroFolio,

            'prefijo_folio' =>
                $claveFolio,

            'folio' =>
                $claveFolio
                . '-'
                . $numeroFolio,

            'nomenclatura' =>
                $nomenclaturaBase
                . $numeroFolio,

        ];
    }
}