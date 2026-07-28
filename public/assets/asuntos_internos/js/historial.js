document.addEventListener('DOMContentLoaded', () => {
    iniciarHistorial();
});

function iniciarHistorial() {

    const buscador = document.querySelector('#buscar-archivo');
    const filtroFecha = document.querySelector('#buscar-fecha');
    const botonLimpiar = document.querySelector('#limpiar-filtros');

    const filas = document.querySelectorAll('[data-file-row]');
    const sinResultados = document.querySelector('#sin-resultados');

    if (!buscador || !filtroFecha) {
        return;
    }

    buscador.addEventListener('input', aplicarFiltros);
    filtroFecha.addEventListener('change', aplicarFiltros);

    botonLimpiar?.addEventListener('click', () => {

        buscador.value = '';
        filtroFecha.value = '';

        aplicarFiltros();

        buscador.focus();

    });

    aplicarFiltros();

    function aplicarFiltros() {

        const texto = normalizarTexto(
            buscador.value
        );

        const fecha = filtroFecha.value;

        let visibles = 0;

        filas.forEach((fila) => {

            const nombre = normalizarTexto(
                fila.dataset.fileName ?? ''
            );

            const fechaFila =
                fila.dataset.fileDate ?? '';

            const coincideNombre =
                texto === ''
                || nombre.includes(texto);

            const coincideFecha =
                fecha === ''
                || fecha === fechaFila;

            const mostrar =
                coincideNombre
                && coincideFecha;

            fila.hidden = !mostrar;

            if (mostrar) {
                visibles++;
            }

        });

        if (sinResultados) {

            sinResultados.hidden =
                visibles !== 0;

        }

    }

}

function normalizarTexto(texto) {

    return texto
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim();

}