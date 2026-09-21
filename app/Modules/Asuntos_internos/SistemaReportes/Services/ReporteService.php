<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use App\Modules\Asuntos_internos\SistemaReportes\Models\ReporteModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\ReportePersonalModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\ReporteUnidadModel;
use App\Modules\Asuntos_internos\SistemaReportes\Models\ReporteEvidenciaModel;
use App\Modules\Asuntos_internos\SistemaReportes\Services\FolioService;
use CodeIgniter\Database\BaseConnection;

class ReporteService
{
    protected BaseConnection $db;

    protected ReporteModel $reporteModel;

    protected ReportePersonalModel $personalModel;

    protected ReporteUnidadModel $unidadModel;

    protected ReporteEvidenciaModel $evidenciaModel;

    protected FolioService $folioService;

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

        $this->folioService =
            new FolioService(
                $this->db
            );
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

        /* =========================================================
        VALIDAR USUARIO
        ========================================================= */

        if ($idUsuario <= 0) {

            throw new \RuntimeException(
                'No fue posible identificar al usuario que registra el reporte.'
            );
        }


        /* =========================================================
        ARCHIVOS CREADOS
        ========================================================= */

        $rutasCreadas = [];


        /* =========================================================
        INICIAR TRANSACCIÓN
        ========================================================= */

        $this->db->transBegin();


        try {

            /* =====================================================
            PREPARAR DATOS DEL REPORTE
            ===================================================== */

            $datosReporte =
                $this->prepararDatosReporte(
                    $datos,
                    $idUsuario
                );


            /* =====================================================
            VALIDAR FOLIOS IP / IMP ÚNICOS
            ===================================================== */

            $this->validarFoliosUnicos(
                $datosReporte
            );


            /* =====================================================
            CLAVE DEL FOLIO
            ===================================================== */

            $claveFolio =
                strtoupper(
                    trim(
                        (string) (
                            $datos['tipo_folio']
                            ?? 'QJ'
                        )
                    )
                );


            $clavesPermitidas = [
                'QJ',
                'QJV',
                'QJF',
            ];


            if (
                !in_array(
                    $claveFolio,
                    $clavesPermitidas,
                    true
                )
            ) {

                throw new \InvalidArgumentException(
                    'El tipo de folio seleccionado no es válido.'
                );
            }


            /* =====================================================
            GENERAR FOLIO AUTOMÁTICO
            ===================================================== */

            $folioGenerado =
                $this->folioService
                ->generar(
                    $claveFolio
                );


            /*
         * Aunque QJ, QJV y QJF tengan consecutivos separados,
         * todos siguen perteneciendo al tipo general QUEJA.
         */

            $datosReporte['tipo_registro'] =
                $folioGenerado['tipo_registro'];


            $datosReporte['numero_folio'] =
                $folioGenerado['numero_folio'];


            $datosReporte['folio'] =
                $folioGenerado['folio'];


            /* =====================================================
            MODALIDAD DE UNIDAD PARA QJF
            ===================================================== */

            if ($claveFolio === 'QJF') {

                $datosReporte['modalidad_unidad'] =
                    'NO_APLICA';
            }


            /* =====================================================
            NOMENCLATURA AUTOMÁTICA
            ===================================================== */

            $fechaRegistro =
                trim(
                    (string) (
                        $datosReporte['fecha_registro']
                        ?? ''
                    )
                );


            if ($fechaRegistro === '') {

                throw new \InvalidArgumentException(
                    'La fecha de registro no es válida para generar la nomenclatura.'
                );
            }


            $timestampFechaRegistro =
                strtotime(
                    $fechaRegistro
                );


            if ($timestampFechaRegistro === false) {

                throw new \InvalidArgumentException(
                    'La fecha de registro no tiene un formato válido.'
                );
            }


            $anioRegistro =
                date(
                    'Y',
                    $timestampFechaRegistro
                );


            $datosReporte['nomenclatura'] =
                'CGSC/CAI/'
                . $claveFolio
                . '/'
                . $datosReporte['numero_folio']
                . '/'
                . $anioRegistro;


            /* =====================================================
            GUARDAR REPORTE
            ===================================================== */

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


            /* =====================================================
            PERSONAL Y UNIDADES

            QJF:
            - No requiere personal relacionado.
            - No requiere modalidad de unidad.
            - No guarda unidades.

            QJ / QJV:
            - Conservan el comportamiento normal.
            ===================================================== */

            if ($claveFolio !== 'QJF') {

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
                    $unidades,
                    $datosReporte['modalidad_unidad']
                );
            }


            /* =====================================================
            DIRECCIÓN PARA NOTIFICACIÓN
            ===================================================== */

            $this->guardarDireccionNotificacion(
                $idReporte,
                $datos,
                $idUsuario
            );


            /* =====================================================
            MOTIVOS Y SANCIONES
            ===================================================== */

            $this->guardarMotivosYSanciones(
                $idReporte,
                $datos,
                $idUsuario
            );


            /* =====================================================
            EVIDENCIAS
            ===================================================== */

            $rutasCreadas =
                $this->guardarEvidencias(
                    $idReporte,
                    $archivos,
                    $idUsuario
                );


            /* =====================================================
            VALIDAR TRANSACCIÓN
            ===================================================== */

            if (
                $this->db->transStatus()
                === false
            ) {

                throw new \RuntimeException(
                    'Ocurrió un error al guardar la información del reporte.'
                );
            }


            /* =====================================================
            CONFIRMAR
            ===================================================== */

            $this->db->transCommit();


            /* =====================================================
            RESPUESTA
            ===================================================== */

