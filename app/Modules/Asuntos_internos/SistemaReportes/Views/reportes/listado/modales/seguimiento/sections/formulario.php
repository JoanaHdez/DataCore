<!-- =================================================
     NUEVO SEGUIMIENTO
================================================== -->
<div class="seguimiento-reporte__section">

    <div class="seguimiento-reporte__section-header">

        <span id="seguimiento-form-eyebrow">
            Nuevo movimiento
        </span>

        <h3 id="seguimiento-form-titulo">
            Registrar seguimiento
        </h3>

    </div>


    <!--
        Se utilizará después para Editar seguimiento.
        Por ahora permanece vacío.
    -->
    <input type="hidden" id="seguimiento-id-edicion" name="id_seguimiento_edicion" value="">


    <div class="seguimiento-reporte-grid">

        <!-- FECHA -->
        <div class="editar-reporte-campo">

            <label for="seguimiento-fecha">
                Fecha
            </label>

            <input type="date" id="seguimiento-fecha" name="fecha" required>

        </div>


        <!-- =================================================
            TIPO DE SEGUIMIENTO
        ================================================== -->

        <div class="editar-reporte-campo">

            <label>
                Tipo de seguimiento
            </label>


            <!-- =================================================
                VALOR REAL PARA BACKEND
            ================================================== -->

            <input type="hidden" id="seguimiento-tipo" name="id_tipo_seguimiento" value="" required>


            <!-- =================================================
                CONTENEDOR DEL CATÁLOGO
            ================================================== -->

            <div class="seguimiento-catalogo seguimiento-catalogo--tipo">


                <!-- =============================================
                    SELECTOR VISUAL
                ============================================== -->

                <button type="button" class="seguimiento-select" id="seguimiento-tipo-select" aria-expanded="false"
                    aria-controls="seguimiento-tipo-resultados">

                    <span class="seguimiento-select__texto" id="seguimiento-tipo-select-texto">
                        Selecciona
                    </span>


                    <span class="seguimiento-select__flecha" aria-hidden="true">
                        ▾
                    </span>

                </button>


                <!-- =============================================
                    CATÁLOGO
                ============================================== -->

                <div class="seguimiento-resultados" id="seguimiento-tipo-resultados" hidden>


                    <?php
                    $tiposSeguimiento =
                        $tiposSeguimiento
                        ?? [];
                    ?>

                    <?php foreach ($tiposSeguimiento as $tipoSeguimiento): ?>
                        <?php
                        $idTipoSeguimiento =
                            (int) (
                                $tipoSeguimiento['id_tipo_seguimiento']
                                ?? 0
                            );

                        $nombreTipoSeguimiento =
                            trim(
                                (string) (
                                    $tipoSeguimiento['nombre']
                                    ?? ''
                                )
                            );

                        if (
                            $idTipoSeguimiento <= 0
                            || $nombreTipoSeguimiento === ''
                        ) {
                            continue;
                        }

                        $inicialTipoSeguimiento =
                            mb_substr(
                                $nombreTipoSeguimiento,
                                0,
                                1,
                                'UTF-8'
                            );

                        $descripcionTipoSeguimiento =
                            $nombreTipoSeguimiento === 'ACTUALIZACIÓN'
                                ? 'Actualización del seguimiento'
                                : (
                                    $nombreTipoSeguimiento === 'INVESTIGACIÓN'
                                        ? 'Avance relacionado con investigación'
                                        : (
                                            $nombreTipoSeguimiento === 'RESOLUCIÓN'
                                                ? 'Movimiento relacionado con resolución'
                                                : 'Otro tipo de seguimiento'
                                        )
                                );
                        ?>

                        <button type="button" class="seguimiento-resultados__item" data-seguimiento-tipo-opcion
                            data-valor="<?= esc((string) $idTipoSeguimiento) ?>"
                            data-nombre="<?= esc($nombreTipoSeguimiento) ?>"
                            data-es-otro="<?= $nombreTipoSeguimiento === 'OTRO' ? '1' : '0' ?>">

                            <span class="seguimiento-resultados__avatar">
                                <?= esc($inicialTipoSeguimiento) ?>
                            </span>

                            <span class="seguimiento-resultados__datos">

                                <strong>
                                    <?= esc($nombreTipoSeguimiento) ?>
                                </strong>

                                <small>
                                    <?= esc($descripcionTipoSeguimiento) ?>
                                </small>

                            </span>

                        </button>
                    <?php endforeach; ?>

                </div>

            </div>

        </div>


        <div class="editar-reporte-campo seguimiento-tipo-otro" id="seguimiento-campo-tipo-otro" hidden>

            <label for="seguimiento-tipo-otro">
                Especifique el tipo de seguimiento
            </label>

            <input type="text" id="seguimiento-tipo-otro" name="tipo_otro"
                placeholder="Describe el tipo de seguimiento" autocomplete="off" maxlength="255" disabled>

        </div>


        <!-- FOLIO IP -->
        <div class="editar-reporte-campo">

            <label for="seguimiento-folio-ip">
                Folio IP
            </label>

            <input type="text" id="seguimiento-folio-ip" name="folio_ip" placeholder="Ingresa el folio IP"
                autocomplete="off" maxlength="100">

            <small>
                Campo opcional.
            </small>

        </div>


        <!-- =================================================
            ESTADO RESULTANTE
        ================================================== -->

        <div class="editar-reporte-campo">

            <label>
                Estado resultante
            </label>


            <!-- =================================================
                VALOR REAL PARA BACKEND
            ================================================== -->

            <input type="hidden" id="seguimiento-estado" name="estado" value="" required>


            <!-- =================================================
                CONTENEDOR DEL CATÁLOGO
            ================================================== -->

            <div class="seguimiento-catalogo seguimiento-catalogo--estado">


                <!-- =============================================
                    SELECTOR VISUAL
                ============================================== -->

                <button type="button" class="seguimiento-select" id="seguimiento-estado-select" aria-expanded="false"
                    aria-controls="seguimiento-estado-resultados">

                    <span class="seguimiento-select__texto" id="seguimiento-estado-select-texto">
                        Selecciona
                    </span>


                    <span class="seguimiento-select__flecha" aria-hidden="true">
                        ▾
                    </span>

                </button>


                <!-- =============================================
                    CATÁLOGO
                ============================================== -->

                <div class="seguimiento-resultados" id="seguimiento-estado-resultados" hidden>


                    <!-- PENDIENTE -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-estado-opcion
                        data-valor="Pendiente">

                        <span class="seguimiento-resultados__avatar">
                            P
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Pendiente
                            </strong>

                            <small>
                                Seguimiento pendiente de atención
                            </small>

                        </span>

                    </button>


                    <!-- EN PROCESO -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-estado-opcion
                        data-valor="En proceso">

                        <span class="seguimiento-resultados__avatar">
                            EP
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                En proceso
                            </strong>

                            <small>
                                Seguimiento actualmente en atención
                            </small>

                        </span>

                    </button>


                    <!-- FINALIZADO -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-estado-opcion
                        data-valor="Finalizado">

                        <span class="seguimiento-resultados__avatar">
                            F
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Finalizado
                            </strong>

                            <small>
                                Seguimiento concluido
                            </small>

                        </span>

                    </button>

                </div>

            </div>

        </div>


        <!-- =================================================
            SANCIÓN DISCIPLINARIA
        ================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full seguimiento-campo-sancion">

            <label>
                Sanción disciplinaria
            </label>


            <!-- =================================================
                VALOR REAL PARA BACKEND
            ================================================== -->

            <input type="hidden" id="seguimiento-sancion" name="id_sancion_seguimiento" value="">


            <!-- =================================================
                CONTENEDOR DEL CATÁLOGO
            ================================================== -->

            <div class="seguimiento-catalogo seguimiento-catalogo--sancion">


                <!-- =============================================
                    SELECTOR VISUAL
                ============================================== -->

                <button type="button" class="seguimiento-select" id="seguimiento-sancion-select" aria-expanded="false"
                    aria-controls="seguimiento-sancion-resultados">

                    <span class="seguimiento-select__texto" id="seguimiento-sancion-select-texto">
                        Sin cambio
                    </span>


                    <span class="seguimiento-select__flecha" aria-hidden="true">
                        ▾
                    </span>

                </button>


                <!-- =============================================
                    CATÁLOGO
                ============================================== -->

                <div class="seguimiento-resultados" id="seguimiento-sancion-resultados" hidden>


                    <?php
                    $sancionesSeguimiento =
                        $sancionesSeguimiento
                        ?? [];
                    ?>

                    <?php foreach ($sancionesSeguimiento as $sancionSeguimiento): ?>
                        <?php
                        $idSancionSeguimiento =
                            (int) (
                                $sancionSeguimiento['id_sancion_seguimiento']
                                ?? 0
                            );

                        $nombreSancionSeguimiento =
                            trim(
                                (string) (
                                    $sancionSeguimiento['nombre']
                                    ?? ''
                                )
                            );

                        if (
                            $idSancionSeguimiento <= 0
                            || $nombreSancionSeguimiento === ''
                        ) {
                            continue;
                        }

                        $inicialSancionSeguimiento =
                            preg_replace(
                                '/[^A-ZÁÉÍÓÚÑ]/u',
                                '',
                                $nombreSancionSeguimiento
                            );

                        $inicialSancionSeguimiento =
                            mb_substr(
                                $inicialSancionSeguimiento ?: $nombreSancionSeguimiento,
                                0,
                                2,
                                'UTF-8'
                            );
                        ?>

                        <button type="button" class="seguimiento-resultados__item" data-seguimiento-sancion-opcion
                            data-valor="<?= esc((string) $idSancionSeguimiento) ?>"
                            data-nombre="<?= esc($nombreSancionSeguimiento) ?>">

                            <span class="seguimiento-resultados__avatar">
                                <?= esc($inicialSancionSeguimiento) ?>
                            </span>

                            <span class="seguimiento-resultados__datos">

                                <strong>
                                    <?= esc($nombreSancionSeguimiento) ?>
                                </strong>

                                <small>
                                    Sanción disciplinaria de seguimiento
                                </small>

                            </span>

                        </button>
                    <?php endforeach; ?>

                </div>

            </div>


            <small>
                Selecciona una opción únicamente si la sanción vigente cambia como resultado de este seguimiento.
            </small>

        </div>


        <!-- OBSERVACIONES -->
        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="seguimiento-observaciones">
                Observaciones
            </label>

            <textarea id="seguimiento-observaciones" name="observaciones" class="seguimiento-reporte__textarea" rows="5"
                placeholder="Describe el seguimiento realizado..." required></textarea>

        </div>

    </div>


    <!-- Preparado para Editar seguimiento -->
    <div id="seguimiento-acciones-edicion" hidden style="display: none;">

        <button type="button" id="seguimiento-cancelar-edicion"
            class="modal-reporte__button modal-reporte__button--secondary">
            Cancelar edición
        </button>

    </div>

</div>
