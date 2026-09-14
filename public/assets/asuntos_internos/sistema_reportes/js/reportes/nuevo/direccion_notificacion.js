/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   NUEVO REPORTE
   DIRECCIÓN PARA NOTIFICACIÓN
========================================================= */


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarDireccionNotificacion() {

    /* =====================================================
       ELEMENTOS
    ===================================================== */

    const contenedorMapa =
        document.querySelector(
            '#notificacion-mapa-ubicacion'
        );


    const inputBusqueda =
        document.querySelector(
            '#notificacion-ubicacion-busqueda'
        );


    const inputCalle =
        document.querySelector(
            '#notificacion-calle'
        );


    const inputNumero =
        document.querySelector(
            '#notificacion-numero'
        );


    const inputColonia =
        document.querySelector(
            '#notificacion-colonia'
        );


    const inputEntreCalle =
        document.querySelector(
            '#notificacion-entre-calle'
        );


    const inputYCalle =
        document.querySelector(
            '#notificacion-y-calle'
        );


    const inputMunicipio =
        document.querySelector(
            '#notificacion-municipio'
        );


    const inputEstado =
        document.querySelector(
            '#notificacion-estado'
        );


    const inputSector =
        document.querySelector(
            '#notificacion-sector'
        );


    const inputCuadrante =
        document.querySelector(
            '#notificacion-cuadrante'
        );


    const inputIdCuadra =
        document.querySelector(
            '#notificacion-id-cuadra'
        );


    const inputLatitud =
        document.querySelector(
            '#notificacion-latitud'
        );


    const inputLongitud =
        document.querySelector(
            '#notificacion-longitud'
        );


    const inputLatitudVisible =
        document.querySelector(
            '#notificacion-latitud-visible'
        );


    const inputLongitudVisible =
        document.querySelector(
            '#notificacion-longitud-visible'
        );


    const inputCoordenadas =
        document.querySelector(
            '#notificacion-coordenadas'
        );


    const inputOrigen =
        document.querySelector(
            '#notificacion-origen-ubicacion'
        );


    /* =====================================================
       PERTENECE A NEZAHUALCÓYOTL
    ===================================================== */

    const opcionNezaSi =
        document.querySelector(
            '#notificacion-pertenece-neza-si'
        );


    const opcionNezaNo =
        document.querySelector(
            '#notificacion-pertenece-neza-no'
        );


    if (!contenedorMapa) {
        return;
    }


    /* =====================================================
       VALIDAR GOOGLE MAPS
    ===================================================== */

    if (
        typeof google === 'undefined'
        || !google.maps
    ) {

        console.error(
            'Google Maps no se encuentra disponible para la dirección de notificación.'
        );


        return;
    }


    /* =====================================================
       CONFIGURACIÓN
    ===================================================== */

    const CENTRO = {

        lat:
            19.40874,

        lng:
            -99.01825,

    };


    const mapa =
        new google.maps.Map(
            contenedorMapa,
            {

                center:
                    CENTRO,

                zoom:
                    16,

                mapTypeControl:
                    false,

                streetViewControl:
                    false,

                fullscreenControl:
                    true,

                clickableIcons:
                    false,

                gestureHandling:
                    'greedy',

            }
        );


    const geocoder =
        new google.maps.Geocoder();


    const marcador =
        new google.maps.Marker(
            {

                map:
                    mapa,

                visible:
                    false,

                draggable:
                    true,

                title:
                    'Dirección para notificación',

            }
        );


    let secuencia =
        0;


    let actualizandoAutomaticamente =
        false;


    let temporizadorManual =
        null;


    /* =====================================================
       TIPO DE DIRECCIÓN
    ===================================================== */

    function perteneceANeza() {

        return (
            opcionNezaSi?.checked
            === true
        );
    }


    function esForaneo() {

        return (
            opcionNezaNo?.checked
            === true
        );
    }


    /* =====================================================
       CAMBIO: PERTENECE A NEZA
    ===================================================== */

    opcionNezaSi?.addEventListener(
        'change',
        () => {

            if (!opcionNezaSi.checked) {
                return;
            }


            actualizandoAutomaticamente =
                true;


            try {

                /*
                 * Solo colocamos estos valores automáticamente
                 * cuando todavía están vacíos.
                 */

                if (
                    inputMunicipio
                    && inputMunicipio.value.trim() === ''
                ) {

                    llenarCampo(
                        inputMunicipio,
                        'NEZAHUALCÓYOTL'
                    );
                }


                if (
                    inputEstado
                    && inputEstado.value.trim() === ''
                ) {

                    llenarCampo(
                        inputEstado,
                        'ESTADO DE MÉXICO'
                    );
                }


                /*
                 * Si antes estaba como foráneo,
                 * limpiamos territorio para permitir
                 * volver a consultar Nezahualcóyotl.
                 */

                if (
                    normalizarMayusculas(
                        inputSector?.value
                    ) === 'FORÁNEO'
                ) {

                    llenarCampo(
                        inputSector,
                        ''
                    );
                }


                if (
                    normalizarMayusculas(
                        inputCuadrante?.value
                    ) === 'FORÁNEO'
                ) {

                    llenarCampo(
                        inputCuadrante,
                        ''
                    );
                }


                llenarCampo(
                    inputIdCuadra,
                    ''
                );

            } finally {

                actualizandoAutomaticamente =
                    false;
            }


            /*
             * Si ya tenemos coordenadas,
             * volvemos a consultar el territorio.
             */

            const latitud =
                Number(
                    inputLatitud?.value
                );


            const longitud =
                Number(
                    inputLongitud?.value
                );


            if (
                inputLatitud?.value
                && inputLongitud?.value
                && !Number.isNaN(
                    latitud
                )
                && !Number.isNaN(
                    longitud
                )
            ) {

                completarTerritorio(
                    latitud,
                    longitud,
                    ++secuencia
                );
            }
        }
    );


    /* =====================================================
       CAMBIO: FORÁNEO
    ===================================================== */

    opcionNezaNo?.addEventListener(
        'change',
        () => {

            if (!opcionNezaNo.checked) {
                return;
            }


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
    );


    /* =====================================================
       CLIC EN MAPA
    ===================================================== */

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


    /* =====================================================
       ARRASTRAR MARCADOR
    ===================================================== */

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


    /* =====================================================
       BUSCADOR
    ===================================================== */

    inputBusqueda?.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key !== 'Enter'
            ) {
                return;
            }


            evento.preventDefault();


            buscarDireccion();
        }
    );


    /* =====================================================
       CAPTURA MANUAL
    ===================================================== */

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

                    if (
                        actualizandoAutomaticamente
                    ) {
                        return;
                    }


                    establecerOrigen(
                        'manual'
                    );


                    if (
                        temporizadorManual
                    ) {

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


    /* =====================================================
       BUSCAR DIRECCIÓN
    ===================================================== */

    function buscarDireccion() {

        const termino =
            inputBusqueda?.value.trim()
            ?? '';


        if (!termino) {
            return;
        }


        /* =================================================
           DETECTAR COORDENADAS
        ================================================= */

        const coincidenciaCoordenadas =
            termino.match(
                /^(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)$/
            );


        if (
            coincidenciaCoordenadas
        ) {

            const valor1 =
                Number(
                    coincidenciaCoordenadas[1]
                );


            const valor2 =
                Number(
                    coincidenciaCoordenadas[2]
                );


            let latitud =
                null;


            let longitud =
                null;


            /* =============================================
               LATITUD, LONGITUD
            ============================================= */

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


            /* =============================================
               LONGITUD, LATITUD
            ============================================= */

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


        /* =================================================
           BÚSQUEDA POR DIRECCIÓN
        ================================================= */

        let consulta =
            termino;


        /*
         * Si pertenece a Nezahualcóyotl,
         * hacemos exactamente lo mismo que el mapa principal:
         * delimitamos la búsqueda al municipio.
         */

        if (
            perteneceANeza()
            && !/nezahualc[oó]yotl/i.test(
                consulta
            )
        ) {

            consulta +=
                ', Nezahualcóyotl, Estado de México, México';
        }


        /*
         * Si es foráneo no agregamos Neza.
         * Google Maps buscará libremente.
         */


        geocoder.geocode(
            {
                address:
                    consulta,
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
                        'No se encontró la dirección para notificación.'
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


    /* =====================================================
       BÚSQUEDA DESDE CAMPOS MANUALES
    ===================================================== */

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


        const partes = [

            calle,
            numero,
            colonia,

        ];


        /* =================================================
           DIRECCIÓN DE NEZA
        ================================================= */

        if (
            perteneceANeza()
        ) {

            partes.push(
                municipio
                || 'Nezahualcóyotl'
            );


            partes.push(
                estado
                || 'Estado de México'
            );

        } else {

            /* =================================================
               DIRECCIÓN FORÁNEA
            ================================================= */

            if (municipio) {

                partes.push(
                    municipio
                );
            }


            if (estado) {

                partes.push(
                    estado
                );
            }
        }


        partes.push(
            'México'
        );


        const consulta =
            partes
                .filter(Boolean)
                .join(', ');


        geocoder.geocode(
            {
                address:
                    consulta,
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


    /* =====================================================
       LIMPIAR DATOS AUTOMÁTICOS
    ===================================================== */

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


            if (
                esForaneo()
            ) {

                llenarCampo(
                    inputSector,
                    'FORÁNEO'
                );


                llenarCampo(
                    inputCuadrante,
                    'FORÁNEO'
                );

            } else {

                llenarCampo(
                    inputSector,
                    ''
                );


                llenarCampo(
                    inputCuadrante,
                    ''
                );
            }


            llenarCampo(
                inputIdCuadra,
                ''
            );

        } finally {

            actualizandoAutomaticamente =
                false;
        }
    }


    /* =====================================================
    SELECCIONAR PUNTO
    ===================================================== */

    function seleccionarPunto(
        latitud,
        longitud,
        origen,
        centrar
    ) {

        const posicion = {

            lat:
                Number(
                    latitud
                ),

            lng:
                Number(
                    longitud
                ),

        };


        if (
            Number.isNaN(
                posicion.lat
            )
            || Number.isNaN(
                posicion.lng
            )
        ) {

            return;
        }


        /*
        * Si viene del buscador o del mapa,
        * limpiamos la ubicación anterior.
        */

        if (
            origen === 'busqueda'
            || origen === 'mapa'
        ) {

            limpiarDatosUbicacionAutomatica();
        }


        const miSecuencia =
            ++secuencia;


        /* =================================================
        COORDENADAS
        ================================================= */

        guardarCoordenadas(
            posicion.lat,
            posicion.lng
        );


        establecerOrigen(
            origen
        );


        /* =================================================
        MARCADOR
        ================================================= */

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


        /* =================================================
        GOOGLE MAPS
        ================================================= */

        completarGoogle(
            posicion,
            miSecuencia
        );


        /* =================================================
        BASE TERRITORIAL

        La consultamos SIEMPRE.

        Esto permite que pegar coordenadas determine
        automáticamente si el punto pertenece o no
        a Nezahualcóyotl.
        ================================================= */

        completarTerritorio(
            posicion.lat,
            posicion.lng,
            miSecuencia
        );
    }


    /* =====================================================
       GOOGLE MAPS
       DOMICILIO POSTAL
    ===================================================== */

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
                     * Igual que en el mapa principal:
                     * Google sirve principalmente para
                     * número, municipio y estado.
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


    /* =====================================================
    BASE TERRITORIAL
    ===================================================== */

    async function completarTerritorio(
        latitud,
        longitud,
        miSecuencia
    ) {

        try {

            const url =
                new URL(
                    'DataCore/public/asuntos-internos/reportes/ubicacion/territorio',
                    `${window.location.origin}/`
                );


            url.searchParams.set(
                'lat',
                String(
                    latitud
                )
            );


            url.searchParams.set(
                'lng',
                String(
                    longitud
                )
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

                /* =================================================
                PERTENECE A NEZAHUALCÓYOTL
                ================================================= */

                if (
                    datos.matched
                ) {

                    /*
                    * Marcamos automáticamente "Sí".
                    */

                    if (opcionNezaSi) {

                        opcionNezaSi.checked =
                            true;
                    }


                    if (opcionNezaNo) {

                        opcionNezaNo.checked =
                            false;
                    }


                    /* =============================================
                    SECTOR
                    ============================================= */

                    llenarCampo(
                        inputSector,
                        normalizarMayusculas(
                            datos.sector
                        )
                    );


                    /* =============================================
                    CUADRANTE
                    ============================================= */

                    llenarCampo(
                        inputCuadrante,
                        normalizarMayusculas(
                            datos.cuadrante
                        )
                    );


                    /* =============================================
                    ID CUADRA
                    ============================================= */

                    llenarCampo(
                        inputIdCuadra,
                        datos.id_cuadra
                        ?? ''
                    );


                    /* =============================================
                    CALLE
                    ============================================= */

                    if (
                        datos.calle
                    ) {

                        llenarCampo(
                            inputCalle,
                            normalizarMayusculas(
                                datos.calle
                            )
                        );
                    }


                    /* =============================================
                    COLONIA
                    ============================================= */

                    if (
                        datos.colonia
                    ) {

                        llenarCampo(
                            inputColonia,
                            normalizarMayusculas(
                                datos.colonia
                            )
                        );
                    }


                    /* =============================================
                    ENTRE CALLE
                    ============================================= */

                    if (
                        datos.entre_calle
                    ) {

                        llenarCampo(
                            inputEntreCalle,
                            normalizarMayusculas(
                                datos.entre_calle
                            )
                        );
                    }


                    /* =============================================
                    Y CALLE
                    ============================================= */

                    if (
                        datos.y_calle
                    ) {

                        llenarCampo(
                            inputYCalle,
                            normalizarMayusculas(
                                datos.y_calle
                            )
                        );
                    }


                    /*
                    * Como sabemos que sí pertenece al municipio,
                    * garantizamos estos valores si Google no
                    * logró identificarlos correctamente.
                    */

                    if (
                        !inputMunicipio?.value.trim()
                    ) {

                        llenarCampo(
                            inputMunicipio,
                            'NEZAHUALCÓYOTL'
                        );
                    }


                    if (
                        !inputEstado?.value.trim()
                    ) {

                        llenarCampo(
                            inputEstado,
                            'ESTADO DE MÉXICO'
                        );
                    }


                    return;
                }


                /* =================================================
                FORÁNEO
                ================================================= */

                /*
                * La base territorial indicó que el punto
                * no pertenece a Nezahualcóyotl.
                */

                if (opcionNezaSi) {

                    opcionNezaSi.checked =
                        false;
                }


                if (opcionNezaNo) {

                    opcionNezaNo.checked =
                        true;
                }


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

            } finally {

                actualizandoAutomaticamente =
                    false;
            }

        } catch (error) {

            console.error(
                'Error consultando información territorial para notificación:',
                error
            );


            /*
            * No eliminamos la dirección obtenida
            * mediante Google Maps.
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


    /* =====================================================
       COMPONENTE GOOGLE
    ===================================================== */

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


            if (
                encontrado
            ) {

                return (
                    encontrado.long_name
                    ?? ''
                );
            }
        }


        return '';
    }


    /* =====================================================
       COORDENADAS
    ===================================================== */

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


        /* =================================================
           CAMPOS REALES
        ================================================= */

        llenarCampo(
            inputLatitud,
            lat
        );


        llenarCampo(
            inputLongitud,
            lng
        );


        /* =================================================
           CAMPOS VISIBLES
        ================================================= */

        llenarCampo(
            inputLongitudVisible,
            lng
        );


        llenarCampo(
            inputLatitudVisible,
            lat
        );


        /* =================================================
           COORDENADAS
        ================================================= */

        llenarCampo(
            inputCoordenadas,
            `${lat}, ${lng}`
        );
    }


    /* =====================================================
       ORIGEN
    ===================================================== */

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


    /* =====================================================
       UTILIDADES
    ===================================================== */

    function llenarCampo(
        campo,
        valor
    ) {

        if (
            !campo
        ) {
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
            valor
            ?? ''
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


    /* =====================================================
       COORDENADAS EXISTENTES
    ===================================================== */

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

        seleccionarPunto(
            latitudInicial,
            longitudInicial,
            inputOrigen?.value
            || 'manual',
            true
        );
    }
}