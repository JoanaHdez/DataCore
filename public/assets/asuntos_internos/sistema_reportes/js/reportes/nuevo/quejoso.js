/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   NUEVO REPORTE
   DATOS DEL QUEJOSO
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGeneroQuejoso();

        inicializarCanalizacionQuejoso();

        inicializarEstadoMunicipioQuejoso();

        inicializarColoniaQuejosoMayusculas();

        inicializarQuejosoAnonimo();
    }
);

/* =========================================================
   GÉNERO
========================================================= */

function inicializarGeneroQuejoso() {

    const selector =
        document.querySelector(
            '#genero-select'
        );


    const textoSelector =
        document.querySelector(
            '#genero-select-texto'
        );


    const inputGenero =
        document.querySelector(
            '#genero'
        );


    const resultados =
        document.querySelector(
            '#genero-resultados'
        );


    const opciones =
        document.querySelectorAll(
            '[data-genero-opcion]'
        );


    if (
        !selector
        || !textoSelector
        || !inputGenero
        || !resultados
    ) {
        return;
    }


    /* =====================================================
       ABRIR / CERRAR
    ===================================================== */

    function abrirCatalogo() {

        if (
            selector.disabled
        ) {
            return;
        }


        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'genero-select--activo'
        );
    }


    function cerrarCatalogo() {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'genero-select--activo'
        );
    }


    /* =====================================================
       SELECCIONAR GÉNERO
    ===================================================== */

    function seleccionarGenero(
        valor
    ) {

        const genero =
            String(
                valor
                || ''
            ).trim();


        inputGenero.value =
            genero;


        textoSelector.textContent =
            genero !== ''
                ? genero
                : 'Selecciona una opción';


        cerrarCatalogo();
    }


    /* =====================================================
       CLICK SELECTOR
    ===================================================== */

    selector.addEventListener(
        'click',
        () => {

            if (
                resultados.hidden
            ) {

                abrirCatalogo();

            } else {

                cerrarCatalogo();
            }
        }
    );


    /* =====================================================
       OPCIONES
    ===================================================== */

    opciones.forEach(
        (opcion) => {

            opcion.addEventListener(
                'click',
                () => {

                    seleccionarGenero(
                        opcion.dataset.genero
                    );
                }
            );
        }
    );


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                selector.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrarCatalogo();
        }
    );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
            ) {

                cerrarCatalogo();
            }
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    seleccionarGenero(
        inputGenero.value
    );
}


/* =========================================================
   CANALIZACIÓN
========================================================= */

function ajustarAlturaCatalogoCanalizacion(
    resultados
) {

    if (!resultados) {
        return;
    }


    const opciones =
        Array.from(
            resultados.querySelectorAll(
                '.canalizacion-resultados__item'
            )
        )
        .filter(
            (opcion) => !opcion.hidden
        );


    if (
        opciones.length <= 4
    ) {

        resultados.style.maxHeight =
            '';

        resultados.classList.remove(
            'canalizacion-resultados--scroll'
        );

        return;
    }


    const alturaPrimerasOpciones =
        opciones
            .slice(
                0,
                4
            )
            .reduce(
                (total, opcion) => total
                    + opcion.getBoundingClientRect().height,
                0
            );


    resultados.style.maxHeight =
        `${Math.ceil(alturaPrimerasOpciones + 2)}px`;


    resultados.classList.add(
        'canalizacion-resultados--scroll'
    );
}


function normalizarBusquedaCanalizacion(
    texto
) {

    return String(
        texto
        || ''
    )
        .normalize(
            'NFD'
        )
        .replace(
            /[\u0300-\u036f]/g,
            ''
        )
        .toUpperCase()
        .trim();
}


function seleccionarTextoCanalizacion(
    elemento
) {

    if (!elemento) {
        return;
    }


    const seleccion =
        window.getSelection();


    if (!seleccion) {
        return;
    }


    const rango =
        document.createRange();


    rango.selectNodeContents(
        elemento
    );


    seleccion.removeAllRanges();


    seleccion.addRange(
        rango
    );
}


function obtenerPosicionCursorCanalizacion(
    elemento
) {

    const seleccion =
        window.getSelection();


    if (
        !seleccion
        || seleccion.rangeCount === 0
    ) {
        return 0;
    }


    const rango =
        seleccion.getRangeAt(
            0
        );


    const previo =
        rango.cloneRange();


    previo.selectNodeContents(
        elemento
    );


    previo.setEnd(
        rango.endContainer,
        rango.endOffset
    );


    return previo.toString().length;
}


