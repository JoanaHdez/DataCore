document.addEventListener('DOMContentLoaded', () => {
    iniciarHistorial();
    configurarConfirmacionEliminacion();
});

function iniciarHistorial() {
    const buscador = document.querySelector('#buscar-archivo');
    const filtroFecha = document.querySelector('#buscar-fecha');
    const botonLimpiar = document.querySelector('#limpiar-filtros');

    const filas = Array.from(
        document.querySelectorAll('[data-file-row]')
    );

    const sinResultados = document.querySelector('#sin-resultados');

    const botonAnterior = document.querySelector('#pagina-anterior');
    const botonSiguiente = document.querySelector('#pagina-siguiente');
    const resumen = document.querySelector('#pagination-summary');
    const contador = document.querySelector('#pagination-counter');
    const paginador = document.querySelector('#history-pagination');

    if (!buscador || !filtroFecha) {
        return;
    }

    const registrosPorPagina = 5;

    let paginaActual = 1;
    let filasFiltradas = [...filas];

    buscador.addEventListener('input', () => {
        paginaActual = 1;
        aplicarFiltros();
    });

    filtroFecha.addEventListener('change', () => {
        paginaActual = 1;
        aplicarFiltros();
    });

    botonLimpiar?.addEventListener('click', () => {
        buscador.value = '';
        filtroFecha.value = '';
        paginaActual = 1;

        aplicarFiltros();
        buscador.focus();
    });

    botonAnterior?.addEventListener('click', () => {
        if (paginaActual <= 1) {
            return;
        }

        paginaActual--;
        mostrarPagina();
    });

    botonSiguiente?.addEventListener('click', () => {
        const totalPaginas = obtenerTotalPaginas();

        if (paginaActual >= totalPaginas) {
            return;
        }

        paginaActual++;
        mostrarPagina();
    });

    function aplicarFiltros() {
        const textoBusqueda = normalizarTexto(buscador.value);
        const fechaSeleccionada = filtroFecha.value;

        filasFiltradas = filas.filter((fila) => {
            const nombre = normalizarTexto(
                fila.dataset.fileName ?? ''
            );

            const fecha = fila.dataset.fileDate ?? '';

            const coincideNombre =
                textoBusqueda === ''
                || nombre.includes(textoBusqueda);

            const coincideFecha =
                fechaSeleccionada === ''
                || fecha === fechaSeleccionada;

            return coincideNombre && coincideFecha;
        });

        actualizarBotonLimpiar();
        mostrarPagina();
    }

    function mostrarPagina() {
        filas.forEach((fila) => {
            fila.hidden = true;
        });

        const totalRegistros = filasFiltradas.length;
        const totalPaginas = obtenerTotalPaginas();

        if (paginaActual > totalPaginas) {
            paginaActual = totalPaginas;
        }

        if (totalRegistros === 0) {
            sinResultados?.removeAttribute('hidden');

            if (resumen) {
                resumen.textContent = 'No se encontraron archivos';
            }

            if (contador) {
                contador.textContent = 'Página 0 de 0';
            }

            if (paginador) {
                paginador.hidden = filas.length === 0;
            }

            actualizarBotones(0);

            return;
        }

        sinResultados?.setAttribute('hidden', '');

        const inicio = (paginaActual - 1) * registrosPorPagina;

        const fin = Math.min(
            inicio + registrosPorPagina,
            totalRegistros
        );

        filasFiltradas
            .slice(inicio, fin)
            .forEach((fila) => {
                fila.hidden = false;
            });

        if (resumen) {
            resumen.textContent =
                `Mostrando ${inicio + 1}–${fin} de `
                + `${totalRegistros} archivos`;
        }

        if (contador) {
            contador.textContent =
                `Página ${paginaActual} de ${totalPaginas}`;
        }

        if (paginador) {
            paginador.hidden = false;
        }

        actualizarBotones(totalPaginas);
    }

    function obtenerTotalPaginas() {
        return Math.max(
            1,
            Math.ceil(
                filasFiltradas.length / registrosPorPagina
            )
        );
    }

    function actualizarBotones(totalPaginas) {
        if (botonAnterior) {
            botonAnterior.disabled =
                paginaActual <= 1
                || filasFiltradas.length === 0;
        }

        if (botonSiguiente) {
            botonSiguiente.disabled =
                paginaActual >= totalPaginas
                || filasFiltradas.length === 0;
        }
    }

    function actualizarBotonLimpiar() {
        if (!botonLimpiar) {
            return;
        }

        const hayFiltros =
            buscador.value.trim() !== ''
            || filtroFecha.value !== '';

        botonLimpiar.disabled = !hayFiltros;
    }

    aplicarFiltros();
}

