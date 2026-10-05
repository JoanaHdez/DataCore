/* =========================================================
   DASHBOARD
   Copiar / descargar visualizaciones analiticas completas
========================================================= */

import {
    cerrarResultado,
    mostrarResultado,
} from '../../notificaciones/resultado.js';

import {
    toBlob as htmlToImageBlob,
} from '../../../librerias/html-to-image.js';


const SELECTOR_ACCIONES =
    '[data-dashboard-visualizacion-acciones]';

const CLASE_CAPTURANDO =
    'dashboard-visualizacion--capturando';

const PIXEL_RATIO =
    2;


const VISUALIZACIONES_DASHBOARD = [
    {
        root: '.dashboard-indicadores',
        accionesTarget: '.dashboard-indicadores__encabezado',
        nombre: () => nombreConTipo('dashboard-indicadores'),
    },
    {
        root: '.dashboard-grafica--catalogo',
        accionesTarget: '.dashboard-catalogo__encabezado',
        nombre: () => 'dashboard-catalogo-general',
    },
    {
        root: '.dashboard-grafica--principal',
        accionesTarget: '.dashboard-grafica--principal .dashboard-grafica__encabezado',
        nombre: () => 'dashboard-sectores-turnos',
    },
    {
        root: '.dashboard-grafica--areas-involucradas',
        accionesTarget: '.dashboard-grafica--areas-involucradas .dashboard-grafica__encabezado',
        nombre: () => 'dashboard-quejas-por-area',
    },
    {
        root: '#dashboard-evolucion',
        accionesTarget: '.dashboard-evolucion__encabezado',
        nombre: () => nombreConTipo('dashboard-evolucion-temporal'),
    },
    {
        root: 'section.dashboard-estado',
        accionesTarget: '.dashboard-estado__encabezado',
        etiqueta: 'Estado de las Quejas',
        nombre: () => nombreConTipo('dashboard-estado-quejas'),
        generarBlob: generarBlobEstado,
    },
    {
        root: '.dashboard-grafica--zona',
        accionesTarget: '.dashboard-grafica-zona__encabezado',
        nombre: () => nombreConTipo('dashboard-zonas'),
    },
    {
        root: '.dashboard-grafica--turnos',
        accionesTarget: '.dashboard-turnos__encabezado',
        nombre: () => nombreConTipo('dashboard-turnos'),
        expandir: [
            '.dashboard-turnos__detalle',
            '.dashboard-turnos__lista',
        ],
    },
    {
        root: '#dashboard-sectores',
        accionesTarget: '.dashboard-sectores__encabezado',
        nombre: nombreSectorActivo,
        expandir: [
            '.dashboard-sectores__lista',
        ],
    },
    {
        root: '.dashboard-grafica--sanciones',
        accionesTarget: '.dashboard-sanciones__encabezado',
        nombre: () => 'dashboard-sanciones',
    },
    {
        root: '#dashboard-dimension',
        accionesTarget: '.dashboard-dimension__encabezado',
        nombre: nombreDimensionActiva,
        ocultar: [
            '.dashboard-dimension__selector',
        ],
        expandir: [
            '.dashboard-dimension__lista',
            '.dashboard-dimension__detalle',
        ],
        contexto: textoDimensionActiva,
    },
    {
        root: '#dashboard-cruce',
        accionesTarget: '.dashboard-cruce__encabezado',
        nombre: nombreCruceActivo,
        ocultar: [
            '.dashboard-cruce__selectores',
            '.dashboard-cruce__selector-menu',
        ],
        expandir: [
            '.dashboard-cruce__grafica-scroll',
        ],
        contexto: textoCruceActivo,
    },
    {
        root: '#dashboard-ranking',
        accionesTarget: '.dashboard-ranking__encabezado',
        nombre: nombreRankingActivo,
        ocultar: [
            '.dashboard-ranking__selector',
        ],
        expandir: [
            '.dashboard-ranking__lista',
            '.dashboard-ranking__clasificacion',
        ],
        contexto: textoRankingActivo,
    },
    {
        root: '#dashboard-hallazgos',
        accionesTarget: '.dashboard-hallazgos__encabezado',
        nombre: () => nombreConTipo('dashboard-hallazgos'),
    },
    {
        root: '#dashboard-comparativa',
        accionesTarget: '.dashboard-comparativa__encabezado',
        nombre: () => nombreConTipo('dashboard-comparativa-temporal'),
    },
    {
        root: '#dashboard-personal-individual',
        accionesTarget: '.dashboard-personal-individual__encabezado',
        nombre: () => nombreConTipo('dashboard-personal-individual'),
        ocultar: [
            '.dashboard-personal-individual__busqueda',
            '#modal-personal-individual-motivos',
        ],
        expandir: [
            '.dashboard-personal-individual__tabla-contenedor',
        ],
    },
];


