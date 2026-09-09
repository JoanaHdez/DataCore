<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;
use App\Modules\Asuntos_internos\SistemaReportes\Models\FelicitacionModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\FelicitacionPersonalModel;
use App\Modules\Asuntos_internos\SistemaReportes\Services\FelicitacionService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\AuthService;

class Felicitaciones_Controller extends BaseController
{
    /* =========================================================
    LISTADO DE FELICITACIONES
    ========================================================= */

    public function index()
    {
        /* =====================================================
        VALIDAR SESIÓN
        ===================================================== */

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return redirect()
                ->to(
                    base_url(
                        'asuntos-internos/reportes'
                    )
                )
                ->with(
                    'error',
                    'Inicia sesión para continuar.'
                );
        }


        try {

            /* =====================================================
            CONEXIÓN DATACORE
            ===================================================== */

            $db =
                \Config\Database::connect(
                    'datacore'
                );


            /* =====================================================
            CONSULTAR FELICITACIONES
            ===================================================== */

            $felicitacionModel =
                new FelicitacionModel();


            $felicitaciones =
                $felicitacionModel
                    ->where(
                        'eliminado',
                        0
                    )
                    ->orderBy(
                        'id_felicitacion',
                        'DESC'
                    )
                    ->findAll();


            /* =====================================================
            PREPARAR FELICITACIONES
            ===================================================== */

            foreach (
                $felicitaciones
                as &$felicitacion
            ) {

                $idFelicitacion =
                    (int) (
                        $felicitacion['id_felicitacion']
                        ?? 0
                    );


                /* =================================================
                PERSONAL RELACIONADO
                ================================================= */

                $personal =
                    $db
                        ->table(
                            'ai_felicitacion_personal'
                        )
                        ->select([
                            'plantilla_id',
                            'perscod',
                            'nombre_snapshot',
                            'area_snapshot',
                            'turno_snapshot',
                            'alias_snapshot',
                        ])
                        ->where(
                            'id_felicitacion',
                            $idFelicitacion
                        )
                        ->orderBy(
                            'id_felicitacion_personal',
                            'ASC'
                        )
                        ->get()
                        ->getResultArray();


                $felicitacion['personal'] =
                    $personal;


                /* =================================================
                DATOS INTERNOS PARA FILTROS
                ================================================= */

                $nombres =
                    [];


                $aliases =
                    [];


                $areasPersonal =
                    [];


                $turnosPersonal =
                    [];


                $sectoresPersonal =
                    [];


                foreach (
                    $personal
                    as $persona
                ) {

                    /* =============================================
                    NOMBRE
                    ============================================= */

                    $nombre =
                        trim(
                            (string) (
                                $persona['nombre_snapshot']
                                ?? ''
                            )
                        );


                    if (
                        $nombre !== ''
                        && !in_array(
                            $nombre,
                            $nombres,
                            true
                        )
                    ) {

                        $nombres[] =
                            $nombre;
                    }


                    /* =============================================
                    ALIAS
                    ============================================= */

                    $alias =
                        trim(
                            (string) (
                                $persona['alias_snapshot']
                                ?? ''
                            )
                        );


                    if (
                        $alias !== ''
                        && !in_array(
                            $alias,
                            $aliases,
                            true
                        )
                    ) {

                        $aliases[] =
                            $alias;
                    }


                    /* =============================================
                    ÁREA
                    ============================================= */

                    $area =
                        trim(
                            preg_replace(
                                '/\s+/u',
                                ' ',
                                (string) (
                                    $persona['area_snapshot']
                                    ?? ''
                                )
                            )
                            ?? ''
                        );


                    if (
                        $area !== ''
                        && !in_array(
                            $area,
                            $areasPersonal,
                            true
                        )
                    ) {

                        $areasPersonal[] =
                            $area;
                    }


                    /* =============================================
                    TURNO
                    ============================================= */

                    $turno =
                        trim(
                            preg_replace(
                                '/\s+/u',
                                ' ',
                                (string) (
                                    $persona['turno_snapshot']
                                    ?? ''
                                )
                            )
                            ?? ''
                        );


                    if (
                        $turno !== ''
                        && !in_array(
                            $turno,
                            $turnosPersonal,
                            true
                        )
                    ) {

                        $turnosPersonal[] =
                            $turno;
                    }


                    /* =============================================
                    SECTOR DESDE ÁREA
                    ============================================= */

                    $areaMayusculas =
                        mb_strtoupper(
                            $area,
                            'UTF-8'
                        );


                    if (
                        preg_match(
                            '/^SECTOR\s+0*([0-9]+)/u',
                            $areaMayusculas,
                            $coincidencias
                        )
                    ) {

                        $numeroSector =
                            (int) (
                                $coincidencias[1]
                                ?? 0
                            );


                        if (
                            $numeroSector > 0
                        ) {

                            $sector =
                                'SECTOR '
                                . str_pad(
                                    (string) $numeroSector,
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                );


                            if (
                                !in_array(
                                    $sector,
                                    $sectoresPersonal,
                                    true
                                )
                            ) {

                                $sectoresPersonal[] =
                                    $sector;
                            }
                        }
                    }
                }


                /* =================================================
                GUARDAR DATOS DE FILTRO EN EL REGISTRO
                ================================================= */

                $felicitacion['filtro_personal'] =
                    implode(
                        ' | ',
                        $nombres
                    );


                $felicitacion['filtro_aliases'] =
                    implode(
                        ' | ',
                        $aliases
                    );


                $felicitacion['filtro_areas'] =
                    implode(
                        ' | ',
                        $areasPersonal
                    );


                $felicitacion['filtro_turnos'] =
                    implode(
                        ' | ',
                        $turnosPersonal
                    );


                $felicitacion['filtro_sectores'] =
                    implode(
                        ' | ',
                        $sectoresPersonal
                    );


                /* =================================================
                FECHA PARA FILTRO
                ================================================= */

                $fechaRegistro =
                    trim(
                        (string) (
                            $felicitacion['fecha_registro']
                            ?? ''
                        )
                    );


                $felicitacion['fecha_filtro'] =
                    $fechaRegistro;


                /* =================================================
                FECHA PARA MOSTRAR
                ================================================= */

                if (
                    $fechaRegistro !== ''
                ) {

                    $fecha =
                        \DateTime::createFromFormat(
                            'Y-m-d',
                            $fechaRegistro
                        );


                    if (
                        $fecha !== false
                    ) {

                        $felicitacion['fecha_registro'] =
                            $fecha->format(
                                'd/m/Y'
                            );
                    }
                }
            }


            unset(
                $felicitacion
            );


            /* =====================================================
            CATÁLOGOS DE FILTROS

            Solo mostramos valores que realmente ya existen
            en felicitaciones registradas.
            ===================================================== */


            /* =====================================================
            ÁREAS REGISTRADAS
            ===================================================== */

            $registrosAreas =
                $db
                    ->table(
                        'ai_felicitacion_personal'
                    )
                    ->select(
                        'area_snapshot'
                    )
                    ->where(
                        'area_snapshot IS NOT NULL',
                        null,
                        false
                    )
                    ->where(
                        "TRIM(area_snapshot) != ''",
                        null,
                        false
                    )
                    ->groupBy(
                        'area_snapshot'
                    )
                    ->get()
                    ->getResultArray();


            $areasEncontradas =
                [];


            foreach (
                $registrosAreas
                as $registro
            ) {

                $area =
                    trim(
                        preg_replace(
                            '/\s+/u',
                            ' ',
                            (string) (
                                $registro['area_snapshot']
                                ?? ''
                            )
                        )
                        ?? ''
                    );


                if (
                    $area === ''
                ) {
                    continue;
                }


                $areasEncontradas[$area] =
                    $area;
            }


            natcasesort(
                $areasEncontradas
            );


            $areas =
                array_values(
                    $areasEncontradas
                );


            /* =====================================================
            TURNOS REGISTRADOS
            ===================================================== */

            $registrosTurnos =
                $db
                    ->table(
                        'ai_felicitacion_personal'
                    )
                    ->select(
                        'turno_snapshot'
                    )
                    ->where(
                        'turno_snapshot IS NOT NULL',
                        null,
                        false
                    )
                    ->where(
                        "TRIM(turno_snapshot) != ''",
                        null,
                        false
                    )
                    ->groupBy(
                        'turno_snapshot'
                    )
                    ->get()
                    ->getResultArray();


            $turnosEncontrados =
                [];


            foreach (
                $registrosTurnos
                as $registro
            ) {

                $turno =
                    trim(
                        preg_replace(
                            '/\s+/u',
                            ' ',
                            (string) (
                                $registro['turno_snapshot']
                                ?? ''
                            )
                        )
                        ?? ''
                    );


                if (
                    $turno === ''
                ) {
                    continue;
                }


                $turnosEncontrados[$turno] =
                    $turno;
            }


            natcasesort(
                $turnosEncontrados
            );


            $turnos =
                array_values(
                    $turnosEncontrados
                );


            /* =====================================================
            SECTORES REGISTRADOS

            Se obtienen de las áreas que ya existen
            en las felicitaciones.
            ===================================================== */

            $sectoresEncontrados =
                [];


            foreach (
                $areas
                as $area
            ) {

                $areaMayusculas =
                    mb_strtoupper(
                        $area,
                        'UTF-8'
                    );


                if (
                    !preg_match(
                        '/^SECTOR\s+0*([0-9]+)/u',
                        $areaMayusculas,
                        $coincidencias
                    )
                ) {
                    continue;
                }


                $numeroSector =
                    (int) (
                        $coincidencias[1]
                        ?? 0
                    );


                if (
                    $numeroSector <= 0
                ) {
                    continue;
                }


                $sector =
                    'SECTOR '
                    . str_pad(
                        (string) $numeroSector,
                        2,
                        '0',
                        STR_PAD_LEFT
                    );


                $sectoresEncontrados[$numeroSector] =
                    $sector;
            }


            ksort(
                $sectoresEncontrados,
                SORT_NUMERIC
            );


            $sectores =
                array_values(
                    $sectoresEncontrados
                );


        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error consultando listado de felicitaciones: {mensaje}',
                [
                    'mensaje' =>
                        $e->getMessage(),
                ]
            );


