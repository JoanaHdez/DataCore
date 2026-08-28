<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProbarConexiones extends BaseCommand
{
    protected $group       = 'Pruebas';
    protected $name        = 'db:probar-conexiones';
    protected $description = 'Comprueba las conexiones de DataCore, Plantilla General y Parque Vehicular.';

    public function run(array $params)
    {
        $conexiones = [
            'datacore'  => 'datacore',
            'plantilla' => 'plantilla_general',
            'unidades'  => 'puestasyremisiones',
        ];

        foreach ($conexiones as $grupo => $esperada) {

            try {

                $db = \Config\Database::connect($grupo);

                $resultado = $db
                    ->query('SELECT DATABASE() AS bd')
                    ->getRowArray();

                $actual = $resultado['bd'] ?? null;

                if ($actual === $esperada) {

                    CLI::write(
                        "OK  {$grupo} -> {$actual}",
                        'green'
                    );

                    continue;
                }

                CLI::error(
                    "ERROR  {$grupo} -> esperaba {$esperada}, obtuvo "
                    . ($actual ?? 'NULL')
                );

            } catch (\Throwable $e) {

                CLI::error(
                    "ERROR  {$grupo}: {$e->getMessage()}"
                );

            }

        }
    }
}