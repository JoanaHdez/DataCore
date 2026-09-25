<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;


class HistorialService
{

    private $db;


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct()
    {
        $this->db =
            \Config\Database::connect(
                'datacore'
            );
    }


    /* =========================================================
       REGISTRAR EVENTO
    ========================================================= */

    public function registrar(
        string $tipoEntidad,
        ?int $idEntidad,
        ?int $idReporte,
        string $tipoAccion,
        int $idUsuario,
        ?string $descripcion = null,
        ?string $campo = null,
        mixed $valorAnterior = null,
        mixed $valorNuevo = null
    ): bool {

        /* =====================================================
           VALIDACIONES
        ===================================================== */

        $tipoEntidad =
            strtoupper(
                trim(
                    $tipoEntidad
                )
            );


        $tipoAccion =
            strtoupper(
                trim(
                    $tipoAccion
                )
            );


        $tiposPermitidos = [
            'REPORTE',
            'FELICITACION',
            'SEGUIMIENTO',
            'SANCION',
            'EVIDENCIA',
        ];


        if (
            !in_array(
                $tipoEntidad,
                $tiposPermitidos,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'El tipo de entidad del historial no es válido.'
            );
        }


        if (
            $tipoAccion === ''
        ) {

            throw new \InvalidArgumentException(
                'La acción del historial es obligatoria.'
            );
        }


        if (
            $idUsuario <= 0
        ) {

            throw new \InvalidArgumentException(
                'El usuario del historial no es válido.'
            );
        }


        if (
            $idEntidad !== null
            && $idEntidad <= 0
        ) {

            $idEntidad = null;
        }


        if (
            $idReporte !== null
            && $idReporte <= 0
        ) {

            $idReporte = null;
        }


        /* =====================================================
           NORMALIZAR VALORES
        ===================================================== */

        $descripcion =
            $this->normalizarTextoNullable(
                $descripcion
            );


        $campo =
            $this->normalizarTextoNullable(
                $campo
            );


        $valorAnterior =
            $this->normalizarValorHistorial(
                $valorAnterior
            );


        $valorNuevo =
            $this->normalizarValorHistorial(
                $valorNuevo
            );


        /* =====================================================
           INSERTAR
        ===================================================== */

        return $this->db
            ->table(
                'ai_reporte_historial_cambios'
            )
            ->insert([
                'tipo_entidad' =>
                $tipoEntidad,

                'id_entidad' =>
                $idEntidad,

                'id_reporte' =>
                $idReporte,

                'tipo_accion' =>
                $tipoAccion,

                'campo' =>
                $campo,

                'valor_anterior' =>
                $valorAnterior,

                'valor_nuevo' =>
                $valorNuevo,

                'descripcion' =>
                $descripcion,

                'created_by' =>
                $idUsuario,

                'created_at' =>
                date('Y-m-d H:i:s'),
            ]);
    }


    /* =========================================================
       REGISTRAR CREACIÓN DE REPORTE
    ========================================================= */

    public function registrarCreacionReporte(
        int $idReporte,
        int $idUsuario
    ): bool {

        if (
            $idReporte <= 0
        ) {

            throw new \InvalidArgumentException(
                'El identificador del reporte no es válido.'
            );
        }


        return $this->registrar(
            'REPORTE',
            $idReporte,
            $idReporte,
            'CREAR',
            $idUsuario,
            'Agregó el reporte.'
        );
    }


    /* =========================================================
       NORMALIZAR TEXTO NULLABLE
    ========================================================= */

    private function normalizarTextoNullable(
        mixed $valor
    ): ?string {

        if (
            $valor === null
        ) {

            return null;
        }


        $texto =
            trim(
                (string) $valor
            );


        return $texto !== ''
            ? $texto
            : null;
    }


    /* =========================================================
       NORMALIZAR VALOR PARA HISTORIAL
    ========================================================= */

    private function normalizarValorHistorial(
        mixed $valor
    ): ?string {

        if (
            $valor === null
        ) {

            return null;
        }


        if (
            is_array($valor)
            || is_object($valor)
        ) {

            $json =
                json_encode(
                    $valor,
                    JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                );


            return $json !== false
                ? $json
                : null;
        }


        $texto =
            trim(
                (string) $valor
            );


        return $texto !== ''
            ? $texto
            : null;
    }

    /* =========================================================
    REGISTRAR EDICIÓN DE REPORTE
    ========================================================= */

    public function registrarEdicionReporte(
        int $idReporte,
        int $idUsuario
    ): bool {

        if (
            $idReporte <= 0
        ) {

            throw new \InvalidArgumentException(
                'El identificador del reporte no es válido.'
            );
        }


        return $this->registrar(
            'REPORTE',
            $idReporte,
            $idReporte,
            'EDITAR',
            $idUsuario,
            'Editó el reporte.'
        );
    }