document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarAccionesVisualizaciones();

    }
);


function inicializarAccionesVisualizaciones() {

    VISUALIZACIONES_DASHBOARD.forEach(
        configuracion => {

            const root =
                obtenerElemento(configuracion.root);

            const encabezado =
                obtenerElemento(configuracion.accionesTarget);


            if (
                !root
                || !encabezado
                || encabezado.querySelector(SELECTOR_ACCIONES)
            ) {

                return;

            }


            encabezado.classList.add(
                'dashboard-visualizacion__encabezado'
            );

            encabezado.appendChild(
                crearAccionesVisualizacion(root, configuracion)
            );

        }
    );
}


function crearAccionesVisualizacion(root, configuracion) {

    const contenedor =
        document.createElement('div');

    contenedor.className =
        'dashboard-visualizacion__acciones';

    contenedor.dataset.dashboardVisualizacionAcciones =
        'true';

    contenedor.append(
        crearBotonAccion({
            tipo: 'copiar',
            etiqueta: 'Copiar visualización',
            icono: iconoCopiar(),
            configuracion,
            accion: () => copiarVisualizacionAlPortapapeles(
                root,
                configuracion
            ),
        }),
        crearBotonAccion({
            tipo: 'descargar',
            etiqueta: 'Descargar visualización',
            icono: iconoDescargar(),
            configuracion,
            accion: () => descargarVisualizacionComoPng(
                root,
                configuracion
            ),
        })
    );

    return contenedor;
}


function crearBotonAccion({
    tipo,
    etiqueta,
    icono,
    configuracion,
    accion,
}) {

    const boton =
        document.createElement('button');

    boton.type =
        'button';

    boton.className =
        `dashboard-visualizacion__accion dashboard-visualizacion__accion--${tipo}`;

    boton.setAttribute(
        'aria-label',
        etiqueta
    );

    boton.title =
        etiqueta;

    boton.innerHTML =
        icono;

    if (
        tipo === 'copiar'
    ) {

        marcarBotonCopiarNoDisponible(
            boton
        );

    }

    boton.addEventListener(
        'click',
        async () => {

            boton.disabled =
                true;

            try {

                await accion();

            } catch (error) {

                registrarErrorVisualizacion(
                    configuracion,
                    error
                );

                console.error(
                    'No fue posible procesar la visualización.',
                    error
                );

                notificarVisualizacion({
                    tipo: 'error',
                    titulo: 'No fue posible',
                    mensaje: 'No fue posible procesar la visualización.',
                });

            } finally {

                boton.disabled =
                    false;

            }

        }
    );

    return boton;
}


async function descargarVisualizacionComoPng(root, configuracion) {

    const restaurar =
        prepararVisualizacionParaCaptura(root, configuracion);

    try {

        const blob =
            await generarBlobVisualizacion(
                root,
                configuracion
            );

        descargarBlob(
            blob,
            generarNombreArchivo(configuracion)
        );

        notificarVisualizacion({
            tipo: 'success',
            titulo: 'Visualización descargada',
            mensaje: 'Visualización descargada correctamente.',
        });

    } finally {

        restaurar();

    }
}


async function copiarVisualizacionAlPortapapeles(root, configuracion) {

    const compatibilidad =
        comprobarCompatibilidadPortapapeles();

    if (
        !compatibilidad.disponible
    ) {

        notificarVisualizacion({
            tipo: 'warning',
            titulo: 'No disponible',
            mensaje: compatibilidad.mensaje,
        });

        return;

    }

    const restaurar =
        prepararVisualizacionParaCaptura(root, configuracion);

    try {

        const blob =
            await generarBlobVisualizacion(
                root,
                configuracion
            );

        try {

            await navigator.clipboard.write([
                new ClipboardItem({
                    [blob.type]: blob,
                }),
            ]);

        } catch (error) {

            notificarErrorPortapapeles(error);
            return;

        }

        notificarVisualizacion({
            tipo: 'success',
            titulo: 'Visualización copiada',
            mensaje: 'Visualización copiada al portapapeles.',
        });

    } finally {

        restaurar();

    }
}


