<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Personal relacionado
            </span>

            <h2 class="report-section__title">
                Personal y unidades involucradas
            </h2>

            <p class="report-section__description">
                Información del personal relacionado con los hechos y de la unidad asignada.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-form-grid">

            <!-- OFICIAL -->
            <div class="report-field report-field--full">

                <label for="oficial">
                    Oficial
                </label>

                <input type="text" id="oficial" name="oficial" class="report-input"
                    placeholder="Ingresa el nombre del oficial" autocomplete="off">

                <small class="report-field__help">
                    Al seleccionar al oficial se cargarán automáticamente su área y las unidades relacionadas.
                </small>

            </div>


            <!-- ÁREA -->
            <div class="report-field">

                <label for="area">
                    Área
                </label>

                <input type="text" id="area" name="area" class="report-input report-input--readonly" readonly>

            </div>

            <!-- TURNO -->
            <div class="report-field">

                <label for="turno">
                    Turno
                </label>

                <input type="text" id="turno" name="turno" class="report-input" placeholder="Ingresa el turno"
                    autocomplete="off">

            </div>

        </div>


        <!-- =====================================================
             DATOS DE LA UNIDAD
        ====================================================== -->
        <div class="report-subsection">

            <div class="report-subsection__header">

                <span class="report-subsection__eyebrow">
                    Unidad seleccionada
                </span>

                <h3 class="report-subsection__title">
                    Datos de la unidad
                </h3>

                <p class="report-subsection__description">
                    La información se completará automáticamente al seleccionar una unidad.
                </p>

            </div>


            <div class="report-form-grid report-form-grid--unit">

                <!-- UNIDAD -->
                <div class="report-field">

                    <label for="unidad">
                        Unidad
                    </label>

                    <select id="unidad" name="unidad" class="report-select" disabled>
                        <option value="">
                            Selecciona primero un oficial
                        </option>
                    </select>

                    <small class="report-field__help">
                        Las unidades disponibles dependerán del oficial seleccionado.
                    </small>

                </div>


                <!-- MARCA -->
                <div class="report-field">

                    <label for="unidad_marca">
                        Marca Unidad
                    </label>

                    <input type="text" id="unidad_marca" name="unidad_marca" class="report-input report-input--readonly"
                        readonly>

                </div>


                <!-- SUBMARCA -->
                <div class="report-field">

                    <label for="unidad_submarca">
                        SubMarca Unidad
                    </label>

                    <input type="text" id="unidad_submarca" name="unidad_submarca"
                        class="report-input report-input--readonly" readonly>

                </div>


                <!-- COLOR -->
                <div class="report-field">

                    <label for="unidad_color">
                        Color Unidad
                    </label>

                    <input type="text" id="unidad_color" name="unidad_color" class="report-input report-input--readonly"
                        readonly>

                </div>


                <!-- ESTATUS -->
                <div class="report-field">

                    <label for="unidad_estatus">
                        Estatus de la Unidad
                    </label>

                    <input type="text" id="unidad_estatus" name="unidad_estatus"
                        class="report-input report-input--readonly" readonly>

                </div>


                <!-- SERVICIO Y ADSCRIPCIÓN -->
                <div class="report-field">

                    <label for="unidad_servicio_adscripcion">
                        Servicio y Adscripción
                    </label>

                    <input type="text" id="unidad_servicio_adscripcion" name="unidad_servicio_adscripcion"
                        class="report-input report-input--readonly" readonly>

                </div>


                <!-- TIPO DE VEHÍCULO -->
                <div class="report-field">

                    <label for="unidad_tipo_vehiculo">
                        Tipo de Vehículo
                    </label>

                    <input type="text" id="unidad_tipo_vehiculo" name="unidad_tipo_vehiculo"
                        class="report-input report-input--readonly" readonly>

                </div>


                <!-- ORIGEN -->
                <div class="report-field">

                    <label for="unidad_origen">
                        Origen
                    </label>

                    <input type="text" id="unidad_origen" name="unidad_origen"
                        class="report-input report-input--readonly" readonly>

                </div>

            </div>

        </div>

    </div>

</section>