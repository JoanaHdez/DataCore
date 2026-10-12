/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   MOTIVOS Y SANCIONES
========================================================= */


/* =========================================================
   ESTADO
========================================================= */

const motivosSeleccionados =
    new Map();


const motivosRespaldo =
    new Map();

/* =========================================================
   MOTIVOS ORIGINALES GUARDADOS EN BASE DE DATOS
========================================================= */

const motivosOriginalesEditar = new Map();

function normalizarFolioSancion(
    valor
) {

    return String(
        valor
        || ''
    )
        .trim()
        .toUpperCase();
}

/* =========================================================
   CARGAR MOTIVOS EXISTENTES - EDITAR
========================================================= */

export function cargarMotivosEditar(
    modal,
    motivos = []
) {

    if (!modal) {
        return;
    }

    /* =====================================================
       LIMPIAR INFORMACIÓN DEL REPORTE ANTERIOR
    ===================================================== */

    motivosSeleccionados.clear();
    motivosRespaldo.clear();
    motivosOriginalesEditar.clear();

    /* =====================================================
       CARGAR MOTIVOS DESDE BASE DE DATOS
    ===================================================== */

    if (Array.isArray(motivos)) {

        motivos.forEach((motivo) => {

            const idMotivo = String(
                motivo.id_motivo ?? ''
            ).trim();

            if (!idMotivo) {
                return;
            }

            const datosMotivo = {

                id_motivo: idMotivo,

                motivo: String(
                    motivo.motivo ?? ''
                ).trim(),

                motivo_personalizado: String(
                    motivo.motivo_personalizado ?? ''
                ).trim(),

                sancion: String(
                    motivo.sancion ??
                    motivo.tipo_sancion ??
                    ''
                ).trim(),

                folio_sancion: normalizarFolioSancion(
                    motivo.folio_sancion
                )

            };

            /* Motivo visible durante la edición */

            motivosSeleccionados.set(
                idMotivo,
                { ...datosMotivo }
            );

            /* Copia original, independiente */

            motivosOriginalesEditar.set(
                idMotivo,
                { ...datosMotivo }
            );

        });

    }

    /* =====================================================
       OPCIONES DE SANCIÓN
    ===================================================== */

    const conSancion =
        modal.querySelector('#editar-con-sancion');

    const sinSanciones =
        modal.querySelector('#editar-sin-sanciones');

    const bajaVoluntaria =
        modal.querySelector('#editar-baja-voluntaria');

    const desistir =
        modal.querySelector('#editar-desistir');

    const tieneBaja =
        bajaVoluntaria?.checked === true;

    const tieneDesistimiento =
        desistir?.checked === true;

    const noTieneSanciones =
        sinSanciones?.checked === true;

    const tieneMotivos =
        motivosOriginalesEditar.size > 0;

    if (conSancion) {

        conSancion.checked =
            tieneMotivos &&
            !tieneBaja &&
            !tieneDesistimiento &&
            !noTieneSanciones;

    }

    /* =====================================================
       LIMPIAR ESTADOS TEMPORALES
    ===================================================== */

    if (bajaVoluntaria) {
        delete bajaVoluntaria.dataset.estadoAnterior;
    }

    if (desistir) {
        delete desistir.dataset.estadoAnterior;
    }

    /* =====================================================
       REINICIAR BUSCADOR
    ===================================================== */

    const buscador =
        modal.querySelector('#editar-buscar-motivo');

    const resultados =
        modal.querySelector('#editar-motivos-resultados');

    if (buscador) {

        buscador.value = '';

        buscador.disabled =
            conSancion?.checked !== true;

        buscador.classList.toggle(
            'report-input--disabled',
            buscador.disabled
        );

    }

    if (resultados) {

        resultados.hidden = true;

        resultados.querySelectorAll(
            '[data-editar-motivo-opcion]'
        ).forEach((opcion) => {

            opcion.hidden = true;
            opcion.style.display = 'none';

        });

        const mensaje = resultados.querySelector(
            '.motivos-resultados__vacio'
        );

        if (mensaje) {
            mensaje.hidden = true;
            mensaje.style.display = 'none';
        }

    }

    /* =====================================================
       MOSTRAR MOTIVOS ÚNICAMENTE SI CORRESPONDE
    ===================================================== */

    if (conSancion?.checked !== true) {
        motivosSeleccionados.clear();
    }

    renderizarMotivosEditar(modal);

    /* =====================================================
       ACTUALIZAR ESTADO DEL REPORTE
    ===================================================== */

    actualizarBajaVoluntariaEditar(modal);
    actualizarDesistirEditar(modal);
}
/* =========================================================
   INICIALIZAR MOTIVOS - EDITAR
========================================================= */

