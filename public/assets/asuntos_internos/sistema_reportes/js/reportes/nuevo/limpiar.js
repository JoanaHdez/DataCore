document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarLimpiarReporte();

    }
);


/* =========================================================
   INICIALIZAR LIMPIEZA DEL PASO ACTUAL
========================================================= */

function inicializarLimpiarReporte() {

    const formulario =
        document.querySelector(
            '#form-nuevo-reporte'
        );

    const botonLimpiar =
        document.querySelector(
            '#btn-limpiar-reporte'
        );


    if (
        !formulario
        || !botonLimpiar
    ) {
        return;
    }


    botonLimpiar.addEventListener(
        'click',
        () => {

            const pasoActual =
                formulario.querySelector(
                    '.report-step--active'
                );


            if (!pasoActual) {
                return;
            }


            const numeroPaso =
                Number(
                    pasoActual.dataset.step
                    || 0
                );


            limpiarPasoActual(
                pasoActual
            );


            restaurarDatosAutomaticosPaso(
                pasoActual,
                numeroPaso
            );


            restaurarEstadoEspecialPaso(
                formulario,
                pasoActual,
                numeroPaso
            );

        }
    );
}


/* =========================================================
   LIMPIAR ÚNICAMENTE EL PASO ACTUAL
========================================================= */

function limpiarPasoActual(
    paso
) {

    const campos =
        paso.querySelectorAll(
            'input, select, textarea'
        );


    campos.forEach(
        (campo) => {

            /*
             * No modificamos botones.
             */
            if (
                campo.type === 'button'
                || campo.type === 'submit'
                || campo.type === 'reset'
            ) {
                return;
            }


            /*
             * Checkbox y radio.
             */
            if (
                campo.type === 'checkbox'
                || campo.type === 'radio'
            ) {

                campo.checked = false;

                return;
            }


            /*
             * Archivos.
             */
            if (
                campo.type === 'file'
            ) {

                campo.value = '';

                return;
            }


            /*
             * Select.
             */
            if (
                campo.tagName === 'SELECT'
            ) {

                campo.selectedIndex = 0;

                return;
            }


            /*
             * Inputs y textarea.
             */
            campo.value = '';

        }
    );
}


/* =========================================================
   RESTAURAR DATOS AUTOMÁTICOS DEL PASO
========================================================= */

function restaurarDatosAutomaticosPaso(
    paso,
    numeroPaso
) {

    /*
     * PASO 1
     * Prefijo fijo y fecha actual.
     */
    if (numeroPaso === 1) {

        const prefijo =
            paso.querySelector(
                '#prefijo_folio'
            );

        if (prefijo) {
            prefijo.value = 'QJ';
        }


        const fechaRegistro =
            paso.querySelector(
                '#fecha_registro'
            );

        if (fechaRegistro) {

            fechaRegistro.value =
                obtenerFechaActual();

        }

    }

}


/* =========================================================
   RESTAURAR ESTADOS ESPECIALES
========================================================= */

function restaurarEstadoEspecialPaso(
    formulario,
    paso,
    numeroPaso
) {

    /*
     * PASO 3
     * Personal y unidades.
     *
     * La unidad depende del oficial seleccionado,
     * por lo que debe regresar a su estado inicial.
     */
    if (numeroPaso === 3) {

        limpiarDatosUnidad(
            paso
        );

    }

}


/* =========================================================
   LIMPIAR INFORMACIÓN DE UNIDAD
========================================================= */

function limpiarDatosUnidad(
    contenedor
) {

    /*
     * Área obtenida automáticamente
     * del oficial.
     */
    const area =
        contenedor.querySelector(
            '#area'
        );

    if (area) {
        area.value = '';
    }


    /*
     * Unidad
     */
    const unidad =
        contenedor.querySelector(
            '#unidad'
        );

    if (unidad) {

        unidad.innerHTML = `
            <option value="">
                Selecciona primero un oficial
            </option>
        `;

        unidad.value = '';

        unidad.disabled = true;

    }


    /*
     * Información automática
     * de la unidad seleccionada.
     */
    const camposUnidad = [
        '#unidad_marca',
        '#unidad_submarca',
        '#unidad_color',
        '#unidad_estatus',
        '#unidad_servicio_adscripcion',
        '#unidad_tipo_vehiculo',
        '#unidad_origen',
    ];


    camposUnidad.forEach(
        (selector) => {

            const campo =
                contenedor.querySelector(
                    selector
                );

            if (campo) {
                campo.value = '';
            }

        }
    );

}


/* =========================================================
   FECHA ACTUAL
   Formato DD/MM/YYYY
========================================================= */

function obtenerFechaActual() {

    const fecha =
        new Date();


    const dia =
        String(
            fecha.getDate()
        ).padStart(
            2,
            '0'
        );


    const mes =
        String(
            fecha.getMonth() + 1
        ).padStart(
            2,
            '0'
        );


    const anio =
        fecha.getFullYear();


    return (
        `${dia}/${mes}/${anio}`
    );

}