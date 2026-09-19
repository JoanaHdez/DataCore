import {
    asignarValorEditar,
} from './utilidades.js';

/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   CLASIFICACIÓN
========================================================= */


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarClasificacion(
    modal
) {

    if (!modal) {
        return;
    }


    /*
     * Evitamos registrar los eventos más de una vez.
     */
    if (
        modal.dataset.editarClasificacionInicializada
        === '1'
    ) {
        return;
    }


    modal.dataset.editarClasificacionInicializada =
        '1';


    const selector =
        modal.querySelector(
            '#editar-clasificacion-select'
        );


    const textoSelector =
        modal.querySelector(
            '#editar-clasificacion-select-texto'
        );


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    const resultados =
        modal.querySelector(
            '#editar-clasificacion-resultados'
        );


    if (
        !selector
        || !textoSelector
        || !inputClasificacion
        || !resultados
    ) {
        return;
    }

    inicializarInvestigadorEditar(
        modal
    );

    /* =====================================================
       ABRIR / CERRAR SELECTOR
    ===================================================== */

    selector.addEventListener(
        'click',
        () => {

            if (selector.disabled) {
                return;
            }


            const estaAbierto =
                !resultados.hidden;


            if (estaAbierto) {

                cerrarClasificacionEditar(
                    modal
                );

                return;
            }


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
    );


    /* =====================================================
       SELECCIONAR OPCIÓN
    ===================================================== */

    resultados.addEventListener(
        'click',
        (evento) => {

            const opcion =
                evento.target.closest(
                    '[data-editar-clasificacion-opcion]'
                );


            if (!opcion) {
                return;
            }


            const nombre =
                String(
                    opcion.dataset.clasificacionNombre
                    || ''
                ).trim();


            if (nombre === '') {
                return;
            }


            seleccionarClasificacionEditar(
                modal,
                nombre
            );
        }
    );


    /* =====================================================
       CERRAR AL HACER CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            const objetivo =
                evento.target;


            if (
                selector.contains(
                    objetivo
                )
                || resultados.contains(
                    objetivo
                )
            ) {
                return;
            }


            cerrarClasificacionEditar(
                modal
            );
        }
    );


    /* =====================================================
       CERRAR CON ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key !== 'Escape'
            ) {
                return;
            }


            cerrarClasificacionEditar(
                modal
            );
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarClasificacionEditar(
        modal
    );
}


/* =========================================================
   SELECCIONAR CLASIFICACIÓN
========================================================= */

export function seleccionarClasificacionEditar(
    modal,
    valor
) {

    if (!modal) {
        return;
    }


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    const textoSelector =
        modal.querySelector(
            '#editar-clasificacion-select-texto'
        );


    const nombre =
        String(
            valor
            || ''
        ).trim();


    if (nombre === '') {
        return;
    }


    if (inputClasificacion) {

        inputClasificacion.value =
            nombre;
    }


    if (textoSelector) {

        textoSelector.textContent =
            nombre;
    }


    cerrarClasificacionEditar(
        modal
    );
}


/* =========================================================
   ACTUALIZAR INTERFAZ
========================================================= */

export function actualizarClasificacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    const textoSelector =
        modal.querySelector(
            '#editar-clasificacion-select-texto'
        );


    if (
        !inputClasificacion
        || !textoSelector
    ) {
        return;
    }


    const valor =
        String(
            inputClasificacion.value
            || ''
        ).trim();


    textoSelector.textContent =
        valor !== ''
            ? valor
            : 'Selecciona una clasificación';
}


/* =========================================================
   CARGAR CLASIFICACIÓN EXISTENTE
========================================================= */

export function cargarClasificacionEditar(
    modal,
    reporte
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    /* =====================================================
       CLASIFICACIÓN
    ===================================================== */

    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    if (
        inputClasificacion
    ) {

        inputClasificacion.value =
            String(
                reporte.clasificacion
                || ''
            ).trim();
    }


    actualizarClasificacionEditar(
        modal
    );


    /* =====================================================
       INSPECTOR
    ===================================================== */

    const inspectorGuardado =
        String(
            reporte.inspector
            || ''
        ).trim();


    const inputInspector =
        modal.querySelector(
            '#editar-inspector'
        );


    const buscadorInspector =
        modal.querySelector(
            '#editar-inspector-busqueda'
        );


    const inputPlantillaId =
        modal.querySelector(
            '#editar-inspector-plantilla-id'
        );


    const inputPerscod =
        modal.querySelector(
            '#editar-inspector-perscod'
        );


    const seleccionado =
        modal.querySelector(
            '#editar-inspector-seleccionado'
        );


    const foto =
        modal.querySelector(
            '#editar-inspector-foto'
        );


    const fotoFallback =
        modal.querySelector(
            '#editar-inspector-foto-fallback'
        );


    const nombreInspector =
        modal.querySelector(
            '#editar-inspector-nombre'
        );


    const nominaInspector =
        modal.querySelector(
            '#editar-inspector-nomina'
        );


    const areaInspector =
        modal.querySelector(
            '#editar-inspector-area'
        );


    /* =====================================================
       LETRA DEL PRIMER APELLIDO
    ===================================================== */

    function obtenerLetraApellido(
        valor
    ) {

        const texto =
            String(
                valor
                || ''
            ).trim();


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
       LIMPIAR FOTO
    ===================================================== */

    function limpiarFotoInspector() {

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
       MOSTRAR TARJETA DEL INSPECTOR
    ===================================================== */

    function mostrarInspectorSeleccionado(
        persona = null
    ) {

        if (
            inspectorGuardado === ''
        ) {

            if (
                seleccionado
            ) {

                seleccionado.hidden =
                    true;
            }


            return;
        }


        const nombre =
            String(
                persona?.nombre
                || inspectorGuardado
            ).trim();


        const nomina =
            String(
                persona?.nomina
                || ''
            ).trim();


        const area =
            String(
                persona?.area
                || ''
            ).trim();


        const turno =
            String(
                persona?.turno
                || ''
            ).trim();


        const fotoPersona =
            String(
                persona?.foto
                || ''
            ).trim();


        /* =================================================
           VALORES REALES
        ================================================= */

        if (
            inputInspector
        ) {

            inputInspector.value =
                nombre;
        }


        if (
            buscadorInspector
        ) {

            buscadorInspector.value =
                nombre;
        }


        if (
            inputPlantillaId
        ) {

            inputPlantillaId.value =
                persona?.id
                    ? String(
                        persona.id
                    )
                    : '';
        }


        if (
            inputPerscod
        ) {

            inputPerscod.value =
                String(
                    persona?.perscod
                    || ''
                ).trim();
        }


        /* =================================================
           NOMBRE
        ================================================= */

        if (
            nombreInspector
        ) {

            nombreInspector.textContent =
                nombre;
        }


        /* =================================================
           NÓMINA
        ================================================= */

        if (
            nominaInspector
        ) {

            nominaInspector.textContent =
                nomina !== ''
                    ? `Nómina: ${nomina}`
                    : 'Nómina no disponible';
        }


        /* =================================================
           ÁREA / TURNO
        ================================================= */

        if (
            areaInspector
        ) {

            const datos =
                [];


            if (
                area !== ''
            ) {

                datos.push(
                    area
                );
            }


            if (
                turno !== ''
            ) {

                datos.push(
                    `Turno: ${turno}`
                );
            }


            areaInspector.textContent =
                datos.length > 0
                    ? datos.join(' · ')
                    : 'Inspector registrado';
        }


        /* =================================================
           FOTO / FALLBACK
        ================================================= */

        limpiarFotoInspector();


        if (
            foto
            && fotoFallback
        ) {

            const letra =
                obtenerLetraApellido(
                    nombre
                );


            if (
                fotoPersona !== ''
            ) {

                /*
                 * IMPORTANTE:
                 * eventos antes del src.
                 */

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
                    nombre;


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


        if (
            seleccionado
        ) {

            seleccionado.hidden =
                false;
        }
    }


    /* =====================================================
       CARGA INICIAL DEL INSPECTOR

       Primero mostramos inmediatamente el nombre guardado.
    ===================================================== */

    if (
        inspectorGuardado !== ''
    ) {

        mostrarInspectorSeleccionado();


        /* =================================================
           BUSCAR DATOS DE PLANTILLA
        ================================================= */

        const cargarDatosInspector =
            async () => {

                try {

                    const url =
                        new URL(
                            'DataCore/public/asuntos-internos/reportes/personal/asuntos-internos/buscar',
                            `${window.location.origin}/`
                        );


                    url.searchParams.set(
                        'q',
                        inspectorGuardado
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


                    const resultado =
                        await respuesta.json();


                    if (
                        !respuesta.ok
                        || resultado?.success !== true
                    ) {

                        return;
                    }


                    const personal =
                        Array.isArray(
                            resultado.personal
                        )
                            ? resultado.personal
                            : [];


                    /*
                     * Buscamos coincidencia exacta porque
                     * el reporte ya tiene guardado el nombre.
                     */

                    const inspectorEncontrado =
                        personal.find(
                            (persona) => {

                                const nombrePersona =
                                    String(
                                        persona?.nombre
                                        || ''
                                    )
                                        .trim()
                                        .toUpperCase();


                                return nombrePersona
                                    === inspectorGuardado
                                        .toUpperCase();
                            }
                        );


                    if (
                        !inspectorEncontrado
                    ) {

                        return;
                    }


                    /*
                     * Antes de actualizar verificamos que
                     * el usuario no haya cambiado manualmente
                     * el inspector mientras respondía fetch.
                     */

                    const inspectorActual =
                        String(
                            inputInspector?.value
                            || ''
                        ).trim();


                    if (
                        inspectorActual.toUpperCase()
                        !== inspectorGuardado.toUpperCase()
                    ) {

                        return;
                    }


                    mostrarInspectorSeleccionado(
                        inspectorEncontrado
                    );

                } catch (error) {

                    /*
                     * Si la consulta falla, no rompemos Editar.
                     * La tarjeta ya muestra al menos el nombre
                     * guardado en el reporte.
                     */

                    console.error(
                        'Error cargando datos del inspector en edición:',
                        error
                    );
                }
            };


        cargarDatosInspector();

    } else {

        if (
            inputInspector
        ) {

            inputInspector.value =
                '';
        }


        if (
            buscadorInspector
        ) {

            buscadorInspector.value =
                '';
        }


        if (
            inputPlantillaId
        ) {

            inputPlantillaId.value =
                '';
        }


        if (
            inputPerscod
        ) {

            inputPerscod.value =
                '';
        }


        if (
            seleccionado
        ) {

            seleccionado.hidden =
                true;
        }


        limpiarFotoInspector();
    }


    /* =====================================================
   INVESTIGADOR
===================================================== */

    const investigadorGuardado =
        String(
            reporte.investigador
            || ''
        ).trim();


    const inputInvestigador =
        modal.querySelector(
            '#editar-investigador'
        );


    const buscadorInvestigador =
        modal.querySelector(
            '#editar-investigador-busqueda'
        );


    const investigadorPlantillaId =
        modal.querySelector(
            '#editar-investigador-plantilla-id'
        );


    const investigadorTipo =
        modal.querySelector(
            '#editar-investigador-tipo'
        );


    const investigadorSeleccionado =
        modal.querySelector(
            '#editar-investigador-seleccionado'
        );


    const investigadorFoto =
        modal.querySelector(
            '#editar-investigador-foto'
        );


    const investigadorFotoFallback =
        modal.querySelector(
            '#editar-investigador-foto-fallback'
        );


    const investigadorNombre =
        modal.querySelector(
            '#editar-investigador-nombre'
        );


    const investigadorNomina =
        modal.querySelector(
            '#editar-investigador-nomina'
        );


    const investigadorDetalle =
        modal.querySelector(
            '#editar-investigador-detalle'
        );


    const investigadorOtroContenedor =
        modal.querySelector(
            '#editar-investigador-otro-contenedor'
        );


    const investigadorOtroNombre =
        modal.querySelector(
            '#editar-investigador-otro-nombre'
        );


    /* =====================================================
       LETRA DEL PRIMER APELLIDO
    ===================================================== */

    function obtenerLetraInvestigador(
        valor
    ) {

        const texto =
            String(
                valor
                || ''
            ).trim();


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
       LIMPIAR FOTO DEL INVESTIGADOR
    ===================================================== */

    function limpiarFotoInvestigador() {

        if (
            investigadorFoto
        ) {

            investigadorFoto.onerror =
                null;


            investigadorFoto.onload =
                null;


            investigadorFoto.removeAttribute(
                'src'
            );


            investigadorFoto.hidden =
                true;


            investigadorFoto.style.display =
                'none';
        }


        if (
            investigadorFotoFallback
        ) {

            investigadorFotoFallback.textContent =
                '—';


            investigadorFotoFallback.hidden =
                false;


            investigadorFotoFallback.style.display =
                'flex';
        }
    }


    /* =====================================================
       MOSTRAR INVESTIGADOR DE PLANTILLA
    ===================================================== */

    function mostrarInvestigadorPersonal(
        persona
    ) {

        if (
            !persona
        ) {
            return;
        }


        const nombre =
            String(
                persona.nombre
                || investigadorGuardado
            ).trim();


        const nomina =
            String(
                persona.nomina
                || ''
            ).trim();


        const area =
            String(
                persona.area
                || ''
            ).trim();


        const turno =
            String(
                persona.turno
                || ''
            ).trim();


        const fotoPersona =
            String(
                persona.foto
                || ''
            ).trim();


        /* =================================================
           VALORES
        ================================================= */

        if (
            inputInvestigador
        ) {

            inputInvestigador.value =
                nombre;
        }


        if (
            buscadorInvestigador
        ) {

            buscadorInvestigador.value =
                nombre;
        }


        if (
            investigadorPlantillaId
        ) {

            investigadorPlantillaId.value =
                persona.id
                    ? String(
                        persona.id
                    )
                    : '';
        }


        if (
            investigadorTipo
        ) {

            investigadorTipo.value =
                'PERSONAL';
        }


        /* =================================================
           OCULTAR OTRO
        ================================================= */

        if (
            investigadorOtroContenedor
        ) {

            investigadorOtroContenedor.hidden =
                true;
        }


        if (
            investigadorOtroNombre
        ) {

            investigadorOtroNombre.value =
                '';


            investigadorOtroNombre.disabled =
                true;


            investigadorOtroNombre.required =
                false;
        }


        /* =================================================
           DATOS VISUALES
        ================================================= */

        if (
            investigadorNombre
        ) {

            investigadorNombre.textContent =
                nombre;
        }


        if (
            investigadorNomina
        ) {

            investigadorNomina.textContent =
                nomina !== ''
                    ? `Nómina: ${nomina}`
                    : 'Nómina no disponible';
        }


        if (
            investigadorDetalle
        ) {

            const datos =
                [];


            if (
                area !== ''
            ) {

                datos.push(
                    area
                );
            }


            if (
                turno !== ''
            ) {

                datos.push(
                    `Turno: ${turno}`
                );
            }


            investigadorDetalle.textContent =
                datos.length > 0
                    ? datos.join(' · ')
                    : 'Personal de Asuntos Internos';
        }


        /* =================================================
           FOTO
        ================================================= */

        limpiarFotoInvestigador();


        if (
            investigadorFoto
            && investigadorFotoFallback
        ) {

            const letra =
                obtenerLetraInvestigador(
                    nombre
                );


            if (
                fotoPersona !== ''
            ) {

                investigadorFoto.onerror =
                    () => {

                        investigadorFoto.hidden =
                            true;


                        investigadorFoto.style.display =
                            'none';


                        investigadorFotoFallback.textContent =
                            letra;


                        investigadorFotoFallback.hidden =
                            false;


                        investigadorFotoFallback.style.display =
                            'flex';
                    };


                investigadorFoto.onload =
                    () => {

                        investigadorFoto.hidden =
                            false;


                        investigadorFoto.style.display =
                            'block';


                        investigadorFotoFallback.hidden =
                            true;


                        investigadorFotoFallback.style.display =
                            'none';
                    };


                investigadorFoto.alt =
                    nombre;


                investigadorFoto.src =
                    fotoPersona;

            } else {

                investigadorFotoFallback.textContent =
                    letra;


                investigadorFotoFallback.hidden =
                    false;


                investigadorFotoFallback.style.display =
                    'flex';
            }
        }


        if (
            investigadorSeleccionado
        ) {

            investigadorSeleccionado.hidden =
                false;
        }
    }


    /* =====================================================
       MOSTRAR INVESTIGADOR "OTRO"
    ===================================================== */

    function mostrarInvestigadorOtro(
        nombreGuardado
    ) {

        const nombre =
            String(
                nombreGuardado
                || ''
            ).trim();


        if (
            inputInvestigador
        ) {

            inputInvestigador.value =
                nombre;
        }


        if (
            buscadorInvestigador
        ) {

            buscadorInvestigador.value =
                'Otro';
        }


        if (
            investigadorPlantillaId
        ) {

            investigadorPlantillaId.value =
                '';
        }


        if (
            investigadorTipo
        ) {

            investigadorTipo.value =
                'OTRO';
        }


        if (
            investigadorSeleccionado
        ) {

            investigadorSeleccionado.hidden =
                true;
        }


        limpiarFotoInvestigador();


        if (
            investigadorOtroContenedor
        ) {

            investigadorOtroContenedor.hidden =
                false;
        }


        if (
            investigadorOtroNombre
        ) {

            investigadorOtroNombre.disabled =
                false;


            investigadorOtroNombre.required =
                true;


            investigadorOtroNombre.value =
                nombre;
        }
    }


    /* =====================================================
       CARGAR INVESTIGADOR GUARDADO
    ===================================================== */

    if (
        investigadorGuardado !== ''
    ) {

        /*
         * Conservamos inmediatamente el valor real.
         */

        if (
            inputInvestigador
        ) {

            inputInvestigador.value =
                investigadorGuardado;
        }


        /*
         * Mientras consulta plantilla, mostramos el nombre.
         */

        if (
            buscadorInvestigador
        ) {

            buscadorInvestigador.value =
                investigadorGuardado;
        }


        const cargarDatosInvestigador =
            async () => {

                try {

                    const url =
                        new URL(
                            'DataCore/public/asuntos-internos/reportes/personal/asuntos-internos/buscar',
                            `${window.location.origin}/`
                        );


                    url.searchParams.set(
                        'q',
                        investigadorGuardado
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


                    const resultado =
                        await respuesta.json();


                    if (
                        !respuesta.ok
                        || resultado?.success !== true
                    ) {

                        mostrarInvestigadorOtro(
                            investigadorGuardado
                        );

                        return;
                    }


                    const personal =
                        Array.isArray(
                            resultado.personal
                        )
                            ? resultado.personal
                            : [];


                    const investigadorEncontrado =
                        personal.find(
                            (persona) => {

                                const nombrePersona =
                                    String(
                                        persona?.nombre
                                        || ''
                                    )
                                        .trim()
                                        .toUpperCase();


                                return nombrePersona
                                    === investigadorGuardado
                                        .toUpperCase();
                            }
                        );


                    /*
                     * SI EXISTE EN PLANTILLA
                     */

                    if (
                        investigadorEncontrado
                    ) {

                        mostrarInvestigadorPersonal(
                            investigadorEncontrado
                        );

                        return;
                    }


                    /*
                     * SI NO EXISTE EN PLANTILLA,
                     * FUE CAPTURADO COMO "OTRO".
                     */

                    mostrarInvestigadorOtro(
                        investigadorGuardado
                    );

                } catch (error) {

                    console.error(
                        'Error cargando investigador en edición:',
                        error
                    );


                    /*
                     * Ante cualquier problema con la consulta,
                     * no perdemos el valor existente.
                     */

                    mostrarInvestigadorOtro(
                        investigadorGuardado
                    );
                }
            };


        cargarDatosInvestigador();

    } else {

        if (
            inputInvestigador
        ) {

            inputInvestigador.value =
                '';
        }


        if (
            buscadorInvestigador
        ) {

            buscadorInvestigador.value =
                '';
        }


        if (
            investigadorPlantillaId
        ) {

            investigadorPlantillaId.value =
                '';
        }


        if (
            investigadorTipo
        ) {

            investigadorTipo.value =
                '';
        }


        if (
            investigadorSeleccionado
        ) {

            investigadorSeleccionado.hidden =
                true;
        }


        if (
            investigadorOtroContenedor
        ) {

            investigadorOtroContenedor.hidden =
                true;
        }


        if (
            investigadorOtroNombre
        ) {

            investigadorOtroNombre.value =
                '';


            investigadorOtroNombre.disabled =
                true;


            investigadorOtroNombre.required =
                false;
        }


        limpiarFotoInvestigador();
    }


    /* =====================================================
       SIN SANCIONES
    ===================================================== */

    const sinSanciones =
        modal.querySelector(
            '#editar-sin-sanciones'
        );


    if (
        sinSanciones
    ) {

        sinSanciones.checked =
            Number(
                reporte.sin_sanciones
                ?? 0
            ) === 1;
    }


    /* =====================================================
       BAJA VOLUNTARIA
    ===================================================== */

    const bajaVoluntaria =
        modal.querySelector(
            '#editar-baja-voluntaria'
        );


    if (
        bajaVoluntaria
    ) {

        bajaVoluntaria.checked =
            Number(
                reporte.baja_voluntaria
                ?? 0
            ) === 1;
    }


    /* =====================================================
       QUIÉN EMITE LA RESOLUCIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-quien-emite-resolucion',
        reporte.quien_emite_resolucion
    );


    /* =====================================================
       RESOLUCIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-resolucion',
        reporte.resolucion
    );
}


/* =========================================================
   LIMPIAR
========================================================= */

export function limpiarClasificacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    if (inputClasificacion) {

        inputClasificacion.value =
            '';
    }


    actualizarClasificacionEditar(
        modal
    );


    cerrarClasificacionEditar(
        modal
    );
}


/* =========================================================
   CERRAR CATÁLOGO
========================================================= */

function cerrarClasificacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const selector =
        modal.querySelector(
            '#editar-clasificacion-select'
        );


    const resultados =
        modal.querySelector(
            '#editar-clasificacion-resultados'
        );


    if (resultados) {

        resultados.hidden =
            true;
    }


    if (selector) {

        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'clasificacion-select--activo'
        );
    }
}

/* =========================================================
   INICIALIZAR INVESTIGADOR - EDITAR
========================================================= */

function inicializarInvestigadorEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const buscador =
        modal.querySelector(
            '#editar-investigador-busqueda'
        );


    const resultados =
        modal.querySelector(
            '#editar-investigador-resultados'
        );


    const inputInvestigador =
        modal.querySelector(
            '#editar-investigador'
        );


    const inputPlantillaId =
        modal.querySelector(
            '#editar-investigador-plantilla-id'
        );


    const inputTipo =
        modal.querySelector(
            '#editar-investigador-tipo'
        );


    const seleccionado =
        modal.querySelector(
            '#editar-investigador-seleccionado'
        );


    const foto =
        modal.querySelector(
            '#editar-investigador-foto'
        );


    const fotoFallback =
        modal.querySelector(
            '#editar-investigador-foto-fallback'
        );


    const nombre =
        modal.querySelector(
            '#editar-investigador-nombre'
        );


    const nomina =
        modal.querySelector(
            '#editar-investigador-nomina'
        );


    const detalle =
        modal.querySelector(
            '#editar-investigador-detalle'
        );


    const botonQuitar =
        modal.querySelector(
            '#btn-editar-quitar-investigador'
        );


    const contenedorOtro =
        modal.querySelector(
            '#editar-investigador-otro-contenedor'
        );


    const inputOtro =
        modal.querySelector(
            '#editar-investigador-otro-nombre'
        );


    if (
        !buscador
        || !resultados
        || !inputInvestigador
        || !seleccionado
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
       LETRA DEL PRIMER APELLIDO
    ===================================================== */

    function obtenerLetraApellido(
        valor
    ) {

        const texto =
            String(
                valor
                || ''
            ).trim();


        if (texto === '') {
            return '—';
        }


        const partes =
            texto
                .split(/\s+/)
                .filter(Boolean);


        if (partes.length === 0) {
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

        if (foto) {

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


        if (fotoFallback) {

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


        if (inputPlantillaId) {

            inputPlantillaId.value =
                '';
        }


        if (inputTipo) {

            inputTipo.value =
                '';
        }


        buscador.value =
            '';


        seleccionado.hidden =
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


        if (enfocar) {

            buscador.focus();
        }
    }


    /* =====================================================
       MOSTRAR PERSONA SELECCIONADA
    ===================================================== */

    function seleccionarPersona(
        persona
    ) {

        if (!persona) {
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
           VALORES
        ================================================= */

        inputInvestigador.value =
            nombrePersona;


        buscador.value =
            nombrePersona;


        if (inputPlantillaId) {

            inputPlantillaId.value =
                String(
                    id
                );
        }


        if (inputTipo) {

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
           DATOS
        ================================================= */

        if (nombre) {

            nombre.textContent =
                nombrePersona;
        }


        if (nomina) {

            nomina.textContent =
                nominaPersona !== ''
                    ? `Nómina: ${nominaPersona}`
                    : 'Nómina no disponible';
        }


        if (detalle) {

            const datos =
                [];


            if (areaPersona !== '') {

                datos.push(
                    areaPersona
                );
            }


            if (turnoPersona !== '') {

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

        limpiarFoto();


        if (
            foto
            && fotoFallback
        ) {

            const letra =
                obtenerLetraApellido(
                    nombrePersona
                );


            if (fotoPersona !== '') {

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


        seleccionado.hidden =
            false;


        cerrarResultados();


        inputInvestigador.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles: true,
                }
            )
        );
    }


    /* =====================================================
       SELECCIONAR OTRO
    ===================================================== */

    function seleccionarOtro() {

        inputInvestigador.value =
            '';


        if (inputPlantillaId) {

            inputPlantillaId.value =
                '';
        }


        if (inputTipo) {

            inputTipo.value =
                'OTRO';
        }


        seleccionado.hidden =
            true;


        limpiarFoto();


        buscador.value =
            'Otro';


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
       CREAR AVATAR
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


        if (fotoPersona !== '') {

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
       OPCIÓN OTRO
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


                    boton.appendChild(
                        crearAvatar(
                            persona
                        )
                    );


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


                    if (turnoPersona !== '') {

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

                            seleccionarPersona(
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


        agregarOpcionOtro();


        resultados.hidden =
            false;
    }


    /* =====================================================
       BUSCAR
    ===================================================== */

    async function buscarInvestigador(
        termino
    ) {

        const busqueda =
            String(
                termino
                || ''
            ).trim();


        if (busqueda === '') {

            resultados.innerHTML =
                '';


            agregarOpcionOtro();


            resultados.hidden =
                false;


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
                error.name === 'AbortError'
            ) {
                return;
            }


            console.error(
                'Error buscando investigador en edición:',
                error
            );


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
       BUSCADOR
    ===================================================== */

    buscador.addEventListener(
        'input',
        () => {

            const valor =
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
               INVALIDAR PERSONAL SELECCIONADO
            ================================================= */

            if (
                tipoActual === 'PERSONAL'
                && valor !== String(
                    inputInvestigador.value
                    || ''
                ).trim()
            ) {

                inputInvestigador.value =
                    '';


                if (inputPlantillaId) {

                    inputPlantillaId.value =
                        '';
                }


                if (inputTipo) {

                    inputTipo.value =
                        '';
                }


                seleccionado.hidden =
                    true;


                limpiarFoto();
            }


            /* =================================================
               SALIR DE OTRO
            ================================================= */

            if (
                tipoActual === 'OTRO'
                && valor !== 'Otro'
            ) {

                inputInvestigador.value =
                    '';


                if (inputTipo) {

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
                            valor
                        );

                    },
                    250
                );
        }
    );


    /* =====================================================
       FOCO
    ===================================================== */

    buscador.addEventListener(
        'focus',
        () => {

            const valor =
                String(
                    buscador.value
                    || ''
                ).trim();


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
       OTRO - NOMBRE MANUAL
    ===================================================== */

    inputOtro.addEventListener(
        'input',
        () => {

            inputInvestigador.value =
                String(
                    inputOtro.value
                    || ''
                ).trim();


            inputInvestigador.dispatchEvent(
                new Event(
                    'change',
                    {
                        bubbles: true,
                    }
                )
            );
        }
    );


    /* =====================================================
       QUITAR
    ===================================================== */

    if (botonQuitar) {

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