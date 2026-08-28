document.addEventListener('DOMContentLoaded', () => {
    inicializarEditarReporte();
});


/* =========================================================
   REGISTROS TEMPORALES COMPLETOS
========================================================= */

const reportesTemporales =
    new Map();


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
    let folioActual = '';


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


        folioActual =
            obtenerFolioFila(
                fila
            );


        /*
         * La primera vez creamos un registro
         * completo tomando lo que actualmente
         * existe en la tabla.
         */
        if (
            !reportesTemporales.has(
                folioActual
            )
        ) {

            const reporteInicial =
                crearReporteTemporalDesdeFila(
                    fila
                );


            reportesTemporales.set(
                folioActual,
                reporteInicial
            );

        }


        const reporte =
            reportesTemporales.get(
                folioActual
            );


        cargarReporteEnFormulario(
            modal,
            formulario,
            reporte
        );


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

        const botonSeccion =
            evento.target.closest(
                '[data-editar-seccion]'
            );


        if (botonSeccion) {

            mostrarSeccionEditar(
                modal,
                botonSeccion
                    .dataset
                    .editarSeccion
            );


            return;
        }


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


        filaActual = null;
        folioActual = '';

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


            filaActual = null;
            folioActual = '';

        }

    });


    /* =====================================================
       GUARDAR CAMBIOS
    ===================================================== */

    formulario.addEventListener(
        'submit',
        (evento) => {

            evento.preventDefault();


            if (
                !filaActual
                || !folioActual
            ) {
                return;
            }


            formulario.addEventListener(
    'submit',
    (evento) => {

        evento.preventDefault();


        if (
            !filaActual
            || !folioActual
        ) {
            return;
        }


        const reporteActual =
            reportesTemporales.get(
                folioActual
            )
            || {};


        const reporteEditado =
            obtenerReporteDesdeFormulario(
                formulario,
                reporteActual
            );


        const nuevoFolio =
            construirFolio(
                reporteEditado.prefijo,
                reporteEditado.numero_folio
            );


        reporteEditado.folio =
            nuevoFolio;


        if (
            nuevoFolio
            !== folioActual
        ) {

            reportesTemporales.delete(
                folioActual
            );


            reportesTemporales.set(
                nuevoFolio,
                reporteEditado
            );


            folioActual =
                nuevoFolio;

        } else {

            reportesTemporales.set(
                folioActual,
                reporteEditado
            );

        }


        actualizarFilaDesdeReporte(
            filaActual,
            reporteEditado
        );


        actualizarHeaderEditar(
            modal,
            reporteEditado
        );


        actualizarListadoRelacionado();


        cerrarModalEditar(
            modal
        );


        filaActual = null;
        folioActual = '';

    }
);


            const reporteActual =
                reportesTemporales.get(
                    folioActual
                )
                || {};


            const reporteEditado =
                obtenerReporteDesdeFormulario(
                    formulario,
                    reporteActual
                );


            /*
             * El folio puede cambiar.
             */
            const nuevoFolio =
                construirFolio(
                    reporteEditado.prefijo,
                    reporteEditado.numero_folio
                );


            reporteEditado.folio =
                nuevoFolio;


            /*
             * Si cambió el folio, movemos
             * el registro dentro del Map.
             */
            if (
                nuevoFolio
                !== folioActual
            ) {

                reportesTemporales.delete(
                    folioActual
                );


                reportesTemporales.set(
                    nuevoFolio,
                    reporteEditado
                );


                folioActual =
                    nuevoFolio;

            } else {

                reportesTemporales.set(
                    folioActual,
                    reporteEditado
                );

            }


            /*
             * Actualizamos únicamente las columnas
             * que existen visualmente en la tabla.
             */
            actualizarFilaDesdeReporte(
                filaActual,
                reporteEditado
            );


            /*
             * Actualizamos el encabezado.
             */
            actualizarHeaderEditar(
                modal,
                reporteEditado
            );


            /*
             * Recalcular filtros, tarjetas
             * y paginación.
             */
            actualizarListadoRelacionado();


            cerrarModalEditar(
                modal
            );


            filaActual = null;
            folioActual = '';

        }
    );


    /* =====================================================
       MOSTRAR ARCHIVOS NUEVOS
    ===================================================== */

    const inputEvidencia =
        formulario.querySelector(
            '#editar-evidencia-fotografica'
        );


    if (inputEvidencia) {

        inputEvidencia.addEventListener(
            'change',
            () => {

                mostrarEvidenciaNueva(
                    modal,
                    inputEvidencia.files
                );

            }
        );

    }

}