export function inicializarMotivosEditar(modal) {

    if (!modal) {
        return;
    }

    if (modal.dataset.editarMotivosInicializados === '1') {
        return;
    }

    const buscador =
        modal.querySelector('#editar-buscar-motivo');

    const resultados =
        modal.querySelector('#editar-motivos-resultados');

    const tbody =
        modal.querySelector('#editar-motivos-agregados-body');

    const contenedorInputs =
        modal.querySelector('#editar-motivos-inputs');

    const opciones = {
        conSancion: modal.querySelector('#editar-con-sancion'),
        sinSanciones: modal.querySelector('#editar-sin-sanciones'),
        bajaVoluntaria: modal.querySelector('#editar-baja-voluntaria'),
        desistir: modal.querySelector('#editar-desistir')
    };

    if (
        !buscador ||
        !resultados ||
        !tbody ||
        !contenedorInputs ||
        !opciones.conSancion
    ) {
        return;
    }

    modal.dataset.editarMotivosInicializados = '1';

    /* =====================================================
       MOSTRAR CATÁLOGO AL ENFOCAR
    ===================================================== */

    buscador.addEventListener('focus', () => {

        if (!buscador.disabled) {
            filtrarMotivosEditar(modal);
        }

    });

    /* =====================================================
       BUSCAR MOTIVOS
    ===================================================== */

    buscador.addEventListener('input', () => {

        filtrarMotivosEditar(modal);

    });

    /* =====================================================
       SELECCIONAR MOTIVO
    ===================================================== */

    resultados.addEventListener('click', (evento) => {

        const opcion = evento.target.closest(
            '[data-editar-motivo-opcion]'
        );

        if (!opcion || buscador.disabled) {
            return;
        }

        agregarMotivoEditar(modal, opcion);

    });

    /* =====================================================
       QUITAR MOTIVO
    ===================================================== */

    tbody.addEventListener('click', (evento) => {

        const boton = evento.target.closest(
            '[data-editar-quitar-motivo]'
        );

        if (!boton) {
            return;
        }

        const idMotivo = String(
            boton.dataset.motivoId || ''
        ).trim();

        if (!idMotivo) {
            return;
        }

        motivosSeleccionados.delete(idMotivo);

        // Quitar manualmente descarta también el respaldo.
        motivosRespaldo.clear();

        renderizarMotivosEditar(modal);

        if (!buscador.disabled) {
            filtrarMotivosEditar(modal);
        }

    });

    /* =====================================================
       FOLIO DE SANCIÓN
    ===================================================== */

    tbody.addEventListener('input', (evento) => {

        const input = evento.target.closest(
            '[data-editar-folio-sancion]'
        );

        if (!input) {
            return;
        }

        const idMotivo = String(
            input.dataset.motivoId || ''
        ).trim();

        const motivo =
            motivosSeleccionados.get(idMotivo);

        if (!motivo) {
            return;
        }

        motivo.folio_sancion =
            normalizarFolioSancion(input.value);

        input.value = motivo.folio_sancion;

        motivosSeleccionados.set(idMotivo, motivo);

        actualizarInputsMotivosEditar(modal);

    });

    /* =====================================================
       MOTIVO PERSONALIZADO - ID 55
    ===================================================== */

    tbody.addEventListener('input', (evento) => {

        const input = evento.target.closest(
            '[data-editar-motivo-personalizado]'
        );

        if (!input) {
            return;
        }

        const idMotivo = String(
            input.dataset.motivoId || ''
        ).trim();

        const motivo =
            motivosSeleccionados.get(idMotivo);

        if (!motivo) {
            return;
        }

        motivo.motivo_personalizado =
            String(input.value || '');

        motivosSeleccionados.set(idMotivo, motivo);

        actualizarInputsMotivosEditar(modal);

    });

    /* =====================================================
       CAMBIOS EN SITUACIÓN DE LA SANCIÓN
    ===================================================== */

    Object.entries(opciones).forEach(([clave, checkbox]) => {

        if (!checkbox) {
            return;
        }

        checkbox.addEventListener('change', () => {

            actualizarSituacionSancionEditar(
                modal,
                clave
            );

        });

    });

    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarBajaVoluntariaEditar(modal);
    actualizarDesistirEditar(modal);
}


