<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use CodeIgniter\HTTP\IncomingRequest;


class ListadoExportacionService
{

    /* =========================================================
       PREPARAR EXPORTACIÓN
    ========================================================= */

    public function preparar(
        IncomingRequest $request
    ): array {

        /* =====================================================
           CONFIGURACIÓN SOLICITADA
        ===================================================== */

        $secciones =
            $this->obtenerSecciones(
                $request
            );


        $tipos =
            $this->obtenerTipos(
                $request
            );


        $tiposFiltro =
            !empty(
                $tipos
            )
                ? $tipos
                : [
                    'QJ',
                    'QJF',
                    'QJV',
                ];


        $cantidad =
            $this->obtenerCantidad(
                $request
            );


        /* =====================================================
           RELACIONES NECESARIAS
        ===================================================== */

        $incluirPersonal =
            in_array(
                'personal',
                $secciones,
                true
            );


        $incluirUnidades =
            in_array(
                'unidades',
                $secciones,
                true
            );


        $incluirDireccionNotificacion =
            in_array(
                'direccion_notificacion',
                $secciones,
                true
            );


        $incluirClasificacion =
            in_array(
                'clasificacion',
                $secciones,
                true
            );


        $incluirSeguimientos =
            in_array(
                'seguimientos',
                $secciones,
                true
            );


        /* =====================================================
           CONEXIÓN
        ===================================================== */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =====================================================
           REPORTES PRINCIPALES
        ===================================================== */

        $builder =
            $db
                ->table(
                    'ai_reportes'
                )
                ->where(
                    'eliminado',
                    0
                );


        /* =================================================
           TIPOS

           Vacío = todos.
        ================================================= */

        $builder
            ->groupStart();

        foreach (
            $tiposFiltro
            as $indiceTipo => $tipo
        ) {

            $metodo =
                $indiceTipo === 0
                    ? 'like'
                    : 'orLike';

            $builder
                ->{$metodo}(
                    'folio',
                    $tipo . '-',
                    'after'
                );
        }

        $builder
            ->groupEnd();


        /* =================================================
           ORDEN
        ================================================= */

        $builder
            ->orderBy(
                'id_reporte',
                'DESC'
            );


        /* =================================================
           CANTIDAD

           Se aplica después del filtro.
        ================================================= */

        if (
            $cantidad !== null
        ) {

            $builder
                ->limit(
                    $cantidad
                );
        }


        $reportesDb =
            $builder
                ->get()
                ->getResultArray();


        /* =====================================================
           IDS
        ===================================================== */

        $idsReportes =
            [];


        foreach (
            $reportesDb
            as $reporte
        ) {

            $idReporte =
                (int) (
                    $reporte['id_reporte']
                    ?? 0
                );


            if (
                $idReporte > 0
            ) {

                $idsReportes[] =
                    $idReporte;
            }
        }


        /* =====================================================
           RELACIONES
        ===================================================== */

        $personalDb =
            [];


        $unidadesDb =
            [];


        $direccionesDb =
            [];


        $motivosDb =
            [];


        $seguimientosDb =
            [];


        $sancionesSeguimientoDb =
            [];


        if (
            !empty(
                $idsReportes
            )
        ) {

            /* =================================================
               PERSONAL
            ================================================= */

            if (
                $incluirPersonal
            ) {

                $personalDb =
                    $db
                        ->table(
                            'ai_reporte_personal'
                        )
                        ->select([
                            'id_reporte_personal',
                            'id_reporte',
                            'nombre_snapshot',
                            'area_snapshot',
                            'turno_snapshot',
                            'alias_snapshot',
                        ])
                        ->whereIn(
                            'id_reporte',
                            $idsReportes
                        )
                        ->orderBy(
                            'id_reporte',
                            'ASC'
                        )
                        ->orderBy(
                            'id_reporte_personal',
                            'ASC'
                        )
                        ->get()
                        ->getResultArray();
            }


            /* =================================================
               UNIDADES
            ================================================= */

            if (
                $incluirUnidades
            ) {

                $unidadesDb =
                    $db
                        ->table(
                            'ai_reporte_unidades u'
                        )
                        ->select([
                            'u.id_reporte_unidad',
                            'u.id_reporte',

                            'u.parque_vehicular_id',

                            'u.no_economico_snapshot',

                            'u.placas_snapshot',

                            'u.marca_snapshot',

                            'u.submarca_snapshot',

                            'u.color_snapshot',

                            'u.estatus_snapshot',

                            'u.servicio_snapshot',

                            'u.tipo_snapshot',

                            'u.id_origen',

                            'o.clave AS origen',
                        ])
                        ->join(
                            'ai_cat_origen_unidad o',
                            'o.id_origen = u.id_origen',
                            'left'
                        )
                        ->whereIn(
                            'u.id_reporte',
                            $idsReportes
                        )
                        ->orderBy(
                            'u.id_reporte',
                            'ASC'
                        )
                        ->orderBy(
                            'u.id_reporte_unidad',
                            'ASC'
                        )
                        ->get()
                        ->getResultArray();
            }


            /* =================================================
               DIRECCIÓN PARA NOTIFICACIÓN
            ================================================= */

            if (
                $incluirDireccionNotificacion
            ) {

                $direccionesDb =
                    $db
                        ->table(
                            'ai_reporte_direccion_notificacion'
                        )
                        ->select([
                            'id_direccion_notificacion',

                            'id_reporte',

                            'pertenece_neza',

                            'calle',

                            'numero_exterior',

                            'colonia',

                            'entre_calle',

                            'y_calle',

                            'municipio',

                            'estado',

                            'sector',

                            'cuadrante',

                            'id_cuadra',

                            'latitud',

                            'longitud',

                            'origen_ubicacion',
                        ])
                        ->whereIn(
                            'id_reporte',
                            $idsReportes
                        )
                        ->where(
                            'eliminado',
                            0
                        )
                        ->orderBy(
                            'id_reporte',
                            'ASC'
                        )
                        ->get()
                        ->getResultArray();
            }


            /* =================================================
               MOTIVOS
            ================================================= */

            if (
                $incluirClasificacion
            ) {

                $motivosDb =
                    $db
                        ->table(
                            'ai_reporte_motivos rm'
                        )
                        ->select([
                            'rm.id_reporte_motivo',

                            'rm.id_reporte',

                            'rm.id_motivo',

                            'm.motivo',

                            'm.sancion AS sancion',

                            's.id_sancion',

                            's.tipo AS sancion_registrada',

                            's.folio_sancion',

                            's.origen AS sancion_origen',
                        ])
                        ->join(
                            'ai_cat_motivos m',
                            'm.id_motivo = rm.id_motivo',
                            'left'
                        )
                        ->join(
                            'ai_reporte_sanciones s',
                            's.id_reporte_motivo = rm.id_reporte_motivo
                             AND s.eliminado = 0',
                            'left'
                        )
                        ->whereIn(
                            'rm.id_reporte',
                            $idsReportes
                        )
                        ->where(
                            'rm.eliminado',
                            0
                        )
                        ->orderBy(
                            'rm.id_reporte',
                            'ASC'
                        )
                        ->orderBy(
                            'rm.id_reporte_motivo',
                            'ASC'
                        )
                        ->get()
                        ->getResultArray();
            }


            /* =================================================
               SEGUIMIENTOS
            ================================================= */

            if (
                $incluirSeguimientos
            ) {

                $seguimientosDb =
                    $db
                        ->table(
                            'ai_reporte_seguimientos'
                        )
                        ->select([
                            'id_seguimiento',

                            'id_reporte',

                            'fecha',

                            'tipo',

                            'estado_resultante',

                            'observaciones',
                        ])
                        ->whereIn(
                            'id_reporte',
                            $idsReportes
                        )
                        ->where(
                            'eliminado',
                            0
                        )
                        ->orderBy(
                            'id_reporte',
                            'ASC'
                        )
                        ->orderBy(
                            'fecha',
                            'DESC'
                        )
                        ->orderBy(
                            'id_seguimiento',
                            'DESC'
                        )
                        ->get()
                        ->getResultArray();


                /* =============================================
                   IDS DE SEGUIMIENTOS
                ============================================== */

                $idsSeguimientos =
                    [];


                foreach (
                    $seguimientosDb
                    as $seguimiento
                ) {

                    $idSeguimiento =
                        (int) (
                            $seguimiento['id_seguimiento']
                            ?? 0
                        );


                    if (
                        $idSeguimiento > 0
                    ) {

                        $idsSeguimientos[] =
                            $idSeguimiento;
                    }
                }


                /* =============================================
                   SANCIONES GENERADAS POR SEGUIMIENTO
                ============================================== */

                if (
                    !empty(
                        $idsSeguimientos
                    )
                ) {

                    $sancionesSeguimientoDb =
                        $db
                            ->table(
                                'ai_reporte_sanciones'
                            )
                            ->select([
                                'id_sancion',

                                'id_reporte',

                                'tipo',

                                'descripcion_otro',

                                'origen',

                                'id_seguimiento',
                            ])
                            ->whereIn(
                                'id_reporte',
                                $idsReportes
                            )
                            ->whereIn(
                                'id_seguimiento',
                                $idsSeguimientos
                            )
                            ->where(
                                'origen',
                                'seguimiento'
                            )
                            ->where(
                                'eliminado',
                                0
                            )
                            ->orderBy(
                                'id_sancion',
                                'ASC'
                            )
                            ->get()
                            ->getResultArray();
                }
            }
        }


        /* =====================================================
           AGRUPAR PERSONAL
        ===================================================== */

        $personalPorReporte =
            [];


        foreach (
            $personalDb
            as $persona
        ) {

            $idReporte =
                (int) (
                    $persona['id_reporte']
                    ?? 0
                );


            if (
                $idReporte <= 0
            ) {
                continue;
            }


            $personalPorReporte[
                $idReporte
            ][] =
                $persona;
        }


        /* =====================================================
           AGRUPAR UNIDADES
        ===================================================== */

        $unidadesPorReporte =
            [];


        foreach (
            $unidadesDb
            as $unidad
        ) {

            $idReporte =
                (int) (
                    $unidad['id_reporte']
                    ?? 0
                );


            if (
                $idReporte <= 0
            ) {
                continue;
            }


            $unidadesPorReporte[
                $idReporte
            ][] =
                $unidad;
        }


        /* =====================================================
           AGRUPAR DIRECCIÓN DE NOTIFICACIÓN
        ===================================================== */

        $direccionPorReporte =
            [];


        foreach (
            $direccionesDb
            as $direccion
        ) {

            $idReporte =
                (int) (
                    $direccion['id_reporte']
                    ?? 0
                );


            if (
                $idReporte <= 0
            ) {
                continue;
            }


            $direccionPorReporte[
                $idReporte
            ] =
                $direccion;
        }


        /* =====================================================
           AGRUPAR MOTIVOS
        ===================================================== */

        $motivosPorReporte =
            [];


        foreach (
            $motivosDb
            as $motivo
        ) {

            $idReporte =
                (int) (
                    $motivo['id_reporte']
                    ?? 0
                );


            if (
                $idReporte <= 0
            ) {
                continue;
            }


            $motivosPorReporte[
                $idReporte
            ][] =
                $motivo;
        }


        /* =====================================================
           SANCIONES POR SEGUIMIENTO
        ===================================================== */

        $sancionPorSeguimiento =
            [];


        foreach (
            $sancionesSeguimientoDb
            as $sancion
        ) {

            $idSeguimiento =
                (int) (
                    $sancion['id_seguimiento']
                    ?? 0
                );


            if (
                $idSeguimiento <= 0
            ) {
                continue;
            }


            $sancionPorSeguimiento[
                $idSeguimiento
            ] =
                $sancion;
        }


        /* =====================================================
           AGRUPAR SEGUIMIENTOS
        ===================================================== */

        $seguimientosPorReporte =
            [];


        foreach (
            $seguimientosDb
            as $seguimiento
        ) {

            $idReporte =
                (int) (
                    $seguimiento['id_reporte']
                    ?? 0
                );


            $idSeguimiento =
                (int) (
                    $seguimiento['id_seguimiento']
                    ?? 0
                );


            if (
                $idReporte <= 0
                || $idSeguimiento <= 0
            ) {
                continue;
            }


            $sancion =
                $sancionPorSeguimiento[
                    $idSeguimiento
                ]
                ?? [];


            $seguimiento[
                'sancion_disciplinaria'
            ] =
                $sancion['tipo']
                ?? '';


            $seguimiento[
                'sancion_otro'
            ] =
                $sancion['descripcion_otro']
                ?? '';


            $seguimientosPorReporte[
                $idReporte
            ][] =
                $seguimiento;
        }


        /* =====================================================
           PREPARAR REPORTES
        ===================================================== */

        $reportes =
            [];


        foreach (
            $reportesDb
            as $reporte
        ) {

            $idReporte =
                (int) (
                    $reporte['id_reporte']
                    ?? 0
                );


            /* =================================================
               DIRECCIÓN DE NOTIFICACIÓN
            ================================================= */

            $direccion =
                $direccionPorReporte[
                    $idReporte
                ]
                ?? [];


            /* =================================================
               COORDENADAS DE LOS HECHOS
            ================================================= */

            $latitud =
                $this->texto(
                    $reporte['latitud']
                    ?? ''
                );


            $longitud =
                $this->texto(
                    $reporte['longitud']
                    ?? ''
                );


            $coordenadas =
                $this->construirCoordenadas(
                    $latitud,
                    $longitud
                );


            /* =================================================
               COORDENADAS NOTIFICACIÓN
            ================================================= */

            $notificacionLatitud =
                $this->texto(
                    $direccion['latitud']
                    ?? ''
                );


            $notificacionLongitud =
                $this->texto(
                    $direccion['longitud']
                    ?? ''
                );


            $notificacionCoordenadas =
                $this->construirCoordenadas(
                    $notificacionLatitud,
                    $notificacionLongitud
                );


            /* =================================================
               REPORTE
            ================================================= */

            $reportes[] = [

                /* =============================================
                   IDENTIFICADOR
                ============================================== */

                'id_reporte' =>
                    $idReporte,


                /* =============================================
                   DATOS DEL REPORTE
                ============================================== */

                'tipo_folio' =>
                    $this->obtenerTipoFolioDesdeFolio(
                        $reporte['folio']
                        ?? ''
                    ),

                'numero_folio' =>
                    $reporte['numero_folio']
                    ?? '',

                'folio' =>
                    $reporte['folio']
                    ?? '',

                'fecha_registro' =>
                    $reporte['fecha_registro']
                    ?? '',


                /* =============================================
                   IDENTIFICACIÓN
                ============================================== */

                'folio_ip' =>
                    $reporte['folio_ip']
                    ?? '',

                'folio_imp' =>
                    $reporte['folio_imp']
                    ?? '',

                'fecha_queja' =>
                    $reporte['fecha_queja']
                    ?? '',

                'fecha_acuerdo' =>
                    $reporte['fecha_acuerdo']
                    ?? '',

                'expediente' =>
                    $reporte['expediente']
                    ?? '',

                'nomenclatura' =>
                    $reporte['nomenclatura']
                    ?? '',

                'no_oficio' =>
                    $reporte['numero_oficio']
                    ?? '',


                /* =============================================
                   HECHOS
                ============================================== */

                'fecha_hechos' =>
                    $reporte['fecha_hechos']
                    ?? '',

                'hora_hechos' =>
                    $reporte['hora_hechos']
                    ?? '',

                'descripcion' =>
                    $reporte['descripcion_hechos']
                    ?? '',


                /* =============================================
                   UBICACIÓN
                ============================================== */

                'calle' =>
                    $reporte['calle']
                    ?? '',

                'numero' =>
                    $reporte['numero_exterior']
                    ?? '',

                'colonia' =>
                    $reporte['colonia']
                    ?? '',

                'entre_calle' =>
                    $reporte['entre_calle']
                    ?? '',

                'y_calle' =>
                    $reporte['y_calle']
                    ?? '',

                'municipio' =>
                    $reporte['municipio']
                    ?? '',

                'estado' =>
                    $reporte['estado']
                    ?? '',

                'sector' =>
                    $reporte['sector']
                    ?? '',

                'cuadrante' =>
                    $reporte['cuadrante']
                    ?? '',

                'id_cuadra' =>
                    $reporte['id_cuadra']
                    ?? '',

                'longitud' =>
                    $longitud,

                'latitud' =>
                    $latitud,

                'coordenadas' =>
                    $coordenadas,

                'origen_ubicacion' =>
                    $reporte['origen_ubicacion']
                    ?? '',


                /* =============================================
                   PERSONAL
                ============================================== */

                'personal' =>
                    $personalPorReporte[
                        $idReporte
                    ]
                    ?? [],


                /* =============================================
                   UNIDADES
                ============================================== */

                'modalidad_unidad' =>
                    $reporte['modalidad_unidad']
                    ?? '',

                'unidades' =>
                    $unidadesPorReporte[
                        $idReporte
                    ]
                    ?? [],


                /* =============================================
                   QUEJOSO
                ============================================== */

                'es_anonimo' =>
                    $reporte['es_anonimo']
                    ?? 0,

                'numero_anonimo' =>
                    $reporte['numero_anonimo']
                    ?? '',

                'quejoso' =>
                    $reporte['nombre_quejoso']
                    ?? '',

                'edad' =>
                    $reporte['edad_quejoso']
                    ?? '',

                'genero' =>
                    $reporte['genero_quejoso']
                    ?? '',

                'telefono' =>
                    $reporte['telefono_quejoso']
                    ?? '',

                'correo' =>
                    $reporte['correo_quejoso']
                    ?? '',

                'direccion_quejoso' =>
                    $reporte['direccion_quejoso']
                    ?? '',

                'canalizacion' =>
                    $reporte['canalizacion_area']
                    ?? '',

                'canalizacion_otro' =>
                    $reporte['canalizacion_otro']
                    ?? '',


                /* =============================================
                   DIRECCIÓN PARA NOTIFICACIÓN
                ============================================== */

                'notificacion_pertenece_neza' =>
                    $direccion['pertenece_neza']
                    ?? '',

                'notificacion_calle' =>
                    $direccion['calle']
                    ?? '',

                'notificacion_numero_exterior' =>
                    $direccion['numero_exterior']
                    ?? '',

                'notificacion_colonia' =>
                    $direccion['colonia']
                    ?? '',

                'notificacion_entre_calle' =>
                    $direccion['entre_calle']
                    ?? '',

                'notificacion_y_calle' =>
                    $direccion['y_calle']
                    ?? '',

                'notificacion_municipio' =>
                    $direccion['municipio']
                    ?? '',

                'notificacion_estado' =>
                    $direccion['estado']
                    ?? '',

                'notificacion_sector' =>
                    $direccion['sector']
                    ?? '',

                'notificacion_cuadrante' =>
                    $direccion['cuadrante']
                    ?? '',

                'notificacion_id_cuadra' =>
                    $direccion['id_cuadra']
                    ?? '',

                'notificacion_longitud' =>
                    $notificacionLongitud,

                'notificacion_latitud' =>
                    $notificacionLatitud,

                'notificacion_coordenadas' =>
                    $notificacionCoordenadas,

                'notificacion_origen_ubicacion' =>
                    $direccion['origen_ubicacion']
                    ?? '',


                /* =============================================
                   CLASIFICACIÓN Y RESOLUCIÓN
                ============================================== */

                'clasificacion' =>
                    $reporte['clasificacion']
                    ?? '',

                'inspector' =>
                    $reporte['inspector']
                    ?? '',

                'investigador' =>
                    $reporte['investigador']
                    ?? '',

                'estado_actual' =>
                    $reporte['estado_actual']
                    ?? '',

                'sin_sanciones' =>
                    $reporte['sin_sanciones']
                    ?? 0,

                'baja_voluntaria' =>
                    $reporte['baja_voluntaria']
                    ?? 0,

                'desistimiento' =>
                    $reporte['desistir']
                    ?? 0,

                'quien_emite_resolucion' =>
                    $reporte['quien_emite_resolucion']
                    ?? '',

                'resolucion' =>
                    $reporte['resolucion']
                    ?? '',

                'motivos' =>
                    $motivosPorReporte[
                        $idReporte
                    ]
                    ?? [],


                /* =============================================
                   OBSERVACIONES
                ============================================== */

                'observaciones' =>
                    $reporte['observaciones']
                    ?? '',


                /* =============================================
                   SEGUIMIENTOS
                ============================================== */

                'seguimientos' =>
                    $seguimientosPorReporte[
                        $idReporte
                    ]
                    ?? [],
            ];
        }


        /* =====================================================
           RESULTADO
        ===================================================== */

        return [

            'secciones' =>
                $secciones,

            'tipos' =>
                $tipos,

            'cantidad' =>
                $cantidad,

            'reportes' =>
                $reportes,
        ];
    }


