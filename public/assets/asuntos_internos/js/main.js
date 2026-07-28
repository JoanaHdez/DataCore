document.addEventListener('DOMContentLoaded', () => {
    configurarAlertas();
    configurarFormularioCarga();
    configurarAreaDeCarga();
});

function configurarAlertas() {
    const alertas = document.querySelectorAll('[data-alert]');

    alertas.forEach((alerta) => {
        activarAlerta(alerta);
    });
}

function activarAlerta(alerta) {
    if (!alerta) {
        return;
    }

    const botonCerrar = alerta.querySelector('[data-alert-close]');

    botonCerrar?.addEventListener('click', () => {
        cerrarAlerta(alerta);
    });

    const temporizador = window.setTimeout(() => {
        cerrarAlerta(alerta);
    }, 4000);

    alerta.addEventListener(
        'mouseenter',
        () => {
            window.clearTimeout(temporizador);
        },
        { once: true }
    );
}

function cerrarAlerta(alerta) {
    if (!alerta || alerta.classList.contains('is-hiding')) {
        return;
    }

    alerta.classList.add('is-hiding');

    window.setTimeout(() => {
        alerta.remove();
    }, 250);
}

function configurarFormularioCarga() {
    const formulario = document.querySelector('[data-upload-form]');

    if (!formulario) {
        return;
    }

    formulario.addEventListener('submit', (event) => {
        const inputArchivo = formulario.querySelector(
            'input[type="file"]'
        );

        const botonProcesar = formulario.querySelector(
            '[data-submit-button]'
        );

        if (!inputArchivo?.files?.length) {
            event.preventDefault();

            mostrarAlertaLocal(
                'Selecciona un archivo antes de procesarlo.',
                'error'
            );

            return;
        }

        if (botonProcesar) {
            botonProcesar.disabled = true;
            botonProcesar.classList.add('is-loading');

            const texto = botonProcesar.querySelector(
                '[data-button-text]'
            );

            if (texto) {
                texto.textContent = 'Procesando archivo...';
            }
        }
    });
}

function configurarAreaDeCarga() {
    const area = document.querySelector('[data-drop-area]');
    const input = document.querySelector('#archivo_excel');
    const nombre = document.querySelector('#file-name');

    if (!area || !input || !nombre) {
        return;
    }

    input.addEventListener('change', () => {
        actualizarNombreArchivo(input, nombre);
    });

    ['dragenter', 'dragover'].forEach((evento) => {
        area.addEventListener(evento, (event) => {
            event.preventDefault();
            area.classList.add('is-dragging');
        });
    });

    ['dragleave', 'drop'].forEach((evento) => {
        area.addEventListener(evento, (event) => {
            event.preventDefault();
            area.classList.remove('is-dragging');
        });
    });

    area.addEventListener('drop', (event) => {
        const archivos = event.dataTransfer?.files;

        if (!archivos?.length) {
            return;
        }

        const archivo = archivos[0];

        const extension = archivo.name
            .split('.')
            .pop()
            ?.toLowerCase();

        if (!['xlsx', 'xlsm'].includes(extension)) {
            mostrarAlertaLocal(
                'Solo se permiten archivos .xlsx y .xlsm.',
                'error'
            );

            return;
        }

        const transferencia = new DataTransfer();

        transferencia.items.add(archivo);
        input.files = transferencia.files;

        actualizarNombreArchivo(input, nombre);
    });
}

function actualizarNombreArchivo(input, elementoNombre) {
    const archivo = input.files?.[0];

    elementoNombre.textContent = archivo
        ? archivo.name
        : 'Ningún archivo seleccionado';
}

function mostrarAlertaLocal(mensaje, tipo = 'error') {
    const contenidoPrincipal = document.querySelector('.main-content');

    if (!contenidoPrincipal) {
        return;
    }

    const alertaAnterior = document.querySelector('[data-local-alert]');

    alertaAnterior?.remove();

    const alerta = document.createElement('div');

    alerta.className = `alert alert--${tipo}`;
    alerta.dataset.alert = '';
    alerta.dataset.localAlert = '';

    alerta.innerHTML = `
        <div class="alert__icon">
            ${tipo === 'success' ? '✓' : '!'}
        </div>

        <div class="alert__content">
            <strong>
                ${
                    tipo === 'success'
                        ? 'Proceso completado'
                        : 'Revisa la información'
                }
            </strong>

            <span>${escaparHtml(mensaje)}</span>
        </div>

        <button
            type="button"
            class="alert__close"
            aria-label="Cerrar alerta"
            data-alert-close
        >
            ×
        </button>
    `;

    document.body.appendChild(alerta);

    activarAlerta(alerta);
}

function escaparHtml(texto) {
    const elemento = document.createElement('div');

    elemento.textContent = texto;

    return elemento.innerHTML;
}