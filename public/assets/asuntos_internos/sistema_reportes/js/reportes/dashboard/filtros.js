document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarFiltrosDashboard();
    }
);


/* =========================================================
   FILTROS DEL DASHBOARD
   INTERFAZ
========================================================= */

function inicializarFiltrosDashboard() {

    const botonMasFiltros =
        document.querySelector(
            '#dashboard-mas-filtros'
        );


    const panelAvanzado =
        document.querySelector(
            '#dashboard-filtros-avanzados'
        );


    if (
        !botonMasFiltros
        || !panelAvanzado
    ) {
        return;
    }


    botonMasFiltros.addEventListener(
        'click',
        () => {

            const estaAbierto =
                botonMasFiltros.getAttribute(
                    'aria-expanded'
                ) === 'true';


            if (estaAbierto) {

                cerrarFiltrosAvanzados(
                    botonMasFiltros,
                    panelAvanzado
                );

                return;
            }


            abrirFiltrosAvanzados(
                botonMasFiltros,
                panelAvanzado
            );

        }
    );

}


/* =========================================================
   ABRIR
========================================================= */

function abrirFiltrosAvanzados(
    boton,
    panel
) {

    panel.hidden =
        false;


    boton.setAttribute(
        'aria-expanded',
        'true'
    );


    const texto =
        boton.querySelector(
            'span'
        );


    if (texto) {

        texto.textContent =
            'Menos filtros';

    }

}


/* =========================================================
   CERRAR
========================================================= */

function cerrarFiltrosAvanzados(
    boton,
    panel
) {

    panel.hidden =
        true;


    boton.setAttribute(
        'aria-expanded',
        'false'
    );


    const texto =
        boton.querySelector(
            'span'
        );


    if (texto) {

        texto.textContent =
            'Más filtros';

    }

}