/* =========================================================
   ACTUALIZAR BAJA VOLUNTARIA
========================================================= */

export function actualizarBajaVoluntariaEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const bajaVoluntaria =
        modal.querySelector(
            '#editar-baja-voluntaria'
        );


    const desistir =
        modal.querySelector(
            '#editar-desistir'
        );


    const estado =
        modal.querySelector(
            '#editar-estado-actual'
        );


    const textoEstado =
        modal.querySelector(
            '#editar-estado-select-texto'
        );


    const botonEstado =
        modal.querySelector(
            '#editar-estado-select'
        );


    if (
        !bajaVoluntaria
        || !estado
    ) {
        return;
    }


    /* =====================================================
       BAJA VOLUNTARIA ACTIVADA
    ===================================================== */

    if (
        bajaVoluntaria.checked
    ) {

        if (
            !bajaVoluntaria
                .dataset
                .estadoAnterior
        ) {

            const estadoAnterior =
                String(
                    estado.value
                    || 'Pendiente'
                ).trim();


            bajaVoluntaria
                .dataset
                .estadoAnterior =
                estadoAnterior !== 'Finalizado'
                    ? estadoAnterior
                    : 'Pendiente';
        }


        estado.value =
            'Finalizado';


        if (textoEstado) {

            textoEstado.textContent =
                'Finalizado';
        }


        if (botonEstado) {

            botonEstado.disabled =
                true;


            botonEstado.setAttribute(
                'aria-expanded',
                'false'
            );


            botonEstado.classList.remove(
                'estado-select--activo'
            );
        }


        const resultados =
            modal.querySelector(
                '#editar-estado-resultados'
            );


        if (resultados) {

            resultados.hidden =
                true;
        }


        estado.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles: true,
                }
            )
        );


        return;
    }


    /* =====================================================
       BAJA VOLUNTARIA DESACTIVADA
    ===================================================== */

    const estadoAnterior =
        String(
            bajaVoluntaria
                .dataset
                .estadoAnterior
            || ''
        ).trim();


    const desistirActivo =
        desistir
        && desistir.checked;


    if (
        estadoAnterior !== ''
        && !desistirActivo
    ) {

        estado.value =
            estadoAnterior;


        if (textoEstado) {

            textoEstado.textContent =
                estadoAnterior;
        }
    }


    delete bajaVoluntaria
        .dataset
        .estadoAnterior;


    if (botonEstado) {

        botonEstado.disabled =
            Boolean(
                desistirActivo
            );
    }


    estado.dispatchEvent(
        new Event(
            'change',
            {
                bubbles: true,
            }
        )
    );
}


/* =========================================================
   ACTUALIZAR DESISTIR
========================================================= */

