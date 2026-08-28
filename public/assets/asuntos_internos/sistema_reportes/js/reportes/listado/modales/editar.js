document.addEventListener('DOMContentLoaded', () => {
    inicializarEditarReporte();
});


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarEditarReporte() {

    const modal =
        document.querySelector(
            '#modal-editar-reporte'
        );

    const formulario =
        document.querySelector(
            '#form-editar-reporte'
        );


    if (!modal || !formulario) {
        return;
    }


    let filaActual = null;


    /* =====================================================
       ABRIR MODAL
    ===================================================== */

    document.addEventListener('click', (evento) => {

        const boton =
            evento.target.closest(
                '[data-accion="editar"]'
            );


        if (!boton) {
            return;
        }


        const fila =
            boton.closest('tr');


        if (!fila) {
            return;
        }


        filaActual =
            fila;


        cargarDatosFormulario(
            modal,
            fila
        );


        /*
         * Siempre abrimos Editar
         * en la primera pestaña.
         */
        mostrarSeccionEditar(
            modal,
            'datos'
        );


        abrirModalEditar(
            modal
        );

    });


    /* =====================================================
       NAVEGACIÓN Y CIERRE
    ===================================================== */

    modal.addEventListener('click', (evento) => {

        /* =================================================
           CAMBIAR SECCIÓN
        ================================================= */

        const botonSeccion =
            evento.target.closest(
                '[data-editar-seccion]'
            );


        if (botonSeccion) {

            const seccion =
                botonSeccion.dataset.editarSeccion;


            mostrarSeccionEditar(
                modal,
                seccion
            );


            return;
        }


        /* =================================================
           CERRAR MODAL
        ================================================= */

        const botonCerrar =
            evento.target.closest(
                '[data-cerrar-modal-editar]'
            );


        if (!botonCerrar) {
            return;
        }


        cerrarModalEditar(
            modal
        );


        filaActual =
            null;

    });


    /* =====================================================
       CERRAR CON ESCAPE
    ===================================================== */

    document.addEventListener('keydown', (evento) => {

        if (
            evento.key === 'Escape'
            && modal.classList.contains(
                'modal-reporte--visible'
            )
        ) {

            cerrarModalEditar(
                modal
            );


            filaActual =
                null;

        }

    });


    /* =====================================================
       GUARDAR CAMBIOS
    ===================================================== */

    formulario.addEventListener(
        'submit',
        (evento) => {

            evento.preventDefault();


            if (!filaActual) {
                return;
            }


            /*
             * Temporal.
             *
             * Mientras todavía no conectamos
             * la BD, actualizamos únicamente
             * los datos disponibles en la tabla.
             */
            guardarCambiosEnFila(
                filaActual,
                formulario
            );


            cerrarModalEditar(
                modal
            );


            filaActual =
                null;

        }
    );

}


/* =========================================================
   CARGAR DATOS DEL REPORTE
========================================================= */

