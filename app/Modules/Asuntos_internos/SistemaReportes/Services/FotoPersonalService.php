<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

class FotoPersonalService
{
    private const BASE_URL = 'http://10.8.6.2:8083/dgsc/images/fotos/';

    public function obtenerUrl(?string $perscod): ?string
    {
        $perscod = trim((string) $perscod);

        if ($perscod === '') {
            return null;
        }

        return self::BASE_URL . rawurlencode($perscod) . '/F.F.R.E.jpg';
    }
}
