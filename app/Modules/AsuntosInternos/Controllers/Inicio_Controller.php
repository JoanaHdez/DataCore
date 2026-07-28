<?php

namespace App\Modules\AsuntosInternos\Controllers;

use App\Controllers\BaseController;

class Inicio_Controller extends BaseController
{
    private string $rutaProcesados;

    public function __construct()
    {
        $this->rutaProcesados = WRITEPATH . 'asuntos-internos/procesados/';
    }

    public function index()
    {
        return view(
            'App\Modules\AsuntosInternos\Views\inicio',
            [
                'archivosRecientes' => $this->obtenerArchivosRecientes(3),
            ]
        );
    }

    private function obtenerArchivosRecientes(int $limite = 5): array
    {
        $archivos = [];

        foreach (glob($this->rutaProcesados . '*.json') ?: [] as $metadataPath) {
            $contenido = file_get_contents($metadataPath);

            if ($contenido === false) {
                continue;
            }

            $metadata = json_decode($contenido, true);

            if (! is_array($metadata)) {
                continue;
            }

            $rutaArchivo = $this->rutaProcesados
                . ($metadata['archivo_fisico'] ?? '');

            if (! is_file($rutaArchivo)) {
                continue;
            }

            $archivos[] = $metadata;
        }

        usort(
            $archivos,
            static fn (array $a, array $b): int =>
                strcmp(
                    $b['fecha_procesamiento'] ?? '',
                    $a['fecha_procesamiento'] ?? ''
                )
        );

        return array_slice($archivos, 0, $limite);
    }
}