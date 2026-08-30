<section class="dashboard-grafica dashboard-grafica--resoluciones">

    <div class="dashboard-resoluciones__encabezado">

        <div>

            <span class="dashboard-grafica__eyebrow">
                Resolución general
            </span>

            <h2 class="dashboard-grafica__titulo">
                Estado de resolución de las quejas
            </h2>

            <p class="dashboard-grafica__descripcion">
                Distribución de los reportes de acuerdo con la resolución
                registrada en el expediente.
            </p>

        </div>


        <div class="dashboard-resoluciones__total">

            <span>
                Total
            </span>

            <strong id="resoluciones-total">
                <?= esc($resoluciones['total'] ?? 0) ?>
            </strong>

        </div>

    </div>


    <div class="dashboard-resoluciones__contenido">

        <div
            class="
                dashboard-grafica__canvas
                dashboard-grafica__canvas--resoluciones
            "
        >

            <canvas
                id="grafica-resoluciones"
                aria-label="Gráfica de resolución general de las quejas"
            ></canvas>

        </div>

    </div>

</section>


<script
    type="application/json"
    id="datos-grafica-resoluciones"
>
<?= json_encode(
    $resoluciones ?? [
        'resoluciones' => [],
        'totales' => [],
        'total' => 0,
    ],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
) ?>
</script>