function prepararVisualizacionParaCaptura(root, configuracion) {

    const restauradores = [];

    root.classList.add(CLASE_CAPTURANDO);

    restauradores.push(
        () => root.classList.remove(CLASE_CAPTURANDO)
    );

    ocultarElementos(
        root,
        [
            SELECTOR_ACCIONES,
            '.dashboard-cruce__selector-menu',
            ...(configuracion.ocultar || []),
        ],
        restauradores
    );

    expandirContenedores(
        root,
        configuracion.expandir || [],
        restauradores
    );

    if (
        typeof configuracion.contexto === 'function'
    ) {

        agregarContextoCaptura(
            root,
            configuracion.contexto(),
            restauradores
        );

    }

    if (
        typeof configuracion.prepararCaptura === 'function'
    ) {

        configuracion.prepararCaptura(
            root,
            restauradores
        );

    }

    return () => {

        restauradores
            .reverse()
            .forEach(restaurar => restaurar());

    };
}


function ocultarElementos(root, selectores, restauradores) {

    selectores.forEach(
        selector => {

            root.querySelectorAll(selector).forEach(
                elemento => {

                    const displayOriginal =
                        elemento.style.display;

                    elemento.style.display =
                        'none';

                    restauradores.push(
                        () => {

                            elemento.style.display =
                                displayOriginal;

                        }
                    );

                }
            );

        }
    );
}


function expandirContenedores(root, selectores, restauradores) {

    selectores.forEach(
        selector => {

            root.querySelectorAll(selector).forEach(
                elemento => {

                    const originales = {
                        maxHeight: elemento.style.maxHeight,
                        height: elemento.style.height,
                        overflow: elemento.style.overflow,
                        scrollTop: elemento.scrollTop,
                    };

                    elemento.style.maxHeight =
                        'none';

                    elemento.style.height =
                        `${elemento.scrollHeight}px`;

                    elemento.style.overflow =
                        'visible';

                    restauradores.push(
                        () => {

                            elemento.style.maxHeight =
                                originales.maxHeight;

                            elemento.style.height =
                                originales.height;

                            elemento.style.overflow =
                                originales.overflow;

                            elemento.scrollTop =
                                originales.scrollTop;

                        }
                    );

                }
            );

        }
    );
}


function agregarContextoCaptura(root, texto, restauradores) {

    if (!texto) {
        return;
    }

    const contexto =
        document.createElement('div');

    contexto.className =
        'dashboard-visualizacion__contexto';

    contexto.textContent =
        texto;

    const encabezado =
        root.querySelector('.dashboard-visualizacion__encabezado')
        || root.firstElementChild;

    if (
        encabezado
        && encabezado.parentNode
    ) {

        encabezado.parentNode.insertBefore(
            contexto,
            encabezado.nextSibling
        );

    } else {

        root.prepend(contexto);

    }

    restauradores.push(
        () => contexto.remove()
    );
}


async function generarBlobVisualizacion(root, configuracion) {

    if (
        typeof configuracion?.generarBlob === 'function'
    ) {

        return configuracion.generarBlob(
            root,
            configuracion
        );

    }


    return generarBlobPng(
        root,
        configuracion
    );
}


async function generarBlobPng(root, configuracion) {

    const rect =
        root.getBoundingClientRect();

    const ancho =
        Math.ceil(root.scrollWidth || rect.width);

    const alto =
        Math.ceil(root.scrollHeight || rect.height);

    const descripcion =
        configuracion?.etiqueta
        || configuracion?.root
        || 'visualizaciÃ³n';

    try {

        return await htmlToImageBlob(
            root,
            {
                backgroundColor: '#ffffff',
                pixelRatio: PIXEL_RATIO,
                width: ancho,
                height: alto,
                descripcion,
            }
        );

    } catch (error) {

        console.error(
            `Error exportando visualizaciÃ³n ${descripcion}:`,
            {
                error,
                root:
                    configuracion?.root,
                width:
                    ancho,
                height:
                    alto,
                scrollWidth:
                    root.scrollWidth,
                scrollHeight:
                    root.scrollHeight,
                rect:
                    {
                        width:
                            rect.width,
                        height:
                            rect.height,
                    },
                canvas:
                    obtenerDiagnosticoCanvas(root),
            }
        );

        throw error;

    }
}


