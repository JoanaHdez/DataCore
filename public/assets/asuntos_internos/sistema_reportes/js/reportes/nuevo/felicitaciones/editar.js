/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Editar
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarEditarFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarEditarFelicitacion() {

    const modal =
        document.querySelector(
            '#modal-editar-felicitacion'
        );


    const botonesEditar =
        document.querySelectorAll(
            '[data-accion-felicitacion="editar"]'
        );


    if (
        !modal
        || botonesEditar.length === 0
    ) {
        return;
    }


    /* =====================================================
       MODALIDAD DE UNIDAD
    ===================================================== */

    inicializarModalidadUnidadEditar(
        modal
    );


    /* =====================================================
       NAVEGACIÓN ENTRE PESTAÑAS
    ===================================================== */

    const botonesSeccion =
        modal.querySelectorAll(
            '[data-seccion-editar-felicitacion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-panel-editar-felicitacion]'
        );


    botonesSeccion.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                () => {

                    const seccion =
                        String(
                            boton.dataset
                                .seccionEditarFelicitacion
                            || ''
                        ).trim();


                    if (
                        seccion === ''
                    ) {
                        return;
                    }


                    /* =========================================
                       BOTONES
                    ========================================== */

                    botonesSeccion.forEach(
                        (botonActual) => {

                            const activo =
                                botonActual.dataset
                                    .seccionEditarFelicitacion
                                === seccion;


                            botonActual.classList.toggle(
                                'modal-felicitacion-editar__nav-btn--activo',
                                activo
                            );


                            botonActual.setAttribute(
                                'aria-selected',
                                activo
                                    ? 'true'
                                    : 'false'
                            );
                        }
                    );


                    /* =========================================
                       PANELES
                    ========================================== */

                    paneles.forEach(
                        (panel) => {

                            const activo =
                                panel.dataset
                                    .panelEditarFelicitacion
                                === seccion;


                            panel.hidden =
                                !activo;


                            panel.classList.toggle(
                                'modal-felicitacion-editar__panel--activo',
                                activo
                            );
                        }
                    );


                    /* =========================================
                       SUBIR SCROLL DEL BODY
                    ========================================== */

                    const body =
                        modal.querySelector(
                            '.modal-felicitacion-editar__body'
                        );


                    if (body) {

                        body.scrollTop =
                            0;
                    }
                }
            );
        }
    );


    /* =====================================================
       ABRIR DESDE BOTÓN EDITAR
    ===================================================== */

    botonesEditar.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                async () => {

                    const idFelicitacion =
                        Number(
                            boton.dataset.idFelicitacion
                            || 0
                        );


                    const folio =
                        String(
                            boton.dataset.folio
                            || ''
                        ).trim();


                    if (
                        idFelicitacion <= 0
                    ) {
                        return;
                    }


                    const cargado =
                        await prepararModalEditar(
                            modal,
                            idFelicitacion,
                            folio
                        );


                    if (
                        cargado !== true
                    ) {
                        return;
                    }


                    abrirModalEditar(
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
            '[data-cerrar-editar-felicitacion]'
        )
        .forEach(
            (elemento) => {

                elemento.addEventListener(
                    'click',
                    () => {

                        cerrarModalEditar(
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

                cerrarModalEditar(
                    modal
                );
            }
        }
    );
}

/* =========================================================
   PREPARAR MODAL
========================================================= */

async function prepararModalEditar(
    modal,
    idFelicitacion,
    folio
) {

    /* =====================================================
       LIMPIAR DATOS ANTERIORES
    ===================================================== */

    limpiarModalEditar(
        modal
    );


    /* =====================================================
       ID
    ===================================================== */

    const inputId =
        modal.querySelector(
            '#editar-felicitacion-id'
        );


    if (inputId) {

        inputId.value =
            String(
                idFelicitacion
            );
    }


    /* =====================================================
       FOLIO DEL TÍTULO
    ===================================================== */

    const tituloFolio =
        modal.querySelector(
            '#editar-felicitacion-titulo-folio'
        );


    if (tituloFolio) {

        tituloFolio.textContent =
            folio !== ''
                ? ` ${folio}`
                : '';
    }


    /* =====================================================
       CONSULTAR DATOS
    ===================================================== */

    try {

        const url =
            new URL(
                `DataCore/public/asuntos-internos/reportes/felicitaciones/detalle/${idFelicitacion}`,
                `${window.location.origin}/`
            );


        console.log(
            'Consultando felicitación para editar:',
            {
                idFelicitacion,
                folio,
                url: url.toString()
            }
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


        /* =================================================
           RESPUESTA JSON
        ================================================= */

        const resultado =
            await respuesta.json();


        /* =================================================
           DEPURACIÓN TEMPORAL

           Esto nos permitirá revisar exactamente qué
           está enviando el backend.
        ================================================= */

        console.log(
            'DETALLE FELICITACIÓN:',
            resultado
        );


        console.log(
            'FELICITACIÓN:',
            resultado?.felicitacion
        );


        console.log(
            'PERSONAL:',
            resultado?.personal
        );


        console.log(
            'UNIDADES:',
            resultado?.unidades
        );


        console.log(
            'PRIMERA UNIDAD COMPLETA:',
            resultado?.unidades?.[0]
        );


        console.table(
            resultado?.unidades
        );


        /* =================================================
           VALIDAR RESPUESTA
        ================================================= */

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
           DATOS GENERALES
        ================================================= */

        cargarDatosGeneralesEditar(
            modal,
            resultado.felicitacion
            || {}
        );


        /* =================================================
           PERSONAL
        ================================================= */

        const personal =
            Array.isArray(
                resultado.personal
            )
                ? resultado.personal
                : [];


        console.log(
            'PERSONAL QUE SE ENVIARÁ AL MODAL:',
            personal
        );


        cargarPersonalEditar(
            modal,
            personal
        );


        /* =================================================
           UNIDADES
        ================================================= */

        const unidades =
            Array.isArray(
                resultado.unidades
            )
                ? resultado.unidades
                : [];


        console.log(
            'UNIDADES QUE SE ENVIARÁN AL MODAL:',
            unidades
        );


        cargarUnidadesEditar(
            modal,
            unidades
        );


        /* =================================================
           CONSULTA COMPLETADA
        ================================================= */

        return true;


    } catch (error) {

        console.error(
            'Error consultando felicitación para editar:',
            error
        );


        return false;
    }
}


/* =========================================================
   CARGAR DATOS GENERALES
========================================================= */

function cargarDatosGeneralesEditar(
    modal,
    felicitacion
) {

    /* =====================================================
       FOLIO
    ===================================================== */

    const inputFolio =
        modal.querySelector(
            '#editar-felicitacion-folio'
        );


    if (inputFolio) {

        inputFolio.value =
            String(
                felicitacion.folio
                || ''
            ).trim();
    }


    /* =====================================================
       FECHA DE REGISTRO
    ===================================================== */

    const inputFecha =
        modal.querySelector(
            '#editar-felicitacion-fecha'
        );


    if (inputFecha) {

        inputFecha.value =
            String(
                felicitacion.fecha_registro
                || ''
            ).trim();
    }


    /* =====================================================
       NOMBRE DE LA PERSONA QUE FELICITA
    ===================================================== */

    const inputFelicitante =
        modal.querySelector(
            '#editar-felicitacion-felicitante'
        );


    if (inputFelicitante) {

        inputFelicitante.value =
            String(
                felicitacion.nombre_felicitante
                || ''
            ).trim();
    }


    /* =====================================================
       RAZÓN DE LA FELICITACIÓN
    ===================================================== */

    const inputRazon =
        modal.querySelector(
            '#editar-felicitacion-razon'
        );


    if (inputRazon) {

        inputRazon.value =
            String(
                felicitacion.razon_felicitacion
                || ''
            ).trim();
    }
}


/* =========================================================
   CARGAR PERSONAL
========================================================= */

function cargarPersonalEditar(
    modal,
    personal
) {

    const tbody =
        modal.querySelector(
            '#editar-felicitacion-personal'
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
                <td colspan="6">
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

            const plantillaId =
                Number(
                    persona.plantilla_id
                    || 0
                );


            const perscod =
                String(
                    persona.perscod
                    || ''
                ).trim();


            const nombre =
                String(
                    persona.nombre_snapshot
                    || ''
                ).trim();


            const nomina =
                String(
                    persona.nomina_snapshot
                    || persona.nomina
                    || perscod
                    || ''
                ).trim();


            const area =
                String(
                    persona.area_snapshot
                    || ''
                ).trim();


            const turno =
                String(
                    persona.turno_snapshot
                    || ''
                ).trim();


            const alias =
                String(
                    persona.alias_snapshot
                    || ''
                ).trim();


            /* =================================================
               INICIAL PARA FOTO TEMPORAL
            ================================================= */

            const inicial =
                nombre !== ''
                    ? nombre.charAt(0).toUpperCase()
                    : '?';


            /* =================================================
               FILA
            ================================================= */

            const fila =
                document.createElement(
                    'tr'
                );


            fila.dataset.plantillaId =
                String(
                    plantillaId
                );


            fila.dataset.perscod =
                perscod;


            fila.innerHTML = `

                <!-- FOTO -->

                <td>

                    <div class="modal-felicitacion-personal-editar__foto">

                        <span>
                            ${escaparHtml(
                inicial
            )}
                        </span>

                    </div>

                </td>


                <!-- NOMBRE -->

                <td>

                    <span class="modal-felicitacion-personal-editar__nombre">

                        ${escaparHtml(
                nombre
                || '—'
            )}

                    </span>

                </td>


                <!-- NÓMINA -->

                <td>

                    ${escaparHtml(
                nomina
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

                    <input
                        type="text"
                        class="modal-felicitacion-personal-editar__turno"
                        value="${escaparHtml(turno)}"
                        data-turno-personal-felicitacion
                        autocomplete="off"
                    >

                </td>


                <!-- ACCIONES -->

                <td>

                    <button
                        type="button"
                        class="button--remove"
                        data-quitar-personal-felicitacion
                    >
                        Quitar
                    </button>

                </td>

            `;


            /* =================================================
               CONSERVAR ALIAS COMO DATO INTERNO

               No lo mostramos como columna porque queremos
               igualar la estructura visual de Quejas.
            ================================================= */

            fila.dataset.alias =
                alias;


            tbody.appendChild(
                fila
            );
        }
    );
}


/* =========================================================
   CARGAR UNIDADES
========================================================= */

function cargarUnidadesEditar(
    modal,
    unidades
) {

    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const radioSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad'
        );


    const contenidoConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad-contenido'
        );


    const contenidoSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad-contenido'
        );


    const contenedorUnidadesAgregadas =
        modal.querySelector(
            '#editar-felicitacion-unidades-agregadas'
        );


    const tbody =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    if (
        !radioConUnidad
        || !radioSinUnidad
        || !contenidoConUnidad
        || !contenidoSinUnidad
        || !contenedorUnidadesAgregadas
        || !tbody
    ) {
        return;
    }


    /* =====================================================
       LIMPIAR TABLA
    ===================================================== */

    tbody.innerHTML =
        '';


    /* =====================================================
       SIN UNIDADES
    ===================================================== */

    if (
        !Array.isArray(unidades)
        || unidades.length === 0
    ) {

        radioConUnidad.checked =
            false;


        radioSinUnidad.checked =
            true;


        /* =================================================
           OCULTAR CONTENIDO CON UNIDAD
        ================================================= */

        contenidoConUnidad.hidden =
            true;


        /* =================================================
           MOSTRAR ESTADO SIN UNIDAD
        ================================================= */

        contenidoSinUnidad.hidden =
            false;


        /* =================================================
           OCULTAR TABLA DE UNIDADES

           Si después cambia manualmente a "Con unidad",
           se mostrará el buscador, pero esta tabla seguirá
           oculta hasta que realmente agregue una unidad.
        ================================================= */

        contenedorUnidadesAgregadas.hidden =
            true;


        return;
    }


    /* =====================================================
       CON UNIDADES
    ===================================================== */

    radioConUnidad.checked =
        true;


    radioSinUnidad.checked =
        false;


    /* =====================================================
       MOSTRAR CONTENIDO CON UNIDAD
    ===================================================== */

    contenidoConUnidad.hidden =
        false;


    /* =====================================================
       OCULTAR ESTADO SIN UNIDAD
    ===================================================== */

    contenidoSinUnidad.hidden =
        true;


    /* =====================================================
       MOSTRAR TABLA PORQUE SÍ EXISTEN UNIDADES
    ===================================================== */

    contenedorUnidadesAgregadas.hidden =
        false;


    /* =====================================================
       RECORRER UNIDADES
    ===================================================== */

    unidades.forEach(
        (unidad) => {

            const idFelicitacionUnidad =
                Number(
                    unidad.id_felicitacion_unidad
                    || 0
                );


            const parqueVehicularId =
                Number(
                    unidad.parque_vehicular_id
                    || unidad.id
                    || 0
                );


            const numeroEconomico =
                String(
                    unidad.no_economico
                    || ''
                ).trim();


            const placas =
                String(
                    unidad.placas
                    || ''
                ).trim();


            const marca =
                String(
                    unidad.marca
                    || ''
                ).trim();


            const submarca =
                String(
                    unidad.submarca
                    || ''
                ).trim();


            const color =
                String(
                    unidad.color
                    || ''
                ).trim();


            const estatus =
                String(
                    unidad.estatus
                    || ''
                ).trim();


            const servicio =
                String(
                    unidad.servicio
                    || ''
                ).trim();


            const tipo =
                String(
                    unidad.tipo
                    || ''
                ).trim();


            const marcaSubmarca =
                [
                    marca,
                    submarca
                ]
                    .filter(
                        (valor) =>
                            valor !== ''
                    )
                    .join(
                        ' '
                    );


            /* =================================================
               FILA
            ================================================= */

            const fila =
                document.createElement(
                    'tr'
                );


            fila.dataset.idFelicitacionUnidad =
                String(
                    idFelicitacionUnidad
                );


            fila.dataset.parqueVehicularId =
                String(
                    parqueVehicularId
                );


            fila.innerHTML = `

                <!-- UNIDAD -->

                <td>

                    <strong class="modal-felicitacion-unidades-editar__unidad-nombre">

                        ${escaparHtml(
                            numeroEconomico
                            || '—'
                        )}

                    </strong>


                    <span class="modal-felicitacion-unidades-editar__unidad-detalle">

                        Placas:
                        ${escaparHtml(
                            placas
                            || 'SIN PLACAS'
                        )}

                    </span>

                </td>


                <!-- MARCA / SUBMARCA -->

                <td>

                    ${escaparHtml(
                        marcaSubmarca
                        || '—'
                    )}

                </td>


                <!-- COLOR -->

                <td>

                    ${escaparHtml(
                        color
                        || '—'
                    )}

                </td>


                <!-- ESTATUS -->

                <td>

                    ${escaparHtml(
                        estatus
                        || '—'
                    )}

                </td>


                <!-- SERVICIO -->

                <td>

                    ${escaparHtml(
                        servicio
                        || '—'
                    )}

                </td>


                <!-- TIPO -->

                <td>

                    ${escaparHtml(
                        tipo
                        || '—'
                    )}

                </td>


                <!-- ACCIONES -->

                <td>

                    <button
                        type="button"
                        class="button--remove"
                        data-quitar-unidad-felicitacion
                    >
                        Quitar
                    </button>

                </td>

            `;


            tbody.appendChild(
                fila
            );
        }
    );
}
/* =========================================================
   LIMPIAR MODAL
========================================================= */

