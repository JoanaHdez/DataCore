/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Sanción disciplinaria
========================================================= */


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarSancion(
    modal
) {

    const select =
        modal.querySelector(
            '#editar-sancion-disciplinaria'
        );


    const inputOtro =
        modal.querySelector(
            '#editar-sancion-otro'
        );


    if (
        !select
        || !inputOtro
    ) {
        return;
    }


    select.addEventListener(
        'change',
        () => {

            actualizarCampoOtroSancion(
                modal
            );

        }
    );


    actualizarCampoOtroSancion(
        modal
    );
}


/* =========================================================
   CARGAR SANCIÓN VIGENTE
========================================================= */

export function cargarSancionEditar(
    modal,
    sancion
) {

    const select =
        modal.querySelector(
            '#editar-sancion-disciplinaria'
        );

    const inputOtro =
        modal.querySelector(
            '#editar-sancion-otro'
        );

    const original =
        modal.querySelector(
            '#editar-sancion-original'
        );

    const otroOriginal =
        modal.querySelector(
            '#editar-sancion-otro-original'
        );

    const avisoOrigen =
        modal.querySelector(
            '#editar-sancion-origen'
        );


    const tipo =
        String(
            sancion?.tipo
            || ''
        ).trim();


    const descripcionOtro =
        String(
            sancion?.descripcion_otro
            || ''
        ).trim();


    /* =====================================================
       SELECT
    ===================================================== */

    if (select) {

        const valoresPermitidos = [
            '',
            'Arresto',
            'Amonestación',
            'Otro',
        ];


        select.value =
            valoresPermitidos.includes(
                tipo
            )
                ? tipo
                : '';
    }


    /* =====================================================
       OTRO
    ===================================================== */

    if (inputOtro) {

        inputOtro.value =
            tipo === 'Otro'
                ? descripcionOtro
                : '';
    }


    /* =====================================================
       VALORES ORIGINALES
    ===================================================== */

    if (original) {

        original.value =
            tipo;
    }


    if (otroOriginal) {

        otroOriginal.value =
            tipo === 'Otro'
                ? descripcionOtro
                : '';
    }


    /*
     * MUY IMPORTANTE:
     * primero cargamos el valor y DESPUÉS
     * actualizamos la interfaz.
     */

    actualizarCampoOtroSancion(
        modal
    );


    /* =====================================================
       ORIGEN
    ===================================================== */

    if (avisoOrigen) {

        const desdeSeguimiento =
            sancion?.actualizada_desde_seguimiento
            === true;


        if (!desdeSeguimiento) {

            avisoOrigen.hidden =
                true;

            avisoOrigen.style.display =
                'none';

            avisoOrigen.textContent =
                '';

            return;
        }


        const fecha =
            String(
                sancion?.fecha_actualizacion
                || ''
            ).trim();


        avisoOrigen.textContent =
            fecha
                ? `Actualizada desde seguimiento el ${fecha}`
                : 'Actualizada desde seguimiento';


        avisoOrigen.hidden =
            false;

        avisoOrigen.style.display =
            '';
    }
}


/* =========================================================
   LIMPIAR
========================================================= */

export function limpiarSancionEditar(
    modal
) {

    const select =
        modal.querySelector(
            '#editar-sancion-disciplinaria'
        );


    const inputOtro =
        modal.querySelector(
            '#editar-sancion-otro'
        );


    const original =
        modal.querySelector(
            '#editar-sancion-original'
        );


    const otroOriginal =
        modal.querySelector(
            '#editar-sancion-otro-original'
        );


    const avisoOrigen =
        modal.querySelector(
            '#editar-sancion-origen'
        );


    if (select) {
        select.value = '';
    }


    if (inputOtro) {
        inputOtro.value = '';
    }


    if (original) {
        original.value = '';
    }


    if (otroOriginal) {
        otroOriginal.value = '';
    }


    if (avisoOrigen) {

        avisoOrigen.hidden =
            true;

        avisoOrigen.textContent =
            '';
    }


    actualizarCampoOtroSancion(
        modal
    );
}


/* =========================================================
   CAMPO "OTRO"
========================================================= */