export function actualizarDesistirEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const desistir =
        modal.querySelector(
            '#editar-desistir'
        );


    const bajaVoluntaria =
        modal.querySelector(
            '#editar-baja-voluntaria'
        );


    const estado =
        modal.querySelector(
            '#editar-estado-actual'
        );


    const textoEstado =
        modal.querySelector(
            '#editar-estado-select-texto'
        );


    const botonEstado =
        modal.querySelector(
            '#editar-estado-select'
        );


    if (
        !desistir
        || !estado
    ) {
        return;
    }


    /* =====================================================
       DESISTIR ACTIVADO
    ===================================================== */

    if (
        desistir.checked
    ) {

        if (
            !desistir
                .dataset
                .estadoAnterior
        ) {

            const estadoAnterior =
                String(
                    estado.value
                    || 'Pendiente'
                ).trim();


            desistir
                .dataset
                .estadoAnterior =
                estadoAnterior !== 'Finalizado'
                    ? estadoAnterior
                    : 'Pendiente';
        }


        estado.value =
            'Finalizado';


        if (textoEstado) {

            textoEstado.textContent =
                'Finalizado';
        }


        if (botonEstado) {

            botonEstado.disabled =
                true;


            botonEstado.setAttribute(
                'aria-expanded',
                'false'
            );


            botonEstado.classList.remove(
                'estado-select--activo'
            );
        }


        const resultados =
            modal.querySelector(
                '#editar-estado-resultados'
            );


        if (resultados) {

            resultados.hidden =
                true;
        }


        estado.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles: true,
                }
            )
        );


        return;
    }


    /* =====================================================
       DESISTIR DESACTIVADO
    ===================================================== */

    const estadoAnterior =
        String(
            desistir
                .dataset
                .estadoAnterior
            || ''
        ).trim();


    const bajaActiva =
        bajaVoluntaria
        && bajaVoluntaria.checked;


    if (
        estadoAnterior !== ''
        && !bajaActiva
    ) {

        estado.value =
            estadoAnterior;


        if (textoEstado) {

            textoEstado.textContent =
                estadoAnterior;
        }
    }


    delete desistir
        .dataset
        .estadoAnterior;


    if (botonEstado) {

        botonEstado.disabled =
            Boolean(
                bajaActiva
            );
    }


    estado.dispatchEvent(
        new Event(
            'change',
            {
                bubbles: true,
            }
        )
    );
}


/* =========================================================
   FILTRAR MOTIVOS - EDITAR
========================================================= */

function filtrarMotivosEditar(modal) {

    if (!modal) {
        return;
    }

    const buscador =
        modal.querySelector('#editar-buscar-motivo');

    const resultados =
        modal.querySelector('#editar-motivos-resultados');

    if (!buscador || !resultados) {
        return;
    }

    const opciones = resultados.querySelectorAll(
        '[data-editar-motivo-opcion]'
    );

    if (buscador.disabled) {

        resultados.hidden = true;

        return;
    }

    buscador.value =
        buscador.value.toLocaleUpperCase('es-MX');

    const busqueda = normalizarTextoMotivoEditar(
        buscador.value
    );

    let encontrados = 0;

    opciones.forEach((opcion) => {

        const id = String(
            opcion.dataset.motivoId || ''
        ).trim();

        const texto = normalizarTextoMotivoEditar(
            opcion.dataset.motivoTexto
        );

        const sancion = normalizarTextoMotivoEditar(
            opcion.dataset.motivoSancion
        );

        const coincide =
            busqueda === '' ||
            texto.includes(busqueda) ||
            sancion.includes(busqueda) ||
            id === busqueda;

        opcion.hidden = !coincide;

        opcion.style.display = coincide
            ? 'flex'
            : 'none';

        if (coincide) {
            encontrados++;
        }

    });

    let mensajeVacio = resultados.querySelector(
        '.motivos-resultados__vacio'
    );

    if (!mensajeVacio) {

        mensajeVacio = document.createElement('div');

        mensajeVacio.className =
            'motivos-resultados__vacio';

        mensajeVacio.textContent =
            'Sin coincidencias';

        resultados.appendChild(mensajeVacio);
    }

    mensajeVacio.hidden = encontrados > 0;

    mensajeVacio.style.display = encontrados > 0
        ? 'none'
        : 'block';

    resultados.hidden = false;
}

