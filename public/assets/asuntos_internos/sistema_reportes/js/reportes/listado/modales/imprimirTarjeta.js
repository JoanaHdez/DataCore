document.addEventListener('DOMContentLoaded', () => {
    inicializarImpresionTarjeta();
});


function inicializarImpresionTarjeta() {

    const botonImprimir =
        document.querySelector(
            '#btn-imprimir-tarjeta'
        );

    if (!botonImprimir) {
        return;
    }


    botonImprimir.addEventListener(
        'click',
        () => {

            const tarjeta =
                document.querySelector(
                    '#tarjeta-reporte'
                );

            if (!tarjeta) {
                return;
            }


            imprimirTarjeta(
                tarjeta
            );
        }
    );
}


/**
 * Imprime únicamente el contenido
 * de la tarjeta informativa.
 */
function imprimirTarjeta(
    tarjeta
) {

    const ventanaImpresion =
        window.open(
            '',
            '_blank',
            'width=1000,height=800'
        );

    if (!ventanaImpresion) {
        return;
    }


    /*
     * Tomamos el contenido actual
     * de la tarjeta.
     */
    const contenido =
        tarjeta.outerHTML;


    /*
     * URL absoluta del CSS de impresión.
     */
    /* const baseUrl =
        `${window.location.origin}/`;

    const cssImpresion =
        `${baseUrl}`
        + 'assets/asuntos_internos/'
        + 'sistema_reportes/css/'
        + 'reportes/listado/'
        + 'imprimir-tarjeta.css'; */


        const baseUrl =
    `${window.location.origin}/DataCore/public/`;

const cssImpresion =
    `${baseUrl}`
    + 'assets/asuntos_internos/'
    + 'sistema_reportes/css/'
    + 'reportes/listado/'
    + 'imprimir-tarjeta.css';


    /*
     * Documento independiente
     * para impresión.
     */
    ventanaImpresion.document.open();

    ventanaImpresion.document.write(`
        <!DOCTYPE html>

        <html lang="es">

        <head>

            <meta charset="UTF-8">

            <meta
                name="viewport"
                content="width=device-width, initial-scale=1.0"
            >

            <title>
                Tarjeta informativa
            </title>

            <link
                rel="stylesheet"
                href="${cssImpresion}"
            >

        </head>

        <body>

            <main class="tarjeta-impresion">

                ${contenido}

            </main>

        </body>

        </html>
    `);

    ventanaImpresion.document.close();


    /*
     * Esperamos a que cargue el CSS
     * antes de abrir el diálogo
     * de impresión.
     */
    ventanaImpresion.addEventListener(
        'load',
        () => {

            window.setTimeout(
                () => {

                    ventanaImpresion.focus();

                    ventanaImpresion.print();

                },
                250
            );

        }
    );
}