    /* =========================================================
       SECCIONES
    ========================================================= */

    private function obtenerSecciones(
        IncomingRequest $request
    ): array {

        $permitidas = [

            'datos_reporte',

            'identificacion',

            'hechos',

            'ubicacion',

            'personal',

            'unidades',

            'quejoso',

            'direccion_notificacion',

            'clasificacion',

            'observaciones',

            'seguimientos',

        ];


        $solicitadas =
            $request
                ->getPost(
                    'secciones'
                );


        if (
            !is_array(
                $solicitadas
            )
        ) {

            $solicitadas =
                [];
        }


        $resultado =
            [];


        foreach (
            $solicitadas
            as $seccion
        ) {

            $seccion =
                trim(
                    (string) $seccion
                );


            if (
                $seccion === ''
                || !in_array(
                    $seccion,
                    $permitidas,
                    true
                )
                || in_array(
                    $seccion,
                    $resultado,
                    true
                )
            ) {
                continue;
            }


            $resultado[] =
                $seccion;
        }


        if (
            empty(
                $resultado
            )
        ) {

            throw new \InvalidArgumentException(
                'Selecciona al menos una sección para exportar.'
            );
        }


        return $resultado;
    }


    /* =========================================================
       TIPOS DE QUEJA
    ========================================================= */

