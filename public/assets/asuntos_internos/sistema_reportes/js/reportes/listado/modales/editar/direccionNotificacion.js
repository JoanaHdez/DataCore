let instanciaDireccionNotificacionEditar =
    null;

    
/* =========================================================
   CARGAR DIRECCIÓN EXISTENTE
========================================================= */

export function cargarDireccionNotificacionEditar(
    modal,
    direccion
) {

    if (!modal) {
        return;
    }


    const datos =
        direccion
        && typeof direccion === 'object'
            ? direccion
            : {};


    /* =====================================================
       PERTENECE A NEZAHUALCÓYOTL
    ===================================================== */

    const opcionSi =
        modal.querySelector(
            '#editar-notificacion-pertenece-neza-si'
        );


    const opcionNo =
        modal.querySelector(
            '#editar-notificacion-pertenece-neza-no'
        );


    const perteneceNeza =
        datos.pertenece_neza !== null
        && datos.pertenece_neza !== undefined
        && String(
            datos.pertenece_neza
        ).trim() !== ''
            ? Number(
                datos.pertenece_neza
            )
            : null;


    if (opcionSi) {

        opcionSi.checked =
            perteneceNeza === 1;
    }


    if (opcionNo) {

        opcionNo.checked =
            perteneceNeza === 0;
    }


    /* =====================================================
       DIRECCIÓN
    ===================================================== */

    asignarValor(
        modal,
        '#editar-notificacion-calle',
        datos.calle
    );


    asignarValor(
        modal,
        '#editar-notificacion-numero',
        datos.numero_exterior
    );


    asignarValor(
        modal,
        '#editar-notificacion-colonia',
        datos.colonia
    );


    asignarValor(
        modal,
        '#editar-notificacion-entre-calle',
        datos.entre_calle
    );


    asignarValor(
        modal,
        '#editar-notificacion-y-calle',
        datos.y_calle
    );


    asignarValor(
        modal,
        '#editar-notificacion-municipio',
        datos.municipio
    );


    asignarValor(
        modal,
        '#editar-notificacion-estado',
        datos.estado
    );


    asignarValor(
        modal,
        '#editar-notificacion-sector',
        datos.sector
    );


    asignarValor(
        modal,
        '#editar-notificacion-cuadrante',
        datos.cuadrante
    );


    asignarValor(
        modal,
        '#editar-notificacion-id-cuadra',
        datos.id_cuadra
    );


    /* =====================================================
       COORDENADAS REALES
    ===================================================== */

    asignarValor(
        modal,
        '#editar-notificacion-latitud',
        datos.latitud
    );


    asignarValor(
        modal,
        '#editar-notificacion-longitud',
        datos.longitud
    );


    asignarValor(
        modal,
        '#editar-notificacion-origen-ubicacion',
        datos.origen_ubicacion
    );


    /* =====================================================
       COORDENADAS VISIBLES
    ===================================================== */

    asignarValor(
        modal,
        '#editar-notificacion-latitud-visible',
        datos.latitud
    );


    asignarValor(
        modal,
        '#editar-notificacion-longitud-visible',
        datos.longitud
    );


    const latitud =
        String(
            datos.latitud
            ?? ''
        ).trim();


    const longitud =
        String(
            datos.longitud
            ?? ''
        ).trim();


    asignarValor(
        modal,
        '#editar-notificacion-coordenadas',
        latitud && longitud
            ? `${latitud}, ${longitud}`
            : ''
    );


    /* =====================================================
       BUSCADOR
    ===================================================== */

    asignarValor(
        modal,
        '#editar-notificacion-ubicacion-busqueda',
        ''
    );


    /* =====================================================
       MAPA

       Esperamos un poco porque el modal se abre después
       de cargar los datos en el formulario.
    ===================================================== */

    window.setTimeout(
        () => {

            inicializarDireccionNotificacionEditar(
                modal
            );

        },
        150
    );
}

/* =========================================================
   INICIALIZAR DIRECCIÓN PARA NOTIFICACIÓN
========================================================= */

