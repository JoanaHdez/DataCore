/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Resumen
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarResumenFelicitaciones();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarResumenFelicitaciones() {

    const totalFelicitaciones =
        document.querySelector(
            '#resumen-total-felicitaciones'
        );


    const personalFelicitado =
        document.querySelector(
            '#resumen-personal-felicitado'
        );


    const areasFelicitaciones =
        document.querySelector(
            '#resumen-areas-felicitaciones'
        );


    const tbody =
        document.querySelector(
            '#tabla-felicitaciones-body'
        );


    if (
        !totalFelicitaciones
        || !personalFelicitado
        || !areasFelicitaciones
        || !tbody
    ) {
        return;
    }


    /* =====================================================
       ACTUALIZAR RESUMEN
    ===================================================== */

    function actualizarResumen(
        filas
    ) {

        const filasValidas =
            Array.isArray(
                filas
            )
                ? filas
                : [];


        const personasUnicas =
            new Set();


        const areasUnicas =
            new Set();


        /* =================================================
           RECORRER FELICITACIONES VISIBLES
        ================================================= */

        filasValidas.forEach(
            (fila) => {

                /* =========================================
                   PERSONAL ÚNICO
                ========================================== */

                const idsPersonal =
                    separarValores(
                        fila.dataset.personalIds
                    );


                const perscods =
                    separarValores(
                        fila.dataset.perscods
                    );


                /*
                 * Primero usamos plantilla_id.
                 * Si por algún motivo no existe,
                 * usamos perscod como respaldo.
                 */

                if (
                    idsPersonal.length > 0
                ) {

                    idsPersonal.forEach(
                        (id) => {

                            personasUnicas.add(
                                `id:${id}`
                            );
                        }
                    );

                } else {

                    perscods.forEach(
                        (perscod) => {

                            personasUnicas.add(
                                `perscod:${perscod}`
                            );
                        }
                    );
                }


                /* =========================================
                   ÁREAS ÚNICAS
                ========================================== */

                const areas =
                    separarValores(
                        fila.dataset.areas
                    );


                areas.forEach(
                    (area) => {

                        areasUnicas.add(
                            normalizarTexto(
                                area
                            )
                        );
                    }
                );
            }
        );


        /* =================================================
           MOSTRAR RESULTADOS
        ================================================= */

        totalFelicitaciones.textContent =
            String(
                filasValidas.length
            );


        personalFelicitado.textContent =
            String(
                personasUnicas.size
            );


        areasFelicitaciones.textContent =
            String(
                areasUnicas.size
            );
    }


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    const filasIniciales =
        Array.from(
            tbody.querySelectorAll(
                'tr'
            )
        ).filter(
            (fila) => {

                return (
                    !fila.classList.contains(
                        'felicitaciones-tabla__empty'
                    )
                    && !fila.classList.contains(
                        'felicitaciones-tabla__empty--filtros'
                    )
                );
            }
        );


    actualizarResumen(
        filasIniciales
    );


    /* =====================================================
       RECIBIR RESULTADOS DE FILTROS
    ===================================================== */

    document.addEventListener(
        'felicitacionesFiltradasActualizadas',
        (evento) => {

            const filas =
                Array.isArray(
                    evento.detail?.filas
                )
                    ? evento.detail.filas
                    : [];


            actualizarResumen(
                filas
            );
        }
    );
}


/* =========================================================
   SEPARAR VALORES
========================================================= */

function separarValores(
    valor
) {

    return String(
        valor
        || ''
    )
        .split(
            '|'
        )
        .map(
            (item) =>
                String(
                    item
                    || ''
                ).trim()
        )
        .filter(
            Boolean
        );
}


/* =========================================================
   NORMALIZAR TEXTO
========================================================= */

function normalizarTexto(
    texto
) {

    return String(
        texto
        || ''
    )
        .trim()
        .toLowerCase()
        .normalize(
            'NFD'
        )
        .replace(
            /[\u0300-\u036f]/g,
            ''
        );
}