
/* =========================================================
   INICIALIZAR MODALIDAD DE UNIDAD
========================================================= */

export function inicializarModalidadUnidadEditar(
    modal
) {

    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const radioSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad'
        );


    const contenidoConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad-contenido'
        );


    const contenidoSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad-contenido'
        );


    const inputBuscarUnidad =
        modal.querySelector(
            '#editar-felicitacion-buscar-unidad'
        );


    const resultadosUnidad =
        modal.querySelector(
            '#editar-felicitacion-unidad-resultados'
        );


    const tbodyUnidades =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    if (
        !radioConUnidad
        || !radioSinUnidad
        || !contenidoConUnidad
        || !contenidoSinUnidad
    ) {
        return;
    }


    /* =====================================================
       MOSTRAR / OCULTAR CONTENIDO
    ===================================================== */

    function actualizarVista() {

        const conUnidad =
            radioConUnidad.checked;


        const sinUnidad =
            radioSinUnidad.checked;


        contenidoConUnidad.hidden =
            !conUnidad;


        contenidoSinUnidad.hidden =
            !sinUnidad;


        /* =================================================
           SI CAMBIA A SIN UNIDAD
           LIMPIAR BUSCADOR Y RESULTADOS
        ================================================= */

        if (sinUnidad) {

            if (inputBuscarUnidad) {

                inputBuscarUnidad.value =
                    '';
            }


            if (resultadosUnidad) {

                resultadosUnidad.innerHTML =
                    '';

                resultadosUnidad.hidden =
                    true;
            }
        }
    }


    /* =====================================================
       CAMBIO A CON UNIDAD
    ===================================================== */

    radioConUnidad.addEventListener(
        'change',
        () => {

            if (
                !radioConUnidad.checked
            ) {
                return;
            }


            actualizarVista();


            /* =================================================
               SI NO HAY UNIDADES MOSTRAR ESTADO VACÍO
            ================================================= */

            if (
                tbodyUnidades
                && tbodyUnidades.querySelectorAll(
                    'tr[data-unidad-id]'
                ).length === 0
            ) {

                tbodyUnidades.innerHTML = `
                    <tr>
                        <td colspan="7">
                            Sin unidades relacionadas
                        </td>
                    </tr>
                `;
            }
        }
    );


    /* =====================================================
       CAMBIO A SIN UNIDAD / OFICINA
    ===================================================== */

    radioSinUnidad.addEventListener(
        'change',
        () => {

            if (
                !radioSinUnidad.checked
            ) {
                return;
            }


            actualizarVista();
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarVista();
}
