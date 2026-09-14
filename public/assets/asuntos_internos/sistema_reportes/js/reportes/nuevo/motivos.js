/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   NUEVO REPORTE
   MOTIVOS Y SANCIONES
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarMotivos();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarMotivos() {

    const buscador =
        document.querySelector(
            '#buscar-motivo'
        );


    const resultados =
        document.querySelector(
            '#motivos-resultados'
        );


    const opciones =
        Array.from(
            document.querySelectorAll(
                '[data-motivo-opcion]'
            )
        );


    const contenedorAgregados =
        document.querySelector(
            '#motivos-agregados'
        );


    const tablaBody =
        document.querySelector(
            '#motivos-agregados-body'
        );


    const contenedorInputs =
        document.querySelector(
            '#motivos-inputs'
        );


    const checkboxSinSanciones =
        document.querySelector(
            '#sin-sanciones'
        );


    const checkboxBajaVoluntaria =
        document.querySelector(
            '#baja-voluntaria'
        );


    const selectEstado =
        document.querySelector(
            '#estado_actual'
        );


    const inputTotalHoras =
        document.querySelector(
            '#total_horas_arresto'
        );

    if (
        !buscador
        || !resultados
        || !contenedorAgregados
        || !tablaBody
        || !contenedorInputs
    ) {
        return;
    }


    /* =====================================================
       ESTADO
    ===================================================== */

    const motivosSeleccionados =
        new Map();


    /* =====================================================
       NORMALIZAR TEXTO
    ===================================================== */

    function normalizarTexto(
        valor
    ) {

        return String(
            valor
            || ''
        )
            .normalize(
                'NFD'
            )
            .replace(
                /[\u0300-\u036f]/g,
                ''
            )
            .toLowerCase()
            .trim();
    }

    /* =========================================================
    CALCULAR TOTAL DE HORAS DE ARRESTO
    ========================================================= */

    function actualizarTotalHorasArresto() {

        if (!inputTotalHoras) {
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


                /*
                * Solo contabilizamos sanciones con el formato:
                *
                * ARRESTO POR 8 HORAS
                * ARRESTO POR 12 HORAS
                * ARRESTO POR 24 HORAS
                * ARRESTO POR 36 HORAS
                */
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


        inputTotalHoras.value =
            String(
                totalHoras
            );
    }

    /* =====================================================
       CERRAR RESULTADOS
    ===================================================== */

    function cerrarResultados() {

        resultados.hidden =
            true;
    }


    /* =====================================================
       MOSTRAR RESULTADOS
    ===================================================== */

    function mostrarResultados(
        termino
    ) {

        /*
         * Si está marcada la opción
         * "Sin sanciones", no permitimos
         * utilizar el catálogo.
         */
        if (
            (
                checkboxSinSanciones
                && checkboxSinSanciones.checked
            )
            || (
                checkboxBajaVoluntaria
                && checkboxBajaVoluntaria.checked
            )
        ) {

            cerrarResultados();

            return;
        }


        const busqueda =
            normalizarTexto(
                termino
            );


        /*
         * No mostramos los 54 motivos cuando
         * el campo está vacío.
         */
        if (
            busqueda === ''
        ) {

            cerrarResultados();

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
                    );


                const motivo =
                    normalizarTexto(
                        opcion.dataset.motivoTexto
                    );


                const sancion =
                    normalizarTexto(
                        opcion.dataset.motivoSancion
                    );


                const yaSeleccionado =
                    motivosSeleccionados.has(
                        id
                    );


                const coincide =
                    motivo.includes(
                        busqueda
                    )
                    || sancion.includes(
                        busqueda
                    )
                    || id === busqueda;


                const mostrar =
                    coincide
                    && !yaSeleccionado;


                opcion.hidden =
                    !mostrar;


                if (
                    mostrar
                ) {

                    encontrados++;
                }
            }
        );


        resultados.hidden =
            encontrados === 0;
    }


    /* =====================================================
       ESCAPAR HTML
    ===================================================== */

    function escaparHtml(
        valor
    ) {

        const elemento =
            document.createElement(
                'div'
            );


        elemento.textContent =
            String(
                valor
                || ''
            );


        return elemento.innerHTML;
    }


    /* =========================================================
    RENDERIZAR MOTIVOS AGREGADOS
    ========================================================= */

    function renderizarMotivos() {

        tablaBody.innerHTML =
            '';


        contenedorInputs.innerHTML =
            '';


        /* =====================================================
        SIN MOTIVOS
        ===================================================== */

        if (
            motivosSeleccionados.size === 0
        ) {

            contenedorAgregados.hidden =
                true;


            /*
            * Al no existir motivos,
            * el total debe regresar a cero.
            */
            actualizarTotalHorasArresto();


            return;
        }


        /* =====================================================
        MOSTRAR TABLA
        ===================================================== */

        contenedorAgregados.hidden =
            false;


        let indice =
            0;


        motivosSeleccionados.forEach(
            (motivo) => {

                /* =============================================
                FILA VISUAL
                ============================================== */

                const fila =
                    document.createElement(
                        'tr'
                    );


                fila.dataset.motivoId =
                    motivo.id;


                fila.innerHTML = `

                    <td>

                        <span class="motivos-tabla__numero">

                            ${escaparHtml(
                                motivo.id
                            )}

                        </span>

                    </td>


                    <td>

                        <strong class="motivos-tabla__motivo">

                            ${escaparHtml(
                                motivo.texto
                            )}

                        </strong>

                    </td>


                    <td>

                        <span class="motivos-tabla__sancion">

                            ${escaparHtml(
                                motivo.sancion
                                || 'Sin sanción definida'
                            )}

                        </span>

                    </td>


                    <td>

                        <input
                            type="text"
                            class="report-input motivos-tabla__folio"
                            data-motivo-folio
                            data-motivo-id="${escaparHtml(
                                motivo.id
                            )}"
                            value="${escaparHtml(
                                motivo.folio
                            )}"
                            placeholder="Opcional"
                            maxlength="150"
                            autocomplete="off"
                        >

                    </td>


                    <td>

                        <button
                            type="button"
                            class="motivos-tabla__eliminar"
                            data-motivo-eliminar
                            data-motivo-id="${escaparHtml(
                                motivo.id
                            )}"
                        >
                            Quitar
                        </button>

                    </td>

                `;


                tablaBody.appendChild(
                    fila
                );


                /* =============================================
                INPUT ID MOTIVO
                ============================================== */

                const inputId =
                    document.createElement(
                        'input'
                    );


                inputId.type =
                    'hidden';


                inputId.name =
                    `motivos_seleccionados[${indice}][id_motivo]`;


                inputId.value =
                    motivo.id;


                contenedorInputs.appendChild(
                    inputId
                );


                /* =============================================
                INPUT FOLIO SANCIÓN
                ============================================== */

                const inputFolio =
                    document.createElement(
                        'input'
                    );


                inputFolio.type =
                    'hidden';


                inputFolio.name =
                    `motivos_seleccionados[${indice}][folio_sancion]`;


                inputFolio.value =
                    motivo.folio;


                inputFolio.dataset.inputFolioMotivo =
                    motivo.id;


                contenedorInputs.appendChild(
                    inputFolio
                );


                indice++;
            }
        );


        /* =====================================================
        TOTAL DE HORAS DE ARRESTO
        ===================================================== */

        actualizarTotalHorasArresto();


        /* =====================================================
        EVENTOS DE LA TABLA
        ===================================================== */

        inicializarEventosTabla();


        /* =====================================================
        BAJA VOLUNTARIA
        ===================================================== */

        actualizarBajaVoluntaria();
    }


    /* =========================================================
    AGREGAR MOTIVO
    ========================================================= */

    function agregarMotivo(
        opcion
    ) {

        /* =====================================================
        NO PERMITIR MOTIVOS CUANDO NO APLICAN
        ===================================================== */

        if (
            (
                checkboxSinSanciones
                && checkboxSinSanciones.checked
            )
            || (
                checkboxBajaVoluntaria
                && checkboxBajaVoluntaria.checked
            )
        ) {
            return;
        }


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
            id === ''
            || texto === ''
        ) {
            return;
        }


        if (
            motivosSeleccionados.has(
                id
            )
        ) {
            return;
        }


        motivosSeleccionados.set(
            id,
            {
                id,
                texto,
                sancion,
                folio: '',
            }
        );


        buscador.value =
            '';


        cerrarResultados();


        renderizarMotivos();
    }


    /* =====================================================
       QUITAR MOTIVO
    ===================================================== */

    function quitarMotivo(
        id
    ) {

        motivosSeleccionados.delete(
            String(
                id
            )
        );


        renderizarMotivos();
    }


    /* =====================================================
       ACTUALIZAR FOLIO
    ===================================================== */

    function actualizarFolio(
        id,
        valor
    ) {

        const clave =
            String(
                id
            );


        const motivo =
            motivosSeleccionados.get(
                clave
            );


        if (
            !motivo
        ) {
            return;
        }


        motivo.folio =
            String(
                valor
                || ''
            ).trim();


        motivosSeleccionados.set(
            clave,
            motivo
        );


        const inputOculto =
            contenedorInputs.querySelector(
                `[data-input-folio-motivo="${CSS.escape(
                    clave
                )}"]`
            );


        if (
            inputOculto
        ) {

            inputOculto.value =
                motivo.folio;
        }
    }


    /* =====================================================
       EVENTOS DE TABLA
    ===================================================== */

    function inicializarEventosTabla() {

        /* =================================================
           QUITAR
        ================================================= */

        const botonesEliminar =
            tablaBody.querySelectorAll(
                '[data-motivo-eliminar]'
            );


        botonesEliminar.forEach(
            (boton) => {

                boton.addEventListener(
                    'click',
                    () => {

                        quitarMotivo(
                            boton.dataset.motivoId
                        );
                    }
                );
            }
        );


        /* =================================================
           FOLIO
        ================================================= */

        const inputsFolio =
            tablaBody.querySelectorAll(
                '[data-motivo-folio]'
            );


        inputsFolio.forEach(
            (input) => {

                input.addEventListener(
                    'input',
                    () => {

                        actualizarFolio(
                            input.dataset.motivoId,
                            input.value
                        );
                    }
                );
            }
        );
    }


    /* =====================================================
       BUSCADOR
    ===================================================== */

    buscador.addEventListener(
        'input',
        () => {

            mostrarResultados(
                buscador.value
            );
        }
    );


    /* =====================================================
       SELECCIONAR RESULTADO
    ===================================================== */

    opciones.forEach(
        (opcion) => {

            opcion.addEventListener(
                'click',
                () => {

                    agregarMotivo(
                        opcion
                    );
                }
            );
        }
    );


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                buscador.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrarResultados();
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
            ) {

                cerrarResultados();
            }
        }
    );


    /* =========================================================
       SIN SANCIONES
    ========================================================= */

    function actualizarSinSanciones() {

        if (
            !checkboxSinSanciones
        ) {
            return;
        }


        const sinSanciones =
            checkboxSinSanciones.checked;


        /* =====================================================
        NO PUEDE COEXISTIR CON BAJA VOLUNTARIA
        ===================================================== */

        if (
            sinSanciones
            && checkboxBajaVoluntaria
            && checkboxBajaVoluntaria.checked
        ) {

            checkboxBajaVoluntaria.checked =
                false;


            actualizarBajaVoluntaria();
        }


        /* =====================================================
           DESHABILITAR / HABILITAR CATÁLOGO
        ===================================================== */

        buscador.disabled =
            sinSanciones;


        /* =====================================================
           MARCAR VISUALMENTE
        ===================================================== */

        buscador.classList.toggle(
            'report-input--disabled',
            sinSanciones
        );


        if (
            sinSanciones
        ) {

            /* =================================================
               LIMPIAR BÚSQUEDA
            ================================================= */

            buscador.value =
                '';


            /* =================================================
               CERRAR RESULTADOS
            ================================================= */

            cerrarResultados();


            /* =================================================
               OCULTAR TODAS LAS OPCIONES
            ================================================= */

            opciones.forEach(
                (opcion) => {

                    opcion.hidden =
                        true;
                }
            );


            /* =================================================
               ELIMINAR MOTIVOS YA SELECCIONADOS

               Esto elimina también:
               - filas de tabla
               - folios de sanción
               - inputs hidden
            ================================================= */

            motivosSeleccionados.clear();


            renderizarMotivos();


            return;
        }


        /* =====================================================
           VOLVER A HABILITAR

           No restauramos selecciones anteriores.
        ===================================================== */

        buscador.disabled =
            false;
    }


    /* =========================================================
    BAJA VOLUNTARIA
    ========================================================= */

    function actualizarBajaVoluntaria() {

        if (
            !checkboxBajaVoluntaria
        ) {
            return;
        }


        const bajaVoluntaria =
            checkboxBajaVoluntaria.checked;


        /* =====================================================
        NO PUEDE COEXISTIR CON "SIN SANCIONES"
        ===================================================== */

        if (
            bajaVoluntaria
            && checkboxSinSanciones
            && checkboxSinSanciones.checked
        ) {

            checkboxSinSanciones.checked =
                false;
        }


        /* =====================================================
        ESTADO
        ===================================================== */

        if (
            selectEstado
        ) {

            if (
                bajaVoluntaria
            ) {

                /*
                * Guardamos el estado anterior
                * únicamente la primera vez.
                */
                if (
                    !selectEstado.dataset.estadoAnterior
                ) {

                    selectEstado.dataset.estadoAnterior =
                        selectEstado.value;
                }


                /*
                * Baja voluntaria = cierre definitivo.
                */
                selectEstado.value =
                    'Finalizado';


                /*
                * Solo estado visual.
                * NO usamos disabled para que el valor
                * pueda seguir enviándose al backend.
                */
                selectEstado.classList.add(
                    'report-select--readonly'
                );

            } else {

                selectEstado.classList.remove(
                    'report-select--readonly'
                );


                /*
                * Restaurar estado anterior.
                */
                if (
                    selectEstado.dataset.estadoAnterior
                ) {

                    selectEstado.value =
                        selectEstado.dataset.estadoAnterior;


                    delete selectEstado.dataset.estadoAnterior;
                }
            }
        }


        /* =====================================================
        DESHABILITAR / HABILITAR CATÁLOGO DE MOTIVOS
        ===================================================== */

        buscador.disabled =
            bajaVoluntaria;


        buscador.classList.toggle(
            'report-input--disabled',
            bajaVoluntaria
        );


        /* =====================================================
        BAJA VOLUNTARIA ACTIVA
        ===================================================== */

        if (
            bajaVoluntaria
        ) {

            /* =================================================
            LIMPIAR BÚSQUEDA
            ================================================= */

            buscador.value =
                '';


            /* =================================================
            CERRAR RESULTADOS
            ================================================= */

            cerrarResultados();


            /* =================================================
            OCULTAR OPCIONES
            ================================================= */

            opciones.forEach(
                (opcion) => {

                    opcion.hidden =
                        true;
                }
            );


            /* =================================================
            ELIMINAR MOTIVOS SELECCIONADOS

            Baja voluntaria no lleva motivos.
            Esto también elimina:
            - filas de tabla
            - folios de sanción
            - inputs hidden
            ================================================= */

            motivosSeleccionados.clear();


            renderizarMotivos();


            return;
        }


        /* =====================================================
        VOLVER A HABILITAR

        No restauramos motivos anteriores.
        ===================================================== */

        buscador.disabled =
            false;
    }


    /* =========================================================
    EVENTO BAJA VOLUNTARIA
    ========================================================= */

    if (
        checkboxBajaVoluntaria
    ) {

        checkboxBajaVoluntaria.addEventListener(
            'change',
            actualizarBajaVoluntaria
        );
    }


    /* =====================================================
       EVENTO SIN SANCIONES
    ===================================================== */

    if (
        checkboxSinSanciones
    ) {

        checkboxSinSanciones.addEventListener(
            'change',
            actualizarSinSanciones
        );
    }


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarSinSanciones();

    actualizarBajaVoluntaria();
}