<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Datos generales
        </span>

        <h3>
            Identificación del registro
        </h3>

    </div>


    <div class="editar-reporte-grid">

        <!-- FOLIO IP -->
        <div class="editar-reporte-campo">

            <label for="editar-folio-ip">
                Folio IP
            </label>

            <input type="text" id="editar-folio-ip" name="folio_ip" placeholder="Ingresa el folio IP"
                autocomplete="off">

        </div>


        <!-- FECHA DE QUEJA -->
        <div class="editar-reporte-campo">

            <label for="editar-fecha-queja">
                Fecha de queja
                <span class="required">*</span>
            </label>

            <input type="date" id="editar-fecha-queja" name="fecha_queja" required>

        </div>


        <!-- FECHA DE ACUERDO -->
        <div class="editar-reporte-campo">

            <label for="editar-fecha-acuerdo">
                Fecha de acuerdo
            </label>

            <input type="date" id="editar-fecha-acuerdo" name="fecha_acuerdo">

        </div>


        <!-- EXPEDIENTE -->
        <div class="report-field">

            <label for="expediente">
                Expediente
            </label>

            <input type="text" id="expediente" name="expediente" class="report-input"
                placeholder="Ingresa el número de expediente">

        </div>


        <!-- NOMENCLATURA -->
        <div class="editar-reporte-campo">

            <label for="editar-nomenclatura">
                Nomenclatura
            </label>

            <input type="text" id="editar-nomenclatura" name="nomenclatura" readonly>

            <small>
                Se genera automáticamente con el número de folio asignado.
            </small>

        </div>


        <!-- NÚMERO DE OFICIO -->
        <div class="editar-reporte-campo">

            <label for="editar-no-oficio">
                No. de oficio
            </label>

            <input type="text" id="editar-no-oficio" name="no_oficio" placeholder="Ingresa el número de oficio"
                autocomplete="off">

        </div>

    </div>

</div>