<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Investigación
        </span>

        <h3>
            Clasificación y seguimiento
        </h3>

    </div>


    <div class="editar-reporte-grid">

        <!-- CLASIFICACIÓN -->
        <div class="editar-reporte-campo">

            <label for="editar-clasificacion">
                Clasificación
                <span class="required">*</span>
            </label>

            <input type="text" id="editar-clasificacion" name="clasificacion" autocomplete="off" required>

        </div>


        <!-- INSPECTOR -->
        <div class="editar-reporte-campo">

            <label for="editar-inspector">
                Inspector
                <span class="required">*</span>
            </label>

            <input type="text" id="editar-inspector" name="inspector" autocomplete="off" required>

        </div>


        <!-- INVESTIGADOR -->
        <div class="editar-reporte-campo">

            <label for="editar-investigador">
                Investigador
            </label>

            <input type="text" id="editar-investigador" name="investigador" autocomplete="off">

        </div>


        <!-- SANCIÓN DISCIPLINARIA -->
        <div class="editar-reporte-campo">

            <label for="editar-sancion-disciplinaria">
                Sanción disciplinaria
            </label>

            <select id="editar-sancion-disciplinaria" name="sancion_disciplinaria">

                <option value="">
                    Sin sanción
                </option>

                <option value="Arresto">
                    Arresto
                </option>

                <option value="Amonestación">
                    Amonestación
                </option>

                <option value="Otro">
                    Otro
                </option>

            </select>

            <!--
                Valores originales.

                Los usaremos desde JS para detectar si el usuario
                realmente modificó la sanción vigente.
            -->
            <input type="hidden" id="editar-sancion-original" value="">

            <input type="hidden" id="editar-sancion-otro-original" value="">

            <!--
                Se mostrará únicamente cuando la última sanción
                vigente provenga de Seguimiento.
            -->
            <small id="editar-sancion-origen" class="editar-reporte-campo__aviso" hidden>
                Actualizada desde seguimiento
            </small>

        </div>


        <!-- OTRA SANCIÓN -->
        <div class="editar-reporte-campo" id="editar-campo-sancion-otro" hidden style="display: none;">

            <label for="editar-sancion-otro">
                Especifique la sanción
                <span class="required">*</span>
            </label>

            <input type="text" id="editar-sancion-otro" name="sancion_otro"
                placeholder="Ingresa la sanción correspondiente" autocomplete="off" maxlength="255" disabled>

        </div>


        <!-- QUIÉN EMITE RESOLUCIÓN -->
        <div class="editar-reporte-campo">

            <label for="editar-quien-emite-resolucion">
                Quién emite resolución
            </label>

            <input type="text" id="editar-quien-emite-resolucion" name="quien_emite_resolucion" autocomplete="off">

        </div>


        <!-- RESOLUCIÓN -->
        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-resolucion">
                Resolución
            </label>

            <textarea id="editar-resolucion" name="resolucion" rows="4"></textarea>

        </div>


        <!-- MOTIVOS -->
        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-motivos">
                Motivos
            </label>

            <textarea id="editar-motivos" name="motivos" rows="4"></textarea>

        </div>

    </div>

</div>