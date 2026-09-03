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
     * Secciones actualmente disponibles para exportación.
     *
     * Zona y sanciones NO se incluyen todavía porque
     * continúan pendientes de una fuente/regla oficial.
     */
    /**
     * Secciones actualmente disponibles para exportación.
     */
    private const SECCIONES_PERMITIDAS = [
    'indicadores',
    'sectores_turnos',
    'areas',
    'sectores',
    'turnos',
    'sanciones',
    ];


    /**
     * Servicio que contiene exactamente la misma lógica
     * utilizada por las gráficas del Dashboard.
     */
    private DashboardService $dashboardService;


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct(
        array $filtros = []
    ) {

        $this->dashboardService =
            new DashboardService();


        $this->dashboardService
            ->establecerFiltros(
                $filtros
            );
    }


    /* =========================================================
       GENERAR EXCEL
    ========================================================= */

    public function generar(
        array $secciones
    ): string {

        /*
         * Conservamos únicamente secciones válidas.
         *
         * array_intersect utiliza el orden definido en
         * SECCIONES_PERMITIDAS para que el Excel mantenga
         * siempre un orden consistente.
         */

        $secciones =
            array_values(
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


        $spreadsheet =
            new Spreadsheet();


        /*
         * PhpSpreadsheet crea una hoja automáticamente.
         *
         * La eliminamos porque nosotros crearemos únicamente
         * las hojas seleccionadas por el usuario.
         */

        $spreadsheet
            ->removeSheetByIndex(
                0
            );


        /* =====================================================
           CREAR HOJAS
        ===================================================== */

        foreach (
            $secciones
            as $seccion
        ) {

            switch ($seccion) {

                case 'indicadores':

                    $this->crearIndicadores(
                        $spreadsheet
                    );

                    break;


                case 'sectores_turnos':

                    $this->crearSectoresTurnos(
                        $spreadsheet
                    );

                    break;


                case 'areas':

                    $this->crearAreas(
                        $spreadsheet
                    );  

                    break;


                case 'sectores':

                    $this->crearSectores(
                        $spreadsheet
                    );

                    break;


                case 'turnos':

                    $this->crearTurnos(
                        $spreadsheet
                    );

                    break;

                case 'sanciones':

                    $this->crearSanciones(
                        $spreadsheet
                    );

                    break;
            }
        }


        /* =====================================================
           PRIMERA HOJA ACTIVA
        ===================================================== */

        $spreadsheet
            ->setActiveSheetIndex(
                0
            );


        /* =====================================================
           DIRECTORIO TEMPORAL
        ===================================================== */

        $directorio =
            WRITEPATH
            . 'uploads/exportaciones';


        if (
            !is_dir(
                $directorio
            )
        ) {

            if (
                !mkdir(
                    $directorio,
                    0775,
                    true
                )
                && !is_dir(
                    $directorio
                )
            ) {

                throw new \RuntimeException(
                    'No fue posible crear el directorio de exportaciones.'
                );
            }
        }


        /* =====================================================
           NOMBRE
        ===================================================== */

        $nombreArchivo =
            'dashboard_reportes_'
            . date(
                'Ymd_His'
            )
            . '.xlsx';


        $ruta =
            $directorio
            . DIRECTORY_SEPARATOR
            . $nombreArchivo;


        /* =====================================================
           GUARDAR
        ===================================================== */

        $writer =
            new Xlsx(
                $spreadsheet
            );


        $writer->save(
            $ruta
        );


        $spreadsheet
            ->disconnectWorksheets();


        unset(
            $spreadsheet
        );


        return $ruta;
    }


    /* =========================================================
       INDICADORES GENERALES
    ========================================================= */

    private function crearIndicadores(
        Spreadsheet $spreadsheet
    ): void {

        $datosDashboard =
            $this->dashboardService
            ->obtenerIndicadores();


        $datos = [

            [
                'Indicador',
                'Cantidad',
            ],

            [
                'Total de reportes',
                (int) (
                    $datosDashboard['total']
                    ?? 0
                ),
            ],

            [
                'Pendientes',
                (int) (
                    $datosDashboard['pendientes']
                    ?? 0
                ),
            ],

            [
                'En proceso',
                (int) (
                    $datosDashboard['en_proceso']
                    ?? 0
                ),
            ],

            [
                'Finalizados',
                (int) (
                    $datosDashboard['finalizados']
                    ?? 0
                ),
            ],
        ];


        $hoja =
            $spreadsheet
            ->createSheet();


        $hoja->setTitle(
            'Indicadores'
        );


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B'
                . count(
                    $datos
                )
        );
    }


    /* =========================================================
       QUEJAS POR SECTORES Y TURNOS
    ========================================================= */

    private function crearSectoresTurnos(
        Spreadsheet $spreadsheet
    ): void {

        $datosDashboard =
            $this->dashboardService
            ->obtenerSectoresTurnos();


        $sectores =
            $datosDashboard['sectores']
            ?? [];


        $turnos =
            $datosDashboard['turnos']
            ?? [];


        /*
         * El DashboardService devuelve:
         *
         * turnos = [
         *     'Primer turno' => [ ...15 valores... ],
         *     'Segundo turno' => [ ... ],
         *     ...
         * ]
         */

        $nombresTurnos = [
            'Primer turno',
            'Segundo turno',
            'Tercer turno',
            'Alfa',
            'Beta',
            'Diario',
            'No refiere ni fecha ni horario',
        ];


        $encabezado = [
            'Sector',
        ];


        foreach (
            $nombresTurnos
            as $nombreTurno
        ) {

            $encabezado[] =
                $nombreTurno;
        }


        $encabezado[] =
            'Total por sector';


        $datos = [
            $encabezado,
        ];


        /*
         * Totales de las columnas.
         */

        $totalesTurnos =
            array_fill(
                0,
                count(
                    $nombresTurnos
                ),
                0
            );


        $totalGeneral =
            0;


        foreach (
            $sectores
            as $indiceSector => $sector
        ) {

            $fila = [
                $sector,
            ];


            $totalSector =
                0;


            foreach (
                $nombresTurnos
                as $indiceTurno => $nombreTurno
            ) {

                $cantidad =
                    (int) (
                        $turnos[$nombreTurno][$indiceSector]
                        ?? 0
                    );


                $fila[] =
                    $cantidad;


                $totalSector +=
                    $cantidad;


                $totalesTurnos[$indiceTurno] +=
                    $cantidad;
            }


            $fila[] =
                $totalSector;


            $totalGeneral +=
                $totalSector;


            $datos[] =
                $fila;
        }


        /*
         * Fila TOTAL.
         */

        $filaTotal = [
            'TOTAL',
        ];


        foreach (
            $totalesTurnos
            as $totalTurno
        ) {

            $filaTotal[] =
                $totalTurno;
        }


        $filaTotal[] =
            $totalGeneral;


        $datos[] =
            $filaTotal;


        $hoja =
            $spreadsheet
            ->createSheet();


        $hoja->setTitle(
            'Sectores y turnos'
        );


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $ultimaFila =
            count(
                $datos
            );


        $ultimaColumna =
            $hoja
            ->getHighestColumn();


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:'
                . $ultimaColumna
                . $ultimaFila
        );


        /*
         * Resaltamos la fila TOTAL.
         */

        $hoja
            ->getStyle(
                'A'
                    . $ultimaFila
                    . ':'
                    . $ultimaColumna
                    . $ultimaFila
            )
            ->getFont()
            ->setBold(
                true
            );
    }


    /* =========================================================
       QUEJAS POR ÁREA
    ========================================================= */

    private function crearAreas(
        Spreadsheet $spreadsheet
    ): void {

        $datosDashboard =
            $this->dashboardService
            ->obtenerQuejasPorArea();


        $areas =
            $datosDashboard['areas']
            ?? [];


        $totales =
            $datosDashboard['totales']
            ?? [];


        $datos = [

            [
                'Área',
                'Quejas',
            ],

        ];


        foreach (
            $areas
            as $indice => $area
        ) {

            $datos[] = [

                $area,

                (int) (
                    $totales[$indice]
                    ?? 0
                ),

            ];
        }


        $hoja =
            $spreadsheet
            ->createSheet();


        $hoja->setTitle(
            'Quejas por area'
        );


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B'
                . count(
                    $datos
                )
        );
    }


    /* =========================================================
    QUEJAS POR SECTOR
    ========================================================= */

    private function crearSectores(
        Spreadsheet $spreadsheet
    ): void {

        /*
        * Reutilizamos la misma información institucional
        * utilizada por "Sectores y turnos".
        *
        * DashboardService se encarga de:
        *
        * - aplicar los filtros activos;
        * - obtener el sector desde area_snapshot;
        * - clasificar los turnos;
        * - evitar duplicados por reporte + sector + turno.
        */

        $datosDashboard =
            $this->dashboardService
            ->obtenerSectoresTurnos();


        $sectores =
            $datosDashboard['sectores']
            ?? [];


        $turnos =
            $datosDashboard['turnos']
            ?? [];


        $datos = [

            [
                'Sector',
                'Quejas',
            ],

        ];


        $totalGeneral =
            0;


        foreach (
            $sectores
            as $indiceSector => $sector
        ) {

            $totalSector =
                0;


            foreach (
                $turnos
                as $cantidadesTurno
            ) {

                $totalSector +=
                    (int) (
                        $cantidadesTurno[$indiceSector]
                        ?? 0
                    );

            }


            $datos[] = [

                $sector,

                $totalSector,

            ];


            $totalGeneral +=
                $totalSector;

        }


        /* =====================================================
        TOTAL GENERAL
        ===================================================== */

        $datos[] = [

            'TOTAL',

            $totalGeneral,

        ];


        /* =====================================================
        CREAR HOJA
        ===================================================== */

        $hoja =
            $spreadsheet
            ->createSheet();


        $hoja->setTitle(
            'Quejas por sector'
        );


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        /* =====================================================
        ESTILO GENERAL
        ===================================================== */

        $ultimaFila =
            count(
                $datos
            );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B'
                . $ultimaFila
        );


        /* =====================================================
        RESALTAR TOTAL
        ===================================================== */

        $hoja
            ->getStyle(
                'A'
                    . $ultimaFila
                    . ':B'
                    . $ultimaFila
            )
            ->getFont()
            ->setBold(
                true
            );
    }

    /* =========================================================
       QUEJAS POR TURNO
    ========================================================= */

    private function crearTurnos(
        Spreadsheet $spreadsheet
    ): void {

        $datosDashboard =
            $this->dashboardService
            ->obtenerQuejasPorTurno();


        $turnos =
            $datosDashboard['turnos']
            ?? [];


        $totales =
            $datosDashboard['totales']
            ?? [];


        $datos = [

            [
                'Turno',
                'Quejas',
            ],

        ];


        foreach (
            $turnos
            as $indice => $turno
        ) {

            $datos[] = [

                $turno,

                (int) (
                    $totales[$indice]
                    ?? 0
                ),

            ];
        }


        /*
         * Total general.
         */

        $datos[] = [

            'TOTAL',

            (int) (
                $datosDashboard['total']
                ?? 0
            ),

        ];


        $hoja =
            $spreadsheet
            ->createSheet();


        $hoja->setTitle(
            'Quejas por turno'
        );


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $ultimaFila =
            count(
                $datos
            );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B'
                . $ultimaFila
        );


        $hoja
            ->getStyle(
                'A'
                    . $ultimaFila
                    . ':B'
                    . $ultimaFila
            )
            ->getFont()
            ->setBold(
                true
            );
    }


    /* =========================================================
   SANCIONES DISCIPLINARIAS
========================================================= */

    private function crearSanciones(
        Spreadsheet $spreadsheet
    ): void {

        /*
     * Utilizamos exactamente la misma información
     * que alimenta la gráfica de Sanciones
     * disciplinarias del Dashboard.
     *
     * DashboardService se encarga de:
     *
     * - aplicar todos los filtros activos;
     * - consultar ai_reporte_sanciones;
     * - considerar únicamente sanciones actuales;
     * - excluir sanciones eliminadas;
     * - agrupar Arresto, Amonestación y Otro;
     * - evitar duplicados.
     */

        $datosDashboard =
            $this->dashboardService
            ->obtenerSanciones();


        $tipos =
            $datosDashboard['tipos']
            ?? [];


        $totales =
            $datosDashboard['totales']
            ?? [];


        /* =====================================================
       PREPARAR DATOS
    ===================================================== */

        $datos = [

            [
                'Sanción disciplinaria',
                'Cantidad',
            ],

        ];


        foreach (
            $tipos
            as $indice => $tipo
        ) {

            $datos[] = [

                $tipo,

                (int) (
                    $totales[$indice]
                    ?? 0
                ),

            ];
        }


        /* =====================================================
       TOTAL GENERAL
    ===================================================== */

        $datos[] = [

            'TOTAL',

            (int) (
                $datosDashboard['total']
                ?? 0
            ),

        ];


        /* =====================================================
       CREAR HOJA
    ===================================================== */

        $hoja =
            $spreadsheet
            ->createSheet();


        $hoja->setTitle(
            'Sanciones'
        );


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        /* =====================================================
       ESTILO GENERAL
    ===================================================== */

        $ultimaFila =
            count(
                $datos
            );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:B'
                . $ultimaFila
        );


        /* =====================================================
       RESALTAR TOTAL
    ===================================================== */

        $hoja
            ->getStyle(
                'A'
                    . $ultimaFila
                    . ':B'
                    . $ultimaFila
            )
            ->getFont()
            ->setBold(
                true
            );
    }

    /* =========================================================
       REPORTES RECIENTES
    ========================================================= */

    private function crearRecientes(
        Spreadsheet $spreadsheet
    ): void {

        $reportes =
            $this->dashboardService
            ->obtenerReportesRecientes(
                6
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

        ];


        foreach (
            $reportes
            as $reporte
        ) {

            $datos[] = [

                $reporte['folio']
                    ?? '',

                $reporte['fecha']
                    ?? '',

                $reporte['expediente']
                    ?? '',

                $reporte['clasificacion']
                    ?? '',

                $reporte['area']
                    ?? '',

                $reporte['estado']
                    ?? '',

            ];
        }


        $hoja =
            $spreadsheet
            ->createSheet();


        $hoja->setTitle(
            'Reportes recientes'
        );


        $hoja->fromArray(
            $datos,
            null,
            'A1'
        );


        $this->aplicarEstiloGeneral(
            $hoja,
            'A1:F'
                . count(
                    $datos
                )
        );
    }


    /* =========================================================
       ESTILO GENERAL
    ========================================================= */

    private function aplicarEstiloGeneral(
        Worksheet $hoja,
        string $rango
    ): void {

        $ultimaColumna =
            $hoja
            ->getHighestColumn();


        /* =====================================================
           ENCABEZADO
        ===================================================== */

        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . '1'
            )
            ->getFont()
            ->setBold(
                true
            )
            ->getColor()
            ->setRGB(
                'FFFFFF'
            );


        $hoja
            ->getStyle(
                'A1:'
                    . $ultimaColumna
                    . '1'
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB(
                '173554'
            );


        /* =====================================================
           BORDES
        ===================================================== */

        $hoja
            ->getStyle(
                $rango
            )
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(
                Border::BORDER_THIN
            )
            ->getColor()
            ->setRGB(
                'E1E8EE'
            );


        /* =====================================================
           ALINEACIÓN
        ===================================================== */

        $hoja
            ->getStyle(
                $rango
            )
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        /* =====================================================
           AJUSTE AUTOMÁTICO DE COLUMNAS
        ===================================================== */

        foreach (
            range(
                'A',
                $ultimaColumna
            )
            as $columna
        ) {

            $hoja
                ->getColumnDimension(
                    $columna
                )
                ->setAutoSize(
                    true
                );
        }


        /* =====================================================
           ALTURA DEL ENCABEZADO
        ===================================================== */

        $hoja
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                24
            );


        /* =====================================================
           CONGELAR ENCABEZADO
        ===================================================== */

        $hoja
            ->freezePane(
                'A2'
            );


        /* =====================================================
           AUTOFILTRO
        ===================================================== */

        $hoja
            ->setAutoFilter(
                'A1:'
                    . $ultimaColumna
                    . $hoja
                    ->getHighestRow()
            );
    }
}