function descargarBlob(blob, nombreArchivo) {

    const url =
        URL.createObjectURL(blob);

    const enlace =
        document.createElement('a');

    enlace.href =
        url;

    enlace.download =
        nombreArchivo;

    enlace.click();

    URL.revokeObjectURL(url);
}


function comprobarCompatibilidadPortapapeles() {

    if (
        window.isSecureContext === false
    ) {

        return {
            disponible: false,
            mensaje: 'Copiar imagen requiere HTTPS o un contexto seguro como localhost.',
        };

    }

    if (
        !navigator.clipboard
    ) {

        return {
            disponible: false,
            mensaje: 'Tu navegador no dispone de acceso al portapapeles.',
        };

    }

    if (
        typeof navigator.clipboard.write !== 'function'
    ) {

        return {
            disponible: false,
            mensaje: 'Tu navegador no permite copiar imágenes al portapapeles.',
        };

    }

    if (
        typeof ClipboardItem === 'undefined'
    ) {

        return {
            disponible: false,
            mensaje: 'Tu navegador no permite copiar imágenes al portapapeles.',
        };

    }

    return {
        disponible: true,
        mensaje: '',
    };
}


function notificarErrorPortapapeles(error) {

    const nombreError =
        String(error?.name || '');

    const mensaje =
        nombreError === 'NotAllowedError'
        || nombreError === 'SecurityError'
            ? 'No se concedió permiso para copiar la imagen.'
            : 'No fue posible copiar la visualización al portapapeles.';

    notificarVisualizacion({
        tipo: 'warning',
        titulo: 'No fue posible copiar',
        mensaje,
    });
}


function marcarBotonCopiarNoDisponible(boton) {

    if (
        window.isSecureContext !== false
    ) {

        return;

    }

    boton.classList.add(
        'dashboard-visualizacion__accion--no-disponible'
    );

    boton.title =
        'Disponible únicamente mediante HTTPS o localhost';

    boton.setAttribute(
        'aria-label',
        'Copiar visualización no disponible sin HTTPS o localhost'
    );
}


function registrarErrorVisualizacion(configuracion, error) {

    const descripcion =
        configuracion?.etiqueta
        || configuracion?.root
        || 'visualización';

    console.error(
        `Error exportando visualización ${descripcion}:`,
        error
    );
}


async function generarBlobEstado(root, configuracion) {

    const temporal =
        construirEstadoExportable(
            root
        );

    document.body.appendChild(
        temporal
    );

    try {

        return await generarBlobPng(
            temporal,
            configuracion
        );

    } finally {

        temporal.remove();

    }
}


async function diagnosticarEstadoExport(root) {

    const encabezado =
        root.querySelector(
            '.dashboard-estado__encabezado'
        );

    const resumen =
        root.querySelector(
            '.dashboard-estado__resumen'
        );

    const contenido =
        root.querySelector(
            '.dashboard-estado__contenido'
        );

    const grafica =
        root.querySelector(
            '.dashboard-estado__grafica'
        );

    const canvas =
        localizarCanvasEstado(
            root
        );

    const lista =
        root.querySelector(
            '.dashboard-estado__lista'
        );

    console.groupCollapsed(
        'ERROR ESTADO EXPORT - diagnóstico aislado'
    );

    console.info(
        'ERROR ESTADO EXPORT:',
        {
            rootRect:
                obtenerRect(root),
            encabezadoRect:
                obtenerRect(encabezado),
            resumenRect:
                obtenerRect(resumen),
            contenidoRect:
                obtenerRect(contenido),
            graficaRect:
                obtenerRect(grafica),
            canvasRect:
                obtenerRect(canvas),
            listaRect:
                obtenerRect(lista),
            canvasWidth:
                canvas instanceof HTMLCanvasElement
                    ? canvas.width
                    : null,
            canvasHeight:
                canvas instanceof HTMLCanvasElement
                    ? canvas.height
                    : null,
        }
    );

    await probarCanvasEstado(
        canvas
    );

    await probarFragmentoEstado(
        'A. Solo encabezado',
        [
            root.querySelector(
                '.dashboard-estado__encabezado > div:first-child'
            ),
        ]
    );

    await probarFragmentoEstado(
        'B. Encabezado + resumen',
        [
            encabezado,
        ]
    );

    await probarFragmentoEstado(
        'C. Solo gráfica/dona',
        [
            grafica,
        ]
    );

    await probarFragmentoEstado(
        'D. Solo lista lateral',
        [
            lista,
        ]
    );

    await probarFragmentoEstado(
        'E. Gráfica + lista',
        [
            grafica,
            lista,
        ]
    );

    await probarFragmentoEstado(
        'F. Contenedor completo',
        [
            root,
        ]
    );

    console.groupEnd();
}


