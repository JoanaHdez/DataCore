document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarPersonalIndividualDashboard();

    }
);


/* =========================================================
   PERSONAL INDIVIDUAL
========================================================= */

function inicializarPersonalIndividualDashboard() {

    const inputBusqueda =
        document.querySelector(
            '#dashboard-personal-individual-busqueda'
        );


    const inputValor =
        document.querySelector(
            '#dashboard-personal-individual-valor'
        );


    const contenedorResultados =
        document.querySelector(
            '#dashboard-personal-individual-resultados'
        );


    const contenedorSeleccion =
        document.querySelector(
            '#dashboard-personal-individual-seleccion'
        );


    const textoSeleccion =
        document.querySelector(
            '#dashboard-personal-individual-seleccion-texto'
        );


    const botonQuitar =
        document.querySelector(
            '#dashboard-personal-individual-quitar'
        );


    const modalMotivos =
        document.querySelector(
            '#modal-personal-individual-motivos'
        );


    const botonesMotivos =
        document.querySelectorAll(
            '[data-personal-individual-motivos]'
        );


    if (
        !inputBusqueda
        || !inputValor
        || !contenedorResultados
        || !contenedorSeleccion
        || !textoSeleccion
        || !botonQuitar
    ) {

        return;
    }


    let temporizadorBusqueda =
        null;


    let controladorBusqueda =
        null;


    /* =====================================================
       BUSCAR AL ESCRIBIR
    ===================================================== */

    inputBusqueda.addEventListener(
        'input',
        () => {

            const termino =
                inputBusqueda.value.trim();


            if (temporizadorBusqueda) {

                clearTimeout(
                    temporizadorBusqueda
                );
            }


            if (termino === '') {

                ocultarResultados();

                return;
            }


            temporizadorBusqueda =
                window.setTimeout(
                    () => {

                        buscarPersonal(
                            termino
                        );

                    },
                    300
                );

        }
    );


    /* =====================================================
       CONSULTAR PERSONAL
    ===================================================== */

    async function buscarPersonal(
        termino
    ) {

        if (controladorBusqueda) {

            controladorBusqueda.abort();
        }


        controladorBusqueda =
            new AbortController();


        try {

            const url =
                new URL(
                    'DataCore/public/asuntos-internos/reportes/personal/buscar',
                    `${window.location.origin}/`
                );


            url.searchParams.set(
                'q',
                termino
            );


            const respuesta =
                await fetch(
                    url.toString(),
                    {
                        method: 'GET',

                        headers: {
                            Accept:
                                'application/json',
                        },

                        signal:
                            controladorBusqueda.signal,
                    }
                );


            if (!respuesta.ok) {

                throw new Error(
                    'No fue posible consultar el personal.'
                );
            }


            const datos =
                await respuesta.json();


            renderizarResultados(
                Array.isArray(
                    datos.personal
                )
                    ? datos.personal
                    : []
            );

        } catch (error) {

            if (
                error.name === 'AbortError'
            ) {

                return;
            }


            console.error(
                'Error buscando personal individual:',
                error
            );


            mostrarMensaje(
                'No fue posible consultar el personal.'
            );
        }
    }


    /* =====================================================
       RENDERIZAR RESULTADOS
    ===================================================== */

    function renderizarResultados(
        personal
    ) {

        contenedorResultados.innerHTML =
            '';


        if (!personal.length) {

            mostrarMensaje(
                'No se encontraron coincidencias.'
            );

            return;
        }


        personal.forEach(
            persona => {

                const boton =
                    document.createElement(
                        'button'
                    );


                boton.type =
                    'button';


                boton.className =
                    'dashboard-personal-resultados__item';


                const nombre =
                    String(
                        persona.nombre
                        || 'Sin nombre'
                    ).trim();


                const nomina =
                    String(
                        persona.nomina
                        || ''
                    ).trim();


                const area =
                    String(
                        persona.area
                        || ''
                    ).trim();


                const perscod =
                    String(
                        persona.perscod
                        || ''
                    ).trim();


                const plantillaId =
                    Number(
                        persona.id
                    ) || 0;


                const inicial =
                    obtenerInicial(
                        nombre
                    );


                boton.innerHTML = `
                    <span class="dashboard-personal-resultados__avatar">
                        ${escaparHtml(inicial)}
                    </span>

                    <span class="dashboard-personal-resultados__datos">

                        <strong>
                            ${escaparHtml(nombre)}
                        </strong>

                        <small>
                            Nómina:
                            ${escaparHtml(
                                nomina || '—'
                            )}
                        </small>

                        <small>
                            ${escaparHtml(
                                area || 'Sin área'
                            )}
                        </small>

                    </span>
                `;


                boton.addEventListener(
                    'click',
                    () => {

                        seleccionarPersona(
                            {
                                plantillaId,
                                perscod,
                                nombre,
                                nomina,
                            }
                        );

                    }
                );


                contenedorResultados.appendChild(
                    boton
                );

            }
        );


        contenedorResultados.hidden =
            false;
    }


    /* =====================================================
       SELECCIONAR PERSONA
    ===================================================== */

    function seleccionarPersona(
        persona
    ) {

        const valor =
            persona.perscod !== ''
                ? persona.perscod
                : String(
                    persona.plantillaId
                    || ''
                );


        if (valor === '') {

            return;
        }


        inputValor.value =
            valor;


        const url =
            new URL(
                window.location.href
            );


        url.searchParams.set(
            'personal_individual',
            valor
        );


        window.location.href =
            url.toString();
    }


    /* =====================================================
       QUITAR PERSONA
    ===================================================== */

    botonQuitar.addEventListener(
        'click',
        () => {

            const url =
                new URL(
                    window.location.href
                );


            url.searchParams.delete(
                'personal_individual'
            );


            window.location.href =
                url.toString();

        }
    );


    /* =====================================================
       MODAL DE MOTIVOS
    ===================================================== */

    if (
        modalMotivos
        && botonesMotivos.length
    ) {

        botonesMotivos.forEach(
            boton => {

                boton.addEventListener(
                    'click',
                    () => {

                        abrirModalMotivos();
                    }
                );
            }
        );


        modalMotivos.addEventListener(
            'click',
            evento => {

                if (
                    evento.target.closest(
                        '[data-personal-individual-motivos-cerrar]'
                    )
                ) {

                    cerrarModalMotivos();
                }
            }
        );


        document.addEventListener(
            'keydown',
            evento => {

                if (
                    evento.key === 'Escape'
                    && modalMotivos.classList.contains(
                        'modal-reporte--visible'
                    )
                ) {

                    cerrarModalMotivos();
                }
            }
        );
    }


    function abrirModalMotivos() {

        if (!modalMotivos) {

            return;
        }


        modalMotivos.classList.add(
            'modal-reporte--visible'
        );


        modalMotivos.setAttribute(
            'aria-hidden',
            'false'
        );


        document.body.classList.add(
            'modal-abierto'
        );
    }


    function cerrarModalMotivos() {

        if (!modalMotivos) {

            return;
        }


        modalMotivos.classList.remove(
            'modal-reporte--visible'
        );


        modalMotivos.setAttribute(
            'aria-hidden',
            'true'
        );


        document.body.classList.remove(
            'modal-abierto'
        );
    }


    /* =====================================================
       MENSAJE
    ===================================================== */

    function mostrarMensaje(
        mensaje
    ) {

        contenedorResultados.innerHTML = `
            <div class="dashboard-personal-resultados__vacio">
                ${escaparHtml(mensaje)}
            </div>
        `;


        contenedorResultados.hidden =
            false;
    }


    /* =====================================================
       OCULTAR RESULTADOS
    ===================================================== */

    function ocultarResultados() {

        contenedorResultados.hidden =
            true;


        contenedorResultados.innerHTML =
            '';
    }


    /* =====================================================
       CERRAR RESULTADOS AL HACER CLIC FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        evento => {

            if (
                evento.target === inputBusqueda
                || contenedorResultados.contains(
                    evento.target
                )
            ) {

                return;
            }


            ocultarResultados();

        }
    );

}


/* =========================================================
   INICIAL
========================================================= */

function obtenerInicial(
    nombre
) {

    const texto =
        String(
            nombre || ''
        ).trim();


    if (texto === '') {

        return '?';
    }


    return texto
        .charAt(0)
        .toUpperCase();
}


/* =========================================================
   ESCAPAR HTML
========================================================= */

function escaparHtml(
    valor
) {

    return String(
        valor
        ?? ''
    )
        .replaceAll(
            '&',
            '&amp;'
        )
        .replaceAll(
            '<',
            '&lt;'
        )
        .replaceAll(
            '>',
            '&gt;'
        )
        .replaceAll(
            '"',
            '&quot;'
        )
        .replaceAll(
            "'",
            '&#039;'
        );
}