    /* =========================================================
    REGISTRAR CAMBIO DE ESTADO DE REPORTE
    ========================================================= */

    public function registrarCambioEstadoReporte(
        int $idReporte,
        int $idUsuario,
        string $estadoAnterior,
        string $estadoNuevo
    ): bool {

        if (
            $idReporte <= 0
        ) {

            throw new \InvalidArgumentException(
                'El identificador del reporte no es válido.'
            );
        }


        $estadoAnterior =
            trim(
                $estadoAnterior
            );


        $estadoNuevo =
            trim(
                $estadoNuevo
            );


        if (
            $estadoAnterior === ''
            || $estadoNuevo === ''
            || $estadoAnterior === $estadoNuevo
        ) {

            return false;
        }


        return $this->registrar(
            'REPORTE',
            $idReporte,
            $idReporte,
            'CAMBIAR_ESTADO',
            $idUsuario,
            'Cambió el estado del reporte.',
            'estado_actual',
            $estadoAnterior,
            $estadoNuevo
        );
    }

    /* =========================================================
    REGISTRAR ELIMINACIÓN DE REPORTE
    ========================================================= */

    public function registrarEliminacionReporte(
        int $idReporte,
        int $idUsuario
    ): bool {

        if (
            $idReporte <= 0
        ) {

            throw new \InvalidArgumentException(
                'El identificador del reporte no es válido.'
            );
        }


        return $this->registrar(
            'REPORTE',
            $idReporte,
            $idReporte,
            'ELIMINAR',
            $idUsuario,
            'Eliminó el reporte.'
        );
    }

    /* =========================================================
    REGISTRAR CREACIÓN DE SEGUIMIENTO
    ========================================================= */

    public function registrarCreacionSeguimiento(
        int $idSeguimiento,
        int $idReporte,
        int $idUsuario
    ): bool {

        if (
            $idSeguimiento <= 0
            || $idReporte <= 0
        ) {

            throw new \InvalidArgumentException(
                'Los identificadores del seguimiento no son válidos.'
            );
        }


        return $this->registrar(
            'SEGUIMIENTO',
            $idSeguimiento,
            $idReporte,
            'CREAR',
            $idUsuario,
            'Agregó seguimiento.'
        );
    }

    /* =========================================================
    REGISTRAR EDICIÓN DE SEGUIMIENTO
    ========================================================= */

    public function registrarEdicionSeguimiento(
        int $idSeguimiento,
        int $idReporte,
        int $idUsuario
    ): bool {

        if (
            $idSeguimiento <= 0
            || $idReporte <= 0
        ) {

            throw new \InvalidArgumentException(
                'Los identificadores del seguimiento no son válidos.'
            );
        }


        return $this->registrar(
            'SEGUIMIENTO',
            $idSeguimiento,
            $idReporte,
            'EDITAR',
            $idUsuario,
            'Editó seguimiento.'
        );
    }

    /* =========================================================
    REGISTRAR CREACIÓN DE FELICITACIÓN
    ========================================================= */

    public function registrarCreacionFelicitacion(
        int $idFelicitacion,
        int $idUsuario
    ): bool {

        if (
            $idFelicitacion <= 0
        ) {

            throw new \InvalidArgumentException(
                'El identificador de la felicitación no es válido.'
            );
        }


        return $this->registrar(
            'FELICITACION',
            $idFelicitacion,
            null,
            'CREAR',
            $idUsuario,
            'Agregó la felicitación.'
        );
    }

    /* =========================================================
    REGISTRAR EDICIÓN DE FELICITACIÓN
    ========================================================= */

    public function registrarEdicionFelicitacion(
        int $idFelicitacion,
        int $idUsuario
    ): bool {

        if (
            $idFelicitacion <= 0
        ) {

            throw new \InvalidArgumentException(
                'El identificador de la felicitación no es válido.'
            );
        }


        return $this->registrar(
            'FELICITACION',
            $idFelicitacion,
            null,
            'EDITAR',
            $idUsuario,
            'Editó la felicitación.'
        );
    }


    /* =========================================================
    REGISTRAR ELIMINACIÓN DE FELICITACIÓN
    ========================================================= */

    public function registrarEliminacionFelicitacion(
        int $idFelicitacion,
        int $idUsuario
    ): bool {

        if (
            $idFelicitacion <= 0
        ) {

            throw new \InvalidArgumentException(
                'El identificador de la felicitación no es válido.'
            );
        }


        return $this->registrar(
            'FELICITACION',
            $idFelicitacion,
            null,
            'ELIMINAR',
            $idUsuario,
            'Eliminó la felicitación.'
        );
    }
}
