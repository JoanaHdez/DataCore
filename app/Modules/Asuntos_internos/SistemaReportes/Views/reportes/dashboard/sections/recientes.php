<section class="dashboard-recientes">

    <div class="dashboard-recientes__encabezado">

        <div>
            <span class="dashboard-recientes__eyebrow">
                Actividad reciente
            </span>

            <h2 class="dashboard-recientes__titulo">
                Reportes recientes
            </h2>

            <p class="dashboard-recientes__descripcion">
                Últimos reportes registrados en el sistema.
            </p>
        </div>


        <a
            href="<?= base_url(
                'asuntos-internos/reportes/listado'
            ) ?>"
            class="dashboard-recientes__enlace"
        >
            Ver todos los reportes
        </a>

    </div>


    <div class="dashboard-recientes__tabla-contenedor">

        <table class="dashboard-recientes__tabla">

            <thead>

                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Expediente</th>
                    <th>Clasificación</th>
                    <th>Área</th>
                    <th>Estado</th>
                </tr>

            </thead>


            <tbody>

                <!-- TEMPORAL -->
                <tr>

                    <td>
                        <strong>
                            AI-2026-001
                        </strong>
                    </td>

                    <td>
                        25/08/2026
                    </td>

                    <td>
                        CAI/001/2026
                    </td>

                    <td>
                        Queja
                    </td>

                    <td>
                        Seguridad Ciudadana
                    </td>

                    <td>
                        <span
                            class="
                                dashboard-recientes__estado
                                dashboard-recientes__estado--proceso
                            "
                        >
                            En proceso
                        </span>
                    </td>

                </tr>


                <tr>

                    <td>
                        <strong>
                            AI-2026-002
                        </strong>
                    </td>

                    <td>
                        24/08/2026
                    </td>

                    <td>
                        CAI/002/2026
                    </td>

                    <td>
                        Denuncia
                    </td>

                    <td>
                        Tránsito
                    </td>

                    <td>
                        <span
                            class="
                                dashboard-recientes__estado
                                dashboard-recientes__estado--finalizado
                            "
                        >
                            Finalizado
                        </span>
                    </td>

                </tr>


                <tr>

                    <td>
                        <strong>
                            AI-2026-003
                        </strong>
                    </td>

                    <td>
                        23/08/2026
                    </td>

                    <td>
                        CAI/003/2026
                    </td>

                    <td>
                        Queja
                    </td>

                    <td>
                        Operaciones
                    </td>

                    <td>
                        <span
                            class="
                                dashboard-recientes__estado
                                dashboard-recientes__estado--pendiente
                            "
                        >
                            Pendiente
                        </span>
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>