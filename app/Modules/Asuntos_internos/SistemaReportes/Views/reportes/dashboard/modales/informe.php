<?php
/*
 * GENERADOR DE ANALISIS DASHBOARD
 * ---------------------------------------------------------
 * Modal tecnico preparado para IA + Word futuro. Permanece
 * cargado para no perder la integracion, pero el boton visual
 * de acceso se controla desde sections/encabezado.php.
 *
 * TODO IA/WORD:
 * Pendiente validar narrativa con API key institucional y
 * definir dependencia para documento Word editable.
 */
?>

<div class="modal-reporte" id="modal-informe-dashboard" aria-hidden="true">

    <div class="modal-reporte__overlay" data-cerrar-modal-informe></div>

    <div class="modal-reporte__dialog modal-reporte__dialog--informe" role="dialog" aria-modal="true"
        aria-labelledby="modal-informe-dashboard-titulo">

        <div class="modal-reporte__header">

            <div>
                <span class="modal-reporte__eyebrow">
                    Generador de analisis
                </span>

                <h2 class="modal-reporte__title" id="modal-informe-dashboard-titulo">
                    Descargar documento editable
                </h2>
            </div>

            <button type="button" class="modal-reporte__close" data-cerrar-modal-informe aria-label="Cerrar">
                &times;
            </button>

        </div>

        <form id="form-informe-dashboard">

            <div class="modal-reporte__body">

                <div class="dashboard-informe">

                    <section class="dashboard-informe__bloque">
                        <div class="dashboard-informe__aviso dashboard-informe__aviso--seguridad">
                            <h3>
                                Proteccion de informacion
                            </h3>
                            <p>
                                El documento generado utilizara unicamente datos estadisticos y agregados del
                                Dashboard. No se incluiran nombres, nominas, fotografias, folios ni informacion
                                personal o individual identificable.
                            </p>
                        </div>

                        <div class="dashboard-informe__aviso dashboard-informe__aviso--personal">
                            <h3>
                                Uso del filtro Personal
                            </h3>
                            <p>
                                Si selecciona una persona en el filtro de Personal, el analisis utilizara
                                unicamente los resultados estadisticos correspondientes. El nombre y cualquier
                                identificador seran omitidos por seguridad. Despues de descargar el documento
                                editable, debera agregar manualmente el nombre de la persona donde corresponda
                                para mantener la coherencia del reporte.
                            </p>
                        </div>
                    </section>


                    <section class="dashboard-informe__bloque">
                        <div class="dashboard-informe__intro">
                            <h3>
                                Filtros del analisis
                            </h3>
                            <p>
                                Se copian desde el Dashboard al abrir el generador y despues trabajan de forma
                                independiente.
                            </p>
                        </div>

                        <div class="dashboard-informe__grid">
                            <label>
                                Fecha inicial
                                <input type="date" name="fecha_registro_inicio">
                            </label>

                            <label>
                                Fecha final
                                <input type="date" name="fecha_registro_fin">
                            </label>

                            <label>
                                Tipo
                                <select name="tipo" data-informe-tipo>
                                    <option value="">Todos</option>
                                    <option value="QUEJA">Queja</option>
                                    <option value="QUEJA_VERBAL">Queja verbal</option>
                                    <option value="QUEJA_FORANEA">Queja foranea</option>
                                    <option value="FELICITACION">Felicitaciones</option>
                                </select>
                            </label>

                            <label data-informe-quejas-only>
                                Estado
                                <select name="estado">
                                    <option value="">Todos</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="En proceso">En proceso</option>
                                    <option value="Finalizado">Finalizado</option>
                                </select>
                            </label>

                            <label data-informe-quejas-only>
                                Clasificacion
                                <select name="clasificacion">
                                    <option value="">Todas</option>
                                    <option value="INTERNA">INTERNA</option>
                                    <option value="EXTERNA">EXTERNA</option>
                                </select>
                            </label>

                            <label data-informe-quejas-only>
                                Seguimiento
                                <select name="seguimiento">
                                    <option value="">Todos</option>
                                    <option value="con">Con seguimiento</option>
                                    <option value="sin">Sin seguimiento</option>
                                </select>
                            </label>

                            <label data-informe-quejas-only>
                                Queja anonima
                                <select name="es_anonimo">
                                    <option value="">Todas</option>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </label>

                            <label>
                                Zona
                                <select name="zona">
                                    <option value="">Todas</option>
                                    <option value="Zona Norte">Norte</option>
                                    <option value="Zona Poniente">Poniente</option>
                                    <option value="Zona Centro">Centro</option>
                                    <option value="Zona Oriente">Oriente</option>
                                </select>
                            </label>

                            <label>
                                Sector
                                <select name="sector">
                                    <option value="">Todos</option>
                                    <?php for ($numero = 1; $numero <= 15; $numero++): ?>
                                    <?php $sector = 'SECTOR ' . str_pad((string) $numero, 2, '0', STR_PAD_LEFT); ?>
                                    <option value="<?= esc($sector) ?>">
                                        <?= esc($sector) ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </label>

                            <label>
                                Turno
                                <select name="turno">
                                    <option value="">Todos</option>
                                    <option value="Primer turno">Primer turno</option>
                                    <option value="Segundo turno">Segundo turno</option>
                                    <option value="Tercer turno">Tercer turno</option>
                                    <option value="Alfa">Alfa</option>
                                    <option value="Beta">Beta</option>
                                    <option value="Diario">Diario</option>
                                    <option value="No refiere ni fecha ni horario">No refiere ni fecha ni horario</option>
                                </select>
                            </label>

                            <label>
                                Area
                                <input type="text" name="area_personal" placeholder="Area del personal">
                            </label>

                            <label>
                                Personal
                                <input type="text" name="personal" placeholder="Filtro estadistico interno">
                            </label>

                            <label>
                                Unidad
                                <input type="text" name="unidad" placeholder="Unidad o placas">
                            </label>
                        </div>
                    </section>


                    <section class="dashboard-informe__bloque">
                        <div class="dashboard-informe__intro">
                            <h3>
                                Secciones a incluir
                            </h3>
                            <p>
                                Las opciones incompatibles con Felicitaciones se desactivan automaticamente.
                            </p>
                        </div>

                        <div class="dashboard-informe__secciones">
                            <label>
                                <input type="checkbox" name="secciones[]" value="indicadores" checked>
                                Indicadores
                            </label>

                            <label data-informe-seccion-quejas-only>
                                <input type="checkbox" name="secciones[]" value="catalogo" checked>
                                Catalogo / clasificacion
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="evolucion" checked>
                                Evolucion temporal
                            </label>

                            <label data-informe-seccion-quejas-only>
                                <input type="checkbox" name="secciones[]" value="estado" checked>
                                Estado de las Quejas
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="zona" checked>
                                Zona
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="turno" checked>
                                Turno
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="sector" checked>
                                Sector
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="area" checked>
                                Area
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="unidad">
                                Unidad
                            </label>

                            <label data-informe-seccion-quejas-only>
                                <input type="checkbox" name="secciones[]" value="sanciones" checked>
                                Sanciones disciplinarias
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="cruce" checked>
                                Analisis cruzado
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="ranking_sector" checked>
                                Ranking Sector
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="ranking_area">
                                Ranking Area
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="ranking_unidad">
                                Ranking Unidad
                            </label>

                            <label>
                                <input type="checkbox" name="secciones[]" value="hallazgos" checked>
                                Hallazgos
                            </label>

                            <label data-informe-seccion-quejas-only>
                                <input type="checkbox" name="secciones[]" value="comparativa">
                                Comparativa temporal
                            </label>
                        </div>
                    </section>


                    <section class="dashboard-informe__bloque">
                        <div class="dashboard-informe__intro">
                            <h3>
                                Configuracion de secciones
                            </h3>
                            <p>
                                Solo aplica a bloques dinamicos como Area/Unidad y Analisis cruzado.
                            </p>
                        </div>

                        <div class="dashboard-informe__grid">
                            <label>
                                Dimension principal
                                <select name="dimension">
                                    <option value="area">Area</option>
                                    <option value="unidad">Unidad</option>
                                </select>
                            </label>

                            <label>
                                Analizar por
                                <select name="cruce_principal" data-informe-cruce-principal>
                                    <option value="sector">Sector</option>
                                    <option value="zona">Zona</option>
                                    <option value="area">Area</option>
                                    <option value="turno">Turno</option>
                                </select>
                            </label>

                            <label>
                                Comparar con
                                <select name="cruce_secundaria" data-informe-cruce-secundaria>
                                    <option value="turno">Turno</option>
                                    <option value="estado">Estado</option>
                                </select>
                            </label>
                        </div>
                    </section>


                    <section class="dashboard-informe__bloque dashboard-informe__bloque--accion">
                        <div class="dashboard-informe__intro">
                            <h3>
                                Accion final futura
                            </h3>
                            <p>
                                El resultado final de este flujo sera descargar un documento editable de Word.
                                En esta etapa solo se prepara el analisis para validar filtros, secciones y
                                compatibilidad. Si el filtro Personal esta activo, el documento debera incluir
                                en negritas la nota: [Agregar manualmente el nombre de la persona a la que
                                corresponde este analisis.]
                            </p>
                        </div>
                    </section>


                    <section class="dashboard-informe__bloque dashboard-informe__bloque--preview"
                        id="dashboard-informe-preview" hidden>
                    </section>

                    <div class="dashboard-exportar__mensaje" id="dashboard-informe-mensaje" hidden></div>

                </div>

            </div>

            <div class="modal-reporte__footer">
                <button type="button" class="modal-reporte__button modal-reporte__button--secondary"
                    data-cerrar-modal-informe>
                    Cerrar
                </button>

                <button type="submit" class="modal-reporte__button modal-reporte__button--primary">
                    Preparar analisis
                </button>
            </div>

        </form>
    </div>
</div>