function restaurarCursorCanalizacion(
    elemento,
    posicion
) {

    const texto =
        elemento.firstChild;


    if (!texto) {
        return;
    }


    const seleccion =
        window.getSelection();


    if (!seleccion) {
        return;
    }


    const rango =
        document.createRange();


    rango.setStart(
        texto,
        Math.min(
            posicion,
            texto.textContent.length
        )
    );


    rango.collapse(
        true
    );


    seleccion.removeAllRanges();


    seleccion.addRange(
        rango
    );
}


function convertirBusquedaCanalizacionAMayusculas(
    elemento
) {

    const texto =
        elemento.textContent
        || '';


    const mayusculas =
        texto.toLocaleUpperCase(
            'es-MX'
        );


    if (
        texto === mayusculas
    ) {
        return mayusculas;
    }


    const posicion =
        obtenerPosicionCursorCanalizacion(
            elemento
        );


    elemento.textContent =
        mayusculas;


    restaurarCursorCanalizacion(
        elemento,
        posicion
    );


    return mayusculas;
}


function inicializarCanalizacionQuejoso() {

    const selector =
        document.querySelector(
            '#canalizacion-select'
        );


    const textoSelector =
        document.querySelector(
            '#canalizacion-select-texto'
        );


    const inputCanalizacion =
        document.querySelector(
            '#canalizacion'
        );


    const resultados =
        document.querySelector(
            '#canalizacion-resultados'
        );


    const opciones =
        document.querySelectorAll(
            '[data-canalizacion-opcion]'
        );


    const contenedorOtro =
        document.querySelector(
            '#canalizacion-otro-contenedor'
        );


    const inputOtro =
        document.querySelector(
            '#canalizacion_otro'
        );


    if (
        !selector
        || !textoSelector
        || !inputCanalizacion
        || !resultados
        || !contenedorOtro
        || !inputOtro
    ) {
        return;
    }


    const opcionesCanalizacion =
        Array.from(
            opciones
        );


    const mensajeSinCoincidencias =
        document.createElement(
            'div'
        );


    mensajeSinCoincidencias.className =
        'canalizacion-resultados__vacio';


    mensajeSinCoincidencias.textContent =
        'Sin coincidencias';


    mensajeSinCoincidencias.hidden =
        true;


    resultados.appendChild(
        mensajeSinCoincidencias
    );


    function obtenerTextoSeleccionado() {

        const valor =
            String(
                inputCanalizacion.value
                || ''
            ).trim();


        return valor !== ''
            ? valor
            : 'Sin canalización';
    }


    function filtrarCanalizaciones(
        busqueda
    ) {

        const termino =
            normalizarBusquedaCanalizacion(
                busqueda
            );


        let totalVisibles =
            0;


        opcionesCanalizacion.forEach(
            (opcion) => {

                const nombre =
                    opcion.dataset.canalizacionNombre
                    || opcion.textContent
                    || '';


                const coincide =
                    termino === ''
                    || normalizarBusquedaCanalizacion(
                        nombre
                    ).includes(
                        termino
                    );


                opcion.hidden =
                    !coincide;


                if (coincide) {
                    totalVisibles += 1;
                }
            }
        );


        mensajeSinCoincidencias.hidden =
            totalVisibles > 0;


        ajustarAlturaCatalogoCanalizacion(
            resultados
        );
    }


    /* =====================================================
       ABRIR / CERRAR
    ===================================================== */

    let busquedaActiva =
        false;


    function abrirCatalogo(
        busqueda = ''
    ) {

        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'canalizacion-select--activo'
        );


        filtrarCanalizaciones(
            busqueda
        );


        ajustarAlturaCatalogoCanalizacion(
            resultados
        );
    }


    function cerrarCatalogo(
        restaurarTexto = true
    ) {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'canalizacion-select--activo'
        );


        if (restaurarTexto) {

            textoSelector.textContent =
                obtenerTextoSeleccionado();

            busquedaActiva =
                false;


            filtrarCanalizaciones(
                ''
            );
        }
    }


    /* =====================================================
       SELECCIONAR
    ===================================================== */

    function seleccionarCanalizacion(
        valor
    ) {

        const nombre =
            String(
                valor
                || ''
            ).trim();


        inputCanalizacion.value =
            nombre;


        textoSelector.textContent =
            nombre !== ''
                ? nombre
                : 'Sin canalización';


        const esOtro =
            nombre === 'Otro';


        contenedorOtro.hidden =
            !esOtro;


        inputOtro.disabled =
            !esOtro;


        inputOtro.required =
            esOtro;


        if (
            !esOtro
        ) {

            inputOtro.value =
                '';
        }


        busquedaActiva =
            false;


        cerrarCatalogo(
            false
        );
    }


    /* =====================================================
       CLICK SELECTOR
    ===================================================== */

    selector.addEventListener(
        'click',
        (evento) => {

            if (
                evento.target === textoSelector
            ) {

                abrirCatalogo();

                requestAnimationFrame(
                    () => seleccionarTextoCanalizacion(
                        textoSelector
                    )
                );

                return;
            }

            if (
                resultados.hidden
            ) {

                abrirCatalogo();

            } else {

                cerrarCatalogo();
            }
        }
    );


    textoSelector.addEventListener(
        'focus',
        () => {

            abrirCatalogo();


            requestAnimationFrame(
                () => seleccionarTextoCanalizacion(
                    textoSelector
                )
            );
        }
    );


    textoSelector.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Enter'
            ) {

                evento.preventDefault();
            }
        }
    );


    textoSelector.addEventListener(
        'click',
        () => {

            requestAnimationFrame(
                () => seleccionarTextoCanalizacion(
                    textoSelector
                )
            );
        }
    );


    textoSelector.addEventListener(
        'input',
        () => {

            const busqueda =
                convertirBusquedaCanalizacionAMayusculas(
                    textoSelector
                );


            busquedaActiva =
                true;


            abrirCatalogo(
                busqueda
            );


            filtrarCanalizaciones(
                busqueda
            );
        }
    );


    /* =====================================================
       OPCIONES
    ===================================================== */

    opciones.forEach(
        (opcion) => {

            opcion.addEventListener(
                'click',
                () => {

                    seleccionarCanalizacion(
                        opcion.dataset.canalizacionNombre
                    );
                }
            );
        }
    );


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                selector.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrarCatalogo();
        }
    );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
            ) {

                cerrarCatalogo();
            }
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    seleccionarCanalizacion(
        ''
    );
}


