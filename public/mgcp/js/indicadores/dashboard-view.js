class DashboardView {
    constructor(model) {
        this.model = model;
        this.chartMontosAdjudicados = null;
    }

    mostrarIndicadorCdp() {
        $('#divIndicadoresCdp').LoadingOverlay("show", {
            imageAutoResize: true,
            imageColor: "#3c8dbc",
        });
        this.model.obtenerIndicadoresCdpPorPeriodo($('#selectCdpAnio').val()).then((data) => {
            if (data.tipo == 'success') {
                $('#divCdpPendienteAprobar').find('h3').html(data.pendienteAprobar);
                $('#divCdpPendienteRegularizar').find('h3').html(data.pendientesRegularizar);
                $('#divCdpOcSinCuadro').find('h3').html(data.ocSinCuadro);
                $('#divCdpDespues24h').find('h3').html(data.cdpSolAprob24);
            }
            $('#divIndicadoresCdp').LoadingOverlay("hide", true);
        });
    }

    actualizarIndicadorCdpEvent() {
        $('#selectCdpAnio').on('change', () => {
            this.mostrarIndicadorCdp();
        })
    }

    mostrarGraficoMontosAdjudicadosPorAnio() {
        $('#divMontosAdjudicadosOrdenesPorAnio').LoadingOverlay("show", {
            imageAutoResize: true,
            imageColor: "#3c8dbc",
        });
        this.model.obtenerMontosAdjudicadosOcPorAnio($('#selectMontosAdjudicadosOcPorAnio').val()).then((respuesta) => {
            if (respuesta.tipo == 'success') {
                if (this.chartMontosAdjudicados != null) {
                    this.chartMontosAdjudicados.destroy();
                }
                this.chartMontosAdjudicados = new Chart(
                    document.getElementById('canvaMontosAdjudicadosOrdenesPorAnio'),
                    {
                        type: 'line',
                        //plugins: [ChartDataLabels],
                        data: {
                            labels: respuesta.meses,
                            datasets: [{
                                label: 'Montos adjudicados',
                                backgroundColor: 'rgb(52, 143, 235)',
                                borderColor: 'rgb(52, 143, 235)',
                                data: respuesta.montos,
                                datalabels: {
                                    align: 'end',
                                    anchor: 'start'
                                },
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return 'S/ ' + $.number(context.parsed.y, 2, '.', ',');
                                        }
                                    }
                                },
                                datalabels: {
                                    formatter: function (value, context) {
                                        return 'S/ ' + $.number(value, 2, '.', ',');
                                    },
                                    borderColor: function (context) {
                                        return context.dataset.borderColor;
                                    },
                                    borderWidth: 2,
                                    borderRadius: 4,
                                    padding: 6
                                }
                            }
                        }
                    }
                );
            }
            $('#divMontosAdjudicadosOrdenesPorAnio').LoadingOverlay("hide", true);
        });
    }

    actualizarGraficoMontosAdjudicadosPorAnio() {
        $('#selectMontosAdjudicadosOcPorAnio').on('change', () => {
            this.mostrarGraficoMontosAdjudicadosPorAnio();
        })
    }

    mostrarGraficoMontosFacturadosTercerosPorAnio() {
        $('#divMontosFacturadosTercerosPorAnio').LoadingOverlay("show", {
            imageAutoResize: true,
            imageColor: "#3c8dbc",
        });
        this.model.obtenerMontosFacturadosTercerosPorAnio($('#selectMontosFacturadosTercerosPorAnio').val()).then((respuesta) => {
            if (respuesta.tipo == 'success') {

                new Chart(
                    document.getElementById('canvaMontosFacturadosTercerosPorAnio'),
                    {
                        type: 'line',
                        //plugins: [ChartDataLabels],
                        data: {
                            labels: respuesta.meses,
                            datasets: [
                                {
                                    label: 'Meta por mes',
                                    backgroundColor: 'rgb(219, 31, 2)',
                                    borderColor: 'rgb(219, 31, 2)',
                                    data: respuesta.montosMeta,
                                    datalabels: {
                                        align: 'end',
                                        anchor: 'start'
                                    },
                                    display: 'auto'
                                }, {
                                    label: 'Monto facturado',
                                    backgroundColor: 'rgb(39, 143, 31)',
                                    borderColor: 'rgb(39, 143, 31)',
                                    data: respuesta.montosFacturados,
                                    datalabels: {
                                        align: 'end',
                                        anchor: 'start'
                                    },
                                    display: 'auto'
                                }
                            ]
                        },
                        options: {
                            /*scales: {
                                y: {
                                    grid: {
                                        display: false
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },*/
                            maintainAspectRatio: false,

                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return 'S/ ' + $.number(context.parsed.y, 2, '.', ',');
                                        }
                                    }
                                },
                                datalabels: {
                                    formatter: function (value, context) {
                                        return 'S/ ' + $.number(value, 2, '.', ',');
                                    },
                                    borderColor: function (context) {
                                        return context.dataset.borderColor;
                                    },
                                    borderWidth: 2,
                                    borderRadius: 4,
                                    padding: 6
                                }
                            }
                        }
                    }
                );













            }
            $('#divMontosFacturadosTercerosPorAnio').LoadingOverlay("hide", true);
        });
    }

}