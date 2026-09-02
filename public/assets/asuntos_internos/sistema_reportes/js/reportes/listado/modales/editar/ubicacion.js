/* =============================================================
   EDITAR REPORTE
   UBICACIÓN
   GOOGLE MAPS + BASE TERRITORIAL
============================================================= */

let instanciaUbicacionEditar = null;


/* =============================================================
   INICIALIZAR UBICACIÓN EDITAR
============================================================= */

export function inicializarUbicacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    /* =========================================================
       ELEMENTOS
    ========================================================= */

    const contenedorMapa =
        modal.querySelector(
            '#editar-mapa-ubicacion'
        );

    const inputBusqueda =
        modal.querySelector(
            '#editar-ubicacion-busqueda'
        );

    const inputCalle =
        modal.querySelector(
            '#editar-calle'
        );

    const inputNumero =
        modal.querySelector(
            '#editar-numero'
        );

    const inputColonia =
        modal.querySelector(
            '#editar-colonia'
        );

    const inputEntreCalle =
        modal.querySelector(
            '#editar-entre-calle'
        );

    const inputYCalle =
        modal.querySelector(
            '#editar-y-calle'
        );

    const inputMunicipio =
        modal.querySelector(
            '#editar-municipio'
        );

    const inputEstado =
        modal.querySelector(
            '#editar-estado'
        );

    const inputSector =
        modal.querySelector(
            '#editar-sector'
        );

    const inputCuadrante =
        modal.querySelector(
            '#editar-cuadrante'
        );

    const inputIdCuadra =
        modal.querySelector(
            '#editar-id-cuadra'
        );

    const inputLatitud =
        modal.querySelector(
            '#editar-latitud'
        );

    const inputLongitud =
        modal.querySelector(
            '#editar-longitud'
        );

    const inputLatitudVisible =
        modal.querySelector(
            '#editar-latitud-visible'
        );

    const inputLongitudVisible =
        modal.querySelector(
            '#editar-longitud-visible'
        );

    const inputCoordenadas =
        modal.querySelector(
            '#editar-coordenadas'
        );

    const inputOrigen =
        modal.querySelector(
            '#editar-origen-ubicacion'
        );


    if (!contenedorMapa) {
        return;
    }


    /* =========================================================
       VALIDAR GOOGLE MAPS
    ========================================================= */

    if (
        typeof google === 'undefined'
        || !google.maps
    ) {

        setTimeout(
            () => {

                inicializarUbicacionEditar(
                    modal
                );

            },
            300
        );

        return;
    }


    /* =========================================================
       SI YA EXISTE INSTANCIA
       SOLO ACTUALIZAR POSICIÓN
    ========================================================= */

    if (
        instanciaUbicacionEditar
        && instanciaUbicacionEditar.modal === modal
    ) {

        instanciaUbicacionEditar
            .actualizarDesdeFormulario();

        return;
    }


    /* =========================================================
       CONFIGURACIÓN
    ========================================================= */

    const CENTRO = {
        lat: 19.40874,
        lng: -99.01825,
    };


    const mapa =
        new google.maps.Map(
            contenedorMapa,
            {
                center: CENTRO,
                zoom: 16,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                clickableIcons: false,
                gestureHandling: 'greedy',
            }
        );


    const geocoder =
        new google.maps.Geocoder();


    const marcador =
        new google.maps.Marker(
            {
                map: mapa,
                visible: false,
                draggable: true,
                title: 'Ubicación de los hechos',
            }
        );


    let secuencia =
        0;

    let actualizandoAutomaticamente =
        false;

    let temporizadorManual =
        null;


    /* =========================================================
       REDIBUJAR MAPA AL MOSTRAR SECCIÓN
    ========================================================= */

    window.addEventListener(
        'editar:ubicacion-visible',
        () => {

            setTimeout(
                () => {

                    google.maps.event.trigger(
                        mapa,
                        'resize'
                    );


                    if (
                        marcador.getVisible()
                        && marcador.getPosition()
                    ) {

                        mapa.setCenter(
                            marcador.getPosition()
                        );

                    }

                },
                100
            );

        }
    );


    /* =========================================================
       CLIC EN MAPA
    ========================================================= */

    mapa.addListener(
        'click',
        (evento) => {

            if (!evento.latLng) {
                return;
            }


            seleccionarPunto(
                evento.latLng.lat(),
                evento.latLng.lng(),
                'mapa',
                false
            );

        }
    );


    /* =========================================================
       ARRASTRAR MARCADOR
    ========================================================= */

    marcador.addListener(
        'dragend',
        (evento) => {

            if (!evento.latLng) {
                return;
            }


            seleccionarPunto(
                evento.latLng.lat(),
                evento.latLng.lng(),
                'mapa',
                false
            );

        }
    );


    /* =========================================================
       BUSCADOR
    ========================================================= */

    inputBusqueda?.addEventListener(
        'keydown',
        (evento) => {

            if (evento.key !== 'Enter') {
                return;
            }


            evento.preventDefault();

            buscarDireccion();

        }
    );


    /* =========================================================
       CAPTURA MANUAL
    ========================================================= */

    const camposManual = [
        inputCalle,
        inputNumero,
        inputColonia,
        inputMunicipio,
        inputEstado,
    ];


    camposManual.forEach(
        (campo) => {

            campo?.addEventListener(
                'input',
                () => {

                    if (actualizandoAutomaticamente) {
                        return;
                    }


                    establecerOrigen(
                        'manual'
                    );


                    if (temporizadorManual) {

                        clearTimeout(
                            temporizadorManual
                        );

                    }


                    temporizadorManual =
                        setTimeout(
                            () => {

                                buscarDireccionManual();

                            },
                            900
                        );

                }
            );

        }
    );


    /* =========================================================
       BUSCAR DIRECCIÓN
    ========================================================= */

    function buscarDireccion() {

        const termino =
            inputBusqueda?.value.trim()
            ?? '';


        if (!termino) {
            return;
        }


        /* =====================================================
           DETECTAR COORDENADAS

           LATITUD, LONGITUD
           19.4332090, -98.9486140

           LONGITUD, LATITUD
           -98.9486140, 19.4332090
        ===================================================== */

        const coincidenciaCoordenadas =
            termino.match(
                /^(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)$/
            );


        if (coincidenciaCoordenadas) {

            const valor1 =
                Number(
                    coincidenciaCoordenadas[1]
                );

            const valor2 =
                Number(
                    coincidenciaCoordenadas[2]
                );


            let latitud = null;
            let longitud = null;


            if (
                valor1 >= -90
                && valor1 <= 90
                && valor2 >= -180
                && valor2 <= 180
            ) {

                latitud =
                    valor1;

                longitud =
                    valor2;

            }


            if (
                (
                    valor1 < -90
                    || valor1 > 90
                )
                && valor1 >= -180
                && valor1 <= 180
                && valor2 >= -90
                && valor2 <= 90
            ) {

                longitud =
                    valor1;

                latitud =
                    valor2;

            }


            if (
                latitud !== null
                && longitud !== null
            ) {

                seleccionarPunto(
                    latitud,
                    longitud,
                    'busqueda',
                    true
                );

                return;
            }

        }


        /* =====================================================
           BÚSQUEDA NORMAL POR DIRECCIÓN
        ===================================================== */

        let consulta =
            termino;


        if (
            !/nezahualc[oó]yotl/i.test(
                consulta
            )
        ) {

            consulta +=
                ', Nezahualcóyotl, Estado de México, México';

        }


        geocoder.geocode(
            {
                address: consulta,
            },
            (
                resultados,
                status
            ) => {

                if (
                    status !== 'OK'
                    || !resultados
                    || !resultados[0]
                ) {

                    console.warn(
                        'No se encontró la dirección.'
                    );

                    return;
                }


                const ubicacion =
                    resultados[0]
                        .geometry
                        .location;


                seleccionarPunto(
                    ubicacion.lat(),
                    ubicacion.lng(),
                    'busqueda',
                    true
                );

            }
        );

    }


    /* =========================================================
       BÚSQUEDA DESDE CAMPOS MANUALES
    ========================================================= */

    function buscarDireccionManual() {

        const calle =
            inputCalle?.value.trim()
            ?? '';

        const numero =
            inputNumero?.value.trim()
            ?? '';

        const colonia =
            inputColonia?.value.trim()
            ?? '';

        const municipio =
            inputMunicipio?.value.trim()
            ?? '';

        const estado =
            inputEstado?.value.trim()
            ?? '';


        if (
            calle === ''
            || (
                colonia === ''
                && municipio === ''
            )
        ) {
            return;
        }


        const consulta =
            [
                calle,
                numero,
                colonia,
                municipio || 'Nezahualcóyotl',
                estado || 'Estado de México',
                'México',
            ]
                .filter(Boolean)
                .join(', ');


        geocoder.geocode(
            {
                address: consulta,
            },
            (
                resultados,
                status
            ) => {

                if (
                    status !== 'OK'
                    || !resultados
                    || !resultados[0]
                ) {
                    return;
                }


                const ubicacion =
                    resultados[0]
                        .geometry
                        .location;


                seleccionarPunto(
                    ubicacion.lat(),
                    ubicacion.lng(),
                    'manual',
                    true
                );

            }
        );

    }


    /* =========================================================
       LIMPIAR DATOS AUTOMÁTICOS
    ========================================================= */

    function limpiarDatosUbicacionAutomatica() {

        actualizandoAutomaticamente =
            true;


        try {

            llenarCampo(
                inputCalle,
                ''
            );

            llenarCampo(
                inputNumero,
                ''
            );

            llenarCampo(
                inputColonia,
                ''
            );

            llenarCampo(
                inputEntreCalle,
                ''
            );

            llenarCampo(
                inputYCalle,
                ''
            );

            llenarCampo(
                inputMunicipio,
                ''
            );

            llenarCampo(
                inputEstado,
                ''
            );

            llenarCampo(
                inputSector,
                ''
            );

            llenarCampo(
                inputCuadrante,
                ''
            );

            llenarCampo(
                inputIdCuadra,
                ''
            );

        } finally {

            actualizandoAutomaticamente =
                false;

        }

    }


    /* =========================================================
       SELECCIONAR PUNTO
    ========================================================= */

    function seleccionarPunto(
        latitud,
        longitud,
        origen,
        centrar
    ) {

        const posicion = {
            lat: Number(latitud),
            lng: Number(longitud),
        };


        if (
            Number.isNaN(posicion.lat)
            || Number.isNaN(posicion.lng)
        ) {
            return;
        }


        /*
         * Si la ubicación viene del buscador
         * o directamente del mapa, eliminamos
         * los datos automáticos de la ubicación
         * anterior.
         *
         * Para captura manual NO limpiamos,
         * porque borraríamos lo escrito por
         * el usuario.
         */

        if (
            origen === 'busqueda'
            || origen === 'mapa'
        ) {

            limpiarDatosUbicacionAutomatica();

        }


        const miSecuencia =
            ++secuencia;


        guardarCoordenadas(
            posicion.lat,
            posicion.lng
        );


        establecerOrigen(
            origen
        );


        marcador.setPosition(
            posicion
        );

        marcador.setVisible(
            true
        );


        if (centrar) {

            mapa.panTo(
                posicion
            );

            mapa.setZoom(
                18
            );

        }


        completarGoogle(
            posicion,
            miSecuencia
        );


        completarTerritorio(
            posicion.lat,
            posicion.lng,
            miSecuencia
        );

    }


    /* =========================================================
       GOOGLE MAPS
       DOMICILIO POSTAL
    ========================================================= */

    function completarGoogle(
        posicion,
        miSecuencia
    ) {

        geocoder.geocode(
            {
                location:
                    posicion,
            },
            (
                resultados,
                status
            ) => {

                if (
                    miSecuencia !== secuencia
                    || status !== 'OK'
                    || !resultados
                    || !resultados[0]
                ) {
                    return;
                }


                const resultado =
                    resultados[0];


                const componentes =
                    resultado.address_components
                    ?? [];


                const calle =
                    obtenerComponente(
                        componentes,
                        [
                            'route',
                        ]
                    );


                const numero =
                    obtenerComponente(
                        componentes,
                        [
                            'street_number',
                        ]
                    );


                const colonia =
                    obtenerComponente(
                        componentes,
                        [
                            'sublocality_level_1',
                            'sublocality_level_2',
                            'sublocality',
                            'neighborhood',
                        ]
                    );


                const municipio =
                    obtenerComponente(
                        componentes,
                        [
                            'locality',
                            'administrative_area_level_2',
                            'administrative_area_level_3',
                        ]
                    );


                const estado =
                    obtenerComponente(
                        componentes,
                        [
                            'administrative_area_level_1',
                        ]
                    );


                actualizandoAutomaticamente =
                    true;


                try {

                    if (
                        inputBusqueda
                        && resultado.formatted_address
                    ) {

                        inputBusqueda.value =
                            resultado.formatted_address;

                    }


                    /*
                     * Google llena principalmente:
                     *
                     * número
                     * municipio
                     * estado
                     *
                     * Calle y colonia pueden ser reemplazadas
                     * posteriormente por la base territorial.
                     */

                    if (
                        !inputCalle?.value
                        && calle
                    ) {

                        llenarCampo(
                            inputCalle,
                            calle
                        );

                    }


                    llenarCampo(
                        inputNumero,
                        numero
                    );


                    if (
                        !inputColonia?.value
                        && colonia
                    ) {

                        llenarCampo(
                            inputColonia,
                            colonia
                        );

                    }


                    llenarCampo(
                        inputMunicipio,
                        municipio
                    );


                    llenarCampo(
                        inputEstado,
                        estado
                    );

                } finally {

                    actualizandoAutomaticamente =
                        false;

                }

            }
        );

    }


    /* =========================================================
       BASE TERRITORIAL
    ========================================================= */

    async function completarTerritorio(
        latitud,
        longitud,
        miSecuencia
    ) {

        try {

            /* const base =
                document.querySelector(
                    'base'
                )?.href
                || window.location.origin + '/';


            const url =
                new URL(
                    'asuntos-internos/reportes/ubicacion/territorio',
                    base
                ); */

const url =
    new URL(
        'DataCore/public/asuntos-internos/reportes/ubicacion/territorio',
        `${window.location.origin}/`
    );

            url.searchParams.set(
                'lat',
                String(latitud)
            );


            url.searchParams.set(
                'lng',
                String(longitud)
            );


            const respuesta =
                await fetch(
                    url.toString(),
                    {
                        headers: {
                            Accept:
                                'application/json',
                        },

                        credentials:
                            'same-origin',
                    }
                );


            const datos =
                await respuesta.json();


            if (
                miSecuencia !== secuencia
            ) {
                return;
            }


            if (!respuesta.ok) {

                throw new Error(
                    datos?.message
                    || 'No fue posible consultar el territorio.'
                );

            }


            actualizandoAutomaticamente =
                true;


            try {

                if (datos.matched) {

                    llenarCampo(
                        inputSector,
                        normalizarMayusculas(
                            datos.sector
                        )
                    );


                    llenarCampo(
                        inputCuadrante,
                        normalizarMayusculas(
                            datos.cuadrante
                        )
                    );


                    llenarCampo(
                        inputIdCuadra,
                        datos.id_cuadra
                        ?? ''
                    );


                    if (datos.calle) {

                        llenarCampo(
                            inputCalle,
                            normalizarMayusculas(
                                datos.calle
                            )
                        );

                    }


                    if (datos.colonia) {

                        llenarCampo(
                            inputColonia,
                            normalizarMayusculas(
                                datos.colonia
                            )
                        );

                    }


                    if (datos.entre_calle) {

                        llenarCampo(
                            inputEntreCalle,
                            normalizarMayusculas(
                                datos.entre_calle
                            )
                        );

                    }


                    if (datos.y_calle) {

                        llenarCampo(
                            inputYCalle,
                            normalizarMayusculas(
                                datos.y_calle
                            )
                        );

                    }

                } else {

                    llenarCampo(
                        inputSector,
                        'FORÁNEO'
                    );


                    llenarCampo(
                        inputCuadrante,
                        'FORÁNEO'
                    );


                    llenarCampo(
                        inputIdCuadra,
                        ''
                    );

                }

            } finally {

                actualizandoAutomaticamente =
                    false;

            }

        } catch (error) {

            console.error(
                'Error consultando información territorial:',
                error
            );


            /*
             * Conservamos el domicilio obtenido de Google.
             * Solo limpiamos los datos territoriales.
             */

            llenarCampo(
                inputSector,
                ''
            );


            llenarCampo(
                inputCuadrante,
                ''
            );


            llenarCampo(
                inputIdCuadra,
                ''
            );

        }

    }


    /* =========================================================
       COMPONENTE GOOGLE
    ========================================================= */

    function obtenerComponente(
        componentes,
        tipos
    ) {

        for (
            const tipo
            of tipos
        ) {

            const encontrado =
                componentes.find(
                    (componente) =>
                        Array.isArray(
                            componente.types
                        )
                        && componente.types.includes(
                            tipo
                        )
                );


            if (encontrado) {

                return (
                    encontrado.long_name
                    ?? ''
                );

            }

        }


        return '';

    }


    /* =========================================================
       COORDENADAS
    ========================================================= */

    function guardarCoordenadas(
        latitud,
        longitud
    ) {

        const lat =
            Number(
                latitud
            ).toFixed(
                7
            );


        const lng =
            Number(
                longitud
            ).toFixed(
                7
            );


        /* =====================================================
           CAMPOS QUE SE GUARDAN
        ===================================================== */

        llenarCampo(
            inputLatitud,
            lat
        );


        llenarCampo(
            inputLongitud,
            lng
        );


        /* =====================================================
           CAMPOS VISIBLES

           X = LONGITUD
           Y = LATITUD
        ===================================================== */

        llenarCampo(
            inputLongitudVisible,
            lng
        );


        llenarCampo(
            inputLatitudVisible,
            lat
        );


        /* =====================================================
           COORDENADAS

           LATITUD, LONGITUD
        ===================================================== */

        llenarCampo(
            inputCoordenadas,
            `${lat}, ${lng}`
        );

    }


    /* =========================================================
       ORIGEN
    ========================================================= */

    function establecerOrigen(
        origen
    ) {

        const permitidos = [
            'manual',
            'busqueda',
            'mapa',
        ];


        llenarCampo(
            inputOrigen,
            permitidos.includes(
                origen
            )
                ? origen
                : 'manual'
        );

    }


    /* =========================================================
       ACTUALIZAR DESDE FORMULARIO
    ========================================================= */

    function actualizarDesdeFormulario() {

    const latitudInicial =
        Number(
            inputLatitud?.value
        );

    const longitudInicial =
        Number(
            inputLongitud?.value
        );


    if (
        inputLatitud?.value
        && inputLongitud?.value
        && !Number.isNaN(
            latitudInicial
        )
        && !Number.isNaN(
            longitudInicial
        )
    ) {

        const posicion = {
            lat:
                latitudInicial,

            lng:
                longitudInicial,
        };


        marcador.setPosition(
            posicion
        );

        marcador.setVisible(
            true
        );


        mapa.setCenter(
            posicion
        );

        mapa.setZoom(
            18
        );


        guardarCoordenadas(
            latitudInicial,
            longitudInicial
        );


        const miSecuencia =
            ++secuencia;


        completarTerritorio(
            latitudInicial,
            longitudInicial,
            miSecuencia
        );


    } else {

        marcador.setVisible(
            false
        );


        mapa.setCenter(
            CENTRO
        );

        mapa.setZoom(
            16
        );

    }


    redibujarMapa();

}


    /* =========================================================
       REDIBUJAR MAPA
    ========================================================= */

    function redibujarMapa() {

        setTimeout(
            () => {

                google.maps.event.trigger(
                    mapa,
                    'resize'
                );


                if (
                    marcador.getVisible()
                    && marcador.getPosition()
                ) {

                    mapa.setCenter(
                        marcador.getPosition()
                    );

                }

            },
            100
        );

    }


    /* =========================================================
       UTILIDADES
    ========================================================= */

    function llenarCampo(
        campo,
        valor
    ) {

        if (!campo) {
            return;
        }


        campo.value =
            valor == null
                ? ''
                : String(
                    valor
                );

    }


    function normalizarMayusculas(
        valor
    ) {

        return String(
            valor ?? ''
        )
            .replace(
                /\s+/g,
                ' '
            )
            .trim()
            .toLocaleUpperCase(
                'es-MX'
            );

    }


    /* =========================================================
       GUARDAR INSTANCIA
    ========================================================= */

    instanciaUbicacionEditar = {

        modal,

        actualizarDesdeFormulario,

        redibujarMapa,

    };


    /* =========================================================
       POSICIÓN INICIAL
    ========================================================= */

    actualizarDesdeFormulario();

}