/* =========================================================
   ESTADO / MUNICIPIO DEL QUEJOSO
========================================================= */

function ajustarAlturaCatalogoQuejoso(
    resultados
) {

    if (!resultados) {
        return;
    }


    const opciones =
        Array.from(
            resultados.querySelectorAll(
                '.quejoso-catalogo-resultados__item'
            )
        );


    if (
        opciones.length <= 4
    ) {

        resultados.style.maxHeight =
            '';

        resultados.classList.remove(
            'quejoso-catalogo-resultados--scroll'
        );

        return;
    }


    const alturaPrimerasOpciones =
        opciones
            .slice(
                0,
                4
            )
            .reduce(
                (total, opcion) => total
                    + opcion.getBoundingClientRect().height,
                0
            );


    resultados.style.maxHeight =
        `${Math.ceil(alturaPrimerasOpciones + 2)}px`;


    resultados.classList.add(
        'quejoso-catalogo-resultados--scroll'
    );
}


function normalizarBusquedaQuejosoCatalogo(
    texto
) {

    return String(
        texto
        || ''
    )
        .normalize(
            'NFD'
        )
        .replace(
            /[\u0300-\u036f]/g,
            ''
        )
        .toLocaleUpperCase(
            'es-MX'
        )
        .trim();
}


function seleccionarTextoQuejosoCatalogo(
    elemento
) {

    if (!elemento) {
        return;
    }


    const seleccion =
        window.getSelection();


    if (!seleccion) {
        return;
    }


    const rango =
        document.createRange();


    rango.selectNodeContents(
        elemento
    );


    seleccion.removeAllRanges();


    seleccion.addRange(
        rango
    );
}


function obtenerPosicionCursorQuejosoCatalogo(
    elemento
) {

    const seleccion =
        window.getSelection();


    if (
        !seleccion
        || seleccion.rangeCount === 0
    ) {
        return 0;
    }


    const rango =
        seleccion.getRangeAt(
            0
        );


    const previo =
        rango.cloneRange();


    previo.selectNodeContents(
        elemento
    );


    previo.setEnd(
        rango.endContainer,
        rango.endOffset
    );


    return previo.toString().length;
}


function restaurarCursorQuejosoCatalogo(
    elemento,
    posicion
) {

    const texto =
        elemento.firstChild;


    if (!texto) {
        return;
    }


    const seleccion =
        window.getSelection();


    if (!seleccion) {
        return;
    }


    const rango =
        document.createRange();


    rango.setStart(
        texto,
        Math.min(
            posicion,
            texto.textContent.length
        )
    );


    rango.collapse(
        true
    );


    seleccion.removeAllRanges();


    seleccion.addRange(
        rango
    );
}


