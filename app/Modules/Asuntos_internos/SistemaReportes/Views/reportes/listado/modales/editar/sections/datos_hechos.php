<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Información del incidente
        </span>

        <h3>
            Datos de los hechos
        </h3>

    </div>


    <div class="editar-reporte-grid">

        <!-- FECHA DE LOS HECHOS -->
        <div class="editar-reporte-campo">

            <label for="editar-fecha-hechos">
                Fecha de los hechos
                <span class="required">*</span>
            </label>

            <input type="date" id="editar-fecha-hechos" name="fecha_hechos" required>

        </div>


        <!-- HORA DE LOS HECHOS -->
        <div class="editar-reporte-campo">

            <label for="editar-hora-hechos">
                Hora de los hechos
                <span class="required">*</span>
            </label>

            <input type="time" id="editar-hora-hechos" name="hora_hechos" required>

        </div>


        <!-- DESCRIPCIÓN -->
        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-descripcion">
                Descripción de los hechos
                <span class="required">*</span>
            </label>

            <textarea id="editar-descripcion" name="descripcion" rows="5" required></textarea>

        </div>

    </div>

</div>