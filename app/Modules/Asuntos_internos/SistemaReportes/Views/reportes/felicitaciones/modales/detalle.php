<div class="modal-felicitacion" id="modal-detalle-felicitacion" aria-hidden="true" hidden>

    <!-- =====================================================
         OVERLAY
    ====================================================== -->

    <div class="modal-felicitacion__overlay" data-cerrar-detalle-felicitacion></div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="modal-felicitacion__contenido" role="dialog" aria-modal="true"
        aria-labelledby="titulo-detalle-felicitacion">

        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="modal-felicitacion__header">

            <div>

                <span class="modal-felicitacion__eyebrow">
                    Detalle del registro
                </span>

                <h2 class="modal-felicitacion__titulo" id="titulo-detalle-felicitacion">
                    Felicitación
                    <span id="detalle-felicitacion-titulo-folio"></span>
                </h2>

            </div>


            <button type="button" class="modal-felicitacion__cerrar" data-cerrar-detalle-felicitacion
                aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =================================================
             NAVEGACIÓN
        ================================================== -->

        <nav class="detalle-felicitacion-nav">

            <button type="button" class="detalle-felicitacion-nav__item detalle-felicitacion-nav__item--active"
                data-detalle-felicitacion-seccion="datos">
                Datos de la felicitación
            </button>


            <button type="button" class="detalle-felicitacion-nav__item" data-detalle-felicitacion-seccion="personal">
                Personal y unidad
            </button>

        </nav>


        <!-- =================================================
             BODY
        ================================================== -->

        <div class="modal-felicitacion__body">


            <!-- =============================================
                 PANEL 1
                 DATOS DE LA FELICITACIÓN
            ============================================== -->

            <section class="detalle-felicitacion-panel detalle-felicitacion-panel--active"
                data-detalle-felicitacion-panel="datos">

                <!-- =========================================
                     DATOS GENERALES
                ========================================== -->

                <section class="modal-felicitacion__seccion">

                    <div class="modal-felicitacion__seccion-header">

                        <span class="modal-felicitacion__eyebrow">
                            Información general
                        </span>

                        <h3 class="modal-felicitacion__seccion-title">
                            Datos generales
                        </h3>

                    </div>


                    <div class="modal-felicitacion__grid">

                        <div class="modal-felicitacion__campo">

                            <span>
                                Folio
                            </span>

                            <strong id="detalle-felicitacion-folio">
                                —
                            </strong>

                        </div>


                        <div class="modal-felicitacion__campo">

                            <span>
                                Fecha
                            </span>

                            <strong id="detalle-felicitacion-fecha">
                                —
                            </strong>

                        </div>


                        <div class="modal-felicitacion__campo modal-felicitacion__campo--full">

                            <span>
                                Felicitante
                            </span>

                            <strong id="detalle-felicitacion-felicitante">
                                —
                            </strong>

                        </div>

                    </div>

                </section>


                <!-- =========================================
                     RAZÓN
                ========================================== -->

                <section class="modal-felicitacion__seccion">

                    <div class="modal-felicitacion__seccion-header">

                        <span class="modal-felicitacion__eyebrow">
                            Descripción
                        </span>

                        <h3 class="modal-felicitacion__seccion-title">
                            Razón de la felicitación
                        </h3>

                    </div>


                    <div class="modal-felicitacion__texto" id="detalle-felicitacion-razon">
                        —
                    </div>

                </section>

            </section>


            <!-- =============================================
                 PANEL 2
                 PERSONAL Y UNIDAD
            ============================================== -->

            <section class="detalle-felicitacion-panel" data-detalle-felicitacion-panel="personal" hidden>

                <!-- =========================================
     PERSONAL
========================================== -->

                <section class="modal-felicitacion__seccion">

                    <div class="modal-felicitacion__seccion-header">

                        <span class="modal-felicitacion__eyebrow">
                            Personal relacionado
                        </span>

                        <h3 class="modal-felicitacion__seccion-title">
                            Personal felicitado
                        </h3>

                    </div>


                    <div class="detalle-felicitacion-personal__vacio" id="detalle-felicitacion-personal-vacio" hidden>
                        Sin personal relacionado
                    </div>


                    <div class="detalle-felicitacion-personal__tabla-wrapper"
                        id="detalle-felicitacion-personal-tabla-wrapper">

                        <table class="detalle-felicitacion-personal__tabla">

                            <thead>

                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Área</th>
                                    <th>Turno</th>
                                    <th>Alias</th>
                                </tr>

                            </thead>


                            <tbody id="detalle-felicitacion-personal"></tbody>

                        </table>

                    </div>

                </section>


                <!-- =========================================
                     UNIDADES
                ========================================== -->

                <section class="modal-felicitacion__seccion">

                    <div class="modal-felicitacion__seccion-header">

                        <span class="modal-felicitacion__eyebrow">
                            Unidades relacionadas
                        </span>

                        <h3 class="modal-felicitacion__seccion-title">
                            Unidades relacionadas
                        </h3>

                    </div>


                    <!-- =====================================
                         CON UNIDAD
                    ====================================== -->

                    <div id="detalle-felicitacion-unidades-contenedor">

                        <div class="modal-felicitacion__tabla-wrapper">

                            <table class="modal-felicitacion__tabla">

                                <thead>

                                    <tr>
                                        <th>Unidad</th>
                                        <th>Marca / Submarca</th>
                                        <th>Color</th>
                                        <th>Estatus</th>
                                        <th>Servicio</th>
                                        <th>Tipo</th>
                                    </tr>

                                </thead>


                                <tbody id="detalle-felicitacion-unidades">

                                    <tr>

                                        <td colspan="7">
                                            —
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>


                    <!-- =====================================
                         SIN UNIDAD
                    ====================================== -->

                    <div class="unidad-sin-unidad" id="detalle-felicitacion-sin-unidad" hidden>

                        <div class="unidad-sin-unidad__contenido">

                            <strong>
                                Sin unidad / Oficina
                            </strong>

                            <p>
                                El personal felicitado no cuenta con una unidad vehicular relacionada.
                            </p>

                        </div>

                    </div>

                </section>

            </section>

        </div>

    </div>

</div>