document.addEventListener('DOMContentLoaded', () => {
    inicializarExportacionListado();
});


function inicializarExportacionListado() {

    const boton =
        document.querySelector(
            '#btn-exportar-reportes'
        );

    if (!boton) {
        return;
    }


    boton.addEventListener('click', async () => {

        const filas =
            obtenerFilasFiltradas();


        if (!filas.length) {

            alert(
                'No hay reportes para exportar.'
            );

            return;
        }


        const reportes =
            filas.map(
                convertirFilaAReporte
            );


        const textoOriginal =
            boton.textContent;


        try {

            boton.disabled = true;
            boton.textContent =
                'Generando...';


            const datos =
                new FormData();


            datos.append(
                'reportes',
                JSON.stringify(
                    reportes
                )
            );


            const respuesta =
                await fetch(
                    '/asuntos-internos/reportes/listado/exportar',
                    {
                        method: 'POST',
                        body: datos,
                    }
                );


            if (!respuesta.ok) {

                let mensaje =
                    'No fue posible generar el archivo de Excel.';


                try {

                    const resultado =
                        await respuesta.json();


                    if (resultado?.message) {
                        mensaje =
                            resultado.message;
                    }

                } catch (error) {
                    // La respuesta no era JSON.
                }


                throw new Error(
                    mensaje
                );
            }


            const archivo =
                await respuesta.blob();


            const disposition =
                respuesta.headers.get(
                    'Content-Disposition'
                );


            const nombre =
                obtenerNombreArchivo(
                    disposition
                );


            const url =
                window.URL.createObjectURL(
                    archivo
                );


            const enlace =
                document.createElement(
                    'a'
                );


            enlace.href =
                url;


            enlace.download =
                nombre;


            document.body.appendChild(
                enlace
            );


            enlace.click();


            enlace.remove();


            window.URL.revokeObjectURL(
                url
            );

        } catch (error) {

            console.error(
                'Error exportando listado:',
                error
            );


            alert(
                error.message
                || 'No fue posible generar el archivo de Excel.'
            );

        } finally {

            boton.disabled = false;

            boton.textContent =
                textoOriginal;

        }

    });

}


/* =========================================================
   OBTENER REPORTES FILTRADOS
========================================================= */

function obtenerFilasFiltradas() {

    const tbody =
        document.querySelector(
            '#tabla-reportes-body'
        );


    if (!tbody) {
        return [];
    }


    /*
     * Importante:
     *
     * No usamos únicamente las filas visibles,
     * porque la paginación oculta las filas
     * que pertenecen a otras páginas.
     *
     * filtros.js guarda el estado lógico mediante
     * hidden, pero paginacion.js también lo modifica.
     *
     * Por ahora, mientras seguimos con datos temporales,
     * tomamos todas las filas reales de la tabla.
     *
     * Cuando conectemos BD, exportaremos por IDs/filtros
     * directamente desde backend.
     */
    return Array.from(
        tbody.querySelectorAll('tr')
    ).filter((fila) => {

        return !fila.classList.contains(
            'reportes-tabla__empty'
        );

    });

}


/* =========================================================
   CONVERTIR FILA A REPORTE TEMPORAL
========================================================= */

function convertirFilaAReporte(
    fila
) {

    const celdas =
        fila.querySelectorAll('td');


    return {

        /*
         * Datos que actualmente sí existen
         * en la tabla.
         */
        folio:
            celdas[0]?.textContent.trim()
            || '',

        fecha_queja:
            celdas[1]?.textContent.trim()
            || '',

        expediente:
            celdas[2]?.textContent.trim()
            || '',

        clasificacion:
            celdas[3]?.textContent.trim()
            || '',

        quejoso:
            celdas[4]?.textContent.trim()
            || '',

        area:
            celdas[5]?.textContent.trim()
            || '',

        turno:
            celdas[6]?.textContent.trim()
            || '',

        resolucion:
            celdas[7]?.textContent.trim()
            || '',


        /*
         * Campos preparados para el registro completo.
         *
         * En cuanto conectemos BD dejarán de venir
         * vacíos y serán obtenidos desde el backend.
         */
        prefijo: '',
        numero_folio: '',
        fecha_registro: '',

        folio_ip: '',
        fecha_acuerdo: '',
        nomenclatura: '',
        no_oficio: '',

        fecha_hechos: '',
        hora_hechos: '',
        descripcion: '',

        calle: '',
        numero: '',
        colonia: '',
        entre_calle: '',
        y_calle: '',
        municipio: '',
        estado: '',
        sector: '',
        cuadrante: '',
        latitud: '',
        longitud: '',

        oficial: '',

        unidad: '',
        unidad_marca: '',
        unidad_submarca: '',
        unidad_color: '',
        unidad_estatus: '',
        unidad_servicio_adscripcion: '',
        unidad_tipo_vehiculo: '',
        unidad_origen: '',

        edad: '',
        genero: '',
        telefono: '',
        correo: '',

        inspector: '',
        investigador: '',
        quien_emite_resolucion: '',
        motivos: '',

        observaciones: '',

        evidencias: [],
    };

}


/* =========================================================
   NOMBRE DEL ARCHIVO
========================================================= */

function obtenerNombreArchivo(
    contentDisposition
) {

    const predeterminado =
        'reportes_asuntos_internos.xlsx';


    if (!contentDisposition) {
        return predeterminado;
    }


    const coincidencia =
        contentDisposition.match(
            /filename="?([^"]+)"?/i
        );


    if (
        !coincidencia
        || !coincidencia[1]
    ) {

        return predeterminado;

    }


    return coincidencia[1].trim();

}