function convertirTextoQuejosoCatalogoAMayusculas(
    elemento
) {

    const texto =
        elemento.textContent
        || '';


    const mayusculas =
        texto.toLocaleUpperCase(
            'es-MX'
        );


    if (
        texto === mayusculas
    ) {
        return mayusculas;
    }


    const posicion =
        obtenerPosicionCursorQuejosoCatalogo(
            elemento
        );


    elemento.textContent =
        mayusculas;


    restaurarCursorQuejosoCatalogo(
        elemento,
        posicion
    );


    return mayusculas;
}


function convertirInputQuejosoAMayusculas(
    input
) {

    if (!input) {
        return;
    }


    const valor =
        input.value
        || '';


    const mayusculas =
        valor.toLocaleUpperCase(
            'es-MX'
        );


    if (
        valor === mayusculas
    ) {
        return;
    }


    const inicio =
        input.selectionStart;

    const fin =
        input.selectionEnd;


    input.value =
        mayusculas;


    if (
        inicio !== null
        && fin !== null
    ) {

        input.setSelectionRange(
            inicio,
            fin
        );
    }
}


function construirUrlCatalogoQuejoso(
    ruta
) {

    return new URL(
        `DataCore/public/asuntos-internos/reportes/${ruta}`,
        `${window.location.origin}/`
    );
}


function obtenerInicialCatalogoQuejoso(
    texto
) {

    const limpio =
        String(
            texto
            || ''
        ).trim();


    return limpio !== ''
        ? limpio.charAt(0).toLocaleUpperCase('es-MX')
        : '-';
}


