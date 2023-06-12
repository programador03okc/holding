@extends('mgcp.layouts.app')


@section('cabecera')
Resumen de oportunidades
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Oportunidades</li>
    <li class="active">Resumen</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Oportunidades</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <?php
            $total_opor = 0;
            $total_soles = 0;
            $total_dolares = 0;
            ?>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table id="table_resumen_oportunidades" class="table table-condensed" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Oportunidades</th>
                                <th class="text-center">Monto en soles</th>
                                <th class="text-center">Monto en dólares</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center">Generando resumen...</td>
                            </tr>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-md-4 col-md-offset-1">
                <canvas id="chart_resumen_oportunidades" width="300" height="300"></canvas>
            </div>
        </div>
    </div>
</div>


<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title">Corporativos</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="table-responsive">
                    <table id="table_resumen_vendedores" class="table table-condensed" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Corporativo</th>
                                <th class="text-center">Registro</th>
                                <th class="text-center">Cotizado</th>
                                <th class="text-center">Negociación</th>
                                <th class="text-center">Ganado</th>
                                <th class="text-center">Perdido</th>
                                <th class="text-center">Desestimado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cont = 0;
                            ?>
                            @foreach ($responsables as $responsable)
                            <?php
                            $codEstado = 1;
                            ?>
                            <tr>
                                <td>{{$responsable->name}}</td>
                                @foreach ($detalles[$cont] as $detalle)
                                <td class="text-right"><span style="cursor: pointer; color: #337ab7" class="detallesMonto" data-moneda="s" data-corporativo="{{$responsable->id}}" data-estado="{{$codEstado}}">S/ {{number_format($detalle->suma_soles,2,'.',',')}}</span><br><span style="cursor: pointer; color: #337ab7" class="detallesMonto" data-moneda="d" data-corporativo="{{$responsable->id}}" data-estado="{{$codEstado}}">$ {{number_format($detalle->suma_dolares,2,'.',',')}}</span>
                                </td>
                                <?php
                                $codEstado++;
                                ?>
                                @endforeach
                            </tr>
                            <?php
                            $cont++;
                            ?>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Total:</strong></td>
                                @foreach ($sumas as $suma)
                                <td class="text-right"><strong>S/ {{number_format($suma->suma_soles,2,'.',',')}}<br>$ {{number_format($suma->suma_dolares,2,'.',',')}}</strong></td>
                                @endforeach
                            <tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">Montos por corporativo (en soles)</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 text-center">
                <canvas id="chart_soles"  width="1000"  height="500"></canvas>
            </div>  
        </div>
    </div>
</div>

<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">Montos por corporativo (en dólares)</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 text-center">
                <canvas id="chart_dolares"  width="1000"  height="500"></canvas>
            </div> 
        </div>
    </div>
</div>


<div class="modal fade" id="modalDetallesMonto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detalles de monto - Oportunidades <span id="spanOportunidadEstado"></span></h4>
            </div>
            <div class="modal-body">

                <p><strong>Corporativo: </strong><span id="spanMontoCorporativo"></span></p>
                <table class="table table-condensed table-bordered" id="tableDetallesMonto">
                    <thead>
                        <tr>
                            <th width="12%" class="text-center">Código</th>
                            <th class="text-center">Descripción</th>
                            <th width="12%" class="text-center">Fecha creación</th>
                            <th width="12%" class="text-center">Fecha límite</th>
                            <th width="15%" class="text-center">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-right" colspan="4"><strong>Total:</strong></td>
                            <td class="text-right" id="tdSumaMonto"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script src='{{ asset("assets/chartjs/Chart.bundle.min.js") }}'></script>
