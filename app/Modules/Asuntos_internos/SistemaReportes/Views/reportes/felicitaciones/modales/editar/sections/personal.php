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