document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarQuejosoIndividualDashboard();

    }
);


/* =========================================================
   QUEJOSO INDIVIDUAL
========================================================= */

function inicializarQuejosoIndividualDashboard() {

    const inputBusqueda =
        document.querySelector(
            '#dashboard-quejoso-individual-busqueda'
        );


    const inputValor =
        document.querySelector(
            '#dashboard-quejoso-individual-valor'
        );


    const contenedorResultados =
        document.querySelector(
            '#dashboard-quejoso-individual-resultados'
        );


    const contenedorSeleccion =
        document.querySelector(
            '#dashboard-quejoso-individual-seleccion'
        );


    const textoSeleccion =
        document.querySelector(
            '#dashboard-quejoso-individual-seleccion-texto'
        );


    const botonQuitar =
        document.querySelector(
            '#dashboard-quejoso-individual-quitar'
        );


    const botonesMotivos =
        document.querySelectorAll(
            '[data-quejoso-individual-motivos]'
        );


    if (
        !inputBusqueda
        || !inputValor
        || !contenedorResultados
        || !contenedorSeleccion
        || !textoSeleccion
        || !botonQuitar
    ) {

        return;
    }


    let temporizadorBusqueda =
        null;


    let controladorBusqueda =
        null;


    inputBusqueda.addEventListener(
        'input',
        () => {

            const termino =
                normalizarBusquedaQuejoso()
                    .trim();


            if (temporizadorBusqueda) {

                clearTimeout(
                    temporizadorBusqueda
                );
            }


            if (termino === '') {

                ocultarResultados();

                return;
            }


            temporizadorBusqueda =
                window.setTimeout(
                    () => {

                        buscarQuejosos(
                            termino
                        );

                    },
                    300
                );

        }
    );


    async function buscarQuejosos(
        termino
    ) {

        if (
            inputBusqueda.disabled
        ) {

            return;
        }


        if (controladorBusqueda) {

            controladorBusqueda.abort();
        }


        controladorBusqueda =
            new AbortController();


        try {

            const url =
                new URL(
                    'DataCore/public/asuntos-internos/reportes/dashboard/quejosos/buscar',
                    `${window.location.origin}/`
                );


            agregarFiltrosGlobales(
                url
            );


            url.searchParams.set(
                'q',
                termino
            );


            const respuesta =
                await fetch(
                    url.toString(),
                    {
                        method: 'GET',

                        headers: {
                            Accept:
                                'application/json',
                        },

                        signal:
                            controladorBusqueda.signal,
                    }
                );


            if (!respuesta.ok) {

                throw new Error(
                    'No fue posible consultar los quejosos.'
                );
            }


            const datos =
                await respuesta.json();


            renderizarResultados(
                Array.isArray(
                    datos.quejosos
                )
                    ? datos.quejosos
                    : []
            );

        } catch (error) {

            if (
                error.name === 'AbortError'
            ) {

                return;
            }


            console.error(
                'Error buscando quejosos:',
                error
            );


            mostrarMensaje(
                'No fue posible consultar los quejosos.'
            );
        }
    }


    function renderizarResultados(
        quejosos
    ) {

        contenedorResultados.innerHTML =
            '';


        if (!quejosos.length) {

            mostrarMensaje(
                'Sin coincidencias.'
            );

            return;
        }


        quejosos.forEach(
            quejoso => {

                const boton =
                    document.createElement(
                        'button'
                    );


                boton.type =
                    'button';


                boton.className =
                    'dashboard-quejoso-resultados__item';


                const nombre =
                    String(
                        quejoso.nombre
                        || 'Sin nombre'
                    ).trim();


                const clave =
                    String(
                        quejoso.clave
                        || ''
                    ).trim();


                const totalQuejas =
                    Number(
                        quejoso.total_quejas
                    ) || 0;


                const inicial =
                    obtenerInicialQuejoso(
                        nombre
                    );


                boton.innerHTML = `
                    <span class="dashboard-quejoso-resultados__avatar">
                        ${escaparHtmlQuejoso(inicial)}
                    </span>

                    <span class="dashboard-quejoso-resultados__datos">

                        <strong>
                            ${escaparHtmlQuejoso(nombre)}
                        </strong>

                        <small>
                            ${escaparHtmlQuejoso(
                                String(totalQuejas)
                            )}
                            quejas
                        </small>

                    </span>
                `;


                boton.addEventListener(
                    'click',
                    () => {

                        seleccionarQuejoso(
                            {
                                clave,
                            }
                        );

                    }
                );


                contenedorResultados.appendChild(
                    boton
                );

            }
        );


        contenedorResultados.hidden =
            false;
    }


    function seleccionarQuejoso(
        quejoso
    ) {

        const clave =
            String(
                quejoso.clave
                || ''
            ).trim();


        if (
            clave === ''
        ) {

            return;
        }


        inputValor.value =
            clave;


        const url =
            new URL(
                window.location.href
            );


        url.searchParams.set(
            'quejoso_individual',
            clave
        );


        window.location.href =
            url.toString();
    }


    botonQuitar.addEventListener(
        'click',
        () => {

            const url =
                new URL(
                    window.location.href
                );


            url.searchParams.delete(
                'quejoso_individual'
            );


            window.location.href =
                url.toString();

        }
    );


    if (
        botonesMotivos.length
    ) {

        botonesMotivos.forEach(
            boton => {

                boton.addEventListener(
                    'click',
                    () => {

                        const idReporte =
                            String(
                                boton.dataset.quejosoIndividualMotivos
                                || ''
                            ).trim();


                        abrirModalMotivosQuejoso(
                            idReporte
                        );
                    }
                );
            }
        );


        document.addEventListener(
            'click',
            evento => {

                if (
                    evento.target.closest(
                        '[data-quejoso-individual-motivos-cerrar]'
                    )
                ) {

                    cerrarModalMotivosQuejoso(
                        evento.target.closest(
                            '.modal-quejoso-individual-motivos'
                        )
                    );
                }
            }
        );


        document.addEventListener(
            'keydown',
            evento => {

                if (
                    evento.key !== 'Escape'
                ) {

                    return;
                }


                const modalVisible =
                    document.querySelector(
                        '.modal-quejoso-individual-motivos.modal-reporte--visible'
                    );


                if (
                    modalVisible
                ) {

                    cerrarModalMotivosQuejoso(
                        modalVisible
                    );
                }
            }
        );
    }


    function abrirModalMotivosQuejoso(
        idReporte
    ) {

        if (
            idReporte === ''
        ) {

            return;
        }


        const modal =
            document.getElementById(
                `modal-quejoso-individual-motivos-${idReporte}`
            );


        if (
            !modal
        ) {

            return;
        }


        modal.classList.add(
            'modal-reporte--visible'
        );


        modal.setAttribute(
            'aria-hidden',
            'false'
        );


        document.body.classList.add(
            'modal-abierto'
        );
    }


    function cerrarModalMotivosQuejoso(
        modal
    ) {

        if (
            !modal
        ) {

            return;
        }


        modal.classList.remove(
            'modal-reporte--visible'
        );


        modal.setAttribute(
            'aria-hidden',
            'true'
        );


        if (
            !document.querySelector(
                '.modal-reporte.modal-reporte--visible'
            )
        ) {

            document.body.classList.remove(
                'modal-abierto'
            );
        }
    }


    document.addEventListener(
        'click',
        evento => {

            if (
                evento.target === inputBusqueda
                || contenedorResultados.contains(
                    evento.target
                )
            ) {

                return;
            }


            ocultarResultados();

        }
    );


    function normalizarBusquedaQuejoso() {

        const valorOriginal =
            inputBusqueda.value;


        const valorMayusculas =
            valorOriginal.toLocaleUpperCase(
                'es-MX'
            );


        if (
            valorOriginal === valorMayusculas
        ) {

            return valorMayusculas;
        }


        const inicioSeleccion =
            inputBusqueda.selectionStart;


        const finSeleccion =
            inputBusqueda.selectionEnd;


        inputBusqueda.value =
            valorMayusculas;


        if (
            inicioSeleccion !== null
            && finSeleccion !== null
            && typeof inputBusqueda.setSelectionRange === 'function'
        ) {

            inputBusqueda.setSelectionRange(
                inicioSeleccion,
                finSeleccion
            );
        }


        return valorMayusculas;
    }


    function agregarFiltrosGlobales(
        url
    ) {

        const parametrosFiltro = [
            'fecha_registro_inicio',
            'fecha_registro_fin',
            'tipo',
            'estado',
            'clasificacion',
            'seguimiento',
            'es_anonimo',
            'zona',
            'sector',
            'turno',
            'area_personal',
            'personal',
            'unidad',
        ];


        const parametrosActuales =
            new URLSearchParams(
                window.location.search
            );


        parametrosFiltro.forEach(
            parametro => {

                const valor =
                    parametrosActuales.get(
                        parametro
                    );


                if (
                    valor !== null
                    && valor.trim() !== ''
                ) {

                    url.searchParams.set(
                        parametro,
                        valor
                    );
                }

            }
        );
    }


    function mostrarMensaje(
        mensaje
    ) {

        contenedorResultados.innerHTML = `
            <div class="dashboard-quejoso-resultados__vacio">
                ${escaparHtmlQuejoso(mensaje)}
            </div>
        `;


        contenedorResultados.hidden =
            false;
    }


    function ocultarResultados() {

        contenedorResultados.hidden =
            true;


        contenedorResultados.innerHTML =
            '';
    }
}


function obtenerInicialQuejoso(
    nombre
) {

    const texto =
        String(
            nombre || ''
        ).trim();


    if (
        texto === ''
    ) {

        return '?';
    }


    return texto
        .charAt(0)
        .toLocaleUpperCase(
            'es-MX'
        );
}


function escaparHtmlQuejoso(
    valor
) {

    return String(
        valor
        ?? ''
    )
        .replaceAll(
            '&',
            '&amp;'
        )
        .replaceAll(
            '<',
            '&lt;'
        )
        .replaceAll(
            '>',
            '&gt;'
        )
        .replaceAll(
            '"',
            '&quot;'
        )
        .replaceAll(
            "'",
            '&#039;'
        );
}
