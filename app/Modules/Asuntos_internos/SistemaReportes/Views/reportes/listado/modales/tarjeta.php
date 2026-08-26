<div
    class="modal-reporte"
    id="modal-tarjeta-reporte"
    aria-hidden="true"
>

    <div
        class="modal-reporte__overlay"
        data-cerrar-modal-tarjeta
    ></div>

    <div
        class="modal-reporte__dialog modal-reporte__dialog--tarjeta"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-tarjeta-titulo"
    >

        <!-- =====================================================
             HEADER DEL MODAL
        ====================================================== -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Ficha del reporte
                </span>

                <h2
                    class="modal-reporte__title"
                    id="modal-tarjeta-titulo"
                >
                    Tarjeta informativa
                </h2>

            </div>

            <button
                type="button"
                class="modal-reporte__close"
                data-cerrar-modal-tarjeta
                aria-label="Cerrar"
            >
                ×
            </button>

        </div>


        <!-- =====================================================
             CONTENIDO
        ====================================================== -->
        <div class="modal-reporte__body">

            <article
                class="tarjeta-reporte"
                id="tarjeta-reporte"
            >

                <!-- ENCABEZADO INSTITUCIONAL -->
                <div class="tarjeta-reporte__encabezado">

                    <div class="tarjeta-reporte__institucion">

                        <span>
                            Coordinación de Asuntos Internos
                        </span>

                        <h3>
                            Tarjeta informativa
                        </h3>

                    </div>


                    <div class="tarjeta-reporte__folio">

                        <span>
                            Folio
                        </span>

                        <strong id="tarjeta-folio">
                            —
                        </strong>

                    </div>

                </div>


                <!-- =================================================
                     DATOS PRINCIPALES
                ================================================== -->
                <div class="tarjeta-reporte__seccion">

                    <div class="tarjeta-reporte__seccion-titulo">
                        Información del reporte
                    </div>


                    <div class="tarjeta-reporte__grid">

                        <div class="tarjeta-reporte__campo">

                            <span>
                                Fecha de queja
                            </span>

                            <strong id="tarjeta-fecha-queja">
                                —
                            </strong>

                        </div>


                        <div class="tarjeta-reporte__campo">

                            <span>
                                Expediente
                            </span>

                            <strong id="tarjeta-expediente">
                                —
                            </strong>

                        </div>


                        <div class="tarjeta-reporte__campo">

                            <span>
                                Clasificación
                            </span>

                            <strong id="tarjeta-clasificacion">
                                —
                            </strong>

                        </div>


                        <div class="tarjeta-reporte__campo">

                            <span>
                                Resolución
                            </span>

                            <strong id="tarjeta-resolucion">
                                —
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     QUEJOSO
                ================================================== -->
                <div class="tarjeta-reporte__seccion">

                    <div class="tarjeta-reporte__seccion-titulo">
                        Quejoso
                    </div>

                    <div class="tarjeta-reporte__dato-destacado">

                        <strong id="tarjeta-quejoso">
                            —
                        </strong>

                    </div>

                </div>


                <!-- =================================================
                     ADSCRIPCIÓN
                ================================================== -->
                <div class="tarjeta-reporte__seccion">

                    <div class="tarjeta-reporte__seccion-titulo">
                        Información operativa
                    </div>


                    <div class="tarjeta-reporte__grid">

                        <div class="tarjeta-reporte__campo">

                            <span>
                                Área
                            </span>

                            <strong id="tarjeta-area">
                                —
                            </strong>

                        </div>


                        <div class="tarjeta-reporte__campo">

                            <span>
                                Turno
                            </span>

                            <strong id="tarjeta-turno">
                                —
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     ÚLTIMO SEGUIMIENTO
                ================================================== -->
                <div class="tarjeta-reporte__seccion">

                    <div class="tarjeta-reporte__seccion-titulo">
                        Último seguimiento
                    </div>


                    <div
                        class="tarjeta-reporte__seguimiento"
                        id="tarjeta-ultimo-seguimiento"
                    >

                        <span>
                            Sin seguimientos registrados.
                        </span>

                    </div>

                </div>

            </article>

        </div>


        <!-- =====================================================
             FOOTER
        ====================================================== -->
        <div class="modal-reporte__footer">

            <button
                type="button"
                class="
                    modal-reporte__button
                    modal-reporte__button--secondary
                "
                data-cerrar-modal-tarjeta
            >
                Cerrar
            </button>

            <button
                type="button"
                class="
                    modal-reporte__button
                    modal-reporte__button--primary
                "
                id="btn-imprimir-tarjeta"
            >
                Imprimir tarjeta
            </button>

        </div>

    </div>

</div>