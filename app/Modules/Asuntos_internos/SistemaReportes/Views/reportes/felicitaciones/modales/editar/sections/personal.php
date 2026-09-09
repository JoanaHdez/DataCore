<section class="modal-felicitacion__seccion modal-felicitacion-personal-editar">

    <!-- =====================================================
         ENCABEZADO PRINCIPAL
    ====================================================== -->

    <div class="modal-felicitacion__seccion-header">

        <span class="modal-felicitacion__eyebrow">
            Personal relacionado
        </span>

        <h3 class="modal-felicitacion__seccion-title">
            Personal felicitado
        </h3>

    </div>


    <!-- =====================================================
         BUSCADOR
    ====================================================== -->

    <div class="modal-felicitacion-personal-editar__busqueda">

        <label for="editar-felicitacion-buscar-personal">
            Buscar personal
        </label>


        <input type="text" id="editar-felicitacion-buscar-personal" class="report-input"
            placeholder="Busca por nombre o nómina" autocomplete="off">


        <small>
            Puedes agregar una o más personas a la felicitación.
        </small>


        <!-- =================================================
     RESULTADOS DE BÚSQUEDA
================================================== -->

        <div class="modal-felicitacion-personal-editar__resultados" id="editar-felicitacion-personal-resultados" hidden>
        </div>


        <!-- =================================================
     PERSONA SELECCIONADA
================================================== -->

        <div class="editar-personal-seleccionado" id="editar-felicitacion-personal-seleccionado" hidden>

            <div class="editar-personal-seleccionado__foto">

                <img id="editar-felicitacion-personal-foto" src="" alt="" hidden>

                <span id="editar-felicitacion-personal-foto-fallback">
                    —
                </span>

            </div>


            <div class="editar-personal-seleccionado__datos">

                <input type="hidden" id="editar-felicitacion-personal-plantilla-id">

                <input type="hidden" id="editar-felicitacion-personal-perscod">


                <div class="editar-reporte-grid">

                    <div class="editar-reporte-campo editar-reporte-campo--full">

                        <label>
                            Nombre
                        </label>

                        <input type="text" id="editar-felicitacion-personal-nombre" readonly>

                    </div>


                    <div class="editar-reporte-campo">

                        <label>
                            Nómina
                        </label>

                        <input type="text" id="editar-felicitacion-personal-nomina" readonly>

                    </div>


                    <div class="editar-reporte-campo">

                        <label>
                            Área
                        </label>

                        <input type="text" id="editar-felicitacion-personal-area" readonly>

                    </div>


                    <div class="editar-reporte-campo">

                        <label>
                            Turno
                        </label>

                        <input type="text" id="editar-felicitacion-personal-turno" placeholder="Turno">

                    </div>


                    <div class="editar-reporte-campo">

                        <label>
                            Alias
                        </label>

                        <input type="text" id="editar-felicitacion-personal-alias"
                            placeholder="Alias del elemento, si aplica" autocomplete="off">

                    </div>

                </div>

            </div>


            <div class="editar-personal-seleccionado__acciones">

                <button type="button" class="button button--primary" id="btn-editar-agregar-personal-felicitacion">
                    Agregar personal
                </button>

            </div>

        </div>

    </div>


    <!-- =====================================================
         PERSONAL AGREGADO
    ====================================================== -->

    <div class="modal-felicitacion-personal-editar__agregado">

        <div class="modal-felicitacion-personal-editar__subheader">

            <span class="modal-felicitacion__eyebrow">
                Personal agregado
            </span>

            <strong>
                Elementos relacionados con la felicitación
            </strong>

        </div>


        <div class="modal-felicitacion__tabla-wrapper">

            <table class="modal-felicitacion__tabla">

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


                <tbody id="editar-felicitacion-personal">

                    <tr>

                        <td colspan="6">
                            Sin personal relacionado
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =====================================================
         INPUTS DINÁMICOS
    ====================================================== -->

    <div id="editar-felicitacion-personal-inputs"></div>

</section>