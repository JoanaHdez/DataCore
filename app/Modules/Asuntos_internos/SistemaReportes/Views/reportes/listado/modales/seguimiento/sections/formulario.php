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


        <!-- ESTADO RESULTANTE -->
        <div class="editar-reporte-campo">

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