/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Utilidades
========================================================= */


/* =========================================================
   FOLIO
========================================================= */

export function obtenerFolioFila(
    fila
) {

    return fila
        ?.querySelector('td')
        ?.textContent
        .trim()
        || '';

}


export function obtenerPrefijoFolio(
    folio
) {

    if (!folio) {
        return 'QJ';
    }


    const partes =
        String(
            folio
        ).split('-');


    return partes.length > 1
        ? partes[0]
        : 'QJ';

}


export function obtenerNumeroFolio(
    folio
) {

    if (!folio) {
        return '';
    }


    const partes =
        String(
            folio
        ).split('-');


    if (
        partes.length <= 1
    ) {
        return String(
            folio
        );
    }


    return partes
        .slice(1)
        .join('-');

}


export function construirFolio(
    prefijo,
    numero
) {

    const prefijoLimpio =
        String(
            prefijo || 'QJ'
        )
            .trim()
            .toUpperCase();


    const numeroLimpio =
        String(
            numero || ''
        ).trim();


    if (!numeroLimpio) {
        return prefijoLimpio;
    }


    return `${prefijoLimpio}-${numeroLimpio}`;

}


/* =========================================================
   FECHAS
========================================================= */

export function convertirFechaInput(
    fecha
) {

    const valor =
        String(
            fecha || ''
        ).trim();


    if (!valor) {
        return '';
    }


    /*
     * Si ya viene en formato YYYY-MM-DD,
     * lo dejamos intacto.
     */
    if (
        /^\d{4}-\d{2}-\d{2}$/.test(
            valor
        )
    ) {
        return valor;
    }


    const partes =
        valor.split('/');


    if (
        partes.length !== 3
    ) {
        return '';
    }


    const [
        dia,
        mes,
        anio
    ] = partes;


    if (
        !dia
        || !mes
        || !anio
    ) {
        return '';
    }


    return `${anio}-${mes}-${dia}`;

}


export function formatearFechaTabla(
    fecha
) {

    const valor =
        String(
            fecha || ''
        ).trim();


    if (!valor) {
        return '';
    }


    /*
     * Si ya está como DD/MM/YYYY,
     * no volvemos a modificarlo.
     */
    if (
        /^\d{2}\/\d{2}\/\d{4}$/.test(
            valor
        )
    ) {
        return valor;
    }


    const partes =
        valor.split('-');


    if (
        partes.length !== 3
    ) {
        return valor;
    }


    const [
        anio,
        mes,
        dia
    ] = partes;


    return `${dia}/${mes}/${anio}`;

}


/* =========================================================
   VALORES EN EL DOM
========================================================= */

export function asignarValorEditar(
    modal,
    selector,
    valor
) {

    if (!modal) {
        return;
    }


    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    elemento.value =
        valor ?? '';

}


export function asignarTextoEditar(
    modal,
    selector,
    valor
) {

    if (!modal) {
        return;
    }


    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    elemento.textContent =
        String(
            valor ?? ''
        ).trim()
        || '—';

}


/* =========================================================
   SELECT SEGURO
========================================================= */

export function asignarSelectSeguro(
    modal,
    selector,
    valor
) {

    if (!modal) {
        return;
    }


    const select =
        modal.querySelector(
            selector
        );


    if (!select) {
        return;
    }


    const valorLimpio =
        String(
            valor || ''
        ).trim();


    if (!valorLimpio) {

        select.value =
            '';

        return;
    }


    const existe =
        Array.from(
            select.options
        ).some(
            (opcion) =>
                opcion.value
                === valorLimpio
        );


    if (!existe) {

        const opcion =
            document.createElement(
                'option'
            );


        opcion.value =
            valorLimpio;

        opcion.textContent =
            valorLimpio;


        select.appendChild(
            opcion
        );

    }


    select.value =
        valorLimpio;

}


/* =========================================================
   FORM DATA
========================================================= */

export function obtenerDatoFormulario(
    datos,
    nombre
) {

    const valor =
        datos.get(
            nombre
        );


    return typeof valor === 'string'
        ? valor.trim()
        : '';

}


/* =========================================================
   TEXTO
========================================================= */

export function normalizarMayuscula(
    valor
) {

    return String(
        valor ?? ''
    )
        .trim()
        .toUpperCase();

}


export function obtenerInicialEditar(
    nombre
) {

    const texto =
        String(
            nombre || ''
        ).trim();


    if (!texto) {
        return '?';
    }


    return texto
        .charAt(0)
        .toUpperCase();

}


/* =========================================================
   ESCAPAR HTML
========================================================= */

export function escaparHTML(
    valor
) {

    const elemento =
        document.createElement(
            'div'
        );


    elemento.textContent =
        valor ?? '';


    return elemento.innerHTML;

}


/* =========================================================
   DATASET
========================================================= */

export function leerDatasetArray(
    contenido
) {

    if (!contenido) {
        return [];
    }


    try {

        const datos =
            JSON.parse(
                contenido
            );


        return Array.isArray(
            datos
        )
            ? datos
            : [];


    } catch (error) {

        console.error(
            'No fue posible leer los datos relacionados:',
            error
        );


        return [];
    }

}


/* =========================================================
   CLONAR ARRAY
========================================================= */

export function clonarArray(
    datos
) {

    if (
        !Array.isArray(
            datos
        )
    ) {
        return [];
    }


    return datos.map(
        (item) => ({
            ...item,
        })
    );

}


/* =========================================================
   CLASE DE ESTADO
========================================================= */

export function obtenerClaseEstado(
    estado
) {

    const valor =
        String(
            estado || ''
        )
            .trim()
            .toUpperCase();


    switch (valor) {

        case 'FINALIZADO':

            return 'estado--finalizado';


        case 'EN PROCESO':

            return 'estado--proceso';


        default:

            return 'estado--pendiente';

    }

}