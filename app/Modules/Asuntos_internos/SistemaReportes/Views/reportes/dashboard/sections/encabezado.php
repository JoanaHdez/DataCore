<?php

/*
 * GENERADOR DE ANALISIS DASHBOARD
 * ---------------------------------------------------------
 * El boton queda oculto por defecto porque la integracion IA
 * depende de una API key institucional y presupuesto pendiente.
 *
 * Para reactivarlo cuando exista autorizacion:
 * DASHBOARD_INFORME_HABILITADO=true
 */
$informeDashboardHabilitado =
    filter_var(
        env('DASHBOARD_INFORME_HABILITADO') ?? false,
        FILTER_VALIDATE_BOOLEAN
    );
?>

<section class="dashboard-encabezado">

    <div class="dashboard-encabezado__contenido">

        <div>

            <span class="dashboard-encabezado__eyebrow">
                Asuntos Internos
            </span>

            <h1 class="dashboard-encabezado__titulo">
                Dashboard
            </h1>

            <p class="dashboard-encabezado__descripcion">
                Consulta indicadores y estadísticas generales de Quejas y Felicitaciones.
            </p>

        </div>


        <div class="dashboard-encabezado__acciones">

            <button type="button" class="dashboard-encabezado__historial" id="btn-historial-dashboard">
                Historial
            </button>


            <?php if ($informeDashboardHabilitado): ?>
                <button type="button" class="dashboard-encabezado__informe" id="btn-generar-informe-dashboard">
                    Generar reporte
                </button>
            <?php endif; ?>


            <button type="button" class="dashboard-encabezado__exportar" id="btn-exportar-dashboard">
                Exportar Excel
            </button>

        </div>

    </div>

</section>