function limpiarModalEditar(
    modal
) {

    /* =====================================================
       ID
    ===================================================== */

    const inputId =
        modal.querySelector(
            '#editar-felicitacion-id'
        );


    if (inputId) {

        inputId.value =
            '';
    }


    /* =====================================================
       TÍTULO
    ===================================================== */

    const tituloFolio =
        modal.querySelector(
            '#editar-felicitacion-titulo-folio'
        );


    if (tituloFolio) {

        tituloFolio.textContent =
            '';
    }


    /* =====================================================
       FOLIO
    ===================================================== */

    const inputFolio =
        modal.querySelector(
            '#editar-felicitacion-folio'
        );


    if (inputFolio) {

        inputFolio.value =
            '';
    }


    /* =====================================================
       FECHA
    ===================================================== */

    const inputFecha =
        modal.querySelector(
            '#editar-felicitacion-fecha'
        );


    if (inputFecha) {

        inputFecha.value =
            '';
    }


    /* =====================================================
       FELICITANTE
    ===================================================== */

    const inputFelicitante =
        modal.querySelector(
            '#editar-felicitacion-felicitante'
        );


    if (inputFelicitante) {

        inputFelicitante.value =
            '';
    }


    /* =====================================================
       RAZÓN DE LA FELICITACIÓN
    ===================================================== */

    const inputRazon =
        modal.querySelector(
            '#editar-felicitacion-razon'
        );


    if (inputRazon) {

        inputRazon.value =
            '';
    }


    /* =====================================================
       PERSONAL
    ===================================================== */

    const tbodyPersonal =
        modal.querySelector(
            '#editar-felicitacion-personal'
        );


    if (tbodyPersonal) {

        tbodyPersonal.innerHTML = `
            <tr>
                <td colspan="6">
                    Sin personal relacionado
                </td>
            </tr>
        `;
    }


    /* =====================================================
    UNIDADES
    ===================================================== */

    const tbodyUnidades =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    if (tbodyUnidades) {

        tbodyUnidades.innerHTML = `
            <tr>
                <td colspan="7">
                    Sin unidades relacionadas
                </td>
            </tr>
        `;
    }


    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const radioSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad'
        );


    if (radioConUnidad) {
        radioConUnidad.checked = false;
    }


    if (radioSinUnidad) {
        radioSinUnidad.checked = false;
    }

    /* =====================================================
       REGRESAR A PRIMERA PESTAÑA
    ===================================================== */

    const botones =
        modal.querySelectorAll(
            '[data-seccion-editar-felicitacion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-panel-editar-felicitacion]'
        );


    botones.forEach(
        (boton) => {

            const activo =
                boton.dataset
                    .seccionEditarFelicitacion
                === 'datos';


            boton.classList.toggle(
                'modal-felicitacion-editar__nav-btn--activo',
                activo
            );
        }
    );


    paneles.forEach(
        (panel) => {

            const activo =
                panel.dataset
                    .panelEditarFelicitacion
                === 'datos';


            panel.hidden =
                !activo;


            panel.classList.toggle(
                'modal-felicitacion-editar__panel--activo',
                activo
            );
        }
    );
}


