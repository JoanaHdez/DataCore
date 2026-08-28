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

        <!-- QUEJOSO -->
        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-quejoso">
                Nombre del quejoso
                <span class="required">*</span>
            </label>

            <input
                type="text"
                id="editar-quejoso"
                name="quejoso"
                autocomplete="off"
                required
            >

        </div>


        <!-- EDAD -->
        <div class="editar-reporte-campo">

            <label for="editar-edad">
                Edad
                <span class="required">*</span>
            </label>

            <input
                type="number"
                id="editar-edad"
                name="edad"
                min="0"
                required
            >

        </div>


        <!-- GÉNERO -->
        <div class="editar-reporte-campo">

            <label for="editar-genero">
                Género
                <span class="required">*</span>
            </label>

            <select
                id="editar-genero"
                name="genero"
                required
            >
                <option value="">
                    Selecciona
                </option>

                <option value="Masculino">
                    Masculino
                </option>

                <option value="Femenino">
                    Femenino
                </option>

                <option value="Otro">
                    Otro
                </option>

            </select>

        </div>


        <!-- TELÉFONO -->
        <div class="editar-reporte-campo">

            <label for="editar-telefono">
                Teléfono
            </label>

            <input
                type="tel"
                id="editar-telefono"
                name="telefono"
                autocomplete="off"
            >

        </div>


        <!-- CORREO -->
        <div class="editar-reporte-campo">

            <label for="editar-correo">
                Correo electrónico
            </label>

            <input
                type="email"
                id="editar-correo"
                name="correo"
                autocomplete="off"
            >

        </div>

    </div>

</div>