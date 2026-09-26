<div class="editar-reporte-seccion__bloque">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="detalle-reporte-seccion__header">

        <span>
            Información general
        </span>

        <h3>
            Datos del reporte
        </h3>

    </div>


    <!-- =====================================================
         CAMPOS
    ====================================================== -->

    <div class="editar-reporte-grid">


        <!-- =================================================
             TIPO DE FOLIO
        ================================================== -->

        <div class="editar-reporte-campo">

            <label>
                Tipo de folio
                <span class="required">*</span>
            </label>


            <!-- =============================================
                 VALOR REAL PARA BACKEND
            ============================================== -->

            <input type="hidden" id="editar-tipo-folio" name="tipo_folio" value="QJ">


            <!-- =============================================
                 SELECTOR VISUAL
            ============================================== -->

            <button type="button" class="tipo-folio-select" id="editar-tipo-folio-select" aria-expanded="false"
                aria-controls="editar-tipo-folio-resultados">

                <span class="tipo-folio-select__texto" id="editar-tipo-folio-select-texto">
                    QJ - Queja
                </span>


                <span class="tipo-folio-select__flecha" aria-hidden="true">
                    ▾
                </span>

            </button>


            <small class="editar-reporte-campo__help">
                El consecutivo depende del tipo de folio seleccionado.
            </small>


            <!-- =============================================
                 CATÁLOGO DE TIPOS DE FOLIO
            ============================================== -->

            <div class="tipo-folio-resultados" id="editar-tipo-folio-resultados" hidden>


                <!-- =========================================
                     QJ
                ========================================== -->

                <button type="button" class="tipo-folio-resultados__item" data-editar-tipo-folio-opcion
                    data-tipo-folio="QJ" data-tipo-folio-nombre="QJ - Queja">

                    <span class="tipo-folio-resultados__avatar">
                        QJ
                    </span>


                    <span class="tipo-folio-resultados__datos">

                        <strong>
                            QJ - Queja
                        </strong>

                        <small>
                            Queja general
                        </small>

                    </span>

                </button>


                <!-- =========================================
                     QJV
                ========================================== -->

                <button type="button" class="tipo-folio-resultados__item" data-editar-tipo-folio-opcion
                    data-tipo-folio="QJV" data-tipo-folio-nombre="QJV - Queja verbal">

                    <span class="tipo-folio-resultados__avatar">
                        QJV
                    </span>


                    <span class="tipo-folio-resultados__datos">

                        <strong>
                            QJV - Queja verbal
                        </strong>

                        <small>
                            Queja recibida de forma verbal
                        </small>

                    </span>

                </button>


                <!-- =========================================
                     QJF
                ========================================== -->

                <button type="button" class="tipo-folio-resultados__item" data-editar-tipo-folio-opcion
                    data-tipo-folio="QJF" data-tipo-folio-nombre="QJF - Queja foránea">

                    <span class="tipo-folio-resultados__avatar">
                        QJF
                    </span>


                    <span class="tipo-folio-resultados__datos">

                        <strong>
                            QJF - Queja foránea
                        </strong>

                        <small>
                            No requiere personal ni unidades relacionadas
                        </small>

                    </span>

                </button>

            </div>

        </div>


        <!-- =================================================
            NÚMERO DE FOLIO
        ================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--folio">

            <label for="editar-folio">
                Número de folio
            </label>

            <input type="text" id="editar-folio" name="folio" readonly>

            <small class="editar-reporte-campo__help">
                El número de folio se asigna automáticamente.
            </small>

            <div class="editar-reporte-campo__alerta-foranea" id="editar-aviso-queja-foranea" hidden>
                Este tipo de queja es para dependencias externas a la
                Comisaría General de Seguridad Ciudadana de Nezahualcóyotl.
            </div>

        </div>


        <!-- =================================================
             FECHA DE REGISTRO
        ================================================== -->

        <div class="editar-reporte-campo">

            <label for="editar-fecha-registro">
                Fecha de registro
            </label>

            <input type="date" id="editar-fecha-registro" name="fecha_registro" readonly>

            <small class="editar-reporte-campo__help">
                La fecha de registro se asigna automáticamente.
            </small>

        </div>

    </div>

</div>