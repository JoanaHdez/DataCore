<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;

class DashboardQuejosoService
{
    private $db;

    private DashboardFiltrosService $filtrosService;

    private ?string $secretoSeleccion = null;

    public function __construct(
        DashboardFiltrosService $filtrosService
    ) {
        $this->db =
            \Config\Database::connect(
                'datacore'
            );

        $this->filtrosService =
            $filtrosService;
    }

    public function buscarQuejosos(
        string $termino,
        int $limite = 10
    ): array {
        $this->obtenerSecretoSeleccion();

        $termino =
            $this->normalizarNombre(
                $termino
            );

        $limite =
            max(
                1,
                min(
                    $limite,
                    20
                )
            );

        if (
            mb_strlen(
                $termino,
                'UTF-8'
            ) > 80
        ) {
            $termino =
                mb_substr(
                    $termino,
                    0,
                    80,
                    'UTF-8'
                );
        }

        $terminoBusqueda =
            mb_strtoupper(
                $termino,
                'UTF-8'
            );

        $quejosos =
            $this->obtenerQuejososAgrupados();

        $resultados = [];

        foreach (
            $quejosos
            as $claveNombre => $quejoso
        ) {
            if (
                $terminoBusqueda !== ''
                && !str_contains(
                    $claveNombre,
                    $terminoBusqueda
                )
            ) {
                continue;
            }

            $resultados[] = [

                'clave' =>
                    $this->crearClaveSeleccion(
                        $claveNombre
                    ),

                'nombre' =>
                    (string) (
                        $quejoso['nombre']
                        ?? $claveNombre
                    ),

                'total_quejas' =>
                    (int) (
                        $quejoso['total_quejas']
                        ?? 0
                    ),

            ];

            if (
                count(
                    $resultados
                ) >= $limite
            ) {
                break;
            }
        }

        return $resultados;
    }

    public function obtenerAnalisisBase(
        string $claveSeleccion
    ): array {
        $this->obtenerSecretoSeleccion();

        $claveSeleccion =
            trim(
                $claveSeleccion
            );

        if (
            $claveSeleccion === ''
        ) {
            return $this->respuestaVacia(
                $claveSeleccion
            );
        }

        $claveNombreSeleccionada =
            null;


        $quejosoSeleccionado =
            null;


        foreach (
            $this->obtenerQuejososAgrupados(
                false
            )
            as $claveNombre => $quejoso
        ) {
            if (
                hash_equals(
                    $this->crearClaveSeleccion(
                        $claveNombre
                    ),
                    $claveSeleccion
                )
            ) {
                $claveNombreSeleccionada =
                    $claveNombre;

                $quejosoSeleccionado =
                    $quejoso;

                break;
            }
        }

        if (
            $claveNombreSeleccionada === null
            || $quejosoSeleccionado === null
        ) {
            return $this->respuestaVacia(
                $claveSeleccion
            );
        }


        $quejososFiltrados =
            $this->obtenerQuejososAgrupados();


        $quejosoFiltrado =
            $quejososFiltrados[$claveNombreSeleccionada]
            ?? null;


        $historial =
            array_values(
                $quejosoFiltrado['reportes']
                ?? []
            );


        $idsReportes =
            array_values(
                array_map(
                    'intval',
                    array_keys(
                        $quejosoFiltrado['reportes']
                        ?? []
                    )
                )
            );


        return [

            'quejoso' => [

                'clave' =>
                    $claveSeleccion,

                'nombre' =>
                    (string) (
                        $quejosoSeleccionado['nombre']
                        ?? $claveNombreSeleccionada
                    ),

            ],

            'total_quejas' =>
                (int) (
                    $quejosoFiltrado['total_quejas']
                    ?? 0
                ),

            'indicadores' =>
                $this->calcularIndicadores(
                    $historial
                ),

            'historial' =>
                $this->agregarMotivosAHistorial(
                    $historial
                ),

            'ids_reportes' =>
                $idsReportes,

            'recurrencias' =>
                $this->obtenerRecurrencias(
                    $idsReportes,
                    $historial
                ),

        ];
    }

    private function obtenerQuejososAgrupados(
        bool $aplicarFiltros = true
    ): array
    {
        $builder =
            $this->obtenerBuilderQuejososBase();


        if (
            $aplicarFiltros
        ) {
            $this->filtrosService
                ->aplicarFiltrosReportes(
                    $builder,
                    'r'
                );
        }


        return $this->agruparQuejososDesdeRegistros(
            $builder
                ->get()
                ->getResultArray()
        );
    }