async function probarCanvasEstado(canvas) {

    if (
        !(canvas instanceof HTMLCanvasElement)
    ) {

        console.error(
            'ERROR ESTADO EXPORT canvas:',
            'No se encontró #dashboard-estado-chart.'
        );

        return;

    }

    try {

        const dataUrl =
            canvas.toDataURL(
                'image/png'
            );

        console.info(
            'ERROR ESTADO EXPORT canvas.toDataURL:',
            {
                ok:
                    dataUrl.startsWith(
                        'data:image/png'
                    ),
                length:
                    dataUrl.length,
            }
        );

    } catch (error) {

        console.error(
            'ERROR ESTADO EXPORT canvas.toDataURL falló:',
            error
        );

    }

    await new Promise(
        resolve => {

            try {

                canvas.toBlob(
                    blob => {

                        console.info(
                            'ERROR ESTADO EXPORT canvas.toBlob:',
                            {
                                ok:
                                    blob instanceof Blob,
                                size:
                                    blob?.size
                                    || 0,
                                type:
                                    blob?.type
                                    || '',
                            }
                        );

                        resolve();

                    },
                    'image/png'
                );

            } catch (error) {

                console.error(
                    'ERROR ESTADO EXPORT canvas.toBlob falló:',
                    error
                );

                resolve();

            }

        }
    );
}


async function probarFragmentoEstado(nombre, elementos) {

    const temporal =
        crearContenedorEstadoTemporal();

    elementos
        .filter(Boolean)
        .forEach(
            elemento => {

                temporal.appendChild(
                    prepararNodoEstadoParaPrueba(
                        elemento
                    )
                );

            }
        );

    document.body.appendChild(
        temporal
    );

    try {

        const blob =
            await generarBlobPng(
                temporal,
                {
                    etiqueta:
                        `Estado de las Quejas - ${nombre}`,
                }
            );

        console.info(
            `ERROR ESTADO EXPORT prueba ${nombre}: OK`,
            {
                size:
                    blob.size,
            }
        );

    } catch (error) {

        console.error(
            `ERROR ESTADO EXPORT prueba ${nombre}: FALLÓ`,
            error
        );

    } finally {

        temporal.remove();

    }
}


function localizarCanvasEstado(root) {

    const rootEstado =
        root instanceof HTMLElement
        && root.matches(
            'section.dashboard-estado'
        )
            ? root
            : document.querySelector(
                'section.dashboard-estado'
            );

    const canvas =
        rootEstado?.querySelector(
            '.dashboard-estado__grafica canvas'
        )
        || rootEstado?.querySelector(
            'canvas'
        )
        || null;

    return canvas instanceof HTMLCanvasElement
        ? canvas
        : null;
}


