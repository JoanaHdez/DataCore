<div class="detalle-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Investigación
        </span>

        <h3>
            Clasificación y seguimiento
        </h3>

    </div>


    <div class="detalle-reporte-grid">

        <!-- =====================================================
             CLASIFICACIÓN
        ====================================================== -->

        <div class="detalle-reporte-campo">

            <span>
                Clasificación
            </span>

            <strong id="detalle-clasificacion">
                —
            </strong>

        </div>


        <!-- =====================================================
             INSPECTOR
        ====================================================== -->

        <div class="detalle-reporte-campo">

            <span>
                Inspector
            </span>

            <strong id="detalle-inspector">
                —
            </strong>

        </div>


        <!-- =====================================================
             INVESTIGADOR
        ====================================================== -->

        <div class="detalle-reporte-campo">

            <span>
                Investigador
            </span>

            <strong id="detalle-investigador">
                —
            </strong>

        </div>


        <!-- =====================================================
             ESTADO
        ====================================================== -->

        <div class="detalle-reporte-campo">

            <span>
                Estado
            </span>

            <strong id="detalle-estado-actual">
                —
            </strong>

        </div>


        <!-- =====================================================
             SITUACIÓN DE LA SANCIÓN
        ====================================================== -->

        <div class="detalle-reporte-campo">

            <span>
                Situación de la sanción
            </span>

            <strong id="detalle-situacion-sancion">
                —
            </strong>

        </div>


        <!-- =====================================================
             ESPACIO VACÍO
        ====================================================== -->

        <div class="detalle-clasificacion__espacio"></div>


        <!-- =====================================================
             MOTIVOS
        ====================================================== -->

        <div class="detalle-reporte-campo detalle-reporte-campo--full detalle-clasificacion__motivos">

            <span>
                Motivos
            </span>


            <!-- =================================================
                 SIN MOTIVOS
            ================================================== -->

            <div class="detalle-clasificacion__motivos-vacio" id="detalle-motivos-vacio">
                Sin motivos relacionados
            </div>


            <!-- =================================================
                 TABLA DE MOTIVOS
            ================================================== -->

            <div class="detalle-clasificacion__motivos-wrapper" id="detalle-motivos-wrapper" hidden>

                <table class="detalle-clasificacion__motivos-tabla">

                    <thead>

                        <tr>

                            <th>
                                Num
                            </th>

                            <th>
                                Motivo
                            </th>

                            <th>
                                Sanción
                            </th>

                            <th>
                                Folio sanción
                            </th>

                        </tr>

                    </thead>


                    <tbody id="detalle-motivos-body"></tbody>

                </table>

            </div>

        </div>


        <!-- =====================================================
             QUIÉN EMITE LA RESOLUCIÓN
        ====================================================== -->

        <div class="detalle-reporte-campo detalle-reporte-campo--full detalle-clasificacion__resolucion-emisor">

            <span>
                Quién emite la resolución
            </span>

            <strong id="detalle-quien-emite-resolucion">
                —
            </strong>

        </div>


        <!-- =====================================================
             RESOLUCIÓN
        ====================================================== -->

        <div class="detalle-reporte-campo detalle-reporte-campo--full detalle-clasificacion__resolucion">

            <span>
                Resolución
            </span>

            <strong id="detalle-resolucion">
                —
            </strong>

        </div>

    </div>

</div>