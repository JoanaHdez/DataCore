<div class="modal-reporte" id="modal-seguimiento-reporte" aria-hidden="true">

    <div class="modal-reporte__overlay" data-cerrar-modal-seguimiento></div>

    <div class="modal-reporte__dialog modal-reporte__dialog--seguimiento" role="dialog" aria-modal="true"
        aria-labelledby="modal-seguimiento-titulo">

        <!-- =====================================================
             HEADER
        ====================================================== -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Seguimiento del caso
                </span>

                <h2 class="modal-reporte__title" id="modal-seguimiento-titulo">
                    Seguimiento
                </h2>

            </div>

            <button type="button" class="modal-reporte__close" data-cerrar-modal-seguimiento aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =====================================================
             FORMULARIO
        ====================================================== -->
        <form class="modal-reporte__form modal-reporte__form--seguimiento" id="form-seguimiento-reporte">

            <?= csrf_field() ?>
            
            <div class="modal-reporte__body modal-reporte__body--seguimiento">

                <!-- =================================================
                     INFORMACIÓN DEL REPORTE
                ================================================== -->
                <div class="seguimiento-reporte__info">

                    <div class="seguimiento-reporte__dato">

                        <span>
                            Folio
                        </span>

                        <strong id="seguimiento-folio">
                            —
                        </strong>

                    </div>


                    <div class="seguimiento-reporte__dato">

                        <span>
                            Expediente
                        </span>

                        <strong id="seguimiento-expediente">
                            —
                        </strong>

                    </div>


                    <div class="seguimiento-reporte__dato">

                        <span>
                            Estado actual
                        </span>

                        <strong id="seguimiento-estado-actual">
                            —
                        </strong>

                    </div>


                    <!-- SANCIÓN ACTUAL -->
                    <div class="seguimiento-reporte__dato">

                        <span>
                            Sanción actual
                        </span>

                        <strong id="seguimiento-sancion-actual">
                            Sin sanción registrada
                        </strong>

                        <small id="seguimiento-sancion-origen" class="editar-reporte-campo__aviso" hidden
                            style="display: none;"></small>

                    </div>

                </div>


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


                        <!-- TIPO DE SEGUIMIENTO -->
                        <div class="editar-reporte-campo">

                            <label for="seguimiento-tipo">
                                Tipo de seguimiento
                            </label>

                            <select id="seguimiento-tipo" name="tipo" required>

                                <option value="">
                                    Selecciona
                                </option>

                                <option value="Actualización">
                                    Actualización
                                </option>

                                <option value="Investigación">
                                    Investigación
                                </option>

                                <option value="Turnado">
                                    Turnado
                                </option>

                                <option value="Resolución">
                                    Resolución
                                </option>

                                <option value="Otro">
                                    Otro
                                </option>

                            </select>

                        </div>


                        <!-- ESTADO RESULTANTE -->
                        <div class="editar-reporte-campo editar-reporte-campo--full">

                            <label for="seguimiento-estado">
                                Estado resultante
                            </label>

                            <select id="seguimiento-estado" name="estado" required>

                                <option value="">
                                    Selecciona
                                </option>

                                <option value="Pendiente">
                                    Pendiente
                                </option>

                                <option value="En proceso">
                                    En proceso
                                </option>

                                <option value="Finalizado">
                                    Finalizado
                                </option>

                            </select>

                        </div>


                        <!-- =================================================
                             SANCIÓN DISCIPLINARIA
                        ================================================== -->
                        <div class="editar-reporte-campo editar-reporte-campo--full">

                            <label for="seguimiento-sancion">
                                Sanción disciplinaria
                            </label>

                            <select id="seguimiento-sancion" name="sancion_disciplinaria">

                                <option value="">
                                    Sin cambio
                                </option>

                                <option value="Arresto">
                                    Arresto
                                </option>

                                <option value="Amonestación">
                                    Amonestación
                                </option>

                                <option value="Otro">
                                    Otro
                                </option>

                            </select>

                            <small>
                                Selecciona una opción únicamente si la sanción vigente cambia como resultado de este
                                seguimiento.
                            </small>

                        </div>


                        <!-- ESPECIFICAR OTRO -->
                        <div class="editar-reporte-campo editar-reporte-campo--full" id="seguimiento-campo-sancion-otro"
                            hidden style="display: none;">

                            <label for="seguimiento-sancion-otro">
                                Especifique la sanción
                                <span class="required">*</span>
                            </label>

                            <input type="text" id="seguimiento-sancion-otro" name="sancion_otro"
                                placeholder="Ingresa la sanción correspondiente" autocomplete="off" maxlength="255"
                                disabled>

                        </div>


                        <!-- OBSERVACIONES -->
                        <div class="editar-reporte-campo editar-reporte-campo--full">

                            <label for="seguimiento-observaciones">
                                Observaciones
                            </label>

                            <textarea id="seguimiento-observaciones" name="observaciones"
                                class="seguimiento-reporte__textarea" rows="5"
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


                <!-- =================================================
                     HISTORIAL DE SEGUIMIENTOS
                ================================================== -->
                <div class="seguimiento-historial">

                    <div class="seguimiento-historial__header">

                        <span>
                            Historial
                        </span>

                        <h3>
                            Seguimientos registrados
                        </h3>

                        <p>
                            Consulta los movimientos realizados sobre este reporte.
                        </p>

                    </div>


                    <div class="seguimiento-historial__lista" id="seguimiento-historial-lista">

                        <div class="seguimiento-historial__vacio">

                            <strong>
                                Sin seguimientos registrados
                            </strong>

                            <span>
                                Los movimientos del reporte aparecerán aquí.
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 FOOTER
            ====================================================== -->
            <div class="modal-reporte__footer">

                <button type="button" class="modal-reporte__button modal-reporte__button--secondary"
                    data-cerrar-modal-seguimiento>
                    Cancelar
                </button>

                <button type="submit" id="seguimiento-boton-guardar"
                    class="modal-reporte__button modal-reporte__button--primary">
                    Registrar seguimiento
                </button>

            </div>

        </form>

    </div>

</div>