            $felicitaciones =
                [];


            $sectores =
                [];


            $areas =
                [];


            $turnos =
                [];
        }


        /* =====================================================
        VISTA
        ===================================================== */

        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\index',
            [
                'felicitaciones' =>
                    $felicitaciones,

                'sectores' =>
                    $sectores,

                'areas' =>
                    $areas,

                'turnos' =>
                    $turnos,
            ]
        );
    }

    /* =========================================================
    DETALLE DE FELICITACIÓN
    ========================================================= */

    public function detalle(int $idFelicitacion)
    {
        /* =========================================================
        VALIDAR SESIÓN
        ========================================================= */

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'La sesión no es válida.',
                ]);
        }


        /* =========================================================
        VALIDAR ID
        ========================================================= */

        if (
            $idFelicitacion <= 0
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'La felicitación solicitada no es válida.',
                ]);
        }


        try {

            /* =====================================================
            CONEXIONES
            ===================================================== */

            $db =
                \Config\Database::connect(
                    'datacore'
                );


            $dbPlantilla =
                \Config\Database::connect(
                    'plantilla'
                );


            /* =====================================================
            FELICITACIÓN
            ===================================================== */

            $felicitacionModel =
                new FelicitacionModel();


            $felicitacion =
                $felicitacionModel
                    ->where(
                        'id_felicitacion',
                        $idFelicitacion
                    )
                    ->where(
                        'eliminado',
                        0
                    )
                    ->first();


            if (
                !$felicitacion
            ) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' =>
                            false,

                        'message' =>
                            'La felicitación no existe.',
                    ]);
            }


            /* =====================================================
            FECHA
            ===================================================== */

            $fechaRegistro =
                trim(
                    (string) (
                        $felicitacion['fecha_registro']
                        ?? ''
                    )
                );


            if (
                $fechaRegistro !== ''
            ) {

                $fecha =
                    \DateTime::createFromFormat(
                        'Y-m-d',
                        $fechaRegistro
                    );


                if (
                    $fecha !== false
                ) {

                    $felicitacion['fecha_registro'] =
                        $fecha->format(
                            'd/m/Y'
                        );
                }
            }


            /* =====================================================
            PERSONAL RELACIONADO
            ===================================================== */

            $personalModel =
                new FelicitacionPersonalModel();


            $personalDb =
                $personalModel
                    ->where(
                        'id_felicitacion',
                        $idFelicitacion
                    )
                    ->orderBy(
                        'id_felicitacion_personal',
                        'ASC'
                    )
                    ->findAll();


            /* =====================================================
            IDS DE PLANTILLA
            ===================================================== */

            $idsPlantilla =
                [];


            foreach (
                $personalDb
                as $persona
            ) {

                $plantillaId =
                    (int) (
                        $persona['plantilla_id']
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
            CONSULTAR DATOS ACTUALES DE PLANTILLA

            Se hace una sola consulta para todo el personal.
            ===================================================== */

            $personalPlantilla =
                [];


            if (
                !empty(
                    $idsPlantilla
                )
            ) {

                $registrosPlantilla =
                    $dbPlantilla
                        ->table(
                            'plantilla'
                        )
                        ->select([
                            'ID',
                            'PERSCOD',
                            'NO_NOMINA',
                        ])
                        ->whereIn(
                            'ID',
                            $idsPlantilla
                        )
                        ->get()
                        ->getResultArray();


                foreach (
                    $registrosPlantilla
                    as $registroPlantilla
                ) {

                    $id =
                        (int) (
                            $registroPlantilla['ID']
                            ?? 0
                        );


                    if (
                        $id <= 0
                    ) {
                        continue;
                    }


                    $personalPlantilla[$id] =
                        $registroPlantilla;
                }
            }


            /* =====================================================
            PREPARAR PERSONAL
            ===================================================== */

            $personal =
                [];


            foreach (
                $personalDb
                as $persona
            ) {

                $plantillaId =
                    (int) (
                        $persona['plantilla_id']
                        ?? 0
                    );


                $datosPlantilla =
                    $personalPlantilla[$plantillaId]
                    ?? [];


                /* =================================================
                PERSCOD
                ================================================= */

                $perscod =
                    trim(
                        (string) (
                            $datosPlantilla['PERSCOD']
                            ?? $persona['perscod']
                            ?? ''
                        )
                    );


                /* =================================================
                NÓMINA
                ================================================= */

                $nomina =
                    trim(
                        (string) (
                            $datosPlantilla['NO_NOMINA']
                            ?? ''
                        )
                    );


                /* =================================================
                FOTO

                No se convierte a Base64.
                El navegador la carga después.
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
                RESULTADO
                ================================================= */

                $personal[] = [

                    'id_felicitacion_personal' =>
                        (int) (
                            $persona['id_felicitacion_personal']
                            ?? 0
                        ),

                    'plantilla_id' =>
                        $plantillaId,

                    'perscod' =>
                        $perscod,

                    'nomina' =>
                        $nomina,

                    'nombre_snapshot' =>
                        $persona['nombre_snapshot']
                        ?? '',

                    'nombre' =>
                        $persona['nombre_snapshot']
                        ?? '',

                    'area_snapshot' =>
                        $persona['area_snapshot']
                        ?? '',

                    'area' =>
                        $persona['area_snapshot']
                        ?? '',

                    'turno_snapshot' =>
                        $persona['turno_snapshot']
                        ?? '',

                    'turno' =>
                        $persona['turno_snapshot']
                        ?? '',

                    'alias_snapshot' =>
                        $persona['alias_snapshot']
                        ?? '',

                    'alias' =>
                        $persona['alias_snapshot']
                        ?? '',

                    'foto' =>
                        $foto,
                ];
            }


            /* =====================================================
            UNIDADES RELACIONADAS
            ===================================================== */

            $unidadesDb =
                $db
                    ->table(
                        'ai_felicitacion_unidades'
                    )
                    ->select([
                        'id_felicitacion_unidad',
                        'parque_vehicular_id',
                        'no_economico_snapshot',
                        'placas_snapshot',
                        'marca_snapshot',
                        'submarca_snapshot',
                        'color_snapshot',
                        'estatus_snapshot',
                        'servicio_snapshot',
                        'tipo_snapshot',
                        'id_origen',
                    ])
                    ->where(
                        'id_felicitacion',
                        $idFelicitacion
                    )
                    ->orderBy(
                        'id_felicitacion_unidad',
                        'ASC'
                    )
                    ->get()
                    ->getResultArray();


            /* =====================================================
            NORMALIZAR UNIDADES
            ===================================================== */

            $unidades =
                [];


            foreach (
                $unidadesDb
                as $unidad
            ) {

                $unidades[] = [

                    'id_felicitacion_unidad' =>
                        (int) (
                            $unidad['id_felicitacion_unidad']
                            ?? 0
                        ),

                    'id' =>
                        (int) (
                            $unidad['parque_vehicular_id']
                            ?? 0
                        ),

                    'parque_vehicular_id' =>
                        (int) (
                            $unidad['parque_vehicular_id']
                            ?? 0
                        ),

                    'no_economico' =>
                        $unidad['no_economico_snapshot']
                        ?? '',

                    'placas' =>
                        $unidad['placas_snapshot']
                        ?? '',

                    'marca' =>
                        $unidad['marca_snapshot']
                        ?? '',

                    'submarca' =>
                        $unidad['submarca_snapshot']
                        ?? '',

                    'color' =>
                        $unidad['color_snapshot']
                        ?? '',

                    'estatus' =>
                        $unidad['estatus_snapshot']
                        ?? '',

                    'servicio' =>
                        $unidad['servicio_snapshot']
                        ?? '',

                    'tipo' =>
                        $unidad['tipo_snapshot']
                        ?? '',

                    'id_origen' =>
                        isset(
                            $unidad['id_origen']
                        )
                            ? (int) $unidad['id_origen']
                            : null,
                ];
            }


            /* =====================================================
            MODALIDAD

            Si hay unidades:
                CON_UNIDAD

            Si no hay unidades:
                SIN_UNIDAD_OFICINA
            ===================================================== */

            $felicitacion['modalidad_unidad'] =
                !empty(
                    $unidades
                )
                    ? 'CON_UNIDAD'
                    : 'SIN_UNIDAD_OFICINA';


            /* =====================================================
            RESPUESTA
            ===================================================== */

            return $this->response
                ->setJSON([
                    'success' =>
                        true,

                    'felicitacion' =>
                        $felicitacion,

                    'personal' =>
                        $personal,

                    'unidades' =>
                        $unidades,
                ]);


        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error consultando detalle de felicitación {id}: {mensaje}',
                [
                    'id' =>
                        $idFelicitacion,

                    'mensaje' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'No fue posible consultar el detalle de la felicitación.',
                ]);
        }
    }

    /* =========================================================
    ACTUALIZAR FELICITACIÓN
    ========================================================= */

    public function actualizar(
        int $idFelicitacion
    ) {

        /* =====================================================
        VALIDAR SESIÓN
        ===================================================== */

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'La sesión no es válida.',
                ]);
        }


        /* =====================================================
        VALIDAR ID
        ===================================================== */

        if (
            $idFelicitacion <= 0
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'La felicitación que deseas actualizar no es válida.',
                ]);
        }


        /* =====================================================
        USUARIO
        ===================================================== */

        $usuario =
            session()->get(
                'usuario_reportes'
            );


        $idUsuario =
            (int) (
                $usuario['id_usuario']
                ?? 0
            );


        if (
            $idUsuario <= 0
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'No fue posible identificar al usuario.',
                ]);
        }


        /* =====================================================
        DATOS
        ===================================================== */

        $datos =
            $this->request
                ->getPost();


        /* =====================================================
        PERSONAL
        ===================================================== */

        $personal =
            $this->request
                ->getPost(
                    'personal'
                );


        if (
            !is_array(
                $personal
            )
        ) {

            $personal =
                [];
        }


        /* =====================================================
        UNIDADES
        ===================================================== */

        $unidades =
            $this->request
                ->getPost(
                    'unidades'
                );


        if (
            !is_array(
                $unidades
            )
        ) {

            $unidades =
                [];
        }


        /* =====================================================
        ACTUALIZAR
        ===================================================== */

        try {

            $servicio =
                new FelicitacionService();


            $resultado =
                $servicio->actualizar(
                    $idFelicitacion,
                    $datos,
                    $personal,
                    $unidades,
                    $idUsuario
                );


            return $this->response
                ->setJSON([
                    'success' =>
                        true,

                    'message' =>
                        'La felicitación fue actualizada correctamente.',

                    'id_felicitacion' =>
                        $resultado['id_felicitacion']
                        ?? $idFelicitacion,

                    'folio' =>
                        $resultado['folio']
                        ?? null,
                ]);


        } catch (\InvalidArgumentException $e) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        $e->getMessage(),
                ]);


        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error actualizando felicitación {id}: {mensaje}',
                [
                    'id' =>
                        $idFelicitacion,

                    'mensaje' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'No fue posible actualizar la felicitación.',
                ]);
        }
    }

    public function eliminar(int $idFelicitacion)
    {
        /* =========================================================
        VALIDAR SESIÓN
        ========================================================= */

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'La sesión no es válida.',
                ]);
        }


        $usuario =
            session()->get(
                'usuario_reportes'
            );


        $idUsuario =
            (int) (
                $usuario['id_usuario']
                ?? 0
            );


        $rol =
            trim(
                (string) (
                    $usuario['rol']
                    ?? 'usuario'
                )
            );


        if (
            $idUsuario <= 0
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'No fue posible identificar al usuario.',
                ]);
        }


        if (
            $idFelicitacion <= 0
        ) {

            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'La felicitación no es válida.',
                ]);
        }


        /* =========================================================
        CONEXIÓN DATACORE
        ========================================================= */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =========================================================
        BUSCAR FELICITACIÓN
        ========================================================= */

        $felicitacion =
            $db
                ->table(
                    'ai_felicitaciones'
                )
                ->where(
                    'id_felicitacion',
                    $idFelicitacion
                )
                ->get()
                ->getRowArray();


        if (
            !$felicitacion
        ) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'La felicitación no existe.',
                ]);
        }


        /* =========================================================
        VALIDAR SI YA FUE ELIMINADA
        ========================================================= */

        if (
            (int) (
                $felicitacion['eliminado']
                ?? 0
            ) === 1
        ) {

            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' => 'La felicitación ya fue eliminada.',
                ]);
        }


        /* =========================================================
        AUTORIZACIÓN
        ========================================================= */

        $idAdministradorAutorizador =
            null;


        /*
        * =====================================================
        * ADMINISTRADOR
        *
        * Si la sesión ya pertenece a un administrador,
        * no solicitamos contraseña adicional.
        * =====================================================
        */

        if (
            $rol === 'admin'
        ) {

            $idAdministradorAutorizador =
                $idUsuario;

        } else {

            /*
            * =================================================
            * USUARIO NORMAL
            *
            * El backend vuelve a validar la contraseña.
            * No confiamos únicamente en JavaScript.
            * =================================================
            */

            $passwordAdmin =
                strtoupper(
                    trim(
                        (string) $this->request
                            ->getPost(
                                'password_admin'
                            )
                    )
                );


            if (
                $passwordAdmin === ''
            ) {

                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Se requiere autorización administrativa.',
                    ]);
            }


            try {

                $authService =
                    new AuthService();


                $administrador =
                    $authService
                        ->validarAutorizacionAdministradores(
                            $passwordAdmin
                        );

            } catch (\Throwable $e) {

                log_message(
                    'error',
                    'Error validando autorización administrativa para eliminar felicitación: {mensaje}',
                    [
                        'mensaje' =>
                            $e->getMessage(),
                    ]
                );


                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'No fue posible validar la autorización.',
                    ]);
            }


            if (
                !$administrador
            ) {

                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Contraseña de administrador incorrecta.',
                    ]);
            }


            $idAdministradorAutorizador =
                (int) (
                    $administrador['id_usuario']
                    ?? 0
                );


            if (
                $idAdministradorAutorizador <= 0
            ) {

                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'No fue posible identificar al administrador autorizador.',
                    ]);
            }
        }


        /* =========================================================
        TRANSACCIÓN
        ========================================================= */

        $db->transBegin();


        try {

            $ahora =
                date(
                    'Y-m-d H:i:s'
                );


            /* =====================================================
            BORRADO LÓGICO
            ===================================================== */

            $db
                ->table(
                    'ai_felicitaciones'
                )
                ->where(
                    'id_felicitacion',
                    $idFelicitacion
                )
                ->update([
                    'eliminado' =>
                        1,

                    'eliminado_at' =>
                        $ahora,

                    'eliminado_por' =>
                        $idUsuario,

                    'updated_at' =>
                        $ahora,
                ]);


            /* =====================================================
            REGISTRO DE ELIMINACIÓN
            ===================================================== */

            $db
                ->table(
                    'ai_felicitacion_eliminaciones'
                )
                ->insert([

                    'id_felicitacion' =>
                        $idFelicitacion,

                    'solicitado_por' =>
                        $idUsuario,

                    'autorizado_por' =>
                        $idAdministradorAutorizador,

                    'requirio_autorizacion' =>
                        $rol === 'admin'
                            ? 0
                            : 1,

                    'motivo' =>
                        'Eliminación solicitada desde el listado de felicitaciones.',

                    'ip' =>
                        $this->request
                            ->getIPAddress(),

                    'created_at' =>
                        $ahora,
                ]);


            /* =====================================================
            VALIDAR TRANSACCIÓN
            ===================================================== */

            if (
                $db->transStatus() === false
            ) {

                throw new \RuntimeException(
                    'La transacción de eliminación no pudo completarse.'
                );
            }


            $db->transCommit();

        } catch (\Throwable $e) {

            $db->transRollback();


            log_message(
                'error',
                'Error eliminando lógicamente felicitación {id}: {mensaje}',
                [
                    'id' =>
                        $idFelicitacion,

                    'mensaje' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'No fue posible eliminar la felicitación.',
                ]);
        }


        /* =========================================================
        RESPUESTA
        ========================================================= */

        return $this->response
            ->setJSON([
                'success' => true,

                'message' =>
                    'La felicitación fue eliminada correctamente.',

                'folio' =>
                    $felicitacion['folio']
                    ?? '',
            ]);
    }
}
