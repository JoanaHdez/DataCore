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
             * Por ahora seguimos trabajando
             * de manera temporal con la tabla.
             *
             * Cuando conectemos la BD,
             * aquí se hará el fetch al backend.
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
       LIMPIAR FORMULARIO
    ===================================================== */

    limpiarFormularioEditar(
        modal
    );


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
       PASO 1
       DATOS DEL REPORTE
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-prefijo',
        obtenerPrefijoFolio(
            folio
        )
    );


    asignarValorEditar(
        modal,
        '#editar-numero-folio',
        obtenerNumeroFolio(
            folio
        )
    );


    /*
     * Fecha de registro todavía no está
     * disponible en la tabla temporal.
     */
    asignarValorEditar(
        modal,
        '#editar-fecha-registro',
        ''
    );


    /* =====================================================
       IDENTIFICACIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-folio-ip',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-fecha-queja',
        fechaQueja
    );


    asignarValorEditar(
        modal,
        '#editar-fecha-acuerdo',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-expediente',
        expediente
    );


    asignarValorEditar(
        modal,
        '#editar-nomenclatura',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-no-oficio',
        ''
    );


    /* =====================================================
       PASO 2
       DATOS DE LOS HECHOS
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-fecha-hechos',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-hora-hechos',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-descripcion',
        ''
    );


    /* =====================================================
       UBICACIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-calle',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-numero',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-colonia',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-entre-calle',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-y-calle',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-municipio',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-estado',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-sector',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-cuadrante',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-latitud',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-longitud',
        ''
    );


    /* =====================================================
       PASO 3
       PERSONAL
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-oficial',
        ''
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


    /* =====================================================
       UNIDAD
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-unidad',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-marca',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-submarca',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-color',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-estatus',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-servicio-adscripcion',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-tipo-vehiculo',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-origen',
        ''
    );


    /* =====================================================
       PASO 4
       QUEJOSO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-quejoso',
        quejoso
    );


    asignarValorEditar(
        modal,
        '#editar-edad',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-genero',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-telefono',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-correo',
        ''
    );


    /* =====================================================
       PASO 5
       CLASIFICACIÓN Y SEGUIMIENTO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-clasificacion',
        clasificacion
    );


    asignarValorEditar(
        modal,
        '#editar-inspector',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-investigador',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-quien-emite-resolucion',
        ''
    );


    /*
     * TEMPORAL:
     * la tabla actual utiliza la última columna
     * como estado/resolución.
     */
    asignarValorEditar(
        modal,
        '#editar-resolucion',
        estado
    );


    asignarValorEditar(
        modal,
        '#editar-motivos',
        ''
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-observaciones',
        ''
    );


    /* =====================================================
       EVIDENCIA
    ===================================================== */

    limpiarEvidenciaEditar(
        modal
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
            '.modal-reporte__body--editar'
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
   LIMPIAR FORMULARIO
========================================================= */

function limpiarFormularioEditar(
    modal
) {

    const campos =
        modal.querySelectorAll(
            'input, select, textarea'
        );


    campos.forEach((campo) => {

        /*
         * No intentamos establecer value
         * en inputs de tipo file.
         */
        if (
            campo instanceof HTMLInputElement
            && campo.type === 'file'
        ) {

            campo.value = '';

            return;
        }


        /*
         * El prefijo QJ es fijo.
         */
        if (
            campo.id === 'editar-prefijo'
        ) {

            campo.value = 'QJ';

            return;
        }


        campo.value = '';

    });


    limpiarEvidenciaEditar(
        modal
    );

}


/* =========================================================
   LIMPIAR EVIDENCIA
========================================================= */

function limpiarEvidenciaEditar(
    modal
) {

    const input =
        modal.querySelector(
            '#editar-evidencia-fotografica'
        );


    if (input) {

        input.value = '';

    }


    const existente =
        modal.querySelector(
            '#editar-evidencia-existente'
        );


    if (existente) {

        existente.innerHTML = `
            <span class="editar-evidencia__vacio">
                Sin evidencia registrada
            </span>
        `;

    }


    const nueva =
        modal.querySelector(
            '#editar-evidencia-nueva'
        );


    if (nueva) {

        nueva.innerHTML = `
            <span class="editar-evidencia__vacio">
                No se han seleccionado archivos nuevos
            </span>
        `;

    }

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


    /* =====================================================
       DATOS QUE ACTUALMENTE EXISTEN EN LA TABLA
    ===================================================== */

    const numeroFolio =
        obtenerDatoFormulario(
            datos,
            'numero_folio'
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


    /* =====================================================
       FOLIO
    ===================================================== */

    if (
        datos.has('numero_folio')
        && numeroFolio
    ) {

        const folioTemporal =
            `QJ-${numeroFolio}`;


        celdas[0].innerHTML =
            `<strong>${escaparHTML(
                folioTemporal
            )}</strong>`;

    }


    /* =====================================================
       FECHA DE QUEJA
    ===================================================== */

    if (datos.has('fecha_queja')) {

        celdas[1].textContent =
            formatearFechaTabla(
                fechaQueja
            );

    }


    /* =====================================================
       EXPEDIENTE
    ===================================================== */

    if (datos.has('expediente')) {

        celdas[2].textContent =
            expediente;

    }


    /* =====================================================
       CLASIFICACIÓN
    ===================================================== */

    if (datos.has('clasificacion')) {

        celdas[3].textContent =
            clasificacion;

    }


    /* =====================================================
       QUEJOSO
    ===================================================== */

    if (datos.has('quejoso')) {

        celdas[4].textContent =
            quejoso;

    }


    /* =====================================================
       ÁREA
    ===================================================== */

    if (datos.has('area')) {

        celdas[5].textContent =
            area;

    }


    /* =====================================================
       TURNO
    ===================================================== */

    if (datos.has('turno')) {

        celdas[6].textContent =
            turno;

    }


    /* =====================================================
       ESTADO / RESOLUCIÓN TEMPORAL
    ===================================================== */

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
   OBTENER PREFIJO DEL FOLIO
========================================================= */

function obtenerPrefijoFolio(
    folio
) {

    if (!folio) {
        return 'QJ';
    }


    const partes =
        folio.split('-');


    if (partes.length > 1) {

        /*
         * Temporal:
         * los registros actuales todavía
         * utilizan folios ficticios AI-...
         */
        return partes[0];

    }


    return 'QJ';

}


/* =========================================================
   OBTENER NÚMERO DEL FOLIO
========================================================= */

function obtenerNumeroFolio(
    folio
) {

    if (!folio) {
        return '';
    }


    const partes =
        folio.split('-');


    if (partes.length > 1) {

        return partes
            .slice(1)
            .join('-');

    }


    return folio;

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
   ASIGNAR VALOR
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
        valor ?? '';

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


    const texto =
        String(
            valor ?? ''
        ).trim();


    elemento.textContent =
        texto || '—';

}


/* =========================================================
   OBTENER DATO DE FORMULARIO
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