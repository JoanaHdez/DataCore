document.addEventListener('DOMContentLoaded', () => {
    inicializarGraficaAreas();
});


function inicializarGraficaAreas() {

    const canvas =
        document.querySelector(
            '#grafica-areas'
        );

    if (!canvas || typeof Chart === 'undefined') {
        return;
    }


    const datosAreas = {
        labels: [
            'Seguridad Ciudadana',
            'Tránsito',
            'Operaciones',
            'Prevención',
            'Administrativa',
        ],

        valores: [
            46,
            31,
            24,
            17,
            10,
        ],
    };


    new Chart(
        canvas,
        {
            type: 'bar',

            data: {
                labels:
                    datosAreas.labels,

                datasets: [
                    {
                        label: 'Reportes',

                        data:
                            datosAreas.valores,

                        borderWidth: 1,

                        borderRadius: 5,

                        barThickness: 22,
                    },
                ],
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

                indexAxis: 'y',

                scales: {

                    x: {
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

                    y: {
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