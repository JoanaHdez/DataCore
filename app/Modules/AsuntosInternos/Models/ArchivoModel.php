<?php

namespace App\Modules\AsuntosInternos\Models;

use CodeIgniter\Model;

class ArchivoModel extends Model
{
    protected $table = 'archivos_excel';
    protected $primaryKey = 'id_archivo';

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'nombre_original',
        'extension',
        'tipo_mime',
        'tamano',
        'archivo_excel',
        'total_fechas_modificadas',
        'estado',
        'mensaje_error',
        'fecha_procesamiento',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nombre_original' => 'required|max_length[255]',
        'extension' => 'required|max_length[20]',
        'tipo_mime' => 'required|max_length[150]',
        'tamano' => 'required|integer',
        'estado' => 'required|in_list[procesando,completado,error]',
    ];
}