function crearSelectorCatalogoQuejoso(
    configuracion
) {

    const selector =
        document.querySelector(
            configuracion.selector
        );


    const textoSelector =
        document.querySelector(
            configuracion.texto
        );


    const inputValor =
        document.querySelector(
            configuracion.input
        );


    const inputId =
        configuracion.inputId
            ? document.querySelector(
                configuracion.inputId
            )
            : null;


    const resultados =
        document.querySelector(
            configuracion.resultados
        );


    if (
        !selector
        || !textoSelector
        || !inputValor
        || !resultados
    ) {
        return null;
    }


    let opciones =
        [];


    let deshabilitado =
        selector.disabled;


    function obtenerTextoSeleccionado() {

        const valor =
            String(
                inputValor.value
                || ''
            ).trim();


        return valor !== ''
            ? valor
            : configuracion.placeholder;
    }


    function renderizarOpciones(
        lista
    ) {

        resultados.innerHTML =
            '';


        if (
            lista.length === 0
        ) {

            const vacio =
                document.createElement(
                    'div'
                );


            vacio.className =
                'quejoso-catalogo-resultados__vacio';


            vacio.textContent =
                'Sin coincidencias';


            resultados.appendChild(
                vacio
            );


            ajustarAlturaCatalogoQuejoso(
                resultados
            );

            return;
        }


        lista.forEach(
            (opcion) => {

                const boton =
                    document.createElement(
                        'button'
                    );


                boton.type =
                    'button';


                boton.className =
                    'quejoso-catalogo-resultados__item';


                boton.dataset.id =
                    String(
                        opcion.id
                        ?? ''
                    );


                boton.dataset.nombre =
                    String(
                        opcion.nombre
                        ?? ''
                    );


                boton.innerHTML =
                    `<span class="quejoso-catalogo-resultados__avatar">${obtenerInicialCatalogoQuejoso(opcion.nombre)}</span>
                    <span class="quejoso-catalogo-resultados__datos">
                        <strong></strong>
                        <small>${configuracion.descripcion}</small>
                    </span>`;


                const nombre =
                    boton.querySelector(
                        'strong'
                    );


                if (nombre) {
                    nombre.textContent =
                        opcion.nombre;
                }


                boton.addEventListener(
                    'click',
                    () => seleccionar(
                        opcion
                    )
                );


                resultados.appendChild(
                    boton
                );
            }
        );


        requestAnimationFrame(
            () => ajustarAlturaCatalogoQuejoso(
                resultados
            )
        );
    }


    function filtrar(
        busqueda = ''
    ) {

        const termino =
            normalizarBusquedaQuejosoCatalogo(
                busqueda
            );


        const filtradas =
            termino === ''
                ? opciones
                : opciones.filter(
                    (opcion) => normalizarBusquedaQuejosoCatalogo(
                        opcion.nombre
                    ).includes(
                        termino
                    )
                );


        renderizarOpciones(
            filtradas
        );
    }


    function abrir(
        busqueda = ''
    ) {

        if (
            deshabilitado
            || selector.disabled
        ) {
            return;
        }


        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'quejoso-catalogo-select--activo'
        );


        filtrar(
            busqueda
        );
    }


    function cerrar(
        restaurarTexto = true
    ) {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'quejoso-catalogo-select--activo'
        );


        if (restaurarTexto) {

            textoSelector.textContent =
                obtenerTextoSeleccionado();

            filtrar(
                ''
            );
        }
    }


    function seleccionar(
        opcion
    ) {

        const nombre =
            String(
                opcion.nombre
                || ''
            ).trim();


        inputValor.value =
            nombre;


        textoSelector.textContent =
            nombre !== ''
                ? nombre
                : configuracion.placeholder;


        if (inputId) {

            inputId.value =
                String(
                    opcion.id
                    ?? ''
                );
        }


        cerrar(
            false
        );


        if (
            typeof configuracion.onSelect === 'function'
        ) {

            configuracion.onSelect(
                opcion
            );
        }


        inputValor.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles:
                        true,
                }
            )
        );
    }


    function limpiar(
        texto = configuracion.placeholder
    ) {

        inputValor.value =
            '';


        if (inputId) {
            inputId.value =
                '';
        }


        textoSelector.textContent =
            texto;


        cerrar(
            false
        );
    }


    function establecerOpciones(
        nuevasOpciones
    ) {

        opciones =
            Array.isArray(
                nuevasOpciones
            )
                ? nuevasOpciones
                    .map(
                        (opcion) => ({
                            id:
                                String(
                                    opcion.id
                                    ?? opcion.idEstado
                                    ?? ''
                                ),

                            nombre:
                                String(
                                    opcion.nombre
                                    ?? ''
                                ).trim(),
                        })
                    )
                    .filter(
                        (opcion) => opcion.id !== ''
                            && opcion.nombre !== ''
                    )
                : [];


        filtrar(
            ''
        );
    }


    function establecerDeshabilitado(
        valor,
        texto
    ) {

        deshabilitado =
            valor;


        selector.disabled =
            valor;


        selector.setAttribute(
            'aria-disabled',
            valor
                ? 'true'
                : 'false'
        );


        textoSelector.contentEditable =
            valor
                ? 'false'
                : 'true';


        if (texto) {

            textoSelector.textContent =
                texto;
        }


        if (valor) {
            cerrar(
                false
            );
        }
    }


    selector.addEventListener(
        'click',
        (evento) => {

            if (
                deshabilitado
                || selector.disabled
            ) {
                return;
            }


            if (
                evento.target === textoSelector
            ) {

                abrir();

                requestAnimationFrame(
                    () => seleccionarTextoQuejosoCatalogo(
                        textoSelector
                    )
                );

                return;
            }


            if (
                resultados.hidden
            ) {

                abrir();

            } else {

                cerrar();
            }
        }
    );


    textoSelector.addEventListener(
        'focus',
        () => {

            if (
                deshabilitado
                || selector.disabled
            ) {
                return;
            }


            abrir();


            requestAnimationFrame(
                () => seleccionarTextoQuejosoCatalogo(
                    textoSelector
                )
            );
        }
    );


    textoSelector.addEventListener(
        'click',
        () => {

            if (
                deshabilitado
                || selector.disabled
            ) {
                return;
            }


            requestAnimationFrame(
                () => seleccionarTextoQuejosoCatalogo(
                    textoSelector
                )
            );
        }
    );


    textoSelector.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Enter'
            ) {

                evento.preventDefault();
            }
        }
    );


    textoSelector.addEventListener(
        'input',
        () => {

            const busqueda =
                convertirTextoQuejosoCatalogoAMayusculas(
                    textoSelector
                );


            abrir(
                busqueda
            );


            filtrar(
                busqueda
            );
        }
    );


    document.addEventListener(
        'click',
        (evento) => {

            if (
                selector.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrar();
        }
    );


    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
            ) {

                cerrar();
            }
        }
    );


    filtrar(
        ''
    );


    return {
        abrir,
        cerrar,
        limpiar,
        seleccionar,
        establecerOpciones,
        establecerDeshabilitado,
        obtenerIdSeleccionado: () => inputId
            ? String(inputId.value || '')
            : '',
    };
}