/* =========================================================
   AGREGAR / REEMPLAZAR MOTIVO - EDITAR
========================================================= */

function agregarMotivoEditar(modal, opcion) {

    if (!modal || !opcion) {
        return;
    }

    const conSancion =
        modal.querySelector('#editar-con-sancion');

    if (!conSancion?.checked) {
        return;
    }

    const id = String(
        opcion.dataset.motivoId || ''
    ).trim();

    const texto = String(
        opcion.dataset.motivoTexto || ''
    ).trim();

    const sancion = String(
        opcion.dataset.motivoSancion || ''
    ).trim();

    if (!id) {
        return;
    }

    /* =====================================================
       CONSERVAR DATOS SI ES EL MISMO MOTIVO
    ===================================================== */

    const motivoAnterior =
        motivosSeleccionados.get(id);

    /* =====================================================
       PERMITIR ÚNICAMENTE UN MOTIVO
    ===================================================== */

    motivosSeleccionados.clear();
    motivosRespaldo.clear();

    motivosSeleccionados.set(id, {

        id_motivo: id,

        motivo: texto,

        sancion: sancion,

        folio_sancion:
            motivoAnterior?.folio_sancion || '',

        motivo_personalizado:
            id === '55'
                ? (motivoAnterior?.motivo_personalizado || '')
                : ''

    });

    /* =====================================================
       LIMPIAR BUSCADOR
    ===================================================== */

    const buscador =
        modal.querySelector('#editar-buscar-motivo');

    const resultados =
        modal.querySelector('#editar-motivos-resultados');

    if (buscador) {
        buscador.value = '';
    }

    if (resultados) {
        resultados.hidden = true;
    }

    /* =====================================================
       ACTUALIZAR TABLA Y CAMPOS
    ===================================================== */

    renderizarMotivosEditar(modal);
}


/* =========================================================
   RENDERIZAR MOTIVOS
========================================================= */