/* =========================================================
   ABRIR
========================================================= */

function abrirModalEditar(
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


/* =========================================================
   CERRAR
========================================================= */

function cerrarModalEditar(
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
   INICIALIZAR MODALIDAD DE UNIDAD
========================================================= */

function inicializarModalidadUnidadEditar(
    modal
) {

    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const radioSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad'
        );


    const contenidoConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad-contenido'
        );


    const contenidoSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad-contenido'
        );


    if (
        !radioConUnidad
        || !radioSinUnidad
        || !contenidoConUnidad
        || !contenidoSinUnidad
    ) {
        return;
    }


    function actualizarVista() {

        const sinUnidad =
            radioSinUnidad.checked;


        /* =====================================================
           SIN UNIDAD / OFICINA
        ===================================================== */

        if (sinUnidad) {

            contenidoConUnidad.hidden =
                true;


            contenidoSinUnidad.hidden =
                false;


            return;
        }


        /* =====================================================
           CON UNIDAD
        ===================================================== */

        contenidoConUnidad.hidden =
            false;


        contenidoSinUnidad.hidden =
            true;


        /* =====================================================
           MOSTRAR TABLA SOLO SI YA HAY UNIDADES
        ===================================================== */

        const tablaUnidades =
            modal.querySelector(
                '#editar-felicitacion-unidades-agregadas'
            );


        const filas =
            modal.querySelectorAll(
                '#editar-felicitacion-unidades tr[data-parque-vehicular-id]'
            );


        if (tablaUnidades) {

            tablaUnidades.hidden =
                filas.length === 0;
        }
    }


    radioConUnidad.addEventListener(
        'change',
        actualizarVista
    );


    radioSinUnidad.addEventListener(
        'change',
        actualizarVista
    );


    actualizarVista();
}