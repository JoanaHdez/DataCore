/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Seguimiento - Normalizadores
========================================================= */

import {
    normalizarSancion,
} from './sanciones.js';


/* =========================================================
   NORMALIZAR HISTORIAL
========================================================= */

export function normalizarSeguimientos(
    seguimientos
) {

    if (
        !Array.isArray(
            seguimientos
        )
    ) {
        return [];
    }


    return seguimientos.map(
        (seguimiento) => ({

            id_seguimiento:
                Number(
                    seguimiento.id_seguimiento
                    || 0
                ),

            fecha:
                String(
                    seguimiento.fecha
                    || ''
                ).trim(),

            tipo:
                String(
                    seguimiento.tipo
                    || ''
                ).trim(),

            estado:
                String(
                    seguimiento.estado_resultante
                    || seguimiento.estado
                    || ''
                ).trim(),

            observaciones:
                String(
                    seguimiento.observaciones
                    || ''
                ).trim(),

            created_by:
                Number(
                    seguimiento.created_by
                    || 0
                ),

            created_at:
                String(
                    seguimiento.created_at
                    || ''
                ).trim(),

            sancion:
                normalizarSancion(
                    seguimiento.sancion
                ),

        })
    );

}