    private function obtenerBuilderQuejososBase()
    {
        return $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([
                'r.id_reporte',
                'r.folio',
                'r.fecha_registro',
                'r.estado_actual',
                'r.clasificacion',
                'r.nombre_quejoso',
            ])
            ->where(
                'r.eliminado',
                0
            )
            ->where(
                'r.tipo_registro',
                'QUEJA'
            )
            ->where(
                'r.es_anonimo',
                0
            )
            ->where(
                'r.nombre_quejoso IS NOT NULL',
                null,
                false
            )
            ->where(
                "TRIM(r.nombre_quejoso) != ''",
                null,
                false
            );
    }

    private function agruparQuejososDesdeRegistros(
        array $registros
    ): array {
        $quejosos = [];

        $reportesContados = [];

        foreach (
            $registros
            as $registro
        ) {
            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );

            if (
                $idReporte <= 0
            ) {
                continue;
            }

            $nombreNormalizado =
                $this->normalizarNombre(
                    (string) (
                        $registro['nombre_quejoso']
                        ?? ''
                    )
                );

            if (
                $nombreNormalizado === ''
            ) {
                continue;
            }

            $claveNombre =
                mb_strtoupper(
                    $nombreNormalizado,
                    'UTF-8'
                );

            $claveReporte =
                $idReporte
                . '|'
                . $claveNombre;

            if (
                isset(
                    $reportesContados[$claveReporte]
                )
            ) {
                continue;
            }

            $reportesContados[$claveReporte] =
                true;

            if (
                !isset(
                    $quejosos[$claveNombre]
                )
            ) {
                $quejosos[$claveNombre] = [

                    'nombre' =>
                        $claveNombre,

                    'total_quejas' =>
                        0,

                    'reportes' =>
                        [],

                ];
            }

            $quejosos[$claveNombre]['total_quejas']++;

            $quejosos[$claveNombre]['reportes'][$idReporte] = [

                'id_reporte' =>
                    $idReporte,

                'folio' =>
                    trim(
                        (string) (
                            $registro['folio']
                            ?? ''
                        )
                    ),

                'fecha_registro' =>
                    trim(
                        (string) (
                            $registro['fecha_registro']
                            ?? ''
                        )
                    ),

                'estado_actual' =>
                    trim(
                        (string) (
                            $registro['estado_actual']
                            ?? ''
                        )
                    ),

                'clasificacion' =>
                    trim(
                        (string) (
                            $registro['clasificacion']
                            ?? ''
                        )
                    ),

            ];
        }

        foreach (
            $quejosos
            as &$quejoso
        ) {
            uasort(
                $quejoso['reportes'],
                static function (
                    array $a,
                    array $b
                ): int {
                    $comparacionFecha =
                        strcmp(
                            (string) (
                                $b['fecha_registro']
                                ?? ''
                            ),
                            (string) (
                                $a['fecha_registro']
                                ?? ''
                            )
                        );

                    if (
                        $comparacionFecha !== 0
                    ) {
                        return $comparacionFecha;
                    }

                    return ((int) ($b['id_reporte'] ?? 0))
                        <=>
                        ((int) ($a['id_reporte'] ?? 0));
                }
            );
        }

        unset(
            $quejoso
        );


        uasort(
            $quejosos,
            static function (
                array $a,
                array $b
            ): int {
                $comparacion =
                    ((int) ($b['total_quejas'] ?? 0))
                    <=>
                    ((int) ($a['total_quejas'] ?? 0));

                if (
                    $comparacion !== 0
                ) {
                    return $comparacion;
                }

                return strcasecmp(
                    (string) (
                        $a['nombre']
                        ?? ''
                    ),
                    (string) (
                        $b['nombre']
                        ?? ''
                    )
                );
            }
        );

