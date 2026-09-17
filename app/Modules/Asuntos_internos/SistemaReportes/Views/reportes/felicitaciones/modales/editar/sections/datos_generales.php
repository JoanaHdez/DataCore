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

        <!-- FOLIO -->
        <div class="modal-felicitacion__campo">

            <label for="editar-felicitacion-folio">
                Folio
            </label>

            <input type="text" id="editar-felicitacion-folio" class="report-input" readonly>

        </div>


        <!-- FECHA -->
        <div class="modal-felicitacion__campo">

            <label for="editar-felicitacion-fecha">
                Fecha de registro
            </label>

            <input type="text" id="editar-felicitacion-fecha" name="fecha_registro" class="report-input" readonly>

        </div>


        <!-- NOMENCLATURA -->
        <div class="modal-felicitacion__campo modal-felicitacion__campo--full">

            <label for="editar-felicitacion-nomenclatura-parte">
                Nomenclatura
            </label>

            <div class="editar-nomenclatura">

                <span class="editar-nomenclatura__prefijo" id="editar-felicitacion-nomenclatura-prefijo">
                    CGSC/CAI/FEL/
                </span>

                <input type="text" id="editar-felicitacion-nomenclatura-parte" class="editar-nomenclatura__input"
                    placeholder="Ej. 1295/2026" autocomplete="off">

            </div>

            <small>
                Captura únicamente la parte final de la nomenclatura.
            </small>

            <input type="hidden" id="editar-felicitacion-nomenclatura" name="nomenclatura" value="">

        </div>

    </div>

</section>