function construirEstadoExportable(root) {

    const canvas =
        localizarCanvasEstado(
            root
        );

    if (
        !(canvas instanceof HTMLCanvasElement)
    ) {

        throw new Error(
            'No se encontró el canvas #dashboard-estado-chart.'
        );

    }

    const temporal =
        crearContenedorEstadoTemporal();

    const encabezado =
        root.querySelector(
            '.dashboard-estado__encabezado'
        );

    if (
        encabezado
    ) {

        temporal.appendChild(
            prepararNodoEstadoParaPrueba(
                encabezado
            )
        );

    }

    const contenido =
        document.createElement(
            'div'
        );

    contenido.style.display =
        'grid';

    contenido.style.gridTemplateColumns =
        'minmax(240px, 0.9fr) minmax(0, 1.4fr)';

    contenido.style.gap =
        '22px';

    contenido.style.alignItems =
        'center';

    contenido.style.padding =
        '18px';

    contenido.style.background =
        '#ffffff';

    contenido.style.border =
        '1px solid #e4ebe8';

    contenido.style.borderRadius =
        '18px';

    const grafica =
        document.createElement(
            'div'
        );

    grafica.style.height =
        '250px';

    grafica.style.display =
        'flex';

    grafica.style.alignItems =
        'center';

    grafica.style.justifyContent =
        'center';

    const imagen =
        document.createElement(
            'img'
        );

    imagen.src =
        canvas.toDataURL(
            'image/png'
        );

    imagen.alt =
        canvas.getAttribute(
            'aria-label'
        )
        || 'Estado de las Quejas';

    const canvasAncho =
        canvas.width
        || canvas.offsetWidth
        || 250;

    const canvasAlto =
        canvas.height
        || canvas.offsetHeight
        || 250;

    const proporcion =
        canvasAlto > 0
            ? canvasAncho / canvasAlto
            : 1;

    const altoVisual =
        Math.min(
            canvas.offsetHeight
            || 250,
            250
        );

    const anchoVisual =
        Math.round(
            altoVisual * proporcion
        );

    imagen.style.width =
        `${anchoVisual}px`;

    imagen.style.height =
        'auto';

    imagen.style.maxWidth =
        '100%';

    imagen.style.maxHeight =
        '250px';

    imagen.style.aspectRatio =
        `${canvasAncho} / ${canvasAlto}`;

    imagen.style.objectFit =
        'contain';

    imagen.style.display =
        'block';

    grafica.appendChild(
        imagen
    );

    contenido.appendChild(
        grafica
    );

    const lista =
        root.querySelector(
            '.dashboard-estado__lista'
        );

    if (
        lista
    ) {

        contenido.appendChild(
            prepararNodoEstadoParaPrueba(
                lista
            )
        );

    }

    temporal.appendChild(
        contenido
    );

    return temporal;
}


function crearContenedorEstadoTemporal() {

    const temporal =
        document.createElement(
            'section'
        );

    temporal.className =
        'dashboard-estado dashboard-estado--export-temporal';

    temporal.style.position =
        'absolute';

    temporal.style.left =
        '0';

    temporal.style.top =
        '0';

    temporal.style.zIndex =
        '-1';

    temporal.style.pointerEvents =
        'none';

    temporal.style.width =
        '760px';

    temporal.style.padding =
        '22px 24px 24px';

    temporal.style.background =
        '#ffffff';

    temporal.style.border =
        '1px solid #dfe9e4';

    temporal.style.borderRadius =
        '24px';

    temporal.style.boxSizing =
        'border-box';

    temporal.style.overflow =
        'visible';

    return temporal;
}


function prepararNodoEstadoParaPrueba(elemento) {

    const clon =
        elemento.cloneNode(
            true
        );

    clon.querySelectorAll(
        SELECTOR_ACCIONES
    ).forEach(
        accion => accion.remove()
    );

    return clon;
}


function prepararEstadoParaCaptura(root, restauradores) {

    const canvas =
        localizarCanvasEstado(
            root
        );

    if (
        !(canvas instanceof HTMLCanvasElement)
    ) {

        console.error(
            'Error exportando visualización Estado de las Quejas:',
            'No se encontró el canvas #dashboard-estado-chart.'
        );

        return;

    }

    try {

        const imagen =
            document.createElement(
                'img'
            );

        imagen.src =
            canvas.toDataURL(
                'image/png'
            );

        imagen.alt =
            canvas.getAttribute(
                'aria-label'
            )
            || 'Estado de las Quejas';

        imagen.className =
            'dashboard-visualizacion__canvas-imagen-temporal';

        imagen.style.width =
            `${canvas.offsetWidth}px`;

        imagen.style.height =
            `${canvas.offsetHeight}px`;

        imagen.style.display =
            'block';

        const displayOriginal =
            canvas.style.display;

        canvas.style.display =
            'none';

        canvas.insertAdjacentElement(
            'afterend',
            imagen
        );

        restauradores.push(
            () => {

                imagen.remove();

                canvas.style.display =
                    displayOriginal;

            }
        );

    } catch (error) {

        console.error(
            'Error exportando visualización Estado de las Quejas:',
            error
        );

        throw error;

    }
}