function configurarConfirmacionEliminacion() {
    const formularios = document.querySelectorAll(
        '[data-delete-form]'
    );

    formularios.forEach((formulario) => {
        formulario.addEventListener('submit', (event) => {
            event.preventDefault();

            mostrarConfirmacionEliminacion(formulario);
        });
    });
}

function mostrarConfirmacionEliminacion(formulario) {
    const modalAnterior = document.querySelector(
        '[data-delete-confirmation-modal]'
    );

    modalAnterior?.remove();

    const nombreArchivo =
        formulario.dataset.fileName || 'este archivo';

    const modal = document.createElement('div');

    modal.className = 'confirmation-modal';
    modal.dataset.deleteConfirmationModal = '';

    modal.setAttribute('aria-hidden', 'false');

    modal.innerHTML = `
        <div
            class="confirmation-modal__backdrop"
            data-delete-cancel
        ></div>

        <div
            class="confirmation-modal__dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-confirmation-title"
            aria-describedby="delete-confirmation-description"
        >
            <button
                type="button"
                class="confirmation-modal__close"
                data-delete-cancel
                aria-label="Cerrar"
            >
                ×
            </button>

            <div class="confirmation-modal__icon">
                !
            </div>

            <div class="confirmation-modal__content">

                <span class="confirmation-modal__label">
                    Confirmar eliminación
                </span>

                <h2 id="delete-confirmation-title">
                    ¿Eliminar este archivo?
                </h2>

                <p id="delete-confirmation-description">
                    Se eliminará:
                </p>

                <strong class="confirmation-modal__file-name">
                    ${escaparHtmlHistorial(nombreArchivo)}
                </strong>

                <p class="confirmation-modal__warning">
                    Esta acción no se puede deshacer.
                </p>

            </div>

            <div class="confirmation-modal__actions">

                <button
                    type="button"
                    class="button button--secondary"
                    data-delete-cancel
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="button button--danger"
                    data-delete-confirm
                >
                    Eliminar archivo
                </button>

            </div>
        </div>
    `;

    document.body.appendChild(modal);
    document.body.classList.add('modal-open');

    const botonConfirmar = modal.querySelector(
        '[data-delete-confirm]'
    );

    const elementosCancelar = modal.querySelectorAll(
        '[data-delete-cancel]'
    );

    const cerrarConEscape = (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        cerrarModal();
    };

    const cerrarModal = () => {
        document.removeEventListener(
            'keydown',
            cerrarConEscape
        );

        cerrarConfirmacionEliminacion(modal);
    };

    elementosCancelar.forEach((elemento) => {
        elemento.addEventListener('click', cerrarModal);
    });

    botonConfirmar?.addEventListener('click', () => {
        botonConfirmar.disabled = true;
        botonConfirmar.textContent = 'Eliminando...';

        document.removeEventListener(
            'keydown',
            cerrarConEscape
        );

        formulario.submit();
    });

    document.addEventListener(
        'keydown',
        cerrarConEscape
    );

    botonConfirmar?.focus();
}

function cerrarConfirmacionEliminacion(modal) {
    if (!modal) {
        return;
    }

    modal.classList.add('is-hiding');

    window.setTimeout(() => {
        modal.remove();
        document.body.classList.remove('modal-open');
    }, 300);
}

function normalizarTexto(texto) {
    return texto
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim();
}

function escaparHtmlHistorial(texto) {
    const elemento = document.createElement('div');

    elemento.textContent = texto;

    return elemento.innerHTML;
}