/* =========================================================
   CREAR REPORTE TEMPORAL DESDE FILA
========================================================= */

function crearReporteTemporalDesdeFila(
    fila
) {

    const celdas =
        fila.querySelectorAll('td');


    const folio =
        celdas[0]
            ?.textContent
            .trim()
        || '';


    return {

        folio:
            folio,

        prefijo:
            obtenerPrefijoFolio(
                folio
            ),

        numero_folio:
            obtenerNumeroFolio(
                folio
            ),

        fecha_registro:
            '',

        folio_ip:
            '',

        fecha_queja:
            convertirFechaInput(
                celdas[1]
                    ?.textContent
                    .trim()
                || ''
            ),

        fecha_acuerdo:
            '',

        expediente:
            celdas[2]
                ?.textContent
                .trim()
            || '',

        nomenclatura:
            '',

        no_oficio:
            '',


        /* HECHOS */

        fecha_hechos:
            '',

        hora_hechos:
            '',

        descripcion:
            '',


        /* UBICACIÓN */

        calle:
            '',

        numero:
            '',

        colonia:
            '',

        entre_calle:
            '',

        y_calle:
            '',

        municipio:
            '',

        estado:
            '',

        sector:
            '',

        cuadrante:
            '',

        latitud:
            '',

        longitud:
            '',


        /* PERSONAL */

        oficial:
            '',

        area:
            celdas[5]
                ?.textContent
                .trim()
            || '',

        turno:
            celdas[6]
                ?.textContent
                .trim()
            || '',


        /* UNIDAD */

        unidad:
            '',

        unidad_marca:
            '',

        unidad_submarca:
            '',

        unidad_color:
            '',

        unidad_estatus:
            '',

        unidad_servicio_adscripcion:
            '',

        unidad_tipo_vehiculo:
            '',

        unidad_origen:
            '',


        /* QUEJOSO */

        quejoso:
            celdas[4]
                ?.textContent
                .trim()
            || '',

        edad:
            '',

        genero:
            '',

        telefono:
            '',

        correo:
            '',


        /* CLASIFICACIÓN */

        clasificacion:
            celdas[3]
                ?.textContent
                .trim()
            || '',

        inspector:
            '',

        investigador:
            '',

        quien_emite_resolucion:
            '',

        resolucion:
            celdas[7]
                ?.textContent
                .trim()
            || '',

        motivos:
            '',


        /* ADICIONAL */

        observaciones:
            '',


        /* EVIDENCIA */

        evidencias:
            [],

    };

}


/* =========================================================
   CARGAR REPORTE EN FORMULARIO
========================================================= */

