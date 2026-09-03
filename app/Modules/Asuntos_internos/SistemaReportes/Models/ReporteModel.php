<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Models;

use CodeIgniter\Model;

class ReporteModel extends Model
{
    protected $DBGroup = 'datacore';

    protected $table = 'ai_reportes';

    protected $primaryKey = 'id_reporte';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'folio',
        'fecha_registro',
        'folio_ip',
        'fecha_queja',
        'fecha_acuerdo',
        'expediente',
        'nomenclatura',
        'numero_oficio',

        'fecha_hechos',
        'hora_hechos',
        'descripcion_hechos',

        'calle',
        'numero_exterior',
        'colonia',
        'entre_calle',
        'y_calle',
        'municipio',
        'estado',
        'sector',
        'cuadrante',
        'id_cuadra',
        'latitud',
        'longitud',
        'origen_ubicacion',

        'nombre_quejoso',
        'edad_quejoso',
        'genero_quejoso',
        'telefono_quejoso',
        'correo_quejoso',

        'clasificacion',
        'inspector',
        'investigador',
        'quien_emite_resolucion',
        'resolucion',
        'motivos',
        'estado_actual',
        'observaciones',
        'modalidad_unidad',

        'created_by',
        'updated_by',

        'eliminado',
        'eliminado_at',
        'eliminado_por',
    ];

    protected $useTimestamps = false;

    protected $skipValidation = true;
}