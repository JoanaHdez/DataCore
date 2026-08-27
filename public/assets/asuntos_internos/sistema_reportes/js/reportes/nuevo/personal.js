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

    const inputTurno =
        document.querySelector('#turno');


    if (
        !inputOficial
        || !inputArea
        || !inputTurno
    ) {
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
            turno: 'Primer turno',
        },
        {
            id: 2,
            nombre: 'María Hernández García',
            area: 'Tránsito',
            turno: 'Segundo turno',
        },
        {
            id: 3,
            nombre: 'Carlos Ramírez Torres',
            area: 'Operaciones',
            turno: 'Tercer turno',
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
                inputArea,
                inputTurno
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
         * mantenemos Área y Turno vacíos.
         */
        if (!oficial) {

            limpiarDatosOficial(
                inputArea,
                inputTurno
            );

            return;
        }


        /*
         * Si encontramos al oficial,
         * cargamos sus datos automáticamente.
         */
        cargarDatosOficial(
            oficial,
            inputArea,
            inputTurno
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
    inputArea,
    inputTurno
) {

    inputArea.value =
        oficial.area || '';

    inputTurno.value =
        oficial.turno || '';

}


/* =========================================================
   LIMPIAR DATOS DEL OFICIAL
========================================================= */

function limpiarDatosOficial(
    inputArea,
    inputTurno
) {

    inputArea.value = '';

    inputTurno.value = '';

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