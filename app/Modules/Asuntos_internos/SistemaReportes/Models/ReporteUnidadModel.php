<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Models;

use CodeIgniter\Model;

class ReporteUnidadModel extends Model
{
    protected $DBGroup = 'datacore';

    protected $table = 'ai_reporte_unidades';

    protected $primaryKey = 'id_reporte_unidad';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'id_reporte',
        'parque_vehicular_id',
        'no_economico_snapshot',
        'placas_snapshot',
        'marca_snapshot',
        'submarca_snapshot',
        'color_snapshot',
        'estatus_snapshot',
        'servicio_snapshot',
        'tipo_snapshot',
        'id_origen',
    ];

    protected $useTimestamps = false;

    protected $skipValidation = true;
}