document.addEventListener('DOMContentLoaded', () => {
    configurarFormularioCarga();
    configurarAreaDeCarga();
    configurarModalesDeResultado();
});

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

            mostrarModalLocal(
                'Revisa la información',
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
    const botonSeleccionar = document.querySelector(
        '[data-file-trigger]'
    );

    if (!area || !input || !nombre) {
        return;
    }

    botonSeleccionar?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        input.click();
    });

    botonSeleccionar?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        event.preventDefault();
        input.click();
    });

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
            mostrarModalLocal(
                'Formato no permitido',
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

function configurarModalesDeResultado() {
    const modales = document.querySelectorAll(
        '[data-result-modal]'
    );

    modales.forEach((modal) => {
        activarModalResultado(modal);
    });
}

function activarModalResultado(modal) {
    if (!modal) {
        return;
    }

    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');

    document.body.classList.add('modal-open');

    window.setTimeout(() => {
        cerrarModalResultado(modal);
    }, 3500);
}

function cerrarModalResultado(modal) {
    if (!modal || modal.hidden) {
        return;
    }

    modal.classList.add('is-hiding');

    window.setTimeout(() => {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('modal-open');

        if (modal.hasAttribute('data-local-modal')) {
            modal.remove();
        }
    }, 300);
}

function mostrarModalLocal(
    titulo,
    mensaje,
    tipo = 'error'
) {
    const modalAnterior = document.querySelector(
        '[data-local-modal]'
    );

    modalAnterior?.remove();

    const esExito = tipo === 'success';

    const modal = document.createElement('div');

    modal.className =
        `confirmation-modal confirmation-modal--${tipo}`;

    modal.dataset.resultModal = '';
    modal.dataset.localModal = '';

    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');

    modal.innerHTML = `
        <div class="confirmation-modal__backdrop"></div>

        <div
            class="confirmation-modal__dialog"
            role="status"
            aria-live="polite"
        >
            <div class="confirmation-modal__icon">
                ${esExito ? '✓' : '!'}
            </div>

            <div class="confirmation-modal__content">

                <span class="confirmation-modal__label">
                    ${esExito
                        ? 'Proceso completado'
                        : 'Atención'}
                </span>

                <h2>
                    ${escaparHtml(titulo)}
                </h2>

                <p>
                    ${escaparHtml(mensaje)}
                </p>

            </div>
        </div>
    `;

    document.body.appendChild(modal);

    activarModalResultado(modal);
}

function escaparHtml(texto) {
    const elemento = document.createElement('div');

    elemento.textContent = texto;

    return elemento.innerHTML;
}