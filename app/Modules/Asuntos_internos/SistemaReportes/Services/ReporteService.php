<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use App\Modules\Asuntos_internos\SistemaReportes\Models\ReporteModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\ReportePersonalModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\ReporteUnidadModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\ReporteEvidenciaModel;
use CodeIgniter\Database\BaseConnection;

class ReporteService
{
    protected BaseConnection $db;

    protected ReporteModel $reporteModel;

    protected ReportePersonalModel $personalModel;

    protected ReporteUnidadModel $unidadModel;

    protected ReporteEvidenciaModel $evidenciaModel;


    public function __construct()
    {
        $this->db =
            \Config\Database::connect(
                'datacore'
            );


        $this->reporteModel =
            new ReporteModel();


        $this->personalModel =
            new ReportePersonalModel();


        $this->unidadModel =
            new ReporteUnidadModel();


        $this->evidenciaModel =
            new ReporteEvidenciaModel();
    }


    /* =========================================================
       GUARDAR REPORTE COMPLETO
    ========================================================= */

    public function guardar(
        array $datos,
        array $personal,
        array $unidades,
        array $archivos,
        int $idUsuario
    ): array {

        if ($idUsuario <= 0) {

            throw new \RuntimeException(
                'No fue posible identificar al usuario que registra el reporte.'
            );
        }


        $rutasCreadas = [];


        $this->db->transBegin();


        try {

            /* =================================================
               REPORTE PRINCIPAL
            ================================================= */

            $datosReporte =
                $this->prepararDatosReporte(
                    $datos,
                    $idUsuario
                );


            $idReporte =
                $this->reporteModel
                    ->insert(
                        $datosReporte,
                        true
                    );


            if (!$idReporte) {

                throw new \RuntimeException(
                    'No fue posible guardar el reporte.'
                );
            }


            $idReporte =
                (int) $idReporte;


            /* =================================================
               PERSONAL
            ================================================= */

            $this->guardarPersonal(
                $idReporte,
                $personal
            );


            /* =================================================
               UNIDADES
            ================================================= */

            $this->guardarUnidades(
                $idReporte,
                $unidades
            );


            /* =================================================
               EVIDENCIAS
            ================================================= */

            $rutasCreadas =
                $this->guardarEvidencias(
                    $idReporte,
                    $archivos,
                    $idUsuario
                );


            /* =================================================
               VALIDAR TRANSACCIÓN
            ================================================= */

            if (
                $this->db->transStatus()
                === false
            ) {

                throw new \RuntimeException(
                    'Ocurrió un error al guardar la información del reporte.'
                );
            }


            $this->db->transCommit();


            return [

                'success' =>
                    true,

                'id_reporte' =>
                    $idReporte,

                'folio' =>
                    $datosReporte['folio'],

            ];


        } catch (\Throwable $e) {

            $this->db->transRollback();


            /*
             * Si alcanzamos a mover archivos físicos,
             * pero la transacción no se completó,
             * eliminamos esos archivos.
             */
            foreach (
                $rutasCreadas
                as $ruta
            ) {

                if (
                    is_file(
                        $ruta
                    )
                ) {

                    @unlink(
                        $ruta
                    );
                }
            }


            throw $e;
        }
    }


    /* =========================================================
       PREPARAR REPORTE PRINCIPAL
    ========================================================= */

