/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   NUEVO REPORTE
   CLASIFICACIÓN
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarClasificacion();

        inicializarInspector();

        inicializarInvestigador();
    }
);

/* =========================================================
   INICIALIZAR CLASIFICACIÓN
========================================================= */

function inicializarClasificacion() {

    const selector =
        document.querySelector(
            '#clasificacion-select'
        );


    const textoSelector =
        document.querySelector(
            '#clasificacion-select-texto'
        );


    const inputClasificacion =
        document.querySelector(
            '#clasificacion'
        );


    const resultados =
        document.querySelector(
            '#clasificacion-resultados'
        );


    const opciones =
        document.querySelectorAll(
            '[data-clasificacion-opcion]'
        );


    if (
        !selector
        || !textoSelector
        || !inputClasificacion
        || !resultados
    ) {
        return;
    }


    /* =====================================================
       ABRIR
    ===================================================== */

    function abrirCatalogo() {

        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'clasificacion-select--activo'
        );
    }


    /* =====================================================
       CERRAR
    ===================================================== */

    function cerrarCatalogo() {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'clasificacion-select--activo'
        );
    }


    /* =====================================================
       SELECCIONAR
    ===================================================== */

    function seleccionarClasificacion(
        valor
    ) {

        const nombre =
            String(
                valor
                || ''
            ).trim();


        if (
            nombre === ''
        ) {
            return;
        }


        inputClasificacion.value =
            nombre;


        textoSelector.textContent =
            nombre;


        cerrarCatalogo();
    }


    /* =====================================================
       SELECTOR
    ===================================================== */

    selector.addEventListener(
        'click',
        () => {

            if (
                resultados.hidden
            ) {

                abrirCatalogo();

            } else {

                cerrarCatalogo();
            }
        }
    );


    /* =====================================================
       OPCIONES
    ===================================================== */

    opciones.forEach(
        (opcion) => {

            opcion.addEventListener(
                'click',
                () => {

                    seleccionarClasificacion(
                        opcion.dataset
                            .clasificacionNombre
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
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrarCatalogo();
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

                cerrarCatalogo();
            }
        }
    );
}


/* =========================================================
   INICIALIZAR INSPECTOR
========================================================= */

function inicializarInspector() {

    const buscador =
        document.querySelector(
            '#inspector-busqueda'
        );


    const resultados =
        document.querySelector(
            '#inspector-resultados'
        );


    const inputInspector =
        document.querySelector(
            '#inspector'
        );


    const inputPlantillaId =
        document.querySelector(
            '#inspector-plantilla-id'
        );


    const contenedorSeleccionado =
        document.querySelector(
            '#inspector-seleccionado'
        );


    const foto =
        document.querySelector(
            '#inspector-foto'
        );


    const fotoFallback =
        document.querySelector(
            '#inspector-foto-fallback'
        );


    const nombre =
        document.querySelector(
            '#inspector-nombre'
        );


    const nomina =
        document.querySelector(
            '#inspector-nomina'
        );


    const detalle =
        document.querySelector(
            '#inspector-detalle'
        );


    const botonQuitar =
        document.querySelector(
            '#btn-quitar-inspector'
        );


    if (
        !buscador
        || !resultados
        || !inputInspector
        || !contenedorSeleccionado
    ) {
        return;
    }


    let temporizadorBusqueda =
        null;


    let controladorBusqueda =
        null;


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
    OBTENER LETRA DEL PRIMER APELLIDO
    ========================================================= */

    function obtenerIniciales(
        valor
    ) {

        const texto =
            String(
                valor
                || ''
            )
                .trim();


        if (
            texto === ''
        ) {

            return '—';
        }


        const partes =
            texto
                .split(/\s+/)
                .filter(Boolean);


        if (
            partes.length === 0
        ) {

            return '—';
        }


        /*
         * En plantilla_general.plantilla el nombre viene como:
         *
         * APELLIDO_PATERNO APELLIDO_MATERNO NOMBRE...
         *
         * Por eso tomamos la primera letra
         * de la primera palabra.
         */

        return partes[0]
            .charAt(0)
            .toUpperCase();
    }

    /* =====================================================
       CERRAR RESULTADOS
    ===================================================== */

    function cerrarResultados() {

        resultados.hidden =
            true;


        resultados.innerHTML =
            '';
    }


    /* =====================================================
       LIMPIAR SELECCIÓN
    ===================================================== */

    function limpiarInspector() {

        inputInspector.value =
            '';


        if (
            inputPlantillaId
        ) {

            inputPlantillaId.value =
                '';
        }


        buscador.value =
            '';


        contenedorSeleccionado.hidden =
            true;


        if (foto) {

            foto.src =
                '';

            foto.hidden =
                true;
        }


        if (fotoFallback) {

            fotoFallback.textContent =
                '—';

            fotoFallback.hidden =
                false;
        }


        if (nombre) {

            nombre.textContent =
                '—';
        }


        if (nomina) {

            nomina.textContent =
                '—';
        }


        if (detalle) {

            detalle.textContent =
                '—';
        }


        cerrarResultados();


        buscador.focus();
    }


    /* =====================================================
   SELECCIONAR INSPECTOR
===================================================== */

    function seleccionarInspector(
        persona
    ) {

        if (
            !persona
        ) {
            return;
        }


        const id =
            Number(
                persona.id
                || 0
            );


        const nombrePersona =
            String(
                persona.nombre
                || ''
            ).trim();


        const nominaPersona =
            String(
                persona.nomina
                || ''
            ).trim();


        const areaPersona =
            String(
                persona.area
                || ''
            ).trim();


        const turnoPersona =
            String(
                persona.turno
                || ''
            ).trim();


        const fotoPersona =
            String(
                persona.foto
                || ''
            ).trim();


        if (
            id <= 0
            || nombrePersona === ''
        ) {
            return;
        }


        /* =================================================
           VALORES REALES
        ================================================= */

        inputInspector.value =
            nombrePersona;


        if (
            inputPlantillaId
        ) {

            inputPlantillaId.value =
                String(
                    id
                );
        }


        /* =================================================
           BUSCADOR
        ================================================= */

        buscador.value =
            nombrePersona;


        /* =================================================
           INFORMACIÓN VISUAL
        ================================================= */

        if (
            nombre
        ) {

            nombre.textContent =
                nombrePersona;
        }


        if (
            nomina
        ) {

            nomina.textContent =
                nominaPersona !== ''
                    ? `Nómina: ${nominaPersona}`
                    : 'Nómina no disponible';
        }


        if (
            detalle
        ) {

            const datosDetalle =
                [];


            if (
                areaPersona !== ''
            ) {

                datosDetalle.push(
                    areaPersona
                );
            }


            if (
                turnoPersona !== ''
            ) {

                datosDetalle.push(
                    `Turno: ${turnoPersona}`
                );
            }


            detalle.textContent =
                datosDetalle.length > 0
                    ? datosDetalle.join(' · ')
                    : 'Personal de Asuntos Internos';
        }


        /* =================================================
           FOTO / FALLBACK
        ================================================= */

        if (
            foto
            && fotoFallback
        ) {

            const letraApellido =
                obtenerIniciales(
                    nombrePersona
                );


            /*
             * Estado inicial:
             * ocultamos ambos antes de decidir qué mostrar.
             */

            foto.hidden =
                true;

            foto.style.display =
                'none';


            fotoFallback.hidden =
                true;

            fotoFallback.style.display =
                'none';


            /*
             * Limpiar eventos y src anteriores.
             */

            foto.onerror =
                null;


            foto.removeAttribute(
                'src'
            );


            /* =================================================
               EXISTE URL DE FOTO
            ================================================= */

            if (
                fotoPersona !== ''
            ) {

                /*
                 * IMPORTANTE:
                 * registramos el error ANTES de colocar src.
                 */

                foto.onerror =
                    () => {

                        foto.hidden =
                            true;

                        foto.style.display =
                            'none';


                        fotoFallback.textContent =
                            letraApellido;

                        fotoFallback.hidden =
                            false;

                        fotoFallback.style.display =
                            'flex';
                    };


                foto.onload =
                    () => {

                        foto.hidden =
                            false;

                        foto.style.display =
                            'block';


                        fotoFallback.hidden =
                            true;

                        fotoFallback.style.display =
                            'none';
                    };


                foto.alt =
                    nombrePersona;


                /*
                 * src siempre al final.
                 */

                foto.src =
                    fotoPersona;

            } else {

                /* =================================================
                   NO EXISTE FOTO
                ================================================= */

                fotoFallback.textContent =
                    letraApellido;

                fotoFallback.hidden =
                    false;

                fotoFallback.style.display =
                    'flex';
            }
        }


        /* =================================================
           MOSTRAR PERSONA SELECCIONADA
        ================================================= */

        contenedorSeleccionado.hidden =
            false;


        cerrarResultados();


        /* =================================================
           NOTIFICAR CAMBIO
        ================================================= */

        inputInspector.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles:
                        true,
                }
            )
        );
    }


    /* =====================================================
   RENDERIZAR RESULTADOS
===================================================== */

    function renderizarResultados(
        personal
    ) {

        resultados.innerHTML =
            '';


        if (
            !Array.isArray(
                personal
            )
            || personal.length === 0
        ) {

            resultados.innerHTML = `
            <div class="inspector-resultados__vacio">
                No se encontró personal de Asuntos Internos.
            </div>
        `;


            resultados.hidden =
                false;


            return;
        }


        personal.forEach(
            (persona) => {

                const boton =
                    document.createElement(
                        'button'
                    );


                boton.type =
                    'button';


                boton.className =
                    'inspector-resultados__item';


                const nombrePersona =
                    String(
                        persona.nombre
                        || ''
                    ).trim();


                const nominaPersona =
                    String(
                        persona.nomina
                        || ''
                    ).trim();


                const turnoPersona =
                    String(
                        persona.turno
                        || ''
                    ).trim();


                const fotoPersona =
                    String(
                        persona.foto
                        || ''
                    ).trim();


                const letraApellido =
                    obtenerIniciales(
                        nombrePersona
                    );


                /* =================================================
                   AVATAR
                ================================================= */

                const avatar =
                    document.createElement(
                        'span'
                    );


                avatar.className =
                    'inspector-resultados__avatar';


                const imagen =
                    document.createElement(
                        'img'
                    );


                const fallback =
                    document.createElement(
                        'span'
                    );


                fallback.textContent =
                    letraApellido;


                imagen.alt =
                    nombrePersona;


                imagen.hidden =
                    true;

                imagen.style.display =
                    'none';


                fallback.hidden =
                    true;

                fallback.style.display =
                    'none';


                /* =================================================
                   FOTO DISPONIBLE
                ================================================= */

                if (
                    fotoPersona !== ''
                ) {

                    imagen.onerror =
                        () => {

                            imagen.hidden =
                                true;

                            imagen.style.display =
                                'none';


                            fallback.hidden =
                                false;

                            fallback.style.display =
                                'flex';
                        };


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


                    /*
                     * src al final.
                     */

                    imagen.src =
                        fotoPersona;

                } else {

                    fallback.hidden =
                        false;

                    fallback.style.display =
                        'flex';
                }


                avatar.appendChild(
                    imagen
                );


                avatar.appendChild(
                    fallback
                );


                /* =================================================
                   DATOS
                ================================================= */

                const contenedorDatos =
                    document.createElement(
                        'span'
                    );


                contenedorDatos.className =
                    'inspector-resultados__datos';


                const nombreElemento =
                    document.createElement(
                        'strong'
                    );


                nombreElemento.textContent =
                    nombrePersona;


                const detalleElemento =
                    document.createElement(
                        'small'
                    );


                let textoDetalle =
                    nominaPersona !== ''
                        ? `Nómina: ${nominaPersona}`
                        : 'Nómina no disponible';


                if (
                    turnoPersona !== ''
                ) {

                    textoDetalle +=
                        ` · ${turnoPersona}`;
                }


                detalleElemento.textContent =
                    textoDetalle;


                contenedorDatos.appendChild(
                    nombreElemento
                );


                contenedorDatos.appendChild(
                    detalleElemento
                );


                /* =================================================
                   ARMAR RESULTADO
                ================================================= */

                boton.appendChild(
                    avatar
                );


                boton.appendChild(
                    contenedorDatos
                );


                /* =================================================
                   SELECCIONAR
                ================================================= */

                boton.addEventListener(
                    'click',
                    () => {

                        seleccionarInspector(
                            persona
                        );
                    }
                );


                resultados.appendChild(
                    boton
                );
            }
        );


        resultados.hidden =
            false;
    }

    /* =====================================================
       CONSULTAR PERSONAL
    ===================================================== */

    async function buscarInspector(
        termino
    ) {

        const busqueda =
            String(
                termino
                || ''
            ).trim();


        if (
            busqueda === ''
        ) {

            cerrarResultados();

            return;
        }


        /* =================================================
           CANCELAR CONSULTA ANTERIOR
        ================================================= */

        if (
            controladorBusqueda
        ) {

            controladorBusqueda.abort();
        }


        controladorBusqueda =
            new AbortController();


        try {

            const url =
                new URL(
                    'DataCore/public/asuntos-internos/reportes/personal/asuntos-internos/buscar',
                    `${window.location.origin}/`
                );


            url.searchParams.set(
                'q',
                busqueda
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


            if (
                !respuesta.ok
                || resultado?.success !== true
            ) {

                throw new Error(
                    resultado?.message
                    || 'No fue posible consultar el personal.'
                );
            }


            renderizarResultados(
                resultado.personal
                || []
            );

        } catch (error) {

            if (
                error.name ===
                'AbortError'
            ) {

                return;
            }


            console.error(
                'Error buscando inspector:',
                error
            );


            resultados.innerHTML = `
                <div class="inspector-resultados__vacio">
                    No fue posible consultar el personal.
                </div>
            `;


            resultados.hidden =
                false;
        }
    }


    /* =====================================================
       ESCRIBIR EN BUSCADOR
    ===================================================== */

    buscador.addEventListener(
        'input',
        () => {

            /*
             * Si el usuario modifica manualmente el texto
             * después de haber seleccionado una persona,
             * invalidamos la selección anterior.
             */

            const valorSeleccionado =
                String(
                    inputInspector.value
                    || ''
                ).trim();


            const valorBuscador =
                String(
                    buscador.value
                    || ''
                ).trim();


            if (
                valorSeleccionado !== ''
                && valorBuscador
                !== valorSeleccionado
            ) {

                inputInspector.value =
                    '';


                if (
                    inputPlantillaId
                ) {

                    inputPlantillaId.value =
                        '';
                }


                contenedorSeleccionado.hidden =
                    true;
            }


            window.clearTimeout(
                temporizadorBusqueda
            );


            if (
                valorBuscador === ''
            ) {

                cerrarResultados();

                return;
            }


            temporizadorBusqueda =
                window.setTimeout(
                    () => {

                        buscarInspector(
                            valorBuscador
                        );

                    },
                    250
                );
        }
    );


    /* =====================================================
       QUITAR INSPECTOR
    ===================================================== */

    if (
        botonQuitar
    ) {

        botonQuitar.addEventListener(
            'click',
            () => {

                limpiarInspector();
            }
        );
    }


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
}