export function renderizarMotivosEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const contenedor =
        modal.querySelector(
            '#editar-motivos-agregados'
        );


    const tbody =
        modal.querySelector(
            '#editar-motivos-agregados-body'
        );


    if (
        !contenedor
        || !tbody
    ) {
        return;
    }


    tbody.innerHTML =
        '';


    /* =====================================================
       SIN MOTIVOS
    ===================================================== */

    if (
        motivosSeleccionados.size === 0
    ) {

        contenedor.hidden =
            true;


        contenedor.style.setProperty(
            'display',
            'none',
            'important'
        );


        actualizarInputsMotivosEditar(
            modal
        );


        actualizarTotalHorasArrestoEditar(
            modal
        );


        return;
    }


    /* =====================================================
       MOSTRAR TABLA
    ===================================================== */

    contenedor.hidden =
        false;


    contenedor.style.removeProperty(
        'display'
    );


    /* =====================================================
       FILAS
    ===================================================== */

    motivosSeleccionados.forEach(
        (motivo) => {

            const fila =
                document.createElement(
                    'tr'
                );


            /* =================================================
               NÚMERO
            ================================================= */

            const celdaNumero =
                document.createElement(
                    'td'
                );


            const numero =
                document.createElement(
                    'span'
                );


            numero.className =
                'motivos-tabla__numero';


            numero.textContent =
                motivo.id_motivo;


            celdaNumero.appendChild(
                numero
            );


            /* =================================================
            MOTIVO
            ================================================= */

            const celdaMotivo =
                document.createElement(
                    'td'
                );


            const esAmonestacionVerbal =
                String(
                    motivo.id_motivo
                ) === '55';


            if (
                esAmonestacionVerbal
            ) {

                const inputMotivoPersonalizado =
                    document.createElement(
                        'input'
                    );


                inputMotivoPersonalizado.type =
                    'text';


                inputMotivoPersonalizado.className =
                    'report-input motivos-tabla__motivo-personalizado';


                inputMotivoPersonalizado.value =
                    motivo.motivo_personalizado
                    || '';


                inputMotivoPersonalizado.placeholder =
                    'Escribe el motivo de la amonestación';


                inputMotivoPersonalizado.maxLength =
                    255;


                inputMotivoPersonalizado.autocomplete =
                    'off';


                inputMotivoPersonalizado.required =
                    true;


                inputMotivoPersonalizado.dataset.editarMotivoPersonalizado =
                    '1';


                inputMotivoPersonalizado.dataset.motivoId =
                    motivo.id_motivo;


                celdaMotivo.appendChild(
                    inputMotivoPersonalizado
                );

            } else {

                const textoMotivo =
                    document.createElement(
                        'span'
                    );


                textoMotivo.className =
                    'motivos-tabla__motivo';


                textoMotivo.textContent =
                    motivo.motivo
                    || '—';


                celdaMotivo.appendChild(
                    textoMotivo
                );
            }


            /* =================================================
               SANCIÓN
            ================================================= */

            const celdaSancion =
                document.createElement(
                    'td'
                );


            const sancion =
                document.createElement(
                    'span'
                );


            sancion.className =
                'motivos-tabla__sancion';


            sancion.textContent =
                motivo.sancion
                || 'Sin sanción definida';


            celdaSancion.appendChild(
                sancion
            );


            /* =================================================
               FOLIO DE SANCIÓN
            ================================================= */

            const celdaFolio =
                document.createElement(
                    'td'
                );


            const inputFolio =
                document.createElement(
                    'input'
                );


            inputFolio.type =
                'text';


            inputFolio.className =
                'motivos-tabla__folio';


            inputFolio.value =
                normalizarFolioSancion(
                    motivo.folio_sancion
                );


            inputFolio.placeholder =
                'Opcional';


            inputFolio.autocomplete =
                'off';


            inputFolio.dataset.editarFolioSancion =
                '1';


            inputFolio.dataset.motivoId =
                motivo.id_motivo;


            celdaFolio.appendChild(
                inputFolio
            );


            /* =================================================
               ACCIONES
            ================================================= */

            const celdaAcciones =
                document.createElement(
                    'td'
                );


            const botonQuitar =
                document.createElement(
                    'button'
                );


            botonQuitar.type =
                'button';


            botonQuitar.className =
                'motivos-tabla__eliminar';


            botonQuitar.textContent =
                'Quitar';


            botonQuitar.dataset.editarQuitarMotivo =
                '1';


            botonQuitar.dataset.motivoId =
                motivo.id_motivo;


            celdaAcciones.appendChild(
                botonQuitar
            );


            /* =================================================
               AGREGAR CELDAS
            ================================================= */

            fila.append(
                celdaNumero,
                celdaMotivo,
                celdaSancion,
                celdaFolio,
                celdaAcciones
            );


            tbody.appendChild(
                fila
            );
        }
    );


    /* =====================================================
       INPUTS PARA BACKEND
    ===================================================== */

    actualizarInputsMotivosEditar(
        modal
    );


    /* =====================================================
       TOTAL DE HORAS DE ARRESTO
    ===================================================== */

    actualizarTotalHorasArrestoEditar(
        modal
    );
}

/* =========================================================
   INPUTS DINÁMICOS PARA FORMDATA
========================================================= */

