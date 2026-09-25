document.addEventListener('DOMContentLoaded', () => {

    /* =========================================================
       ELEMENTOS
    ========================================================= */

    const botonAbrir =
        document.getElementById(
            'btn-historial-dashboard'
        );

    const modal =
        document.getElementById(
            'modal-historial-dashboard'
        );

    const botonesCerrar =
        document.querySelectorAll(
            '[data-historial-cerrar]'
        );

    const tabs =
        document.querySelectorAll(
            '[data-historial-tab]'
        );

    const paneles =
        document.querySelectorAll(
            '[data-historial-panel]'
        );

    const inputBusqueda =
        document.getElementById(
            'historial-busqueda'
        );

    const listaQuejas =
        document.getElementById(
            'historial-lista-quejas'
        );

    const listaFelicitaciones =
        document.getElementById(
            'historial-lista-felicitaciones'
        );

    const totalQuejas =
        document.getElementById(
            'historial-total-quejas'
        );

    const totalFelicitaciones =
        document.getElementById(
            'historial-total-felicitaciones'
        );

    const estadoCargando =
        document.getElementById(
            'historial-cargando'
        );

    const estadoError =
        document.getElementById(
            'historial-error'
        );

    const errorMensaje =
        document.getElementById(
            'historial-error-mensaje'
        );

    const estadoVacio =
        document.getElementById(
            'historial-vacio'
        );


    /* =========================================================
       VALIDAR ELEMENTOS PRINCIPALES
    ========================================================= */

    if (
        !botonAbrir
        || !modal
    ) {
        return;
    }


    /* =========================================================
       ESTADO
    ========================================================= */

    let historial = {
        quejas: [],
        felicitaciones: [],
    };

    let tabActiva =
        'quejas';

    let historialCargado =
        false;

    let cargandoHistorial =
        false;


    /* =========================================================
       ABRIR MODAL
    ========================================================= */

    function abrirModal() {

        modal.classList.add(
            'dashboard-historial--activo'
        );

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.style.overflow =
            'hidden';


        if (
            inputBusqueda
        ) {
            inputBusqueda.value = '';
        }


        cambiarTab(
            'quejas'
        );


        if (
            !historialCargado
        ) {
            cargarHistorial();
        } else {
            renderizarHistorial();
        }
    }


    /* =========================================================
       CERRAR MODAL
    ========================================================= */

    function cerrarModal() {

        modal.classList.remove(
            'dashboard-historial--activo'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.style.overflow =
            '';
    }


    /* =========================================================
       CAMBIAR PESTAÑA
    ========================================================= */

    function cambiarTab(
        tipo
    ) {

        if (
            tipo !== 'quejas'
            && tipo !== 'felicitaciones'
        ) {
            return;
        }


        tabActiva =
            tipo;


        tabs.forEach(
            (tab) => {

                const activa =
                    tab.dataset.historialTab
                    === tipo;


                tab.classList.toggle(
                    'dashboard-historial__tab--activo',
                    activa
                );


                tab.setAttribute(
                    'aria-selected',
                    activa
                        ? 'true'
                        : 'false'
                );
            }
        );


        paneles.forEach(
            (panel) => {

                const activo =
                    panel.dataset.historialPanel
                    === tipo;


                panel.classList.toggle(
                    'dashboard-historial__panel--activo',
                    activo
                );


                panel.hidden =
                    !activo;
            }
        );


        renderizarHistorial();
    }


    /* =========================================================
       CARGAR HISTORIAL
    ========================================================= */

    async function cargarHistorial() {

        if (
            cargandoHistorial
        ) {
            return;
        }


        cargandoHistorial =
            true;


        mostrarEstado(
            'cargando'
        );


        try {

            const respuesta =
                await fetch(
                    `${window.location.origin}/DataCore/public/asuntos-internos/reportes/dashboard/historial`,
                    {
                        method: 'GET',
                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',
                            'Accept':
                                'application/json',
                        },
                    }
                );


            let datos = null;


            try {

                datos =
                    await respuesta.json();

            } catch (error) {

                throw new Error(
                    'El servidor devolvió una respuesta no válida.'
                );
            }


            if (
                respuesta.status === 401
            ) {

                throw new Error(
                    'Tu sesión ha expirado. Inicia sesión nuevamente.'
                );
            }


            if (
                !respuesta.ok
                || datos?.success !== true
            ) {

                throw new Error(
                    datos?.message
                    || 'No fue posible consultar el historial.'
                );
            }


            historial = {

                quejas:
                    Array.isArray(
                        datos?.historial?.quejas
                    )
                        ? datos.historial.quejas
                        : [],

                felicitaciones:
                    Array.isArray(
                        datos?.historial?.felicitaciones
                    )
                        ? datos.historial.felicitaciones
                        : [],

            };


            historialCargado =
                true;


            actualizarContadores();

            renderizarHistorial();

        } catch (error) {

            console.error(
                'Error cargando historial:',
                error
            );


            mostrarEstado(
                'error',
                error.message
            );

        } finally {

            cargandoHistorial =
                false;
        }
    }


    /* =========================================================
       ACTUALIZAR CONTADORES
    ========================================================= */

    function actualizarContadores() {

        if (
            totalQuejas
        ) {

            totalQuejas.textContent =
                String(
                    historial.quejas.length
                );
        }


        if (
            totalFelicitaciones
        ) {

            totalFelicitaciones.textContent =
                String(
                    historial.felicitaciones.length
                );
        }
    }


    /* =========================================================
       RENDERIZAR HISTORIAL
    ========================================================= */

    function renderizarHistorial() {

        if (
            !historialCargado
        ) {
            return;
        }


        ocultarEstados();


        const busqueda =
            normalizarTexto(
                inputBusqueda?.value
                || ''
            );


        const registrosOriginales =
            tabActiva === 'felicitaciones'
                ? historial.felicitaciones
                : historial.quejas;


        const registros =
            registrosOriginales.filter(
                (registro) => {

                    if (
                        busqueda === ''
                    ) {
                        return true;
                    }


                    const usuario =
                        registro?.usuario?.nombre
                        || '';


                    const contenido =
                        normalizarTexto(
                            [
                                registro?.folio,
                                registro?.accion,
                                registro?.tipo_accion,
                                registro?.tipo_entidad,
                                usuario,
                                registro?.fecha,
                                registro?.hora,
                            ]
                                .filter(Boolean)
                                .join(' ')
                        );


                    return contenido.includes(
                        busqueda
                    );
                }
            );


        const lista =
            tabActiva === 'felicitaciones'
                ? listaFelicitaciones
                : listaQuejas;


        if (
            !lista
        ) {
            return;
        }


        lista.innerHTML = '';


        if (
            registros.length === 0
        ) {

            mostrarEstado(
                'vacio'
            );

            return;
        }


        const fragmento =
            document.createDocumentFragment();


        registros.forEach(
            (registro) => {

                fragmento.appendChild(
                    crearTarjeta(
                        registro
                    )
                );
            }
        );


        lista.appendChild(
            fragmento
        );
    }


    /* =========================================================
       CREAR TARJETA
    ========================================================= */

    function crearTarjeta(
        registro
    ) {

        const tarjeta =
            document.createElement(
                'article'
            );


        tarjeta.className =
            'dashboard-historial__item';


        /* =====================================================
           ENCABEZADO
        ===================================================== */

        const encabezado =
            document.createElement(
                'div'
            );


        encabezado.className =
            'dashboard-historial__item-encabezado';


        const folioContenedor =
            document.createElement(
                'div'
            );


        folioContenedor.className =
            'dashboard-historial__folio-contenedor';


        const folioLabel =
            document.createElement(
                'span'
            );


        folioLabel.className =
            'dashboard-historial__folio-label';


        folioLabel.textContent =
            registro?.tipo_entidad
                === 'FELICITACION'
                ? 'Folio de felicitación'
                : 'Folio de reporte';


        const folio =
            document.createElement(
                'strong'
            );


        folio.className =
            'dashboard-historial__folio';


        folio.textContent =
            registro?.folio
            || '—';


        folioContenedor.append(
            folioLabel,
            folio
        );


        const badge =
            crearBadge(
                registro
            );


        encabezado.append(
            folioContenedor,
            badge
        );


        /* =====================================================
           TIMELINE
        ===================================================== */

        const timeline =
            document.createElement(
                'div'
            );


        timeline.className =
            'dashboard-historial__timeline';


        /* =====================================================
           ACCIÓN
        ===================================================== */

        const datoAccion =
            document.createElement(
                'div'
            );


        datoAccion.className =
            'dashboard-historial__dato';


        const accionLabel =
            document.createElement(
                'span'
            );


        accionLabel.className =
            'dashboard-historial__dato-label';


        accionLabel.textContent =
            'Acción';


        const accion =
            document.createElement(
                'p'
            );


        accion.className =
            'dashboard-historial__accion';


        accion.textContent =
            registro?.accion
            || 'Movimiento registrado.';


        datoAccion.append(
            accionLabel,
            accion
        );


        /* =====================================================
           USUARIO
        ===================================================== */

        const datoUsuario =
            document.createElement(
                'div'
            );


        datoUsuario.className =
            'dashboard-historial__dato';


        const usuarioLabel =
            document.createElement(
                'span'
            );


        usuarioLabel.className =
            'dashboard-historial__dato-label';


        usuarioLabel.textContent =
            'Realizado por';


        const usuario =
            crearUsuario(
                registro?.usuario
                || {}
            );


        datoUsuario.append(
            usuarioLabel,
            usuario
        );


        /* =====================================================
           FECHA
        ===================================================== */

        const datoFecha =
            document.createElement(
                'div'
            );


        datoFecha.className =
            'dashboard-historial__dato';


        const fechaLabel =
            document.createElement(
                'span'
            );


        fechaLabel.className =
            'dashboard-historial__dato-label';


        fechaLabel.textContent =
            'Fecha y hora';


        const fecha =
            document.createElement(
                'div'
            );


        fecha.className =
            'dashboard-historial__fecha';


        fecha.textContent =
            construirFecha(
                registro
            );


        datoFecha.append(
            fechaLabel,
            fecha
        );


        timeline.append(
            datoAccion,
            datoUsuario,
            datoFecha
        );


        tarjeta.append(
            encabezado,
            timeline
        );


        return tarjeta;
    }


    /* =========================================================
       CREAR BADGE
    ========================================================= */

    function crearBadge(
        registro
    ) {

        const badge =
            document.createElement(
                'span'
            );


        badge.className =
            'dashboard-historial__badge';


        const tipoEntidad =
            String(
                registro?.tipo_entidad
                || ''
            ).toUpperCase();


        const tipoAccion =
            String(
                registro?.tipo_accion
                || ''
            ).toUpperCase();


        if (
            tipoAccion === 'CAMBIAR_ESTADO'
        ) {

            badge.classList.add(
                'dashboard-historial__badge--estado'
            );

            badge.textContent =
                'Cambio de estado';


            return badge;
        }


        if (
            tipoEntidad === 'SEGUIMIENTO'
        ) {

            badge.classList.add(
                'dashboard-historial__badge--seguimiento'
            );


            badge.textContent =
                tipoAccion === 'EDITAR'
                    ? 'Seguimiento editado'
                    : 'Seguimiento';


            return badge;
        }


        switch (
            tipoAccion
        ) {

            case 'CREAR':

                badge.classList.add(
                    'dashboard-historial__badge--crear'
                );

                badge.textContent =
                    'Agregado';

                break;


            case 'EDITAR':

                badge.classList.add(
                    'dashboard-historial__badge--editar'
                );

                badge.textContent =
                    'Editado';

                break;


            case 'ELIMINAR':

                badge.classList.add(
                    'dashboard-historial__badge--eliminar'
                );

                badge.textContent =
                    'Eliminado';

                break;


            default:

                badge.textContent =
                    tipoAccion
                    || 'Movimiento';

                break;
        }


        return badge;
    }


    /* =========================================================
       CREAR USUARIO
    ========================================================= */

    function crearUsuario(
        datos
    ) {

        const contenedor =
            document.createElement(
                'div'
            );


        contenedor.className =
            'dashboard-historial__usuario';


        const fotoContenedor =
            document.createElement(
                'div'
            );


        fotoContenedor.className =
            'dashboard-historial__usuario-foto';


        const inicial =
            document.createElement(
                'span'
            );


        inicial.className =
            'dashboard-historial__usuario-inicial';


        inicial.textContent =
            datos?.inicial
            || '?';


        fotoContenedor.appendChild(
            inicial
        );


        const urlFoto =
            String(
                datos?.foto
                || ''
            ).trim();


        if (
            urlFoto !== ''
        ) {

            const imagen =
                document.createElement(
                    'img'
                );


            imagen.alt =
                datos?.nombre
                    ? `Foto de ${datos.nombre}`
                    : 'Foto de usuario';


            imagen.loading =
                'lazy';


            imagen.style.display =
                'none';


            imagen.addEventListener(
                'load',
                () => {

                    imagen.style.display =
                        'block';

                    inicial.style.display =
                        'none';
                }
            );


            imagen.addEventListener(
                'error',
                () => {

                    imagen.remove();

                    inicial.style.display =
                        'flex';
                }
            );


            imagen.src =
                urlFoto;


            fotoContenedor.appendChild(
                imagen
            );
        }


        const nombre =
            document.createElement(
                'span'
            );


        nombre.className =
            'dashboard-historial__usuario-nombre';


        nombre.textContent =
            datos?.nombre
            || 'Usuario';


        contenedor.append(
            fotoContenedor,
            nombre
        );


        return contenedor;
    }


    /* =========================================================
       CONSTRUIR FECHA
    ========================================================= */

    function construirFecha(
        registro
    ) {

        const fecha =
            String(
                registro?.fecha
                || ''
            ).trim();


        const hora =
            String(
                registro?.hora
                || ''
            ).trim();


        if (
            fecha !== ''
            && hora !== ''
            && fecha !== '—'
            && hora !== '—'
        ) {

            return `${fecha} · ${hora}`;
        }


        return (
            registro?.fecha_hora
            || fecha
            || hora
            || '—'
        );
    }


    /* =========================================================
       MOSTRAR ESTADO
    ========================================================= */

    function mostrarEstado(
        tipo,
        mensaje = ''
    ) {

        ocultarEstados();


        paneles.forEach(
            (panel) => {

                panel.hidden =
                    true;

                panel.classList.remove(
                    'dashboard-historial__panel--activo'
                );
            }
        );


        if (
            tipo === 'cargando'
            && estadoCargando
        ) {

            estadoCargando.hidden =
                false;

            return;
        }


        if (
            tipo === 'error'
            && estadoError
        ) {

            if (
                errorMensaje
            ) {

                errorMensaje.textContent =
                    mensaje
                    || 'Intenta nuevamente.';
            }


            estadoError.hidden =
                false;

            return;
        }


        if (
            tipo === 'vacio'
            && estadoVacio
        ) {

            estadoVacio.hidden =
                false;
        }
    }


    /* =========================================================
       OCULTAR ESTADOS
    ========================================================= */

    function ocultarEstados() {

        if (
            estadoCargando
        ) {
            estadoCargando.hidden =
                true;
        }


        if (
            estadoError
        ) {
            estadoError.hidden =
                true;
        }


        if (
            estadoVacio
        ) {
            estadoVacio.hidden =
                true;
        }


        paneles.forEach(
            (panel) => {

                const activo =
                    panel.dataset.historialPanel
                    === tabActiva;


                panel.hidden =
                    !activo;


                panel.classList.toggle(
                    'dashboard-historial__panel--activo',
                    activo
                );
            }
        );
    }


    /* =========================================================
       NORMALIZAR TEXTO PARA BUSCADOR
    ========================================================= */

    function normalizarTexto(
        valor
    ) {

        return String(
            valor
            || ''
        )
            .normalize('NFD')
            .replace(
                /[\u0300-\u036f]/g,
                ''
            )
            .toLowerCase()
            .trim();
    }


    /* =========================================================
       EVENTOS
    ========================================================= */

    botonAbrir.addEventListener(
        'click',
        abrirModal
    );


    botonesCerrar.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                cerrarModal
            );
        }
    );


    tabs.forEach(
        (tab) => {

            tab.addEventListener(
                'click',
                () => {

                    cambiarTab(
                        tab.dataset.historialTab
                    );
                }
            );
        }
    );


    if (
        inputBusqueda
    ) {

        inputBusqueda.addEventListener(
            'input',
            renderizarHistorial
        );
    }


    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
                && modal.classList.contains(
                    'dashboard-historial--activo'
                )
            ) {

                cerrarModal();
            }
        }
    );

});