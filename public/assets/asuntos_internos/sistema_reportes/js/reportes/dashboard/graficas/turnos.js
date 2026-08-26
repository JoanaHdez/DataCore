document.addEventListener('DOMContentLoaded', () => {
    inicializarGraficaTurnos();
});


function inicializarGraficaTurnos() {

    const canvas =
        document.querySelector(
            '#grafica-turnos'
        );

    if (!canvas || typeof Chart === 'undefined') {
        return;
    }


    const datosTurnos = {
        labels: [
            'Primer turno',
            'Segundo turno',
            'Tercer turno',
        ],

        valores: [
            52,
            43,
            33,
        ],
    };


    new Chart(
        canvas,
        {
            type: 'bar',

            data: {
                labels:
                    datosTurnos.labels,

                datasets: [
                    {
                        label: 'Reportes',

                        data:
                            datosTurnos.valores,

                        borderWidth: 1,

                        borderRadius: 6,

                        maxBarThickness: 55,
                    },
                ],
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

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

                                return `Reportes: ${valor}`;
                            },
                        },
                    },
                },
            },
        }
    );
}