function cargarDatosFormulario(
    modal,
    fila
) {

    const celdas =
        fila.querySelectorAll('td');


    if (celdas.length < 8) {
        return;
    }


    /* =====================================================
       DATOS TEMPORALES DISPONIBLES EN LA TABLA
    ===================================================== */

    const folio =
        celdas[0].textContent.trim();


    const fechaQueja =
        convertirFechaInput(
            celdas[1].textContent.trim()
        );


    const expediente =
        celdas[2].textContent.trim();


    const clasificacion =
        celdas[3].textContent.trim();


    const quejoso =
        celdas[4].textContent.trim();


    const area =
        celdas[5].textContent.trim();


    const turno =
        celdas[6].textContent.trim();


    const estado =
        celdas[7].textContent.trim();


    /* =====================================================
       HEADER
    ===================================================== */

    asignarTextoEditar(
        modal,
        '#editar-meta-expediente',
        expediente
    );


    asignarTextoEditar(
        modal,
        '#editar-meta-estado',
        estado
    );


    const titulo =
        modal.querySelector(
            '#modal-editar-titulo'
        );


    if (titulo) {

        titulo.textContent =
            `Editar ${folio}`;

    }


    /* =====================================================
       CAMPOS TEMPORALES
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-folio',
        folio
    );


    asignarValorEditar(
        modal,
        '#editar-fecha-queja',
        fechaQueja
    );


    asignarValorEditar(
        modal,
        '#editar-expediente',
        expediente
    );


    asignarValorEditar(
        modal,
        '#editar-clasificacion',
        clasificacion
    );


    asignarValorEditar(
        modal,
        '#editar-quejoso',
        quejoso
    );


    asignarValorEditar(
        modal,
        '#editar-area',
        area
    );


    asignarValorEditar(
        modal,
        '#editar-turno',
        turno
    );


    assignarEstadoTemporal(
        modal,
        estado
    );

}


/* =========================================================
   CAMBIAR SECCIÓN
========================================================= */

function mostrarSeccionEditar(
    modal,
    seccion
) {

    const botones =
        modal.querySelectorAll(
            '[data-editar-seccion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-editar-panel]'
        );


    /* =====================================================
       BOTONES
    ===================================================== */

    botones.forEach((boton) => {

        const esActivo =
            boton.dataset.editarSeccion
            === seccion;


        boton.classList.toggle(
            'detalle-reporte-nav__item--active',
            esActivo
        );

    });


    /* =====================================================
       PANELES
    ===================================================== */

    paneles.forEach((panel) => {

        const esActivo =
            panel.dataset.editarPanel
            === seccion;


        panel.classList.toggle(
            'detalle-reporte-seccion--active',
            esActivo
        );

    });


    /* =====================================================
       REINICIAR SCROLL INTERNO
    ===================================================== */

    const body =
        modal.querySelector(
            '.modal-reporte__body--detalle'
        );


    if (body) {

        body.scrollTo({
            top: 0,
            behavior: 'smooth',
        });

    }

}


/* =========================================================
   ABRIR MODAL
========================================================= */

function abrirModalEditar(
    modal
) {

    modal.classList.add(
        'modal-reporte--visible'
    );


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    document.body.classList.add(
        'modal-abierto'
    );

}


/* =========================================================
   CERRAR MODAL
========================================================= */

function cerrarModalEditar(
    modal
) {

    const elementoActivo =
        document.activeElement;


    if (
        elementoActivo
        && modal.contains(elementoActivo)
    ) {

        elementoActivo.blur();

    }


    modal.classList.remove(
        'modal-reporte--visible'
    );


    modal.setAttribute(
        'aria-hidden',
        'true'
    );


    document.body.classList.remove(
        'modal-abierto'
    );

}


/* =========================================================
   GUARDAR CAMBIOS TEMPORALES EN TABLA
========================================================= */

function guardarCambiosEnFila(
    fila,
    formulario
) {

    const celdas =
        fila.querySelectorAll('td');


    if (celdas.length < 8) {
        return;
    }


    const datos =
        new FormData(
            formulario
        );


    /*
     * Como todavía estamos construyendo
     * las nuevas secciones de Editar,
     * estos campos pueden no existir todavía.
     */
    const folio =
        obtenerDatoFormulario(
            datos,
            'folio'
        );


    const fechaQueja =
        obtenerDatoFormulario(
            datos,
            'fecha_queja'
        );


    const expediente =
        obtenerDatoFormulario(
            datos,
            'expediente'
        );


    const clasificacion =
        obtenerDatoFormulario(
            datos,
            'clasificacion'
        );


    const quejoso =
        obtenerDatoFormulario(
            datos,
            'quejoso'
        );


    const area =
        obtenerDatoFormulario(
            datos,
            'area'
        );


    const turno =
        obtenerDatoFormulario(
            datos,
            'turno'
        );


    const resolucion =
        obtenerDatoFormulario(
            datos,
            'resolucion'
        );


    /*
     * Solo actualizamos una celda
     * si el formulario contiene ese campo.
     */

    if (datos.has('folio')) {

        celdas[0].innerHTML =
            `<strong>${escaparHTML(
                folio
            )}</strong>`;

    }


    if (datos.has('fecha_queja')) {

        celdas[1].textContent =
            formatearFechaTabla(
                fechaQueja
            );

    }


    if (datos.has('expediente')) {

        celdas[2].textContent =
            expediente;

    }


    if (datos.has('clasificacion')) {

        celdas[3].textContent =
            clasificacion;

    }


    if (datos.has('quejoso')) {

        celdas[4].textContent =
            quejoso;

    }


    if (datos.has('area')) {

        celdas[5].textContent =
            area;

    }


    if (datos.has('turno')) {

        celdas[6].textContent =
            turno;

    }


    if (datos.has('resolucion')) {

        actualizarEstadoFilaEditar(
            celdas[7],
            resolucion
        );

    }

}


/* =========================================================
   ACTUALIZAR ESTADO DE TABLA
========================================================= */

function actualizarEstadoFilaEditar(
    celda,
    estado
) {

    celda.innerHTML = '';


    const etiqueta =
        document.createElement(
            'span'
        );


    etiqueta.className =
        `reportes-tabla__estado ${obtenerClaseEstado(
            estado
        )}`;


    etiqueta.textContent =
        estado || 'Pendiente';


    celda.appendChild(
        etiqueta
    );

}


/* =========================================================
   ASIGNAR ESTADO TEMPORAL
========================================================= */

function assignarEstadoTemporal(
    modal,
    estado
) {

    const campo =
        modal.querySelector(
            '#editar-resolucion'
        );


    if (!campo) {
        return;
    }


    campo.value =
        estado || '';

}


/* =========================================================
   CONVERTIR FECHA PARA INPUT
========================================================= */

function convertirFechaInput(
    fecha
) {

    if (!fecha) {
        return '';
    }


    const partes =
        fecha.split('/');


    if (partes.length !== 3) {
        return '';
    }


    const [
        dia,
        mes,
        anio
    ] = partes;


    return `${anio}-${mes}-${dia}`;

}


/* =========================================================
   FORMATEAR FECHA PARA TABLA
========================================================= */

function formatearFechaTabla(
    fecha
) {

    if (!fecha) {
        return '';
    }


    const partes =
        fecha.split('-');


    if (partes.length !== 3) {
        return fecha;
    }


    const [
        anio,
        mes,
        dia
    ] = partes;


    return `${dia}/${mes}/${anio}`;

}


/* =========================================================
   CLASE VISUAL DEL ESTADO
========================================================= */

function obtenerClaseEstado(
    estado
) {

    switch (estado) {

        case 'Finalizado':

            return 'estado--finalizado';


        case 'En proceso':

            return 'estado--proceso';


        default:

            return 'estado--pendiente';

    }

}


/* =========================================================
   ASIGNAR VALOR A CAMPO
========================================================= */

function asignarValorEditar(
    modal,
    selector,
    valor
) {

    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    elemento.value =
        valor || '';

}


/* =========================================================
   ASIGNAR TEXTO
========================================================= */

function asignarTextoEditar(
    modal,
    selector,
    valor
) {

    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    elemento.textContent =
        valor?.trim()
        || '—';

}


/* =========================================================
   OBTENER DATO DE FORMDATA
========================================================= */

function obtenerDatoFormulario(
    datos,
    nombre
) {

    const valor =
        datos.get(
            nombre
        );


    if (
        typeof valor
        !== 'string'
    ) {

        return '';

    }


    return valor.trim();

}


/* =========================================================
   ESCAPAR HTML
========================================================= */

function escaparHTML(
    valor
) {

    const elemento =
        document.createElement(
            'div'
        );


    elemento.textContent =
        valor ?? '';


    return elemento.innerHTML;

}