    protected function prepararDatosReporte(
        array $datos,
        int $idUsuario
    ): array {

        $folio =
            trim(
                (string)
                ($datos['folio'] ?? '')
            );


        if ($folio === '') {

            $folio =
                $this->construirFolio(

                    $datos['prefijo']
                    ?? $datos['prefijo_folio']
                    ?? 'QJ',

                    $datos['numero_folio']
                    ?? ''

                );
        }


        if ($folio === '') {

            throw new \InvalidArgumentException(
                'El folio es obligatorio.'
            );
        }


        return [

            /* =================================================
               DATOS DEL REPORTE
            ================================================= */

            'folio' =>
                $folio,


            'fecha_registro' =>
                $this->normalizarFecha(
                    $this->valorRequerido(
                        $datos,
                        'fecha_registro',
                        'La fecha de registro es obligatoria.'
                    )
                ),


            'folio_ip' =>
                $this->valorNullable(
                    $datos['folio_ip']
                    ?? null
                ),


            'fecha_queja' =>
                $this->normalizarFecha(
                    $this->valorRequerido(
                        $datos,
                        'fecha_queja',
                        'La fecha de la queja es obligatoria.'
                    )
                ),


            'fecha_acuerdo' =>
                $this->normalizarFechaNullable(
                    $datos['fecha_acuerdo']
                    ?? null
                ),


            'expediente' =>
                $this->valorRequerido(
                    $datos,
                    'expediente',
                    'El expediente es obligatorio.'
                ),


            'nomenclatura' =>
                $this->valorNullable(
                    $datos['nomenclatura']
                    ?? null
                ),


            'numero_oficio' =>
                $this->valorNullable(

                    $datos['no_oficio']
                    ?? $datos['numero_oficio']
                    ?? null

                ),


            /* =================================================
               DATOS DE LOS HECHOS
            ================================================= */

            'fecha_hechos' =>
                $this->normalizarFecha(
                    $this->valorRequerido(
                        $datos,
                        'fecha_hechos',
                        'La fecha de los hechos es obligatoria.'
                    )
                ),


            'hora_hechos' =>
                $this->valorRequerido(
                    $datos,
                    'hora_hechos',
                    'La hora de los hechos es obligatoria.'
                ),


            'descripcion_hechos' =>
                $this->valorRequeridoAlternativo(
                    $datos,
                    [
                        'descripcion_hechos',
                        'descripcion',
                    ],
                    'La descripción de los hechos es obligatoria.'
                ),


            /* =================================================
               UBICACIÓN
            ================================================= */

            'calle' =>
                $this->valorRequerido(
                    $datos,
                    'calle',
                    'La calle es obligatoria.'
                ),


            'numero_exterior' =>
                $this->valorRequeridoAlternativo(
                    $datos,
                    [
                        'numero_exterior',
                        'numero',
                    ],
                    'El número exterior es obligatorio.'
                ),


            'colonia' =>
                $this->valorRequerido(
                    $datos,
                    'colonia',
                    'La colonia es obligatoria.'
                ),


            'entre_calle' =>
                $this->valorRequerido(
                    $datos,
                    'entre_calle',
                    'La primera entre calle es obligatoria.'
                ),


            'y_calle' =>
                $this->valorRequerido(
                    $datos,
                    'y_calle',
                    'La segunda entre calle es obligatoria.'
                ),


            'municipio' =>
                $this->valorRequerido(
                    $datos,
                    'municipio',
                    'El municipio es obligatorio.'
                ),


            'estado' =>
                $this->valorRequerido(
                    $datos,
                    'estado',
                    'El estado es obligatorio.'
                ),


            'sector' =>
                $this->valorRequerido(
                    $datos,
                    'sector',
                    'El sector es obligatorio.'
                ),


            'cuadrante' =>
                $this->valorRequerido(
                    $datos,
                    'cuadrante',
                    'El cuadrante es obligatorio.'
                ),


            'latitud' =>
                $this->decimalNullable(
                    $datos['latitud']
                    ?? null
                ),


            'longitud' =>
                $this->decimalNullable(
                    $datos['longitud']
                    ?? null
                ),


            'origen_ubicacion' =>
                $this->normalizarOrigenUbicacion(
                    $datos['origen_ubicacion']
                    ?? null
                ),


            /* =================================================
               QUEJOSO
            ================================================= */

            'nombre_quejoso' =>
                $this->valorRequeridoAlternativo(
                    $datos,
                    [
                        'nombre_quejoso',
                        'quejoso',
                    ],
                    'El nombre del quejoso es obligatorio.'
                ),


            'edad_quejoso' =>
                $this->edadValida(

                    $datos['edad_quejoso']
                    ?? $datos['edad']
                    ?? null

                ),


            'genero_quejoso' =>
                $this->valorRequeridoAlternativo(
                    $datos,
                    [
                        'genero_quejoso',
                        'genero',
                    ],
                    'El género del quejoso es obligatorio.'
                ),


            'telefono_quejoso' =>
                $this->valorNullable(

                    $datos['telefono_quejoso']
                    ?? $datos['telefono']
                    ?? null

                ),


            'correo_quejoso' =>
                $this->valorNullable(

                    $datos['correo_quejoso']
                    ?? $datos['correo']
                    ?? null

                ),


            /* =================================================
               CLASIFICACIÓN
            ================================================= */

            'clasificacion' =>
                $this->valorRequerido(
                    $datos,
                    'clasificacion',
                    'La clasificación es obligatoria.'
                ),


            'inspector' =>
                $this->valorRequerido(
                    $datos,
                    'inspector',
                    'El inspector es obligatorio.'
                ),


            'investigador' =>
                $this->valorNullable(
                    $datos['investigador']
                    ?? null
                ),


            'quien_emite_resolucion' =>
                $this->valorNullable(
                    $datos['quien_emite_resolucion']
                    ?? null
                ),


            'resolucion' =>
                $this->valorNullable(
                    $datos['resolucion']
                    ?? null
                ),


            'motivos' =>
                $this->valorNullable(
                    $datos['motivos']
                    ?? null
                ),


            'estado_actual' =>
                $this->normalizarEstadoActual(
                    $datos['estado_actual']
                    ?? 'Pendiente'
                ),


            'observaciones' =>
                $this->valorNullable(
                    $datos['observaciones']
                    ?? null
                ),


            /* =================================================
               AUDITORÍA
            ================================================= */

            'created_by' =>
                $idUsuario,


            'eliminado' =>
                0,

        ];
    }


