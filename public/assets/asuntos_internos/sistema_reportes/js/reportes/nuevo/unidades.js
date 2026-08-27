/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Nuevo reporte - Unidades
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarUnidades();
});


function inicializarUnidades() {

    const inputOficial =
        document.querySelector('#oficial');

    const selectUnidad =
        document.querySelector('#unidad');

    const inputMarca =
        document.querySelector('#unidad_marca');

    const inputSubmarca =
        document.querySelector('#unidad_submarca');

    const inputColor =
        document.querySelector('#unidad_color');

    const inputEstatus =
        document.querySelector('#unidad_estatus');

    const inputServicioAdscripcion =
        document.querySelector(
            '#unidad_servicio_adscripcion'
        );

    const inputTipoVehiculo =
        document.querySelector(
            '#unidad_tipo_vehiculo'
        );

    const inputOrigen =
        document.querySelector('#unidad_origen');


    if (
        !inputOficial
        || !selectUnidad
    ) {
        return;
    }


    /* =====================================================
       DATOS TEMPORALES

       Después serán sustituidos por consultas al backend.
    ===================================================== */

    const oficiales = [
        {
            id: 1,
            nombre: 'Juan Pérez López',
        },
        {
            id: 2,
            nombre: 'María Hernández García',
        },
        {
            id: 3,
            nombre: 'Carlos Ramírez Torres',
        },
    ];


    const unidades = [
        {
            id: 101,
            oficial_id: 1,

            unidad: 'SP-101',

            marca: 'Ford',
            submarca: 'F-150',
            color: 'Blanco',
            estatus: 'Activa',

            servicio_adscripcion:
                'Seguridad Ciudadana',

            tipo_vehiculo:
                'Patrulla',

            origen:
                'Municipal',
        },

        {
            id: 102,
            oficial_id: 1,

            unidad: 'SP-115',

            marca: 'Nissan',
            submarca: 'Frontier',
            color: 'Blanco / Azul',
            estatus: 'Activa',

            servicio_adscripcion:
                'Seguridad Ciudadana',

            tipo_vehiculo:
                'Patrulla',

            origen:
                'Municipal',
        },

        {
            id: 201,
            oficial_id: 2,

            unidad: 'TR-204',

            marca: 'Dodge',
            submarca: 'Ram 1500',
            color: 'Blanco',
            estatus: 'Activa',

            servicio_adscripcion:
                'Tránsito',

            tipo_vehiculo:
                'Patrulla',

            origen:
                'Municipal',
        },

        {
            id: 301,
            oficial_id: 3,

            unidad: 'OP-310',

            marca: 'Chevrolet',
            submarca: 'Tahoe',
            color: 'Negro',
            estatus: 'Mantenimiento',

            servicio_adscripcion:
                'Operaciones',

            tipo_vehiculo:
                'SUV',

            origen:
                'Municipal',
        },
    ];


    /* =====================================================
       CAMBIO DE OFICIAL
    ===================================================== */

    inputOficial.addEventListener('input', () => {

        const nombre =
            inputOficial.value.trim();


        const oficial =
            buscarOficialPorNombreUnidad(
                oficiales,
                nombre
            );


        /*
         * Si el oficial aún no coincide
         * con un registro válido:
         *
         * - limpiamos unidades
         * - deshabilitamos el select
         * - limpiamos datos automáticos
         */
        if (!oficial) {

            limpiarSelectorUnidad(
                selectUnidad
            );

            limpiarDatosUnidad({
                inputMarca,
                inputSubmarca,
                inputColor,
                inputEstatus,
                inputServicioAdscripcion,
                inputTipoVehiculo,
                inputOrigen,
            });

            return;
        }


        /*
         * Buscamos las unidades relacionadas
         * con el oficial seleccionado.
         */
        const unidadesOficial =
            obtenerUnidadesPorOficial(
                unidades,
                oficial.id
            );


        cargarUnidadesEnSelect(
            selectUnidad,
            unidadesOficial
        );


        limpiarDatosUnidad({
            inputMarca,
            inputSubmarca,
            inputColor,
            inputEstatus,
            inputServicioAdscripcion,
            inputTipoVehiculo,
            inputOrigen,
        });

    });


    /* =====================================================
       SELECCIONAR UNIDAD
    ===================================================== */

    selectUnidad.addEventListener('change', () => {

        const idUnidad =
            Number(selectUnidad.value);


        if (!idUnidad) {

            limpiarDatosUnidad({
                inputMarca,
                inputSubmarca,
                inputColor,
                inputEstatus,
                inputServicioAdscripcion,
                inputTipoVehiculo,
                inputOrigen,
            });

            return;
        }


        const unidad =
            buscarUnidadPorId(
                unidades,
                idUnidad
            );


        if (!unidad) {

            limpiarDatosUnidad({
                inputMarca,
                inputSubmarca,
                inputColor,
                inputEstatus,
                inputServicioAdscripcion,
                inputTipoVehiculo,
                inputOrigen,
            });

            return;
        }


        cargarDatosUnidad(
            unidad,
            {
                inputMarca,
                inputSubmarca,
                inputColor,
                inputEstatus,
                inputServicioAdscripcion,
                inputTipoVehiculo,
                inputOrigen,
            }
        );

    });

}


