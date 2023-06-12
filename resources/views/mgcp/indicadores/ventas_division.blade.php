@extends('mgcp.layouts.app')

@section('cabecera') Reporte de Ventas por División - Módulo Gerencial @endsection

@section('estilos')
    <style>
        table tbody th,
        table tbody td {
            font-size: 12px;
        }
        .box .box-header {
            background-color: #f7f7f7;
        }
    </style>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-6 col-md-offset-3">
                <label class="form-control-static col-md-2">
                    Periodo:
                </label>
                <div class="col-md-3">
                    <select class="form-control" name="periodo">
                        @foreach ($periodo as $itemPeriodo)
                            <option value="{{ $itemPeriodo->id_periodo }}">{{ $itemPeriodo->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary btn-flat" onclick="search();">Obtener</button>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="box" style="border: 1px solid #f4f4f4;">
                    <div class="box-header with-border"><h3 class="box-title">Ventas Internas</h3></div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>División (CC)</th>
                                        <th>Ene</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Abr</th>
                                        <th>May</th>
                                        <th>Jun</th>
                                        <th>Jul</th>
                                        <th>Ago</th>
                                        <th>Set</th>
                                        <th>Oct</th>
                                        <th>Nov</th>
                                        <th>Dic</th>
                                    </tr>
                                </thead>
                                <tbody id="result-int"><tr><td colspan="13">No se encontraron resultados</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="box" style="border: 1px solid #f4f4f4;">
                    <div class="box-header with-border"><h3 class="box-title">Ventas Terceros</h3></div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>División (CC)</th>
                                        <th>Ene</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Abr</th>
                                        <th>May</th>
                                        <th>Jun</th>
                                        <th>Jul</th>
                                        <th>Ago</th>
                                        <th>Set</th>
                                        <th>Oct</th>
                                        <th>Nov</th>
                                        <th>Dic</th>
                                    </tr>
                                </thead>
                                <tbody id="result-ext"><tr><td colspan="13">No se encontraron resultados</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-4 col-md-offset-4">
                <label class="form-control-static col-md-3">
                    División:
                </label>
                <div class="col-md-9">
                    <select class="form-control" name="centro_costo" onchange="changeMeta(this.value);">
                        <option value="" selected disabled>Elija una división</option>
                        @foreach ($cen_cos as $itemCentro)
                            <option value="{{ $itemCentro->id_centro_costo }}">{{ $itemCentro->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="box box-primary box-solid">
                    <div class="box-header with-border"><h3 class="box-title">Cumplimiento de la Meta</h3></div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Ene</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Abr</th>
                                        <th>May</th>
                                        <th>Jun</th>
                                        <th>Jul</th>
                                        <th>Ago</th>
                                        <th>Set</th>
                                        <th>Oct</th>
                                        <th>Nov</th>
                                        <th>Dic</th>
                                    </tr>
                                </thead>
                                <tbody id="result-meta"><tr><td colspan="13">No se encontraron resultados</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src='{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}'></script>
    <script src='{{ asset("mgcp/js/util.js?v=27") }}'></script>    
    <script src='{{ asset("mgcp/js/indicadores/indicador.js?v=7") }}'></script>    
    <script src='{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}'></script>
    <script>
        $(document).ready(function() {
            $('.sidebar-mini').addClass('sidebar-collapse');
            Util.seleccionarMenu(window.location);

            let ini_ene = 0;
        });

        function search() {
            var periodo = $("[name=periodo] option:selected").text();
            var idperiodo = $("[name=periodo]").val();
            const buscador = new Indicador('{{csrf_token()}}');
            buscador.searchDivision(periodo, idperiodo, 1);
        }

        function changeMeta(id) {
            var row_met = '';
            var ext_ene = $("#result-ext #ext-"+ id + " .td-ext-ene").text();
            var ext_feb = $("#result-ext #ext-"+ id + " .td-ext-feb").text();
            var ext_mar = $("#result-ext #ext-"+ id + " .td-ext-mar").text();
            var ext_abr = $("#result-ext #ext-"+ id + " .td-ext-abr").text();
            var ext_may = $("#result-ext #ext-"+ id + " .td-ext-may").text();
            var ext_jun = $("#result-ext #ext-"+ id + " .td-ext-jun").text();
            var ext_jul = $("#result-ext #ext-"+ id + " .td-ext-jul").text();
            var ext_ago = $("#result-ext #ext-"+ id + " .td-ext-ago").text();
            var ext_set = $("#result-ext #ext-"+ id + " .td-ext-set").text();
            var ext_oct = $("#result-ext #ext-"+ id + " .td-ext-oct").text();
            var ext_nov = $("#result-ext #ext-"+ id + " .td-ext-nov").text();
            var ext_dic = $("#result-ext #ext-"+ id + " .td-ext-dic").text();

            var met_ene = 0;
            var met_feb = 0;
            var met_mar = 0;
            var met_abr = 0;
            var met_may = 0;
            var met_jun = 0;
            var met_jul = 0;
            var met_ago = 0;
            var met_set = 0;
            var met_oct = 0;
            var met_nov = 0;
            var met_dic = 0;

            $.ajax({
                type: "POST",
                url : route('mgcp.indicadores.buscar-meta-division'),
                data: {value: id, _token: "{{ csrf_token() }}"},
                dataType: "JSON",
                success: function (response) {
                    console.log(response);
                    
                    var datax = response.data;
                    if (datax.length > 0) {
                        datax.forEach(function(element, index) {
                            met_ene = parseFloat(element.ene);
                            met_feb = parseFloat(element.feb);
                            met_mar = parseFloat(element.mar);
                            met_abr = parseFloat(element.abr);
                            met_may = parseFloat(element.may);
                            met_jun = parseFloat(element.jun);
                            met_jul = parseFloat(element.jul);
                            met_ago = parseFloat(element.ago);
                            met_set = parseFloat(element.set);
                            met_oct = parseFloat(element.oct);
                            met_nov = parseFloat(element.nov);
                            met_dic = parseFloat(element.dic);

                            var txt_ene_met = Util.formatoNumero(met_ene, 2);
                            var txt_feb_met = Util.formatoNumero(met_feb, 2);
                            var txt_mar_met = Util.formatoNumero(met_mar, 2);
                            var txt_abr_met = Util.formatoNumero(met_abr, 2);
                            var txt_may_met = Util.formatoNumero(met_may, 2);
                            var txt_jun_met = Util.formatoNumero(met_jun, 2);
                            var txt_jul_met = Util.formatoNumero(met_jul, 2);
                            var txt_ago_met = Util.formatoNumero(met_ago, 2);
                            var txt_set_met = Util.formatoNumero(met_set, 2);
                            var txt_oct_met = Util.formatoNumero(met_oct, 2);
                            var txt_nov_met = Util.formatoNumero(met_nov, 2);
                            var txt_dic_met = Util.formatoNumero(met_dic, 2);

                            row_met += `
                            <tr>
                                <td class="text-left">Ventas Terceros</td>
                                <td class="text-right td-ext-ene">`+ ext_ene +`</td>
                                <td class="text-right td-ext-feb">`+ ext_feb +`</td>
                                <td class="text-right td-ext-mar">`+ ext_mar +`</td>
                                <td class="text-right td-ext-abr">`+ ext_abr +`</td>
                                <td class="text-right td-ext-may">`+ ext_may +`</td>
                                <td class="text-right td-ext-jun">`+ ext_jun +`</td>
                                <td class="text-right td-ext-jul">`+ ext_jul +`</td>
                                <td class="text-right td-ext-ago">`+ ext_ago +`</td>
                                <td class="text-right td-ext-set">`+ ext_set +`</td>
                                <td class="text-right td-ext-oct">`+ ext_oct +`</td>
                                <td class="text-right td-ext-nov">`+ ext_nov +`</td>
                                <td class="text-right td-ext-dic">`+ ext_dic +`</td>
                            </tr>
                            <tr>
                                <td class="text-left">Meta</td>
                                <td class="text-right td-ext-ene">`+ txt_ene_met +`</td>
                                <td class="text-right td-ext-feb">`+ txt_feb_met +`</td>
                                <td class="text-right td-ext-mar">`+ txt_mar_met +`</td>
                                <td class="text-right td-ext-abr">`+ txt_abr_met +`</td>
                                <td class="text-right td-ext-may">`+ txt_may_met +`</td>
                                <td class="text-right td-ext-jun">`+ txt_jun_met +`</td>
                                <td class="text-right td-ext-jul">`+ txt_jul_met +`</td>
                                <td class="text-right td-ext-ago">`+ txt_ago_met +`</td>
                                <td class="text-right td-ext-set">`+ txt_set_met +`</td>
                                <td class="text-right td-ext-oct">`+ txt_oct_met +`</td>
                                <td class="text-right td-ext-nov">`+ txt_nov_met +`</td>
                                <td class="text-right td-ext-dic">`+ txt_dic_met +`</td>
                            </tr>`;
                        });

                        $("#result-meta").html(row_met);
                    }
                }
            }).fail( function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
            return false;
        }
    </script>
@endsection