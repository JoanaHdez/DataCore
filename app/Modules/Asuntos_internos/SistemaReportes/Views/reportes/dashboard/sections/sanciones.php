<section class="dashboard-grafica dashboard-grafica--sanciones">

    <div class="dashboard-sanciones__encabezado">

        <div>

            <span class="dashboard-grafica__eyebrow">
                Régimen disciplinario
            </span>

            <h2 class="dashboard-grafica__titulo">
                Sanciones disciplinarias
            </h2>

            <p class="dashboard-grafica__descripcion">
                Distribución de arrestos y amonestaciones registradas.
            </p>

        </div>

    </div>


    <div class="dashboard-sanciones__contenido">

        <!-- =====================================================
             RESUMEN
        ====================================================== -->

        <div class="dashboard-sanciones__resumen">

            <span class="dashboard-sanciones__resumen-label">
                Total de sanciones
            </span>

            <strong
                class="dashboard-sanciones__resumen-total"
                id="sanciones-total"
            >
                503
            </strong>

            <span class="dashboard-sanciones__resumen-periodo">
                Ene – Jun 2026
            </span>

        </div>


        <!-- =====================================================
             GRÁFICA
        ====================================================== -->

        <div class="dashboard-sanciones__grafica">

            <div
                class="dashboard-grafica__canvas
                       dashboard-grafica__canvas--sanciones"
            >

                <canvas
                    id="grafica-sanciones"
                    aria-label="Gráfica de sanciones disciplinarias"
                ></canvas>

            </div>

        </div>

    </div>

</section>