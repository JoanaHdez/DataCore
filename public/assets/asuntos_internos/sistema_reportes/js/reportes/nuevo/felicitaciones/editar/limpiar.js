/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   LIMPIAR MODAL
========================================================= */


/* =========================================================
   LIMPIAR MODAL
========================================================= */

export function limpiarModalEditar(
    modal
) {

    /* =====================================================
       ID
    ===================================================== */

    const inputId =
        modal.querySelector(
            '#editar-felicitacion-id'
        );


    if (inputId) {

        inputId.value =
            '';
    }


    /* =====================================================
       TÍTULO
    ===================================================== */

    const tituloFolio =
        modal.querySelector(
            '#editar-felicitacion-titulo-folio'
        );


    if (tituloFolio) {

        tituloFolio.textContent =
            '';
    }


    /* =====================================================
       FOLIO
    ===================================================== */

    const inputFolio =
        modal.querySelector(
            '#editar-felicitacion-folio'
        );


    if (inputFolio) {

        inputFolio.value =
            '';
    }


    /* =====================================================
       FECHA
    ===================================================== */

    const inputFecha =
        modal.querySelector(
            '#editar-felicitacion-fecha'
        );


    if (inputFecha) {

        inputFecha.value =
            '';
    }


    /* =====================================================
       FELICITANTE
    ===================================================== */

    const inputFelicitante =
        modal.querySelector(
            '#editar-felicitacion-felicitante'
        );


    if (inputFelicitante) {

        inputFelicitante.value =
            '';
    }


    /* =====================================================
       RAZÓN DE LA FELICITACIÓN
    ===================================================== */

    const inputRazon =
        modal.querySelector(
            '#editar-felicitacion-razon'
        );


    if (inputRazon) {

        inputRazon.value =
            '';
    }


    /* =====================================================
       PERSONAL
    ===================================================== */

    const tbodyPersonal =
        modal.querySelector(
            '#editar-felicitacion-personal'
        );


    if (tbodyPersonal) {

        tbodyPersonal.innerHTML = `
            <tr>
                <td colspan="6">
                    Sin personal relacionado
                </td>
            </tr>
        `;
    }


    /* =====================================================
       INPUTS DE PERSONAL
    ===================================================== */

    const inputsPersonal =
        modal.querySelector(
            '#editar-felicitacion-personal-inputs'
        );


    if (inputsPersonal) {

        inputsPersonal.innerHTML =
            '';
    }


    /* =====================================================
       UNIDADES
    ===================================================== */

    const tbodyUnidades =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    if (tbodyUnidades) {

        tbodyUnidades.innerHTML = `
            <tr>
                <td colspan="7">
                    Sin unidades relacionadas
                </td>
            </tr>
        `;
    }


    /* =====================================================
       INPUTS DE UNIDADES
    ===================================================== */

    const inputsUnidades =
        modal.querySelector(
            '#editar-felicitacion-unidades-inputs'
        );


    if (inputsUnidades) {

        inputsUnidades.innerHTML =
            '';
    }


    /* =====================================================
       MODALIDAD DE UNIDAD
    ===================================================== */

    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const radioSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad'
        );


    if (radioConUnidad) {

        radioConUnidad.checked =
            false;
    }


    if (radioSinUnidad) {

        radioSinUnidad.checked =
            false;
    }


    /* =====================================================
       REGRESAR A PRIMERA PESTAÑA
    ===================================================== */

    const botones =
        modal.querySelectorAll(
            '[data-seccion-editar-felicitacion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-panel-editar-felicitacion]'
        );


    botones.forEach(
        (boton) => {

            const activo =
                boton.dataset
                    .seccionEditarFelicitacion
                === 'datos';


            boton.classList.toggle(
                'modal-felicitacion-editar__nav-btn--activo',
                activo
            );


            boton.setAttribute(
                'aria-selected',
                activo
                    ? 'true'
                    : 'false'
            );
        }
    );


    paneles.forEach(
        (panel) => {

            const activo =
                panel.dataset
                    .panelEditarFelicitacion
                === 'datos';


            panel.hidden =
                !activo;


            panel.classList.toggle(
                'modal-felicitacion-editar__panel--activo',
                activo
            );
        }
    );


    /* =====================================================
       REGRESAR SCROLL ARRIBA
    ===================================================== */

    const body =
        modal.querySelector(
            '.modal-felicitacion-editar__body'
        );


    if (body) {

        body.scrollTop =
            0;
    }
}