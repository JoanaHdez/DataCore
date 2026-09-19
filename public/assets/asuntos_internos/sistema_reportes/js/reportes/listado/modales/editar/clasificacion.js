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

       Todavía conserva su funcionamiento actual.
       Lo convertiremos al catálogo en el siguiente paso.
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-investigador',
        reporte.investigador
    );


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