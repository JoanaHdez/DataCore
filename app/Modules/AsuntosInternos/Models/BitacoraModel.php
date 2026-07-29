<?php

namespace App\Modules\AsuntosInternos\Models;

use CodeIgniter\Model;

class BitacoraModel extends Model
{
    protected $table = 'bitacora_procesamiento';
    protected $primaryKey = 'id_bitacora';

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'id_archivo',
        'accion',
        'descripcion',
        'nivel',
        'fecha_evento',
    ];

    protected $useTimestamps = false;
}