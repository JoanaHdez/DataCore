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
                Define el periodo de los reportes que deseas consultar.
            </p>

        </div>

    </div>


    <div class="reportes-periodo__body">

        <!-- =====================================================
             FECHA INICIAL
        ====================================================== -->

        <div class="reportes-periodo__field">

            <label for="fecha_inicio">
                Fecha inicial
            </label>

            <input type="date" id="fecha_inicio" name="fecha_inicio" class="reportes-periodo__input">

        </div>


        <!-- =====================================================
             FECHA FINAL
        ====================================================== -->

        <div class="reportes-periodo__field">

            <label for="fecha_fin">
                Fecha final
            </label>

            <input type="date" id="fecha_fin" name="fecha_fin" class="reportes-periodo__input">

        </div>


        <!-- =====================================================
             ACCIONES
        ====================================================== -->

        <div class="reportes-periodo__actions">

            <button type="button" class="
                    reportes-periodo__button
                    reportes-periodo__button--primary
                " id="btn-aplicar-periodo">
                Aplicar periodo
            </button>

            <button type="button" class="
                    reportes-periodo__button
                    reportes-periodo__button--secondary
                " id="btn-limpiar-filtros">
                Limpiar filtros
            </button>

        </div>

    </div>

</section>