function actualizarInputsMotivosEditar(
    modal
) {

    const contenedor =
        modal.querySelector(
            '#editar-motivos-inputs'
        );


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML =
        '';


    let indice =
        0;


    motivosSeleccionados.forEach(
        (motivo) => {

            /* =================================================
               ID MOTIVO
            ================================================= */

            const inputId =
                document.createElement(
                    'input'
                );


            inputId.type =
                'hidden';


            inputId.name =
                `motivos_seleccionados[${indice}][id_motivo]`;


            inputId.value =
                motivo.id_motivo;


            /* =================================================
               FOLIO DE SANCIÓN
            ================================================= */

            const inputFolio =
                document.createElement(
                    'input'
                );


            inputFolio.type =
                'hidden';


            inputFolio.name =
                `motivos_seleccionados[${indice}][folio_sancion]`;


            inputFolio.value =
                motivo.folio_sancion
                || '';


            contenedor.append(
                inputId,
                inputFolio
            );


            /* =================================================
               MOTIVO PERSONALIZADO
               SOLO PARA AMONESTACIÓN VERBAL
            ================================================= */

            if (
                String(
                    motivo.id_motivo
                ) === '55'
            ) {

                const inputMotivoPersonalizado =
                    document.createElement(
                        'input'
                    );


                inputMotivoPersonalizado.type =
                    'hidden';


                inputMotivoPersonalizado.name =
                    `motivos_seleccionados[${indice}][motivo_personalizado]`;


                inputMotivoPersonalizado.value =
                    String(
                        motivo.motivo_personalizado
                        || ''
                    );


                contenedor.appendChild(
                    inputMotivoPersonalizado
                );
            }


            indice++;
        }
    );
}


/* =========================================================
   LIMPIAR MOTIVOS - EDITAR
========================================================= */

export function limpiarMotivosEditar(modal) {

    /* =====================================================
       LIMPIAR TODOS LOS ESTADOS
    ===================================================== */

    motivosSeleccionados.clear();
    motivosRespaldo.clear();
    motivosOriginalesEditar.clear();

    if (!modal) {
        return;
    }

    /* =====================================================
       LIMPIAR BUSCADOR
    ===================================================== */

    const buscador =
        modal.querySelector('#editar-buscar-motivo');

    const resultados =
        modal.querySelector('#editar-motivos-resultados');

    if (buscador) {

        buscador.value = '';
        buscador.disabled = true;

        buscador.classList.add(
            'report-input--disabled'
        );

    }

    /* =====================================================
       OCULTAR CATÁLOGO
    ===================================================== */

    if (resultados) {

        resultados.hidden = true;

        resultados.querySelectorAll(
            '[data-editar-motivo-opcion]'
        ).forEach((opcion) => {

            opcion.hidden = true;
            opcion.style.display = 'none';

        });

        const mensaje = resultados.querySelector(
            '.motivos-resultados__vacio'
        );

        if (mensaje) {
            mensaje.hidden = true;
            mensaje.style.display = 'none';
        }

    }

    /* =====================================================
       LIMPIAR VISUALIZACIÓN
    ===================================================== */

    renderizarMotivosEditar(modal);
}

/* =========================================================
   NORMALIZAR TEXTO DE MOTIVOS - EDITAR
========================================================= */

function normalizarTextoMotivoEditar(valor) {

    return String(valor ?? '')
        .trim()
        .toLocaleUpperCase('es-MX')
        .replace(/Ñ/g, '\uE000')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\uE000/g, 'Ñ');
}


/* =========================================================
   ACTUALIZAR TOTAL DE HORAS DE ARRESTO
========================================================= */

function actualizarTotalHorasArrestoEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const inputTotal =
        modal.querySelector(
            '#editar-total-horas-arresto'
        );


    if (!inputTotal) {
        return;
    }


    let totalHoras =
        0;


    motivosSeleccionados.forEach(
        (motivo) => {

            const sancion =
                String(
                    motivo.sancion
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const coincidencia =
                sancion.match(
                    /^ARRESTO\s+POR\s+(\d+)\s+HORAS$/
                );


            if (!coincidencia) {
                return;
            }


            const horas =
                Number(
                    coincidencia[1]
                );


            if (
                Number.isFinite(
                    horas
                )
                && horas > 0
            ) {

                totalHoras +=
                    horas;
            }
        }
    );


    inputTotal.value =
        String(
            totalHoras
        );
}


