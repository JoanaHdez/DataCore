<form id="form-nueva-felicitacion" class="form-nuevo-reporte" method="post" action="#">

    <?= csrf_field() ?>


    <!-- =====================================================
         DATOS DE LA FELICITACIÓN
    ====================================================== -->

    <section class="report-section">

        <div class="report-section__header">

            <div>

                <span class="report-section__eyebrow">
                    Nuevo registro
                </span>

                <h2 class="report-section__title">
                    Datos de la felicitación
                </h2>

                <p class="report-section__description">
                    Información general para registrar la felicitación.
                </p>

            </div>

        </div>


        <div class="report-section__body">

            <div class="report-form-grid">

                <!-- FOLIO -->
                <div class="report-field">

                    <label for="felicitacion-folio-visual">
                        Número de folio
                    </label>

                    <input type="text" id="felicitacion-folio-visual" class="report-input report-input--readonly"
                        value="FEL- — Automático" readonly>

                    <small class="report-field__help">
                        El consecutivo se asigna automáticamente.
                    </small>

                </div>


                <!-- FECHA -->
                <div class="report-field">

                    <label for="felicitacion-fecha-registro">
                        Fecha de registro
                    </label>

                    <input type="text" id="felicitacion-fecha-registro" name="fecha_registro"
                        class="report-input report-input--readonly" value="<?= date('d/m/Y') ?>" readonly>

                    <small class="report-field__help">
                        Se asigna automáticamente con la fecha actual.
                    </small>

                </div>


                <!-- NOMENCLATURA -->
                <div class="report-field report-field--full">

                    <label for="felicitacion-nomenclatura">
                        Nomenclatura
                    </label>

                    <input type="text" id="felicitacion-nomenclatura" class="report-input report-input--readonly"
                        value="CGSC/CAI/FEL/ — Automático" readonly>

                    <small class="report-field__help">
                        Se genera automáticamente con el número de folio asignado.
                    </small>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         PERSONAL FELICITADO
    ====================================================== -->

    <section class="report-section">

        <div class="report-section__header">

            <div>

                <span class="report-section__eyebrow">
                    Personal relacionado
                </span>

                <h2 class="report-section__title">
                    Personal felicitado
                </h2>

                <p class="report-section__description">
                    Busca y agrega una o más personas relacionadas con la felicitación.
                </p>

            </div>

        </div>


        <div class="report-section__body">

            <!-- BÚSQUEDA -->
            <div class="report-form-grid">

                <div class="report-field report-field--full">

                    <label for="felicitacion-oficial">
                        Buscar personal
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="felicitacion-oficial" class="report-input"
                        placeholder="Busca por nombre o nómina" autocomplete="off">

                    <small class="report-field__help">
                        Selecciona una persona para cargar automáticamente sus datos.
                    </small>

                    <div class="personal-resultados" id="felicitacion-personal-resultados" hidden></div>

                </div>

            </div>


            <!-- PERSONA SELECCIONADA -->
            <div class="personal-seleccionado" id="felicitacion-personal-seleccionado" hidden>

                <div class="personal-seleccionado__foto">

                    <img id="felicitacion-personal-foto" src="" alt="" hidden>

                    <span id="felicitacion-personal-foto-fallback">
                        —
                    </span>

                </div>


                <div class="personal-seleccionado__datos">

                    <input type="hidden" id="felicitacion-personal-plantilla-id">

                    <input type="hidden" id="felicitacion-personal-perscod">


                    <div class="report-form-grid">

                        <!-- NOMBRE -->
                        <div class="report-field report-field--full">

                            <label>
                                Nombre
                            </label>

                            <input type="text" id="felicitacion-personal-nombre"
                                class="report-input report-input--readonly" readonly>

                        </div>


                        <!-- ÁREA -->
                        <div class="report-field">

                            <label>
                                Área
                            </label>

                            <input type="text" id="felicitacion-personal-area"
                                class="report-input report-input--readonly" readonly>

                        </div>


                        <!-- TURNO -->
                        <div class="report-field">

                            <label for="felicitacion-personal-turno">
                                Turno
                            </label>

                            <input type="text" id="felicitacion-personal-turno" class="report-input"
                                placeholder="Turno">

                        </div>


                        <!-- ALIAS -->
                        <div class="report-field report-field--full">

                            <label for="felicitacion-personal-alias">
                                Alias
                            </label>

                            <input type="text" id="felicitacion-personal-alias" class="report-input"
                                placeholder="Alias del elemento, si aplica" autocomplete="off">

                            <small class="report-field__help">
                                Este campo es opcional.
                            </small>

                        </div>

                    </div>

                </div>


                <div class="personal-seleccionado__acciones">

                    <button type="button" class="button button--primary" id="btn-agregar-personal-felicitacion">
                        Agregar personal
                    </button>

                </div>

            </div>


            <!-- PERSONAL AGREGADO -->
            <div class="personal-agregado" id="felicitacion-personal-agregado" hidden>

                <div class="personal-agregado__header">

                    <div>

                        <span>
                            Personal agregado
                        </span>

                        <strong>
                            Elementos relacionados con la felicitación
                        </strong>

                    </div>

                </div>


                <div class="personal-agregado__tabla-wrapper">

                    <table class="personal-agregado__tabla">

                        <thead>

                            <tr>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Área</th>
                                <th>Turno</th>
                                <th>Alias</th>
                                <th>Acciones</th>
                            </tr>

                        </thead>

                        <tbody id="felicitacion-personal-agregado-body"></tbody>

                    </table>

                </div>

            </div>


            <!-- DATOS PARA BACKEND -->
            <div id="felicitacion-personal-hidden-inputs"></div>

        </div>

    </section>


    <!-- =====================================================
         DATOS DEL FELICITANTE
    ====================================================== -->

    <section class="report-section">

        <div class="report-section__header">

            <div>

                <span class="report-section__eyebrow">
                    Felicitante
                </span>

                <h2 class="report-section__title">
                    Información de la felicitación
                </h2>

                <p class="report-section__description">
                    Captura quién realiza la felicitación y el motivo de la misma.
                </p>

            </div>

        </div>


        <div class="report-section__body">

            <div class="report-form-grid">

                <!-- NOMBRE -->
                <div class="report-field report-field--full">

                    <label for="nombre_felicitante">
                        Nombre de la persona que da la felicitación
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="nombre_felicitante" name="nombre_felicitante" class="report-input"
                        placeholder="Ingresa el nombre" autocomplete="off" required>

                </div>


                <!-- RAZÓN -->
                <div class="report-field report-field--full">

                    <label for="razon_felicitacion">
                        Razón de la felicitación
                        <span class="required">*</span>
                    </label>

                    <textarea id="razon_felicitacion" name="razon_felicitacion" class="report-textarea"
                        placeholder="Describe la razón de la felicitación" rows="6" required></textarea>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         ACCIONES
    ====================================================== -->

    <div class="report-step-actions">

        <div></div>


        <div class="report-step-actions__right">

            <button type="submit" class="button button--primary" id="btn-guardar-felicitacion">
                Guardar felicitación
            </button>

        </div>

    </div>

</form>