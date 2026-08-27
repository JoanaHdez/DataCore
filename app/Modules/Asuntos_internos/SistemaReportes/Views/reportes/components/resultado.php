<div
    class="modal-resultado"
    id="modal-resultado"
    aria-hidden="true"
>

    <div
        class="modal-resultado__overlay"
        data-cerrar-resultado
    ></div>


    <div
        class="modal-resultado__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-resultado-titulo"
    >

        <button
            type="button"
            class="modal-resultado__cerrar"
            data-cerrar-resultado
            aria-label="Cerrar"
        >
            ×
        </button>


        <div class="modal-resultado__icono" id="modal-resultado-icono">

            <svg
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    id="modal-resultado-icono-path"
                    d="M20 6L9 17l-5-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.3"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>

        </div>


        <div class="modal-resultado__contenido">

            <span class="modal-resultado__eyebrow">
                Sistema de Reportes
            </span>

            <h2
                class="modal-resultado__titulo"
                id="modal-resultado-titulo"
            >
                Operación realizada
            </h2>

            <p
                class="modal-resultado__mensaje"
                id="modal-resultado-mensaje"
            >
                La operación se realizó correctamente.
            </p>

        </div>


        <div class="modal-resultado__acciones">

            <button
                type="button"
                class="modal-resultado__boton"
                data-cerrar-resultado
            >
                Aceptar
            </button>

        </div>

    </div>

</div>