<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

class FotoPersonalService
{
    private const BASE_URL =
    'http://10.8.6.2:8083/dgsc/images/fotos/';


    public function obtenerBase64(
        ?string $perscod
    ): ?string {

        $perscod = trim(
            (string) $perscod
        );


        if ($perscod === '') {
            return null;
        }


        $url =
            self::BASE_URL
            . rawurlencode($perscod)
            . '/F.F.R.E.jpg';


        try {

            $curl =
                curl_init();


            curl_setopt_array(
                $curl,
                [
                    CURLOPT_URL =>
                    $url,

                    CURLOPT_RETURNTRANSFER =>
                    true,

                    CURLOPT_FOLLOWLOCATION =>
                    true,

                    CURLOPT_CONNECTTIMEOUT =>
                    5,

                    CURLOPT_TIMEOUT =>
                    10,

                    CURLOPT_HTTPGET =>
                    true,
                ]
            );


            $contenido =
                curl_exec(
                    $curl
                );


            $httpCode =
                (int) curl_getinfo(
                    $curl,
                    CURLINFO_HTTP_CODE
                );


            $contentType =
                (string) curl_getinfo(
                    $curl,
                    CURLINFO_CONTENT_TYPE
                );


            $error =
                curl_error(
                    $curl
                );


            curl_close(
                $curl
            );


            if (
                $contenido === false
                || $contenido === ''
            ) {

                log_message(
                    'error',
                    'No fue posible descargar foto {perscod} por cURL: {error}',
                    [
                        'perscod' =>
                        $perscod,

                        'error' =>
                        $error,
                    ]
                );


                return null;
            }


            if (
                $httpCode < 200
                || $httpCode >= 300
            ) {

                log_message(
                    'error',
                    'Foto {perscod} respondió HTTP {codigo}',
                    [
                        'perscod' =>
                        $perscod,

                        'codigo' =>
                        $httpCode,
                    ]
                );


                return null;
            }


            if (
                !str_starts_with(
                    strtolower($contentType),
                    'image/'
                )
            ) {

                log_message(
                    'error',
                    'La respuesta de foto {perscod} no es una imagen. Content-Type: {tipo}',
                    [
                        'perscod' =>
                        $perscod,

                        'tipo' =>
                        $contentType,
                    ]
                );


                return null;
            }


            return
                'data:'
                . $contentType
                . ';base64,'
                . base64_encode(
                    $contenido
                );
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error obteniendo fotografía {perscod}: {mensaje}',
                [
                    'perscod' =>
                    $perscod,

                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return null;
        }
    }
}