export function actualizarCampoOtroSancion(
    modal
) {

    const select =
        modal.querySelector(
            '#editar-sancion-disciplinaria'
        );


    const contenedor =
        modal.querySelector(
            '#editar-campo-sancion-otro'
        );


    const input =
        modal.querySelector(
            '#editar-sancion-otro'
        );


    if (
        !select
        || !contenedor
        || !input
    ) {
        return;
    }


    const esOtro =
        select.value === 'Otro';


    /* =====================================================
       SI ES "OTRO"
    ===================================================== */

    if (esOtro) {

        contenedor.hidden =
            false;


        /*
         * Quitamos cualquier display:none que haya quedado
         * aplicado anteriormente.
         */

        contenedor.style.removeProperty(
            'display'
        );


        input.disabled =
            false;


        input.required =
            true;


        return;
    }


    /* =====================================================
       CUALQUIER OTRA OPCIÓN
    ===================================================== */

    contenedor.hidden =
        true;


    /*
     * Forzamos display:none porque algunas reglas CSS
     * del formulario pueden sobrescribir [hidden].
     */

    contenedor.style.setProperty(
        'display',
        'none',
        'important'
    );


    input.disabled =
        true;


    input.required =
        false;


    input.value =
        '';
}


/* =========================================================
   VALIDAR OTRO
========================================================= */

export function validarSancionEditar(
    modal
) {

    const select =
        modal.querySelector(
            '#editar-sancion-disciplinaria'
        );


    const inputOtro =
        modal.querySelector(
            '#editar-sancion-otro'
        );


    if (!select) {
        return true;
    }


    if (
        select.value !== 'Otro'
    ) {
        return true;
    }


    const descripcion =
        String(
            inputOtro?.value
            || ''
        ).trim();


    if (descripcion) {
        return true;
    }


    if (inputOtro) {

        inputOtro.focus();

        inputOtro.reportValidity();
    }


    return false;
}


/* =========================================================
   ¿CAMBIÓ LA SANCIÓN?
========================================================= */

export function sancionFueModificada(
    modal
) {

    const select =
        modal.querySelector(
            '#editar-sancion-disciplinaria'
        );


    const inputOtro =
        modal.querySelector(
            '#editar-sancion-otro'
        );


    const original =
        modal.querySelector(
            '#editar-sancion-original'
        );


    const otroOriginal =
        modal.querySelector(
            '#editar-sancion-otro-original'
        );


    const tipoActual =
        String(
            select?.value
            || ''
        ).trim();


    const tipoOriginal =
        String(
            original?.value
            || ''
        ).trim();


    if (
        tipoActual
        !== tipoOriginal
    ) {
        return true;
    }


    if (
        tipoActual !== 'Otro'
    ) {
        return false;
    }


    const descripcionActual =
        normalizarTextoSancion(
            inputOtro?.value
        );


    const descripcionOriginal =
        normalizarTextoSancion(
            otroOriginal?.value
        );


    return descripcionActual
        !== descripcionOriginal;
}


/* =========================================================
   OBTENER VALORES
========================================================= */

export function obtenerSancionEditar(
    modal
) {

    const select =
        modal.querySelector(
            '#editar-sancion-disciplinaria'
        );


    const inputOtro =
        modal.querySelector(
            '#editar-sancion-otro'
        );


    return {

        tipo:
            String(
                select?.value
                || ''
            ).trim(),

        descripcion_otro:
            select?.value === 'Otro'
                ? String(
                    inputOtro?.value
                    || ''
                ).trim()
                : '',
    };
}


/* =========================================================
   TEXTO VISIBLE
========================================================= */

export function obtenerTextoSancionEditar(
    sancion
) {

    const tipo =
        String(
            sancion?.tipo
            || ''
        ).trim();


    if (!tipo) {
        return 'Sin sanción';
    }


    if (tipo === 'Otro') {

        const descripcion =
            String(
                sancion?.descripcion_otro
                || ''
            ).trim();


        return descripcion
            || 'Otro';
    }


    return tipo;
}


/* =========================================================
   NORMALIZAR
========================================================= */

function normalizarTextoSancion(
    valor
) {

    return String(
        valor
        || ''
    )
        .trim()
        .replace(
            /\s+/g,
            ' '
        )
        .toLocaleLowerCase(
            'es-MX'
        );
}