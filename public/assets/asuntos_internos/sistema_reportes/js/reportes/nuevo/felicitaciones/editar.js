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

    inicializarBuscadorUnidadesEditar(
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


    const inputBuscarUnidad =
        modal.querySelector(
            '#editar-felicitacion-buscar-unidad'
        );


    const resultadosUnidad =
        modal.querySelector(
            '#editar-felicitacion-unidad-resultados'
        );


    const tbodyUnidades =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    if (
        !radioConUnidad
        || !radioSinUnidad
        || !contenidoConUnidad
        || !contenidoSinUnidad
    ) {
        return;
    }


    /* =====================================================
       MOSTRAR / OCULTAR CONTENIDO
    ===================================================== */

    function actualizarVista() {

        const conUnidad =
            radioConUnidad.checked;


        const sinUnidad =
            radioSinUnidad.checked;


        contenidoConUnidad.hidden =
            !conUnidad;


        contenidoSinUnidad.hidden =
            !sinUnidad;


        /* =================================================
           SI CAMBIA A SIN UNIDAD
           LIMPIAR BUSCADOR Y RESULTADOS
        ================================================= */

        if (sinUnidad) {

            if (inputBuscarUnidad) {

                inputBuscarUnidad.value =
                    '';
            }


            if (resultadosUnidad) {

                resultadosUnidad.innerHTML =
                    '';

                resultadosUnidad.hidden =
                    true;
            }
        }
    }


    /* =====================================================
       CAMBIO A CON UNIDAD
    ===================================================== */

    radioConUnidad.addEventListener(
        'change',
        () => {

            if (
                !radioConUnidad.checked
            ) {
                return;
            }


            actualizarVista();


            /* =================================================
               SI NO HAY UNIDADES MOSTRAR ESTADO VACÍO
            ================================================= */

            if (
                tbodyUnidades
                && tbodyUnidades.querySelectorAll(
                    'tr[data-unidad-id]'
                ).length === 0
            ) {

                tbodyUnidades.innerHTML = `
                    <tr>
                        <td colspan="7">
                            Sin unidades relacionadas
                        </td>
                    </tr>
                `;
            }
        }
    );


    /* =====================================================
       CAMBIO A SIN UNIDAD / OFICINA
    ===================================================== */

    radioSinUnidad.addEventListener(
        'change',
        () => {

            if (
                !radioSinUnidad.checked
            ) {
                return;
            }


            actualizarVista();
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarVista();
}


/* =========================================================
   INICIALIZAR BUSCADOR DE UNIDADES - EDITAR FELICITACIÓN
========================================================= */

function inicializarBuscadorUnidadesEditar(
    modal
) {

    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const inputBusqueda =
        modal.querySelector(
            '#editar-felicitacion-buscar-unidad'
        );


    const contenedorResultados =
        modal.querySelector(
            '#editar-felicitacion-unidades-resultados'
        );


    /* =====================================================
       UNIDAD SELECCIONADA
    ===================================================== */

    const contenedorSeleccionada =
        modal.querySelector(
            '#editar-felicitacion-unidad-seleccionada'
        );


    const inputParqueId =
        modal.querySelector(
            '#editar-felicitacion-unidad-parque-id'
        );


    const inputNoEconomico =
        modal.querySelector(
            '#editar-felicitacion-unidad-no-economico'
        );


    const inputPlacas =
        modal.querySelector(
            '#editar-felicitacion-unidad-placas'
        );


    const inputMarca =
        modal.querySelector(
            '#editar-felicitacion-unidad-marca'
        );


    const inputSubmarca =
        modal.querySelector(
            '#editar-felicitacion-unidad-submarca'
        );


    const inputColor =
        modal.querySelector(
            '#editar-felicitacion-unidad-color'
        );


    const inputEstatus =
        modal.querySelector(
            '#editar-felicitacion-unidad-estatus'
        );


    const inputServicio =
        modal.querySelector(
            '#editar-felicitacion-unidad-servicio'
        );


    const inputTipo =
        modal.querySelector(
            '#editar-felicitacion-unidad-tipo'
        );


    const btnAgregar =
        modal.querySelector(
            '#btn-editar-agregar-unidad-felicitacion'
        );


    /* =====================================================
       TABLA
    ===================================================== */

    const contenedorAgregadas =
        modal.querySelector(
            '#editar-felicitacion-unidades-agregadas'
        );


    const tbody =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    const contenedorInputs =
        modal.querySelector(
            '#editar-felicitacion-unidades-inputs'
        );


    if (
        !radioConUnidad
        || !inputBusqueda
        || !contenedorResultados
        || !contenedorSeleccionada
        || !inputParqueId
        || !inputNoEconomico
        || !inputPlacas
        || !inputMarca
        || !inputSubmarca
        || !inputColor
        || !inputEstatus
        || !inputServicio
        || !inputTipo
        || !btnAgregar
        || !contenedorAgregadas
        || !tbody
        || !contenedorInputs
    ) {

        console.error(
            'No se encontraron todos los elementos de unidades para editar felicitación.'
        );

        return;
    }


    /* =====================================================
       ESTADO
    ===================================================== */

    let temporizadorBusqueda =
        null;


    let controladorBusqueda =
        null;


    let unidadSeleccionada =
        null;


    /* =====================================================
       BUSCAR AL ESCRIBIR
    ===================================================== */

    inputBusqueda.addEventListener(
        'input',
        () => {

            if (!radioConUnidad.checked) {
                return;
            }


            const termino =
                inputBusqueda.value.trim();


            if (temporizadorBusqueda) {

                clearTimeout(
                    temporizadorBusqueda
                );
            }


            limpiarUnidadSeleccionada();


            if (
                termino.length < 1
            ) {

                ocultarResultados();

                return;
            }


            temporizadorBusqueda =
                window.setTimeout(
                    () => {

                        buscarUnidades(
                            termino
                        );

                    },
                    300
                );
        }
    );


    /* =====================================================
       CONSULTAR BACKEND
    ===================================================== */

    async function buscarUnidades(
        termino
    ) {

        if (!radioConUnidad.checked) {
            return;
        }


        if (controladorBusqueda) {

            controladorBusqueda.abort();
        }


        controladorBusqueda =
            new AbortController();


        try {

            const url =
                new URL(
                    'DataCore/public/asuntos-internos/reportes/unidades/buscar',
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
                        method:
                            'GET',

                        headers: {
                            Accept:
                                'application/json',
                        },

                        credentials:
                            'same-origin',

                        signal:
                            controladorBusqueda.signal,
                    }
                );


            const resultado =
                await respuesta.json();


            if (!respuesta.ok) {

                throw new Error(
                    resultado?.message
                    || 'No fue posible consultar las unidades.'
                );
            }


            const unidades =
                Array.isArray(
                    resultado.unidades
                )
                    ? resultado.unidades
                    : [];


            renderizarResultados(
                unidades
            );


        } catch (error) {

            if (
                error.name === 'AbortError'
            ) {
                return;
            }


            console.error(
                'Error buscando unidades:',
                error
            );


            mostrarMensajeResultados(
                'No fue posible consultar las unidades.'
            );
        }
    }


    /* =====================================================
       MOSTRAR RESULTADOS
    ===================================================== */

    function renderizarResultados(
        unidades
    ) {

        contenedorResultados.innerHTML =
            '';


        if (
            unidades.length === 0
        ) {

            mostrarMensajeResultados(
                'No se encontraron unidades.'
            );

            return;
        }


        unidades.forEach(
            (unidad) => {

                const boton =
                    document.createElement(
                        'button'
                    );


                boton.type =
                    'button';


                boton.className =
                    'modal-felicitacion-unidades-editar__resultado';


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


                boton.innerHTML = `

                    <strong>
                        ${escaparHtml(
                            numeroEconomico
                            || 'SIN NÚMERO'
                        )}
                    </strong>

                    <span>
                        ${escaparHtml(
                            placas
                            || 'SIN PLACAS'
                        )}
                    </span>

                    <small>
                        ${escaparHtml(
                            [marca, submarca]
                                .filter(Boolean)
                                .join(' ')
                                || 'Sin información'
                        )}
                    </small>
                `;


                boton.addEventListener(
                    'click',
                    () => {

                        seleccionarUnidad(
                            unidad
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
       SELECCIONAR UNIDAD
    ===================================================== */

    function seleccionarUnidad(
        unidad
    ) {

        const parqueVehicularId =
            Number(
                unidad.id
                || unidad.parque_vehicular_id
                || 0
            );


        if (
            parqueVehicularId <= 0
        ) {
            return;
        }


        unidadSeleccionada = {
            id:
                parqueVehicularId,

            no_economico:
                String(
                    unidad.no_economico
                    || ''
                ).trim(),

            placas:
                String(
                    unidad.placas
                    || ''
                ).trim(),

            marca:
                String(
                    unidad.marca
                    || ''
                ).trim(),

            submarca:
                String(
                    unidad.submarca
                    || ''
                ).trim(),

            color:
                String(
                    unidad.color
                    || ''
                ).trim(),

            estatus:
                String(
                    unidad.estatus
                    || ''
                ).trim(),

            servicio:
                String(
                    unidad.servicio
                    || ''
                ).trim(),

            tipo:
                String(
                    unidad.tipo
                    || ''
                ).trim(),
        };


        /* =================================================
           CARGAR CAMPOS
        ================================================= */

        inputParqueId.value =
            String(
                unidadSeleccionada.id
            );


        inputNoEconomico.value =
            unidadSeleccionada.no_economico;


        inputPlacas.value =
            unidadSeleccionada.placas;


        inputMarca.value =
            unidadSeleccionada.marca;


        inputSubmarca.value =
            unidadSeleccionada.submarca;


        inputColor.value =
            unidadSeleccionada.color;


        inputEstatus.value =
            unidadSeleccionada.estatus;


        inputServicio.value =
            unidadSeleccionada.servicio;


        inputTipo.value =
            unidadSeleccionada.tipo;


        /* =================================================
           MOSTRAR CONTENEDOR
        ================================================= */

        contenedorSeleccionada.hidden =
            false;


        /* =================================================
           MOSTRAR UNIDAD EN BUSCADOR
        ================================================= */

        inputBusqueda.value =
            unidadSeleccionada.no_economico;


        ocultarResultados();
    }


    /* =====================================================
       AGREGAR UNIDAD
    ===================================================== */

    btnAgregar.addEventListener(
        'click',
        () => {

            if (
                !unidadSeleccionada
                || unidadSeleccionada.id <= 0
            ) {
                return;
            }


            const parqueVehicularId =
                unidadSeleccionada.id;


            /* =================================================
               EVITAR DUPLICADO
            ================================================= */

            const existente =
                tbody.querySelector(
                    `tr[data-parque-vehicular-id="${parqueVehicularId}"]`
                );


            if (existente) {

                alert(
                    'Esta unidad ya está relacionada con la felicitación.'
                );

                return;
            }


            /* =================================================
               QUITAR FILA VACÍA
            ================================================= */

            tbody
                .querySelectorAll(
                    'tr'
                )
                .forEach(
                    (fila) => {

                        if (
                            !fila.dataset.parqueVehicularId
                        ) {

                            fila.remove();
                        }
                    }
                );


            /* =================================================
               CREAR FILA
            ================================================= */

            const fila =
                document.createElement(
                    'tr'
                );


            fila.dataset.parqueVehicularId =
                String(
                    parqueVehicularId
                );


            fila.dataset.unidadNueva =
                '1';


            const marcaSubmarca =
                [
                    unidadSeleccionada.marca,
                    unidadSeleccionada.submarca
                ]
                    .filter(Boolean)
                    .join(' ');


            fila.innerHTML = `

                <td>

                    <strong class="modal-felicitacion-unidades-editar__unidad-nombre">

                        ${escaparHtml(
                            unidadSeleccionada.no_economico
                            || '—'
                        )}

                    </strong>

                    <span class="modal-felicitacion-unidades-editar__unidad-detalle">

                        Placas:
                        ${escaparHtml(
                            unidadSeleccionada.placas
                            || 'SIN PLACAS'
                        )}

                    </span>

                </td>


                <td>
                    ${escaparHtml(
                        marcaSubmarca
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        unidadSeleccionada.color
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        unidadSeleccionada.estatus
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        unidadSeleccionada.servicio
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        unidadSeleccionada.tipo
                        || '—'
                    )}
                </td>


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


            /* =================================================
               MOSTRAR TABLA
            ================================================= */

            contenedorAgregadas.hidden =
                false;


            /* =================================================
               ACTUALIZAR INPUTS
            ================================================= */

            actualizarInputsUnidades();


            /* =================================================
               LIMPIAR SELECTOR
            ================================================= */

            limpiarSelectorCompleto();
        }
    );


    /* =====================================================
       QUITAR UNIDAD
    ===================================================== */

    tbody.addEventListener(
        'click',
        (evento) => {

            const boton =
                evento.target.closest(
                    '[data-quitar-unidad-felicitacion]'
                );


            if (!boton) {
                return;
            }


            const fila =
                boton.closest(
                    'tr[data-parque-vehicular-id]'
                );


            if (!fila) {
                return;
            }


            fila.remove();


            actualizarInputsUnidades();


            const filasRestantes =
                tbody.querySelectorAll(
                    'tr[data-parque-vehicular-id]'
                );


            if (
                filasRestantes.length === 0
            ) {

                contenedorAgregadas.hidden =
                    true;
            }
        }
    );


    /* =====================================================
       LIMPIAR UNIDAD SELECCIONADA
    ===================================================== */

    function limpiarUnidadSeleccionada() {

        unidadSeleccionada =
            null;


        inputParqueId.value =
            '';


        inputNoEconomico.value =
            '';


        inputPlacas.value =
            '';


        inputMarca.value =
            '';


        inputSubmarca.value =
            '';


        inputColor.value =
            '';


        inputEstatus.value =
            '';


        inputServicio.value =
            '';


        inputTipo.value =
            '';


        contenedorSeleccionada.hidden =
            true;
    }


    /* =====================================================
       LIMPIAR SELECTOR COMPLETO
    ===================================================== */

    function limpiarSelectorCompleto() {

        inputBusqueda.value =
            '';


        ocultarResultados();


        limpiarUnidadSeleccionada();
    }


    /* =====================================================
       INPUTS OCULTOS
    ===================================================== */

    function actualizarInputsUnidades() {

        contenedorInputs.innerHTML =
            '';


        const filas =
            tbody.querySelectorAll(
                'tr[data-parque-vehicular-id]'
            );


        filas.forEach(
            (fila, indice) => {

                const parqueVehicularId =
                    Number(
                        fila.dataset.parqueVehicularId
                        || 0
                    );


                if (
                    parqueVehicularId <= 0
                ) {
                    return;
                }


                const input =
                    document.createElement(
                        'input'
                    );


                input.type =
                    'hidden';


                input.name =
                    `unidades[${indice}][parque_vehicular_id]`;


                input.value =
                    String(
                        parqueVehicularId
                    );


                contenedorInputs.appendChild(
                    input
                );
            }
        );
    }


    /* =====================================================
       RESULTADOS
    ===================================================== */

    function mostrarMensajeResultados(
        mensaje
    ) {

        contenedorResultados.innerHTML = `

            <div class="modal-felicitacion-unidades-editar__resultado-vacio">

                ${escaparHtml(
                    mensaje
                )}

            </div>
        `;


        contenedorResultados.hidden =
            false;
    }


    function ocultarResultados() {

        contenedorResultados.hidden =
            true;


        contenedorResultados.innerHTML =
            '';
    }


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

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