    private function obtenerTipos(
        IncomingRequest $request
    ): array {

        $permitidos = [
            'QJ',
            'QJF',
            'QJV',
        ];


        $solicitados =
            $request
                ->getPost(
                    'tipos'
                );


        if (
            !is_array(
                $solicitados
            )
        ) {

            return [];
        }


        $resultado =
            [];


        foreach (
            $solicitados
            as $tipo
        ) {

            $tipo =
                strtoupper(
                    trim(
                        (string) $tipo
                    )
                );


            if (
                $tipo === ''
                || !in_array(
                    $tipo,
                    $permitidos,
                    true
                )
                || in_array(
                    $tipo,
                    $resultado,
                    true
                )
            ) {
                continue;
            }


            $resultado[] =
                $tipo;
        }


        return $resultado;
    }


    /* =========================================================
       CANTIDAD
    ========================================================= */

    private function obtenerCantidad(
        IncomingRequest $request
    ): ?int {

        $valor =
            trim(
                (string) (
                    $request
                        ->getPost(
                            'cantidad'
                        )
                    ?? ''
                )
            );


        if (
            $valor === ''
        ) {

            return null;
        }


        if (
            !ctype_digit(
                $valor
            )
        ) {

            throw new \InvalidArgumentException(
                'La cantidad debe ser un número entero mayor a 0.'
            );
        }


        $cantidad =
            (int) $valor;


        if (
            $cantidad <= 0
        ) {

            throw new \InvalidArgumentException(
                'La cantidad debe ser mayor a 0.'
            );
        }


        return $cantidad;
    }


    /* =========================================================
       TEXTO
    ========================================================= */

    private function texto(
        mixed $valor
    ): string {

        if (
            is_array(
                $valor
            )
            || is_object(
                $valor
            )
        ) {

            return '';
        }


        return trim(
            (string) (
                $valor
                ?? ''
            )
        );
    }


    /* =========================================================
       COORDENADAS
    ========================================================= */

    private function construirCoordenadas(
        string $latitud,
        string $longitud
    ): string {

        if (
            $latitud === ''
            || $longitud === ''
        ) {

            return '';
        }


        return $latitud
            . ', '
            . $longitud;
    }


    /* =========================================================
       TIPO DE FOLIO
    ========================================================= */

    private function obtenerTipoFolioDesdeFolio(
        mixed $folio
    ): string {

        $folio =
            strtoupper(
                trim(
                    (string) (
                        $folio
                        ?? ''
                    )
                )
            );


        foreach (
            [
                'QJF',
                'QJV',
                'QJ',
            ]
            as $tipo
        ) {

            if (
                str_starts_with(
                    $folio,
                    $tipo . '-'
                )
            ) {

                return $tipo;
            }
        }


        return '';
    }
}
