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

            <strong id="sanciones-total">
                0
            </strong>

            <span class="dashboard-sanciones__resumen-periodo">
                Fuente pendiente
            </span>

        </div>


        <!-- =====================================================
             GRÁFICA
        ====================================================== -->

        <div class="dashboard-sanciones__grafica">

            <div
                class="
                    dashboard-grafica__canvas
                    dashboard-grafica__canvas--sanciones
                "
            >

                <canvas
                    id="grafica-sanciones"
                    aria-label="Gráfica de sanciones disciplinarias"
                ></canvas>


                <!-- ESTADO SIN DATOS -->

                <div
                    class="dashboard-sanciones__sin-datos"
                    id="sanciones-sin-datos"
                    hidden
                >

                    <span class="dashboard-sanciones__sin-datos-icono">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                            />

                            <path
                                d="M12 8v5"
                            />

                            <path
                                d="M12 16.5h.01"
                            />
                        </svg>

                    </span>


                    <div>

                        <strong>
                            Sin información disponible
                        </strong>

                        <span>
                            Pendiente de definir la fuente de datos
                            de sanciones disciplinarias.
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>