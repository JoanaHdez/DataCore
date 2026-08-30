<section class="dashboard-grafica dashboard-grafica--turnos">

    <div class="dashboard-turnos__encabezado">

        <div>

            <span class="dashboard-grafica__eyebrow">
                Distribución operativa
            </span>

            <h2 class="dashboard-grafica__titulo">
                Quejas por turno
            </h2>

            <p class="dashboard-grafica__descripcion">
                Distribución general de las quejas registradas
                de acuerdo con el turno relacionado.
            </p>

        </div>


        <div class="dashboard-turnos__total">

            <span>
                Total
            </span>

            <strong id="turnos-total">
                276
            </strong>

            <small>
                quejas
            </small>

        </div>

    </div>


    <div class="dashboard-turnos__contenido">

        <div
            class="
                dashboard-grafica__canvas
                dashboard-grafica__canvas--turnos
            "
        >

            <canvas
                id="grafica-turnos"
                aria-label="Gráfica de quejas por turno"
            ></canvas>

        </div>

    </div>

</section>