function inicializarEstadoMunicipioQuejoso() {

    const estado =
        crearSelectorCatalogoQuejoso({
            selector:
                '#estado-quejoso-select',

            texto:
                '#estado-quejoso-select-texto',

            input:
                '#estado_quejoso',

            inputId:
                '#estado_quejoso_id',

            resultados:
                '#estado-quejoso-resultados',

            placeholder:
                'Selecciona un estado',

            descripcion:
                'Estado',

            onSelect:
                (opcion) => {

                    municipio.limpiar(
                        'Cargando municipios...'
                    );

                    cargarMunicipiosQuejoso(
                        String(
                            opcion.id
                            || ''
                        )
                    );
                },
        });


    const municipio =
        crearSelectorCatalogoQuejoso({
            selector:
                '#municipio-quejoso-select',

            texto:
                '#municipio-quejoso-select-texto',

            input:
                '#municipio_quejoso',

            inputId:
                '#municipio_quejoso_id',

            resultados:
                '#municipio-quejoso-resultados',

            placeholder:
                'Selecciona un municipio',

            descripcion:
                'Municipio',
        });


    if (
        !estado
        || !municipio
    ) {
        return;
    }


    let controladorMunicipios =
        null;


    let solicitudMunicipios =
        0;


    municipio.establecerDeshabilitado(
        true,
        'Selecciona un estado primero'
    );


    async function cargarEstadosQuejoso() {

        estado.establecerDeshabilitado(
            true,
            'Cargando estados...'
        );


        try {

            const url =
                construirUrlCatalogoQuejoso(
                    'catalogos/estados'
                );


            const respuesta =
                await fetch(
                    url.toString(),
                    {
                        method:
                            'GET',

                        credentials:
                            'same-origin',

                        headers: {
                            Accept:
                                'application/json',
                        },
                    }
                );


            if (
                !respuesta.ok
            ) {
                throw new Error(
                    'No fue posible consultar los estados.'
                );
            }


            const datos =
                await respuesta.json();


            estado.establecerOpciones(
                Array.isArray(
                    datos.estados
                )
                    ? datos.estados
                    : []
            );


            estado.limpiar(
                'Selecciona un estado'
            );


            estado.establecerDeshabilitado(
                false,
                'Selecciona un estado'
            );
        } catch (error) {

            console.error(
                'Error cargando estados del quejoso:',
                error
            );


            estado.establecerOpciones(
                []
            );


            estado.establecerDeshabilitado(
                true,
                'No fue posible cargar estados'
            );


            municipio.limpiar(
                'Selecciona un estado primero'
            );


            municipio.establecerDeshabilitado(
                true,
                'Selecciona un estado primero'
            );
        }
    }


    async function cargarMunicipiosQuejoso(
        idEstado
    ) {

        const id =
            String(
                idEstado
                || ''
            ).trim();


        solicitudMunicipios += 1;


        const solicitudActual =
            solicitudMunicipios;


        if (controladorMunicipios) {
            controladorMunicipios.abort();
        }


        if (id === '') {

            municipio.establecerOpciones(
                []
            );


            municipio.limpiar(
                'Selecciona un estado primero'
            );


            municipio.establecerDeshabilitado(
                true,
                'Selecciona un estado primero'
            );

            return;
        }


        controladorMunicipios =
            new AbortController();


        municipio.establecerDeshabilitado(
            true,
            'Cargando municipios...'
        );


        try {

            const url =
                construirUrlCatalogoQuejoso(
                    'catalogos/municipios'
                );


            url.searchParams.set(
                'idEstado',
                id
            );


            const respuesta =
                await fetch(
                    url.toString(),
                    {
                        method:
                            'GET',

                        credentials:
                            'same-origin',

                        headers: {
                            Accept:
                                'application/json',
                        },

                        signal:
                            controladorMunicipios.signal,
                    }
                );


            if (
                !respuesta.ok
            ) {
                throw new Error(
                    'No fue posible consultar los municipios.'
                );
            }


            const datos =
                await respuesta.json();


            if (
                solicitudActual !== solicitudMunicipios
            ) {
                return;
            }


            municipio.establecerOpciones(
                Array.isArray(
                    datos.municipios
                )
                    ? datos.municipios
                    : []
            );


            municipio.limpiar(
                'Selecciona un municipio'
            );


            municipio.establecerDeshabilitado(
                false,
                'Selecciona un municipio'
            );
        } catch (error) {

            if (
                error.name === 'AbortError'
            ) {
                return;
            }


            console.error(
                'Error cargando municipios del quejoso:',
                error
            );


            if (
                solicitudActual !== solicitudMunicipios
            ) {
                return;
            }


            municipio.establecerOpciones(
                []
            );


            municipio.limpiar(
                'No fue posible cargar municipios'
            );


            municipio.establecerDeshabilitado(
                true,
                'No fue posible cargar municipios'
            );
        }
    }


    cargarEstadosQuejoso();


    const formulario =
        document.querySelector(
            '#form-nuevo-reporte'
        )
        || document.querySelector(
            'form'
        );


    if (formulario) {

        formulario.addEventListener(
            'reset',
            () => {

                requestAnimationFrame(
                    () => {

                        estado.limpiar(
                            'Selecciona un estado'
                        );


                        municipio.establecerOpciones(
                            []
                        );


                        municipio.limpiar(
                            'Selecciona un estado primero'
                        );


                        municipio.establecerDeshabilitado(
                            true,
                            'Selecciona un estado primero'
                        );
                    }
                );
            }
        );
    }
}


