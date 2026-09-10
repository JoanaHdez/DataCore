<section class="report-section">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Persona que presenta la queja
            </span>

            <h2 class="report-section__title">
                Datos del quejoso
            </h2>

            <p class="report-section__description">
                Información de la persona relacionada con la presentación de la queja.
            </p>

        </div>

    </div>


    <!-- =========================================================
         BODY
    ========================================================== -->

    <div class="report-section__body">

        <div class="report-form-grid">


            <!-- =====================================================
                 QUEJA ANÓNIMA
            ====================================================== -->

            <div class="report-field report-field--full">

                <label>
                    ¿Queja anónima?
                </label>


                <div class="report-options">

                    <label class="report-option">

                        <input type="radio" name="es_anonimo" value="0" id="quejoso-no-anonimo" checked>

                        <span>
                            No
                        </span>

                    </label>


                    <label class="report-option">

                        <input type="radio" name="es_anonimo" value="1" id="quejoso-anonimo">

                        <span>
                            Sí
                        </span>

                    </label>

                </div>


                <small class="report-field__help">
                    Selecciona “Sí” cuando la identidad del quejoso no deba registrarse.
                </small>

            </div>


            <!-- =====================================================
                 NÚMERO ANÓNIMO
            ====================================================== -->

            <div class="report-field report-field--full" id="numero-anonimo-contenedor" hidden>

                <label for="numero_anonimo">

                    No. Numérico

                    <span class="required">
                        *
                    </span>

                </label>


                <input type="text" id="numero_anonimo" name="numero_anonimo" class="report-input"
                    placeholder="Ingresa el número asignado" autocomplete="off" disabled>


                <small class="report-field__help">
                    Este número será el identificador del quejoso anónimo.
                </small>

            </div>


            <!-- =====================================================
                 QUEJOSO
            ====================================================== -->

            <div class="report-field report-field--full">

                <label for="quejoso">

                    Quejoso

                    <span class="required">
                        *
                    </span>

                </label>


                <input type="text" id="quejoso" name="quejoso" class="report-input"
                    placeholder="Ingresa el nombre del quejoso" autocomplete="off" required>

            </div>


            <!-- =====================================================
                 EDAD
            ====================================================== -->

            <div class="report-field">

                <label for="edad">

                    Edad

                    <span class="required">
                        *
                    </span>

                </label>


                <input type="number" id="edad" name="edad" class="report-input" placeholder="Ingresa la edad" min="0"
                    max="120" required>

            </div>


            <!-- =====================================================
                 GÉNERO
            ====================================================== -->

            <div class="report-field">

                <label for="genero">

                    Género

                    <span class="required">
                        *
                    </span>

                </label>


                <select id="genero" name="genero" class="report-select" required>

                    <option value="" selected disabled>
                        Selecciona una opción
                    </option>

                    <option value="Mujer">
                        Mujer
                    </option>

                    <option value="Hombre">
                        Hombre
                    </option>

                    <option value="Otro">
                        Otro
                    </option>

                    <option value="No especificado">
                        No especificado
                    </option>

                </select>

            </div>


            <!-- =====================================================
                 TELÉFONO
            ====================================================== -->

            <div class="report-field">

                <label for="telefono">
                    Número de teléfono
                </label>


                <input type="tel" id="telefono" name="telefono" class="report-input"
                    placeholder="Ingresa el número de teléfono" autocomplete="tel">

            </div>


            <!-- =====================================================
                 CORREO ELECTRÓNICO
            ====================================================== -->

            <div class="report-field">

                <label for="correo">
                    Correo electrónico
                </label>


                <input type="email" id="correo" name="correo" class="report-input"
                    placeholder="Ingresa el correo electrónico" autocomplete="email">

            </div>


            <!-- =====================================================
                 CANALIZACIÓN
            ====================================================== -->

            <div class="report-field report-field--full">

                <label>
                    Canalización al área correspondiente
                </label>


                <!-- =================================================
                     VALOR REAL QUE SE ENVÍA AL BACKEND
                ================================================== -->

                <input type="hidden" id="canalizacion" name="canalizacion" value="">


                <!-- =================================================
                     SELECTOR VISUAL
                ================================================== -->

                <button type="button" class="canalizacion-select" id="canalizacion-select" aria-expanded="false">

                    <span class="canalizacion-select__texto" id="canalizacion-select-texto">
                        Sin canalización
                    </span>


                    <span class="canalizacion-select__flecha" aria-hidden="true">
                        ▾
                    </span>

                </button>


                <small class="report-field__help">
                    Selecciona el área a la que será canalizada la atención, si aplica.
                </small>


                <!-- =================================================
                     CATÁLOGO
                ================================================== -->

                <div class="canalizacion-resultados" id="canalizacion-resultados" hidden>


                    <!-- =============================================
                         SIN CANALIZACIÓN
                    ============================================== -->

                    <button type="button" class="canalizacion-resultados__item" data-canalizacion-opcion
                        data-canalizacion-nombre="">

                        <span class="canalizacion-resultados__avatar">
                            —
                        </span>


                        <span class="canalizacion-resultados__datos">

                            <strong>
                                Sin canalización
                            </strong>

                            <small>
                                No canalizar a otra área
                            </small>

                        </span>

                    </button>


                    <!-- =============================================
                         ÁREAS DESDE CATÁLOGO BD
                    ============================================== -->

                    <?php if (!empty($canalizaciones)): ?>

                    <?php foreach ($canalizaciones as $canalizacion): ?>

                    <?php

                            $nombreCanalizacion =
                                trim(
                                    (string) (
                                        $canalizacion['nombre']
                                        ?? ''
                                    )
                                );

                                $letraCanalizacion =
                                    'A';


                                $nombreMayusculas =
                                    mb_strtoupper(
                                        $nombreCanalizacion,
                                        'UTF-8'
                                    );


                                if (
                                    str_contains(
                                        $nombreMayusculas,
                                        'BUSQUEDA'
                                    )
                                ) {

                                    $letraCanalizacion =
                                        'B';

                                } elseif (
                                    str_contains(
                                        $nombreMayusculas,
                                        'VICTIMAS'
                                    )
                                ) {

                                    $letraCanalizacion =
                                        'V';

                                } elseif (
                                    str_contains(
                                        $nombreMayusculas,
                                        'VIOLENCIA FAMILIAR'
                                    )
                                    || str_contains(
                                        $nombreMayusculas,
                                        'GENERO'
                                    )
                                ) {

                                    $letraCanalizacion =
                                        'G';
                                }

                            ?>

                    <?php if ($nombreCanalizacion !== ''): ?>

                    <button type="button" class="canalizacion-resultados__item" data-canalizacion-opcion
                        data-canalizacion-nombre="<?= esc($nombreCanalizacion) ?>">

                        <span class="canalizacion-resultados__avatar">
                            <?= esc($letraCanalizacion) ?>
                        </span>


                        <span class="canalizacion-resultados__datos">

                            <strong>
                                <?= esc($nombreCanalizacion) ?>
                            </strong>

                            <small>
                                Área de canalización
                            </small>

                        </span>

                    </button>

                    <?php endif; ?>

                    <?php endforeach; ?>

                    <?php endif; ?>


                    <!-- =============================================
                         OTRO
                    ============================================== -->

                    <button type="button" class="canalizacion-resultados__item" data-canalizacion-opcion
                        data-canalizacion-nombre="Otro">

                        <span class="canalizacion-resultados__avatar">
                            +
                        </span>


                        <span class="canalizacion-resultados__datos">

                            <strong>
                                Otro
                            </strong>

                            <small>
                                Especificar otra área
                            </small>

                        </span>

                    </button>

                </div>

            </div>


            <!-- =====================================================
                 OTRA ÁREA
            ====================================================== -->

            <div class="report-field report-field--full" id="canalizacion-otro-contenedor" hidden>

                <label for="canalizacion_otro">

                    Especifica el área

                    <span class="required">
                        *
                    </span>

                </label>


                <input type="text" id="canalizacion_otro" name="canalizacion_otro" class="report-input"
                    placeholder="Ingresa el área correspondiente" autocomplete="off" disabled>


                <small class="report-field__help">
                    Este campo es obligatorio cuando seleccionas “Otro”.
                </small>

            </div>


        </div>

    </div>

</section>