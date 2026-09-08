<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use App\Modules\Asuntos_internos\SistemaReportes\Models\FelicitacionModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\FelicitacionPersonalModel;

class FelicitacionService
{
    protected $db;

    protected FelicitacionModel $felicitacionModel;

    protected FelicitacionPersonalModel $personalModel;

    protected FolioService $folioService;


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct()
    {
        $this->db =
            \Config\Database::connect(
                'datacore'
            );


        $this->felicitacionModel =
            new FelicitacionModel();


        $this->personalModel =
            new FelicitacionPersonalModel();


        $this->folioService =
            new FolioService(
                $this->db
            );
    }


    /* =========================================================
       GUARDAR FELICITACIÓN
    ========================================================= */

    public function guardar(
        array $datos,
        array $personal,
        array $unidades,
        int $idUsuario
    ): array {

        /* =====================================================
           USUARIO
        ===================================================== */

        if (
            $idUsuario <= 0
        ) {

            throw new \RuntimeException(
                'No fue posible identificar al usuario que registra la felicitación.'
            );
        }


        /* =====================================================
           PERSONAL
        ===================================================== */

        if (
            empty(
                $personal
            )
        ) {

            throw new \InvalidArgumentException(
                'Debes agregar al menos una persona a la felicitación.'
            );
        }


        /* =====================================================
           NOMBRE DEL FELICITANTE
        ===================================================== */

        $nombreFelicitante =
            trim(
                (string) (
                    $datos['nombre_felicitante']
                    ?? ''
                )
            );


        if (
            $nombreFelicitante === ''
        ) {

            throw new \InvalidArgumentException(
                'El nombre de la persona que da la felicitación es obligatorio.'
            );
        }


        /* =====================================================
           RAZÓN
        ===================================================== */

        $razonFelicitacion =
            trim(
                (string) (
                    $datos['razon_felicitacion']
                    ?? ''
                )
            );


        if (
            $razonFelicitacion === ''
        ) {

            throw new \InvalidArgumentException(
                'La razón de la felicitación es obligatoria.'
            );
        }


        /* =====================================================
           FECHA
        ===================================================== */

        $fechaRegistro =
            $this->normalizarFecha(
                $datos['fecha_registro']
                ?? null
            );


        /* =====================================================
           MODALIDAD DE UNIDAD
        ===================================================== */

        $modalidadUnidad =
            strtoupper(
                trim(
                    (string) (
                        $datos['modalidad_unidad']
                        ?? 'CON_UNIDAD'
                    )
                )
            );


        $modalidadesPermitidas = [
            'CON_UNIDAD',
            'SIN_UNIDAD_OFICINA',
        ];


        if (
            !in_array(
                $modalidadUnidad,
                $modalidadesPermitidas,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'La modalidad de unidad seleccionada no es válida.'
            );
        }


        /* =====================================================
           VALIDAR UNIDADES
        ===================================================== */

        if (
            $modalidadUnidad === 'CON_UNIDAD'
            && empty(
                $unidades
            )
        ) {

            throw new \InvalidArgumentException(
                'Debes agregar al menos una unidad o seleccionar "Sin unidad / Oficina".'
            );
        }


        /*
         * Si el usuario seleccionó Sin unidad / Oficina,
         * ignoramos cualquier unidad recibida accidentalmente.
         */

        if (
            $modalidadUnidad === 'SIN_UNIDAD_OFICINA'
        ) {

            $unidades =
                [];
        }


        /* =====================================================
           INICIAR TRANSACCIÓN
        ===================================================== */

        $this->db->transBegin();


        try {

            /* =================================================
               GENERAR FOLIO
            ================================================= */

            $folioGenerado =
                $this->folioService
                    ->generar(
                        'FELICITACION'
                    );


            /* =================================================
               GUARDAR FELICITACIÓN
            ================================================= */

            $idFelicitacion =
                $this->felicitacionModel
                    ->insert(
                        [
                            'numero_folio' =>
                                $folioGenerado['numero_folio'],

                            'folio' =>
                                $folioGenerado['folio'],

                            'fecha_registro' =>
                                $fechaRegistro,

                            'nombre_felicitante' =>
                                $nombreFelicitante,

                            'razon_felicitacion' =>
                                $razonFelicitacion,

                            'created_by' =>
                                $idUsuario,

                            'eliminado' =>
                                0,
                        ],
                        true
                    );


            if (
                !$idFelicitacion
            ) {

                throw new \RuntimeException(
                    'No fue posible guardar la felicitación.'
                );
            }


            $idFelicitacion =
                (int) $idFelicitacion;


            /* =================================================
               GUARDAR PERSONAL
            ================================================= */

            $this->guardarPersonal(
                $idFelicitacion,
                $personal
            );


            /* =================================================
               GUARDAR UNIDADES
            ================================================= */

            if (
                $modalidadUnidad === 'CON_UNIDAD'
            ) {

                $this->guardarUnidades(
                    $idFelicitacion,
                    $unidades
                );
            }


            /* =================================================
               VALIDAR TRANSACCIÓN
            ================================================= */

            if (
                $this->db->transStatus()
                === false
            ) {

                throw new \RuntimeException(
                    'Ocurrió un error al guardar la felicitación.'
                );
            }


            /* =================================================
               CONFIRMAR
            ================================================= */

            $this->db->transCommit();


            return [
                'success' =>
                    true,

                'id_felicitacion' =>
                    $idFelicitacion,

                'numero_folio' =>
                    $folioGenerado['numero_folio'],

                'folio' =>
                    $folioGenerado['folio'],

                'nomenclatura' =>
                    $folioGenerado['nomenclatura'],
            ];


        } catch (\Throwable $e) {

            $this->db->transRollback();

            throw $e;
        }
    }


