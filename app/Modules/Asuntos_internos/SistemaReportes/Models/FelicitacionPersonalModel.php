<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Models;

use CodeIgniter\Model;

class FelicitacionPersonalModel extends Model
{
    protected $DBGroup = 'datacore';

    protected $table = 'ai_felicitacion_personal';

    protected $primaryKey = 'id_felicitacion_personal';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'id_felicitacion',
        'plantilla_id',
        'perscod',
        'nombre_snapshot',
        'area_snapshot',
        'turno_snapshot',
        'alias_snapshot',
        'created_at',
    ];

    protected $useTimestamps = false;

    protected $skipValidation = true;
}