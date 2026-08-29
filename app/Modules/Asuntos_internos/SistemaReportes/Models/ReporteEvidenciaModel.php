<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Models;

use CodeIgniter\Model;

class ReporteEvidenciaModel extends Model
{
    protected $DBGroup = 'datacore';

    protected $table = 'ai_reporte_evidencias';

    protected $primaryKey = 'id_evidencia';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'id_reporte',
        'nombre_original',
        'nombre_archivo',
        'ruta_archivo',
        'extension',
        'mime_type',
        'tamano_bytes',
        'orden',
        'created_by',
        'eliminado',
        'eliminado_at',
        'eliminado_por',
    ];

    protected $useTimestamps = false;

    protected $skipValidation = true;
}