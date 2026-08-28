/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Paginación
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarPaginacionReportes();
});


function inicializarPaginacionReportes() {

    const tbody =
        document.querySelector(
            '#tabla-reportes-body'
        );

    const desde =
        document.querySelector(
            '#paginacion-desde'
        );

    const hasta =
        document.querySelector(
            '#paginacion-hasta'
        );

    const total =
        document.querySelector(
            '#paginacion-total'
        );

    const paginas =
        document.querySelector(
            '#paginacion-paginas'
        );

    const anterior =
        document.querySelector(
            '#paginacion-anterior'
        );

    const siguiente =
        document.querySelector(
            '#paginacion-siguiente'
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


    const POR_PAGINA = 10;

    let paginaActual = 1;

    let filasFiltradas = [];


    /* =====================================================
       OBTENER FILAS REALES
    ===================================================== */

    function obtenerFilasReales() {

        return Array.from(
            tbody.querySelectorAll('tr')
        ).filter((fila) => {

            return !fila.classList.contains(
                'reportes-tabla__empty'
            );

        });

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
                    cantidad / POR_PAGINA
                )
            );


        if (paginaActual > totalPaginas) {
            paginaActual = totalPaginas;
        }


        if (paginaActual < 1) {
            paginaActual = 1;
        }


        const inicio =
            (paginaActual - 1)
            * POR_PAGINA;


        const fin =
            Math.min(
                inicio + POR_PAGINA,
                cantidad
            );


        /* =================================================
           OCULTAR TODAS LAS FILAS
        ================================================= */

        obtenerFilasReales()
            .forEach((fila) => {

                fila.hidden = true;

            });


        /* =================================================
           MOSTRAR SOLO LA PÁGINA ACTUAL
        ================================================= */

        filasFiltradas
            .slice(
                inicio,
                fin
            )
            .forEach((fila) => {

                fila.hidden = false;

            });


        /* =================================================
           INFORMACIÓN
        ================================================= */

        if (cantidad === 0) {

            desde.textContent = '0';
            hasta.textContent = '0';
            total.textContent = '0';

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
           BOTONES
        ================================================= */

        anterior.disabled =
            paginaActual <= 1
            || cantidad === 0;


        siguiente.disabled =
            paginaActual >= totalPaginas
            || cantidad === 0;


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

        paginas.innerHTML = '';


        if (cantidad === 0) {
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
                String(numero);


            if (
                numero === paginaActual
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

            if (paginaActual <= 1) {
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
        'reportesFiltradosActualizados',
        (evento) => {

            filasFiltradas =
                Array.isArray(
                    evento.detail?.filas
                )
                    ? evento.detail.filas
                    : [];


            /*
             * Al aplicar cualquier filtro,
             * volvemos a la página 1.
             */
            paginaActual = 1;


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