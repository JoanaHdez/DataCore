import {
    establecerMotivosHabilitadosEditar
} from './motivos.js';

/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   SITUACIÓN DE LA SANCIÓN
========================================================= */


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarSancionesEditar(
    modal
) {

    if (!modal) {
        return;
    }


    if (
        modal.dataset.editarSancionesInicializadas
        === '1'
    ) {
        return;
    }


    const sinSanciones =
        modal.querySelector(
            '#editar-sin-sanciones'
        );


    const bajaVoluntaria =
        modal.querySelector(
            '#editar-baja-voluntaria'
        );


    const estado =
        modal.querySelector(
            '#editar-estado-actual'
        );


    if (
        !sinSanciones
        || !bajaVoluntaria
        || !estado
    ) {
        return;
    }


    modal.dataset.editarSancionesInicializadas =
        '1';


    /* =====================================================
       SIN SANCIONES
    ===================================================== */

    sinSanciones.addEventListener(
        'change',
        () => {

            if (
                sinSanciones.checked
            ) {

                bajaVoluntaria.checked =
                    false;


                establecerMotivosHabilitadosEditar(
                    modal,
                    false
                );

            } else {

                establecerMotivosHabilitadosEditar(
                    modal,
                    true
                );
            }


            actualizarBajaVoluntariaEditar(
                modal
            );
        }
    );


    /* =====================================================
       BAJA VOLUNTARIA
    ===================================================== */

    bajaVoluntaria.addEventListener(
        'change',
        () => {

            if (
                bajaVoluntaria.checked
            ) {

                sinSanciones.checked =
                    false;


                establecerMotivosHabilitadosEditar(
                    modal,
                    true
                );
            }


            actualizarBajaVoluntariaEditar(
                modal
            );
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarBajaVoluntariaEditar(
        modal
    );
}


/* =========================================================
   ACTUALIZAR BAJA VOLUNTARIA
========================================================= */

export function actualizarBajaVoluntariaEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const bajaVoluntaria =
        modal.querySelector(
            '#editar-baja-voluntaria'
        );


    const estado =
        modal.querySelector(
            '#editar-estado-actual'
        );


    if (
        !bajaVoluntaria
        || !estado
    ) {
        return;
    }


    /* =====================================================
       BAJA VOLUNTARIA ACTIVADA
    ===================================================== */

    if (
        bajaVoluntaria.checked
    ) {

        /*
         * Guardamos el estado que tenía antes de seleccionar
         * Baja voluntaria.
         */

        if (
            !estado.dataset.estadoAnterior
        ) {

            estado.dataset.estadoAnterior =
                estado.value
                || 'Pendiente';
        }


        /*
         * Baja voluntaria finaliza automáticamente
         * el reporte.
         */

        estado.value =
            'Finalizado';


        /*
         * No usamos disabled porque necesitamos que
         * estado_actual siga enviándose en FormData.
         */

        estado.classList.add(
            'report-select--readonly'
        );


        return;
    }


    /* =====================================================
       BAJA VOLUNTARIA DESACTIVADA
    ===================================================== */

    estado.classList.remove(
        'report-select--readonly'
    );


    /*
     * Si existía un estado anterior, lo restauramos.
     */

    if (
        estado.dataset.estadoAnterior
    ) {

        estado.value =
            estado.dataset.estadoAnterior;


        delete estado.dataset.estadoAnterior;
    }
}


/* =========================================================
   CARGAR DATOS EXISTENTES
========================================================= */

export function cargarSancionesEditar(
    modal,
    reporte
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    const sinSanciones =
        modal.querySelector(
            '#editar-sin-sanciones'
        );


    const bajaVoluntaria =
        modal.querySelector(
            '#editar-baja-voluntaria'
        );


    const estado =
        modal.querySelector(
            '#editar-estado-actual'
        );


    if (
        !sinSanciones
        || !bajaVoluntaria
        || !estado
    ) {
        return;
    }


    /* =====================================================
       LIMPIAR ESTADO TEMPORAL
    ===================================================== */

    delete estado.dataset.estadoAnterior;


    /* =====================================================
       CARGAR VALORES EXISTENTES
    ===================================================== */

    sinSanciones.checked =
        convertirBooleano(
            reporte.sin_sanciones
        );


    bajaVoluntaria.checked =
        convertirBooleano(
            reporte.baja_voluntaria
        );


    /* =====================================================
       EVITAR AMBAS OPCIONES ACTIVAS
    ===================================================== */

    if (
        sinSanciones.checked
        && bajaVoluntaria.checked
    ) {

        sinSanciones.checked =
            false;
    }


    /* =====================================================
       MOTIVOS

       Si está marcado "Sin sanciones",
       el buscador debe quedar deshabilitado.

       En cualquier otro caso debe quedar habilitado.
    ===================================================== */

    establecerMotivosHabilitadosEditar(
        modal,
        !sinSanciones.checked
    );


    /* =====================================================
       BAJA VOLUNTARIA
    ===================================================== */

    actualizarBajaVoluntariaEditar(
        modal
    );
}

/* =========================================================
   LIMPIAR
========================================================= */

export function limpiarSancionesEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const sinSanciones =
        modal.querySelector(
            '#editar-sin-sanciones'
        );


    const bajaVoluntaria =
        modal.querySelector(
            '#editar-baja-voluntaria'
        );


    const estado =
        modal.querySelector(
            '#editar-estado-actual'
        );


    /* =====================================================
       SIN SANCIONES
    ===================================================== */

    if (sinSanciones) {

        sinSanciones.checked =
            false;
    }


    /* =====================================================
       BAJA VOLUNTARIA
    ===================================================== */

    if (bajaVoluntaria) {

        bajaVoluntaria.checked =
            false;
    }


    /* =====================================================
       ESTADO
    ===================================================== */

    if (estado) {

        delete estado.dataset.estadoAnterior;


        estado.classList.remove(
            'report-select--readonly'
        );
    }


    /* =====================================================
       MOTIVOS

       Al limpiar Editar, el buscador debe volver
       a quedar disponible.
    ===================================================== */

    establecerMotivosHabilitadosEditar(
        modal,
        true
    );
}


/* =========================================================
   CONVERTIR VALOR A BOOLEANO
========================================================= */

function convertirBooleano(
    valor
) {

    return (
        valor === true
        || valor === 1
        || valor === '1'
        || String(
            valor
            || ''
        )
            .trim()
            .toLowerCase()
            === 'true'
    );
}