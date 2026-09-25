<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;


class HistorialService
{

    private $db;
    private $dbPlantilla;

    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    /* =========================================================
    CONSTRUCTOR
    ========================================================= */

    public function __construct()
    {
        $this->db =
            \Config\Database::connect(
                'datacore'
            );


        $this->dbPlantilla =
            \Config\Database::connect(
                'plantilla'
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

    /* =========================================================
   OBTENER HISTORIAL GENERAL
========================================================= */

    public function obtenerHistorial(): array
    {
        return [

            'quejas' =>
            $this->obtenerHistorialQuejas(),

            'felicitaciones' =>
            $this->obtenerHistorialFelicitaciones(),

        ];
    }


    /* =========================================================
   OBTENER HISTORIAL DE QUEJAS
========================================================= */

    public function obtenerHistorialQuejas(): array
    {
        $registros =
            $this->db
            ->table(
                'ai_reporte_historial_cambios h'
            )
            ->select(
                "
            h.id_historial,
            h.tipo_entidad,
            h.id_entidad,
            h.id_reporte,
            h.tipo_accion,
            h.campo,
            h.valor_anterior,
            h.valor_nuevo,
            h.descripcion,
            h.created_by,
            h.created_at,

            r.folio,

            u.plantilla_id
            ",
                false
            )
            ->join(
                'ai_reportes r',
                'r.id_reporte = h.id_reporte',
                'left'
            )
            ->join(
                'dc_usuarios u',
                'u.id_usuario = h.created_by',
                'left'
            )
            ->whereIn(
                'h.tipo_entidad',
                [
                    'REPORTE',
                    'SEGUIMIENTO',
                ]
            )
            ->orderBy(
                'h.created_at',
                'DESC'
            )
            ->orderBy(
                'h.id_historial',
                'DESC'
            )
            ->get()
            ->getResultArray();


        return $this->prepararHistorial(
            $registros
        );
    }


    /* =========================================================
   OBTENER HISTORIAL DE FELICITACIONES
========================================================= */

    public function obtenerHistorialFelicitaciones(): array
    {
        $registros =
            $this->db
            ->table(
                'ai_reporte_historial_cambios h'
            )
            ->select(
                "
            h.id_historial,
            h.tipo_entidad,
            h.id_entidad,
            h.id_reporte,
            h.tipo_accion,
            h.campo,
            h.valor_anterior,
            h.valor_nuevo,
            h.descripcion,
            h.created_by,
            h.created_at,

            f.folio,

            u.plantilla_id
            ",
                false
            )
            ->join(
                'ai_felicitaciones f',
                'f.id_felicitacion = h.id_entidad',
                'left'
            )
            ->join(
                'dc_usuarios u',
                'u.id_usuario = h.created_by',
                'left'
            )
            ->where(
                'h.tipo_entidad',
                'FELICITACION'
            )
            ->orderBy(
                'h.created_at',
                'DESC'
            )
            ->orderBy(
                'h.id_historial',
                'DESC'
            )
            ->get()
            ->getResultArray();


        return $this->prepararHistorial(
            $registros
        );
    }


    /* =========================================================
   PREPARAR HISTORIAL
========================================================= */

    private function prepararHistorial(
        array $registros
    ): array {

        if (
            empty($registros)
        ) {

            return [];
        }


        /* =====================================================
       IDS DE PLANTILLA
    ===================================================== */

        $idsPlantilla = [];


        foreach (
            $registros
            as $registro
        ) {

            $plantillaId =
                (int) (
                    $registro['plantilla_id']
                    ?? 0
                );


            if (
                $plantillaId > 0
            ) {

                $idsPlantilla[] =
                    $plantillaId;
            }
        }


        $idsPlantilla =
            array_values(
                array_unique(
                    $idsPlantilla
                )
            );


        /* =====================================================
       CONSULTAR USUARIOS EN PLANTILLA
    ===================================================== */

        $usuariosPlantilla = [];


        if (
            !empty($idsPlantilla)
        ) {

            $personal =
                $this->dbPlantilla
                ->table(
                    'plantilla'
                )
                ->select([
                    'ID',
                    'PERSCOD',
                    'NOMBRE_COMPLETO',
                ])
                ->whereIn(
                    'ID',
                    $idsPlantilla
                )
                ->get()
                ->getResultArray();


            foreach (
                $personal
                as $persona
            ) {

                $id =
                    (int) (
                        $persona['ID']
                        ?? 0
                    );


                if (
                    $id <= 0
                ) {

                    continue;
                }


                $usuariosPlantilla[$id] =
                    $persona;
            }
        }


        /* =====================================================
       ARMAR RESPUESTA
    ===================================================== */

        $resultado = [];


        foreach (
            $registros
            as $registro
        ) {

            $plantillaId =
                (int) (
                    $registro['plantilla_id']
                    ?? 0
                );


            $datosUsuario =
                $usuariosPlantilla[$plantillaId]
                ?? [];


            /* =================================================
           NOMBRE
        ================================================= */

            $nombre =
                trim(
                    (string) (
                        $datosUsuario['NOMBRE_COMPLETO']
                        ?? ''
                    )
                );


            if (
                $nombre === ''
            ) {

                $nombre =
                    'Usuario #'
                    . (
                        (int) (
                            $registro['created_by']
                            ?? 0
                        )
                    );
            }


            /* =================================================
           PERSCOD
        ================================================= */

            $perscod =
                trim(
                    (string) (
                        $datosUsuario['PERSCOD']
                        ?? ''
                    )
                );


            /* =================================================
           FOTO
        ================================================= */

            $foto =
                null;


            if (
                $perscod !== ''
            ) {

                $foto =
                    'http://10.8.6.2:8083/dgsc/images/fotos/'
                    . rawurlencode(
                        $perscod
                    )
                    . '/F.F.R.E.jpg';
            }


            /* =================================================
           INICIAL / FALLBACK
        ================================================= */

            $inicial =
                $this->obtenerInicialUsuario(
                    $nombre
                );


            /* =================================================
           FECHA Y HORA
        ================================================= */

            $fechaHoraOriginal =
                trim(
                    (string) (
                        $registro['created_at']
                        ?? ''
                    )
                );


            $fecha =
                '—';


            $hora =
                '—';


            $fechaHora =
                '—';


            if (
                $fechaHoraOriginal !== ''
            ) {

                try {

                    $fechaObjeto =
                        new \DateTime(
                            $fechaHoraOriginal
                        );


                    $fecha =
                        $fechaObjeto->format(
                            'd/m/Y'
                        );


                    $hora =
                        $fechaObjeto->format(
                            'H:i'
                        );


                    $fechaHora =
                        $fechaObjeto->format(
                            'd/m/Y H:i'
                        );
                } catch (\Throwable $e) {

                    $fechaHora =
                        $fechaHoraOriginal;
                }
            }


            /* =================================================
           FOLIO
        ================================================= */

            $folio =
                trim(
                    (string) (
                        $registro['folio']
                        ?? ''
                    )
                );


            if (
                $folio === ''
            ) {

                $folio =
                    '—';
            }


            /* =================================================
           ACCIÓN
        ================================================= */

            $accion =
                $this->construirTextoAccion(
                    $registro
                );


            /* =================================================
           RESULTADO
        ================================================= */

            $resultado[] = [

                'id_historial' =>
                (int) (
                    $registro['id_historial']
                    ?? 0
                ),

                'tipo_entidad' =>
                trim(
                    (string) (
                        $registro['tipo_entidad']
                        ?? ''
                    )
                ),

                'tipo_accion' =>
                trim(
                    (string) (
                        $registro['tipo_accion']
                        ?? ''
                    )
                ),

                'fecha' =>
                $fecha,

                'hora' =>
                $hora,

                'fecha_hora' =>
                $fechaHora,

                'folio' =>
                $folio,

                'accion' =>
                $accion,

                'campo' =>
                $registro['campo']
                    ?? null,

                'valor_anterior' =>
                $registro['valor_anterior']
                    ?? null,

                'valor_nuevo' =>
                $registro['valor_nuevo']
                    ?? null,

                'usuario' => [

                    'id_usuario' =>
                    (int) (
                        $registro['created_by']
                        ?? 0
                    ),

                    'plantilla_id' =>
                    $plantillaId,

                    'nombre' =>
                    $nombre,

                    'perscod' =>
                    $perscod,

                    'foto' =>
                    $foto,

                    'inicial' =>
                    $inicial,

                ],

            ];
        }


        return $resultado;
    }


    /* =========================================================
    CONSTRUIR TEXTO DE ACCIÓN
    ========================================================= */

    private function construirTextoAccion(
        array $registro
    ): string {

        $tipoAccion =
            strtoupper(
                trim(
                    (string) (
                        $registro['tipo_accion']
                        ?? ''
                    )
                )
            );


        $descripcion =
            trim(
                (string) (
                    $registro['descripcion']
                    ?? ''
                )
            );


        /* =====================================================
        CAMBIO DE ESTADO
        ===================================================== */

        if (
            $tipoAccion === 'CAMBIAR_ESTADO'
        ) {

            $anterior =
                trim(
                    (string) (
                        $registro['valor_anterior']
                        ?? ''
                    )
                );


            $nuevo =
                trim(
                    (string) (
                        $registro['valor_nuevo']
                        ?? ''
                    )
                );


            if (
                $anterior !== ''
                && $nuevo !== ''
            ) {

                return
                    'Cambió el estado de '
                    . $anterior
                    . ' a '
                    . $nuevo
                    . '.';
            }
        }


        /* =====================================================
        DESCRIPCIÓN GUARDADA
        ===================================================== */

        if (
            $descripcion !== ''
        ) {

            return $descripcion;
        }


        /* =====================================================
        FALLBACK
        ===================================================== */

        return match ($tipoAccion) {

            'CREAR' =>
            'Agregó el registro.',

            'EDITAR' =>
            'Editó el registro.',

            'ELIMINAR' =>
            'Eliminó el registro.',

            default =>
            'Realizó una acción sobre el registro.',
        };
    }


    /* =========================================================
    OBTENER INICIAL DEL USUARIO
    ========================================================= */

    private function obtenerInicialUsuario(
        string $nombre
    ): string {

        $nombre =
            trim(
                $nombre
            );


        if (
            $nombre === ''
        ) {

            return '?';
        }


        return mb_strtoupper(
            mb_substr(
                $nombre,
                0,
                1,
                'UTF-8'
            ),
            'UTF-8'
        );
    }
}
