<div class="dashboard-historial" id="modal-historial-dashboard" aria-hidden="true">

    <!-- =====================================================
         FONDO
    ====================================================== -->

    <div class="dashboard-historial__overlay" data-historial-cerrar></div>


    <!-- =====================================================
         MODAL
    ====================================================== -->

    <div class="dashboard-historial__modal" role="dialog" aria-modal="true"
        aria-labelledby="dashboard-historial-titulo">

        <!-- =================================================
             ENCABEZADO
        ================================================== -->

        <div class="dashboard-historial__encabezado">

            <div>

                <span class="dashboard-historial__eyebrow">
                    Trazabilidad
                </span>

                <h2 class="dashboard-historial__titulo" id="dashboard-historial-titulo">
                    Historial de movimientos
                </h2>

                <p class="dashboard-historial__descripcion">
                    Consulta las acciones realizadas sobre
                    quejas, seguimientos y felicitaciones.
                </p>

            </div>


            <button type="button" class="dashboard-historial__cerrar" id="btn-cerrar-historial"
                aria-label="Cerrar historial" data-historial-cerrar>
                ×
            </button>

        </div>


        <!-- =================================================
             PESTAÑAS
        ================================================== -->

        <div class="dashboard-historial__tabs" role="tablist" aria-label="Tipo de historial">

            <button type="button" class="
                    dashboard-historial__tab
                    dashboard-historial__tab--activo
                " id="historial-tab-quejas" data-historial-tab="quejas" role="tab" aria-selected="true">

                <span>
                    Quejas
                </span>

                <strong id="historial-total-quejas">
                    0
                </strong>

            </button>


            <button type="button" class="dashboard-historial__tab" id="historial-tab-felicitaciones"
                data-historial-tab="felicitaciones" role="tab" aria-selected="false">

                <span>
                    Felicitaciones
                </span>

                <strong id="historial-total-felicitaciones">
                    0
                </strong>

            </button>

        </div>


        <!-- =================================================
             BUSCADOR
        ================================================== -->

        <div class="dashboard-historial__busqueda">

            <div class="dashboard-historial__busqueda-control">

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />

                    <path d="m20 20-3.5-3.5" />
                </svg>


                <input type="search" id="historial-busqueda" placeholder="Buscar por folio, usuario o acción..."
                    autocomplete="off">

            </div>

        </div>


        <!-- =================================================
             CUERPO
        ================================================== -->

        <div class="dashboard-historial__cuerpo">


            <!-- =============================================
                 CARGANDO
            ============================================== -->

            <div class="dashboard-historial__estado" id="historial-cargando" hidden>

                <div class="dashboard-historial__spinner"></div>

                <strong>
                    Consultando historial
                </strong>

                <span>
                    Espera un momento...
                </span>

            </div>


            <!-- =============================================
                 ERROR
            ============================================== -->

            <div class="dashboard-historial__estado" id="historial-error" hidden>

                <strong>
                    No fue posible cargar el historial
                </strong>

                <span id="historial-error-mensaje">
                    Intenta nuevamente.
                </span>

            </div>


            <!-- =============================================
                 SIN RESULTADOS
            ============================================== -->

            <div class="dashboard-historial__estado" id="historial-vacio" hidden>

                <strong>
                    Sin movimientos para mostrar
                </strong>

                <span>
                    No existen registros que coincidan con la consulta.
                </span>

            </div>


            <!-- =============================================
                 QUEJAS
            ============================================== -->

            <div class="
                    dashboard-historial__panel
                    dashboard-historial__panel--activo
                " id="historial-panel-quejas" data-historial-panel="quejas" role="tabpanel"
                aria-labelledby="historial-tab-quejas">

                <div class="dashboard-historial__lista" id="historial-lista-quejas"></div>

            </div>


            <!-- =============================================
                 FELICITACIONES
            ============================================== -->

            <div class="dashboard-historial__panel" id="historial-panel-felicitaciones"
                data-historial-panel="felicitaciones" role="tabpanel" aria-labelledby="historial-tab-felicitaciones"
                hidden>

                <div class="dashboard-historial__lista" id="historial-lista-felicitaciones"></div>

            </div>

        </div>

    </div>

</div>