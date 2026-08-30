<section class="dashboard-grafica dashboard-grafica--areas-involucradas">

    <div class="dashboard-grafica__encabezado">

        <div>
            <span class="dashboard-grafica__eyebrow">
                Distribución institucional
            </span>

            <h2 class="dashboard-grafica__titulo">
                Quejas por área
            </h2>

            <p class="dashboard-grafica__descripcion">
                Áreas de adscripción del personal involucrado en los reportes.
            </p>
        </div>

    </div>


    <div class="dashboard-areas-involucradas__grafica">

        <canvas
            id="grafica-areas-involucradas"
            aria-label="Gráfica de quejas por área"
        ></canvas>

    </div>

</section>


<script
    type="application/json"
    id="datos-grafica-areas-involucradas"
>
<?= json_encode(
    $quejasPorArea ?? [
        'areas' => [],
        'totales' => [],
    ],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
) ?>
</script>