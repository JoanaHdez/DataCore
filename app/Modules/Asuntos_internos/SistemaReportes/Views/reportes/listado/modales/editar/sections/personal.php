<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Personal relacionado
        </span>

        <h3>
            Personal involucrado
        </h3>

    </div>


    <!-- =====================================================
         BUSCADOR
    ====================================================== -->

    <div class="editar-reporte-grid">

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-personal-busqueda">
                Buscar personal
            </label>

            <input
                type="text"
                id="editar-personal-busqueda"
                autocomplete="off"
                placeholder="Busca por nombre o nómina"
            >

            <small class="editar-reporte-campo__help">
                Puedes agregar una o más personas al reporte.
            </small>


            <div
                class="editar-personal-resultados"
                id="editar-personal-resultados"
                hidden
            ></div>

        </div>

    </div>


    <!-- =====================================================
         PERSONA SELECCIONADA
    ====================================================== -->

    <div
        class="editar-personal-seleccionado"
        id="editar-personal-seleccionado"
        hidden
    >

        <div class="editar-personal-seleccionado__foto">

            <img
                id="editar-personal-foto"
                src=""
                alt=""
                hidden
            >

            <span id="editar-personal-foto-fallback">
                —
            </span>

        </div>


        <div class="editar-personal-seleccionado__datos">

            <input
                type="hidden"
                id="editar-personal-plantilla-id"
            >

            <input
                type="hidden"
                id="editar-personal-perscod"
            >


            <div class="editar-reporte-grid">

                <div class="editar-reporte-campo editar-reporte-campo--full">

                    <label for="editar-personal-nombre">
                        Nombre
                    </label>

                    <input
                        type="text"
                        id="editar-personal-nombre"
                        readonly
                    >

                </div>


                <div class="editar-reporte-campo">

                    <label for="editar-personal-area">
                        Área
                    </label>

                    <input
                        type="text"
                        id="editar-personal-area"
                        readonly
                    >

                </div>


                <div class="editar-reporte-campo">

                    <label for="editar-personal-turno">
                        Turno
                    </label>

                    <input
                        type="text"
                        id="editar-personal-turno"
                        placeholder="Turno"
                    >

                </div>

            </div>

        </div>


        <div class="editar-personal-seleccionado__acciones">

            <button
                type="button"
                class="modal-reporte__button modal-reporte__button--primary"
                id="btn-editar-agregar-personal"
            >
                Agregar personal
            </button>

        </div>

    </div>


    <!-- =====================================================
         PERSONAL ACTUAL DEL REPORTE
    ====================================================== -->

    <div
        class="editar-personal-agregado"
        id="editar-personal-agregado"
        hidden
    >

        <div class="editar-personal-agregado__header">

            <div>

                <span>
                    Personal agregado
                </span>

                <strong>
                    Elementos relacionados con el reporte
                </strong>

            </div>

        </div>


        <div class="editar-personal-agregado__tabla-wrapper">

            <table class="editar-personal-agregado__tabla">

                <thead>

                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Nómina</th>
                        <th>Área</th>
                        <th>Turno</th>
                        <th>Acciones</th>
                    </tr>

                </thead>

                <tbody id="editar-personal-agregado-body"></tbody>

            </table>

        </div>

    </div>


    <!-- =====================================================
         DATOS PARA BACKEND
    ====================================================== -->

    <div id="editar-personal-hidden-inputs"></div>

</div>