export function inicializarDireccionNotificacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    /* =====================================================
       ELEMENTOS
    ===================================================== */

    const contenedorMapa =
        modal.querySelector(
            '#editar-notificacion-mapa-ubicacion'
        );


    const inputBusqueda =
        modal.querySelector(
            '#editar-notificacion-ubicacion-busqueda'
        );


    const inputCalle =
        modal.querySelector(
            '#editar-notificacion-calle'
        );


    const inputNumero =
        modal.querySelector(
            '#editar-notificacion-numero'
        );


    const inputColonia =
        modal.querySelector(
            '#editar-notificacion-colonia'
        );


    const inputEntreCalle =
        modal.querySelector(
            '#editar-notificacion-entre-calle'
        );


    const inputYCalle =
        modal.querySelector(
            '#editar-notificacion-y-calle'
        );


    const inputMunicipio =
        modal.querySelector(
            '#editar-notificacion-municipio'
        );


    const inputEstado =
        modal.querySelector(
            '#editar-notificacion-estado'
        );


    const inputSector =
        modal.querySelector(
            '#editar-notificacion-sector'
        );


    const inputCuadrante =
        modal.querySelector(
            '#editar-notificacion-cuadrante'
        );


    const inputIdCuadra =
        modal.querySelector(
            '#editar-notificacion-id-cuadra'
        );


    const inputLatitud =
        modal.querySelector(
            '#editar-notificacion-latitud'
        );


    const inputLongitud =
        modal.querySelector(
            '#editar-notificacion-longitud'
        );


    const inputLatitudVisible =
        modal.querySelector(
            '#editar-notificacion-latitud-visible'
        );


    const inputLongitudVisible =
        modal.querySelector(
            '#editar-notificacion-longitud-visible'
        );


    const inputCoordenadas =
        modal.querySelector(
            '#editar-notificacion-coordenadas'
        );


    const inputOrigen =
        modal.querySelector(
            '#editar-notificacion-origen-ubicacion'
        );


    const opcionNezaSi =
        modal.querySelector(
            '#editar-notificacion-pertenece-neza-si'
        );


    const opcionNezaNo =
        modal.querySelector(
            '#editar-notificacion-pertenece-neza-no'
        );


    if (!contenedorMapa) {
        return;
    }


    /* =====================================================
       ESPERAR GOOGLE MAPS
    ===================================================== */

    if (
        typeof google === 'undefined'
        || !google.maps
    ) {

        window.setTimeout(
            () => {

                inicializarDireccionNotificacionEditar(
                    modal
                );

            },
            300
        );


        return;
    }


    /* =====================================================
       SI YA EXISTE EL MAPA, SOLO ACTUALIZARLO
    ===================================================== */

    if (
        instanciaDireccionNotificacionEditar
        && instanciaDireccionNotificacionEditar.modal === modal
    ) {

        instanciaDireccionNotificacionEditar
            .actualizarDesdeFormulario();


        return;
    }


    /* =====================================================
       CENTRO PREDETERMINADO
    ===================================================== */

    const CENTRO = {

        lat:
            19.40874,

        lng:
            -99.01825,

    };


    /* =====================================================
       CREAR MAPA
    ===================================================== */

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
       UTILIDADES
    ===================================================== */

    function llenarCampo(
        campo,
        valor
    ) {

        if (!campo) {
            return;
        }


        campo.value =
            valor === null
            || valor === undefined
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


    function perteneceANeza() {

        return opcionNezaSi?.checked === true;
    }


    function esForaneo() {

        return opcionNezaNo?.checked === true;
    }


    /* =====================================================
       GUARDAR COORDENADAS
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


        llenarCampo(
            inputLatitud,
            lat
        );


        llenarCampo(
            inputLongitud,
            lng
        );


        llenarCampo(
            inputLatitudVisible,
            lat
        );


        llenarCampo(
            inputLongitudVisible,
            lng
        );


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


            if (encontrado) {

                return (
                    encontrado.long_name
                    ?? ''
                );
            }
        }


        return '';
    }


    /* =====================================================
       GOOGLE MAPS - DIRECCIÓN
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


                    if (
                        !inputCalle?.value
                        && calle
                    ) {

                        llenarCampo(
                            inputCalle,
                            normalizarMayusculas(
                                calle
                            )
                        );
                    }


                    if (numero) {

                        llenarCampo(
                            inputNumero,
                            numero
                        );
                    }


                    if (
                        !inputColonia?.value
                        && colonia
                    ) {

                        llenarCampo(
                            inputColonia,
                            normalizarMayusculas(
                                colonia
                            )
                        );
                    }


                    if (municipio) {

                        llenarCampo(
                            inputMunicipio,
                            normalizarMayusculas(
                                municipio
                            )
                        );
                    }


                    if (estado) {

                        llenarCampo(
                            inputEstado,
                            normalizarMayusculas(
                                estado
                            )
                        );
                    }

                } finally {

                    actualizandoAutomaticamente =
                        false;
                }
            }
        );
    }


    /* =====================================================
       CONSULTAR TERRITORIO
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
                   DENTRO DE NEZA
                ================================================= */

                if (
                    datos.matched
                ) {

                    if (opcionNezaSi) {

                        opcionNezaSi.checked =
                            true;
                    }


                    if (opcionNezaNo) {

                        opcionNezaNo.checked =
                            false;
                    }


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
        }
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
        centrar = true
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
           ¿PEGÓ COORDENADAS?
        ================================================= */

        const coincidencia =
            termino.match(
                /^(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)$/
            );


        if (coincidencia) {

            const valor1 =
                Number(
                    coincidencia[1]
                );


            const valor2 =
                Number(
                    coincidencia[2]
                );


            if (
                valor1 >= -90
                && valor1 <= 90
                && valor2 >= -180
                && valor2 <= 180
            ) {

                seleccionarPunto(
                    valor1,
                    valor2,
                    'busqueda',
                    true
                );


                return;
            }
        }


        /* =================================================
           TEXTO DE DIRECCIÓN
        ================================================= */

        let consulta =
            termino;


        if (
            perteneceANeza()
            && !/nezahualc[oó]yotl/i.test(
                consulta
            )
        ) {

            consulta +=
                ', Nezahualcóyotl, Estado de México, México';
        }


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
                    'busqueda',
                    true
                );
            }
        );
    }


    /* =====================================================
       BUSCAR DESDE CAMPOS MANUALES
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
            municipio,
            estado,
            'México',
        ]
            .filter(Boolean);


        geocoder.geocode(
            {
                address:
                    partes.join(', '),
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
       RADIO: SÍ ES NEZA
    ===================================================== */

    opcionNezaSi?.addEventListener(
        'change',
        () => {

            if (!opcionNezaSi.checked) {
                return;
            }


            if (opcionNezaNo) {

                opcionNezaNo.checked =
                    false;
            }


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
       RADIO: FORÁNEO
    ===================================================== */

    opcionNezaNo?.addEventListener(
        'change',
        () => {

            if (!opcionNezaNo.checked) {
                return;
            }


            if (opcionNezaSi) {

                opcionNezaSi.checked =
                    false;
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
       CAMPOS MANUALES
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
                        window.setTimeout(
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
       ACTUALIZAR MAPA DESDE LOS DATOS GUARDADOS
    ===================================================== */

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


            completarTerritorio(
                latitudInicial,
                longitudInicial,
                ++secuencia
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


        window.setTimeout(
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
            150
        );
    }


    /* =====================================================
       GUARDAR INSTANCIA
    ===================================================== */

    instanciaDireccionNotificacionEditar = {

        modal,

        actualizarDesdeFormulario,

    };


    /* =====================================================
       CARGAR POSICIÓN EXISTENTE
    ===================================================== */

    actualizarDesdeFormulario();
}

/* =========================================================
   ASIGNAR VALOR
========================================================= */

function asignarValor(
    modal,
    selector,
    valor
) {

    if (!modal) {
        return;
    }


    const campo =
        modal.querySelector(
            selector
        );


    if (!campo) {
        return;
    }


    campo.value =
        valor === null
        || valor === undefined
            ? ''
            : String(
                valor
            );
}