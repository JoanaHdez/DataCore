<section class="report-section">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Nuevo registro
            </span>

            <h2 class="report-section__title">
                Datos del reporte
            </h2>

            <p class="report-section__description">
                Información general para la identificación del reporte.
            </p>

        </div>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="report-section__body">

        <div class="report-form-grid">


            <!-- =================================================
                 TIPO DE FOLIO
            ================================================== -->

            <div class="report-field">

                <label>
                    Tipo de folio
                    <span class="required">*</span>
                </label>


                <!-- =============================================
                     VALOR REAL PARA BACKEND
                ============================================== -->

                <input type="hidden" id="tipo_folio" name="tipo_folio" value="QJ">


                <!-- =============================================
                     SELECTOR VISUAL
                ============================================== -->

                <button type="button" class="tipo-folio-select" id="tipo-folio-select" aria-expanded="false"
                    aria-controls="tipo-folio-resultados">

                    <span class="tipo-folio-select__texto" id="tipo-folio-select-texto">
                        QJ - Queja
                    </span>


                    <span class="tipo-folio-select__flecha" aria-hidden="true">
                        ▾
                    </span>

                </button>


                <small class="report-field__help">
                    El consecutivo se asignará de acuerdo con el tipo de folio seleccionado.
                </small>


                <!-- =============================================
                     CATÁLOGO
                ============================================== -->

                <div class="tipo-folio-resultados" id="tipo-folio-resultados" hidden>


                    <!-- =========================================
                         QJ
                    ========================================== -->

                    <button type="button" class="tipo-folio-resultados__item" data-tipo-folio-opcion
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

                    <button type="button" class="tipo-folio-resultados__item" data-tipo-folio-opcion
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

                    <button type="button" class="tipo-folio-resultados__item" data-tipo-folio-opcion
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

            <div class="report-field">

                <label for="folio_visual">
                    Número de folio
                </label>


                <input type="text" id="folio_visual" class="report-input report-input--readonly"
                    value="<?= esc($folioVisual ?? 'QJ- — Automático') ?>" readonly>


                <small class="report-field__help">
                    El consecutivo se asignará automáticamente al guardar.
                </small>

            </div>


            <!-- =================================================
                 FECHA DE REGISTRO
            ================================================== -->

            <div class="report-field">

                <label for="fecha_registro">
                    Fecha de registro
                </label>


                <input type="text" id="fecha_registro" name="fecha_registro" class="report-input report-input--readonly"
                    value="<?= date('d/m/Y') ?>" readonly>


                <small class="report-field__help">
                    Se asigna automáticamente con la fecha actual.
                </small>

            </div>

        </div>

    </div>

</section>