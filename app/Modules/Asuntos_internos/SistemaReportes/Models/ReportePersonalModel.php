<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Models;

use CodeIgniter\Model;

class ReportePersonalModel extends Model
{
    protected $DBGroup = 'datacore';

    protected $table = 'ai_reporte_personal';

    protected $primaryKey = 'id_reporte_personal';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'id_reporte',
        'plantilla_id',
        'perscod',
        'nombre_snapshot',
        'area_snapshot',
        'turno_snapshot',
    ];

    protected $useTimestamps = false;

    protected $skipValidation = true;
}
