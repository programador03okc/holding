@extends('mgcp.layouts.app')

@section('cabecera') Dashboard de Ventas - Contabilidad @endsection

@section('estilos')
    <style>
    </style>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title" id="title-anual">Metas Anuales 2022</h3>
        <div class="pull-right box-tools">
            <select name="periodo" class="form-control form-control-sm" onchange="cambiarPeriodo(this);">
                @foreach ($periodo as $item)
                    <option value="{{ $item->id_periodo }}">{{ $item->descripcion }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3 id="meta_total">S/ 0.00</h3>
                                <p>Meta Total</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3 id="venta_total">S/ 0.00</h3>
                                <p>Total de Ventas Actual</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3 id="diff_total">S/ 0.00</h3>
                                <p>Diferencia Anual</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                            <a href="javascript: void(0);" onclick="abrirModalAnual();" class="small-box-footer">Ver <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3 id="ptc_anual">S/ 0.00</h3>
                                <p>Porcentaje de Avance Anual</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                              <h3 class="box-title">Gráfico de Avance Anual</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="chartAnual" style="height:350px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title" id="title-mensual">Metas Mensual - Noviembre</h3>
        <div class="pull-right box-tools">
            <select name="month" class="form-control form-control-sm" onchange="cambiarMes(this.value);">
                <option value="1" {{ (date('n') == 1) ? 'selected' : '' }}>Enero</option>
                <option value="2" {{ (date('n') == 2) ? 'selected' : '' }}>Febrero</option>
                <option value="3" {{ (date('n') == 3) ? 'selected' : '' }}>Marzo</option>
                <option value="4" {{ (date('n') == 4) ? 'selected' : '' }}>Abril</option>
                <option value="5" {{ (date('n') == 5) ? 'selected' : '' }}>Mayo</option>
                <option value="6" {{ (date('n') == 6) ? 'selected' : '' }}>Junio</option>
                <option value="7" {{ (date('n') == 7) ? 'selected' : '' }}>Julio</option>
                <option value="8" {{ (date('n') == 8) ? 'selected' : '' }}>Agosto</option>
                <option value="9" {{ (date('n') == 9) ? 'selected' : '' }}>Setiembre</option>
                <option value="10" {{ (date('n') == 10) ? 'selected' : '' }}>Octubre</option>
                <option value="11" {{ (date('n') == 11) ? 'selected' : '' }}>Noviembre</option>
                <option value="12" {{ (date('n') == 12) ? 'selected' : '' }}>Diciembre</option>
            </select>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3 id="meta_mes">S/ 0.00</h3>
                                <p>Meta Mensual</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3 id="venta_mes">S/ 0.00</h3>
                                <p>Total de Ventas Mes Actual</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3 id="diff_mes">S/ 0.00</h3>
                                <p>Diferencia Mensual</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                            <a href="javascript: void(0);" onclick="abrirModalMensual();" class="small-box-footer">Ver <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3 id="ptc_mensual">S/ 0.00</h3>
                                <p>Porcentaje de Avance Mensaul</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                              <h3 class="box-title">Gráfico de Avance Mensual</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="chartMensual" style="height:350px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-anual" tabindex="-1" role="dialog" aria-labelledby="modal-anual">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Metas Anuales 2021</h4>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Meta</th>
                                    <th>Ventas (Avance)</th>
                                    <th>Diferencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Enero</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Febrero</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Marzo</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Abril</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Mayo</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Junio</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Julio</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Agosto</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Setiembre</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Octubre</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Noviembre</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                                <tr>
                                    <td>Diciembre</td>
                                    <td class="text-right">10,000.00</td>
                                    <td class="text-right">8,000.00</td>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th class="text-right">120,000.00</th>
                                    <th class="text-right">96,000.00</th>
                                    <th class="text-right">24,000.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-mensual" tabindex="-1" role="dialog" aria-labelledby="modal-mensual">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Meta Mensual Noviembre</h4>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <th>Meta de Noviembre</th>
                                    <td class="text-right">10,000.00</td>
                                </tr>
                                <tr>
                                    <th>Ventas de Noviembre</th>
                                    <td class="text-right">8,000.00</td>
                                </tr>
                                <tr>
                                    <th>Diferencia</th>
                                    <td class="text-right">2,000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src='{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}'></script>
    <script src='{{ asset("assets/chartjs/Chart.bundle.min.js") }}'></script>
    <script src='{{ asset("mgcp/js/util.js?v=27") }}'></script>    
    <script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
    <script src='{{ asset("mgcp/js/indicadores/indicador.js?v=7") }}'></script>    
    <script src='{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}'></script>
    <script>
        const buscador = new Indicador('{{csrf_token()}}');
        $(document).ready(function() {
            $('.sidebar-mini').addClass('sidebar-collapse');
            Util.seleccionarMenu(window.location);

            buscador.dashboard(0, 0, 0, 'todo');
        });

        function abrirModalAnual() {
            $("#modal-anual").modal('show');
        }

        function abrirModalMensual() {
            $("#modal-mensual").modal('show');
        }

        function cambiarPeriodo(element) {
            var periodo = $(element).find("option:selected").text();
            var id_periodo = $(element).val();
            var mes = $("[name=month]").val();
            buscador.dashboard(periodo, id_periodo, mes, 'todo');
        }

        function cambiarMes(value) {
            var periodo = $("[name=periodo] option:selected").text();
            var id_periodo = $("[name=periodo]").val();
            buscador.dashboard(periodo, id_periodo, value, 'mensual');
        }
    </script>
@endsection