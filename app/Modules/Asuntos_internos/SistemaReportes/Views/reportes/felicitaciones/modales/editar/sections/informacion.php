<section class="modal-felicitacion__seccion">

    <div class="modal-felicitacion__seccion-header">

        <span class="modal-felicitacion__eyebrow">
            Información de la felicitación
        </span>

        <h3 class="modal-felicitacion__seccion-title">
            Datos de la felicitación
        </h3>

    </div>


    <div class="modal-felicitacion__grid">

        <!-- PERSONA QUE FELICITA -->
        <div class="modal-felicitacion__campo modal-felicitacion__campo--full">

            <label for="editar-felicitacion-felicitante">
                Nombre de la persona que felicita
            </label>

            <input type="text" id="editar-felicitacion-felicitante" name="nombre_felicitante" class="report-input"
                maxlength="255" autocomplete="off" required>

        </div>


        <!-- RAZÓN -->
        <div class="modal-felicitacion__campo modal-felicitacion__campo--full modal-felicitacion__campo--texto">

            <label for="editar-felicitacion-razon">
                Razón de la felicitación
            </label>

            <textarea id="editar-felicitacion-razon" name="razon_felicitacion" class="report-textarea" rows="6"
                required></textarea>

        </div>

    </div>

</section>