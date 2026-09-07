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
        int $idUsuario
    ): array {

        if ($idUsuario <= 0) {

            throw new \RuntimeException(
                'No fue posible identificar al usuario que registra la felicitación.'
            );
        }


        if (empty($personal)) {

            throw new \InvalidArgumentException(
                'Debes agregar al menos una persona a la felicitación.'
            );
        }


        $nombreFelicitante =
            trim(
                (string) (
                    $datos['nombre_felicitante']
                    ?? ''
                )
            );


        if ($nombreFelicitante === '') {

            throw new \InvalidArgumentException(
                'El nombre de la persona que da la felicitación es obligatorio.'
            );
        }


        $razonFelicitacion =
            trim(
                (string) (
                    $datos['razon_felicitacion']
                    ?? ''
                )
            );


        if ($razonFelicitacion === '') {

            throw new \InvalidArgumentException(
                'La razón de la felicitación es obligatoria.'
            );
        }


        $fechaRegistro =
            $this->normalizarFecha(
                $datos['fecha_registro']
                ?? null
            );


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


            if (!$idFelicitacion) {

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


            if ($insertado === false) {

                throw new \RuntimeException(
                    'No fue posible guardar el personal relacionado con la felicitación.'
                );
            }
        }
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


        if ($fecha === '') {

            throw new \InvalidArgumentException(
                'La fecha de registro es obligatoria.'
            );
        }


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