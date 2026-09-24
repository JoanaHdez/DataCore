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

            <input type="hidden" id="seguimiento-tipo" name="tipo" value="" required>


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


                    <!-- ACTUALIZACIÓN -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-tipo-opcion
                        data-valor="Actualización">

                        <span class="seguimiento-resultados__avatar">
                            A
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Actualización
                            </strong>

                            <small>
                                Actualización del seguimiento
                            </small>

                        </span>

                    </button>


                    <!-- INVESTIGACIÓN -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-tipo-opcion
                        data-valor="Investigación">

                        <span class="seguimiento-resultados__avatar">
                            I
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Investigación
                            </strong>

                            <small>
                                Avance relacionado con investigación
                            </small>

                        </span>

                    </button>


                    <!-- TURNADO -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-tipo-opcion
                        data-valor="Turnado">

                        <span class="seguimiento-resultados__avatar">
                            T
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Turnado
                            </strong>

                            <small>
                                Reporte turnado para atención
                            </small>

                        </span>

                    </button>


                    <!-- RESOLUCIÓN -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-tipo-opcion
                        data-valor="Resolución">

                        <span class="seguimiento-resultados__avatar">
                            R
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Resolución
                            </strong>

                            <small>
                                Movimiento relacionado con resolución
                            </small>

                        </span>

                    </button>


                    <!-- OTRO -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-tipo-opcion
                        data-valor="Otro">

                        <span class="seguimiento-resultados__avatar">
                            O
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Otro
                            </strong>

                            <small>
                                Otro tipo de seguimiento
                            </small>

                        </span>

                    </button>

                </div>

            </div>

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

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label>
                Sanción disciplinaria
            </label>


            <!-- =================================================
                VALOR REAL PARA BACKEND
            ================================================== -->

            <input type="hidden" id="seguimiento-sancion" name="sancion_disciplinaria" value="">


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


                    <!-- SIN CAMBIO -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-sancion-opcion
                        data-valor="">

                        <span class="seguimiento-resultados__avatar">
                            —
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Sin cambio
                            </strong>

                            <small>
                                Mantener la sanción vigente
                            </small>

                        </span>

                    </button>


                    <!-- ARRESTO -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-sancion-opcion
                        data-valor="Arresto">

                        <span class="seguimiento-resultados__avatar">
                            A
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Arresto
                            </strong>

                            <small>
                                Sanción disciplinaria de arresto
                            </small>

                        </span>

                    </button>


                    <!-- AMONESTACIÓN -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-sancion-opcion
                        data-valor="Amonestación">

                        <span class="seguimiento-resultados__avatar">
                            AM
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Amonestación
                            </strong>

                            <small>
                                Sanción disciplinaria de amonestación
                            </small>

                        </span>

                    </button>


                    <!-- OTRO -->

                    <button type="button" class="seguimiento-resultados__item" data-seguimiento-sancion-opcion
                        data-valor="Otro">

                        <span class="seguimiento-resultados__avatar">
                            O
                        </span>

                        <span class="seguimiento-resultados__datos">

                            <strong>
                                Otro
                            </strong>

                            <small>
                                Otra sanción disciplinaria
                            </small>

                        </span>

                    </button>

                </div>

            </div>


            <small>
                Selecciona una opción únicamente si la sanción vigente cambia como resultado de este seguimiento.
            </small>

        </div>


        <!-- ESPECIFICAR OTRO -->
        <div class="editar-reporte-campo editar-reporte-campo--full" id="seguimiento-campo-sancion-otro" hidden
            style="display: none;">

            <label for="seguimiento-sancion-otro">
                Especifique la sanción
                <span class="required">*</span>
            </label>

            <input type="text" id="seguimiento-sancion-otro" name="sancion_otro"
                placeholder="Ingresa la sanción correspondiente" autocomplete="off" maxlength="255" disabled>

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