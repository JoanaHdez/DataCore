<section class="dashboard-grafica dashboard-grafica--principal dashboard-grafica--dark">

    <div class="dashboard-grafica__encabezado">

        <div class="dashboard-grafica__encabezado-info">

            <span class="dashboard-grafica__eyebrow">
                Distribución
            </span>

            <h2 class="dashboard-grafica__titulo">
                Quejas por sectores y turnos
            </h2>

            <p class="dashboard-grafica__descripcion">
                Distribución de las quejas registradas por sector
                de acuerdo con el turno relacionado.
            </p>

        </div>


        <div
            class="dashboard-grafica__periodos"
            aria-label="Periodo de visualización"
        >

            <button
                type="button"
                class="dashboard-grafica__periodo"
                data-periodo-grafica="1m"
            >
                1M
            </button>

            <button
                type="button"
                class="dashboard-grafica__periodo"
                data-periodo-grafica="3m"
            >
                3M
            </button>

            <button
                type="button"
                class="dashboard-grafica__periodo"
                data-periodo-grafica="6m"
            >
                6M
            </button>

            <button
                type="button"
                class="dashboard-grafica__periodo"
                data-periodo-grafica="1a"
            >
                1A
            </button>

            <button
                type="button"
                class="
                    dashboard-grafica__periodo
                    dashboard-grafica__periodo--activo
                "
                data-periodo-grafica="todo"
            >
                Todo
            </button>

        </div>

    </div>


    <div class="dashboard-grafica__contenido">

        <div
            class="
                dashboard-grafica__canvas
                dashboard-grafica__canvas--sectores
            "
        >

            <canvas
                id="grafica-sectores-turnos"
                aria-label="Gráfica de quejas por sectores y turnos"
            ></canvas>

        </div>

    </div>

</section>


<script
    type="application/json"
    id="datos-grafica-sectores-turnos"
>
<?= json_encode(
    $sectoresTurnos ?? [
        'sectores' => [],
        'turnos' => [],
    ],
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
) ?>
</script>