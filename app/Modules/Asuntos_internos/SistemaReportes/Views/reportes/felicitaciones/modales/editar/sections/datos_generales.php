<section class="modal-felicitacion__seccion">

    <div class="modal-felicitacion__seccion-header">

        <span class="modal-felicitacion__eyebrow">
            Información general
        </span>

        <h3 class="modal-felicitacion__seccion-title">
            Identificación de la felicitación
        </h3>

    </div>


    <div class="modal-felicitacion__grid">

        <!-- =================================================
             FOLIO
        ================================================== -->

        <div class="modal-felicitacion__campo">

            <label for="editar-felicitacion-folio">
                Folio
            </label>

            <input type="text" id="editar-felicitacion-folio" class="report-input report-input--readonly" readonly>

        </div>


        <!-- =================================================
             FECHA
        ================================================== -->

        <div class="modal-felicitacion__campo">

            <label for="editar-felicitacion-fecha">
                Fecha de registro
            </label>

            <input type="text" id="editar-felicitacion-fecha" name="fecha_registro"
                class="report-input report-input--readonly" readonly>

        </div>


        <!-- =================================================
             NOMENCLATURA
        ================================================== -->

        <div class="modal-felicitacion__campo modal-felicitacion__campo--full">

            <label for="editar-felicitacion-nomenclatura-visual">
                Nomenclatura
            </label>

            <input type="text" id="editar-felicitacion-nomenclatura-visual" class="report-input report-input--readonly"
                value="" readonly>

            <small>
                Se genera automáticamente con el número de folio y el año de registro.
            </small>

            <input type="hidden" id="editar-felicitacion-nomenclatura" name="nomenclatura" value="">

        </div>

    </div>

</section>