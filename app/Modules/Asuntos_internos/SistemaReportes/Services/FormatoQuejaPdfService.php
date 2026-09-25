<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class FormatoQuejaPdfService
{
    /* =========================================================
       GENERAR PDF
    ========================================================= */

    public function generar(
        int $idReporte
    ): array {

        if (
            $idReporte <= 0
        ) {

            throw new \InvalidArgumentException(
                'El reporte solicitado no es válido.'
            );
        }


        /* =====================================================
           CONSULTAR DATOS
        ===================================================== */

        $datos =
            $this->consultarDatos(
                $idReporte
            );


        $folio =
            $this->texto(
                $datos['reporte']['folio']
                ?? ''
            );


        /* =====================================================
           GENERAR HTML
        ===================================================== */

        $html =
            view(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\pdf\queja',
                [
                    'datos' =>
                        $datos,

                    'assets' =>
                        $this->obtenerAssets(),
                ]
            );


        /* =====================================================
           DIRECTORIO DE EXPORTACIÓN
        ===================================================== */

        $directorio =
            WRITEPATH
            . 'exports/pdf/';


        if (
            !is_dir(
                $directorio
            )
        ) {

            $creado =
                mkdir(
                    $directorio,
                    0775,
                    true
                );


            if (
                !$creado
                && !is_dir(
                    $directorio
                )
            ) {

                throw new \RuntimeException(
                    'No fue posible crear el directorio de PDF.'
                );
            }
        }


        /* =====================================================
           NOMBRE DEL ARCHIVO
        ===================================================== */

        $nombreArchivo =
            'QUEJA_'
            . preg_replace(
                '/[^A-Za-z0-9_-]+/',
                '_',
                $folio !== ''
                    ? $folio
                    : (string) $idReporte
            )
            . '.pdf';


        $ruta =
            $directorio
            . date(
                'Ymd_His'
            )
            . '_'
            . uniqid(
                '',
                false
            )
            . '_'
            . $nombreArchivo;


        /* =====================================================
           CONFIGURAR DOMPDF
        ===================================================== */

        $options =
            new Options();


        $options->set(
            'isRemoteEnabled',
            false
        );


        $options->set(
            'isHtml5ParserEnabled',
            true
        );


        $options->set(
            'tempDir',
            WRITEPATH
            . 'cache'
        );


        $dompdf =
            new Dompdf(
                $options
            );


        $dompdf->loadHtml(
            $html,
            'UTF-8'
        );


        $dompdf->setPaper(
            'letter',
            'portrait'
        );


        $dompdf->render();


        /* =====================================================
           GUARDAR PDF
        ===================================================== */

        $resultadoGuardado =
            file_put_contents(
                $ruta,
                $dompdf->output()
            );


        if (
            $resultadoGuardado === false
        ) {

            throw new \RuntimeException(
                'No fue posible guardar el archivo PDF.'
            );
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [
            'ruta' =>
                $ruta,

            'nombre' =>
                $nombreArchivo,

            'folio' =>
                $folio,
        ];
    }


    /* =========================================================
       CONSULTAR DATOS DEL REPORTE
    ========================================================= */

    private function consultarDatos(
        int $idReporte
    ): array {

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =====================================================
           REPORTE
        ===================================================== */

        $reporte =
            $db
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


        if (
            !$reporte
        ) {

            throw new \RuntimeException(
                'El reporte no existe.'
            );
        }


        /* =====================================================
           TIPO DE FOLIO
        ===================================================== */

        $tipoFolio =
            $this->obtenerTipoFolio(
                $reporte['folio']
                ?? ''
            );


        if (
            !in_array(
                $tipoFolio,
                [
                    'QJ',
                    'QJF',
                    'QJV',
                ],
                true
            )
        ) {

            throw new \RuntimeException(
                'El PDF de queja solo está disponible para QJ, QJF y QJV.'
            );
        }


        /* =====================================================
           DIRECCIÓN PARA NOTIFICACIÓN
        ===================================================== */

        $direccionNotificacion =
            $db
            ->table(
                'ai_reporte_direccion_notificacion'
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
            ->getRowArray()
            ?? [];


        /* =====================================================
           PERSONAL
        ===================================================== */

        $personal =
            $db
            ->table(
                'ai_reporte_personal'
            )
            ->select([
                'id_reporte_personal',
                'perscod',
                'nombre_snapshot',
                'area_snapshot',
                'turno_snapshot',
                'alias_snapshot',
            ])
            ->where(
                'id_reporte',
                $idReporte
            )
            ->orderBy(
                'id_reporte_personal',
                'ASC'
            )
            ->get()
            ->getResultArray();


        /* =====================================================
           UNIDADES
        ===================================================== */

        $unidades =
            $db
            ->table(
                'ai_reporte_unidades'
            )
            ->select([
                'id_reporte_unidad',
                'no_economico_snapshot',
                'placas_snapshot',
                'marca_snapshot',
                'submarca_snapshot',
                'color_snapshot',
                'estatus_snapshot',
                'servicio_snapshot',
                'tipo_snapshot',
            ])
            ->where(
                'id_reporte',
                $idReporte
            )
            ->orderBy(
                'id_reporte_unidad',
                'ASC'
            )
            ->get()
            ->getResultArray();


        /* =====================================================
           EVIDENCIAS
        ===================================================== */

        $evidencias =
            $db
            ->table(
                'ai_reporte_evidencias'
            )
            ->select([
                'id_evidencia',
                'tipo_evidencia',
                'nombre_original',
                'nombre_archivo',
                'ruta_archivo',
                'extension',
                'mime_type',
                'tamano_bytes',
                'contenido',
                'almacenamiento',
                'orden',
                'created_at',
            ])
            ->where(
                'id_reporte',
                $idReporte
            )
            ->where(
                'eliminado',
                0
            )
            ->orderBy(
                'orden',
                'ASC'
            )
            ->orderBy(
                'id_evidencia',
                'ASC'
            )
            ->get()
            ->getResultArray();


        /* =====================================================
           PREPARAR EVIDENCIAS PARA DOMPDF
        ===================================================== */

        $evidencias =
            $this->prepararEvidenciasPdf(
                $evidencias
            );


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [
            'reporte' =>
                $reporte,

            'tipo_folio' =>
                $tipoFolio,

            'direccion_notificacion' =>
                $direccionNotificacion,

            'personal' =>
                $personal,

            'unidades' =>
                $unidades,

            'evidencias' =>
                $evidencias,
        ];
    }


    /* =========================================================
    PREPARAR EVIDENCIAS PARA PDF
    ========================================================= */

    private function prepararEvidenciasPdf(
        array $evidencias
    ): array {

        $resultado = [];


        foreach (
            $evidencias
            as $evidencia
        ) {

            $tipoEvidencia =
                strtoupper(
                    $this->texto(
                        $evidencia['tipo_evidencia']
                        ?? ''
                    )
                );


            $almacenamiento =
                strtoupper(
                    $this->texto(
                        $evidencia['almacenamiento']
                        ?? ''
                    )
                );


            $mimeType =
                strtolower(
                    $this->texto(
                        $evidencia['mime_type']
                        ?? ''
                    )
                );


            $extension =
                strtolower(
                    ltrim(
                        $this->texto(
                            $evidencia['extension']
                            ?? ''
                        ),
                        '.'
                    )
                );


            /* =====================================================
            IDENTIFICAR SI ES IMAGEN

            Registros nuevos:
            tipo_evidencia = IMAGEN

            Registros anteriores:
            mime_type = image/...
            o extensión de imagen
            ===================================================== */

            $esImagen =
                $tipoEvidencia === 'IMAGEN'
                || str_starts_with(
                    $mimeType,
                    'image/'
                )
                || in_array(
                    $extension,
                    [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                        'webp',
                    ],
                    true
                );


            /*
            * Normalizamos el tipo únicamente para los datos
            * que recibe el PDF.
            *
            * No modifica la base de datos.
            */
            if (
                $tipoEvidencia === ''
                && $esImagen
            ) {

                $tipoEvidencia =
                    'IMAGEN';

                $evidencia['tipo_evidencia'] =
                    'IMAGEN';
            }


            $dataUri =
                '';


            /* =====================================================
            PREPARAR IMAGEN
            ===================================================== */

            if (
                $esImagen
            ) {

                /* =================================================
                ALMACENAMIENTO EN BD
                ================================================= */

                if (
                    $almacenamiento === 'BD'
                ) {

                    $contenido =
                        $evidencia['contenido']
                        ?? null;


                    if (
                        $contenido !== null
                        && $contenido !== ''
                    ) {

                        $dataUri =
                            $this->crearDataUriEvidencia(
                                $contenido,
                                $mimeType
                            );
                    }
                }


                /* =================================================
                ALMACENAMIENTO COMO ARCHIVO
                ================================================= */

                if (
                    $almacenamiento === 'ARCHIVO'
                ) {

                    $rutaArchivo =
                        $this->resolverRutaEvidencia(
                            $evidencia
                        );


                    if (
                        $rutaArchivo !== ''
                        && is_file(
                            $rutaArchivo
                        )
                    ) {

                        $contenido =
                            file_get_contents(
                                $rutaArchivo
                            );


                        if (
                            $contenido !== false
                        ) {

                            $dataUri =
                                $this->crearDataUriEvidencia(
                                    $contenido,
                                    $mimeType
                                );
                        }
                    }
                }
            }


            /* =====================================================
            NO ENVIAR BLOB COMPLETO A LA VISTA
            ===================================================== */

            unset(
                $evidencia['contenido']
            );


            $evidencia['data_uri'] =
                $dataUri;


            $evidencia['es_imagen'] =
                $esImagen;


            $resultado[] =
                $evidencia;
        }


        return $resultado;
    }


    /* =========================================================
    RESOLVER RUTA FÍSICA DE EVIDENCIA
    ========================================================= */

    private function resolverRutaEvidencia(
        array $evidencia
    ): string {

        $ruta =
            $this->texto(
                $evidencia['ruta_archivo']
                ?? ''
            );


        if (
            $ruta === ''
        ) {

            return '';
        }


        /* =====================================================
        NORMALIZAR DIAGONALES
        ===================================================== */

        $rutaNormalizada =
            str_replace(
                '\\',
                '/',
                $ruta
            );


        /* =====================================================
        1. LA RUTA YA ES ABSOLUTA Y EXISTE
        ===================================================== */

        if (
            is_file(
                $rutaNormalizada
            )
        ) {

            return $rutaNormalizada;
        }


        /* =====================================================
        2. RUTA GUARDADA DESDE LA RAÍZ DEL PROYECTO

        Ejemplo real de tu BD:

        writable/uploads/asuntos_internos/reportes/3/archivo.jpg

        ROOTPATH ya apunta a la raíz de DataCore.
        ===================================================== */

        $rutaProyecto =
            ROOTPATH
            . ltrim(
                $rutaNormalizada,
                '/'
            );


        if (
            is_file(
                $rutaProyecto
            )
        ) {

            return $rutaProyecto;
        }


        /* =====================================================
        3. RUTA RELATIVA A WRITABLE

        Este caso sirve si en algún registro se guarda:

        uploads/asuntos_internos/reportes/3/archivo.jpg
        ===================================================== */

        $rutaSinWritable =
            preg_replace(
                '#^writable/#i',
                '',
                ltrim(
                    $rutaNormalizada,
                    '/'
                )
            );


        $rutaWritable =
            WRITEPATH
            . $rutaSinWritable;


        if (
            is_file(
                $rutaWritable
            )
        ) {

            return $rutaWritable;
        }


        /* =====================================================
        4. RUTA RELATIVA A PUBLIC
        ===================================================== */

        $rutaPublica =
            FCPATH
            . ltrim(
                $rutaNormalizada,
                '/'
            );


        if (
            is_file(
                $rutaPublica
            )
        ) {

            return $rutaPublica;
        }


        return '';
    }

    /* =========================================================
       CREAR DATA URI PARA EVIDENCIA
    ========================================================= */

    private function crearDataUriEvidencia(
        mixed $contenido,
        string $mimeType
    ): string {

        if (
            $contenido === null
            || $contenido === ''
        ) {

            return '';
        }


        $mimeType =
            trim(
                $mimeType
            );


        /* =====================================================
           VALIDAR MIME
        ===================================================== */

        if (
            $mimeType === ''
            || !str_starts_with(
                strtolower(
                    $mimeType
                ),
                'image/'
            )
        ) {

            $mimeType =
                'image/jpeg';
        }


        return
            'data:'
            . $mimeType
            . ';base64,'
            . base64_encode(
                $contenido
            );
    }


    /* =========================================================
       ASSETS INSTITUCIONALES
    ========================================================= */

    private function obtenerAssets(): array
    {
        $base =
            FCPATH
            . 'assets/asuntos_internos/sistema_reportes/img/pdf/quejas/';


        return [
            'header' =>
                $this->dataUri(
                    $base
                    . 'header.png'
                ),

            'watermark' =>
                $this->dataUri(
                    $base
                    . 'watermark.png'
                ),

            'footer' =>
                $this->dataUri(
                    $base
                    . 'footer.png'
                ),
        ];
    }


    /* =========================================================
       CONVERTIR ASSET A DATA URI
    ========================================================= */

    private function dataUri(
        string $ruta
    ): string {

        if (
            !is_file(
                $ruta
            )
        ) {

            return '';
        }


        $contenido =
            file_get_contents(
                $ruta
            );


        if (
            $contenido === false
        ) {

            return '';
        }


        return
            'data:image/png;base64,'
            . base64_encode(
                $contenido
            );
    }


    /* =========================================================
       OBTENER TIPO DE FOLIO
    ========================================================= */

    private function obtenerTipoFolio(
        mixed $folio
    ): string {

        $folio =
            strtoupper(
                $this->texto(
                    $folio
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
                    $tipo
                    . '-'
                )
            ) {

                return $tipo;
            }
        }


        return '';
    }


    /* =========================================================
       NORMALIZAR TEXTO
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
}