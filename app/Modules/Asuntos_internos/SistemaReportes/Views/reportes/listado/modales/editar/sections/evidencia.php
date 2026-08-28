<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Archivos adjuntos
        </span>

        <h3>
            Evidencia fotográfica
        </h3>

    </div>


    <div class="editar-reporte-grid">

        <div class="editar-reporte-campo editar-reporte-campo--full">

    <label for="editar-evidencia-fotografica">
        Agregar evidencia
    </label>

    <div class="editar-evidencia-carga">

        <input
            type="file"
            id="editar-evidencia-fotografica"
            name="evidencia_fotografica[]"
            class="editar-evidencia-carga__input"
            accept="image/jpeg,image/png,image/webp"
            multiple
        >

        <label
            for="editar-evidencia-fotografica"
            class="editar-evidencia-carga__zona"
        >

            <div class="editar-evidencia-carga__icono">
                ↑
            </div>

            <div class="editar-evidencia-carga__texto">

                <strong>
                    Seleccionar fotografías
                </strong>

                <span>
                    Haz clic para agregar una o varias imágenes
                </span>

                <small>
                    JPG, PNG o WEBP
                </small>

            </div>

        </label>

    </div>

</div>


        <!-- Evidencia que ya pertenece al reporte -->
        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label>
                Evidencia registrada
            </label>

            <div
                id="editar-evidencia-existente"
                class="editar-evidencia__lista"
            >
                <span class="editar-evidencia__vacio">
                    Sin evidencia registrada
                </span>
            </div>

        </div>


        <!-- Archivos nuevos seleccionados -->
        <div
            class="editar-reporte-campo editar-reporte-campo--full"
            id="editar-evidencia-nueva-contenedor"
        >

            <label>
                Nueva evidencia seleccionada
            </label>

            <div
                id="editar-evidencia-nueva"
                class="editar-evidencia__lista"
            >
                <span class="editar-evidencia__vacio">
                    No se han seleccionado archivos nuevos
                </span>
            </div>

        </div>

    </div>

</div>