function cargarReporteEnFormulario(
    modal,
    formulario,
    reporte
) {

    limpiarFormularioEditar(
        modal
    );


    /* =====================================================
       HEADER
    ===================================================== */

    actualizarHeaderEditar(
        modal,
        reporte
    );


    /* =====================================================
       DATOS DEL REPORTE
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-prefijo',
        reporte.prefijo
    );

    asignarValorEditar(
        modal,
        '#editar-numero-folio',
        reporte.numero_folio
    );

    asignarValorEditar(
        modal,
        '#editar-fecha-registro',
        reporte.fecha_registro
    );


    /* =====================================================
       IDENTIFICACIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-folio-ip',
        reporte.folio_ip
    );

    asignarValorEditar(
        modal,
        '#editar-fecha-queja',
        reporte.fecha_queja
    );

    asignarValorEditar(
        modal,
        '#editar-fecha-acuerdo',
        reporte.fecha_acuerdo
    );

    asignarValorEditar(
        modal,
        '#editar-expediente',
        reporte.expediente
    );

    asignarValorEditar(
        modal,
        '#editar-nomenclatura',
        reporte.nomenclatura
    );

    asignarValorEditar(
        modal,
        '#editar-no-oficio',
        reporte.no_oficio
    );


    /* =====================================================
       HECHOS
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-fecha-hechos',
        reporte.fecha_hechos
    );

    asignarValorEditar(
        modal,
        '#editar-hora-hechos',
        reporte.hora_hechos
    );

    asignarValorEditar(
        modal,
        '#editar-descripcion',
        reporte.descripcion
    );


    /* =====================================================
       UBICACIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-calle',
        reporte.calle
    );

    asignarValorEditar(
        modal,
        '#editar-numero',
        reporte.numero
    );

    asignarValorEditar(
        modal,
        '#editar-colonia',
        reporte.colonia
    );

    asignarValorEditar(
        modal,
        '#editar-entre-calle',
        reporte.entre_calle
    );

    asignarValorEditar(
        modal,
        '#editar-y-calle',
        reporte.y_calle
    );

    asignarValorEditar(
        modal,
        '#editar-municipio',
        reporte.municipio
    );

    asignarValorEditar(
        modal,
        '#editar-estado',
        reporte.estado
    );

    asignarValorEditar(
        modal,
        '#editar-sector',
        reporte.sector
    );

    asignarValorEditar(
        modal,
        '#editar-cuadrante',
        reporte.cuadrante
    );

    asignarValorEditar(
        modal,
        '#editar-latitud',
        reporte.latitud
    );

    asignarValorEditar(
        modal,
        '#editar-longitud',
        reporte.longitud
    );


    /* =====================================================
       PERSONAL
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-oficial',
        reporte.oficial
    );

    asignarValorEditar(
        modal,
        '#editar-area',
        reporte.area
    );

    asignarValorEditar(
        modal,
        '#editar-turno',
        reporte.turno
    );


    /* =====================================================
       UNIDAD
    ===================================================== */

    asignarSelectSeguro(
        modal,
        '#editar-unidad',
        reporte.unidad
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-marca',
        reporte.unidad_marca
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-submarca',
        reporte.unidad_submarca
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-color',
        reporte.unidad_color
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-estatus',
        reporte.unidad_estatus
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-servicio-adscripcion',
        reporte.unidad_servicio_adscripcion
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-tipo-vehiculo',
        reporte.unidad_tipo_vehiculo
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-origen',
        reporte.unidad_origen
    );


    /* =====================================================
       QUEJOSO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-quejoso',
        reporte.quejoso
    );

    asignarValorEditar(
        modal,
        '#editar-edad',
        reporte.edad
    );

    asignarSelectSeguro(
        modal,
        '#editar-genero',
        reporte.genero
    );

    asignarValorEditar(
        modal,
        '#editar-telefono',
        reporte.telefono
    );

    asignarValorEditar(
        modal,
        '#editar-correo',
        reporte.correo
    );


    /* =====================================================
       CLASIFICACIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-clasificacion',
        reporte.clasificacion
    );

    asignarValorEditar(
        modal,
        '#editar-inspector',
        reporte.inspector
    );

    asignarValorEditar(
        modal,
        '#editar-investigador',
        reporte.investigador
    );

    asignarValorEditar(
        modal,
        '#editar-quien-emite-resolucion',
        reporte.quien_emite_resolucion
    );

    asignarValorEditar(
        modal,
        '#editar-resolucion',
        reporte.resolucion
    );

    asignarValorEditar(
        modal,
        '#editar-motivos',
        reporte.motivos
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-observaciones',
        reporte.observaciones
    );


    /* =====================================================
       EVIDENCIA
    ===================================================== */

    mostrarEvidenciaExistente(
        modal,
        reporte.evidencias
    );


    mostrarEvidenciaNueva(
        modal,
        []
    );

}


/* =========================================================
   OBTENER REPORTE DESDE FORMULARIO
========================================================= */

