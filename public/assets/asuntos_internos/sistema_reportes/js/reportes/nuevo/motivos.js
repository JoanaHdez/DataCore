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


    const checkboxDesistir =
        document.querySelector(
            '#desistir'
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
        * Cuando alguna situación especial está activa,
        * no permitimos utilizar el catálogo de motivos.
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
            || (
                checkboxDesistir
                && checkboxDesistir.checked
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
        * No mostramos todos los motivos
        * cuando el campo está vacío.
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
            || (
                checkboxDesistir
                && checkboxDesistir.checked
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
    CATÁLOGO DE ESTADO
    ========================================================= */

    function inicializarCatalogoEstado() {

        const inputEstado =
            document.querySelector(
                '#estado_actual'
            );


        const selector =
            document.querySelector(
                '#estado-select'
            );


        const texto =
            document.querySelector(
                '#estado-select-texto'
            );


        const resultadosEstado =
            document.querySelector(
                '#estado-resultados'
            );


        const opcionesEstado =
            document.querySelectorAll(
                '[data-estado-opcion]'
            );


        if (
            !inputEstado
            || !selector
            || !texto
            || !resultadosEstado
        ) {
            return;
        }


        /* =====================================================
           EVITAR LISTENERS DUPLICADOS
        ===================================================== */

        if (
            selector.dataset
                .estadoInicializado
            === '1'
        ) {
            return;
        }


        selector.dataset
            .estadoInicializado =
            '1';


        /* =====================================================
           CERRAR CATÁLOGO
        ===================================================== */

        function cerrarCatalogoEstado() {

            resultadosEstado.hidden =
                true;


            selector.classList.remove(
                'estado-select--activo'
            );


            selector.setAttribute(
                'aria-expanded',
                'false'
            );
        }


        /* =====================================================
           ABRIR / CERRAR
        ===================================================== */

        selector.addEventListener(
            'click',
            () => {

                /*
                 * Cuando Baja voluntaria está activa,
                 * Estado queda bloqueado.
                 */
                if (
                    selector.disabled
                ) {
                    return;
                }


                const estaAbierto =
                    !resultadosEstado.hidden;


                if (
                    estaAbierto
                ) {

                    cerrarCatalogoEstado();

                    return;
                }


                resultadosEstado.hidden =
                    false;


                selector.classList.add(
                    'estado-select--activo'
                );


                selector.setAttribute(
                    'aria-expanded',
                    'true'
                );
            }
        );


        /* =====================================================
           SELECCIONAR OPCIÓN
        ===================================================== */

        opcionesEstado.forEach(
            (opcion) => {

                opcion.addEventListener(
                    'click',
                    () => {

                        const valor =
                            String(
                                opcion.dataset.estado
                                || ''
                            ).trim();


                        if (
                            valor === ''
                        ) {
                            return;
                        }


                        /* =========================================
                           VALOR REAL
                        ========================================== */

                        inputEstado.value =
                            valor;


                        /* =========================================
                           VALOR VISUAL
                        ========================================== */

                        texto.textContent =
                            valor;


                        /* =========================================
                           CERRAR
                        ========================================== */

                        cerrarCatalogoEstado();


                        /* =========================================
                           NOTIFICAR CAMBIO
                        ========================================== */

                        inputEstado.dispatchEvent(
                            new Event(
                                'change',
                                {
                                    bubbles:
                                        true,
                                }
                            )
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
                    selector.contains(
                        evento.target
                    )
                    || resultadosEstado.contains(
                        evento.target
                    )
                ) {
                    return;
                }


                cerrarCatalogoEstado();
            }
        );


        /* =====================================================
           ESC
        ===================================================== */

        document.addEventListener(
            'keydown',
            (evento) => {

                if (
                    evento.key
                    !== 'Escape'
                ) {
                    return;
                }


                cerrarCatalogoEstado();
            }
        );


        /* =====================================================
           ESTADO VISUAL INICIAL
        ===================================================== */

        const valorInicial =
            String(
                inputEstado.value
                || 'Pendiente'
            ).trim();


        texto.textContent =
            valorInicial !== ''
                ? valorInicial
                : 'Pendiente';
    }


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
        NO PUEDE COEXISTIR CON DESISTIR
        ===================================================== */

        if (
            sinSanciones
            && checkboxDesistir
            && checkboxDesistir.checked
        ) {

            checkboxDesistir.checked =
                false;


            actualizarDesistir();
        }


        /* =====================================================
        DESHABILITAR / HABILITAR CATÁLOGO
        ===================================================== */

        buscador.disabled =
            sinSanciones;


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
            ================================================= */

            motivosSeleccionados.clear();


            renderizarMotivos();


            return;
        }


        /* =====================================================
        VOLVER A HABILITAR

        Solo si Baja voluntaria y Desistir tampoco están activos.
        ===================================================== */

        const bloqueadoPorOtraSituacion =
            (
                checkboxBajaVoluntaria
                && checkboxBajaVoluntaria.checked
            )
            || (
                checkboxDesistir
                && checkboxDesistir.checked
            );


        buscador.disabled =
            bloqueadoPorOtraSituacion;


        buscador.classList.toggle(
            'report-input--disabled',
            bloqueadoPorOtraSituacion
        );
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


        const inputEstado =
            document.querySelector(
                '#estado_actual'
            );


        const textoEstado =
            document.querySelector(
                '#estado-select-texto'
            );


        const botonEstado =
            document.querySelector(
                '#estado-select'
            );


        const resultadosEstado =
            document.querySelector(
                '#estado-resultados'
            );


        /* =====================================================
        NO PUEDE COEXISTIR CON SIN SANCIONES
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
        NO PUEDE COEXISTIR CON DESISTIR
        ===================================================== */

        if (
            bajaVoluntaria
            && checkboxDesistir
            && checkboxDesistir.checked
        ) {

            checkboxDesistir.checked =
                false;


            actualizarDesistir();
        }


        /* =====================================================
        ESTADO DEL REPORTE
        ===================================================== */

        if (
            inputEstado
        ) {

            /* =================================================
            BAJA VOLUNTARIA ACTIVADA
            ================================================= */

            if (
                bajaVoluntaria
            ) {

                /*
                * Guardamos el estado anterior.
                */

                if (
                    !checkboxBajaVoluntaria
                        .dataset
                        .estadoAnterior
                ) {

                    const estadoAnterior =
                        String(
                            inputEstado.value
                            || 'Pendiente'
                        ).trim();


                    checkboxBajaVoluntaria
                        .dataset
                        .estadoAnterior =
                        estadoAnterior !== 'Finalizado'
                            ? estadoAnterior
                            : 'Pendiente';
                }


                /* =============================================
                FORZAR FINALIZADO
                ============================================== */

                inputEstado.value =
                    'Finalizado';


                if (
                    textoEstado
                ) {

                    textoEstado.textContent =
                        'Finalizado';
                }


                /* =============================================
                BLOQUEAR CATÁLOGO
                ============================================== */

                if (
                    botonEstado
                ) {

                    botonEstado.disabled =
                        true;


                    botonEstado.classList.remove(
                        'estado-select--activo'
                    );


                    botonEstado.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }


                if (
                    resultadosEstado
                ) {

                    resultadosEstado.hidden =
                        true;
                }


                inputEstado.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true,
                        }
                    )
                );

            } else {

                /* =================================================
                BAJA VOLUNTARIA DESACTIVADA
                ================================================= */

                const estadoAnterior =
                    String(
                        checkboxBajaVoluntaria
                            .dataset
                            .estadoAnterior
                        || ''
                    ).trim();


                /*
                * Solo restauramos si Desistir no está activo.
                */

                const desistirActivo =
                    checkboxDesistir
                    && checkboxDesistir.checked;


                if (
                    estadoAnterior !== ''
                    && !desistirActivo
                ) {

                    inputEstado.value =
                        estadoAnterior;


                    if (
                        textoEstado
                    ) {

                        textoEstado.textContent =
                            estadoAnterior;
                    }
                }


                delete checkboxBajaVoluntaria
                    .dataset
                    .estadoAnterior;


                /* =============================================
                HABILITAR ESTADO SOLO SI DESISTIR NO ESTÁ ACTIVO
                ============================================== */

                if (
                    botonEstado
                ) {

                    botonEstado.disabled =
                        Boolean(
                            desistirActivo
                        );
                }


                inputEstado.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true,
                        }
                    )
                );
            }
        }


        /* =====================================================
        CATÁLOGO DE MOTIVOS
        ===================================================== */

        const desistirActivo =
            checkboxDesistir
            && checkboxDesistir.checked;


        const sinSancionesActivo =
            checkboxSinSanciones
            && checkboxSinSanciones.checked;


        const bloquearMotivos =
            bajaVoluntaria
            || desistirActivo
            || sinSancionesActivo;


        buscador.disabled =
            bloquearMotivos;


        buscador.classList.toggle(
            'report-input--disabled',
            bloquearMotivos
        );


        /* =====================================================
        BAJA VOLUNTARIA ACTIVA
        ===================================================== */

        if (
            bajaVoluntaria
        ) {

            buscador.value =
                '';


            cerrarResultados();


            opciones.forEach(
                (opcion) => {

                    opcion.hidden =
                        true;
                }
            );


            motivosSeleccionados.clear();


            renderizarMotivos();
        }
    }


    /* =========================================================
    DESISTIR
    ========================================================= */

    function actualizarDesistir() {

        if (
            !checkboxDesistir
        ) {

            return;
        }


        const desistir =
            checkboxDesistir.checked;


        const inputEstado =
            document.querySelector(
                '#estado_actual'
            );


        const textoEstado =
            document.querySelector(
                '#estado-select-texto'
            );


        const botonEstado =
            document.querySelector(
                '#estado-select'
            );


        const resultadosEstado =
            document.querySelector(
                '#estado-resultados'
            );


        /* =====================================================
        NO PUEDE COEXISTIR CON SIN SANCIONES
        ===================================================== */

        if (
            desistir
            && checkboxSinSanciones
            && checkboxSinSanciones.checked
        ) {

            checkboxSinSanciones.checked =
                false;
        }


        /* =====================================================
        NO PUEDE COEXISTIR CON BAJA VOLUNTARIA
        ===================================================== */

        if (
            desistir
            && checkboxBajaVoluntaria
            && checkboxBajaVoluntaria.checked
        ) {

            checkboxBajaVoluntaria.checked =
                false;


            actualizarBajaVoluntaria();
        }


        /* =====================================================
        ESTADO DEL REPORTE
        ===================================================== */

        if (
            inputEstado
        ) {

            /* =================================================
            DESISTIR ACTIVADO
            ================================================= */

            if (
                desistir
            ) {

                /*
                * Conservamos el estado que existía antes
                * de marcar Desistir.
                */

                if (
                    !checkboxDesistir
                        .dataset
                        .estadoAnterior
                ) {

                    const estadoAnterior =
                        String(
                            inputEstado.value
                            || 'Pendiente'
                        ).trim();


                    checkboxDesistir
                        .dataset
                        .estadoAnterior =
                        estadoAnterior !== 'Finalizado'
                            ? estadoAnterior
                            : 'Pendiente';
                }


                /* =============================================
                FORZAR FINALIZADO
                ============================================== */

                inputEstado.value =
                    'Finalizado';


                if (
                    textoEstado
                ) {

                    textoEstado.textContent =
                        'Finalizado';
                }


                /* =============================================
                BLOQUEAR CATÁLOGO DE ESTADO
                ============================================== */

                if (
                    botonEstado
                ) {

                    botonEstado.disabled =
                        true;


                    botonEstado.classList.remove(
                        'estado-select--activo'
                    );


                    botonEstado.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }


                if (
                    resultadosEstado
                ) {

                    resultadosEstado.hidden =
                        true;
                }


                inputEstado.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true,
                        }
                    )
                );

            } else {

                /* =================================================
                DESISTIR DESACTIVADO
                ================================================= */

                const estadoAnterior =
                    String(
                        checkboxDesistir
                            .dataset
                            .estadoAnterior
                        || ''
                    ).trim();


                const bajaVoluntariaActiva =
                    checkboxBajaVoluntaria
                    && checkboxBajaVoluntaria.checked;


                /*
                * Restauramos únicamente cuando Baja voluntaria
                * tampoco está obligando el estado Finalizado.
                */

                if (
                    estadoAnterior !== ''
                    && !bajaVoluntariaActiva
                ) {

                    inputEstado.value =
                        estadoAnterior;


                    if (
                        textoEstado
                    ) {

                        textoEstado.textContent =
                            estadoAnterior;
                    }
                }


                delete checkboxDesistir
                    .dataset
                    .estadoAnterior;


                /* =============================================
                RESTAURAR CATÁLOGO DE ESTADO
                ============================================== */

                if (
                    botonEstado
                ) {

                    botonEstado.disabled =
                        Boolean(
                            bajaVoluntariaActiva
                        );
                }


                inputEstado.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true,
                        }
                    )
                );
            }
        }


        /* =====================================================
        MOTIVOS
        ===================================================== */

        const bajaVoluntariaActiva =
            checkboxBajaVoluntaria
            && checkboxBajaVoluntaria.checked;


        const sinSancionesActivo =
            checkboxSinSanciones
            && checkboxSinSanciones.checked;


        const bloquearMotivos =
            desistir
            || bajaVoluntariaActiva
            || sinSancionesActivo;


        buscador.disabled =
            bloquearMotivos;


        buscador.classList.toggle(
            'report-input--disabled',
            bloquearMotivos
        );


        /* =====================================================
        DESISTIR ACTIVO
        ===================================================== */

        if (
            desistir
        ) {

            buscador.value =
                '';


            cerrarResultados();


            opciones.forEach(
                (opcion) => {

                    opcion.hidden =
                        true;
                }
            );


            /*
            * Un desistimiento no conserva motivos/sanciones
            * anteriores.
            */

            motivosSeleccionados.clear();


            renderizarMotivos();
        }
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


    /* =========================================================
    EVENTO DESISTIR
    ========================================================= */

    if (
        checkboxDesistir
    ) {

        checkboxDesistir.addEventListener(
            'change',
            actualizarDesistir
        );
    }


    /* =====================================================
    CATÁLOGO DE ESTADO
    ===================================================== */

    inicializarCatalogoEstado();


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarSinSanciones();

    actualizarBajaVoluntaria();

    actualizarDesistir();

}