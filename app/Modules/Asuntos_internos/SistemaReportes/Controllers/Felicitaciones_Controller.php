<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;
use App\Modules\Asuntos_internos\SistemaReportes\Models\FelicitacionModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\FelicitacionPersonalModel;

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
                    'success' => false,
                    'message' => 'La sesión no es válida.',
                ]);
        }


        if ($idFelicitacion <= 0) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'La felicitación solicitada no es válida.',
                ]);
        }


        try {

            /* =================================================
            FELICITACIÓN
            ================================================= */

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


            if (!$felicitacion) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'La felicitación no existe.',
                    ]);
            }


            /* =================================================
            FECHA
            ================================================= */

            $fechaRegistro =
                trim(
                    (string) (
                        $felicitacion['fecha_registro']
                        ?? ''
                    )
                );


            if ($fechaRegistro !== '') {

                $fecha =
                    \DateTime::createFromFormat(
                        'Y-m-d',
                        $fechaRegistro
                    );


                if ($fecha !== false) {

                    $felicitacion['fecha_registro'] =
                        $fecha->format(
                            'd/m/Y'
                        );
                }
            }


            /* =================================================
            PERSONAL RELACIONADO
            ================================================= */

            $personalModel =
                new FelicitacionPersonalModel();


            $personal =
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


            /* =================================================
            RESPUESTA
            ================================================= */

            return $this->response
                ->setJSON([
                    'success' =>
                    true,

                    'felicitacion' =>
                    $felicitacion,

                    'personal' =>
                    $personal,
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
                    'success' => false,
                    'message' => 'No fue posible consultar el detalle de la felicitación.',
                ]);
        }
    }
}
