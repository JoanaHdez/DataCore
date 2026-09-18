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


                <input type="text" id="editar-numero-anonimo" name="numero_anonimo" autocomplete="off"
                    placeholder="Ingresa el número asignado" disabled>


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

            <label>
                Género
                <span class="required">*</span>
            </label>


            <!-- =================================================
                VALOR REAL PARA BACKEND
            ================================================== -->

            <input type="hidden" id="editar-genero" name="genero" value="" required>


            <!-- =================================================
                SELECTOR VISUAL
            ================================================== -->

            <button type="button" class="genero-select" id="editar-genero-select" aria-expanded="false"
                aria-controls="editar-genero-resultados">

                <span class="genero-select__texto" id="editar-genero-select-texto">
                    Selecciona una opción
                </span>


                <span class="genero-select__flecha" aria-hidden="true">
                    ▾
                </span>

            </button>


            <!-- =================================================
                CATÁLOGO
            ================================================== -->

            <div class="genero-resultados" id="editar-genero-resultados" hidden>


                <!-- MUJER -->
                <button type="button" class="genero-resultados__item" data-editar-genero-opcion data-genero="Mujer">

                    <span class="genero-resultados__avatar">
                        M
                    </span>

                    <span class="genero-resultados__datos">

                        <strong>
                            Mujer
                        </strong>

                        <small>
                            Género femenino
                        </small>

                    </span>

                </button>


                <!-- HOMBRE -->
                <button type="button" class="genero-resultados__item" data-editar-genero-opcion data-genero="Hombre">

                    <span class="genero-resultados__avatar">
                        H
                    </span>

                    <span class="genero-resultados__datos">

                        <strong>
                            Hombre
                        </strong>

                        <small>
                            Género masculino
                        </small>

                    </span>

                </button>


                <!-- OTRO -->
                <button type="button" class="genero-resultados__item" data-editar-genero-opcion data-genero="Otro">

                    <span class="genero-resultados__avatar">
                        O
                    </span>

                    <span class="genero-resultados__datos">

                        <strong>
                            Otro
                        </strong>

                        <small>
                            Otra identidad de género
                        </small>

                    </span>

                </button>


                <!-- NO ESPECIFICADO -->
                <button type="button" class="genero-resultados__item" data-editar-genero-opcion
                    data-genero="No especificado">

                    <span class="genero-resultados__avatar">
                        —
                    </span>

                    <span class="genero-resultados__datos">

                        <strong>
                            No especificado
                        </strong>

                        <small>
                            No se especifica el género
                        </small>

                    </span>

                </button>

            </div>

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
            DIRECCIÓN DEL QUEJOSO
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-direccion-quejoso">
                Dirección del quejoso
            </label>

            <textarea id="editar-direccion-quejoso" name="direccion_quejoso" rows="3"
                placeholder="Ingresa la dirección del quejoso" autocomplete="street-address"></textarea>

            <small class="editar-reporte-campo__help">
                Ingresa la dirección proporcionada por el quejoso.
            </small>

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