    /* =========================================================
       GUARDAR PERSONAL
    ========================================================= */

    protected function guardarPersonal(
        int $idFelicitacion,
        array $personal
    ): void {

        foreach (
            $personal
            as $persona
        ) {

            if (
                !is_array(
                    $persona
                )
            ) {
                continue;
            }


            $plantillaId =
                (int) (
                    $persona['plantilla_id']
                    ?? 0
                );


            $nombre =
                trim(
                    (string) (
                        $persona['nombre']
                        ?? ''
                    )
                );


            if (
                $plantillaId <= 0
                || $nombre === ''
            ) {
                continue;
            }


            $insertado =
                $this->personalModel
                    ->insert([
                        'id_felicitacion' =>
                            $idFelicitacion,

                        'plantilla_id' =>
                            $plantillaId,

                        'perscod' =>
                            $this->nullable(
                                $persona['perscod']
                                ?? null
                            ),

                        'nombre_snapshot' =>
                            $nombre,

                        'area_snapshot' =>
                            $this->nullable(
                                $persona['area']
                                ?? null
                            ),

                        'turno_snapshot' =>
                            $this->nullable(
                                $persona['turno']
                                ?? null
                            ),

                        'alias_snapshot' =>
                            $this->nullable(
                                $persona['alias']
                                ?? null
                            ),

                        'created_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),
                    ]);


            if (
                $insertado === false
            ) {

                throw new \RuntimeException(
                    'No fue posible guardar el personal relacionado con la felicitación.'
                );
            }
        }
    }


    /* =========================================================
       GUARDAR UNIDADES
    ========================================================= */

