<section class="dashboard-grafica dashboard-grafica--catalogo">

    <div class="dashboard-catalogo__encabezado">

        <div class="dashboard-catalogo__encabezado-info">

            <span class="dashboard-grafica__eyebrow">
                Incidencia
            </span>

            <h2 class="dashboard-grafica__titulo">
                Catálogo general
            </h2>

            <p class="dashboard-grafica__descripcion">
                Distribución de las conductas registradas
                de acuerdo con su incidencia.
            </p>

        </div>


        <div class="dashboard-catalogo__total">

            <span class="dashboard-catalogo__total-label">
                Total
            </span>

            <strong
                class="dashboard-catalogo__total-valor"
                id="catalogo-total"
            >
                276
            </strong>

            <small>
                registros
            </small>

        </div>

    </div>


    <div class="dashboard-catalogo__contenido">

        <div class="dashboard-catalogo__destacados">

            <div class="dashboard-catalogo__destacado">

                <span class="dashboard-catalogo__posicion">
                    01
                </span>

                <div>
                    <small>
                        Mayor incidencia
                    </small>

                    <strong id="catalogo-principal-nombre">
                        Conducta institucional
                    </strong>
                </div>

                <span
                    class="dashboard-catalogo__cantidad"
                    id="catalogo-principal-total"
                >
                    103
                </span>

            </div>


            <div class="dashboard-catalogo__destacado">

                <span class="dashboard-catalogo__posicion">
                    02
                </span>

                <div>
                    <small>
                        Segunda incidencia
                    </small>

                    <strong id="catalogo-segundo-nombre">
                        Agresiones físicas y/o verbales
                    </strong>
                </div>

                <span
                    class="dashboard-catalogo__cantidad"
                    id="catalogo-segundo-total"
                >
                    61
                </span>

            </div>


            <div class="dashboard-catalogo__destacado">

                <span class="dashboard-catalogo__posicion">
                    03
                </span>

                <div>
                    <small>
                        Tercera incidencia
                    </small>

                    <strong id="catalogo-tercero-nombre">
                        Omisión de apoyo
                    </strong>
                </div>

                <span
                    class="dashboard-catalogo__cantidad"
                    id="catalogo-tercero-total"
                >
                    56
                </span>

            </div>

        </div>


        <div class="dashboard-catalogo__grafica">

            <div
                class="
                    dashboard-grafica__canvas
                    dashboard-grafica__canvas--catalogo
                "
            >

                <canvas
                    id="grafica-catalogo"
                    aria-label="Gráfica del catálogo general"
                ></canvas>

            </div>

        </div>

    </div>

</section>