<script>
    $(document).ready(function () {
        Util.seleccionarMenu(window.location);
        var url = "{{url('/mgcp')}}";
        $('span.detallesMonto').click(function () {
            $('#modalDetallesMonto').modal('show');
            var $span = $(this);
            var $tbody = $('#tableDetallesMonto').find('tbody');
            $tbody.html('<tr><td colspan="5" class="text-center">Obteniendo datos...</td></tr>');
            $.ajax({
                url: "{{ route('mgcp.oportunidades.resumen-detalles-monto') }}",
                type: 'post',
                dataType: 'json',
                data: {codEstado: $span.data('estado'), corporativo: $span.data('corporativo'), moneda: $span.data('moneda'), _token: '{{ csrf_token() }}'},
                success: function (datos) {
                    var cadena = '';
                    for (var indice in datos) {
                        cadena += '<tr>';
                        cadena += '<td class="text-center"><a target="_blank" href="' + url + '/oportunidades/detalles/' + datos[indice].id + '">' + datos[indice].codigo_oportunidad + '</a></td>';
                        cadena += '<td class="text-justify">' + datos[indice].oportunidad + '</td>';
                        cadena += '<td class="text-center">' + datos[indice].created_at + '</td>';
                        cadena += '<td class="text-center">' + datos[indice].fecha_limite + '</td>';
                        cadena += '<td class="text-right">' + datos[indice].monto + '</td>';
                        cadena += '</tr>';
                    }
                    $tbody.html(cadena);
                    var descripcion = '';
                    switch ($span.data('estado'))
                    {
                        case 1:
                            descripcion = 'en registro';
                            break;
                        case 2:
                            descripcion = 'cotizadas';
                            break;
                        case 3:
                            descripcion = 'en negociación';
                            break;
                        case 4:
                            descripcion = 'ganadas';
                            break;
                        case 5:
                            descripcion = 'perdidas';
                            break;
                        case 6:
                            descripcion = 'desestimadas';
                            break;
                    }
                    $('#spanOportunidadEstado').html(descripcion);
                    $('#spanMontoCorporativo').html($span.closest('tr').find('td:eq(0)').html());
                    $('#tdSumaMonto').html('<strong>' + $span.html() + '</strong>');
                }
            });
        });

//Chart de resumen de oportunidades
        $.ajax({
            url: "{{ route('mgcp.oportunidades.resumen-data') }}",
            type: 'post',
            dataType: 'json',
            data: {_token: '{{ csrf_token() }}'},
            success: function (datos) {
                var contenido;
                var sumaSoles = 0;
                var sumaDolares = 0;
                var sumaEstados = 0;
                var etiquetas = [];
                var cantidades = [];
                for (var indice in datos.resumen_oportunidades) {
                    etiquetas.push(datos.resumen_oportunidades[indice].estado);
                    cantidades.push(datos.resumen_oportunidades[indice].cantidad);
                    sumaSoles += parseFloat(datos.resumen_oportunidades[indice].suma_soles);
                    sumaDolares += parseFloat(datos.resumen_oportunidades[indice].suma_dolares);
                    sumaEstados += parseInt(datos.resumen_oportunidades[indice].cantidad);
                    contenido += '<tr><td class="text-left"><span data-estado="" data-toggle="modal" data-target="#modal_detalles">' + datos.resumen_oportunidades[indice].estado + '</span></td><td class="text-center">' + datos.resumen_oportunidades[indice].cantidad + '</td>';
                    contenido += '<td class="text-right">S/ ' + Util.formatoNumero(datos.resumen_oportunidades[indice].suma_soles, 2, '.', ',') + '</td><td class="text-right">$ ' + Util.formatoNumero(datos.resumen_oportunidades[indice].suma_dolares, 2, '.', ',') + '</td></tr>';
                }
                $('#table_resumen_oportunidades').find('tbody').html(contenido);
                var pie = '<tr><td class="text-left"><strong>Total</strong></td>';
                pie += '<td class="text-center"><strong>' + sumaEstados + '</strong></td>';
                pie += '<td class="text-right"><strong>S/ ' + Util.formatoNumero(sumaSoles, 2, '.', ',') + '</strong></td>';
                pie += '<td class="text-right"><strong>$ ' + Util.formatoNumero(sumaDolares, 2, '.', ',') + '</strong></td>';
                $('#table_resumen_oportunidades').find('tfoot').html(pie);
                var data = {
                    labels: etiquetas,
                    datasets: [
                        {
                            data: cantidades,
                            backgroundColor: [
                                "#2966B8", "#58D3F7", "#FFFF00", "#1FCB4A", "#FF4848", "#9669FE"
                            ],
                            hoverBackgroundColor: [
                                "#2966B8", "#58D3F7", "#FFFF00", "#1FCB4A", "#FF4848", "#9669FE"
                            ]
                        }]
                };
                var ctx = $("#chart_resumen_oportunidades");
                var myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true
                    }
                });

                //Chart para vendedores
                var corporativos = [];
                var pendientes_soles = [];
                var pendientes_dolares = [];

                var ganados_soles = [];
                var ganados_dolares = [];

                var perdidos_soles = [];
                var perdidos_dolares = [];

                var desestimados_soles = [];
                var desestimados_dolares = [];

                for (var indice in datos.corporativos) {
                    corporativos.push(datos.corporativos[indice].name);
                }

                for (var indice in datos.pendientes) {
                    for (var i in datos.pendientes[indice]) {
                        pendientes_soles.push(datos.pendientes[indice][i].suma_soles);
                        pendientes_dolares.push(datos.pendientes[indice][i].suma_dolares);
                    }
                }

                for (var indice in datos.ganados) {
                    for (var i in datos.ganados[indice]) {
                        ganados_soles.push(datos.ganados[indice][i].suma_soles);
                        ganados_dolares.push(datos.ganados[indice][i].suma_dolares);
                    }
                }

                for (var indice in datos.perdidos) {
                    for (var i in datos.perdidos[indice]) {
                        perdidos_soles.push(datos.perdidos[indice][i].suma_soles);
                        perdidos_dolares.push(datos.perdidos[indice][i].suma_dolares);
                    }
                }

                for (var indice in datos.desestimados) {
                    for (var i in datos.desestimados[indice]) {
                        desestimados_soles.push(datos.desestimados[indice][i].suma_soles);
                        desestimados_dolares.push(datos.desestimados[indice][i].suma_dolares);
                    }
                }

                var canvaSoles = document.getElementById("chart_soles").getContext("2d");
                var canvaDolares = document.getElementById("chart_dolares").getContext("2d");
                var dataSoles = {
                    labels: corporativos,
                    datasets: [
                        {
                            label: "Pendiente",
                            backgroundColor: "#2966B8",
                            data: pendientes_soles
                        },
                        {
                            label: "Ganado",
                            backgroundColor: "#1FCB4A",
                            data: ganados_soles
                        },
                        {
                            label: "Perdido",
                            backgroundColor: "#FF4848",
                            data: perdidos_soles
                        },
                        {
                            label: "Desestimado",
                            backgroundColor: "#9669FE",
                            data: desestimados_soles
                        }
                    ]
                };
                var dataDolares = {
                    labels: corporativos,
                    datasets: [
                        {
                            label: "Pendiente",
                            backgroundColor: "#2966B8",
                            data: pendientes_dolares
                        },
                        {
                            label: "Ganado",
                            backgroundColor: "#1FCB4A",
                            data: ganados_dolares
                        },
                        {
                            label: "Perdido",
                            backgroundColor: "#FF4848",
                            data: perdidos_dolares
                        },
                        {
                            label: "Desestimado",
                            backgroundColor: "#9669FE",
                            data: desestimados_dolares
                        }
                    ]
                };
                var chartSoles = new Chart(canvaSoles, {
                    type: 'bar',
                    data: dataSoles,
                    options: {
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    var allData = data.datasets[tooltipItem.datasetIndex].data;
                                    var tooltipLabel = data.datasets[tooltipItem.datasetIndex].label;
                                    var tooltipData = allData[tooltipItem.index];
                                    return tooltipLabel + ': S/ ' + Util.formatoNumero(tooltipData, 2, '.', ',');
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
                                            return 'S/ ' + Util.formatoNumero(value, 0, '.', ',');
                                        }
                                    }
                                }],
                            xAxes: [{
                                    id: 'x-axis-0',
                                    gridLines: {
                                        display: false
                                    },
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                        }
                    }
                });

                var chartDolares = new Chart(canvaDolares, {
                    type: 'bar',
                    data: dataDolares,
                    options: {
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    var allData = data.datasets[tooltipItem.datasetIndex].data;
                                    var tooltipLabel = data.datasets[tooltipItem.datasetIndex].label;
                                    var tooltipData = allData[tooltipItem.index];
                                    return tooltipLabel + ': $ ' + Util.formatoNumero(tooltipData, 2, '.', ',');
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
                                            return '$ ' + Util.formatoNumero(value, 0, '.', ',');
                                        }
                                    }
                                }],
                            xAxes: [{
                                    id: 'x-axis-0',
                                    gridLines: {
                                        display: false
                                    },
                                    ticks: {
                                        beginAtZero: true
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