@extends('mgcp.layouts.app')

@section('cabecera') Reporte de Ventas por Empresa - Módulo Gerencial @endsection

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
                <label class="form-control-static col-md-3 text-right">
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
                                        <th>Empresa</th>
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
                                        <th>Empresa</th>
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
        });

        function search() {
            var periodo = $("[name=periodo] option:selected").text();
            var idperiodo = $("[name=periodo]").val();
            const buscador = new Indicador('{{csrf_token()}}');
            buscador.searchCompany(periodo, idperiodo, 1);
        }
    </script>
@endsection