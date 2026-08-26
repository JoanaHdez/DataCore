document.addEventListener('DOMContentLoaded', () => {

    /* =========================================================
       ELEMENTOS
    ========================================================= */

    const contenedorMapa =
        document.querySelector('#mapa-ubicacion');

    const inputBusqueda =
        document.querySelector('#ubicacion_busqueda');

    if (!contenedorMapa || typeof L === 'undefined') {
        return;
    }


    /* =========================================================
       CONFIGURACIÓN INICIAL
    ========================================================= */

    const coordenadasNeza = [
        19.4006,
        -99.0148
    ];

    const mapa = L
        .map(contenedorMapa)
        .setView(
            coordenadasNeza,
            13
        );


    /* =========================================================
       OPENSTREETMAP
    ========================================================= */

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution:
                '&copy; OpenStreetMap contributors'
        }
    ).addTo(mapa);


    /* =========================================================
       MARCADOR
    ========================================================= */

    let marcador = null;


    /* =========================================================
       CLIC EN MAPA
    ========================================================= */

    mapa.on('click', async (evento) => {

        const latitud =
            evento.latlng.lat;

        const longitud =
            evento.latlng.lng;

        await seleccionarUbicacion(
            latitud,
            longitud,
            true
        );

    });


    /* =========================================================
       BUSCADOR
       Enter para buscar
    ========================================================= */

    inputBusqueda?.addEventListener(
        'keydown',
        async (evento) => {

            if (evento.key !== 'Enter') {
                return;
            }

            evento.preventDefault();

            const termino =
                inputBusqueda.value.trim();

            if (!termino) {
                return;
            }

            await buscarUbicacion(
                termino
            );

        }
    );


    /* =========================================================
       SELECCIONAR UBICACIÓN
    ========================================================= */

    async function seleccionarUbicacion(
        latitud,
        longitud,
        centrarMapa = false
    ) {

        if (!marcador) {

            marcador = L.marker(
                [latitud, longitud]
            ).addTo(mapa);

        } else {

            marcador.setLatLng(
                [latitud, longitud]
            );

        }


        if (centrarMapa) {

            mapa.panTo(
                [latitud, longitud]
            );

        }


        guardarCoordenadas(
            latitud,
            longitud
        );


        await obtenerDireccionDesdeCoordenadas(
            latitud,
            longitud
        );

    }


    /* =========================================================
       BUSCAR DIRECCIÓN
    ========================================================= */

    async function buscarUbicacion(
        termino
    ) {

        try {

            /*
             * Agregamos Nezahualcóyotl para dar prioridad
             * a resultados dentro del municipio.
             */
            const consulta =
                `${termino}, Nezahualcóyotl, Estado de México, México`;

            const url =
                'https://nominatim.openstreetmap.org/search'
                + '?format=jsonv2'
                + '&addressdetails=1'
                + '&limit=1'
                + '&countrycodes=mx'
                + `&q=${encodeURIComponent(consulta)}`;


            const respuesta = await fetch(
                url,
                {
                    headers: {
                        'Accept-Language': 'es'
                    }
                }
            );


            if (!respuesta.ok) {

                throw new Error(
                    'No fue posible buscar la ubicación.'
                );

            }


            const resultados =
                await respuesta.json();


            if (
                !Array.isArray(resultados)
                || resultados.length === 0
            ) {

                console.warn(
                    'No se encontraron resultados para:',
                    termino
                );

                return;

            }


            const resultado =
                resultados[0];


            const latitud =
                Number(resultado.lat);

            const longitud =
                Number(resultado.lon);


            if (
                Number.isNaN(latitud)
                || Number.isNaN(longitud)
            ) {
                return;
            }


            mapa.setView(
                [latitud, longitud],
                17
            );


            await seleccionarUbicacion(
                latitud,
                longitud
            );

        } catch (error) {

            console.error(
                'Error buscando ubicación:',
                error
            );

        }

    }


    /* =========================================================
       GUARDAR COORDENADAS
    ========================================================= */

    function guardarCoordenadas(
        latitud,
        longitud
    ) {

        const inputLatitud =
            document.querySelector(
                '#latitud'
            );

        const inputLongitud =
            document.querySelector(
                '#longitud'
            );


        if (inputLatitud) {

            inputLatitud.value =
                Number(latitud).toFixed(7);

        }


        if (inputLongitud) {

            inputLongitud.value =
                Number(longitud).toFixed(7);

        }

    }


    /* =========================================================
       AJUSTAR MAPA
    ========================================================= */

    window.setTimeout(() => {

        mapa.invalidateSize();

    }, 100);

});


/* =============================================================
   GEOCODIFICACIÓN INVERSA
   Coordenadas -> dirección
============================================================= */

async function obtenerDireccionDesdeCoordenadas(
    latitud,
    longitud
) {

    try {

        const url =
            'https://nominatim.openstreetmap.org/reverse'
            + '?format=jsonv2'
            + `&lat=${encodeURIComponent(latitud)}`
            + `&lon=${encodeURIComponent(longitud)}`
            + '&addressdetails=1'
            + '&zoom=18';


        const respuesta = await fetch(
            url,
            {
                headers: {
                    'Accept-Language': 'es'
                }
            }
        );


        if (!respuesta.ok) {

            throw new Error(
                'No fue posible consultar la dirección.'
            );

        }


        const datos =
            await respuesta.json();


        const direccion =
            datos.address ?? {};


        /* =====================================================
           DIRECCIÓN COMPLETA
        ===================================================== */

        llenarCampo(
            '#ubicacion',
            datos.display_name ?? ''
        );


        /* =====================================================
           CALLE
        ===================================================== */

        llenarCampo(
            '#calle',

            direccion.road
                ?? direccion.pedestrian
                ?? direccion.residential
                ?? direccion.footway
                ?? direccion.path
                ?? direccion.cycleway
                ?? ''
        );


        /* =====================================================
           NÚMERO EXTERIOR
        ===================================================== */

        llenarCampo(
            '#numero',
            direccion.house_number
                ?? ''
        );


        /* =====================================================
           COLONIA
        ===================================================== */

        llenarCampo(
            '#colonia',

            direccion.neighbourhood
                ?? direccion.suburb
                ?? direccion.quarter
                ?? direccion.residential
                ?? ''
        );


        /* =====================================================
           CIUDAD / MUNICIPIO
        ===================================================== */

        llenarCampo(
            '#municipio',

            direccion.city
                ?? direccion.town
                ?? direccion.municipality
                ?? direccion.city_district
                ?? direccion.county
                ?? ''
        );


        /* =====================================================
           ESTADO
        ===================================================== */

        llenarCampo(
            '#estado',
            direccion.state
                ?? ''
        );


        /*
         * Estos campos se conservan manuales por ahora:
         *
         * #entre_calle
         * #y_calle
         * #sector
         * #cuadrante
         *
         * Nominatim no garantiza esa información y Sector /
         * Cuadrante necesitan la cartografía institucional.
         */

    } catch (error) {

        console.error(
            'Error obteniendo dirección:',
            error
        );

    }

}


/* =============================================================
   LLENAR CAMPO
============================================================= */

function llenarCampo(
    selector,
    valor
) {

    const campo =
        document.querySelector(selector);

    if (!campo) {
        return;
    }

    campo.value =
        valor ?? '';

}