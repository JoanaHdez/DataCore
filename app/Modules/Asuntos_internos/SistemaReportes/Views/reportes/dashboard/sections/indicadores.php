<section class="dashboard-indicadores">

    <div class="dashboard-indicadores__encabezado">

        <div>

            <span class="dashboard-indicadores__eyebrow">
                Resumen general
            </span>

            <h2 class="dashboard-indicadores__titulo">
                Indicadores de reportes
            </h2>

        </div>


        <span class="dashboard-indicadores__periodo">
            Periodo consultado
        </span>

    </div>


    <div class="dashboard-indicadores__grid">

        <!-- =================================================
             TOTAL DE REPORTES
        ================================================== -->

        <article class="dashboard-indicador dashboard-indicador--total">

            <div class="dashboard-indicador__icono" aria-hidden="true">

                <svg viewBox="0 0 24 24">

                    <path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                    <path d="M7 9h10" />
                    <path d="M7 13h7" />
                    <path d="M7 17h4" />

                </svg>

            </div>


            <div class="dashboard-indicador__contenido">

                <span class="dashboard-indicador__etiqueta">
                    Total de reportes
                </span>

                <strong class="dashboard-indicador__valor" id="dashboard-total-reportes">
                    <?= esc($indicadores['total'] ?? 0) ?>
                </strong>

                <span class="dashboard-indicador__descripcion">
                    Registros encontrados
                </span>

            </div>

        </article>


        <!-- =================================================
             QUEJAS
        ================================================== -->

        <article class="dashboard-indicador dashboard-indicador--quejas">

            <div class="dashboard-indicador__icono" aria-hidden="true">

                <svg viewBox="0 0 24 24">

                    <path d="M6 3h9l3 3v15H6Z" />
                    <path d="M15 3v4h4" />
                    <path d="M9 11h6" />
                    <path d="M9 15h4" />

                </svg>

            </div>


            <div class="dashboard-indicador__contenido">

                <span class="dashboard-indicador__etiqueta">
                    Quejas
                </span>

                <strong class="dashboard-indicador__valor" id="dashboard-total-quejas">
                    <?= esc($indicadores['quejas'] ?? 0) ?>
                </strong>

                <span class="dashboard-indicador__descripcion">
                    Quejas registradas
                </span>

            </div>

        </article>


        <!-- =================================================
             PENDIENTES
        ================================================== -->

        <article class="dashboard-indicador dashboard-indicador--pendiente">

            <div class="dashboard-indicador__icono" aria-hidden="true">

                <svg viewBox="0 0 24 24">

                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v5l3 2" />

                </svg>

            </div>


            <div class="dashboard-indicador__contenido">

                <span class="dashboard-indicador__etiqueta">
                    Pendientes
                </span>

                <strong class="dashboard-indicador__valor" id="dashboard-total-pendientes">
                    <?= esc($indicadores['pendientes'] ?? 0) ?>
                </strong>

                <span class="dashboard-indicador__descripcion">
                    Requieren atención
                </span>

            </div>

        </article>


        <!-- =================================================
             EN PROCESO
        ================================================== -->

        <article class="dashboard-indicador dashboard-indicador--proceso">

            <div class="dashboard-indicador__icono" aria-hidden="true">

                <svg viewBox="0 0 24 24">

                    <path d="M4 12a8 8 0 0 1 13.7-5.7" />
                    <path d="M18 3v4h-4" />
                    <path d="M20 12a8 8 0 0 1-13.7 5.7" />
                    <path d="M6 21v-4h4" />

                </svg>

            </div>


            <div class="dashboard-indicador__contenido">

                <span class="dashboard-indicador__etiqueta">
                    En proceso
                </span>

                <strong class="dashboard-indicador__valor" id="dashboard-total-proceso">
                    <?= esc($indicadores['en_proceso'] ?? 0) ?>
                </strong>

                <span class="dashboard-indicador__descripcion">
                    Actualmente en atención
                </span>

            </div>

        </article>


        <!-- =================================================
             FINALIZADOS
        ================================================== -->

        <article class="dashboard-indicador dashboard-indicador--finalizado">

            <div class="dashboard-indicador__icono" aria-hidden="true">

                <svg viewBox="0 0 24 24">

                    <circle cx="12" cy="12" r="9" />
                    <path d="m8 12 2.5 2.5L16 9" />

                </svg>

            </div>


            <div class="dashboard-indicador__contenido">

                <span class="dashboard-indicador__etiqueta">
                    Finalizados
                </span>

                <strong class="dashboard-indicador__valor" id="dashboard-total-finalizados">
                    <?= esc($indicadores['finalizados'] ?? 0) ?>
                </strong>

                <span class="dashboard-indicador__descripcion">
                    Casos concluidos
                </span>

            </div>

        </article>


        <!-- =================================================
             QUEJAS ANÓNIMAS
        ================================================== -->

        <article class="dashboard-indicador dashboard-indicador--anonimas">

            <div class="dashboard-indicador__icono" aria-hidden="true">

                <svg viewBox="0 0 24 24">

                    <circle cx="12" cy="8" r="3" />
                    <path d="M5 20a7 7 0 0 1 14 0" />
                    <path d="M4 4l16 16" />

                </svg>

            </div>


            <div class="dashboard-indicador__contenido">

                <span class="dashboard-indicador__etiqueta">
                    Quejas anónimas
                </span>

                <strong class="dashboard-indicador__valor" id="dashboard-total-anonimas">
                    <?= esc($indicadores['anonimas'] ?? 0) ?>
                </strong>

                <span class="dashboard-indicador__descripcion">
                    Sin identificación del quejoso
                </span>

            </div>

        </article>


        <!-- =================================================
             PERSONAL INVOLUCRADO
        ================================================== -->

        <article class="dashboard-indicador dashboard-indicador--personal">

            <div class="dashboard-indicador__icono" aria-hidden="true">

                <svg viewBox="0 0 24 24">

                    <circle cx="9" cy="8" r="3" />
                    <circle cx="17" cy="9" r="2" />

                    <path d="M3 20a6 6 0 0 1 12 0" />
                    <path d="M14 20a5 5 0 0 1 7 0" />

                </svg>

            </div>


            <div class="dashboard-indicador__contenido">

                <span class="dashboard-indicador__etiqueta">
                    Personal involucrado
                </span>

                <strong class="dashboard-indicador__valor" id="dashboard-total-personal">
                    <?= esc($indicadores['personal_involucrado'] ?? 0) ?>
                </strong>

                <span class="dashboard-indicador__descripcion">
                    Personas distintas asociadas
                </span>

            </div>

        </article>

    </div>

</section>