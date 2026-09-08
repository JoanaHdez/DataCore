/* =========================================================
   FELICITACIONES
   DETALLE
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarDetalleFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarDetalleFelicitacion() {

    const modal =
        document.querySelector(
            '#modal-detalle-felicitacion'
        );


    const botonesVer =
        document.querySelectorAll(
            '[data-accion-felicitacion="ver"]'
        );


    if (
        !modal
        || botonesVer.length === 0
    ) {
        return;
    }


    botonesVer.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                async () => {

                    const idFelicitacion =
                        Number(
                            boton.dataset.idFelicitacion
                            || 0
                        );


                    if (
                        idFelicitacion <= 0
                    ) {
                        return;
                    }


                    await cargarDetalleFelicitacion(
                        idFelicitacion,
                        modal
                    );
                }
            );
        }
    );


    /* =====================================================
       CERRAR
    ===================================================== */

    modal
        .querySelectorAll(
            '[data-cerrar-detalle-felicitacion]'
        )
        .forEach(
            (elemento) => {

                elemento.addEventListener(
                    'click',
                    () => {
                        cerrarModal(
                            modal
                        );
                    }
                );
            }
        );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
                && !modal.hidden
            ) {

                cerrarModal(
                    modal
                );
            }
        }
    );
}


/* =========================================================
   CARGAR DETALLE
========================================================= */

async function cargarDetalleFelicitacion(
    idFelicitacion,
    modal
) {

    try {

        const url =
            new URL(
                `DataCore/public/asuntos-internos/reportes/felicitaciones/detalle/${idFelicitacion}`,
                `${window.location.origin}/`
            );


        const respuesta =
            await fetch(
                url.toString(),
                {
                    method:
                        'GET',

                    headers: {
                        Accept:
                            'application/json',
                    },

                    credentials:
                        'same-origin',
                }
            );


        const resultado =
            await respuesta.json();


        if (
            !respuesta.ok
            || resultado?.success !== true
        ) {

            throw new Error(
                resultado?.message
                || 'No fue posible consultar la felicitación.'
            );
        }


        renderizarDetalle(
            resultado.felicitacion
            || {},
            Array.isArray(
                resultado.personal
            )
                ? resultado.personal
                : []
        );


        abrirModal(
            modal
        );


    } catch (error) {

        console.error(
            'Error consultando detalle de felicitación:',
            error
        );


        alert(
            error.message
            || 'No fue posible consultar la felicitación.'
        );
    }
}


/* =========================================================
   RENDERIZAR
========================================================= */

function renderizarDetalle(
    felicitacion,
    personal
) {

    /* =====================================================
       TÍTULO DEL MODAL
    ===================================================== */

    const tituloFolio =
        document.querySelector(
            '#detalle-felicitacion-titulo-folio'
        );


    if (tituloFolio) {

        tituloFolio.textContent =
            felicitacion.folio
                ? ` ${felicitacion.folio}`
                : '';
    }


    /* =====================================================
       DATOS GENERALES
    ===================================================== */

    asignarTexto(
        '#detalle-felicitacion-folio',
        felicitacion.folio
    );


    asignarTexto(
        '#detalle-felicitacion-fecha',
        felicitacion.fecha_registro
    );


    asignarTexto(
        '#detalle-felicitacion-felicitante',
        felicitacion.nombre_felicitante
    );


    asignarTexto(
        '#detalle-felicitacion-razon',
        felicitacion.razon_felicitacion
    );


    /* =====================================================
       PERSONAL
    ===================================================== */

    renderizarPersonal(
        personal
    );
}


/* =========================================================
   PERSONAL
========================================================= */

function renderizarPersonal(
    personal
) {

    const tbody =
        document.querySelector(
            '#detalle-felicitacion-personal'
        );


    if (!tbody) {
        return;
    }


    tbody.innerHTML =
        '';


    if (
        personal.length === 0
    ) {

        tbody.innerHTML = `
            <tr>
                <td colspan="4">
                    Sin personal relacionado
                </td>
            </tr>
        `;

        return;
    }


    personal.forEach(
        (persona) => {

            const fila =
                document.createElement(
                    'tr'
                );


            fila.innerHTML = `
                <td>
                    ${escaparHtml(
                        persona.nombre_snapshot
                        || '—'
                    )}
                </td>

                <td>
                    ${escaparHtml(
                        persona.area_snapshot
                        || '—'
                    )}
                </td>

                <td>
                    ${escaparHtml(
                        persona.turno_snapshot
                        || '—'
                    )}
                </td>

                <td>
                    ${escaparHtml(
                        persona.alias_snapshot
                        || '—'
                    )}
                </td>
            `;


            tbody.appendChild(
                fila
            );
        }
    );
}


/* =========================================================
   MODAL
========================================================= */

function abrirModal(
    modal
) {

    modal.hidden =
        false;


    document.body.classList.add(
        'modal-abierto'
    );
}


function cerrarModal(
    modal
) {

    modal.hidden =
        true;


    document.body.classList.remove(
        'modal-abierto'
    );
}


/* =========================================================
   UTILIDADES
========================================================= */

function asignarTexto(
    selector,
    valor
) {

    const elemento =
        document.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    const texto =
        String(
            valor
            ?? ''
        ).trim();


    elemento.textContent =
        texto !== ''
            ? texto
            : '—';
}


function escaparHtml(
    valor
) {

    return String(
        valor
        ?? ''
    )
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}