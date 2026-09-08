/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Paginación
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarPaginacionFelicitaciones();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarPaginacionFelicitaciones() {

    const tbody =
        document.querySelector(
            '#tabla-felicitaciones-body'
        );


    const desde =
        document.querySelector(
            '#felicitaciones-paginacion-desde'
        );


    const hasta =
        document.querySelector(
            '#felicitaciones-paginacion-hasta'
        );


    const total =
        document.querySelector(
            '#felicitaciones-paginacion-total'
        );


    const paginas =
        document.querySelector(
            '#felicitaciones-paginacion-paginas'
        );


    const anterior =
        document.querySelector(
            '#felicitaciones-paginacion-anterior'
        );


    const siguiente =
        document.querySelector(
            '#felicitaciones-paginacion-siguiente'
        );


    if (
        !tbody
        || !desde
        || !hasta
        || !total
        || !paginas
        || !anterior
        || !siguiente
    ) {
        return;
    }


    const POR_PAGINA =
        10;


    let paginaActual =
        1;


    let filasFiltradas =
        [];


    /* =====================================================
       OBTENER FILAS REALES
    ===================================================== */

    function obtenerFilasReales() {

        return Array.from(
            tbody.querySelectorAll(
                'tr'
            )
        ).filter(
            (fila) => {

                return !fila.classList.contains(
                    'felicitaciones-tabla__empty'
                );

            }
        );
    }


    /* =====================================================
       ACTUALIZAR PAGINACIÓN
    ===================================================== */

    function actualizarPaginacion() {

        const cantidad =
            filasFiltradas.length;


        const totalPaginas =
            Math.max(
                1,
                Math.ceil(
                    cantidad
                    / POR_PAGINA
                )
            );


        if (
            paginaActual
            > totalPaginas
        ) {

            paginaActual =
                totalPaginas;
        }


        if (
            paginaActual
            < 1
        ) {

            paginaActual =
                1;
        }


        const inicio =
            (
                paginaActual
                - 1
            )
            * POR_PAGINA;


        const fin =
            Math.min(
                inicio
                + POR_PAGINA,
                cantidad
            );


        /* =================================================
           OCULTAR TODAS LAS FILAS
        ================================================= */

        obtenerFilasReales()
            .forEach(
                (fila) => {

                    fila.hidden =
                        true;

                }
            );


        /* =================================================
           MOSTRAR SOLO LA PÁGINA ACTUAL
        ================================================= */

        filasFiltradas
            .slice(
                inicio,
                fin
            )
            .forEach(
                (fila) => {

                    fila.hidden =
                        false;

                }
            );


        /* =================================================
           INFORMACIÓN
        ================================================= */

        if (
            cantidad === 0
        ) {

            desde.textContent =
                '0';


            hasta.textContent =
                '0';


            total.textContent =
                '0';

        } else {

            desde.textContent =
                String(
                    inicio + 1
                );


            hasta.textContent =
                String(
                    fin
                );


            total.textContent =
                String(
                    cantidad
                );
        }


        /* =================================================
           BOTONES ANTERIOR / SIGUIENTE
        ================================================= */

        anterior.disabled =
            paginaActual <= 1
            || cantidad === 0;


        siguiente.disabled =
            paginaActual >= totalPaginas
            || cantidad === 0;


        /* =================================================
           NÚMEROS DE PÁGINA
        ================================================= */

        renderizarPaginas(
            totalPaginas,
            cantidad
        );
    }


    /* =====================================================
       RENDERIZAR NÚMEROS DE PÁGINA
    ===================================================== */

    function renderizarPaginas(
        totalPaginas,
        cantidad
    ) {

        paginas.innerHTML =
            '';


        if (
            cantidad === 0
        ) {
            return;
        }


        for (
            let numero = 1;
            numero <= totalPaginas;
            numero++
        ) {

            const boton =
                document.createElement(
                    'button'
                );


            boton.type =
                'button';


            boton.className =
                'reportes-paginacion__button';


            boton.textContent =
                String(
                    numero
                );


            if (
                numero ===
                paginaActual
            ) {

                boton.classList.add(
                    'reportes-paginacion__button--active'
                );
            }


            boton.addEventListener(
                'click',
                () => {

                    paginaActual =
                        numero;


                    actualizarPaginacion();
                }
            );


            paginas.appendChild(
                boton
            );
        }
    }


    /* =====================================================
       ANTERIOR
    ===================================================== */

    anterior.addEventListener(
        'click',
        () => {

            if (
                paginaActual <= 1
            ) {
                return;
            }


            paginaActual--;


            actualizarPaginacion();
        }
    );


    /* =====================================================
       SIGUIENTE
    ===================================================== */

    siguiente.addEventListener(
        'click',
        () => {

            const totalPaginas =
                Math.max(
                    1,
                    Math.ceil(
                        filasFiltradas.length
                        / POR_PAGINA
                    )
                );


            if (
                paginaActual
                >= totalPaginas
            ) {
                return;
            }


            paginaActual++;


            actualizarPaginacion();
        }
    );


    /* =====================================================
       RECIBIR RESULTADOS DE FILTROS
    ===================================================== */

    document.addEventListener(
        'felicitacionesFiltradasActualizadas',
        (evento) => {

            filasFiltradas =
                Array.isArray(
                    evento.detail?.filas
                )
                    ? evento.detail.filas
                    : [];


            /*
             * Cada vez que cambian los filtros
             * regresamos a la página 1.
             */
            paginaActual =
                1;


            actualizarPaginacion();
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    filasFiltradas =
        obtenerFilasReales();


    actualizarPaginacion();
}