    /* =========================================================
       PERSONAL
    ========================================================= */

    protected function guardarPersonal(
        int $idReporte,
        array $personal
    ): void {

        if (
            empty(
                $personal
            )
        ) {

            throw new \InvalidArgumentException(
                'Debes agregar al menos una persona al reporte.'
            );
        }


        $idsRegistrados =
            [];


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


            /* =================================================
               ID DE PLANTILLA
            ================================================= */

            $plantillaId =
                (int) (

                    $persona['plantilla_id']
                    ?? $persona['id']
                    ?? 0

                );


            if (
                $plantillaId <= 0
            ) {

                throw new \InvalidArgumentException(
                    'Existe una persona relacionada sin identificador válido.'
                );
            }


            /* =================================================
               EVITAR DUPLICADOS
            ================================================= */

            if (
                in_array(
                    $plantillaId,
                    $idsRegistrados,
                    true
                )
            ) {
                continue;
            }


            /* =================================================
               NOMBRE
            ================================================= */

            $nombre =
                strtoupper(
                    trim(
                        (string)
                        ($persona['nombre'] ?? '')
                    )
                );


            if (
                $nombre === ''
            ) {

                throw new \InvalidArgumentException(
                    'Existe una persona relacionada sin nombre.'
                );
            }


            /* =================================================
               TURNO OBLIGATORIO

               Puede:
               - venir originalmente de plantilla;
               - haber sido modificado por el usuario.

               Se guarda siempre el valor final del formulario.
            ================================================= */

            $turno =
                strtoupper(
                    trim(
                        (string)
                        ($persona['turno'] ?? '')
                    )
                );


            if (
                $turno === ''
            ) {

                throw new \InvalidArgumentException(
                    'El turno del personal relacionado es obligatorio.'
                );
            }


            /* =================================================
               GUARDAR SNAPSHOT
            ================================================= */

            $insertado =
                $this->personalModel
                    ->insert([

                        'id_reporte' =>
                            $idReporte,


                        'plantilla_id' =>
                            $plantillaId,


                        'perscod' =>
                            $this->valorNullable(
                                $persona['perscod']
                                ?? null
                            ),


                        'nombre_snapshot' =>
                            $nombre,


                        'area_snapshot' =>
                            $this->valorNullable(
                                $persona['area']
                                ?? null
                            ),


                        'turno_snapshot' =>
                            $turno,

                    ]);


            if (
                $insertado === false
            ) {

                throw new \RuntimeException(
                    'No fue posible guardar el personal relacionado.'
                );
            }


            $idsRegistrados[] =
                $plantillaId;
        }
    }


    /* =========================================================
       UNIDADES
    ========================================================= */

    protected function guardarUnidades(
        int $idReporte,
        array $unidades
    ): void {

        if (
            empty(
                $unidades
            )
        ) {

            throw new \InvalidArgumentException(
                'Debes agregar al menos una unidad al reporte.'
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


            if (
                in_array(
                    $parqueId,
                    $idsRegistrados,
                    true
                )
            ) {
                continue;
            }


            $idOrigen =
                $this->resolverOrigenUnidad(
                    $unidad['origen']
                    ?? null
                );


            $insertado =
                $this->unidadModel
                    ->insert([

                        'id_reporte' =>
                            $idReporte,


                        'parque_vehicular_id' =>
                            $parqueId,


                        'no_economico_snapshot' =>
                            $this->valorNullable(
                                $unidad['no_economico']
                                ?? null
                            ),


                        'placas_snapshot' =>
                            $this->valorNullable(
                                $unidad['placas']
                                ?? null
                            ),


                        'marca_snapshot' =>
                            $this->valorNullable(
                                $unidad['marca']
                                ?? null
                            ),


                        'submarca_snapshot' =>
                            $this->valorNullable(
                                $unidad['submarca']
                                ?? null
                            ),


                        'color_snapshot' =>
                            $this->valorNullable(
                                $unidad['color']
                                ?? null
                            ),


                        'estatus_snapshot' =>
                            $this->valorNullable(
                                $unidad['estatus']
                                ?? null
                            ),


                        'servicio_snapshot' =>
                            $this->valorNullable(
                                $unidad['servicio']
                                ?? null
                            ),


                        'tipo_snapshot' =>
                            $this->valorNullable(
                                $unidad['tipo']
                                ?? null
                            ),


                        'id_origen' =>
                            $idOrigen,

                    ]);


            if (
                $insertado === false
            ) {

                throw new \RuntimeException(
                    'No fue posible guardar una unidad relacionada.'
                );
            }


            $idsRegistrados[] =
                $parqueId;
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
                    (string)
                    $origen
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
                ->where(
                    'activo',
                    1
                )
                ->get()
                ->getRowArray();


        if (
            !$registro
        ) {

            throw new \InvalidArgumentException(
                'El origen de una de las unidades no es válido.'
            );
        }


        return (int)
            $registro['id_origen'];
    }


    /* =========================================================
       EVIDENCIAS
    ========================================================= */

    protected function guardarEvidencias(
        int $idReporte,
        array $archivos,
        int $idUsuario
    ): array {

        if (
            empty(
                $archivos
            )
        ) {

            return [];
        }


        $directorio =
            WRITEPATH
            . 'uploads'
            . DIRECTORY_SEPARATOR
            . 'asuntos_internos'
            . DIRECTORY_SEPARATOR
            . 'reportes'
            . DIRECTORY_SEPARATOR
            . $idReporte;


        if (
            !is_dir(
                $directorio
            )
        ) {

            if (
                !mkdir(
                    $directorio,
                    0775,
                    true
                )
                && !is_dir(
                    $directorio
                )
            ) {

                throw new \RuntimeException(
                    'No fue posible crear el directorio para las evidencias.'
                );
            }
        }


        $rutasCreadas =
            [];


        $orden =
            0;


        foreach (
            $archivos
            as $archivo
        ) {

            if (
                !$archivo
                || !method_exists(
                    $archivo,
                    'isValid'
                )
            ) {
                continue;
            }


            if (
                !$archivo->isValid()
                || $archivo->hasMoved()
            ) {
                continue;
            }


            /* =================================================
               MIME
            ================================================= */

            $mime =
                (string)
                $archivo->getMimeType();


            if (
                !str_starts_with(
                    $mime,
                    'image/'
                )
            ) {

                throw new \InvalidArgumentException(
                    'Las evidencias deben ser archivos de imagen.'
                );
            }


            /* =================================================
               METADATOS
            ================================================= */

            $nombreOriginal =
                (string)
                $archivo->getClientName();


            $extension =
                strtolower(
                    (string)
                    $archivo->getExtension()
                );


            $nombreArchivo =
                $archivo->getRandomName();


            $tamano =
                (int)
                $archivo->getSize();


            /* =================================================
               MOVER
            ================================================= */

            $archivo->move(
                $directorio,
                $nombreArchivo
            );


            $rutaAbsoluta =
                $directorio
                . DIRECTORY_SEPARATOR
                . $nombreArchivo;


            $rutaRelativa =
                'writable/uploads/asuntos_internos/reportes/'
                . $idReporte
                . '/'
                . $nombreArchivo;


            $rutasCreadas[] =
                $rutaAbsoluta;


            /* =================================================
               REGISTRAR EN BD
            ================================================= */

            $insertado =
                $this->evidenciaModel
                    ->insert([

                        'id_reporte' =>
                            $idReporte,


                        'nombre_original' =>
                            $nombreOriginal,


                        'nombre_archivo' =>
                            $nombreArchivo,


                        'ruta_archivo' =>
                            $rutaRelativa,


                        'extension' =>
                            $extension !== ''
                                ? $extension
                                : null,


                        'mime_type' =>
                            $mime !== ''
                                ? $mime
                                : null,


                        'tamano_bytes' =>
                            $tamano > 0
                                ? $tamano
                                : null,


                        'orden' =>
                            $orden,


                        'created_by' =>
                            $idUsuario,


                        'eliminado' =>
                            0,

                    ]);


            if (
                $insertado === false
            ) {

                throw new \RuntimeException(
                    'No fue posible registrar una evidencia.'
                );
            }


            $orden++;
        }


        return $rutasCreadas;
    }


    /* =========================================================
       VALOR REQUERIDO
    ========================================================= */

    protected function valorRequerido(
        array $datos,
        string $campo,
        string $mensaje
    ): string {

        $valor =
            trim(
                (string)
                ($datos[$campo] ?? '')
            );


        if (
            $valor === ''
        ) {

            throw new \InvalidArgumentException(
                $mensaje
            );
        }


        return $valor;
    }


    /* =========================================================
       VALOR REQUERIDO ALTERNATIVO
    ========================================================= */

    protected function valorRequeridoAlternativo(
        array $datos,
        array $campos,
        string $mensaje
    ): string {

        foreach (
            $campos
            as $campo
        ) {

            $valor =
                trim(
                    (string)
                    ($datos[$campo] ?? '')
                );


            if (
                $valor !== ''
            ) {

                return $valor;
            }
        }


        throw new \InvalidArgumentException(
            $mensaje
        );
    }


    /* =========================================================
       VALOR NULLABLE
    ========================================================= */

    protected function valorNullable(
        mixed $valor
    ): ?string {

        $texto =
            trim(
                (string)
                ($valor ?? '')
            );


        return $texto !== ''
            ? $texto
            : null;
    }


    /* =========================================================
       DECIMAL NULLABLE
    ========================================================= */

    protected function decimalNullable(
        mixed $valor
    ): ?float {

        $texto =
            trim(
                (string)
                ($valor ?? '')
            );


        if (
            $texto === ''
        ) {

            return null;
        }


        if (
            !is_numeric(
                $texto
            )
        ) {

            throw new \InvalidArgumentException(
                'Las coordenadas proporcionadas no son válidas.'
            );
        }


        return (float)
            $texto;
    }


    /* =========================================================
       EDAD
    ========================================================= */

    protected function edadValida(
        mixed $valor
    ): int {

        $edad =
            filter_var(
                $valor,
                FILTER_VALIDATE_INT
            );


        if (
            $edad === false
            || $edad < 0
            || $edad > 255
        ) {

            throw new \InvalidArgumentException(
                'La edad del quejoso no es válida.'
            );
        }


        return (int)
            $edad;
    }


    /* =========================================================
       NORMALIZAR FECHA
    ========================================================= */

    protected function normalizarFecha(
        mixed $valor
    ): string {

        $fecha =
            trim(
                (string)
                $valor
            );


        if (
            $fecha === ''
        ) {

            throw new \InvalidArgumentException(
                'La fecha proporcionada no es válida.'
            );
        }


        /* =====================================================
           YYYY-MM-DD
        ===================================================== */

        if (
            preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $fecha
            )
        ) {

            [
                $anio,
                $mes,
                $dia
            ] =
                explode(
                    '-',
                    $fecha
                );


            if (
                !checkdate(
                    (int) $mes,
                    (int) $dia,
                    (int) $anio
                )
            ) {

                throw new \InvalidArgumentException(
                    'La fecha proporcionada no es válida.'
                );
            }


            return sprintf(
                '%04d-%02d-%02d',
                (int) $anio,
                (int) $mes,
                (int) $dia
            );
        }


        /* =====================================================
           DD/MM/YYYY
        ===================================================== */

        if (
            preg_match(
                '/^\d{1,2}\/\d{1,2}\/\d{4}$/',
                $fecha
            )
        ) {

            [
                $dia,
                $mes,
                $anio
            ] =
                explode(
                    '/',
                    $fecha
                );


            if (
                checkdate(
                    (int) $mes,
                    (int) $dia,
                    (int) $anio
                )
            ) {

                return sprintf(
                    '%04d-%02d-%02d',
                    (int) $anio,
                    (int) $mes,
                    (int) $dia
                );
            }
        }


        throw new \InvalidArgumentException(
            'La fecha proporcionada no es válida.'
        );
    }


    /* =========================================================
       NORMALIZAR FECHA OPCIONAL
    ========================================================= */

    protected function normalizarFechaNullable(
        mixed $valor
    ): ?string {

        $fecha =
            trim(
                (string)
                ($valor ?? '')
            );


        if (
            $fecha === ''
        ) {

            return null;
        }


        return $this->normalizarFecha(
            $fecha
        );
    }


    /* =========================================================
       ESTADO ACTUAL
    ========================================================= */

    protected function normalizarEstadoActual(
        mixed $valor
    ): string {

        $estado =
            strtolower(
                trim(
                    (string)
                    $valor
                )
            );


        return match ($estado) {

            'finalizado' =>
                'Finalizado',

            'en proceso' =>
                'En proceso',

            default =>
                'Pendiente',

        };
    }


    /* =========================================================
       ORIGEN DE UBICACIÓN
    ========================================================= */

    protected function normalizarOrigenUbicacion(
        mixed $valor
    ): ?string {

        $origen =
            strtolower(
                trim(
                    (string)
                    $valor
                )
            );


        if (
            $origen === ''
        ) {

            return null;
        }


        $permitidos = [
            'manual',
            'busqueda',
            'mapa',
        ];


        if (
            !in_array(
                $origen,
                $permitidos,
                true
            )
        ) {

            return null;
        }


        return $origen;
    }


    /* =========================================================
       CONSTRUIR FOLIO
    ========================================================= */

    protected function construirFolio(
        mixed $prefijo,
        mixed $numero
    ): string {

        $prefijo =
            strtoupper(
                trim(
                    (string)
                    $prefijo
                )
            );


        $numero =
            trim(
                (string)
                $numero
            );


        if (
            $prefijo === ''
        ) {

            $prefijo =
                'QJ';
        }


        if (
            $numero === ''
        ) {

            return '';
        }


        return $prefijo
            . '-'
            . $numero;
    }
}