function obtenerReporteDesdeFormulario(
    formulario,
    reporteAnterior
) {

    const datos =
        new FormData(
            formulario
        );


    const reporte = {
        ...reporteAnterior,
    };


    const campos = [

        'prefijo',
        'numero_folio',
        'fecha_registro',

        'folio_ip',
        'fecha_queja',
        'fecha_acuerdo',
        'expediente',
        'nomenclatura',
        'no_oficio',

        'fecha_hechos',
        'hora_hechos',
        'descripcion',

        'calle',
        'numero',
        'colonia',
        'entre_calle',
        'y_calle',
        'municipio',
        'estado',
        'sector',
        'cuadrante',
        'latitud',
        'longitud',

        'oficial',
        'area',
        'turno',

        'unidad',
        'unidad_marca',
        'unidad_submarca',
        'unidad_color',
        'unidad_estatus',
        'unidad_servicio_adscripcion',
        'unidad_tipo_vehiculo',
        'unidad_origen',

        'quejoso',
        'edad',
        'genero',
        'telefono',
        'correo',

        'clasificacion',
        'inspector',
        'investigador',
        'quien_emite_resolucion',
        'resolucion',
        'motivos',

        'observaciones',
    ];


    campos.forEach((campo) => {

        if (!datos.has(campo)) {
            return;
        }


        reporte[campo] =
            obtenerDatoFormulario(
                datos,
                campo
            );

    });


    /* =====================================================
       EVIDENCIAS NUEVAS
    ===================================================== */

    const archivos =
        datos.getAll(
            'evidencia_fotografica[]'
        );


    const nuevos =
        archivos.filter(
            (archivo) => {

                return (
                    archivo instanceof File
                    && archivo.size > 0
                );

            }
        );


    if (nuevos.length) {

        const existentes =
            Array.isArray(
                reporte.evidencias
            )
                ? reporte.evidencias
                : [];


        reporte.evidencias = [
            ...existentes,
            ...nuevos.map(
                (archivo) => ({
                    nombre:
                        archivo.name,

                    archivo:
                        archivo.name,

                    temporal:
                        true,
                })
            ),
        ];

    }


    return reporte;

}


/* =========================================================
   ACTUALIZAR FILA
========================================================= */

function actualizarFilaDesdeReporte(
    fila,
    reporte
) {

    const celdas =
        fila.querySelectorAll('td');


    if (celdas.length < 8) {
        return;
    }


    const folio =
        reporte.folio
        || construirFolio(
            reporte.prefijo,
            reporte.numero_folio
        );


    celdas[0].innerHTML =
        `<strong>${escaparHTML(
            folio
        )}</strong>`;


    celdas[1].textContent =
        formatearFechaTabla(
            reporte.fecha_queja
        );


    celdas[2].textContent =
        reporte.expediente
        || '';


    celdas[3].textContent =
        reporte.clasificacion
        || '';


    celdas[4].textContent =
        reporte.quejoso
        || '';


    celdas[5].textContent =
        reporte.area
        || '';


    celdas[6].textContent =
        reporte.turno
        || '';


    actualizarEstadoFilaEditar(
        celdas[7],
        reporte.resolucion
    );


    /*
     * Actualizamos también los botones,
     * por si usan data-folio.
     */
    fila.querySelectorAll(
        '[data-folio]'
    ).forEach((boton) => {

        boton.dataset.folio =
            folio;

    });

}


/* =========================================================
   HEADER
========================================================= */

function actualizarHeaderEditar(
    modal,
    reporte
) {

    const folio =
        reporte.folio
        || construirFolio(
            reporte.prefijo,
            reporte.numero_folio
        );


    asignarTextoEditar(
        modal,
        '#editar-meta-expediente',
        reporte.expediente
    );


    asignarTextoEditar(
        modal,
        '#editar-meta-estado',
        reporte.resolucion
    );


    const titulo =
        modal.querySelector(
            '#modal-editar-titulo'
        );


    if (titulo) {

        titulo.textContent =
            `Editar ${folio}`;

    }

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


    botones.forEach((boton) => {

        boton.classList.toggle(
            'detalle-reporte-nav__item--active',
            boton.dataset.editarSeccion
                === seccion
        );

    });


    paneles.forEach((panel) => {

        panel.classList.toggle(
            'detalle-reporte-seccion--active',
            panel.dataset.editarPanel
                === seccion
        );

    });


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
   ABRIR / CERRAR
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

    modal.querySelectorAll(
        'input, select, textarea'
    ).forEach((campo) => {

        if (
            campo instanceof HTMLInputElement
            && campo.type === 'file'
        ) {

            campo.value = '';

            return;
        }


        campo.value = '';

    });


    /*
     * El prefijo definitivo se mantiene QJ
     * cuando no exista uno temporal.
     */
    const prefijo =
        modal.querySelector(
            '#editar-prefijo'
        );


    if (
        prefijo
        && !prefijo.value
    ) {

        prefijo.value =
            'QJ';

    }

}


