import {
    mostrarResultadoYRedirigir
} from '../../notificaciones/resultado.js';

/* =========================================================
   FELICITACIONES
   GUARDAR FORMULARIO
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarFormularioFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarFormularioFelicitacion() {

    const formulario =
        document.querySelector(
            '#form-nueva-felicitacion'
        );


    const botonGuardar =
        document.querySelector(
            '#btn-guardar-felicitacion'
        );


    if (
        !formulario
        || !botonGuardar
    ) {
        return;
    }


    let guardando =
        false;


    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            if (
                guardando
            ) {
                return;
            }


            /* =====================================================
               VALIDACIÓN HTML
            ===================================================== */

            if (
                !formulario.checkValidity()
            ) {

                formulario.reportValidity();

                return;
            }


            /* =====================================================
               VALIDAR PERSONAL
            ===================================================== */

            const personal =
                formulario.querySelectorAll(
                    'input[name^="personal["]'
                );


            if (
                personal.length === 0
            ) {

                alert(
                    'Debes agregar al menos una persona a la felicitación.'
                );

                return;
            }


            guardando =
                true;


            establecerEstadoGuardando(
                botonGuardar,
                true
            );


            try {

                /* =================================================
                   FORM DATA
                ================================================= */

                const datos =
                    new FormData(
                        formulario
                    );


                /* =================================================
                   ENDPOINT
                ================================================= */

                const url =
                    new URL(
                        'DataCore/public/asuntos-internos/reportes/felicitaciones/guardar',
                        `${window.location.origin}/`
                    );


                /* =================================================
                   REQUEST
                ================================================= */

                const respuesta =
                    await fetch(
                        url.toString(),
                        {
                            method:
                                'POST',

                            body:
                                datos,

                            headers: {
                                Accept:
                                    'application/json',
                            },

                            credentials:
                                'same-origin',
                        }
                    );


                /* =================================================
                   RESPUESTA
                ================================================= */

                let resultado =
                    null;


                try {

                    resultado =
                        await respuesta.json();

                } catch (error) {

                    throw new Error(
                        'El servidor devolvió una respuesta no válida.'
                    );
                }


                if (
                    !respuesta.ok
                    || resultado?.success !== true
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible guardar la felicitación.'
                    );
                }


                /* =================================================
                   ÉXITO
                ================================================= */

                mostrarResultadoYRedirigir({

                    tipo:
                        'success',

                    titulo:
                        'Felicitación guardada',

                    mensaje:
                        `La felicitación se registró correctamente con el folio ${resultado.folio ?? ''}.`,

                    url:
                        '/DataCore/public/asuntos-internos/reportes/listado',

                    duracion:
                        2000,

                });

            } catch (error) {

                console.error(
                    'Error guardando felicitación:',
                    error
                );


                alert(
                    error.message
                    || 'Ocurrió un error al guardar la felicitación.'
                );


            } finally {

                guardando =
                    false;


                establecerEstadoGuardando(
                    botonGuardar,
                    false
                );
            }
        }
    );
}


/* =========================================================
   ESTADO BOTÓN
========================================================= */

function establecerEstadoGuardando(
    boton,
    guardando
) {

    boton.disabled =
        guardando;


    if (
        guardando
    ) {

        boton.dataset.textoOriginal =
            boton.textContent;


        boton.textContent =
            'Guardando...';


        return;
    }


    boton.textContent =
        boton.dataset.textoOriginal
        || 'Guardar felicitación';


    delete boton.dataset.textoOriginal;
}