/* =========================================================
   INICIALIZAR INVESTIGADOR
========================================================= */

function inicializarInvestigador() {

    const buscador =
        document.querySelector(
            '#investigador-busqueda'
        );


    const resultados =
        document.querySelector(
            '#investigador-resultados'
        );


    const inputInvestigador =
        document.querySelector(
            '#investigador'
        );


    const inputPlantillaId =
        document.querySelector(
            '#investigador-plantilla-id'
        );


    const inputTipo =
        document.querySelector(
            '#investigador-tipo'
        );


    const contenedorSeleccionado =
        document.querySelector(
            '#investigador-seleccionado'
        );


    const foto =
        document.querySelector(
            '#investigador-foto'
        );


    const fotoFallback =
        document.querySelector(
            '#investigador-foto-fallback'
        );


    const nombre =
        document.querySelector(
            '#investigador-nombre'
        );


    const nomina =
        document.querySelector(
            '#investigador-nomina'
        );


    const detalle =
        document.querySelector(
            '#investigador-detalle'
        );


    const botonQuitar =
        document.querySelector(
            '#btn-quitar-investigador'
        );


    const contenedorOtro =
        document.querySelector(
            '#investigador-otro-contenedor'
        );


    const inputOtro =
        document.querySelector(
            '#investigador-otro-nombre'
        );


    if (
        !buscador
        || !resultados
        || !inputInvestigador
        || !contenedorSeleccionado
        || !contenedorOtro
        || !inputOtro
    ) {
        return;
    }


    let temporizadorBusqueda =
        null;


    let controladorBusqueda =
        null;


    /* =====================================================
       OBTENER LETRA DEL PRIMER APELLIDO
    ===================================================== */

    function obtenerLetraApellido(
        valor
    ) {

        const texto =
            String(
                valor
                || ''
            )
                .trim();


        if (
            texto === ''
        ) {

            return '—';
        }


        const partes =
            texto
                .split(/\s+/)
                .filter(Boolean);


        if (
            partes.length === 0
        ) {

            return '—';
        }


        return partes[0]
            .charAt(0)
            .toUpperCase();
    }


    /* =====================================================
       CERRAR RESULTADOS
    ===================================================== */

    function cerrarResultados() {

        resultados.hidden =
            true;


        resultados.innerHTML =
            '';
    }


    /* =====================================================
       LIMPIAR FOTO
    ===================================================== */

    function limpiarFoto() {

        if (
            foto
        ) {

            foto.onerror =
                null;


            foto.onload =
                null;


            foto.removeAttribute(
                'src'
            );


            foto.hidden =
                true;


            foto.style.display =
                'none';
        }


        if (
            fotoFallback
        ) {

            fotoFallback.textContent =
                '—';


            fotoFallback.hidden =
                false;


            fotoFallback.style.display =
                'flex';
        }
    }


    /* =====================================================
       LIMPIAR INVESTIGADOR
    ===================================================== */

    function limpiarInvestigador(
        enfocar = true
    ) {

        inputInvestigador.value =
            '';


        if (
            inputPlantillaId
        ) {

            inputPlantillaId.value =
                '';
        }


        if (
            inputTipo
        ) {

            inputTipo.value =
                '';
        }


        buscador.value =
            '';


        contenedorSeleccionado.hidden =
            true;


        contenedorOtro.hidden =
            true;


        inputOtro.value =
            '';


        inputOtro.disabled =
            true;


        inputOtro.required =
            false;


        limpiarFoto();


        if (
            nombre
        ) {

            nombre.textContent =
                '—';
        }


        if (
            nomina
        ) {

            nomina.textContent =
                '—';
        }


        if (
            detalle
        ) {

            detalle.textContent =
                '—';
        }


        cerrarResultados();


        if (
            enfocar
        ) {

            buscador.focus();
        }
    }


    /* =====================================================
       SELECCIONAR PERSONA
    ===================================================== */

    function seleccionarInvestigador(
        persona
    ) {

        if (
            !persona
        ) {
            return;
        }


        const id =
            Number(
                persona.id
                || 0
            );


        const nombrePersona =
            String(
                persona.nombre
                || ''
            ).trim();


        const nominaPersona =
            String(
                persona.nomina
                || ''
            ).trim();


        const areaPersona =
            String(
                persona.area
                || ''
            ).trim();


        const turnoPersona =
            String(
                persona.turno
                || ''
            ).trim();


        const fotoPersona =
            String(
                persona.foto
                || ''
            ).trim();


        if (
            id <= 0
            || nombrePersona === ''
        ) {
            return;
        }


        /* =================================================
           VALORES REALES
        ================================================= */

        inputInvestigador.value =
            nombrePersona;


        if (
            inputPlantillaId
        ) {

            inputPlantillaId.value =
                String(
                    id
                );
        }


        if (
            inputTipo
        ) {

            inputTipo.value =
                'PERSONAL';
        }


        /* =================================================
           OCULTAR OTRO
        ================================================= */

        contenedorOtro.hidden =
            true;


        inputOtro.value =
            '';


        inputOtro.disabled =
            true;


        inputOtro.required =
            false;


        /* =================================================
           BUSCADOR
        ================================================= */

        buscador.value =
            nombrePersona;


        /* =================================================
           DATOS VISUALES
        ================================================= */

        if (
            nombre
        ) {

            nombre.textContent =
                nombrePersona;
        }


        if (
            nomina
        ) {

            nomina.textContent =
                nominaPersona !== ''
                    ? `Nómina: ${nominaPersona}`
                    : 'Nómina no disponible';
        }


        if (
            detalle
        ) {

            const datos =
                [];


            if (
                areaPersona !== ''
            ) {

                datos.push(
                    areaPersona
                );
            }


            if (
                turnoPersona !== ''
            ) {

                datos.push(
                    `Turno: ${turnoPersona}`
                );
            }


            detalle.textContent =
                datos.length > 0
                    ? datos.join(' · ')
                    : 'Personal de Asuntos Internos';
        }


        /* =================================================
           FOTO / FALLBACK
        ================================================= */

        if (
            foto
            && fotoFallback
        ) {

            const letra =
                obtenerLetraApellido(
                    nombrePersona
                );


            foto.hidden =
                true;


            foto.style.display =
                'none';


            fotoFallback.hidden =
                true;


            fotoFallback.style.display =
                'none';


            foto.onerror =
                null;


            foto.onload =
                null;


            foto.removeAttribute(
                'src'
            );


            if (
                fotoPersona !== ''
            ) {

                foto.onerror =
                    () => {

                        foto.hidden =
                            true;


                        foto.style.display =
                            'none';


                        fotoFallback.textContent =
                            letra;


                        fotoFallback.hidden =
                            false;


                        fotoFallback.style.display =
                            'flex';
                    };


                foto.onload =
                    () => {

                        foto.hidden =
                            false;


                        foto.style.display =
                            'block';


                        fotoFallback.hidden =
                            true;


                        fotoFallback.style.display =
                            'none';
                    };


                foto.alt =
                    nombrePersona;


                foto.src =
                    fotoPersona;

            } else {

                fotoFallback.textContent =
                    letra;


                fotoFallback.hidden =
                    false;


                fotoFallback.style.display =
                    'flex';
            }
        }


        contenedorSeleccionado.hidden =
            false;


        cerrarResultados();


        inputInvestigador.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles:
                        true,
                }
            )
        );
    }


    /* =====================================================
       SELECCIONAR OTRO
    ===================================================== */

    function seleccionarOtro() {

        /* =================================================
           LIMPIAR PERSONA
        ================================================= */

        inputInvestigador.value =
            '';


        if (
            inputPlantillaId
        ) {

            inputPlantillaId.value =
                '';
        }


        if (
            inputTipo
        ) {

            inputTipo.value =
                'OTRO';
        }


        contenedorSeleccionado.hidden =
            true;


        limpiarFoto();


        /* =================================================
           BUSCADOR
        ================================================= */

        buscador.value =
            'Otro';


        /* =================================================
           MOSTRAR CAMPO MANUAL
        ================================================= */

        contenedorOtro.hidden =
            false;


        inputOtro.disabled =
            false;


        inputOtro.required =
            true;


        inputOtro.value =
            '';


        cerrarResultados();


        inputOtro.focus();
    }


    /* =====================================================
       CREAR AVATAR DE PERSONA
    ===================================================== */

    function crearAvatar(
        persona
    ) {

        const nombrePersona =
            String(
                persona.nombre
                || ''
            ).trim();


        const fotoPersona =
            String(
                persona.foto
                || ''
            ).trim();


        const letra =
            obtenerLetraApellido(
                nombrePersona
            );


        const avatar =
            document.createElement(
                'span'
            );


        avatar.className =
            'investigador-resultados__avatar';


        const imagen =
            document.createElement(
                'img'
            );


        const fallback =
            document.createElement(
                'span'
            );


        imagen.alt =
            nombrePersona;


        imagen.hidden =
            true;


        imagen.style.display =
            'none';


        fallback.textContent =
            letra;


        fallback.hidden =
            true;


        fallback.style.display =
            'none';


        if (
            fotoPersona !== ''
        ) {

            imagen.onerror =
                () => {

                    imagen.hidden =
                        true;


                    imagen.style.display =
                        'none';


                    fallback.hidden =
                        false;


                    fallback.style.display =
                        'flex';
                };


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


            imagen.src =
                fotoPersona;

        } else {

            fallback.hidden =
                false;


            fallback.style.display =
                'flex';
        }


        avatar.appendChild(
            imagen
        );


        avatar.appendChild(
            fallback
        );


        return avatar;
    }


    /* =====================================================
       AGREGAR OPCIÓN OTRO
    ===================================================== */

    function agregarOpcionOtro() {

        const boton =
            document.createElement(
                'button'
            );


        boton.type =
            'button';


        boton.className =
            'investigador-resultados__item investigador-resultados__item--otro';


        const avatar =
            document.createElement(
                'span'
            );


        avatar.className =
            'investigador-resultados__avatar investigador-resultados__avatar--otro';


        avatar.textContent =
            '+';


        const datos =
            document.createElement(
                'span'
            );


        datos.className =
            'investigador-resultados__datos';


        const titulo =
            document.createElement(
                'strong'
            );


        titulo.textContent =
            'Otro';


        const ayuda =
            document.createElement(
                'small'
            );


        ayuda.textContent =
            'Capturar manualmente el nombre del investigador';


        datos.appendChild(
            titulo
        );


        datos.appendChild(
            ayuda
        );


        boton.appendChild(
            avatar
        );


        boton.appendChild(
            datos
        );


        boton.addEventListener(
            'click',
            () => {

                seleccionarOtro();
            }
        );


        resultados.appendChild(
            boton
        );
    }


    /* =====================================================
       RENDERIZAR RESULTADOS
    ===================================================== */

    function renderizarResultados(
        personal
    ) {

        resultados.innerHTML =
            '';


        if (
            Array.isArray(
                personal
            )
        ) {

            personal.forEach(
                (persona) => {

                    const boton =
                        document.createElement(
                            'button'
                        );


                    boton.type =
                        'button';


                    boton.className =
                        'investigador-resultados__item';


                    const nombrePersona =
                        String(
                            persona.nombre
                            || ''
                        ).trim();


                    const nominaPersona =
                        String(
                            persona.nomina
                            || ''
                        ).trim();


                    const turnoPersona =
                        String(
                            persona.turno
                            || ''
                        ).trim();


                    /* =========================================
                       AVATAR
                    ========================================== */

                    boton.appendChild(
                        crearAvatar(
                            persona
                        )
                    );


                    /* =========================================
                       DATOS
                    ========================================== */

                    const datos =
                        document.createElement(
                            'span'
                        );


                    datos.className =
                        'investigador-resultados__datos';


                    const titulo =
                        document.createElement(
                            'strong'
                        );


                    titulo.textContent =
                        nombrePersona;


                    const ayuda =
                        document.createElement(
                            'small'
                        );


                    let textoAyuda =
                        nominaPersona !== ''
                            ? `Nómina: ${nominaPersona}`
                            : 'Nómina no disponible';


                    if (
                        turnoPersona !== ''
                    ) {

                        textoAyuda +=
                            ` · ${turnoPersona}`;
                    }


                    ayuda.textContent =
                        textoAyuda;


                    datos.appendChild(
                        titulo
                    );


                    datos.appendChild(
                        ayuda
                    );


                    boton.appendChild(
                        datos
                    );


                    boton.addEventListener(
                        'click',
                        () => {

                            seleccionarInvestigador(
                                persona
                            );
                        }
                    );


                    resultados.appendChild(
                        boton
                    );
                }
            );
        }


        /*
         * "Otro" siempre aparece como última opción.
         */

        agregarOpcionOtro();


        resultados.hidden =
            false;
    }


    /* =====================================================
       BUSCAR INVESTIGADOR
    ===================================================== */

    async function buscarInvestigador(
        termino
    ) {

        const busqueda =
            String(
                termino
                || ''
            ).trim();


        /*
         * Con el campo vacío mostramos únicamente Otro.
         */

        if (
            busqueda === ''
        ) {

            resultados.innerHTML =
                '';


            agregarOpcionOtro();


            resultados.hidden =
                false;


            return;
        }


        if (
            controladorBusqueda
        ) {

            controladorBusqueda.abort();
        }


        controladorBusqueda =
            new AbortController();


        try {

            const url =
                new URL(
                    'DataCore/public/asuntos-internos/reportes/personal/asuntos-internos/buscar',
                    `${window.location.origin}/`
                );


            url.searchParams.set(
                'q',
                busqueda
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


            if (
                !respuesta.ok
                || resultado?.success !== true
            ) {

                throw new Error(
                    resultado?.message
                    || 'No fue posible consultar el personal.'
                );
            }


            renderizarResultados(
                resultado.personal
                || []
            );

        } catch (error) {

            if (
                error.name
                === 'AbortError'
            ) {

                return;
            }


            console.error(
                'Error buscando investigador:',
                error
            );


            /*
             * Aunque falle la consulta, permitimos Otro.
             */

            resultados.innerHTML =
                '';


            const mensaje =
                document.createElement(
                    'div'
                );


            mensaje.className =
                'investigador-resultados__vacio';


            mensaje.textContent =
                'No fue posible consultar el personal.';


            resultados.appendChild(
                mensaje
            );


            agregarOpcionOtro();


            resultados.hidden =
                false;
        }
    }


    /* =====================================================
       ESCRIBIR EN BUSCADOR
    ===================================================== */

    buscador.addEventListener(
        'input',
        () => {

            const valorBuscador =
                String(
                    buscador.value
                    || ''
                ).trim();


            const tipoActual =
                String(
                    inputTipo?.value
                    || ''
                ).trim();


            /* =================================================
               INVALIDAR PERSONA SELECCIONADA
            ================================================= */

            if (
                tipoActual === 'PERSONAL'
                && valorBuscador
                !== String(
                    inputInvestigador.value
                    || ''
                ).trim()
            ) {

                inputInvestigador.value =
                    '';


                if (
                    inputPlantillaId
                ) {

                    inputPlantillaId.value =
                        '';
                }


                if (
                    inputTipo
                ) {

                    inputTipo.value =
                        '';
                }


                contenedorSeleccionado.hidden =
                    true;


                limpiarFoto();
            }


            /* =================================================
               SALIR DE OTRO SI MODIFICA EL BUSCADOR
            ================================================= */

            if (
                tipoActual === 'OTRO'
                && valorBuscador !== 'Otro'
            ) {

                inputInvestigador.value =
                    '';


                if (
                    inputTipo
                ) {

                    inputTipo.value =
                        '';
                }


                contenedorOtro.hidden =
                    true;


                inputOtro.value =
                    '';


                inputOtro.disabled =
                    true;


                inputOtro.required =
                    false;
            }


            window.clearTimeout(
                temporizadorBusqueda
            );


            temporizadorBusqueda =
                window.setTimeout(
                    () => {

                        buscarInvestigador(
                            valorBuscador
                        );

                    },
                    250
                );
        }
    );


    /* =====================================================
       FOCO EN BUSCADOR
    ===================================================== */

    buscador.addEventListener(
        'focus',
        () => {

            const valor =
                String(
                    buscador.value
                    || ''
                ).trim();


            /*
             * Si todavía no hay selección,
             * mostramos el catálogo.
             */

            if (
                String(
                    inputTipo?.value
                    || ''
                ).trim() === ''
            ) {

                buscarInvestigador(
                    valor
                );
            }
        }
    );


    /* =====================================================
       NOMBRE MANUAL DE OTRO
    ===================================================== */

    inputOtro.addEventListener(
        'input',
        () => {

            const valor =
                String(
                    inputOtro.value
                    || ''
                ).trim();


            /*
             * Este será el valor final enviado al backend.
             */

            inputInvestigador.value =
                valor;


            inputInvestigador.dispatchEvent(
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


    /* =====================================================
       QUITAR INVESTIGADOR
    ===================================================== */

    if (
        botonQuitar
    ) {

        botonQuitar.addEventListener(
            'click',
            () => {

                limpiarInvestigador();
            }
        );
    }


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
                || contenedorOtro.contains(
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
}