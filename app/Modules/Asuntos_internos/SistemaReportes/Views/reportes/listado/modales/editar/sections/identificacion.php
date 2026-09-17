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
        <div class="editar-reporte-campo">

            <label for="editar-expediente">
                Expediente
            </label>

            <input type="text" id="editar-expediente" name="expediente" placeholder="Ingresa el número de expediente"
                autocomplete="off">

        </div>


        <!-- NOMENCLATURA -->
        <div class="editar-reporte-campo">

            <label for="editar-nomenclatura-parte">
                Nomenclatura
            </label>

            <div class="editar-nomenclatura">

                <span class="editar-nomenclatura__prefijo" id="editar-nomenclatura-prefijo">
                    CGSC/CAI/QJ/
                </span>

                <input type="text" id="editar-nomenclatura-parte" class="editar-nomenclatura__input"
                    placeholder="Ej. 1295/2026" autocomplete="off">

            </div>

            <small>
                Captura únicamente la parte final de la nomenclatura.
            </small>

            <!-- VALOR COMPLETO QUE SE ENVÍA AL BACKEND -->
            <input type="hidden" id="editar-nomenclatura" name="nomenclatura" value="">

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