/* =========================================================
   HABILITAR / DESHABILITAR MOTIVOS - EDITAR
========================================================= */

export function establecerMotivosHabilitadosEditar(
    modal,
    habilitados,
    conservarRespaldo = true
) {

    if (!modal) {
        return;
    }

    const buscador =
        modal.querySelector('#editar-buscar-motivo');

    const resultados =
        modal.querySelector('#editar-motivos-resultados');

    /* =====================================================
       ACTUALIZAR BUSCADOR
    ===================================================== */

    if (buscador) {

        buscador.disabled = !habilitados;

        buscador.classList.toggle(
            'report-input--disabled',
            !habilitados
        );

        if (!habilitados) {
            buscador.value = '';
        }

    }

    /* =====================================================
       OCULTAR CATÁLOGO
    ===================================================== */

    if (resultados) {

        resultados.hidden = true;

        resultados.querySelectorAll(
            '[data-editar-motivo-opcion]'
        ).forEach((opcion) => {

            opcion.hidden = true;
            opcion.style.display = 'none';

        });

        const mensaje = resultados.querySelector(
            '.motivos-resultados__vacio'
        );

        if (mensaje) {
            mensaje.hidden = true;
            mensaje.style.display = 'none';
        }

    }

    /* =====================================================
       DESHABILITAR MOTIVOS
    ===================================================== */

    if (!habilitados) {

        motivosSeleccionados.clear();
        motivosRespaldo.clear();

        /*
         * No modificamos motivosOriginalesEditar.
         * Su contenido debe permanecer intacto.
         */

        renderizarMotivosEditar(modal);

        return;
    }

    /* =====================================================
       RECUPERAR MOTIVOS ORIGINALES
    ===================================================== */

    /*
     * Al regresar a "Con sanción" se recuperan
     * exclusivamente los motivos cargados desde BD.
     *
     * Nunca se restaura el último motivo temporal.
     */

    motivosSeleccionados.clear();
    motivosRespaldo.clear();

    motivosOriginalesEditar.forEach(
        (motivo, idMotivo) => {

            motivosSeleccionados.set(
                idMotivo,
                { ...motivo }
            );

        }
    );

    /* =====================================================
       ACTUALIZAR TABLA
    ===================================================== */

    renderizarMotivosEditar(modal);
}

/* =========================================================
   OBTENER MOTIVOS ACTUALES DE EDITAR
========================================================= */

export function obtenerMotivosEditar() {

    const motivos = [];


    motivosSeleccionados.forEach(
        (motivo) => {

            motivos.push({
                id_motivo:
                    motivo.id_motivo,

                motivo:
                    motivo.motivo,

                sancion:
                    motivo.sancion,

                folio_sancion:
                    normalizarFolioSancion(
                        motivo.folio_sancion
                    ),
            });
        }
    );


    return motivos;
}


/* =========================================================
   ACTUALIZAR SITUACIÓN DE SANCIÓN - EDITAR
========================================================= */

function actualizarSituacionSancionEditar(
    modal,
    opcionActiva = null
) {

    if (!modal) {
        return;
    }

    const opciones = {
        conSancion: modal.querySelector('#editar-con-sancion'),
        sinSanciones: modal.querySelector('#editar-sin-sanciones'),
        bajaVoluntaria: modal.querySelector('#editar-baja-voluntaria'),
        desistir: modal.querySelector('#editar-desistir')
    };

    if (opcionActiva && opciones[opcionActiva]?.checked) {

        Object.entries(opciones).forEach(([clave, checkbox]) => {

            if (clave !== opcionActiva && checkbox) {
                checkbox.checked = false;
            }

        });

    }

    const conSancionActiva =
        opciones.conSancion?.checked === true;

    establecerMotivosHabilitadosEditar(
        modal,
        conSancionActiva,
        true
    );

    actualizarBajaVoluntariaEditar(modal);
    actualizarDesistirEditar(modal);
}