            return [

                'success' =>
                true,

                'id_reporte' =>
                $idReporte,

                'tipo_registro' =>
                $datosReporte['tipo_registro'],

                'clave_folio' =>
                $folioGenerado['clave_folio']
                    ?? $claveFolio,

                'numero_folio' =>
                $datosReporte['numero_folio'],

                'folio' =>
                $datosReporte['folio'],

                'nomenclatura' =>
                $datosReporte['nomenclatura'],

            ];
        } catch (\Throwable $e) {

            /* =====================================================
            REVERTIR TRANSACCIÓN
            ===================================================== */

            $this->db->transRollback();


            /* =====================================================
            ELIMINAR ARCHIVOS FÍSICOS CREADOS
            ===================================================== */

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


            /* =====================================================
            PROPAGAR ERROR
            ===================================================== */

            throw $e;
        }
    }


    /* =========================================================
    ACTUALIZAR REPORTE COMPLETO
    ========================================================= */

    public function actualizar(
        int $idReporte,
        array $datos,
        array $personal,
        array $unidades,
        array $archivos,
        array $evidenciasEliminadas,
        int $idUsuario
    ): array {

        /* =========================================================
        VALIDAR IDENTIFICADORES
        ========================================================= */

        if ($idReporte <= 0) {

            throw new \InvalidArgumentException(
                'El reporte proporcionado no es válido.'
            );
        }


        if ($idUsuario <= 0) {

            throw new \RuntimeException(
                'No fue posible identificar al usuario que modifica el reporte.'
            );
        }


        /* =========================================================
        VALIDAR REPORTE ACTUAL
        ========================================================= */

        $reporteActual =
            $this->db
                ->table(
                    'ai_reportes'
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->where(
                    'eliminado',
                    0
                )
                ->get()
                ->getRowArray();


        if (!$reporteActual) {

            throw new \RuntimeException(
                'El reporte que intentas modificar no existe.'
            );
        }


        /* =========================================================
        ARCHIVOS CREADOS
        ========================================================= */

        $rutasCreadas = [];


        /* =========================================================
        INICIAR TRANSACCIÓN
        ========================================================= */

        $this->db->transBegin();


        try {

            /* =====================================================
            PREPARAR DATOS PRINCIPALES
            ===================================================== */

            $datosReporte =
                $this->prepararDatosReporte(
                    $datos,
                    $idUsuario
                );


            /*
            * Estos campos no deben tomarse directamente
            * del formulario durante una edición.
            */

            unset(
                $datosReporte['created_by'],
                $datosReporte['eliminado'],
                $datosReporte['tipo_registro']
            );


            /* =====================================================
            VALIDAR FOLIOS IP / IMP ÚNICOS
            ===================================================== */

            $this->validarFoliosUnicos(
                $datosReporte,
                $idReporte
            );


            /* =====================================================
            TIPO DE FOLIO SELECCIONADO
            ===================================================== */

            $claveFolioNueva =
                strtoupper(
                    trim(
                        (string) (
                            $datos['tipo_folio']
                            ?? ''
                        )
                    )
                );


            $clavesPermitidas = [
                'QJ',
                'QJV',
                'QJF',
            ];


            if (
                !in_array(
                    $claveFolioNueva,
                    $clavesPermitidas,
                    true
                )
            ) {

                throw new \InvalidArgumentException(
                    'El tipo de folio seleccionado no es válido.'
                );
            }


            /* =====================================================
            DETERMINAR TIPO DE FOLIO ACTUAL
            ===================================================== */

            $folioActual =
                strtoupper(
                    trim(
                        (string) (
                            $reporteActual['folio']
                            ?? ''
                        )
                    )
                );


            $claveFolioActual =
                'QJ';


            if (
                str_starts_with(
                    $folioActual,
                    'QJV-'
                )
            ) {

                $claveFolioActual =
                    'QJV';

            } elseif (
                str_starts_with(
                    $folioActual,
                    'QJF-'
                )
            ) {

                $claveFolioActual =
                    'QJF';

            } elseif (
                str_starts_with(
                    $folioActual,
                    'QJ-'
                )
            ) {

                $claveFolioActual =
                    'QJ';
            }


            /* =====================================================
            VALIDAR SI CAMBIÓ LA FAMILIA DEL FOLIO
            ===================================================== */

            $cambioTipoFolio =
                $claveFolioNueva
                !== $claveFolioActual;


            /* =====================================================
            FOLIO Y CONSECUTIVO
            ===================================================== */

            if ($cambioTipoFolio) {

                /*
                * Cambió, por ejemplo:
                *
                * QJ -> QJV
                * QJ -> QJF
                * QJF -> QJ
                *
                * Consumimos el siguiente consecutivo
                * de la nueva familia.
                */

                $folioGenerado =
                    $this->folioService
                        ->generar(
                            $claveFolioNueva
                        );


                $datosReporte['numero_folio'] =
                    (int) (
                        $folioGenerado['numero_folio']
                        ?? 0
                    );


                $datosReporte['folio'] =
                    (string) (
                        $folioGenerado['folio']
                        ?? ''
                    );


                if (
                    $datosReporte['numero_folio'] <= 0
                    || trim(
                        $datosReporte['folio']
                    ) === ''
                ) {

                    throw new \RuntimeException(
                        'No fue posible generar el nuevo folio.'
                    );
                }

            } else {

                /*
                * Si conserva la misma familia,
                * conserva exactamente su número y folio.
                */

                $datosReporte['numero_folio'] =
                    (int) (
                        $reporteActual['numero_folio']
                        ?? 0
                    );


                $datosReporte['folio'] =
                    (string) (
                        $reporteActual['folio']
                        ?? ''
                    );


                if (
                    $datosReporte['numero_folio'] <= 0
                    || trim(
                        $datosReporte['folio']
                    ) === ''
                ) {

                    throw new \RuntimeException(
                        'No fue posible identificar el folio actual del reporte.'
                    );
                }
            }


            /* =====================================================
            NOMENCLATURA AUTOMÁTICA
            ===================================================== */

            $fechaRegistro =
                trim(
                    (string) (
                        $datosReporte['fecha_registro']
                        ?? $reporteActual['fecha_registro']
                        ?? ''
                    )
                );


            if ($fechaRegistro === '') {

                throw new \InvalidArgumentException(
                    'La fecha de registro no es válida para generar la nomenclatura.'
                );
            }


            $timestampFechaRegistro =
                strtotime(
                    $fechaRegistro
                );


            if ($timestampFechaRegistro === false) {

                throw new \InvalidArgumentException(
                    'La fecha de registro no tiene un formato válido.'
                );
            }


            $anioRegistro =
                date(
                    'Y',
                    $timestampFechaRegistro
                );


            $datosReporte['nomenclatura'] =
                'CGSC/CAI/'
                . $claveFolioNueva
                . '/'
                . $datosReporte['numero_folio']
                . '/'
                . $anioRegistro;


            /* =====================================================
            MODALIDAD DE UNIDAD

            QJF no utiliza Personal ni Unidades.
            ===================================================== */

            if ($claveFolioNueva === 'QJF') {

                $datosReporte['modalidad_unidad'] =
                    'NO_APLICA';
            }


            /* =====================================================
            AUDITORÍA
            ===================================================== */

            $datosReporte['updated_by'] =
                $idUsuario;


            /* =====================================================
            ACTUALIZAR REPORTE PRINCIPAL
            ===================================================== */

            $actualizado =
                $this->reporteModel
                    ->update(
                        $idReporte,
                        $datosReporte
                    );


            if ($actualizado === false) {

                throw new \RuntimeException(
                    'No fue posible actualizar el reporte.'
                );
            }


            /* =====================================================
            LIMPIAR PERSONAL ACTUAL

            Siempre limpiamos primero para que:
            - QJ/QJV puedan reconstruirse con los datos editados;
            - QJF no conserve personal anterior.
            ===================================================== */

            $this->db
                ->table(
                    'ai_reporte_personal'
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->delete();


            /* =====================================================
            LIMPIAR UNIDADES ACTUALES

            QJF tampoco debe conservar unidades de una
            clasificación anterior.
            ===================================================== */

            $this->db
                ->table(
                    'ai_reporte_unidades'
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->delete();


            /* =====================================================
            PERSONAL Y UNIDADES PARA QJ / QJV
            ===================================================== */

            if ($claveFolioNueva !== 'QJF') {

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
                    $unidades,
                    $datosReporte['modalidad_unidad']
                );
            }


            /* =====================================================
            DIRECCIÓN PARA NOTIFICACIÓN
            ===================================================== */

            $this->actualizarDireccionNotificacion(
                $idReporte,
                $datos,
                $idUsuario
            );


            /* =====================================================
            MOTIVOS Y SANCIONES POR MOTIVO
            ===================================================== */

            $this->actualizarMotivosYSancionesDesdeEdicion(
                $idReporte,
                $datos,
                $idUsuario
            );


            /* =====================================================
            SANCIÓN DISCIPLINARIA
            ===================================================== */

            $this->corregirSancionDesdeEdicion(
                $idReporte,
                $datos,
                $idUsuario
            );


            /* =====================================================
            EVIDENCIAS ELIMINADAS
            ===================================================== */

            $this->marcarEvidenciasEliminadas(
                $idReporte,
                $evidenciasEliminadas,
                $idUsuario
            );


            /* =====================================================
            EVIDENCIAS NUEVAS
            ===================================================== */

            $rutasCreadas =
                $this->guardarEvidencias(
                    $idReporte,
                    $archivos,
                    $idUsuario
                );


            /* =====================================================
            VALIDAR TRANSACCIÓN
            ===================================================== */

            if (
                $this->db->transStatus()
                === false
            ) {

                throw new \RuntimeException(
                    'Ocurrió un error al actualizar la información del reporte.'
                );
            }


            /* =====================================================
            CONFIRMAR
            ===================================================== */

            $this->db->transCommit();


            /* =====================================================
            RESPUESTA
            ===================================================== */

            return [

                'success' =>
                    true,

                'id_reporte' =>
                    $idReporte,

                'tipo_registro' =>
                    (string) (
                        $reporteActual['tipo_registro']
                        ?? 'QUEJA'
                    ),

                'clave_folio' =>
                    $claveFolioNueva,

                'numero_folio' =>
                    (int) $datosReporte['numero_folio'],

                'folio' =>
                    (string) $datosReporte['folio'],

                'nomenclatura' =>
                    (string) $datosReporte['nomenclatura'],

                'modalidad_unidad' =>
                    (string) (
                        $datosReporte['modalidad_unidad']
                        ?? ''
                    ),

            ];

        } catch (\Throwable $e) {

            /* =====================================================
            REVERTIR TRANSACCIÓN
            ===================================================== */

            $this->db->transRollback();


            /* =====================================================
            ELIMINAR ARCHIVOS CREADOS DURANTE EL INTENTO
            ===================================================== */

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

        /* =========================================================
        QUEJOSO ANÓNIMO
        ========================================================= */

        $esAnonimo =
            (int) (
                $datos['es_anonimo']
                ?? 0
            ) === 1;


        $numeroAnonimo =
            null;


        if ($esAnonimo) {

            $numeroAnonimo =
                trim(
                    (string) (
                        $datos['numero_anonimo']
                        ?? ''
                    )
                );


            if (
                $numeroAnonimo === ''
            ) {

                throw new \InvalidArgumentException(
                    'El No. Numérico es obligatorio para una queja anónima.'
                );
            }
        }


        /* =========================================================
        SITUACIÓN DE LA SANCIÓN
        ========================================================= */

        $sinSanciones =
            (int) (
                $datos['sin_sanciones']
                ?? 0
            ) === 1;


        $bajaVoluntaria =
            (int) (
                $datos['baja_voluntaria']
                ?? 0
            ) === 1;


        $desistir =
            (int) (
                $datos['desistir']
                ?? 0
            ) === 1;


        /* =========================================================
        VALIDAR EXCLUSIÓN ENTRE OPCIONES
        ========================================================= */

        $situacionesActivas =
            (
                $sinSanciones
                    ? 1
                    : 0
            )
            +
            (
                $bajaVoluntaria
                    ? 1
                    : 0
            )
            +
            (
                $desistir
                    ? 1
                    : 0
            );


        if (
            $situacionesActivas > 1
        ) {

            throw new \InvalidArgumentException(
                'Sin sanciones, Baja voluntaria y Desistir no pueden seleccionarse al mismo tiempo.'
            );
        }


        /* =========================================================
        ESTADO ACTUAL
        ========================================================= */

        $estadoActual =
            $this->normalizarEstadoActual(
                $datos['estado_actual']
                ?? 'Pendiente'
            );


        /*
        * Baja voluntaria y Desistir
        * siempre implican un reporte finalizado.
        */

        if (
            $bajaVoluntaria
            || $desistir
        ) {

            $estadoActual =
                'Finalizado';
        }


        return [

            /* =================================================
            DATOS DEL REPORTE
            ================================================= */

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


            'folio_imp' =>
            $this->valorNullable(
                $datos['folio_imp']
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
            $this->valorNullable(
                $datos['expediente']
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
            $this->valorNullable(
                $datos['entre_calle']
                    ?? null
            ),


            'y_calle' =>
            $this->valorNullable(
                $datos['y_calle']
                    ?? null
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


            'id_cuadra' =>
            $this->valorNullable(
                $datos['id_cuadra']
                    ?? null
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

            'es_anonimo' =>
            $esAnonimo
                ? 1
                : 0,


            'numero_anonimo' =>
            $esAnonimo
                ? $numeroAnonimo
                : null,


            'nombre_quejoso' =>
            $esAnonimo
                ? null
                : $this->valorRequeridoAlternativo(
                    $datos,
                    [
                        'nombre_quejoso',
                        'quejoso',
                    ],
                    'El nombre del quejoso es obligatorio.'
                ),


            'edad_quejoso' =>
            $esAnonimo
                ? null
                : $this->edadValida(
                    $datos['edad_quejoso']
                        ?? $datos['edad']
                        ?? null
                ),


            'genero_quejoso' =>
            $esAnonimo
                ? null
                : $this->valorRequeridoAlternativo(
                    $datos,
                    [
                        'genero_quejoso',
                        'genero',
                    ],
                    'El género del quejoso es obligatorio.'
                ),


            'telefono_quejoso' =>
            $esAnonimo
                ? null
                : $this->valorNullable(
                    $datos['telefono_quejoso']
                        ?? $datos['telefono']
                        ?? null
                ),


            'correo_quejoso' =>
            $esAnonimo
                ? null
                : $this->valorNullable(
                    $datos['correo_quejoso']
                        ?? $datos['correo']
                        ?? null
                ),


            'direccion_quejoso' =>
            $esAnonimo
                ? null
                : $this->valorNullable(
                    $datos['direccion_quejoso']
                        ?? null
                ),


            'canalizacion_area' =>
            $this->valorNullable(
                $datos['canalizacion_area']
                    ?? $datos['canalizacion']
                    ?? null
            ),


            'canalizacion_otro' =>
            $this->valorNullable(
                $datos['canalizacion_otro']
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
            $estadoActual,


            /* =================================================
            SIN SANCIONES
            ================================================= */

            'sin_sanciones' =>
            $sinSanciones
                ? 1
                : 0,


            /* =================================================
            BAJA VOLUNTARIA
            ================================================= */

            'baja_voluntaria' =>
            $bajaVoluntaria
                ? 1
                : 0,


            /* =================================================
            DESISTIR
            ================================================= */

            'desistir' =>
            $desistir
                ? 1
                : 0,


            'observaciones' =>
            $this->valorNullable(
                $datos['observaciones']
                    ?? null
            ),


            /* =================================================
            MODALIDAD DE UNIDAD
            ================================================= */

            'modalidad_unidad' =>
            $this->normalizarModalidadUnidad(
                $datos['modalidad_unidad']
                    ?? 'CON_UNIDAD'
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
            empty($personal)
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

                    'alias_snapshot' =>
                    $this->valorNullable(
                        $persona['alias']
                            ?? null
                    ),

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
    SANCIÓN DISCIPLINARIA INICIAL
    ========================================================= */

    protected function guardarSancionInicial(
        int $idReporte,
        array $datos,
        int $idUsuario
    ): void {

        $tipo =
            trim(
                (string) (
                    $datos['sancion_disciplinaria']
                    ?? ''
                )
            );


        /*
     * La sanción es opcional.
     *
     * Si el reporte todavía no tiene una sanción,
     * no generamos ningún registro.
     */
        if ($tipo === '') {
            return;
        }


        /* =====================================================
        VALIDAR CATÁLOGO
        ===================================================== */

        $tiposPermitidos = [
            'Arresto',
            'Amonestación',
            'Otro',
        ];


        if (
            !in_array(
                $tipo,
                $tiposPermitidos,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'La sanción disciplinaria seleccionada no es válida.'
            );
        }


        /* =====================================================
        DESCRIPCIÓN PARA "OTRO"
        ===================================================== */

        $descripcionOtro = null;


        if ($tipo === 'Otro') {

            $descripcionOtro =
                trim(
                    (string) (
                        $datos['sancion_otro']
                        ?? ''
                    )
                );


            if ($descripcionOtro === '') {

                throw new \InvalidArgumentException(
                    'Debes especificar la sanción disciplinaria.'
                );
            }


            if (
                mb_strlen(
                    $descripcionOtro
                ) > 255
            ) {

                throw new \InvalidArgumentException(
                    'La descripción de la sanción no puede exceder 255 caracteres.'
                );
            }
        }


        /* =====================================================
        GUARDAR
        ===================================================== */

        $insertado =
            $this->db
            ->table(
                'ai_reporte_sanciones'
            )
            ->insert([

                'id_reporte' =>
                $idReporte,

                'tipo' =>
                $tipo,

                'descripcion_otro' =>
                $descripcionOtro,

                'origen' =>
                'registro',

                'id_seguimiento' =>
                null,

                'es_actual' =>
                1,

                'created_by' =>
                $idUsuario,

                'eliminado' =>
                0,

            ]);


        if ($insertado === false) {

            throw new \RuntimeException(
                'No fue posible guardar la sanción disciplinaria.'
            );
        }
    }


    /* =========================================================
    GUARDAR MOTIVOS Y SANCIONES
    ========================================================= */

    protected function guardarMotivosYSanciones(
        int $idReporte,
        array $datos,
        int $idUsuario
    ): void {

        if (
            $idReporte <= 0
            || $idUsuario <= 0
        ) {
            throw new \RuntimeException(
                'No fue posible identificar el reporte o usuario.'
            );
        }


        /* =====================================================
        SIN SANCIONES
        ===================================================== */

        $sinSanciones =
            (int) (
                $datos['sin_sanciones']
                ?? 0
            ) === 1;


        /* =====================================================
        BAJA VOLUNTARIA
        ===================================================== */

        $bajaVoluntaria =
            (int) (
                $datos['baja_voluntaria']
                ?? 0
            ) === 1;


        /* =====================================================
        MOTIVOS SELECCIONADOS
        ===================================================== */

        $motivos =
            $datos['motivos_seleccionados']
            ?? [];


        if (
            !is_array(
                $motivos
            )
        ) {
            $motivos =
                [];
        }


        /*
        * El motivo es opcional.
        */
        if (
            empty($motivos)
        ) {
            return;
        }


        /* =====================================================
        EVITAR DUPLICADOS
        ===================================================== */

        $motivosRegistrados =
            [];


        foreach (
            $motivos
            as $motivoFormulario
        ) {

            if (
                !is_array(
                    $motivoFormulario
                )
            ) {
                continue;
            }


            /* =================================================
            ID MOTIVO
            ================================================= */

            $idMotivo =
                (int) (
                    $motivoFormulario['id_motivo']
                    ?? 0
                );


            if (
                $idMotivo <= 0
            ) {
                throw new \InvalidArgumentException(
                    'Existe un motivo seleccionado sin identificador válido.'
                );
            }


            /* =================================================
            EVITAR DUPLICADO
            ================================================= */

            if (
                in_array(
                    $idMotivo,
                    $motivosRegistrados,
                    true
                )
            ) {
                continue;
            }


            /* =================================================
            CONSULTAR CATÁLOGO
            ================================================= */

            $motivoCatalogo =
                $this->db
                ->table(
                    'ai_cat_motivos'
                )
                ->select([
                    'id_motivo',
                    'motivo',
                    'sancion',
                    'activo',
                ])
                ->where(
                    'id_motivo',
                    $idMotivo
                )
                ->where(
                    'activo',
                    1
                )
                ->get()
                ->getRowArray();


            if (
                !$motivoCatalogo
            ) {
                throw new \InvalidArgumentException(
                    'Uno de los motivos seleccionados ya no está disponible.'
                );
            }


            /* =================================================
            GUARDAR RELACIÓN REPORTE - MOTIVO
            ================================================= */

            $insertadoMotivo =
                $this->db
                ->table(
                    'ai_reporte_motivos'
                )
                ->insert([

                    'id_reporte' =>
                    $idReporte,

                    'id_motivo' =>
                    $idMotivo,

                    'created_by' =>
                    $idUsuario,

                    'eliminado' =>
                    0,
                ]);


            if (
                $insertadoMotivo === false
            ) {
                throw new \RuntimeException(
                    'No fue posible guardar uno de los motivos del reporte.'
                );
            }


            $idReporteMotivo =
                (int) $this->db
                    ->insertID();


            if (
                $idReporteMotivo <= 0
            ) {
                throw new \RuntimeException(
                    'No fue posible identificar el motivo registrado.'
                );
            }


            $motivosRegistrados[] =
                $idMotivo;


            /* =================================================
            SIN SANCIÓN / BAJA VOLUNTARIA
            ================================================= */

            if (
                $sinSanciones
                || $bajaVoluntaria
            ) {
                continue;
            }


            /* =================================================
            SANCIÓN DESDE CATÁLOGO
            ================================================= */

            $tipoSancion =
                trim(
                    (string) (
                        $motivoCatalogo['sancion']
                        ?? ''
                    )
                );


            if (
                $tipoSancion === ''
            ) {
                continue;
            }


            /* =================================================
            FOLIO DE SANCIÓN
            ================================================= */

            $folioSancion =
                trim(
                    (string) (
                        $motivoFormulario['folio_sancion']
                        ?? ''
                    )
                );


            if (
                $folioSancion === ''
            ) {
                $folioSancion =
                    null;
            }


            if (
                $folioSancion !== null
                && mb_strlen(
                    $folioSancion
                ) > 150
            ) {
                throw new \InvalidArgumentException(
                    'El folio de la sanción no puede exceder 150 caracteres.'
                );
            }


            /* =================================================
            GUARDAR SANCIÓN
            ================================================= */

            $insertadoSancion =
                $this->db
                ->table(
                    'ai_reporte_sanciones'
                )
                ->insert([

                    'id_reporte' =>
                    $idReporte,

                    'id_reporte_motivo' =>
                    $idReporteMotivo,

                    'tipo' =>
                    $tipoSancion,

                    'descripcion_otro' =>
                    null,

                    'folio_sancion' =>
                    $folioSancion,

                    'origen' =>
                    'registro',

                    'id_seguimiento' =>
                    null,

                    'es_actual' =>
                    1,

                    'created_by' =>
                    $idUsuario,

                    'eliminado' =>
                    0,
                ]);


            if (
                $insertadoSancion === false
            ) {
                throw new \RuntimeException(
                    'No fue posible guardar la sanción relacionada con el motivo.'
                );
            }
        }
    }


    /* =========================================================
    ACTUALIZAR MOTIVOS Y SANCIONES DESDE EDICIÓN
    ========================================================= */

    protected function actualizarMotivosYSancionesDesdeEdicion(
        int $idReporte,
        array $datos,
        int $idUsuario
    ): void {

        if (
            $idReporte <= 0
            || $idUsuario <= 0
        ) {

            throw new \RuntimeException(
                'No fue posible identificar el reporte o usuario.'
            );
        }


        /* =====================================================
        SIN SANCIONES
        ===================================================== */

        $sinSanciones =
            (int) (
                $datos['sin_sanciones']
                ?? 0
            ) === 1;


        /* =====================================================
        BAJA VOLUNTARIA
        ===================================================== */

        $bajaVoluntaria =
            (int) (
                $datos['baja_voluntaria']
                ?? 0
            ) === 1;


        /* =====================================================
        MOTIVOS RECIBIDOS DESDE EDITAR
        ===================================================== */

        $motivos =
            $datos['motivos_seleccionados']
            ?? [];


        if (!is_array($motivos)) {

            $motivos = [];
        }


        /* =====================================================
        MOTIVOS ACTUALES DEL REPORTE
        ===================================================== */

        $motivosActuales =
            $this->db
            ->table(
                'ai_reporte_motivos'
            )
            ->where(
                'id_reporte',
                $idReporte
            )
            ->where(
                'eliminado',
                0
            )
            ->get()
            ->getResultArray();


        $actualesPorMotivo = [];


        foreach (
            $motivosActuales
            as $motivoActual
        ) {

            $idMotivoActual =
                (int) (
                    $motivoActual['id_motivo']
                    ?? 0
                );


            if ($idMotivoActual <= 0) {

                continue;
            }


            $actualesPorMotivo[$idMotivoActual] = $motivoActual;
        }


        /* =====================================================
        IDS QUE DEBEN PERMANECER
        ===================================================== */

        $idsRecibidos = [];


        foreach (
            $motivos
            as $motivoFormulario
        ) {

            if (!is_array($motivoFormulario)) {

                continue;
            }


            $idMotivo =
                (int) (
                    $motivoFormulario['id_motivo']
                    ?? 0
                );


            if ($idMotivo <= 0) {

                throw new \InvalidArgumentException(
                    'Existe un motivo seleccionado sin identificador válido.'
                );
            }


            if (
                in_array(
                    $idMotivo,
                    $idsRecibidos,
                    true
                )
            ) {

                continue;
            }


            $idsRecibidos[] =
                $idMotivo;


            /* =================================================
            VALIDAR MOTIVO EN CATÁLOGO
            ================================================= */

            $motivoCatalogo =
                $this->db
                ->table(
                    'ai_cat_motivos'
                )
                ->select([
                    'id_motivo',
                    'motivo',
                    'sancion',
                    'activo',
                ])
                ->where(
                    'id_motivo',
                    $idMotivo
                )
                ->where(
                    'activo',
                    1
                )
                ->get()
                ->getRowArray();


            if (!$motivoCatalogo) {

                throw new \InvalidArgumentException(
                    'Uno de los motivos seleccionados ya no está disponible.'
                );
            }


            /* =================================================
            SI EL MOTIVO YA EXISTE, CONSERVARLO
            ================================================= */

            if (
                isset(
                    $actualesPorMotivo[$idMotivo]
                )
            ) {

                $idReporteMotivo =
                    (int) (
                        $actualesPorMotivo[$idMotivo]['id_reporte_motivo']
                        ?? 0
                    );
            } else {

                /* =============================================
                NUEVO MOTIVO
                ============================================= */

                $insertadoMotivo =
                    $this->db
                    ->table(
                        'ai_reporte_motivos'
                    )
                    ->insert([

                        'id_reporte' =>
                        $idReporte,

                        'id_motivo' =>
                        $idMotivo,

                        'created_by' =>
                        $idUsuario,

                        'eliminado' =>
                        0,
                    ]);


                if ($insertadoMotivo === false) {

                    throw new \RuntimeException(
                        'No fue posible guardar uno de los motivos del reporte.'
                    );
                }


                $idReporteMotivo =
                    (int)
                    $this->db
                        ->insertID();
            }


            if ($idReporteMotivo <= 0) {

                throw new \RuntimeException(
                    'No fue posible identificar el motivo registrado.'
                );
            }


            /* =================================================
            SANCIÓN ACTUAL DEL MOTIVO
            ================================================= */

            $sancionActual =
                $this->db
                ->table(
                    'ai_reporte_sanciones'
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->where(
                    'id_reporte_motivo',
                    $idReporteMotivo
                )
                ->where(
                    'eliminado',
                    0
                )
                ->orderBy(
                    'id_sancion',
                    'DESC'
                )
                ->get()
                ->getRowArray();


            /* =================================================
            SIN SANCIONES / BAJA VOLUNTARIA
            ================================================= */

            if (
                $sinSanciones
                || $bajaVoluntaria
            ) {

                if ($sancionActual) {

                    $actualizado =
                        $this->db
                        ->table(
                            'ai_reporte_sanciones'
                        )
                        ->where(
                            'id_sancion',
                            (int)
                            $sancionActual['id_sancion']
                        )
                        ->update([

                            'es_actual' =>
                            0,

                            'eliminado' =>
                            1,

                            'updated_by' =>
                            $idUsuario,

                            'updated_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                            'eliminado_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                            'eliminado_por' =>
                            $idUsuario,
                        ]);


                    if ($actualizado === false) {

                        throw new \RuntimeException(
                            'No fue posible actualizar la sanción relacionada con el motivo.'
                        );
                    }
                }


                continue;
            }


            /* =================================================
            SANCIÓN DEL CATÁLOGO
            ================================================= */

            $tipoSancion =
                trim(
                    (string) (
                        $motivoCatalogo['sancion']
                        ?? ''
                    )
                );


            if ($tipoSancion === '') {

                continue;
            }


            /* =================================================
            FOLIO DE SANCIÓN
            ================================================= */

            $folioSancion =
                trim(
                    (string) (
                        $motivoFormulario['folio_sancion']
                        ?? ''
                    )
                );


            if ($folioSancion === '') {

                $folioSancion = null;
            }


            if (
                $folioSancion !== null
                && mb_strlen(
                    $folioSancion
                ) > 150
            ) {

                throw new \InvalidArgumentException(
                    'El folio de la sanción no puede exceder 150 caracteres.'
                );
            }


            /* =================================================
            ACTUALIZAR SANCIÓN EXISTENTE
            ================================================= */

            if ($sancionActual) {

                $actualizado =
                    $this->db
                    ->table(
                        'ai_reporte_sanciones'
                    )
                    ->where(
                        'id_sancion',
                        (int)
                        $sancionActual['id_sancion']
                    )
                    ->update([

                        'tipo' =>
                        $tipoSancion,

                        'folio_sancion' =>
                        $folioSancion,

                        'es_actual' =>
                        1,

                        'updated_by' =>
                        $idUsuario,

                        'updated_at' =>
                        date(
                            'Y-m-d H:i:s'
                        ),
                    ]);


                if ($actualizado === false) {

                    throw new \RuntimeException(
                        'No fue posible actualizar la sanción relacionada con el motivo.'
                    );
                }
            } else {

                /* =============================================
                CREAR SANCIÓN DEL NUEVO MOTIVO
                ============================================= */

                $insertadoSancion =
                    $this->db
                    ->table(
                        'ai_reporte_sanciones'
                    )
                    ->insert([

                        'id_reporte' =>
                        $idReporte,

                        'id_reporte_motivo' =>
                        $idReporteMotivo,

                        'tipo' =>
                        $tipoSancion,

                        'descripcion_otro' =>
                        null,

                        'folio_sancion' =>
                        $folioSancion,

                        'origen' =>
                        'edicion',

                        'id_seguimiento' =>
                        null,

                        'es_actual' =>
                        1,

                        'created_by' =>
                        $idUsuario,

                        'eliminado' =>
                        0,
                    ]);


                if ($insertadoSancion === false) {

                    throw new \RuntimeException(
                        'No fue posible guardar la sanción relacionada con el motivo.'
                    );
                }
            }
        }


        /* =====================================================
        MOTIVOS QUE FUERON QUITADOS EN EDITAR
        ===================================================== */

        foreach (
            $actualesPorMotivo
            as $idMotivoActual =>
            $motivoActual
        ) {

            if (
                in_array(
                    $idMotivoActual,
                    $idsRecibidos,
                    true
                )
            ) {

                continue;
            }


            $idReporteMotivo =
                (int) (
                    $motivoActual['id_reporte_motivo']
                    ?? 0
                );


            if ($idReporteMotivo <= 0) {

                continue;
            }


            /* =================================================
            ELIMINAR LÓGICAMENTE SUS SANCIONES
            ================================================= */

            $this->db
                ->table(
                    'ai_reporte_sanciones'
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->where(
                    'id_reporte_motivo',
                    $idReporteMotivo
                )
                ->where(
                    'eliminado',
                    0
                )
                ->update([

                    'es_actual' =>
                    0,

                    'eliminado' =>
                    1,

                    'updated_by' =>
                    $idUsuario,

                    'updated_at' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

                    'eliminado_at' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

                    'eliminado_por' =>
                    $idUsuario,
                ]);


            /* =================================================
            ELIMINAR LÓGICAMENTE EL MOTIVO
            ================================================= */

            $eliminadoMotivo =
                $this->db
                ->table(
                    'ai_reporte_motivos'
                )
                ->where(
                    'id_reporte_motivo',
                    $idReporteMotivo
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->update([

                    'eliminado' =>
                    1,
                ]);


            if ($eliminadoMotivo === false) {

                throw new \RuntimeException(
                    'No fue posible eliminar uno de los motivos del reporte.'
                );
            }
        }
    }


    /* =========================================================
    CORREGIR SANCIÓN DESDE EDITAR
    ========================================================= */

    protected function corregirSancionDesdeEdicion(
        int $idReporte,
        array $datos,
        int $idUsuario
    ): void {

        /* =====================================================
        ¿REALMENTE FUE MODIFICADA?
        ===================================================== */

        $modificada =
            trim(
                (string) (
                    $datos['sancion_modificada']
                    ?? '0'
                )
            );


        if ($modificada !== '1') {

            return;
        }


        /* =====================================================
        VALIDAR ORIGEN DEL CAMBIO
        ===================================================== */

        $origenCambio =
            trim(
                (string) (
                    $datos['sancion_origen_cambio']
                    ?? ''
                )
            );


        if ($origenCambio !== 'edicion') {

            throw new \InvalidArgumentException(
                'El origen de la modificación de la sanción no es válido.'
            );
        }


        /* =====================================================
        VALORES NUEVOS
        ===================================================== */

        $tipo =
            trim(
                (string) (
                    $datos['sancion_disciplinaria']
                    ?? ''
                )
            );


        $descripcionOtro =
            trim(
                (string) (
                    $datos['sancion_otro']
                    ?? ''
                )
            );


        /* =====================================================
        SANCIÓN ACTUAL
        ===================================================== */

        $sancionActual =
            $this->db
            ->table(
                'ai_reporte_sanciones'
            )
            ->where(
                'id_reporte',
                $idReporte
            )
            ->where(
                'es_actual',
                1
            )
            ->where(
                'eliminado',
                0
            )
            ->orderBy(
                'id_sancion',
                'DESC'
            )
            ->get()
            ->getRowArray();


        /* =====================================================
        SIN SANCIÓN

        Si antes existía una sanción pero el usuario
        confirma que fue una captura incorrecta,
        la retiramos como sanción vigente.
        ===================================================== */

        if ($tipo === '') {

            if (!$sancionActual) {

                return;
            }


            $actualizado =
                $this->db
                ->table(
                    'ai_reporte_sanciones'
                )
                ->where(
                    'id_sancion',
                    (int) $sancionActual['id_sancion']
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->update([

                    'es_actual' =>
                    0,

                    'updated_by' =>
                    $idUsuario,

                    'updated_at' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

                    /*
                 * Al tratarse de una corrección que elimina
                 * una sanción capturada por error, dejamos
                 * el registro como eliminado lógicamente.
                 */

                    'eliminado' =>
                    1,

                    'eliminado_at' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

                    'eliminado_por' =>
                    $idUsuario,

                ]);


            if ($actualizado === false) {

                throw new \RuntimeException(
                    'No fue posible retirar la sanción disciplinaria.'
                );
            }


            return;
        }


        /* =====================================================
        VALIDAR CATÁLOGO
        ===================================================== */

        $tiposPermitidos = [
            'Arresto',
            'Amonestación',
            'Otro',
        ];


        if (
            !in_array(
                $tipo,
                $tiposPermitidos,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'La sanción disciplinaria seleccionada no es válida.'
            );
        }


        /* =====================================================
        VALIDAR "OTRO"
        ===================================================== */

        if ($tipo === 'Otro') {

            if ($descripcionOtro === '') {

                throw new \InvalidArgumentException(
                    'Debes especificar la sanción disciplinaria.'
                );
            }


            if (
                mb_strlen(
                    $descripcionOtro
                ) > 255
            ) {

                throw new \InvalidArgumentException(
                    'La descripción de la sanción no puede exceder 255 caracteres.'
                );
            }
        } else {

            /*
         * Arresto y Amonestación jamás deben conservar
         * texto residual de "Otro".
         */

            $descripcionOtro =
                null;
        }


        /* =====================================================
        NO EXISTÍA SANCIÓN

        Por ejemplo:

        Sin sanción
                ↓ corrección
        Arresto

        Como no existe fila anterior, creamos una.
        ===================================================== */

        if (!$sancionActual) {

            $insertado =
                $this->db
                ->table(
                    'ai_reporte_sanciones'
                )
                ->insert([

                    'id_reporte' =>
                    $idReporte,

                    'tipo' =>
                    $tipo,

                    'descripcion_otro' =>
                    $descripcionOtro,

                    'origen' =>
                    'edicion',

                    'id_seguimiento' =>
                    null,

                    'es_actual' =>
                    1,

                    'created_by' =>
                    $idUsuario,

                    'eliminado' =>
                    0,

                ]);


            if ($insertado === false) {

                throw new \RuntimeException(
                    'No fue posible registrar la sanción disciplinaria.'
                );
            }


            return;
        }


        /* =====================================================
        YA EXISTÍA SANCIÓN

        Es una CORRECCIÓN, no un acontecimiento nuevo.

        Por lo tanto actualizamos la misma fila.
        ===================================================== */

        $datosActualizacion = [

            'tipo' =>
            $tipo,

            'descripcion_otro' =>
            $descripcionOtro,

            'updated_by' =>
            $idUsuario,

            'updated_at' =>
            date(
                'Y-m-d H:i:s'
            ),

        ];


        /*
     * MUY IMPORTANTE:
     *
     * NO cambiamos "origen".
     *
     * Si la sanción originalmente nació en Seguimiento,
     * debe continuar indicando que provino de Seguimiento.
     *
     * Editar solamente está corrigiendo el contenido.
     */


        $actualizado =
            $this->db
            ->table(
                'ai_reporte_sanciones'
            )
            ->where(
                'id_sancion',
                (int) $sancionActual['id_sancion']
            )
            ->where(
                'id_reporte',
                $idReporte
            )
            ->where(
                'es_actual',
                1
            )
            ->where(
                'eliminado',
                0
            )
            ->update(
                $datosActualizacion
            );


        if ($actualizado === false) {

            throw new \RuntimeException(
                'No fue posible corregir la sanción disciplinaria.'
            );
        }
    }


    /* =========================================================
    UNIDADES
    ========================================================= */

    protected function guardarUnidades(
        int $idReporte,
        array $unidades,
        string $modalidadUnidad
    ): void {

        /* =====================================================
        SIN UNIDAD / OFICINA
        ===================================================== */

        if (
            $modalidadUnidad ===
            'SIN_UNIDAD_OFICINA'
        ) {

            /*
            * El usuario indicó expresamente que el personal
            * involucrado no cuenta con una unidad vehicular.
            *
            * Por lo tanto:
            *
            * - no exigimos unidades;
            * - no insertamos unidades ficticias;
            * - no utilizamos parque_vehicular_id = 0.
            */

            return;
        }


        /* =====================================================
        CON UNIDAD
        ===================================================== */

        if (
            $modalidadUnidad !==
            'CON_UNIDAD'
        ) {

            throw new \InvalidArgumentException(
                'La modalidad de unidad seleccionada no es válida.'
            );
        }


        if (
            empty($unidades)
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
    GUARDAR DIRECCIÓN PARA NOTIFICACIÓN
    ========================================================= */

    protected function guardarDireccionNotificacion(
        int $idReporte,
        array $datos,
        int $idUsuario
    ): void {

        /* =====================================================
        VALIDAR IDENTIFICADORES
        ===================================================== */

        if (
            $idReporte <= 0
            || $idUsuario <= 0
        ) {

            throw new \RuntimeException(
                'No fue posible identificar el reporte o usuario para la dirección de notificación.'
            );
        }


        /* =====================================================
        PERTENECE A NEZAHUALCÓYOTL
        ===================================================== */

        $perteneceNeza =
            $datos['notificacion_pertenece_neza']
            ?? null;


        if (
            $perteneceNeza !== null
            && $perteneceNeza !== ''
        ) {

            $perteneceNeza =
                (int) $perteneceNeza;


            if (
                !in_array(
                    $perteneceNeza,
                    [
                        0,
                        1,
                    ],
                    true
                )
            ) {

                throw new \InvalidArgumentException(
                    'La opción de pertenencia al municipio de Nezahualcóyotl no es válida.'
                );
            }
        } else {

            $perteneceNeza =
                null;
        }


        /* =====================================================
        CALLE
        ===================================================== */

        $calle =
            $this->valorNullable(
                $datos['notificacion_calle']
                    ?? null
            );


        /* =====================================================
        NÚMERO EXTERIOR
        ===================================================== */

        $numeroExterior =
            $this->valorNullable(
                $datos['notificacion_numero_exterior']
                    ?? null
            );


        /* =====================================================
        COLONIA
        ===================================================== */

        $colonia =
            $this->valorNullable(
                $datos['notificacion_colonia']
                    ?? null
            );


        /* =====================================================
        ENTRE CALLE
        ===================================================== */

        $entreCalle =
            $this->valorNullable(
                $datos['notificacion_entre_calle']
                    ?? null
            );


        /* =====================================================
        Y CALLE
        ===================================================== */

        $yCalle =
            $this->valorNullable(
                $datos['notificacion_y_calle']
                    ?? null
            );


        /* =====================================================
        MUNICIPIO
        ===================================================== */

        $municipio =
            $this->valorNullable(
                $datos['notificacion_municipio']
                    ?? null
            );


        /* =====================================================
        ESTADO
        ===================================================== */

        $estado =
            $this->valorNullable(
                $datos['notificacion_estado']
                    ?? null
            );


        /* =====================================================
        SECTOR
        ===================================================== */

        $sector =
            $this->valorNullable(
                $datos['notificacion_sector']
                    ?? null
            );


        /* =====================================================
        CUADRANTE
        ===================================================== */

        $cuadrante =
            $this->valorNullable(
                $datos['notificacion_cuadrante']
                    ?? null
            );


        /* =====================================================
        ID CUADRA
        ===================================================== */

        $idCuadra =
            $this->valorNullable(
                $datos['notificacion_id_cuadra']
                    ?? null
            );


        /* =====================================================
        LATITUD
        ===================================================== */

        $latitud =
            $this->decimalNullable(
                $datos['notificacion_latitud']
                    ?? null
            );


        /* =====================================================
        LONGITUD
        ===================================================== */

        $longitud =
            $this->decimalNullable(
                $datos['notificacion_longitud']
                    ?? null
            );


        /* =====================================================
        ORIGEN DE UBICACIÓN
        ===================================================== */

        $origenUbicacion =
            $this->normalizarOrigenUbicacion(
                $datos['notificacion_origen_ubicacion']
                    ?? null
            );


        /* =====================================================
        VERIFICAR SI REALMENTE EXISTEN DATOS

        La dirección para notificación es opcional.

        Si el usuario no seleccionó Sí/No y tampoco capturó
        ningún dato de dirección, no insertamos una fila.
        ===================================================== */

        $tieneDatos =
            $perteneceNeza !== null
            || $calle !== null
            || $numeroExterior !== null
            || $colonia !== null
            || $entreCalle !== null
            || $yCalle !== null
            || $municipio !== null
            || $estado !== null
            || $sector !== null
            || $cuadrante !== null
            || $idCuadra !== null
            || $latitud !== null
            || $longitud !== null;


        if (!$tieneDatos) {

            return;
        }


        /* =====================================================
        GUARDAR DIRECCIÓN
        ===================================================== */

        $insertado =
            $this->db
            ->table(
                'ai_reporte_direccion_notificacion'
            )
            ->insert([

                'id_reporte' =>
                $idReporte,

                'pertenece_neza' =>
                $perteneceNeza,

                'calle' =>
                $calle,

                'numero_exterior' =>
                $numeroExterior,

                'colonia' =>
                $colonia,

                'entre_calle' =>
                $entreCalle,

                'y_calle' =>
                $yCalle,

                'municipio' =>
                $municipio,

                'estado' =>
                $estado,

                'sector' =>
                $sector,

                'cuadrante' =>
                $cuadrante,

                'id_cuadra' =>
                $idCuadra,

                'latitud' =>
                $latitud,

                'longitud' =>
                $longitud,

                'origen_ubicacion' =>
                $origenUbicacion,

                'created_by' =>
                $idUsuario,

                'eliminado' =>
                0,

            ]);


        if ($insertado === false) {

            throw new \RuntimeException(
                'No fue posible guardar la dirección para notificación.'
            );
        }
    }


    /* =========================================================
    ACTUALIZAR DIRECCIÓN PARA NOTIFICACIÓN
    ========================================================= */

    protected function actualizarDireccionNotificacion(
        int $idReporte,
        array $datos,
        int $idUsuario
    ): void {

        if (
            $idReporte <= 0
            || $idUsuario <= 0
        ) {

            throw new \RuntimeException(
                'No fue posible identificar el reporte o usuario para actualizar la dirección de notificación.'
            );
        }


        /* =====================================================
        PERTENECE A NEZAHUALCÓYOTL
        ===================================================== */

        $perteneceNeza =
            $datos['notificacion_pertenece_neza']
            ?? null;


        if (
            $perteneceNeza !== null
            && $perteneceNeza !== ''
        ) {

            $perteneceNeza =
                (int) $perteneceNeza;


            if (
                !in_array(
                    $perteneceNeza,
                    [
                        0,
                        1,
                    ],
                    true
                )
            ) {

                throw new \InvalidArgumentException(
                    'La opción de pertenencia al municipio de Nezahualcóyotl no es válida.'
                );
            }
        } else {

            $perteneceNeza =
                null;
        }


        /* =====================================================
        DATOS DE DIRECCIÓN
        ===================================================== */

        $calle =
            $this->valorNullable(
                $datos['notificacion_calle']
                    ?? null
            );


        $numeroExterior =
            $this->valorNullable(
                $datos['notificacion_numero_exterior']
                    ?? null
            );


        $colonia =
            $this->valorNullable(
                $datos['notificacion_colonia']
                    ?? null
            );


        $entreCalle =
            $this->valorNullable(
                $datos['notificacion_entre_calle']
                    ?? null
            );


        $yCalle =
            $this->valorNullable(
                $datos['notificacion_y_calle']
                    ?? null
            );


        $municipio =
            $this->valorNullable(
                $datos['notificacion_municipio']
                    ?? null
            );


        $estado =
            $this->valorNullable(
                $datos['notificacion_estado']
                    ?? null
            );


        $sector =
            $this->valorNullable(
                $datos['notificacion_sector']
                    ?? null
            );


        $cuadrante =
            $this->valorNullable(
                $datos['notificacion_cuadrante']
                    ?? null
            );


        $idCuadra =
            $this->valorNullable(
                $datos['notificacion_id_cuadra']
                    ?? null
            );


        $latitud =
            $this->decimalNullable(
                $datos['notificacion_latitud']
                    ?? null
            );


        $longitud =
            $this->decimalNullable(
                $datos['notificacion_longitud']
                    ?? null
            );


        $origenUbicacion =
            $this->normalizarOrigenUbicacion(
                $datos['notificacion_origen_ubicacion']
                    ?? null
            );


        /* =====================================================
        ¿HAY INFORMACIÓN CAPTURADA?
        ===================================================== */

        $tieneDatos =
            $perteneceNeza !== null
            || $calle !== null
            || $numeroExterior !== null
            || $colonia !== null
            || $entreCalle !== null
            || $yCalle !== null
            || $municipio !== null
            || $estado !== null
            || $sector !== null
            || $cuadrante !== null
            || $idCuadra !== null
            || $latitud !== null
            || $longitud !== null;


        /* =====================================================
        BUSCAR REGISTRO EXISTENTE

        Incluimos incluso registros eliminados porque existe
        UNIQUE(id_reporte).

        Si en algún momento se eliminó lógicamente la dirección
        y posteriormente vuelven a capturarla, reutilizamos
        la misma fila.
        ===================================================== */

        $direccionActual =
            $this->db
            ->table(
                'ai_reporte_direccion_notificacion'
            )
            ->where(
                'id_reporte',
                $idReporte
            )
            ->get()
            ->getRowArray();


        /* =====================================================
        SECCIÓN COMPLETAMENTE VACÍA
        ===================================================== */

        if (!$tieneDatos) {

            /*
            * Si nunca existió una dirección,
            * no tenemos nada que hacer.
            */

            if (!$direccionActual) {

                return;
            }


            /*
            * Si existía, la retiramos lógicamente.
            */

            $actualizado =
                $this->db
                ->table(
                    'ai_reporte_direccion_notificacion'
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->update([

                    'eliminado' =>
                    1,

                    'eliminado_at' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

                    'eliminado_por' =>
                    $idUsuario,

                    'updated_by' =>
                    $idUsuario,

                ]);


            if ($actualizado === false) {

                throw new \RuntimeException(
                    'No fue posible retirar la dirección para notificación.'
                );
            }


            return;
        }


        /* =====================================================
        DATOS A GUARDAR
        ===================================================== */

        $datosDireccion = [

            'pertenece_neza' =>
            $perteneceNeza,

            'calle' =>
            $calle,

            'numero_exterior' =>
            $numeroExterior,

            'colonia' =>
            $colonia,

            'entre_calle' =>
            $entreCalle,

            'y_calle' =>
            $yCalle,

            'municipio' =>
            $municipio,

            'estado' =>
            $estado,

            'sector' =>
            $sector,

            'cuadrante' =>
            $cuadrante,

            'id_cuadra' =>
            $idCuadra,

            'latitud' =>
            $latitud,

            'longitud' =>
            $longitud,

            'origen_ubicacion' =>
            $origenUbicacion,

            'updated_by' =>
            $idUsuario,

            /*
            * Si estaba eliminada y vuelven a capturar
            * una dirección, la reactivamos.
            */

            'eliminado' =>
            0,

            'eliminado_at' =>
            null,

            'eliminado_por' =>
            null,

        ];


        /* =====================================================
        YA EXISTE
        ===================================================== */

        if ($direccionActual) {

            $actualizado =
                $this->db
                ->table(
                    'ai_reporte_direccion_notificacion'
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->update(
                    $datosDireccion
                );


            if ($actualizado === false) {

                throw new \RuntimeException(
                    'No fue posible actualizar la dirección para notificación.'
                );
            }


            return;
        }


        /* =====================================================
        NO EXISTE TODAVÍA
        ===================================================== */

        $datosDireccion['id_reporte'] =
            $idReporte;


        $datosDireccion['created_by'] =
            $idUsuario;


        $insertado =
            $this->db
            ->table(
                'ai_reporte_direccion_notificacion'
            )
            ->insert(
                $datosDireccion
            );


        if ($insertado === false) {

            throw new \RuntimeException(
                'No fue posible registrar la dirección para notificación.'
            );
        }
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
            empty($archivos)
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
                strtolower(
                    trim(
                        (string)
                        $archivo->getMimeType()
                    )
                );


            $mimesPermitidos = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];


            if (
                !in_array(
                    $mime,
                    $mimesPermitidos,
                    true
                )
            ) {

                throw new \InvalidArgumentException(
                    'Las evidencias deben ser imágenes JPG, JPEG, PNG o WEBP.'
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
    MARCAR EVIDENCIAS COMO ELIMINADAS
    ========================================================= */

    protected function marcarEvidenciasEliminadas(
        int $idReporte,
        array $evidencias,
        int $idUsuario
    ): void {

        if (empty($evidencias)) {
            return;
        }


        $ids =
            [];


        foreach (
            $evidencias
            as $idEvidencia
        ) {

            $idEvidencia =
                (int) $idEvidencia;


            if (
                $idEvidencia <= 0
                || in_array(
                    $idEvidencia,
                    $ids,
                    true
                )
            ) {
                continue;
            }


            $ids[] =
                $idEvidencia;
        }


        if (empty($ids)) {
            return;
        }


        /*
     * IMPORTANTE:
     *
     * Además de comprobar el id_evidencia,
     * comprobamos id_reporte.
     *
     * Así un reporte no puede marcar como
     * eliminada una evidencia perteneciente
     * a otro reporte.
     */

        $actualizado =
            $this->db
            ->table(
                'ai_reporte_evidencias'
            )
            ->where(
                'id_reporte',
                $idReporte
            )
            ->whereIn(
                'id_evidencia',
                $ids
            )
            ->where(
                'eliminado',
                0
            )
            ->update([

                'eliminado' =>
                1,

                'eliminado_at' =>
                date(
                    'Y-m-d H:i:s'
                ),

                'eliminado_por' =>
                $idUsuario,

            ]);


        if ($actualizado === false) {

            throw new \RuntimeException(
                'No fue posible actualizar las evidencias eliminadas.'
            );
        }
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
    MODALIDAD DE UNIDAD
    ========================================================= */

    protected function normalizarModalidadUnidad(
        mixed $valor
    ): string {

        $modalidad =
            strtoupper(
                trim(
                    (string)
                    ($valor ?? '')
                )
            );


        $permitidas = [
            'CON_UNIDAD',
            'SIN_UNIDAD_OFICINA',
        ];


        if (
            !in_array(
                $modalidad,
                $permitidas,
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'La modalidad de unidad seleccionada no es válida.'
            );
        }


        return $modalidad;
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
    VALIDAR FOLIOS IP / IMP ÚNICOS
    ========================================================= */

    private function validarFoliosUnicos(
        array $datosReporte,
        ?int $idReporteExcluir = null
    ): void {

        /* =====================================================
        FOLIO IP
        ===================================================== */

        $folioIp =
            trim(
                (string) (
                    $datosReporte['folio_ip']
                    ?? ''
                )
            );


        if ($folioIp !== '') {

            $builder =
                $this->db
                ->table(
                    'ai_reportes'
                )
                ->select(
                    'id_reporte'
                )
                ->where(
                    'folio_ip',
                    $folioIp
                )
                ->where(
                    'eliminado',
                    0
                );


            if (
                $idReporteExcluir !== null
                && $idReporteExcluir > 0
            ) {

                $builder->where(
                    'id_reporte !=',
                    $idReporteExcluir
                );
            }


            $existe =
                $builder
                ->limit(1)
                ->get()
                ->getRowArray();


            if ($existe) {

                throw new \InvalidArgumentException(
                    'El Folio IP ya se encuentra registrado. Debes ingresar uno diferente.'
                );
            }
        }


        /* =====================================================
        FOLIO IMP
        ===================================================== */

        $folioImp =
            trim(
                (string) (
                    $datosReporte['folio_imp']
                    ?? ''
                )
            );


        if ($folioImp !== '') {

            $builder =
                $this->db
                ->table(
                    'ai_reportes'
                )
                ->select(
                    'id_reporte'
                )
                ->where(
                    'folio_imp',
                    $folioImp
                )
                ->where(
                    'eliminado',
                    0
                );


            if (
                $idReporteExcluir !== null
                && $idReporteExcluir > 0
            ) {

                $builder->where(
                    'id_reporte !=',
                    $idReporteExcluir
                );
            }


            $existe =
                $builder
                ->limit(1)
                ->get()
                ->getRowArray();


            if ($existe) {

                throw new \InvalidArgumentException(
                    'El Folio IMP ya se encuentra registrado. Debes ingresar uno diferente.'
                );
            }
        }
    }
}
