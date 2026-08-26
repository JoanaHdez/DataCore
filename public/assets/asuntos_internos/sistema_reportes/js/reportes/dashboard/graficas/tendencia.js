document.addEventListener('DOMContentLoaded', () => {
    inicializarGraficaTendencia();
});


function inicializarGraficaTendencia() {

    const canvas =
        document.querySelector(
            '#grafica-tendencia'
        );

    if (!canvas || typeof Chart === 'undefined') {
        return;
    }


    /*
     * Datos temporales.
     *
     * Después serán sustituidos por los datos
     * obtenidos desde la base de datos.
     */
    const datosTendencia = {

        labels: [
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
        ],

        valores: [
            14,
            19,
            17,
            26,
            22,
            30,
        ],
    };


    new Chart(
        canvas,
        {
            type: 'line',

            data: {

                labels:
                    datosTendencia.labels,

                datasets: [
                    {
                        label:
                            'Reportes registrados',

                        data:
                            datosTendencia.valores,

                        borderWidth: 2,

                        tension: 0.35,

                        fill: false,

                        pointRadius: 4,

                        pointHoverRadius: 6,

                        pointBorderWidth: 2,
                    },
                ],
            },


            options: {

                responsive: true,

                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index',
                },


                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {
                            precision: 0,

                            font: {
                                size: 10,
                            },
                        },

                        grid: {
                            color:
                                'rgba(120, 140, 160, 0.12)',
                        },
                    },


                    x: {

                        ticks: {
                            font: {
                                size: 10,
                            },
                        },

                        grid: {
                            display: false,
                        },
                    },
                },


                plugins: {

                    legend: {
                        display: false,
                    },


                    tooltip: {

                        callbacks: {

                            label(context) {

                                const valor =
                                    context.raw ?? 0;

                                return (
                                    `Reportes: ${valor}`
                                );
                            },
                        },
                    },
                },
            },
        }
    );
}