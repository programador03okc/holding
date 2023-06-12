@extends('mgcp.layouts.app')

@section('cabecera') Registro de Metas Comerciales @endsection

@section('cuerpo')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Registro de Metas</h3></div>
            <form id="formulario" role="form">
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-2 col-md-offset-5">
                            <div class="form-group">
                                <h6>Periodo</h6>
                                <select class="form-control input-sm" name="periodo">
                                    @foreach ($periodo as $itemPeriodo)
                                        <option value="{{ $itemPeriodo->id_periodo }}">{{ $itemPeriodo->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Enero</h6>
                                <input type="number" class="form-control input-sm text-center" name="ene" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Febrero</h6>
                                <input type="number" class="form-control input-sm text-center" name="feb" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Marzo</h6>
                                <input type="number" class="form-control input-sm text-center" name="mar" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Abril</h6>
                                <input type="number" class="form-control input-sm text-center" name="abr" value="0.00" step="any" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Mayo</h6>
                                <input type="number" class="form-control input-sm text-center" name="may" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Junio</h6>
                                <input type="number" class="form-control input-sm text-center" name="jun" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Julio</h6>
                                <input type="number" class="form-control input-sm text-center" name="jul" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Agosto</h6>
                                <input type="number" class="form-control input-sm text-center" name="ago" value="0.00" step="any" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Setiembre</h6>
                                <input type="number" class="form-control input-sm text-center" name="set" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Octubre</h6>
                                <input type="number" class="form-control input-sm text-center" name="oct" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Noviembre</h6>
                                <input type="number" class="form-control input-sm text-center" name="nov" value="0.00" step="any" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Diciembre</h6>
                                <input type="number" class="form-control input-sm text-center" name="dic" value="0.00" step="any" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success btn-sm btn-block btn-flat">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Historial de Metas</h3></div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Periodo</th>
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
                        <tbody id="result"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src='{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}'></script>
    <script src='{{ asset("mgcp/js/util.js?v=27") }}'></script>    
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);

            loadData();

            $("#formulario").on("submit", function() {
                var data = $(this).serializeArray();
                data.push({_token: "{{ csrf_token() }}"});
                $.ajax({
                    type: "POST",
                    url : route('mgcp.indicadores.guardar-mensual'),
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        if (response.response == 'ok') {
                            Util.notify(response.alert, response.message);
                            loadData();
                        } else {
                            Util.notify(response.alert, response.message);
                        }
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });
        });

        function loadData() {
            var row = '';
            $.ajax({
                type: "POST",
                url : route('mgcp.indicadores.mostrar-metas-mensuales'),
                data: {_token: "{{ csrf_token() }}"},
                dataType: "JSON",
                success: function (response) {
                    var datax = response;

                    if (datax.length > 0) {
                        datax.forEach(function(element, index) {
                            var ene = parseFloat(element.ene);
                            var feb = parseFloat(element.feb);
                            var mar = parseFloat(element.mar);
                            var abr = parseFloat(element.abr);
                            var may = parseFloat(element.may);
                            var jun = parseFloat(element.jun);
                            var jul = parseFloat(element.jul);
                            var ago = parseFloat(element.ago);
                            var set = parseFloat(element.set);
                            var oct = parseFloat(element.oct);
                            var nov = parseFloat(element.nov);
                            var dic = parseFloat(element.dic);

                            var txt_ene = Util.formatoNumero(ene, 2);
                            var txt_feb = Util.formatoNumero(feb, 2);
                            var txt_mar = Util.formatoNumero(mar, 2);
                            var txt_abr = Util.formatoNumero(abr, 2);
                            var txt_may = Util.formatoNumero(may, 2);
                            var txt_jun = Util.formatoNumero(jun, 2);
                            var txt_jul = Util.formatoNumero(jul, 2);
                            var txt_ago = Util.formatoNumero(ago, 2);
                            var txt_set = Util.formatoNumero(set, 2);
                            var txt_oct = Util.formatoNumero(oct, 2);
                            var txt_nov = Util.formatoNumero(nov, 2);
                            var txt_dic = Util.formatoNumero(dic, 2);

                            row += `
                            <tr>
                                <td>`+ element.anio +`</td>
                                <td>`+ txt_ene +`</td>
                                <td>`+ txt_feb +`</td>
                                <td>`+ txt_mar +`</td>
                                <td>`+ txt_abr +`</td>
                                <td>`+ txt_may +`</td>
                                <td>`+ txt_jun +`</td>
                                <td>`+ txt_jul +`</td>
                                <td>`+ txt_ago +`</td>
                                <td>`+ txt_set +`</td>
                                <td>`+ txt_oct +`</td>
                                <td>`+ txt_nov +`</td>
                                <td>`+ txt_dic +`</td>
                            </tr>
                            `;
                        });
                    } else {
                        row += `<tr><td colspan="13">No se encontraron resultados</td></tr>`;
                    }
                    $("#result").html(row)
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