/* =========================================================
   EVIDENCIA EXISTENTE
========================================================= */

function mostrarEvidenciaExistente(
    modal,
    evidencias
) {

    const contenedor =
        modal.querySelector(
            '#editar-evidencia-existente'
        );


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML = '';


    if (
        !Array.isArray(evidencias)
        || !evidencias.length
    ) {

        contenedor.innerHTML = `
            <span class="editar-evidencia__vacio">
                Sin evidencia registrada
            </span>
        `;


        return;
    }


    evidencias.forEach((evidencia) => {

        const item =
            document.createElement(
                'div'
            );


        item.className =
            'editar-evidencia__item';


        item.textContent =
            evidencia.nombre
            || evidencia.archivo
            || 'Imagen';


        contenedor.appendChild(
            item
        );

    });

}


/* =========================================================
   EVIDENCIA NUEVA
========================================================= */

function mostrarEvidenciaNueva(
    modal,
    archivos
) {

    const contenedor =
        modal.querySelector(
            '#editar-evidencia-nueva'
        );


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML = '';


    const lista =
        Array.from(
            archivos || []
        );


    if (!lista.length) {

        contenedor.innerHTML = `
            <span class="editar-evidencia__vacio">
                No se han seleccionado archivos nuevos
            </span>
        `;


        return;
    }


    lista.forEach((archivo) => {

        const item =
            document.createElement(
                'div'
            );


        item.className =
            'editar-evidencia__item';


        item.textContent =
            archivo.name;


        contenedor.appendChild(
            item
        );

    });

}


/* =========================================================
   ACTUALIZAR LISTADO
========================================================= */

function actualizarListadoRelacionado() {

    const busqueda =
        document.querySelector(
            '#filtro_busqueda'
        );


    if (!busqueda) {
        return;
    }


    busqueda.dispatchEvent(
        new Event(
            'input',
            {
                bubbles: true,
            }
        )
    );

}


/* =========================================================
   ESTADO TABLA
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
   SELECT SEGURO
========================================================= */

function asignarSelectSeguro(
    modal,
    selector,
    valor
) {

    const select =
        modal.querySelector(
            selector
        );


    if (!select) {
        return;
    }


    if (!valor) {

        select.value = '';

        return;
    }


    const existe =
        Array.from(
            select.options
        ).some(
            (opcion) =>
                opcion.value
                === valor
        );


    /*
     * Para selects dinámicos como Unidad:
     * si todavía no existe la opción,
     * la agregamos temporalmente.
     */
    if (!existe) {

        const opcion =
            document.createElement(
                'option'
            );


        opcion.value =
            valor;


        opcion.textContent =
            valor;


        select.appendChild(
            opcion
        );

    }


    select.value =
        valor;

}


/* =========================================================
   UTILIDADES
========================================================= */

function obtenerFolioFila(
    fila
) {

    return fila
        .querySelector('td')
        ?.textContent
        .trim()
        || '';

}


function obtenerPrefijoFolio(
    folio
) {

    if (!folio) {
        return 'QJ';
    }


    const partes =
        folio.split('-');


    return partes.length > 1
        ? partes[0]
        : 'QJ';

}


function obtenerNumeroFolio(
    folio
) {

    if (!folio) {
        return '';
    }


    const partes =
        folio.split('-');


    if (partes.length <= 1) {
        return folio;
    }


    return partes
        .slice(1)
        .join('-');

}


function construirFolio(
    prefijo,
    numero
) {

    const prefijoLimpio =
        String(
            prefijo || 'QJ'
        ).trim();


    const numeroLimpio =
        String(
            numero || ''
        ).trim();


    if (!numeroLimpio) {
        return prefijoLimpio;
    }


    return `${prefijoLimpio}-${numeroLimpio}`;

}


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
        String(
            valor ?? ''
        ).trim()
        || '—';

}


function obtenerDatoFormulario(
    datos,
    nombre
) {

    const valor =
        datos.get(
            nombre
        );


    return typeof valor === 'string'
        ? valor.trim()
        : '';

}


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