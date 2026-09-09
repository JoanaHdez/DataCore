<section class="felicitaciones-tabla">

    <!-- =========================================================
         ENCABEZADO
    ========================================================== -->

    <div class="felicitaciones-tabla__header">

        <div>

            <span class="felicitaciones-tabla__eyebrow">
                Registros
            </span>

            <h2 class="felicitaciones-tabla__title">
                Felicitaciones registradas
            </h2>

            <p class="felicitaciones-tabla__description">
                Consulta las felicitaciones y accede a sus acciones disponibles.
            </p>

        </div>

    </div>


    <!-- =========================================================
         TABLA
    ========================================================== -->

    <div class="felicitaciones-tabla__container">

        <table class="felicitaciones-tabla__table">

            <thead>

                <tr>

                    <th class="felicitaciones-tabla__numero">
                        No.
                    </th>

                    <th class="felicitaciones-tabla__folio">
                        Folio
                    </th>

                    <th class="felicitaciones-tabla__fecha">
                        Fecha
                    </th>

                    <th>
                        Felicitante
                    </th>

                    <th class="felicitaciones-tabla__acciones-columna">
                        Acciones
                    </th>

                </tr>

            </thead>


            <tbody id="tabla-felicitaciones-body">

                <?php if (!empty($felicitaciones)): ?>

                <?php foreach ($felicitaciones as $indice => $felicitacion): ?>

                <?php

                        /* =============================================
                           DATOS DEL REGISTRO
                        ============================================= */

                        $idFelicitacion =
                            (int) (
                                $felicitacion['id_felicitacion']
                                ?? 0
                            );


                        $folio =
                            trim(
                                (string) (
                                    $felicitacion['folio']
                                    ?? ''
                                )
                            );


                        $fecha =
                            trim(
                                (string) (
                                    $felicitacion['fecha_registro']
                                    ?? ''
                                )
                            );


                        $felicitante =
                            trim(
                                (string) (
                                    $felicitacion['nombre_felicitante']
                                    ?? ''
                                )
                            );


                        /* =============================================
                           DATOS INTERNOS PARA FILTROS
                        ============================================= */

                        $fechaFiltro =
                            trim(
                                (string) (
                                    $felicitacion['fecha_filtro']
                                    ?? ''
                                )
                            );


                        $personalFiltro =
                            trim(
                                (string) (
                                    $felicitacion['filtro_personal']
                                    ?? ''
                                )
                            );


                        $aliasesFiltro =
                            trim(
                                (string) (
                                    $felicitacion['filtro_aliases']
                                    ?? ''
                                )
                            );


                        $areasFiltro =
                            trim(
                                (string) (
                                    $felicitacion['filtro_areas']
                                    ?? ''
                                )
                            );


                        $turnosFiltro =
                            trim(
                                (string) (
                                    $felicitacion['filtro_turnos']
                                    ?? ''
                                )
                            );


                        $sectoresFiltro =
                            trim(
                                (string) (
                                    $felicitacion['filtro_sectores']
                                    ?? ''
                                )
                            );

                        $personalIds =
                            [];


                        $perscods =
                            [];


                        foreach (
                            ($felicitacion['personal'] ?? [])
                            as $persona
                        ) {

                            $plantillaId =
                                (int) (
                                    $persona['plantilla_id']
                                    ?? 0
                                );


                            if (
                                $plantillaId > 0
                                && !in_array(
                                    $plantillaId,
                                    $personalIds,
                                    true
                                )
                            ) {

                                $personalIds[] =
                                    $plantillaId;
                            }


                            $perscod =
                                trim(
                                    (string) (
                                        $persona['perscod']
                                        ?? ''
                                    )
                                );


                            if (
                                $perscod !== ''
                                && !in_array(
                                    $perscod,
                                    $perscods,
                                    true
                                )
                            ) {

                                $perscods[] =
                                    $perscod;
                            }
                        }


                        $personalIdsFiltro =
                            implode(
                                '|',
                                $personalIds
                            );


                        $perscodsFiltro =
                            implode(
                                '|',
                                $perscods
                            );

                        ?>


                <tr data-id-felicitacion="<?= $idFelicitacion ?>" data-folio="<?= esc($folio) ?>"
                    data-fecha="<?= esc($fechaFiltro) ?>" data-felicitante="<?= esc($felicitante) ?>"
                    data-personal="<?= esc($personalFiltro) ?>" data-personal-ids="<?= esc($personalIdsFiltro) ?>"
                    data-perscods="<?= esc($perscodsFiltro) ?>" data-aliases="<?= esc($aliasesFiltro) ?>"
                    data-areas="<?= esc($areasFiltro) ?>" data-turnos="<?= esc($turnosFiltro) ?>"
                    data-sectores="<?= esc($sectoresFiltro) ?>">

                    <!-- =====================================
                                 NO.
                            ====================================== -->

                    <td class="felicitaciones-tabla__numero">

                        <?= $indice + 1 ?>

                    </td>


                    <!-- =====================================
                                 FOLIO
                            ====================================== -->

                    <td class="felicitaciones-tabla__folio">

                        <strong>
                            <?= esc(
                                        $folio
                                            ?: '—'
                                    ) ?>
                        </strong>

                    </td>


                    <!-- =====================================
                                 FECHA
                            ====================================== -->

                    <td class="felicitaciones-tabla__fecha">

                        <?= esc(
                                    $fecha
                                        ?: '—'
                                ) ?>

                    </td>


                    <!-- =====================================
                                 FELICITANTE
                            ====================================== -->

                    <td class="felicitaciones-tabla__felicitante">

                        <?= esc(
                                    $felicitante
                                        ?: '—'
                                ) ?>

                    </td>


                    <!-- =====================================
                                 ACCIONES
                            ====================================== -->

                    <td>

                        <div class="felicitaciones-tabla__acciones">

                            <!-- VER -->

                            <button type="button" class="felicitaciones-tabla__accion" data-accion-felicitacion="ver"
                                data-id-felicitacion="<?= $idFelicitacion ?>" data-folio="<?= esc($folio) ?>">
                                Ver
                            </button>


                            <!-- EDITAR -->

                            <button type="button" class="felicitaciones-tabla__accion" data-accion-felicitacion="editar"
                                data-id-felicitacion="<?= $idFelicitacion ?>" data-folio="<?= esc($folio) ?>">
                                Editar
                            </button>


                            <!-- ELIMINAR -->

                            <button type="button" class="felicitaciones-tabla__accion" data-accion-felicitacion="eliminar" 
                                data-id-felicitacion="<?= $idFelicitacion ?>"data-folio="<?= esc($folio) ?>">
                                Eliminar
                            </button>

                        </div>

                    </td>

                </tr>

                <?php endforeach; ?>


                <?php else: ?>

                <!-- =============================================
                         SIN REGISTROS
                    ============================================== -->

                <tr class="felicitaciones-tabla__empty">

                    <td colspan="5">

                        <div class="felicitaciones-tabla__empty-content">

                            <strong>
                                No hay felicitaciones para mostrar
                            </strong>

                            <span>
                                Las felicitaciones aparecerán aquí cuando existan registros disponibles.
                            </span>

                        </div>

                    </td>

                </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>