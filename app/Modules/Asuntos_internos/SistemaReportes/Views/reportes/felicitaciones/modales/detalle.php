<div class="modal-felicitacion" id="modal-detalle-felicitacion" hidden>

    <div class="modal-felicitacion__overlay" data-cerrar-detalle-felicitacion></div>


    <div class="modal-felicitacion__contenido" role="dialog" aria-modal="true"
        aria-labelledby="titulo-detalle-felicitacion">

        <div class="modal-felicitacion__header">

            <div>

                <span class="modal-felicitacion__eyebrow">
                    Detalle
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


        <div class="modal-felicitacion__body">

            <!-- =====================================================
                 DATOS GENERALES
            ====================================================== -->

            <section class="modal-felicitacion__seccion">

                <h3>
                    Datos generales
                </h3>


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


            <!-- =====================================================
                 RAZÓN
            ====================================================== -->

            <section class="modal-felicitacion__seccion">

                <h3>
                    Razón de la felicitación
                </h3>


                <div class="modal-felicitacion__texto" id="detalle-felicitacion-razon">
                    —
                </div>

            </section>


            <!-- =====================================================
                 PERSONAL
            ====================================================== -->

            <section class="modal-felicitacion__seccion">

                <h3>
                    Personal felicitado
                </h3>


                <div class="modal-felicitacion__tabla-wrapper">

                    <table class="modal-felicitacion__tabla">

                        <thead>

                            <tr>
                                <th>Nombre</th>
                                <th>Área</th>
                                <th>Turno</th>
                                <th>Alias</th>
                            </tr>

                        </thead>


                        <tbody id="detalle-felicitacion-personal">

                            <tr>

                                <td colspan="4">
                                    —
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

        </div>

    </div>

</div>