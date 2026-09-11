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


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarMotivosEditar(
    modal
) {

    if (!modal) {
        return;
    }


    if (
        modal.dataset.editarMotivosInicializados
        === '1'
    ) {
        return;
    }


    const buscador =
        modal.querySelector(
            '#editar-buscar-motivo'
        );


    const resultados =
        modal.querySelector(
            '#editar-motivos-resultados'
        );


    const tbody =
        modal.querySelector(
            '#editar-motivos-agregados-body'
        );


    const contenedorInputs =
        modal.querySelector(
            '#editar-motivos-inputs'
        );


    if (
        !buscador
        || !resultados
        || !tbody
        || !contenedorInputs
    ) {
        return;
    }


    modal.dataset.editarMotivosInicializados =
        '1';


    /* =====================================================
       BUSCAR
    ===================================================== */

    buscador.addEventListener(
        'input',
        () => {

            filtrarMotivosEditar(
                modal
            );
        }
    );


    /* =====================================================
       CLICK EN RESULTADO
    ===================================================== */

    resultados.addEventListener(
        'click',
        (evento) => {

            const opcion =
                evento.target.closest(
                    '[data-editar-motivo-opcion]'
                );


            if (!opcion) {
                return;
            }


            agregarMotivoEditar(
                modal,
                opcion
            );
        }
    );


    /* =====================================================
       QUITAR MOTIVO
    ===================================================== */

    tbody.addEventListener(
        'click',
        (evento) => {

            const boton =
                evento.target.closest(
                    '[data-editar-quitar-motivo]'
                );


            if (!boton) {
                return;
            }


            const idMotivo =
                String(
                    boton.dataset.motivoId
                    || ''
                ).trim();


            if (!idMotivo) {
                return;
            }


            motivosSeleccionados.delete(
                idMotivo
            );


            renderizarMotivosEditar(
                modal
            );


            filtrarMotivosEditar(
                modal
            );
        }
    );


    /* =====================================================
       FOLIO DE SANCIÓN
    ===================================================== */

    tbody.addEventListener(
        'input',
        (evento) => {

            const input =
                evento.target.closest(
                    '[data-editar-folio-sancion]'
                );


            if (!input) {
                return;
            }


            const idMotivo =
                String(
                    input.dataset.motivoId
                    || ''
                ).trim();


            const motivo =
                motivosSeleccionados.get(
                    idMotivo
                );


            if (!motivo) {
                return;
            }


            motivo.folio_sancion =
                String(
                    input.value
                    || ''
                );


            motivosSeleccionados.set(
                idMotivo,
                motivo
            );


            actualizarInputsMotivosEditar(
                modal
            );
        }
    );
}


/* =========================================================
   FILTRAR MOTIVOS
========================================================= */

function filtrarMotivosEditar(
    modal
) {

    const buscador =
        modal.querySelector(
            '#editar-buscar-motivo'
        );


    const resultados =
        modal.querySelector(
            '#editar-motivos-resultados'
        );


    const opciones =
        resultados?.querySelectorAll(
            '[data-editar-motivo-opcion]'
        )
        || [];


    if (
        !buscador
        || !resultados
    ) {
        return;
    }


    const busqueda =
        normalizarTextoMotivoEditar(
            buscador.value
        );


    /*
     * Igual que en Nuevo:
     * si no se escribió nada, no mostramos todo el catálogo.
     */

    if (!busqueda) {

        resultados.hidden =
            true;


        opciones.forEach(
            (opcion) => {

                opcion.hidden =
                    true;
            }
        );


        return;
    }


    let encontrados =
        0;


    opciones.forEach(
        (opcion) => {

            const id =
                String(
                    opcion.dataset.motivoId
                    || ''
                ).trim();


            const texto =
                normalizarTextoMotivoEditar(
                    opcion.dataset.motivoTexto
                );


            const sancion =
                normalizarTextoMotivoEditar(
                    opcion.dataset.motivoSancion
                );


            const yaSeleccionado =
                motivosSeleccionados.has(
                    id
                );


            const coincide =
                !yaSeleccionado
                && (
                    texto.includes(
                        busqueda
                    )
                    || sancion.includes(
                        busqueda
                    )
                    || id === busqueda
                );


            opcion.hidden =
                !coincide;


            if (coincide) {

                encontrados++;
            }
        }
    );


    resultados.hidden =
        encontrados === 0;
}


/* =========================================================
   AGREGAR MOTIVO
========================================================= */

function agregarMotivoEditar(
    modal,
    opcion
) {

    const id =
        String(
            opcion.dataset.motivoId
            || ''
        ).trim();


    const texto =
        String(
            opcion.dataset.motivoTexto
            || ''
        ).trim();


    const sancion =
        String(
            opcion.dataset.motivoSancion
            || ''
        ).trim();


    if (
        !id
        || motivosSeleccionados.has(
            id
        )
    ) {
        return;
    }


    motivosSeleccionados.set(
        id,
        {
            id_motivo:
                id,

            motivo:
                texto,

            sancion:
                sancion,

            folio_sancion:
                '',
        }
    );


    const buscador =
        modal.querySelector(
            '#editar-buscar-motivo'
        );


    const resultados =
        modal.querySelector(
            '#editar-motivos-resultados'
        );


    if (buscador) {

        buscador.value =
            '';
    }


    if (resultados) {

        resultados.hidden =
            true;
    }


    renderizarMotivosEditar(
        modal
    );
}


/* =========================================================
   RENDERIZAR MOTIVOS
========================================================= */

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
                motivo.folio_sancion
                || '';


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


            indice++;
        }
    );
}


/* =========================================================
   LIMPIAR
========================================================= */

export function limpiarMotivosEditar(
    modal
) {

    motivosSeleccionados.clear();


    const buscador =
        modal?.querySelector(
            '#editar-buscar-motivo'
        );


    const resultados =
        modal?.querySelector(
            '#editar-motivos-resultados'
        );


    if (buscador) {

        buscador.value =
            '';
    }


    if (resultados) {

        resultados.hidden =
            true;
    }


    renderizarMotivosEditar(
        modal
    );
}


/* =========================================================
   NORMALIZAR TEXTO
========================================================= */

function normalizarTextoMotivoEditar(
    valor
) {

    return String(
        valor
        || ''
    )
        .trim()
        .toLocaleLowerCase(
            'es-MX'
        )
        .normalize(
            'NFD'
        )
        .replace(
            /[\u0300-\u036f]/g,
            ''
        );
}

/* =========================================================
   HABILITAR / DESHABILITAR MOTIVOS
========================================================= */

export function establecerMotivosHabilitadosEditar(
    modal,
    habilitados
) {

    if (!modal) {
        return;
    }


    const buscador =
        modal.querySelector(
            '#editar-buscar-motivo'
        );


    const resultados =
        modal.querySelector(
            '#editar-motivos-resultados'
        );


    if (buscador) {

        buscador.disabled =
            !habilitados;


        if (!habilitados) {

            buscador.value =
                '';
        }
    }


    if (resultados) {

        resultados.hidden =
            true;
    }


    if (!habilitados) {

        motivosSeleccionados.clear();


        renderizarMotivosEditar(
            modal
        );
    }
}