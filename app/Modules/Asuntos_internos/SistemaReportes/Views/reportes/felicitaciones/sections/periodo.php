<section class="reportes-periodo">

    <div class="reportes-periodo__header">

        <div>

            <span class="reportes-periodo__eyebrow">
                Periodo de consulta
            </span>

            <h2 class="reportes-periodo__title">
                Selecciona un rango de fechas
            </h2>

            <p class="reportes-periodo__description">
                Define el periodo de las felicitaciones que deseas consultar.
            </p>

        </div>

    </div>


    <div class="reportes-periodo__body">

        <!-- =====================================================
             FECHA INICIAL
        ====================================================== -->

        <div class="reportes-periodo__field">

            <label for="felicitaciones-fecha-inicio">
                Fecha inicial
            </label>

            <input type="date" id="felicitaciones-fecha-inicio" name="fecha_inicio" class="reportes-periodo__input">

        </div>


        <!-- =====================================================
             FECHA FINAL
        ====================================================== -->

        <div class="reportes-periodo__field">

            <label for="felicitaciones-fecha-fin">
                Fecha final
            </label>

            <input type="date" id="felicitaciones-fecha-fin" name="fecha_fin" class="reportes-periodo__input">

        </div>


        <!-- =====================================================
             ACCIONES
        ====================================================== -->

        <div class="reportes-periodo__actions">

            <button type="button" class="
                    reportes-periodo__button
                    reportes-periodo__button--primary
                " id="btn-aplicar-periodo-felicitaciones">
                Aplicar periodo
            </button>


            <button type="button" class="
                    reportes-periodo__button
                    reportes-periodo__button--secondary
                " id="btn-limpiar-filtros-felicitaciones">
                Limpiar filtros
            </button>

        </div>

    </div>

</section>