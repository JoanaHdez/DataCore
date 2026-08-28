/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Evidencia
========================================================= */


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarEvidencia(
    modal,
    formulario
) {

    if (
        !modal
        || !formulario
    ) {
        return;
    }


    const input =
        formulario.querySelector(
            '#editar-evidencia-fotografica'
        );


    if (!input) {
        return;
    }


    input.addEventListener(
        'change',
        () => {

            mostrarEvidenciaNueva(
                modal,
                input.files
            );

        }
    );

}


/* =========================================================
   EVIDENCIA EXISTENTE
========================================================= */

export function mostrarEvidenciaExistente(
    modal,
    evidencias
) {

    const contenedor =
        modal.querySelector(
            '#editar-evidencia-existente'
        );


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML =
        '';


    if (
        !Array.isArray(
            evidencias
        )
        || evidencias.length === 0
    ) {

        contenedor.innerHTML = `
            <span class="editar-evidencia__vacio">
                Sin evidencia registrada
            </span>
        `;


        return;
    }


    evidencias.forEach(
        (evidencia) => {

            const item =
                document.createElement(
                    'div'
                );


            item.className =
                'editar-evidencia__item';


            item.textContent =
                evidencia.nombre
                || evidencia.archivo
                || 'Imagen';


            contenedor.appendChild(
                item
            );

        }
    );

}


/* =========================================================
   EVIDENCIA NUEVA
========================================================= */

export function mostrarEvidenciaNueva(
    modal,
    archivos
) {

    const contenedor =
        modal.querySelector(
            '#editar-evidencia-nueva'
        );


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML =
        '';


    const lista =
        Array.from(
            archivos || []
        );


    if (
        lista.length === 0
    ) {

        contenedor.innerHTML = `
            <span class="editar-evidencia__vacio">
                No se han seleccionado archivos nuevos
            </span>
        `;


        return;
    }


    lista.forEach(
        (archivo) => {

            const item =
                document.createElement(
                    'div'
                );


            item.className =
                'editar-evidencia__item';


            const nombre =
                document.createElement(
                    'span'
                );


            nombre.textContent =
                archivo.name
                || 'Imagen';


            item.appendChild(
                nombre
            );


            contenedor.appendChild(
                item
            );

        }
    );

}


/* =========================================================
   OBTENER NUEVAS EVIDENCIAS
========================================================= */

export function obtenerNuevasEvidencias(
    datos
) {

    if (
        !(datos instanceof FormData)
    ) {
        return [];
    }


    const archivos =
        datos.getAll(
            'evidencia_fotografica[]'
        );


    return archivos
        .filter(
            (archivo) => {

                return (
                    archivo instanceof File
                    && archivo.size > 0
                );

            }
        )
        .map(
            (archivo) => ({

                nombre:
                    archivo.name,

                archivo:
                    archivo.name,

                temporal:
                    true,

            })
        );

}


/* =========================================================
   LIMPIAR EVIDENCIA NUEVA
========================================================= */

export function limpiarEvidenciaNueva(
    modal,
    formulario = null
) {

    const input =
        formulario
            ?.querySelector(
                '#editar-evidencia-fotografica'
            )
        || modal.querySelector(
            '#editar-evidencia-fotografica'
        );


    if (input) {

        input.value =
            '';

    }


    mostrarEvidenciaNueva(
        modal,
        []
    );

}