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


    /* =====================================================
       ABRIR DETALLE
    ===================================================== */

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


        /* =================================================
           DATOS
        ================================================= */

        const felicitacion =
            resultado.felicitacion
            || {};


        const personal =
            Array.isArray(
                resultado.personal
            )
                ? resultado.personal
                : [];


        /* =================================================
           RENDER
        ================================================= */

        renderizarDetalle(
            felicitacion,
            personal
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
   RENDERIZAR DETALLE
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


    /* =====================================================
       SIN PERSONAL
    ===================================================== */

    if (
        !Array.isArray(personal)
        || personal.length === 0
    ) {

        tbody.innerHTML = `
            <tr>
                <td colspan="5">
                    Sin personal relacionado
                </td>
            </tr>
        `;


        return;
    }


    /* =====================================================
       PERSONAL REGISTRADO
    ===================================================== */

    personal.forEach(
        (persona) => {

            /* =================================================
               DATOS
            ================================================= */

            const nombre =
                String(
                    persona.nombre_snapshot
                    || persona.nombre
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const area =
                String(
                    persona.area_snapshot
                    || persona.area
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const turno =
                String(
                    persona.turno_snapshot
                    || persona.turno
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const alias =
                String(
                    persona.alias_snapshot
                    || persona.alias
                    || ''
                ).trim();


            const foto =
                obtenerFotoPersonal(
                    persona
                );


            const inicial =
                obtenerInicialApellido(
                    nombre
                );


            /* =================================================
               FILA
            ================================================= */

            const fila =
                document.createElement(
                    'tr'
                );


            fila.innerHTML = `

                <!-- FOTO -->

                <td>

                    <div class="modal-felicitacion-detalle__foto">

                        <img
                            alt=""
                            data-foto-detalle-felicitacion
                            hidden
                        >

                        <span
                            data-fallback-detalle-felicitacion
                        >
                            ${escaparHtml(
                                inicial
                            )}
                        </span>

                    </div>

                </td>


                <!-- NOMBRE -->

                <td>

                    ${escaparHtml(
                        nombre
                        || '—'
                    )}

                </td>


                <!-- ÁREA -->

                <td>

                    ${escaparHtml(
                        area
                        || '—'
                    )}

                </td>


                <!-- TURNO -->

                <td>

                    ${escaparHtml(
                        turno
                        || '—'
                    )}

                </td>


                <!-- ALIAS -->

                <td>

                    ${escaparHtml(
                        alias
                        || '—'
                    )}

                </td>
            `;


            /* =================================================
               FOTO / FALLBACK
            ================================================= */

            const imagen =
                fila.querySelector(
                    '[data-foto-detalle-felicitacion]'
                );


            const fallback =
                fila.querySelector(
                    '[data-fallback-detalle-felicitacion]'
                );


            if (
                imagen
                && fallback
            ) {

                /* =============================================
                   ESTADO INICIAL

                   La inicial siempre se muestra primero.
                ============================================== */

                imagen.hidden =
                    true;


                imagen.removeAttribute(
                    'src'
                );


                imagen.style.display =
                    'none';


                fallback.hidden =
                    false;


                fallback.style.display =
                    'flex';


                /* =============================================
                   SI HAY FOTO
                ============================================== */

                if (
                    foto !== ''
                ) {

                    /* =========================================
                       FOTO CARGADA
                    ========================================== */

                    imagen.onload =
                        () => {

                            imagen.hidden =
                                false;


                            imagen.style.display =
                                'block';


                            fallback.hidden =
                                true;


                            fallback.style.display =
                                'none';
                        };


                    /* =========================================
                       FOTO CON ERROR
                    ========================================== */

                    imagen.onerror =
                        () => {

                            imagen.hidden =
                                true;


                            imagen.style.display =
                                'none';


                            imagen.removeAttribute(
                                'src'
                            );


                            fallback.hidden =
                                false;


                            fallback.style.display =
                                'flex';
                        };


                    /* =========================================
                       INICIAR CARGA
                    ========================================== */

                    imagen.src =
                        foto;
                }
            }


            /* =================================================
               AGREGAR FILA
            ================================================= */

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


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    document.body.classList.add(
        'modal-abierto'
    );
}


function cerrarModal(
    modal
) {

    const activo =
        document.activeElement;


    if (
        activo
        && modal.contains(
            activo
        )
    ) {

        activo.blur();
    }


    modal.hidden =
        true;


    modal.setAttribute(
        'aria-hidden',
        'true'
    );


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


/* =========================================================
   OBTENER INICIAL DEL PRIMER APELLIDO
========================================================= */

function obtenerInicialApellido(
    nombreCompleto
) {

    const nombre =
        String(
            nombreCompleto
            || ''
        ).trim();


    if (
        nombre === ''
    ) {

        return '?';
    }


    const partes =
        nombre
            .split(/\s+/)
            .filter(Boolean);


    if (
        partes.length === 0
    ) {

        return '?';
    }


    return partes[0]
        .charAt(0)
        .toUpperCase();
}


/* =========================================================
   OBTENER FOTO DEL PERSONAL
========================================================= */

function obtenerFotoPersonal(
    persona
) {

    return String(
        persona?.foto
        || ''
    ).trim();
}