<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Persona que presenta la queja
        </span>

        <h3>
            Datos del quejoso
        </h3>

    </div>


    <div class="editar-reporte-grid">


        <!-- =====================================================
             QUEJA ANÓNIMA
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label>
                ¿Queja anónima?
            </label>


            <div class="editar-report-options">

                <label class="editar-report-option">

                    <input type="radio" name="anonimo" id="editar-anonimo-no" value="0" checked>

                    <span>
                        No
                    </span>

                </label>


                <label class="editar-report-option">

                    <input type="radio" name="anonimo" id="editar-anonimo-si" value="1">

                    <span>
                        Sí
                    </span>

                </label>

            </div>


            <small class="editar-reporte-campo__help">
                Selecciona “Sí” cuando la identidad del quejoso no deba registrarse.
            </small>

        </div>


        <!-- =====================================================
             NÚMERO ANÓNIMO
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full" id="editar-numero-anonimo-contenedor" hidden>

            <div class="quejoso-anonimo__numero">

                <label for="editar-numero-anonimo">

                    No. Numérico

                    <span class="required">
                        *
                    </span>

                </label>


                <input type="number" id="editar-numero-anonimo" name="numero_anonimo" min="0" inputmode="numeric"
                    autocomplete="off" placeholder="Ingresa el número asignado" disabled>


                <small class="editar-reporte-campo__help">
                    Este número será el identificador del quejoso anónimo.
                </small>

            </div>

        </div>


        <!-- =====================================================
             QUEJOSO
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-quejoso">

                Quejoso

                <span class="required">
                    *
                </span>

            </label>


            <input type="text" id="editar-quejoso" name="quejoso" autocomplete="off"
                placeholder="Ingresa el nombre del quejoso" required>

        </div>


        <!-- =====================================================
             EDAD
        ====================================================== -->

        <div class="editar-reporte-campo">

            <label for="editar-edad">

                Edad

                <span class="required">
                    *
                </span>

            </label>


            <input type="number" id="editar-edad" name="edad" min="0" max="120" placeholder="Ingresa la edad" required>

        </div>


        <!-- =====================================================
             GÉNERO
        ====================================================== -->

        <div class="editar-reporte-campo">

            <label for="editar-genero">

                Género

                <span class="required">
                    *
                </span>

            </label>


            <select id="editar-genero" name="genero" required>

                <option value="" disabled>
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

        <div class="editar-reporte-campo">

            <label for="editar-telefono">
                Número de teléfono
            </label>


            <input type="tel" id="editar-telefono" name="telefono" autocomplete="tel"
                placeholder="Ingresa el número de teléfono">

        </div>


        <!-- =====================================================
             CORREO ELECTRÓNICO
        ====================================================== -->

        <div class="editar-reporte-campo">

            <label for="editar-correo">
                Correo electrónico
            </label>


            <input type="email" id="editar-correo" name="correo" autocomplete="email"
                placeholder="Ingresa el correo electrónico">

        </div>


        <!-- =====================================================
             CANALIZACIÓN
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label>
                Canalización al área correspondiente
            </label>


            <!-- =================================================
                 VALOR REAL QUE SE ENVÍA AL BACKEND
            ================================================== -->

            <input type="hidden" id="editar-canalizacion" name="canalizacion" value="">


            <!-- =================================================
                 SELECTOR VISUAL
            ================================================== -->

            <button type="button" class="canalizacion-select" id="editar-canalizacion-select" aria-expanded="false">

                <span class="canalizacion-select__texto" id="editar-canalizacion-select-texto">
                    Sin canalización
                </span>


                <span class="canalizacion-select__flecha" aria-hidden="true">
                    ▾
                </span>

            </button>


            <small class="editar-reporte-campo__help">
                Selecciona el área a la que será canalizada la atención, si aplica.
            </small>


            <!-- =================================================
                 CATÁLOGO
            ================================================== -->

            <div class="canalizacion-resultados" id="editar-canalizacion-resultados" hidden>


                <!-- =============================================
                     SIN CANALIZACIÓN
                ============================================== -->

                <button type="button" class="canalizacion-resultados__item" data-editar-canalizacion-opcion
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
                            || str_contains(
                                $nombreMayusculas,
                                'BÚSQUEDA'
                            )
                        ) {

                            $letraCanalizacion =
                                'B';

                        } elseif (
                            str_contains(
                                $nombreMayusculas,
                                'VICTIMAS'
                            )
                            || str_contains(
                                $nombreMayusculas,
                                'VÍCTIMAS'
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
                            || str_contains(
                                $nombreMayusculas,
                                'GÉNERO'
                            )
                        ) {

                            $letraCanalizacion =
                                'G';
                        }

                        ?>


                <?php if ($nombreCanalizacion !== ''): ?>

                <button type="button" class="canalizacion-resultados__item" data-editar-canalizacion-opcion
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

                <button type="button" class="canalizacion-resultados__item" data-editar-canalizacion-opcion
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

        <div class="editar-reporte-campo editar-reporte-campo--full" id="editar-canalizacion-otro-contenedor" hidden>

            <label for="editar-canalizacion-otro">

                Especifica el área

                <span class="required">
                    *
                </span>

            </label>


            <input type="text" id="editar-canalizacion-otro" name="canalizacion_otro" autocomplete="off"
                placeholder="Ingresa el área correspondiente" disabled>


            <small class="editar-reporte-campo__help">
                Este campo es obligatorio cuando seleccionas “Otro”.
            </small>

        </div>


    </div>

</div>