/* =========================================================
   BUSCAR OFICIAL
========================================================= */

function buscarOficialPorNombreUnidad(
    oficiales,
    nombre
) {

    const nombreNormalizado =
        normalizarTextoUnidad(
            nombre
        );


    return oficiales.find((oficial) => {

        return normalizarTextoUnidad(
            oficial.nombre
        ) === nombreNormalizado;

    }) || null;

}


/* =========================================================
   OBTENER UNIDADES DEL OFICIAL
========================================================= */

function obtenerUnidadesPorOficial(
    unidades,
    idOficial
) {

    return unidades.filter(
        (unidad) =>
            Number(unidad.oficial_id)
            === Number(idOficial)
    );

}


/* =========================================================
   CARGAR UNIDADES EN SELECT
========================================================= */

function cargarUnidadesEnSelect(
    selectUnidad,
    unidades
) {

    selectUnidad.innerHTML = '';


    /*
     * Opción inicial
     */
    const opcionInicial =
        document.createElement('option');

    opcionInicial.value = '';

    opcionInicial.textContent =
        'Selecciona una unidad';

    opcionInicial.selected = true;

    opcionInicial.disabled = true;


    selectUnidad.appendChild(
        opcionInicial
    );


    /*
     * Si el oficial no tiene unidades
     */
    if (unidades.length === 0) {

        const opcionSinResultados =
            document.createElement('option');

        opcionSinResultados.value = '';

        opcionSinResultados.textContent =
            'No hay unidades relacionadas';

        opcionSinResultados.disabled = true;


        selectUnidad.appendChild(
            opcionSinResultados
        );


        selectUnidad.disabled = true;

        return;
    }


    /*
     * Agregamos las unidades disponibles
     */
    unidades.forEach((unidad) => {

        const opcion =
            document.createElement('option');


        opcion.value =
            unidad.id;


        opcion.textContent =
            unidad.unidad;


        selectUnidad.appendChild(
            opcion
        );

    });


    /*
     * Habilitamos el selector
     */
    selectUnidad.disabled = false;

}


/* =========================================================
   LIMPIAR SELECTOR DE UNIDAD
========================================================= */

function limpiarSelectorUnidad(
    selectUnidad
) {

    selectUnidad.innerHTML = '';


    const opcion =
        document.createElement('option');


    opcion.value = '';

    opcion.textContent =
        'Selecciona primero un oficial';


    selectUnidad.appendChild(
        opcion
    );


    selectUnidad.disabled =
        true;

}


/* =========================================================
   BUSCAR UNIDAD
========================================================= */

function buscarUnidadPorId(
    unidades,
    idUnidad
) {

    return unidades.find(
        (unidad) =>
            Number(unidad.id)
            === Number(idUnidad)
    ) || null;

}


/* =========================================================
   CARGAR DATOS DE LA UNIDAD
========================================================= */

function cargarDatosUnidad(
    unidad,
    campos
) {

    if (campos.inputMarca) {
        campos.inputMarca.value =
            unidad.marca || '';
    }


    if (campos.inputSubmarca) {
        campos.inputSubmarca.value =
            unidad.submarca || '';
    }


    if (campos.inputColor) {
        campos.inputColor.value =
            unidad.color || '';
    }


    if (campos.inputEstatus) {
        campos.inputEstatus.value =
            unidad.estatus || '';
    }


    if (
        campos.inputServicioAdscripcion
    ) {
        campos.inputServicioAdscripcion.value =
            unidad.servicio_adscripcion
            || '';
    }


    if (campos.inputTipoVehiculo) {
        campos.inputTipoVehiculo.value =
            unidad.tipo_vehiculo
            || '';
    }


    if (campos.inputOrigen) {
        campos.inputOrigen.value =
            unidad.origen || '';
    }

}


/* =========================================================
   LIMPIAR DATOS DE LA UNIDAD
========================================================= */

function limpiarDatosUnidad(
    campos
) {

    const listaCampos = [
        campos.inputMarca,
        campos.inputSubmarca,
        campos.inputColor,
        campos.inputEstatus,
        campos.inputServicioAdscripcion,
        campos.inputTipoVehiculo,
        campos.inputOrigen,
    ];


    listaCampos.forEach((campo) => {

        if (campo) {
            campo.value = '';
        }

    });

}


/* =========================================================
   NORMALIZAR TEXTO
========================================================= */

function normalizarTextoUnidad(
    texto
) {

    return String(texto || '')
        .trim()
        .toLowerCase()
        .normalize('NFD')
        .replace(
            /[\u0300-\u036f]/g,
            ''
        );

}