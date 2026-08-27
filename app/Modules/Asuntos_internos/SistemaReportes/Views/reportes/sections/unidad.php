<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Unidad relacionada
            </span>

            <h2 class="report-section__title">
                Datos de la unidad
            </h2>

            <p class="report-section__description">
                Información de la unidad relacionada con el personal seleccionado.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-form-grid report-form-grid--unit">

            <!-- UNIDAD -->
            <div class="report-field">

                <label for="unidad">
                    Unidad
                    <span class="required">*</span>
                </label>

                <select
                    id="unidad"
                    name="unidad"
                    class="report-select"
                    required
                    disabled
                >
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

                <input
                    type="text"
                    id="unidad_marca"
                    name="unidad_marca"
                    class="report-input report-input--readonly"
                    readonly
                >

            </div>


            <!-- SUBMARCA -->
            <div class="report-field">

                <label for="unidad_submarca">
                    SubMarca Unidad
                </label>

                <input
                    type="text"
                    id="unidad_submarca"
                    name="unidad_submarca"
                    class="report-input report-input--readonly"
                    readonly
                >

            </div>


            <!-- COLOR -->
            <div class="report-field">

                <label for="unidad_color">
                    Color Unidad
                </label>

                <input
                    type="text"
                    id="unidad_color"
                    name="unidad_color"
                    class="report-input report-input--readonly"
                    readonly
                >

            </div>


            <!-- ESTATUS -->
            <div class="report-field">

                <label for="unidad_estatus">
                    Estatus de la Unidad
                </label>

                <input
                    type="text"
                    id="unidad_estatus"
                    name="unidad_estatus"
                    class="report-input report-input--readonly"
                    readonly
                >

            </div>


            <!-- SERVICIO Y ADSCRIPCIÓN -->
            <div class="report-field">

                <label for="unidad_servicio_adscripcion">
                    Servicio y Adscripción
                </label>

                <input
                    type="text"
                    id="unidad_servicio_adscripcion"
                    name="unidad_servicio_adscripcion"
                    class="report-input report-input--readonly"
                    readonly
                >

            </div>


            <!-- TIPO DE VEHÍCULO -->
            <div class="report-field">

                <label for="unidad_tipo_vehiculo">
                    Tipo de Vehículo
                </label>

                <input
                    type="text"
                    id="unidad_tipo_vehiculo"
                    name="unidad_tipo_vehiculo"
                    class="report-input report-input--readonly"
                    readonly
                >

            </div>


            <!-- ORIGEN -->
            <div class="report-field">

                <label for="unidad_origen">
                    Origen
                </label>

                <input
                    type="text"
                    id="unidad_origen"
                    name="unidad_origen"
                    class="report-input report-input--readonly"
                    readonly
                >

            </div>

        </div>

    </div>

</section>