/* =========================================================
   LIBRERIA LOCAL - HTML TO IMAGE
   Adaptador compatible con toBlob / toPng.
========================================================= */

export async function toBlob(nodo, opciones = {}) {
    if (!(nodo instanceof HTMLElement)) {
        throw new Error('El nodo indicado no es un elemento HTML.');
    }

    const escala = Number(opciones.pixelRatio || window.devicePixelRatio || 2);
    const rect = nodo.getBoundingClientRect();
    const ancho = Math.ceil(opciones.width || nodo.scrollWidth || rect.width);
    const alto = Math.ceil(opciones.height || nodo.scrollHeight || rect.height);

    if (ancho <= 0 || alto <= 0) {
        throw new Error('La visualización no tiene dimensiones válidas.');
    }

    const clon = nodo.cloneNode(true);

    await prepararClon(nodo, clon, opciones);
    aplicarBaseClon(clon, ancho, alto, opciones.backgroundColor || '#ffffff');

    const datosSvg = serializarComoSvg(clon, ancho, alto);

    return dibujarSvgEnBlob(datosSvg, ancho, alto, escala);
}

export async function toPng(nodo, opciones = {}) {
    const blob = await toBlob(nodo, opciones);

    return new Promise((resolve, reject) => {
        const lector = new FileReader();

        lector.onload = () => resolve(lector.result);
        lector.onerror = () => reject(
            lector.error || new Error('No fue posible leer la imagen generada.')
        );
        lector.readAsDataURL(blob);
    });
}

async function prepararClon(origen, clon, opciones) {
    reemplazarCanvas(origen, clon, opciones);
    sincronizarCampos(origen, clon);
    copiarEstilosComputados(origen, clon);

    await esperarImagenes(clon);
}

function reemplazarCanvas(origen, clon, opciones) {
    const canvasOrigen = [...origen.querySelectorAll('canvas')];
    const canvasClon = [...clon.querySelectorAll('canvas')];

    canvasOrigen.forEach((canvas, indice) => {
        const destino = canvasClon[indice];

        if (!destino) {
            return;
        }

        if (
            window.getComputedStyle(canvas).display === 'none'
        ) {

            return;

        }

        const anchoCanvas =
            canvas.width
            || canvas.offsetWidth;

        const altoCanvas =
            canvas.height
            || canvas.offsetHeight;

        if (
            anchoCanvas <= 0
            || altoCanvas <= 0
        ) {

            throw new Error(
                `Canvas sin dimensiones validas en ${opciones?.descripcion || 'visualizacion'}: ${anchoCanvas}x${altoCanvas}.`
            );

        }

        const imagen = document.createElement('img');

        imagen.src = canvas.toDataURL('image/png');
        imagen.alt = canvas.getAttribute('aria-label') || '';
        imagen.style.width = `${canvas.offsetWidth}px`;
        imagen.style.height = `${canvas.offsetHeight}px`;
        imagen.style.display = 'block';

        destino.replaceWith(imagen);
    });
}

function sincronizarCampos(origen, clon) {
    const camposOrigen = [...origen.querySelectorAll('input, textarea, select')];
    const camposClon = [...clon.querySelectorAll('input, textarea, select')];

    camposOrigen.forEach((campo, indice) => {
        const destino = camposClon[indice];

        if (!destino) {
            return;
        }

        if (campo instanceof HTMLInputElement) {
            destino.value = campo.value;

            if (campo.checked) {
                destino.setAttribute('checked', 'checked');
            }

            return;
        }

        if (campo instanceof HTMLTextAreaElement) {
            destino.value = campo.value;
            destino.textContent = campo.value;

            return;
        }

        if (campo instanceof HTMLSelectElement) {
            destino.value = campo.value;

            [...destino.options].forEach(opcion => {
                opcion.selected = opcion.value === campo.value;
            });
        }
    });
}

function copiarEstilosComputados(origen, clon) {
    if (
        origen.nodeType !== Node.ELEMENT_NODE
        || clon.nodeType !== Node.ELEMENT_NODE
    ) {
        return;
    }

    const estilos = window.getComputedStyle(origen);

    for (let indice = 0; indice < estilos.length; indice += 1) {
        const propiedad = estilos[indice];

        clon.style.setProperty(
            propiedad,
            estilos.getPropertyValue(propiedad),
            estilos.getPropertyPriority(propiedad)
        );
    }

    const hijosOrigen = [...origen.children];
    const hijosClon = [...clon.children];

    hijosOrigen.forEach((hijo, indice) => {
        if (hijosClon[indice]) {
            copiarEstilosComputados(hijo, hijosClon[indice]);
        }
    });
}

function aplicarBaseClon(clon, ancho, alto, fondo) {
    clon.setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');

    clon.style.width = `${ancho}px`;
    clon.style.minWidth = `${ancho}px`;
    clon.style.height = 'auto';
    clon.style.minHeight = `${alto}px`;
    clon.style.background = fondo;
    clon.style.boxSizing = 'border-box';
}

function serializarComoSvg(clon, ancho, alto) {
    const contenido = new XMLSerializer().serializeToString(clon);
    const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="${ancho}" height="${alto}" viewBox="0 0 ${ancho} ${alto}">
            <foreignObject width="100%" height="100%" x="0" y="0">
                ${contenido}
            </foreignObject>
        </svg>
    `;

    return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`;
}

function dibujarSvgEnBlob(datosSvg, ancho, alto, escala) {
    return new Promise((resolve, reject) => {
        const imagen = new Image();

        imagen.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = Math.ceil(ancho * escala);
            canvas.height = Math.ceil(alto * escala);

            const contexto = canvas.getContext('2d');

            if (!contexto) {
                reject(new Error('No fue posible preparar el lienzo.'));
                return;
            }

            contexto.fillStyle = '#ffffff';
            contexto.fillRect(0, 0, canvas.width, canvas.height);
            contexto.scale(escala, escala);
            contexto.drawImage(imagen, 0, 0, ancho, alto);

            canvas.toBlob(blob => {
                if (!blob) {
                    reject(new Error('No fue posible generar la imagen.'));
                    return;
                }

                resolve(blob);
            }, 'image/png', 1);
        };

        imagen.onerror = () => reject(
            new Error('No fue posible renderizar la visualización.')
        );
        imagen.src = datosSvg;
    });
}

function esperarImagenes(raiz) {
    const imagenes = [...raiz.querySelectorAll('img')];

    return Promise.all(
        imagenes.map(imagen => {
            if (imagen.complete) {
                return Promise.resolve();
            }

            return new Promise(resolve => {
                imagen.onload = resolve;
                imagen.onerror = resolve;
            });
        })
    );
}