        return $quejosos;
    }

    private function calcularIndicadores(
        array $reportes
    ): array {
        $indicadores = [

            'total' =>
                0,

            'pendientes' =>
                0,

            'en_proceso' =>
                0,

            'finalizadas' =>
                0,

        ];


        foreach (
            $reportes
            as $reporte
        ) {
            $indicadores['total']++;

            $estado =
                trim(
                    (string) (
                        $reporte['estado_actual']
                        ?? ''
                    )
                );

            if (
                $estado === 'Pendiente'
            ) {
                $indicadores['pendientes']++;

                continue;
            }

            if (
                $estado === 'En proceso'
            ) {
                $indicadores['en_proceso']++;

                continue;
            }

            if (
                $estado === 'Finalizado'
            ) {
                $indicadores['finalizadas']++;
            }
        }


        return $indicadores;
    }

    private function agregarMotivosAHistorial(
        array $historial
    ): array {
        $motivosPorReporte =
            $this->obtenerMotivosPorReporte(
                array_column(
                    $historial,
                    'id_reporte'
                )
            );


        foreach (
            $historial
            as &$reporte
        ) {
            $idReporte =
                (int) (
                    $reporte['id_reporte']
                    ?? 0
                );

            $reporte['motivos'] =
                $motivosPorReporte[$idReporte]
                ?? [];
        }

        unset(
            $reporte
        );


        return $historial;
    }

    private function obtenerMotivosPorReporte(
        array $idsReporte
    ): array {
        $idsReporte =
            array_values(
                array_unique(
                    array_filter(
                        array_map(
                            'intval',
                            $idsReporte
                        )
                    )
                )
            );


        if (
            empty($idsReporte)
        ) {
            return [];
        }


        $registros =
            $this->db
            ->table(
                'ai_reporte_motivos rm'
            )
            ->select([
                'rm.id_reporte',
                'rm.id_motivo',
                'rm.motivo_personalizado',
                'm.motivo',
                'm.sancion AS sancion_catalogo',
                's.tipo AS sancion_registrada',
            ])
            ->join(
                'ai_cat_motivos m',
                'm.id_motivo = rm.id_motivo',
                'left'
            )
            ->join(
                'ai_reporte_sanciones s',
                's.id_reporte_motivo = rm.id_reporte_motivo
                AND s.es_actual = 1
                AND s.eliminado = 0',
                'left'
            )
            ->whereIn(
                'rm.id_reporte',
                $idsReporte
            )
            ->where(
                'rm.eliminado',
                0
            )
            ->orderBy(
                'rm.id_reporte',
                'ASC'
            )
            ->orderBy(
                'rm.id_reporte_motivo',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $motivos = [];


        foreach (
            $registros
            as $registro
        ) {
            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );

            if (
                $idReporte <= 0
            ) {
                continue;
            }

            $motivoPersonalizado =
                $this->normalizarNombre(
                    (string) (
                        $registro['motivo_personalizado']
                        ?? ''
                    )
                );

            $motivoCatalogo =
                $this->normalizarNombre(
                    (string) (
                        $registro['motivo']
                        ?? ''
                    )
                );

            $motivo =
                $motivoPersonalizado !== ''
                    ? $motivoPersonalizado
                    : $motivoCatalogo;

            if (
                $motivo === ''
            ) {
                continue;
            }

            $sancion =
                $this->normalizarSancionMotivo(
                    (string) (
                        $registro['sancion_registrada']
                        ?? ''
                    ),
                    (string) (
                        $registro['sancion_catalogo']
                        ?? ''
                    )
                );

            $motivos[$idReporte][] = [

                'id_motivo' =>
                    (int) (
                        $registro['id_motivo']
                        ?? 0
                    ),

                'motivo' =>
                    $motivo,

                'sancion' =>
                    $sancion['sancion'],

                'horas_arresto' =>
                    $sancion['horas_arresto'],

            ];
        }


        return $motivos;
    }

    private function normalizarSancionMotivo(
        string $sancionRegistrada,
        string $sancionCatalogo
    ): array {
        $textoBase =
            $this->normalizarNombre(
                $sancionRegistrada
            );


        if (
            $textoBase === ''
        ) {
            $textoBase =
                $this->normalizarNombre(
                    $sancionCatalogo
                );
        }


        $textoNormalizado =
            mb_strtoupper(
                $textoBase,
                'UTF-8'
            );


        $sancion =
            match (true) {

                str_contains(
                    $textoNormalizado,
                    'ARRESTO'
                ) =>
                    'ARRESTO',

                str_contains(
                    $textoNormalizado,
                    'AMONESTACI'
                ) =>
                    'AMONESTACION',

                str_contains(
                    $textoNormalizado,
                    'RECOMENDACI'
                ) =>
                    'RECOMENDACION',

                default =>
                    $textoBase !== ''
                        ? $textoBase
                        : 'Sin sancion',
            };


        return [

            'sancion' =>
                $sancion,

            'horas_arresto' =>
                $this->obtenerHorasArrestoDesdeSancion(
                    $textoBase
                ),

        ];
    }

    private function obtenerHorasArrestoDesdeSancion(
        string $sancion
    ): ?int {
        if (
            stripos(
                $sancion,
                'ARRESTO'
            ) === false
        ) {
            return null;
        }


        if (
            preg_match(
                '/(\d+)\s*horas?/i',
                $sancion,
                $coincidencias
            )
        ) {
            return (int) $coincidencias[1];
        }


        return null;
    }

    private function obtenerRecurrencias(
        array $idsReporte,
        array $historial
    ): array {
        $idsReporte =
            $this->normalizarIdsReporte(
                $idsReporte
            );


        if (
            empty($idsReporte)
        ) {
            return $this->recurrenciasVacias();
        }


        $foliosPorReporte =
            $this->obtenerFoliosPorReporte(
                $historial
            );


        return [

            'personal' =>
                $this->obtenerRecurrenciasPersonal(
                    $idsReporte,
                    $foliosPorReporte
                ),

            'areas' =>
                $this->obtenerRecurrenciasAreas(
                    $idsReporte,
                    $foliosPorReporte
                ),

            'sectores' =>
                $this->obtenerRecurrenciasSectores(
                    $idsReporte,
                    $foliosPorReporte
                ),

            'turnos' =>
                $this->obtenerRecurrenciasTurnos(
                    $idsReporte,
                    $foliosPorReporte
                ),

            'unidades' =>
                $this->obtenerRecurrenciasUnidades(
                    $idsReporte,
                    $foliosPorReporte
                ),

            'motivos' =>
                $this->obtenerRecurrenciasMotivos(
                    $idsReporte,
                    $foliosPorReporte
                ),

        ];
    }

    private function obtenerRecurrenciasPersonal(
        array $idsReporte,
        array $foliosPorReporte
    ): array {
        $registros =
            $this->db
            ->table(
                'ai_reporte_personal'
            )
            ->select([
                'id_reporte',
                'plantilla_id',
                'perscod',
                'nombre_snapshot',
            ])
            ->whereIn(
                'id_reporte',
                $idsReporte
            )
            ->orderBy(
                'nombre_snapshot',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $grupos = [];


        foreach (
            $registros
            as $registro
        ) {
            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );

            $plantillaId =
                (int) (
                    $registro['plantilla_id']
                    ?? 0
                );

            $perscod =
                $this->normalizarNombre(
                    (string) (
                        $registro['perscod']
                        ?? ''
                    )
                );

            $nombre =
                $this->normalizarNombre(
                    (string) (
                        $registro['nombre_snapshot']
                        ?? ''
                    )
                );

            if (
                $idReporte <= 0
                || (
                    $plantillaId <= 0
                    && $perscod === ''
                    && $nombre === ''
                )
            ) {
                continue;
            }

            if (
                $plantillaId > 0
            ) {
                $clave =
                    'plantilla:'
                    . $plantillaId;
            } elseif (
                $perscod !== ''
            ) {
                $clave =
                    'perscod:'
                    . mb_strtoupper(
                        $perscod,
                        'UTF-8'
                    );
            } else {
                $clave =
                    'nombre:'
                    . mb_strtoupper(
                        $nombre,
                        'UTF-8'
                    );
            }

            $valor =
                $nombre !== ''
                    ? $nombre
                    : (
                        $perscod !== ''
                            ? $perscod
                            : 'Personal '
                            . $plantillaId
                    );

            $this->agregarReporteARecurrencia(
                $grupos,
                $clave,
                $valor,
                $idReporte,
                $foliosPorReporte
            );
        }


        return $this->formatearRecurrencias(
            $grupos
        );
    }

    private function obtenerRecurrenciasAreas(
        array $idsReporte,
        array $foliosPorReporte
    ): array {
        return $this->obtenerRecurrenciasDesdePersonalSnapshot(
            $idsReporte,
            $foliosPorReporte,
            'area_snapshot'
        );
    }

    private function obtenerRecurrenciasTurnos(
        array $idsReporte,
        array $foliosPorReporte
    ): array {
        return $this->obtenerRecurrenciasDesdePersonalSnapshot(
            $idsReporte,
            $foliosPorReporte,
            'turno_snapshot'
        );
    }

    private function obtenerRecurrenciasDesdePersonalSnapshot(
        array $idsReporte,
        array $foliosPorReporte,
        string $campo
    ): array {
        $registros =
            $this->db
            ->table(
                'ai_reporte_personal'
            )
            ->select([
                'id_reporte',
                $campo,
            ])
            ->whereIn(
                'id_reporte',
                $idsReporte
            )
            ->where(
                $campo . ' IS NOT NULL',
                null,
                false
            )
            ->where(
                "TRIM({$campo}) != ''",
                null,
                false
            )
            ->orderBy(
                $campo,
                'ASC'
            )
            ->get()
            ->getResultArray();


        $grupos = [];


        foreach (
            $registros
            as $registro
        ) {
            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );

            $valor =
                $this->normalizarNombre(
                    (string) (
                        $registro[$campo]
                        ?? ''
                    )
                );

            if (
                $idReporte <= 0
                || $valor === ''
            ) {
                continue;
            }

            $clave =
                mb_strtoupper(
                    $valor,
                    'UTF-8'
                );

            $this->agregarReporteARecurrencia(
                $grupos,
                $clave,
                $valor,
                $idReporte,
                $foliosPorReporte
            );
        }


        return $this->formatearRecurrencias(
            $grupos
        );
    }

    private function obtenerRecurrenciasSectores(
        array $idsReporte,
        array $foliosPorReporte
    ): array {
        $registros =
            $this->db
            ->table(
                'ai_reporte_personal'
            )
            ->select([
                'id_reporte',
                'area_snapshot',
            ])
            ->whereIn(
                'id_reporte',
                $idsReporte
            )
            ->where(
                'area_snapshot IS NOT NULL',
                null,
                false
            )
            ->where(
                "TRIM(area_snapshot) != ''",
                null,
                false
            )
            ->orderBy(
                'area_snapshot',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $grupos = [];


        foreach (
            $registros
            as $registro
        ) {
            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );

            $sector =
                $this->obtenerSectorDesdeArea(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                );

            if (
                $idReporte <= 0
                || $sector === null
            ) {
                continue;
            }

            $this->agregarReporteARecurrencia(
                $grupos,
                $sector,
                $sector,
                $idReporte,
                $foliosPorReporte
            );
        }


        return $this->formatearRecurrencias(
            $grupos
        );
    }

    private function obtenerRecurrenciasUnidades(
        array $idsReporte,
        array $foliosPorReporte
    ): array {
        $registros =
            $this->db
            ->table(
                'ai_reporte_unidades'
            )
            ->select([
                'id_reporte',
                'parque_vehicular_id',
                'no_economico_snapshot',
                'placas_snapshot',
            ])
            ->whereIn(
                'id_reporte',
                $idsReporte
            )
            ->orderBy(
                'no_economico_snapshot',
                'ASC'
            )
            ->orderBy(
                'placas_snapshot',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $grupos = [];


        foreach (
            $registros
            as $registro
        ) {
            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );

            $parqueId =
                (int) (
                    $registro['parque_vehicular_id']
                    ?? 0
                );

            $noEconomico =
                $this->normalizarNombre(
                    (string) (
                        $registro['no_economico_snapshot']
                        ?? ''
                    )
                );

            $placas =
                $this->normalizarNombre(
                    (string) (
                        $registro['placas_snapshot']
                        ?? ''
                    )
                );

            if (
                $idReporte <= 0
                || (
                    $parqueId <= 0
                    && $noEconomico === ''
                    && $placas === ''
                )
            ) {
                continue;
            }

            if (
                $parqueId > 0
            ) {
                $clave =
                    'parque:'
                    . $parqueId;
            } elseif (
                $noEconomico !== ''
            ) {
                $clave =
                    'economico:'
                    . mb_strtoupper(
                        $noEconomico,
                        'UTF-8'
                    );
            } else {
                $clave =
                    'placas:'
                    . mb_strtoupper(
                        $placas,
                        'UTF-8'
                    );
            }

            if (
                $noEconomico !== ''
                && $placas !== ''
            ) {
                $valor =
                    $noEconomico
                    . ' - '
                    . $placas;
            } else {
                $valor =
                    $noEconomico !== ''
                        ? $noEconomico
                        : $placas;
            }

            $this->agregarReporteARecurrencia(
                $grupos,
                $clave,
                $valor,
                $idReporte,
                $foliosPorReporte
            );
        }


        return $this->formatearRecurrencias(
            $grupos
        );
    }

    private function obtenerRecurrenciasMotivos(
        array $idsReporte,
        array $foliosPorReporte
    ): array {
        $registros =
            $this->db
            ->table(
                'ai_reporte_motivos rm'
            )
            ->select([
                'rm.id_reporte',
                'rm.id_motivo',
                'rm.motivo_personalizado',
                'm.motivo',
            ])
            ->join(
                'ai_cat_motivos m',
                'm.id_motivo = rm.id_motivo',
                'left'
            )
            ->whereIn(
                'rm.id_reporte',
                $idsReporte
            )
            ->where(
                'rm.eliminado',
                0
            )
            ->orderBy(
                'm.motivo',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $grupos = [];


        foreach (
            $registros
            as $registro
        ) {
            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );

            $motivoPersonalizado =
                $this->normalizarNombre(
                    (string) (
                        $registro['motivo_personalizado']
                        ?? ''
                    )
                );

            $motivoCatalogo =
                $this->normalizarNombre(
                    (string) (
                        $registro['motivo']
                        ?? ''
                    )
                );

            $motivo =
                $motivoPersonalizado !== ''
                    ? $motivoPersonalizado
                    : $motivoCatalogo;

            if (
                $idReporte <= 0
                || $motivo === ''
            ) {
                continue;
            }

            $idMotivo =
                (int) (
                    $registro['id_motivo']
                    ?? 0
                );

            if (
                $idMotivo > 0
            ) {
                $clave =
                    'motivo:'
                    . $idMotivo;
            } else {
                $clave =
                    'texto:'
                    . mb_strtoupper(
                        $motivo,
                        'UTF-8'
                    );
            }

            if (
                $motivoPersonalizado !== ''
            ) {
                $clave =
                    'personalizado:'
                    . mb_strtoupper(
                        $motivoPersonalizado,
                        'UTF-8'
                    );
            }

            $this->agregarReporteARecurrencia(
                $grupos,
                $clave,
                $motivo,
                $idReporte,
                $foliosPorReporte
            );
        }


        return $this->formatearRecurrencias(
            $grupos
        );
    }

    private function agregarReporteARecurrencia(
        array &$grupos,
        string $clave,
        string $valor,
        int $idReporte,
        array $foliosPorReporte
    ): void {
        if (
            $clave === ''
            || $valor === ''
            || $idReporte <= 0
        ) {
            return;
        }

        if (
            !isset($grupos[$clave])
        ) {
            $grupos[$clave] = [

                'clave' =>
                    $clave,

                'valor' =>
                    $valor,

                'folios' =>
                    [],

                'ids_reportes' =>
                    [],

                '_reportes' =>
                    [],

            ];
        }

        if (
            isset(
                $grupos[$clave]['_reportes'][$idReporte]
            )
        ) {
            return;
        }

        $grupos[$clave]['_reportes'][$idReporte] =
            true;

        $grupos[$clave]['ids_reportes'][] =
            $idReporte;

        $grupos[$clave]['folios'][] =
            $foliosPorReporte[$idReporte]
            ?? 'Sin folio asignado';
    }

    private function formatearRecurrencias(
        array $grupos
    ): array {
        $resultado = [];

        foreach (
            $grupos
            as $grupo
        ) {
            $idsReporte =
                array_values(
                    array_map(
                        'intval',
                        $grupo['ids_reportes']
                        ?? []
                    )
                );

            $cantidad =
                count(
                    $idsReporte
                );

            if (
                $cantidad <= 0
            ) {
                continue;
            }

            $resultado[] = [

                'clave' =>
                    (string) (
                        $grupo['clave']
                        ?? ''
                    ),

                'valor' =>
                    (string) (
                        $grupo['valor']
                        ?? ''
                    ),

                'cantidad' =>
                    $cantidad,

                'folios' =>
                    array_values(
                        $grupo['folios']
                        ?? []
                    ),

                'ids_reportes' =>
                    $idsReporte,

                'recurrente' =>
                    $cantidad >= 2,

            ];
        }

        usort(
            $resultado,
            static function (
                array $a,
                array $b
            ): int {
                $comparacion =
                    ((int) ($b['cantidad'] ?? 0))
                    <=>
                    ((int) ($a['cantidad'] ?? 0));

                if (
                    $comparacion !== 0
                ) {
                    return $comparacion;
                }

                return strcasecmp(
                    (string) (
                        $a['valor']
                        ?? ''
                    ),
                    (string) (
                        $b['valor']
                        ?? ''
                    )
                );
            }
        );

        return $resultado;
    }

    private function obtenerFoliosPorReporte(
        array $historial
    ): array {
        $folios = [];

        foreach (
            $historial
            as $reporte
        ) {
            $idReporte =
                (int) (
                    $reporte['id_reporte']
                    ?? 0
                );

            if (
                $idReporte <= 0
            ) {
                continue;
            }

            $folio =
                $this->normalizarNombre(
                    (string) (
                        $reporte['folio']
                        ?? ''
                    )
                );

            $folios[$idReporte] =
                $folio !== ''
                    ? $folio
                    : 'Sin folio asignado';
        }

        return $folios;
    }

    private function normalizarIdsReporte(
        array $idsReporte
    ): array {
        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $idsReporte
                    ),
                    static fn (
                        int $idReporte
                    ): bool =>
                        $idReporte > 0
                )
            )
        );
    }

    private function recurrenciasVacias(): array
    {
        return [

            'personal' =>
                [],

            'areas' =>
                [],

            'sectores' =>
                [],

            'turnos' =>
                [],

            'unidades' =>
                [],

            'motivos' =>
                [],

        ];
    }

    private function obtenerSectorDesdeArea(
        string $area
    ): ?string {
        $area =
            mb_strtoupper(
                $this->normalizarNombre(
                    $area
                ),
                'UTF-8'
            );

        if (
            $area === ''
        ) {
            return null;
        }

        if (
            !preg_match(
                '/^SECTOR\s+0*([0-9]+)\b/u',
                $area,
                $coincidencias
            )
        ) {
            return null;
        }

        $numero =
            (int) (
                $coincidencias[1]
                ?? 0
            );

        if (
            $numero < 1
            || $numero > 15
        ) {
            return null;
        }

        return
            'SECTOR '
            . $numero;
    }

    private function crearClaveSeleccion(
        string $nombreNormalizado
    ): string {
        return hash_hmac(
            'sha256',
            $nombreNormalizado,
            $this->obtenerSecretoSeleccion()
        );
    }

    private function obtenerSecretoSeleccion(): string
    {
        if (
            $this->secretoSeleccion !== null
        ) {
            return $this->secretoSeleccion;
        }

        $secreto =
            trim(
                (string) env(
                    'REPORTES_QUEJOSOS_HMAC_SECRET',
                    ''
                )
            );

        if (
            $secreto === ''
        ) {
            log_message(
                'critical',
                'Falta configurar REPORTES_QUEJOSOS_HMAC_SECRET para el analisis individual de quejosos.'
            );

            throw new \RuntimeException(
                'Configuracion de seleccion de quejosos incompleta.'
            );
        }

        if (
            !preg_match(
                '/\A[0-9a-f]{64,}\z/i',
                $secreto
            )
            || strlen($secreto) % 2 !== 0
        ) {
            log_message(
                'critical',
                'REPORTES_QUEJOSOS_HMAC_SECRET tiene un formato invalido.'
            );

            throw new \RuntimeException(
                'Configuracion de seleccion de quejosos invalida.'
            );
        }

        $secretoBinario =
            hex2bin(
                $secreto
            );

        if (
            $secretoBinario === false
            || strlen($secretoBinario) < 32
        ) {
            log_message(
                'critical',
                'REPORTES_QUEJOSOS_HMAC_SECRET no cumple la longitud minima requerida.'
            );

            throw new \RuntimeException(
                'Configuracion de seleccion de quejosos invalida.'
            );
        }

        $this->secretoSeleccion =
            $secretoBinario;

        return $this->secretoSeleccion;
    }

    private function respuestaVacia(
        string $claveSeleccion
    ): array {
        return [

            'quejoso' => [

                'clave' =>
                    $claveSeleccion,

                'nombre' =>
                    null,

            ],

            'total_quejas' =>
                0,

            'indicadores' => [

                'total' =>
                    0,

                'pendientes' =>
                    0,

                'en_proceso' =>
                    0,

                'finalizadas' =>
                    0,

            ],

            'historial' =>
                [],

            'ids_reportes' =>
                [],

            'recurrencias' =>
                $this->recurrenciasVacias(),

        ];
    }

    private function normalizarNombre(
        string $texto
    ): string {
        return trim(
            preg_replace(
                '/\s+/u',
                ' ',
                $texto
            )
            ?? ''
        );
    }
}
