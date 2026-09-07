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
        string $tipoRegistro
    ): array {

        /* =====================================================
           NORMALIZAR TIPO
        ===================================================== */

        $tipoRegistro =
            strtoupper(
                trim(
                    $tipoRegistro
                )
            );


        $tiposPermitidos = [
            'QUEJA',
            'FELICITACION',
        ];


        if (
            !in_array(
                $tipoRegistro,
                $tiposPermitidos,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'El tipo de registro para generar el folio no es válido.'
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
                        nomenclatura,
                        ultimo_numero
                    FROM ai_folios_consecutivos
                    WHERE tipo_registro = ?
                    FOR UPDATE
                    ",
                    [
                        $tipoRegistro,
                    ]
                )
                ->getRowArray();


        if (!$registro) {

            throw new \RuntimeException(
                'No existe configuración de folio para el tipo de registro solicitado.'
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
           PREFIJO CORTO
        ===================================================== */

        $prefijoFolio =
            match ($tipoRegistro) {

                'QUEJA' =>
                    'QJ',

                'FELICITACION' =>
                    'FEL',

            };


        /* =====================================================
           CONSTRUIR IDENTIFICADORES
        ===================================================== */

        $folio =
            $prefijoFolio
            . '-'
            . $numeroFolio;


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
                    'tipo_registro',
                    $tipoRegistro
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

            'numero_folio' =>
                $numeroFolio,

            'prefijo_folio' =>
                $prefijoFolio,

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
        string $tipoRegistro
    ): array {

        /* =====================================================
        NORMALIZAR TIPO
        ===================================================== */

        $tipoRegistro =
            strtoupper(
                trim(
                    $tipoRegistro
                )
            );


        $tiposPermitidos = [
            'QUEJA',
            'FELICITACION',
        ];


        if (
            !in_array(
                $tipoRegistro,
                $tiposPermitidos,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'El tipo de registro para consultar el folio no es válido.'
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
                    'nomenclatura',
                    'ultimo_numero',
                ])
                ->where(
                    'tipo_registro',
                    $tipoRegistro
                )
                ->get()
                ->getRowArray();


        if (!$registro) {

            throw new \RuntimeException(
                'No existe configuración de folio para el tipo de registro solicitado.'
            );
        }


        /* =====================================================
        SIGUIENTE NÚMERO
        ===================================================== */

        $numeroFolio =
            (int) (
                $registro['ultimo_numero']
                ?? 0
            )
            + 1;


        $nomenclaturaBase =
            trim(
                (string) (
                    $registro['nomenclatura']
                    ?? ''
                )
            );


        /* =====================================================
        PREFIJO
        ===================================================== */

        $prefijoFolio =
            match ($tipoRegistro) {

                'QUEJA' =>
                    'QJ',

                'FELICITACION' =>
                    'FEL',

            };


        /* =====================================================
        RESPUESTA
        ===================================================== */

        return [

            'tipo_registro' =>
                $tipoRegistro,

            'numero_folio' =>
                $numeroFolio,

            'folio' =>
                $prefijoFolio
                . '-'
                . $numeroFolio,

            'nomenclatura' =>
                $nomenclaturaBase
                . $numeroFolio,

        ];
    }
}