function inicializarColoniaQuejosoMayusculas() {

    const colonia =
        document.querySelector(
            '#colonia_quejoso'
        );


    if (!colonia) {
        return;
    }


    colonia.addEventListener(
        'input',
        () => convertirInputQuejosoAMayusculas(
            colonia
        )
    );
}


/* =========================================================
   QUEJOSO ANÓNIMO
========================================================= */

function inicializarQuejosoAnonimo() {

    const radioAnonimo =
        document.querySelector(
            '#quejoso-anonimo'
        );


    const radioNoAnonimo =
        document.querySelector(
            '#quejoso-no-anonimo'
        );


    const contenedorNumero =
        document.querySelector(
            '#numero-anonimo-contenedor'
        );


    const inputNumero =
        document.querySelector(
            '#numero_anonimo'
        );


    const camposQuejoso = [

    document.querySelector(
        '#quejoso'
    ),

    document.querySelector(
        '#edad'
    ),

    document.querySelector(
        '#genero'
    ),

    document.querySelector(
        '#telefono'
    ),

    document.querySelector(
        '#correo'
    ),

    document.querySelector(
        '#calle_quejoso'
    ),

    document.querySelector(
        '#numero_quejoso'
    ),

    document.querySelector(
        '#colonia_quejoso'
    ),

    document.querySelector(
        '#municipio_quejoso'
    ),

    document.querySelector(
        '#estado_quejoso'
    ),
    ];


    const selectorGenero =
        document.querySelector(
            '#genero-select'
        );


    const resultadosGenero =
        document.querySelector(
            '#genero-resultados'
        );


    const selectorEstadoQuejoso =
        document.querySelector(
            '#estado-quejoso-select'
        );


    const textoEstadoQuejoso =
        document.querySelector(
            '#estado-quejoso-select-texto'
        );


    const resultadosEstadoQuejoso =
        document.querySelector(
            '#estado-quejoso-resultados'
        );


    const selectorMunicipioQuejoso =
        document.querySelector(
            '#municipio-quejoso-select'
        );


    const textoMunicipioQuejoso =
        document.querySelector(
            '#municipio-quejoso-select-texto'
        );


    const resultadosMunicipioQuejoso =
        document.querySelector(
            '#municipio-quejoso-resultados'
        );

    /* =====================================================
    DIRECCIÓN PARA NOTIFICACIÓN
    ===================================================== */

    const seccionNotificacion =
        document.querySelector(
            '#seccion-direccion-notificacion'
        );


    const advertenciaForaneo =
        document.querySelector(
            '#notificacion-advertencia-foraneo'
        );


    const controlesNotificacion =
        seccionNotificacion
            ? Array.from(
                seccionNotificacion.querySelectorAll(
                    'input, select, textarea, button'
                )
            )
            : [];

    if (
        !radioAnonimo
        || !radioNoAnonimo
        || !contenedorNumero
        || !inputNumero
    ) {
        return;
    }


    /* =====================================================
       ACTUALIZAR ESTADO
    ===================================================== */

    function actualizarEstadoAnonimo() {

        const esAnonimo =
            radioAnonimo.checked;


        /* =================================================
           NÚMERO ANÓNIMO
        ================================================= */

        contenedorNumero.hidden =
            !esAnonimo;


        inputNumero.disabled =
            !esAnonimo;


        inputNumero.required =
            esAnonimo;


        /* =================================================
           DATOS PERSONALES DEL QUEJOSO
        ================================================= */

        camposQuejoso.forEach(
            (campo) => {

                if (
                    !campo
                ) {
                    return;
                }


                campo.disabled =
                    esAnonimo;


                if (
                    esAnonimo
                ) {

                    /*
                     * Guardamos si originalmente era
                     * obligatorio antes de deshabilitarlo.
                     */
                    if (
                        campo.dataset.requiredOriginal
                        === undefined
                    ) {

                        campo.dataset.requiredOriginal =
                            campo.required
                                ? '1'
                                : '0';
                    }


                    campo.required =
                        false;

                } else {

                    /*
                     * Restauramos el required original.
                     */
                    campo.required =
                        campo.dataset.requiredOriginal
                        === '1';


                    delete campo.dataset.requiredOriginal;
                }
            }
        );


        if (selectorGenero) {

            selectorGenero.disabled =
                esAnonimo;


            selectorGenero.setAttribute(
                'aria-disabled',
                esAnonimo
                    ? 'true'
                    : 'false'
            );
        }


        if (
            resultadosGenero
            && esAnonimo
        ) {

            resultadosGenero.hidden =
                true;


            if (selectorGenero) {

                selectorGenero.setAttribute(
                    'aria-expanded',
                    'false'
                );


                selectorGenero.classList.remove(
                    'genero-select--activo'
                );
            }
        }


        [
            [
                selectorEstadoQuejoso,
                textoEstadoQuejoso,
                resultadosEstadoQuejoso,
            ],
            [
                selectorMunicipioQuejoso,
                textoMunicipioQuejoso,
                resultadosMunicipioQuejoso,
            ],
        ].forEach(
            ([
                selector,
                texto,
                resultados,
            ]) => {

                if (!selector) {
                    return;
                }


                selector.disabled =
                    esAnonimo
                    || (
                        selector === selectorMunicipioQuejoso
                        && !document.querySelector(
                            '#estado_quejoso_id'
                        )?.value
                    );


                selector.setAttribute(
                    'aria-disabled',
                    selector.disabled
                        ? 'true'
                        : 'false'
                );


                if (texto) {

                    texto.contentEditable =
                        selector.disabled
                            ? 'false'
                            : 'true';
                }


                if (
                    resultados
                    && esAnonimo
                ) {

                    resultados.hidden =
                        true;


                    selector.setAttribute(
                        'aria-expanded',
                        'false'
                    );


                    selector.classList.remove(
                        'quejoso-catalogo-select--activo'
                    );
                }
            }
        );


        /* =================================================
   DIRECCIÓN PARA NOTIFICACIÓN
================================================= */

        if (seccionNotificacion) {

            seccionNotificacion.classList.toggle(
                'report-section--disabled',
                esAnonimo
            );
        }


        controlesNotificacion.forEach(
            (control) => {

                if (!control) {
                    return;
                }


                /*
                 * Guardamos si originalmente estaba
                 * deshabilitado o era obligatorio.
                 */
                if (
                    control.dataset.disabledOriginal
                    === undefined
                ) {

                    control.dataset.disabledOriginal =
                        control.disabled
                            ? '1'
                            : '0';
                }


                if (
                    control.dataset.requiredOriginal
                    === undefined
                ) {

                    control.dataset.requiredOriginal =
                        control.required
                            ? '1'
                            : '0';
                }


                if (esAnonimo) {

                    control.disabled =
                        true;


                    control.required =
                        false;

                } else {

                    control.disabled =
                        control.dataset.disabledOriginal
                        === '1';


                    control.required =
                        control.dataset.requiredOriginal
                        === '1';


                    delete control.dataset.disabledOriginal;

                    delete control.dataset.requiredOriginal;
                }
            }
        );


        /* =================================================
           OCULTAR ADVERTENCIA CUANDO ES ANÓNIMO
        ================================================= */

        if (
            advertenciaForaneo
            && esAnonimo
        ) {

            advertenciaForaneo.hidden =
                true;
        }


        /* =================================================
           SI DEJA DE SER ANÓNIMO
        ================================================= */

        if (
            !esAnonimo
        ) {

            inputNumero.value =
                '';
        }
    }


    /* =====================================================
       EVENTOS
    ===================================================== */

    radioAnonimo.addEventListener(
        'change',
        actualizarEstadoAnonimo
    );


    radioNoAnonimo.addEventListener(
        'change',
        actualizarEstadoAnonimo
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarEstadoAnonimo();
}
