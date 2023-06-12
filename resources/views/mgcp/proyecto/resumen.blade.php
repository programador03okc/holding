@extends('mgcp.layouts.app')
@section('estilos')
@endsection
<style>
    /*.panel .panel-body {
        padding: 4px !important;
    }
    #main-wrapper {
        margin: 5px !important;
    }*/
</style>

@section('cabecera')
Resumen de proyectos
@endsection

@section('cuerpo')

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Proyectos</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">

                <div class="col-md-6">
                    <div class="table-responsive">
                        <table id="table_resumen_proyectos" class="table table-condensed" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Proyectos</th>
                                    <th class="text-center">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-center">Generando resumen...</td>
                                </tr>
                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 col-md-offset-1">
                    <canvas id="chart_resumen_proyectos" width="300" height="300"></canvas>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">Proyectos</h3>
    </div>
    <div class="box-body">
        <div class="col-md-12 text-center">
            <canvas id="chart_proyectos_fases"  width="1000"  height="500"></canvas>
        </div>
    </div>
</div>



@endsection

@section('scripts')

<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script src='{{ asset("assets/chartjs/Chart.min.js") }}'></script>
<script src='{{ asset("assets/chartjs/Chart.bundle.min.js") }}'></script>

<script>
    $(document).ready(function () {
Util.seleccionarMenu(window.location);
//Chart de resumen de proyectos
        $.ajax({
            url: "{{ route('mgcp.proyectos.resumen-data') }}",
            type: 'post',
            dataType: 'json',
            data: {_token: '{{ csrf_token() }}'},
            success: function (datos) {
                var contenido;
                var sumaMontos = 0;
                var cantProyectos = 0;
                var etiquetas = [];
                var cantidades = [];
                var proyectos = [];
                var fases_descripcion = [];
                var proyectos_faseactual = [];
                var color_fondo = [];
                var color_borde = [];
                for (var indice in datos.resumen_proyectos) {
                    etiquetas.push(datos.resumen_proyectos[indice].estado);
                    cantidades.push(datos.resumen_proyectos[indice].cantidad);
                    contenido += '<tr><td>' + datos.resumen_proyectos[indice].estado + '</td><td class="text-center">' + datos.resumen_proyectos[indice].cantidad + '</td><td class="text-right">S/ ' + Util.formatoNumero(datos.resumen_proyectos[indice].suma_monto, 2, '.', ',') + '</td></tr>';
                    sumaMontos += parseFloat(datos.resumen_proyectos[indice].suma_monto);
                    cantProyectos += parseInt(datos.resumen_proyectos[indice].cantidad);
                }
                for (var indice in datos.proyectos) {
                    proyectos.push(datos.proyectos[indice].codigo_proyecto + ' - ' + datos.proyectos[indice].nombre);
                    proyectos_faseactual.push(datos.proyectos[indice].descripcion_fase.id);
                    //alert("id fase "+datos.proyectos[indice].fase_actual.id_fase);
                    fases_descripcion.push(datos.proyectos[indice].descripcion_fase.descripcion + ' (' + datos.proyectos[indice].descripcion_fase.porcentaje + '%)');
                    switch (datos.proyectos[indice].id_estado)
                    {
                        case 1:
                            color_fondo.push('rgba(31, 203, 74, 0.5)');
                            color_borde.push('rgba(31, 203, 74, 1)');
                            break;
                        case 2:
                            color_fondo.push('rgba(255, 72, 72, 0.5)');
                            color_borde.push('rgba(255, 72, 72, 1)');
                            break;
                        case 3:
                            color_fondo.push('rgba(255, 255, 0, 0.5)');
                            color_borde.push('rgba(255, 255, 0, 1)');
                            break;
                    }
                }
                $('#table_resumen_proyectos').find('tbody').html(contenido);
                var pie = '<tr><td class="text-left"><strong>Total</strong></td>';
                pie += '<td class="text-center"><strong>' + cantProyectos + '</strong></td>';
                pie += '<td class="text-right"><strong>S/ ' + Util.formatoNumero(sumaMontos, 2, '.', ',') + '</strong></td>';
                $('#table_resumen_proyectos').find('tfoot').html(pie);
                var dataPie = {
                    labels: etiquetas,
                    datasets: [
                        {
                            data: cantidades,
                            backgroundColor: [
                                "#1FCB4A",
                                "#FF4848",
                                "#FFFF00"
                            ],
                            hoverBackgroundColor: [
                                "#1FCB4A",
                                "#FF4848",
                                "#FFFF00"
                            ]
                        }]
                };
                var ctx = $("#chart_resumen_proyectos");
                var myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: dataPie,
                    options: {
                        responsive: true
                    }
                });
                var dataBarras = {
                    labels: proyectos,
                    datasets: [
                        {
                            label: "Progreso",
                            backgroundColor: color_fondo,
                            borderColor: color_borde,
                            borderWidth: 1,
                            data: proyectos_faseactual
                        }
                    ],
                    fases: fases_descripcion
                };
                var barras = $("#chart_proyectos_fases");
                var myBarChart = new Chart(barras, {
                    type: 'horizontalBar',
                    data: dataBarras,
                    options: {
                        legend: {
                            display: false,
                            labels: {
                                fontColor: 'rgb(255, 99, 132)'
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    var allData = data.datasets[tooltipItem.datasetIndex].data;
                                    var tooltipData = allData[tooltipItem.index];
                                    return data.labels[tooltipItem.index].substring(14, 100) + '... : Fase ' + tooltipData + ' - ' + data.fases[tooltipItem.index];
                                }
                            }
                        },
                        responsive: true,
                        scales: {
                            yAxes: [{
                                    id: 'y-axis-0',
                                    gridLines: {
                                        display: true,
                                        lineWidth: 1,
                                        color: "rgba(0,0,0,0.30)"
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        mirror: false,
                                        suggestedMin: 0,
                                        callback: function (value, index, values) {
                                            return value.substring(0, 11);
                                            ;
                                        }
                                    }
                                }],
                            xAxes: [{
                                    id: 'x-axis-0',
                                    gridLines: {
                                        display: true
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        max: 15
                                    }
                                }]
                        }
                    }
                });
            }
        });
    });
</script>
@endsection