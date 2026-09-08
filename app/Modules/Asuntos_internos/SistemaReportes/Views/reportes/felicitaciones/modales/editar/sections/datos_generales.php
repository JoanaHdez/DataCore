<section class="modal-felicitacion__seccion">

    <div class="modal-felicitacion__seccion-header">

        <span class="modal-felicitacion__eyebrow">
            Información general
        </span>

        <h3 class="modal-felicitacion__seccion-title">
            Datos generales
        </h3>

    </div>


    <div class="modal-felicitacion__grid">

        <div class="modal-felicitacion__campo">

            <label for="editar-felicitacion-folio">
                Folio
            </label>

            <input type="text" id="editar-felicitacion-folio" class="report-input" readonly>

        </div>


        <div class="modal-felicitacion__campo">

            <label for="editar-felicitacion-fecha">
                Fecha de registro
            </label>

            <input type="text" id="editar-felicitacion-fecha" name="fecha_registro" class="report-input" readonly>

        </div>


        <div class="modal-felicitacion__campo modal-felicitacion__campo--full">

            <label for="editar-felicitacion-felicitante">
                Nombre de la persona que felicita
            </label>

            <input type="text" id="editar-felicitacion-felicitante" name="nombre_felicitante" class="report-input"
                maxlength="255" autocomplete="off" required>

        </div>

    </div>

</section>