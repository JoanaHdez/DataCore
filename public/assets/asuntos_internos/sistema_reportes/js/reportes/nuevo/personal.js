/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Nuevo reporte - Personal involucrado
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarPersonalInvolucrado();
});


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarPersonalInvolucrado() {

    const inputOficial =
        document.querySelector('#oficial');

    const inputArea =
        document.querySelector('#area');


    if (!inputOficial || !inputArea) {
        return;
    }


    /* =====================================================
       DATOS TEMPORALES

       Posteriormente este catálogo será sustituido por
       información obtenida desde la base de datos.
    ===================================================== */

    const oficiales = [
        {
            id: 1,
            nombre: 'Juan Pérez López',
            area: 'Seguridad Ciudadana',
        },
        {
            id: 2,
            nombre: 'María Hernández García',
            area: 'Tránsito',
        },
        {
            id: 3,
            nombre: 'Carlos Ramírez Torres',
            area: 'Operaciones',
        },
    ];


    /* =====================================================
       DETECTAR OFICIAL AUTOMÁTICAMENTE
    ===================================================== */

    inputOficial.addEventListener('input', () => {

        const nombre =
            inputOficial.value.trim();


        /*
         * Si el campo queda vacío,
         * limpiamos los datos automáticos.
         */
        if (!nombre) {

            limpiarDatosOficial(
                inputArea
            );

            return;
        }


        /*
         * Buscamos si el nombre escrito corresponde
         * exactamente a alguno de los oficiales.
         */
        const oficial =
            buscarOficialPorNombre(
                oficiales,
                nombre
            );


        /*
         * Si todavía no existe coincidencia,
         * mantenemos Área vacía.
         */
        if (!oficial) {

            limpiarDatosOficial(
                inputArea
            );

            return;
        }


        /*
         * Si encontramos al oficial,
         * cargamos sus datos automáticamente.
         */
        cargarDatosOficial(
            oficial,
            inputArea
        );

    });

}


/* =========================================================
   BUSCAR OFICIAL
========================================================= */

function buscarOficialPorNombre(
    oficiales,
    nombre
) {

    const nombreNormalizado =
        normalizarTexto(nombre);


    return oficiales.find((oficial) => {

        const nombreOficial =
            normalizarTexto(
                oficial.nombre
            );


        return nombreOficial === nombreNormalizado;

    }) || null;

}


/* =========================================================
   CARGAR DATOS DEL OFICIAL
========================================================= */

function cargarDatosOficial(
    oficial,
    inputArea
) {

    inputArea.value =
        oficial.area || '';

}


/* =========================================================
   LIMPIAR DATOS DEL OFICIAL
========================================================= */

function limpiarDatosOficial(
    inputArea
) {

    inputArea.value = '';

}


/* =========================================================
   NORMALIZAR TEXTO

   Permite comparar nombres ignorando:
   - Mayúsculas / minúsculas
   - Acentos
   - Espacios al inicio o final
========================================================= */

function normalizarTexto(
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