<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashboardExcelService
{
    /**
     * Secciones permitidas para exportación.
     */
    private const SECCIONES_PERMITIDAS = [
        'indicadores',
        'clasificaciones',
        'areas',
        'turnos',
        'tendencia',
        'recientes',
    ];


    /**
     * Genera el archivo Excel temporal.
     */
    public function generar(array $secciones): string
    {
        $secciones = array_values(
            array_intersect(
                self::SECCIONES_PERMITIDAS,
                $secciones
            )
        );

        if ($secciones === []) {
            throw new \InvalidArgumentException(
                'No existen secciones válidas para exportar.'
            );
        }


        $spreadsheet = new Spreadsheet();

        /*
         * Eliminamos la hoja creada automáticamente.
         * Las hojas se crearán según la selección.
         */
        $spreadsheet->removeSheetByIndex(0);


        foreach ($secciones as $seccion) {

            switch ($seccion) {

                case 'indicadores':
                    $this->crearIndicadores(
                        $spreadsheet
                    );
                    break;

                case 'clasificaciones':
                    $this->crearClasificaciones(
                        $spreadsheet
                    );
                    break;

                case 'areas':
                    $this->crearAreas(
                        $spreadsheet
                    );
                    break;

                case 'turnos':
                    $this->crearTurnos(
                        $spreadsheet
                    );
                    break;

                case 'tendencia':
                    $this->crearTendencia(
                        $spreadsheet
                    );
                    break;

                case 'recientes':
                    $this->crearRecientes(
                        $spreadsheet
                    );
                    break;
            }
        }


        /*
         * Dejamos activa la primera hoja.
         */
        $spreadsheet->setActiveSheetIndex(0);


        $directorio =
            WRITEPATH . 'uploads/exportaciones';

        if (! is_dir($directorio)) {

            if (
                ! mkdir(
                    $directorio,
                    0775,
                    true
                )
                && ! is_dir($directorio)
            ) {
                throw new \RuntimeException(
                    'No fue posible crear el directorio de exportaciones.'
                );
            }
        }


        $nombreArchivo =
            'dashboard_reportes_'
            . date('Ymd_His')
            . '.xlsx';

        $ruta =
            $directorio
            . DIRECTORY_SEPARATOR
            . $nombreArchivo;


        $writer = new Xlsx(
            $spreadsheet
        );

        $writer->save(
            $ruta
        );

        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);


        return $ruta;
    }


    /**
     * Indicadores generales.
     */
    private function crearIndicadores(
        Spreadsheet $spreadsheet
    ): void {

        $hoja =
            $spreadsheet->createSheet();

        $hoja->setTitle(
            'Indicadores'
        );


        $datos = [
            ['Indicador', 'Cantidad'],
            ['Total de reportes', 128],
            ['Pendientes', 24],
            ['En proceso', 67],
            ['Finalizados', 37],
        ];


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B5'
        );
    }


    /**
     * Reportes por clasificación.
     */
    private function crearClasificaciones(
        Spreadsheet $spreadsheet
    ): void {

        $hoja =
            $spreadsheet->createSheet();

        $hoja->setTitle(
            'Clasificaciones'
        );


        $datos = [
            ['Clasificación', 'Reportes'],
            ['Queja', 58],
            ['Denuncia', 34],
            ['Investigación', 22],
            ['Otro', 14],
        ];


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B5'
        );
    }


    /**
     * Reportes por área.
     */
    private function crearAreas(
        Spreadsheet $spreadsheet
    ): void {

        $hoja =
            $spreadsheet->createSheet();

        $hoja->setTitle(
            'Areas'
        );


        $datos = [
            ['Área', 'Reportes'],
            ['Seguridad Ciudadana', 46],
            ['Tránsito', 31],
            ['Operaciones', 24],
            ['Prevención', 17],
            ['Administrativa', 10],
        ];


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B6'
        );
    }


    /**
     * Reportes por turno.
     */
    private function crearTurnos(
        Spreadsheet $spreadsheet
    ): void {

        $hoja =
            $spreadsheet->createSheet();

        $hoja->setTitle(
            'Turnos'
        );


        $datos = [
            ['Turno', 'Reportes'],
            ['Primer turno', 52],
            ['Segundo turno', 43],
            ['Tercer turno', 33],
        ];


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B4'
        );
    }


    /**
     * Tendencia de reportes.
     */
    private function crearTendencia(
        Spreadsheet $spreadsheet
    ): void {

        $hoja =
            $spreadsheet->createSheet();

        $hoja->setTitle(
            'Tendencia'
        );


        $datos = [
            ['Periodo', 'Reportes'],
            ['Marzo', 14],
            ['Abril', 19],
            ['Mayo', 17],
            ['Junio', 26],
            ['Julio', 22],
            ['Agosto', 30],
        ];


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B7'
        );
    }


    /**
     * Reportes recientes.
     */
    private function crearRecientes(
        Spreadsheet $spreadsheet
    ): void {

        $hoja =
            $spreadsheet->createSheet();

        $hoja->setTitle(
            'Reportes recientes'
        );


        $datos = [
            [
                'Folio',
                'Fecha',
                'Expediente',
                'Clasificación',
                'Área',
                'Estado',
            ],
            [
                'AI-2026-001',
                '25/08/2026',
                'CAI/001/2026',
                'Queja',
                'Seguridad Ciudadana',
                'En proceso',
            ],
            [
                'AI-2026-002',
                '24/08/2026',
                'CAI/002/2026',
                'Denuncia',
                'Tránsito',
                'Finalizado',
            ],
            [
                'AI-2026-003',
                '23/08/2026',
                'CAI/003/2026',
                'Queja',
                'Operaciones',
                'Pendiente',
            ],
        ];


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:F4'
        );
    }


    /**
     * Estilo base de las hojas.
     */
    private function aplicarEstiloGeneral(
        Worksheet $hoja,
        string $rango
    ): void {

        /*
         * Encabezados.
         */
        $ultimaColumna =
            $hoja->getHighestColumn();

        $hoja
            ->getStyle(
                'A1:' . $ultimaColumna . '1'
            )
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setRGB('FFFFFF');


        $hoja
            ->getStyle(
                'A1:' . $ultimaColumna . '1'
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB('173554');


        /*
         * Bordes suaves.
         */
        $hoja
            ->getStyle($rango)
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(
                Border::BORDER_THIN
            )
            ->getColor()
            ->setRGB('E1E8EE');


        /*
         * Alineación vertical.
         */
        $hoja
            ->getStyle($rango)
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        /*
         * Ajuste automático de columnas.
         */
        foreach (
            range(
                'A',
                $ultimaColumna
            ) as $columna
        ) {
            $hoja
                ->getColumnDimension(
                    $columna
                )
                ->setAutoSize(true);
        }


        /*
         * Altura del encabezado.
         */
        $hoja
            ->getRowDimension(1)
            ->setRowHeight(24);


        /*
         * Congelamos encabezados.
         */
        $hoja->freezePane('A2');


        /*
         * Autofiltro.
         */
        $hoja->setAutoFilter(
            'A1:'
            . $ultimaColumna
            . $hoja->getHighestRow()
        );
    }
}