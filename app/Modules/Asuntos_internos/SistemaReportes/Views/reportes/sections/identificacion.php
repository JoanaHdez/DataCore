<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Datos generales
            </span>

            <h2 class="report-section__title">
                Identificación del registro
            </h2>

            <p class="report-section__description">
                Información general para identificar el reporte.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-form-grid">

            <!-- FOLIO IP -->
            <div class="report-field">

                <label for="folio_ip">
                    Folio IP
                </label>

                <input type="text" id="folio_ip" name="folio_ip" class="report-input" placeholder="Ingresa el folio IP">

            </div>


            <!-- FECHA DE QUEJA -->
            <div class="report-field">

                <label for="fecha_queja">
                    Fecha de queja
                    <span class="required">*</span>
                </label>

                <input type="date" id="fecha_queja" name="fecha_queja" class="report-input" required>

            </div>


            <!-- FECHA DE ACUERDO -->
            <div class="report-field">

                <label for="fecha_acuerdo">
                    Fecha de acuerdo
                </label>

                <input type="date" id="fecha_acuerdo" name="fecha_acuerdo" class="report-input">

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
            <div class="report-field">

                <label for="nomenclatura_parte">
                    Nomenclatura
                </label>

                <div class="report-nomenclatura">

                    <span class="report-nomenclatura__prefijo" id="nomenclatura_prefijo">
                        CGSC/CAI/QJ/
                    </span>

                    <input type="text" id="nomenclatura_parte" name="nomenclatura_parte"
                        class="report-input report-nomenclatura__input" placeholder="Ej. 1295/2026" autocomplete="off">

                </div>

                <small class="report-field__help">
                    Captura únicamente la parte final de la nomenclatura.
                </small>


                <!-- VALOR COMPLETO QUE SE ENVIARÁ AL BACKEND -->
                <input type="hidden" id="nomenclatura" name="nomenclatura" value="">

            </div>


            <!-- NÚMERO DE OFICIO -->
            <div class="report-field">

                <label for="no_oficio">
                    No. de oficio
                </label>

                <input type="text" id="no_oficio" name="no_oficio" class="report-input"
                    placeholder="Ingresa el número de oficio">

            </div>

        </div>

    </div>

</section>