    protected function guardarUnidades(
        int $idFelicitacion,
        array $unidades
    ): void {

        if (
            empty(
                $unidades
            )
        ) {

            throw new \InvalidArgumentException(
                'Debes agregar al menos una unidad a la felicitación.'
            );
        }


        $idsRegistrados =
            [];


        foreach (
            $unidades
            as $unidad
        ) {

            if (
                !is_array(
                    $unidad
                )
            ) {
                continue;
            }


            /* =================================================
               ID DE PARQUE VEHICULAR
            ================================================= */

            $parqueId =
                (int) (
                    $unidad['parque_vehicular_id']
                    ?? $unidad['id']
                    ?? 0
                );


            if (
                $parqueId <= 0
            ) {

                throw new \InvalidArgumentException(
                    'Existe una unidad relacionada sin identificador válido.'
                );
            }


            /* =================================================
               EVITAR DUPLICADOS
            ================================================= */

            if (
                in_array(
                    $parqueId,
                    $idsRegistrados,
                    true
                )
            ) {
                continue;
            }


            /* =================================================
               ORIGEN
            ================================================= */

            $idOrigen =
                $this->resolverOrigenUnidad(
                    $unidad['origen']
                    ?? null
                );


            /* =================================================
               INSERTAR
            ================================================= */

            $insertado =
                $this->db
                    ->table(
                        'ai_felicitacion_unidades'
                    )
                    ->insert([
                        'id_felicitacion' =>
                            $idFelicitacion,

                        'parque_vehicular_id' =>
                            $parqueId,

                        'no_economico_snapshot' =>
                            $this->nullable(
                                $unidad['no_economico']
                                ?? null
                            ),

                        'placas_snapshot' =>
                            $this->nullable(
                                $unidad['placas']
                                ?? null
                            ),

                        'marca_snapshot' =>
                            $this->nullable(
                                $unidad['marca']
                                ?? null
                            ),

                        'submarca_snapshot' =>
                            $this->nullable(
                                $unidad['submarca']
                                ?? null
                            ),

                        'color_snapshot' =>
                            $this->nullable(
                                $unidad['color']
                                ?? null
                            ),

                        'estatus_snapshot' =>
                            $this->nullable(
                                $unidad['estatus']
                                ?? null
                            ),

                        'servicio_snapshot' =>
                            $this->nullable(
                                $unidad['servicio']
                                ?? null
                            ),

                        'tipo_snapshot' =>
                            $this->nullable(
                                $unidad['tipo']
                                ?? null
                            ),

                        'id_origen' =>
                            $idOrigen,

                        'created_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),
                    ]);


            if (
                $insertado === false
            ) {

                throw new \RuntimeException(
                    'No fue posible guardar una unidad relacionada con la felicitación.'
                );
            }


            $idsRegistrados[] =
                $parqueId;
        }


        /*
         * Si todas las entradas recibidas fueran inválidas
         * o no insertables, no permitimos guardar una
         * felicitación CON_UNIDAD sin una unidad real.
         */

        if (
            empty(
                $idsRegistrados
            )
        ) {

            throw new \InvalidArgumentException(
                'Debes agregar al menos una unidad válida a la felicitación.'
            );
        }
    }


    /* =========================================================
       RESOLVER ORIGEN DE UNIDAD
    ========================================================= */

    protected function resolverOrigenUnidad(
        mixed $origen
    ): ?int {

        $clave =
            strtoupper(
                trim(
                    (string) (
                        $origen
                        ?? ''
                    )
                )
            );


        if (
            $clave === ''
        ) {
            return null;
        }


        $registro =
            $this->db
                ->table(
                    'ai_cat_origen_unidad'
                )
                ->select(
                    'id_origen'
                )
                ->where(
                    'clave',
                    $clave
                )
                ->get()
                ->getRowArray();


        if (
            !$registro
        ) {
            return null;
        }


        $idOrigen =
            (int) (
                $registro['id_origen']
                ?? 0
            );


        return $idOrigen > 0
            ? $idOrigen
            : null;
    }


    /* =========================================================
       NORMALIZAR FECHA
    ========================================================= */

    protected function normalizarFecha(
        mixed $valor
    ): string {

        $fecha =
            trim(
                (string) (
                    $valor
                    ?? ''
                )
            );


        if (
            $fecha === ''
        ) {

            throw new \InvalidArgumentException(
                'La fecha de registro es obligatoria.'
            );
        }


        /* =====================================================
           FORMATO DE PANTALLA
           dd/mm/YYYY
        ===================================================== */

        $formatoPantalla =
            \DateTime::createFromFormat(
                'd/m/Y',
                $fecha
            );


        if (
            $formatoPantalla
            && $formatoPantalla->format(
                'd/m/Y'
            ) === $fecha
        ) {

            return $formatoPantalla
                ->format(
                    'Y-m-d'
                );
        }


        /* =====================================================
           FORMATO DE BD
           YYYY-mm-dd
        ===================================================== */

        $formatoBd =
            \DateTime::createFromFormat(
                'Y-m-d',
                $fecha
            );


        if (
            $formatoBd
            && $formatoBd->format(
                'Y-m-d'
            ) === $fecha
        ) {

            return $fecha;
        }


        throw new \InvalidArgumentException(
            'La fecha de registro no es válida.'
        );
    }


    /* =========================================================
       NULLABLE
    ========================================================= */

    protected function nullable(
        mixed $valor
    ): ?string {

        $valor =
            trim(
                (string) (
                    $valor
                    ?? ''
                )
            );


        return $valor !== ''
            ? $valor
            : null;
    }
}