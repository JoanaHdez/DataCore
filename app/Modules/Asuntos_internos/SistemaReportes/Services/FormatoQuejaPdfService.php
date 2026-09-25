<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class FormatoQuejaPdfService
{
    public function generar(
        int $idReporte
    ): array {

        if (
            $idReporte <= 0
        ) {

            throw new \InvalidArgumentException(
                'El reporte solicitado no es valido.'
            );
        }


        $datos =
            $this->consultarDatos(
                $idReporte
            );


        $folio =
            $this->texto(
                $datos['reporte']['folio']
                ?? ''
            );


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


        file_put_contents(
            $ruta,
            $dompdf->output()
        );


        return [
            'ruta' =>
                $ruta,

            'nombre' =>
                $nombreArchivo,

            'folio' =>
                $folio,
        ];
    }


    private function consultarDatos(
        int $idReporte
    ): array {

        $db =
            \Config\Database::connect(
                'datacore'
            );


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
                'El PDF de queja solo esta disponible para QJ, QJF y QJV.'
            );
        }


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


        $evidencias =
            $db
            ->table(
                'ai_reporte_evidencias'
            )
            ->select([
                'id_evidencia',
                'nombre_original',
                'nombre_archivo',
                'extension',
                'mime_type',
                'tamano_bytes',
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


    private function obtenerAssets(): array
    {
        $base =
            FCPATH
            . 'assets/asuntos_internos/sistema_reportes/img/pdf/quejas/';


        return [
            'header' =>
                $this->dataUri(
                    $base . 'header.png'
                ),

            'watermark' =>
                $this->dataUri(
                    $base . 'watermark.png'
                ),

            'footer' =>
                $this->dataUri(
                    $base . 'footer.png'
                ),
        ];
    }


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


        return 'data:image/png;base64,'
            . base64_encode(
                file_get_contents(
                    $ruta
                )
            );
    }


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
                    $tipo . '-'
                )
            ) {

                return $tipo;
            }
        }


        return '';
    }


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