function obtenerDiagnosticoCanvas(root) {

    return [
        ...root.querySelectorAll(
            'canvas'
        ),
    ].map(
        canvas => ({
            id:
                canvas.id,
            width:
                canvas.width,
            height:
                canvas.height,
            offsetWidth:
                canvas.offsetWidth,
            offsetHeight:
                canvas.offsetHeight,
            hidden:
                canvas.hidden,
            display:
                window.getComputedStyle(
                    canvas
                ).display,
        })
    );
}


function obtenerRect(elemento) {

    if (
        !(elemento instanceof Element)
    ) {

        return null;

    }

    const rect =
        elemento.getBoundingClientRect();

    return {
        width:
            rect.width,
        height:
            rect.height,
        top:
            rect.top,
        left:
            rect.left,
    };
}


function obtenerElemento(selector) {

    const elemento =
        document.querySelector(selector);

    return elemento instanceof HTMLElement
        ? elemento
        : null;
}


function generarNombreArchivo(configuracion) {

    const nombreBase =
        typeof configuracion.nombre === 'function'
            ? configuracion.nombre()
            : configuracion.nombre;

    return `${normalizarNombreArchivo(nombreBase)}.png`;
}


function nombreConTipo(base) {

    const tipo =
        obtenerTipoDashboard();

    return tipo === 'felicitacion'
        ? `${base}-felicitaciones`
        : base;
}


function nombreDimensionActiva() {

    const activa =
        document.querySelector('.dashboard-dimension__tab--activo[data-dimension]');

    const dimension =
        activa?.dataset.dimension
        || new URLSearchParams(window.location.search).get('dimension')
        || 'area';

    return `dashboard-${dimension}`;
}


function nombreSectorActivo() {

    return obtenerTipoDashboard() === 'felicitacion'
        ? 'dashboard-felicitaciones-por-sector'
        : 'dashboard-quejas-por-sector';
}


function nombreCruceActivo() {

    const principal =
        document.getElementById('dashboard-cruce-principal')?.value;

    const secundaria =
        document.getElementById('dashboard-cruce-secundaria')?.value;

    if (
        principal
        && secundaria
    ) {

        return `dashboard-${principal}-${secundaria}`;

    }

    return 'dashboard-analisis-cruzado';
}


function nombreRankingActivo() {

    const activo =
        document.querySelector('.dashboard-ranking__tab--activo[data-ranking]');

    const ranking =
        activo?.dataset.ranking;

    return ranking
        ? `dashboard-ranking-${ranking}`
        : 'dashboard-ranking';
}


function textoDimensionActiva() {

    const activa =
        document.querySelector('.dashboard-dimension__tab--activo[data-dimension]');

    const texto =
        activa?.textContent?.trim();

    return texto
        ? `Dimensión activa: ${texto}`
        : '';
}


function textoCruceActivo() {

    const principal =
        obtenerTextoSelect('dashboard-cruce-principal');

    const secundaria =
        obtenerTextoSelect('dashboard-cruce-secundaria');

    if (
        principal
        && secundaria
    ) {

        return `Combinación activa: ${principal} x ${secundaria}`;

    }

    return '';
}


function textoRankingActivo() {

    const activa =
        document.querySelector('.dashboard-ranking__tab--activo[data-ranking]');

    const texto =
        activa?.textContent?.trim();

    return texto
        ? `Ranking activo: ${texto}`
        : '';
}


function obtenerTextoSelect(id) {

    const select =
        document.getElementById(id);

    if (
        !(select instanceof HTMLSelectElement)
    ) {

        return '';

    }

    return select.selectedOptions[0]?.textContent?.trim() || '';
}


function obtenerTipoDashboard() {

    return (
        new URLSearchParams(window.location.search).get('tipo')
        || ''
    )
        .trim()
        .toLowerCase();
}


function normalizarNombreArchivo(nombre) {

    return String(nombre || 'dashboard-visualizacion')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}


function notificarVisualizacion({
    tipo,
    titulo,
    mensaje,
}) {

    mostrarResultado({
        tipo,
        titulo,
        mensaje,
    });

    window.setTimeout(
        () => {

            cerrarResultado();

        },
        1800
    );
}


function iconoCopiar() {

    return `
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <rect x="8" y="8" width="10" height="12" rx="2"></rect>
            <path d="M6 16H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
        </svg>
    `;
}


function iconoDescargar() {

    return `
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3v12"></path>
            <path d="m7 10 5 5 5-5"></path>
            <path d="M5 21h14"></path>
        </svg>
    `;
}
