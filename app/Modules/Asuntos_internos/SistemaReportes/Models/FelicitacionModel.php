<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Models;

use CodeIgniter\Model;

class FelicitacionModel extends Model
{
    protected $DBGroup = 'datacore';

    protected $table = 'ai_felicitaciones';

    protected $primaryKey = 'id_felicitacion';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'numero_folio',
        'folio',
        'fecha_registro',
        'nombre_felicitante',
        'razon_felicitacion',

        'created_by',
        'updated_by',

        'eliminado',
        'eliminado_at',
        'eliminado_por',
    ];

    protected $useTimestamps = false;

    protected $skipValidation = true;
}