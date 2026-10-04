/* =========================================================
   DASHBOARD
   Generador de analisis - preparacion previa a IA/Word
   ---------------------------------------------------------
   Mantiene filtros y secciones independientes del Dashboard
   visible. La vista previa solo muestra informacion agregada
   y narrativas devueltas por backend ya sanitizadas.

   TODO IA/WORD:
   Pendiente activar boton cuando exista API key institucional
   y se defina la generacion de Word editable.
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarInformeDashboard();
    }
);


const CAMPOS_FILTROS_INFORME = [
    'fecha_registro_inicio',
    'fecha_registro_fin',
    'tipo',
    'estado',
    'clasificacion',
    'seguimiento',
    'es_anonimo',
    'zona',
    'sector',
    'turno',
    'area_personal',
    'personal',
    'unidad',
];

const CAMPOS_CONFIGURACION_INFORME = [
    'dimension',
    'cruce_principal',
    'cruce_secundaria',
];

const MAPA_FILTROS_DASHBOARD = {
    fecha_registro_inicio:
        '#dashboard-fecha-registro-inicio',
    fecha_registro_fin:
        '#dashboard-fecha-registro-fin',
    tipo:
        '#dashboard-tipo',
    estado:
        '#dashboard-estado',
    clasificacion:
        '#dashboard-clasificacion',
    seguimiento:
        '#dashboard-seguimiento',
    es_anonimo:
        '#dashboard-anonima',
    zona:
        '#dashboard-zona',
    sector:
        '#dashboard-sector',
    turno:
        '#dashboard-turno',
    area_personal:
        '#dashboard-area-personal',
    personal:
        '#dashboard-personal',
    unidad:
        '#dashboard-unidad',
};

const CRUCE_SECUNDARIAS = {
    sector: [
        'turno',
        'estado',
    ],
    zona: [
        'turno',
        'estado',
    ],
    area: [
        'turno',
        'estado',
    ],
    turno: [
        'estado',
    ],
};

let filtrosInforme = {};


function inicializarInformeDashboard() {

    const botonAbrir =
        document.querySelector(
            '#btn-generar-informe-dashboard'
        );

    const modal =
        document.querySelector(
            '#modal-informe-dashboard'
        );

    const formulario =
        document.querySelector(
            '#form-informe-dashboard'
        );

    if (!botonAbrir || !modal || !formulario) {
        return;
    }

    botonAbrir.addEventListener(
        'click',
        () => {

            filtrosInforme =
                copiarFiltrosDashboard();

            cargarFiltrosEnFormulario(
                formulario,
                filtrosInforme
            );

            limpiarVistaPreviaInforme();
            actualizarEstadoFormularioInforme(
                formulario
            );
            abrirModalInforme(
                modal
            );
        }
    );

    modal.addEventListener(
        'click',
        evento => {

            if (!evento.target.closest('[data-cerrar-modal-informe]')) {
                return;
            }

            cerrarModalInforme(
                modal
            );
        }
    );

    document.addEventListener(
        'keydown',
        evento => {

            if (
                evento.key === 'Escape'
                && modal.classList.contains('modal-reporte--visible')
            ) {

                cerrarModalInforme(
                    modal
                );
            }
        }
    );

    formulario.addEventListener(
        'change',
        () => {

            filtrosInforme =
                obtenerFiltrosFormulario(
                    formulario
                );

            actualizarEstadoFormularioInforme(
                formulario
            );
        }
    );

    formulario.addEventListener(
        'input',
        evento => {

            if (
                CAMPOS_FILTROS_INFORME.includes(
                    evento.target.name
                )
            ) {

            filtrosInforme =
                obtenerFiltrosFormulario(
                    formulario
                );
            }
        }
    );

    formulario.addEventListener(
        'submit',
        async evento => {

            evento.preventDefault();

            await prepararInformeDashboard(
                formulario
            );
        }
    );
}


function copiarFiltrosDashboard() {

    const valores = {};

    CAMPOS_FILTROS_INFORME.forEach(
        campo => {

            const valor =
                document.querySelector(
                    MAPA_FILTROS_DASHBOARD[campo]
                )?.value
                ?? '';

            valores[campo] =
                campo === 'personal'
                    ? obtenerValorPersonalGlobalParaInforme()
                    : String(
                        valor
                    );
        }
    );

    return {
        ...valores,
    };
}


function cargarFiltrosEnFormulario(
    formulario,
    filtros
) {

    CAMPOS_FILTROS_INFORME.forEach(
        campo => {

            const control =
                formulario.elements[campo];

            if (!control) {
                return;
            }

            control.value =
                filtros[campo]
                ?? '';
        }
    );
}


function obtenerFiltrosFormulario(
    formulario
) {

    const filtros = {};

    CAMPOS_FILTROS_INFORME.forEach(
        campo => {

            const valor =
                formulario.elements[campo]?.value
                ?? '';

            filtros[campo] =
                campo === 'personal'
                    ? normalizarValorPersonal(
                        valor
                    )
                    : String(
                        valor
                    );
        }
    );

    return {
        ...filtros,
    };
}


function obtenerConfiguracionFormulario(
    formulario
) {

    const configuracion = {};

    CAMPOS_CONFIGURACION_INFORME.forEach(
        campo => {

            configuracion[campo] =
                String(
                    formulario.elements[campo]?.value
                    ?? ''
                );
        }
    );

    return configuracion;
}


function obtenerSeccionesSeleccionadas(
    formulario
) {

    return Array.from(
        formulario.querySelectorAll(
            'input[name="secciones[]"]:checked:not(:disabled)'
        )
    ).map(
        opcion => opcion.value
    );
}


function actualizarEstadoFormularioInforme(
    formulario
) {

    const tipo =
        String(
            formulario.elements.tipo?.value
            ?? ''
        )
            .trim()
            .toUpperCase();

    const esFelicitacion =
        tipo === 'FELICITACION';

    formulario
        .querySelectorAll('[data-informe-quejas-only]')
        .forEach(
            campo => {

                campo.hidden =
                    esFelicitacion;

                const control =
                    campo.querySelector('input, select, textarea');

                if (control) {

                    control.disabled =
                        esFelicitacion;

                    if (esFelicitacion) {
                        control.value = '';
                    }
                }
            }
        );

    formulario
        .querySelectorAll('[data-informe-seccion-quejas-only]')
        .forEach(
            opcion => {

                const control =
                    opcion.querySelector('input');

                opcion.classList.toggle(
                    'dashboard-informe__opcion--deshabilitada',
                    esFelicitacion
                );

                if (control) {

                    control.disabled =
                        esFelicitacion;

                    if (esFelicitacion) {
                        control.checked = false;
                    }
                }
            }
        );

    actualizarOpcionesCruce(
        formulario,
        esFelicitacion
    );
}


function obtenerValorPersonalGlobalParaInforme() {

    const inputPersonal =
        document.querySelector(
            '#dashboard-personal'
        );

    const contenedorSeleccion =
        document.querySelector(
            '#dashboard-personal-seleccion'
        );

    const textoSeleccion =
        document.querySelector(
            '#dashboard-personal-seleccion-texto'
        );

    const valorRaw =
        inputPersonal?.value
        ?? '';

    const valor =
        normalizarValorPersonal(
            valorRaw
        );

    const texto =
        String(
            textoSeleccion?.textContent
            ?? ''
        ).trim();

    const seleccionVisible =
        !!contenedorSeleccion
        && contenedorSeleccion.hidden === false;

    console.log(
        'DEBUG FILTRO PERSONAL GENERADOR',
        {
            valorRaw,
            tipo:
                typeof valorRaw,
            longitud:
                String(
                    valorRaw
                    ?? ''
                ).length,
            hiddenExiste:
                !!inputPersonal,
            seleccionVisible,
            textoSeleccionLongitud:
                texto.length,
        }
    );

    if (
        valor === ''
        || !seleccionVisible
        || texto === ''
    ) {

        return '';
    }

    return valor;
}


function esFiltroPersonalActivo(
    valor
) {

    return normalizarValorPersonal(
        valor
    ) !== '';
}


function normalizarValorPersonal(
    valor
) {

    const normalizado =
        String(
            valor
            ?? ''
        )
            .trim();

    if (
        normalizado === ''
        || normalizado === '0'
        || normalizado.toLowerCase() === 'null'
        || normalizado.toLowerCase() === 'undefined'
    ) {

        return '';
    }

    return normalizado;
}


function actualizarOpcionesCruce(
    formulario,
    esFelicitacion
) {

    const principal =
        formulario.elements.cruce_principal;

    const secundaria =
        formulario.elements.cruce_secundaria;

    if (!principal || !secundaria) {
        return;
    }

    let valorPrincipal =
        principal.value
        || 'sector';

    if (
        esFelicitacion
        && valorPrincipal === 'turno'
    ) {

        valorPrincipal =
            'sector';

        principal.value =
            valorPrincipal;
    }

    let opciones =
        CRUCE_SECUNDARIAS[valorPrincipal]
        || [
            'turno',
        ];

    if (esFelicitacion) {
        opciones =
            opciones.filter(
                opcion => opcion !== 'estado'
            );
    }

    if (opciones.length === 0) {
        opciones = [
            'turno',
        ];
    }

    const valorActual =
        secundaria.value;

    secundaria.innerHTML = '';

    opciones.forEach(
        opcion => {

            const option =
                document.createElement('option');

            option.value =
                opcion;

            option.textContent =
                etiquetaConfiguracion(
                    opcion
                );

            secundaria.appendChild(
                option
            );
        }
    );

    secundaria.value =
        opciones.includes(valorActual)
            ? valorActual
            : opciones[0];
}


async function prepararInformeDashboard(
    formulario
) {

    const mensaje =
        document.querySelector(
            '#dashboard-informe-mensaje'
        );

    const secciones =
        obtenerSeccionesSeleccionadas(
            formulario
        );

    ocultarMensajeInforme(
        mensaje
    );

    if (secciones.length === 0) {

        mostrarMensajeInforme(
            mensaje,
            'Selecciona al menos una seccion para preparar el analisis.'
        );

        return;
    }

    const boton =
        formulario.querySelector(
            'button[type="submit"]'
        );

    if (boton) {
        boton.disabled = true;
    }

    try {

        const respuesta =
            await fetch(
                construirUrlInforme(),
                {
                    method:
                        'POST',

                    headers: {
                        Accept:
                            'application/json',

                        'Content-Type':
                            'application/json',
                    },

                    body:
                        JSON.stringify({
                            filtros:
                                obtenerFiltrosFormulario(
                                    formulario
                                ),
                            configuracion:
                                obtenerConfiguracionFormulario(
                                    formulario
                                ),
                            secciones,
                        }),
                }
            );

        const datos =
            await respuesta.json();

        if (!respuesta.ok) {

            throw new Error(
                datos?.message
                || 'No fue posible preparar el analisis.'
            );
        }

        renderizarPreparacionInforme(
            datos
        );

    } catch (error) {

        console.error(
            'Error preparando analisis:',
            error
        );

        mostrarMensajeInforme(
            mensaje,
            error.message
            || 'No fue posible preparar el analisis.'
        );

    } finally {

        if (boton) {
            boton.disabled = false;
        }
    }
}


function construirUrlInforme() {

    const base =
        window.location.pathname
            .split('/asuntos-internos/reportes')[0];

    return `${window.location.origin}${base}/asuntos-internos/reportes/dashboard/informe/preparar`;
}


function renderizarPreparacionInforme(
    datos
) {

    const contenedor =
        document.querySelector(
            '#dashboard-informe-preview'
        );

    if (!contenedor) {
        return;
    }

    const secciones =
        datos.secciones_solicitadas
        || [];

    contenedor.innerHTML = `
        <div class="dashboard-informe__intro">
            <h3>Analisis preparado</h3>
            <p>Esta etapa valida filtros, secciones y compatibilidad. La narrativa IA queda preparada y pendiente de validacion real con API institucional; el Word editable se integrara despues.</p>
        </div>

        ${datos.filtro_personal_aplicado ? `
            <div class="dashboard-informe__aviso dashboard-informe__aviso--personal">
                <strong>Filtro Personal aplicado</strong>
                <p>El identificador no se muestra ni se enviara a IA. Solo se usaran resultados estadisticos agregados.</p>
            </div>
        ` : ''}

        <div class="dashboard-informe__preview-grid">
            ${crearBloquePreview('Criterios del analisis', [
                ['Periodo', datos.periodo?.texto],
                ['Tipo', etiquetaTipo(datos.tipo)],
                ...Object.entries(datos.filtros_activos || {})
                    .filter(([campo]) => !['fecha_registro_inicio', 'fecha_registro_fin', 'tipo', 'personal'].includes(campo))
                    .map(([campo, valor]) => [etiquetaFiltro(campo), valor]),
            ])}

            ${crearBloquePreview('Configuracion', [
                ['Dimension', etiquetaConfiguracion(datos.configuracion?.dimension)],
                ['Cruce principal', etiquetaConfiguracion(datos.configuracion?.cruce_principal)],
                ['Cruce secundaria', etiquetaConfiguracion(datos.configuracion?.cruce_secundaria)],
            ])}
        </div>

        ${crearNarrativasInforme(datos.narrativas)}

        <div class="dashboard-informe__preview-secciones">
            <h4>Secciones preparadas</h4>
            ${secciones.map(clave => crearResumenSeccion(clave, datos.secciones?.[clave])).join('')}
        </div>
    `;

    contenedor.hidden =
        false;
}


function crearNarrativasInforme(
    narrativas
) {

    if (!narrativas || typeof narrativas !== 'object') {
        return '';
    }

    const contenido =
        Object.values(narrativas)
            .filter(narrativa => narrativa && typeof narrativa === 'object')
            .map(narrativa => {

                const texto =
                    narrativa.ok === true
                        ? narrativa.narrativa
                        : (
                            narrativa.message
                            || 'No fue posible generar el analisis automatico.'
                        );

                return `
                    <article class="dashboard-informe__narrativa">
                        <h4>${escaparHtml(narrativa.titulo || etiquetaSeccion(narrativa.seccion))}</h4>
                        <p>${escaparHtml(texto).replace(/\n/g, '<br>')}</p>
                    </article>
                `;
            })
            .join('');

    if (contenido === '') {
        return '';
    }

    return `
        <section class="dashboard-informe__narrativas">
            <h4>Analisis generado por IA</h4>
            ${contenido}
        </section>
    `;
}


function crearBloquePreview(
    titulo,
    filas
) {

    const contenido =
        filas
            .filter(([, valor]) => String(valor ?? '').trim() !== '')
            .map(([etiqueta, valor]) => `
                <div>
                    <span>${escaparHtml(etiqueta)}</span>
                    <strong>${escaparHtml(valor)}</strong>
                </div>
            `)
            .join('');

    return `
        <article class="dashboard-informe__preview-card">
            <h4>${escaparHtml(titulo)}</h4>
            ${contenido || '<p>Sin criterios adicionales.</p>'}
        </article>
    `;
}


function crearResumenSeccion(
    clave,
    valor
) {

    return `
        <article class="dashboard-informe__seccion-preview">
            <strong>${escaparHtml(etiquetaSeccion(clave))}</strong>
            <p>${escaparHtml(describirSeccionPreparada(valor))}</p>
        </article>
    `;
}


function describirSeccionPreparada(
    valor
) {

    if (Array.isArray(valor)) {
        return `${valor.length} registros agregados preparados.`;
    }

    if (valor && typeof valor === 'object') {
        return `${Object.keys(valor).length} grupos de datos preparados.`;
    }

    return 'Datos preparados para la siguiente etapa.';
}


function limpiarVistaPreviaInforme() {

    const contenedor =
        document.querySelector(
            '#dashboard-informe-preview'
        );

    if (!contenedor) {
        return;
    }

    contenedor.innerHTML = '';
    contenedor.hidden = true;

    ocultarMensajeInforme(
        document.querySelector(
            '#dashboard-informe-mensaje'
        )
    );
}


function etiquetaFiltro(
    campo
) {

    return {
        estado: 'Estado',
        clasificacion: 'Clasificacion',
        seguimiento: 'Seguimiento',
        es_anonimo: 'Queja anonima',
        zona: 'Zona',
        sector: 'Sector',
        turno: 'Turno',
        area_personal: 'Area',
        unidad: 'Unidad',
    }[campo] || campo;
}


function etiquetaSeccion(
    clave
) {

    return {
        indicadores: 'Indicadores',
        catalogo: 'Catalogo / clasificacion',
        evolucion: 'Evolucion temporal',
        estado: 'Estado de las Quejas',
        zona: 'Zona',
        turno: 'Turno',
        sector: 'Sector',
        area: 'Area',
        unidad: 'Unidad',
        sanciones: 'Sanciones disciplinarias',
        cruce: 'Analisis cruzado',
        ranking_sector: 'Ranking Sector',
        ranking_area: 'Ranking Area',
        ranking_unidad: 'Ranking Unidad',
        hallazgos: 'Hallazgos',
        comparativa: 'Comparativa temporal',
    }[clave] || clave;
}


function etiquetaTipo(
    tipo
) {

    return {
        QUEJA: 'Queja',
        QUEJA_VERBAL: 'Queja verbal',
        QUEJA_FORANEA: 'Queja foranea',
        FELICITACION: 'Felicitaciones',
    }[tipo] || 'Todos';
}


function etiquetaConfiguracion(
    valor
) {

    return {
        sector: 'Sector',
        zona: 'Zona',
        area: 'Area',
        unidad: 'Unidad',
        turno: 'Turno',
        estado: 'Estado',
    }[valor] || valor;
}


function escaparHtml(
    valor
) {

    return String(valor ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


function mostrarMensajeInforme(
    mensaje,
    texto
) {

    if (!mensaje) {
        return;
    }

    mensaje.textContent = texto;
    mensaje.hidden = false;
}


function ocultarMensajeInforme(
    mensaje
) {

    if (!mensaje) {
        return;
    }

    mensaje.textContent = '';
    mensaje.hidden = true;
}


function abrirModalInforme(
    modal
) {

    modal.classList.add('modal-reporte--visible');
    modal.setAttribute('aria-hidden', 'false');
}


function cerrarModalInforme(
    modal
) {

    modal.classList.remove('modal-reporte--visible');
    modal.setAttribute('aria-hidden', 'true');
}
