document.addEventListener('DOMContentLoaded', () => {
    inicializarLimpiarReporte();
});


function inicializarLimpiarReporte() {

    const formulario =
        document.querySelector(
            '#form-nuevo-reporte'
        );

    const botonLimpiar =
        document.querySelector(
            '#btn-limpiar-reporte'
        );


    if (!formulario || !botonLimpiar) {
        return;
    }


    botonLimpiar.addEventListener('click', () => {

        /*
         * Esperamos a que el reset nativo
         * del formulario termine.
         */
        setTimeout(() => {

            restaurarDatosAutomaticos(
                formulario
            );

            limpiarDatosUnidad(
                formulario
            );

        }, 0);

    });
}


/* =========================================================
   RESTAURAR DATOS AUTOMÁTICOS
========================================================= */

function restaurarDatosAutomaticos(
    formulario
) {

    /*
     * Prefijo fijo
     */
    const prefijo =
        formulario.querySelector(
            '#prefijo_folio'
        );

    if (prefijo) {
        prefijo.value = 'QJ';
    }


    /*
     * Fecha actual
     */
    const fechaRegistro =
        formulario.querySelector(
            '#fecha_registro'
        );

    if (fechaRegistro) {
        fechaRegistro.value =
            obtenerFechaActual();
    }
}


/* =========================================================
   LIMPIAR INFORMACIÓN DE UNIDAD
========================================================= */

function limpiarDatosUnidad(
    formulario
) {

    /*
     * Área obtenida automáticamente
     * del oficial.
     */
    const area =
        formulario.querySelector(
            '#area'
        );

    if (area) {
        area.value = '';
    }


    /*
     * Unidad
     */
    const unidad =
        formulario.querySelector(
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
                formulario.querySelector(
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
        ).padStart(2, '0');


    const mes =
        String(
            fecha.getMonth() + 1
        ).padStart(2, '0');


    const anio =
        fecha.getFullYear();


    return `${dia}/${mes}/${anio}`;
}