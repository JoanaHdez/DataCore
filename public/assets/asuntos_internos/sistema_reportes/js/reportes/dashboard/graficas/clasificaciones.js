document.addEventListener('DOMContentLoaded', () => {
    inicializarGraficaClasificaciones();
});


function inicializarGraficaClasificaciones() {

    const canvas =
        document.querySelector(
            '#grafica-clasificaciones'
        );

    if (!canvas || typeof Chart === 'undefined') {
        return;
    }


    const datosClasificacion = {
        labels: [
            'Queja',
            'Denuncia',
            'Investigación',
            'Otro',
        ],

        valores: [
            58,
            34,
            22,
            14,
        ],
    };


    new Chart(
        canvas,
        {
            type: 'doughnut',

            data: {
                labels:
                    datosClasificacion.labels,

                datasets: [
                    {
                        data:
                            datosClasificacion.valores,

                        borderWidth: 2,

                        borderColor:
                            '#ffffff',

                        hoverOffset: 8,
                    },
                ],
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

                cutout: '68%',

                plugins: {

                    legend: {
                        position: 'bottom',

                        labels: {
                            usePointStyle: true,

                            pointStyle: 'circle',

                            padding: 18,

                            font: {
                                size: 11,
                            },
                        },
                    },

                    tooltip: {
                        callbacks: {

                            label(context) {

                                const valor =
                                    context.raw ?? 0;

                                const total =
                                    context.dataset.data
                                        .reduce(
                                            (
                                                acumulado,
                                                actual
                                            ) =>
                                                acumulado
                                                + Number(
                                                    actual
                                                ),
                                            0
                                        );

                                const porcentaje =
                                    total > 0
                                        ? (
                                            (
                                                Number(valor)
                                                / total
                                            )
                                            * 100
                                        ).toFixed(1)
                                        : 0;

                                return (
                                    `${context.label}: `
                                    + `${valor} `
                                    + `(${porcentaje}%)`
                                );
                            },
                        },
                    },
                },
            },
        }
    );
}