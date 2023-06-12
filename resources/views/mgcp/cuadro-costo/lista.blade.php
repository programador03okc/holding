@extends('mgcp.layouts.app')
@section('estilos')
<link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('cabecera')
Lista de cuadros de presupuesto
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Cuadros de presupuesto</li>
    <li class="active">Lista</li>
</ol>
@endsection
@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table style="width: 100%; font-size:small" id="tableDatos" class="table table-bordered table-condensed table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Código</th>
                        <th class="text-center">Fecha creación</th>
                        <th style="width: 25%" class="text-center">Oportunidad</th>
                        <th style="width: 8%" class="text-center">Fecha límite</th>
                        <th style="width: 20%" class="text-center">Cliente</th>
                        <th style="width: 10%" class="text-center">Resp.<br>oportunidad</th>
                        <th class="text-center">Tipo cuadro</th>
                        <th style="width: 8%" class="text-center">O/C<br>vinculada</th>
                        <th class="text-center">Tiene<br>transform.</th>
                        <th class="text-center">Monto G.G.</th>
                        <th class="text-center">Monto ganancia</th>
                        <th class="text-center">Margen ganancia</th>
                        <th class="text-center">Estado<br>aprobación</th>
                        <th>Responsable<br>aprobación</th>
                        <th style="width: 7%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFiltros" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Filtros</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="formFiltros">
                    @csrf
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="fom-control-static mensaje-filtros">Seleccione los filtros que desee aplicar y cierre este cuadro para continuar</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label title="Fecha entrega">
                                    <input type="checkbox" name="chkFechaCreacion" @if (session('ccFiltroFechaCreacionDesde')!==null) checked @endif> Fecha de creación
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaCreacionDesde" class="form-control date-picker" value="@if (session('ccFiltroFechaCreacionDesde')!==null){{session('ccFiltroFechaCreacionDesde')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaCreacionHasta" class="form-control date-picker" value="@if (session('ccFiltroFechaCreacionHasta')!==null){{session('ccFiltroFechaCreacionHasta')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstado" {{session('ccFiltroEstado') != null ? "checked" : ""}}> Estado de cuadro
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-control" name="selectEstado">
                                @foreach ($estados as $estado)
                                <option value="{{$estado->id}}" {{session('ccFiltroEstado') == $estado->id ? "selected" : ""}}>{{$estado->estado}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkTipo" {{session('ccFiltroTipo') != null ? "checked" : ""}}> Tipo de cuadro
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-control" name="selectTipo">
                                <option value="directa" {{session('ccFiltroTipo') == "directa" ? "selected" : ""}}>Venta directa</option>
                                <option value="am" {{session('ccFiltroTipo') == "am" ? "selected" : ""}}>Acuerdo marco</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkResponsableOportunidad" {{session('ccFiltroResponsableOportunidad') != null ? "checked" : ""}}> Resp. oportunidad
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-control" name="selectResponsableOportunidad">
                                @foreach ($responsablesOportunidad as $responsable)
                                <option value="{{$responsable->id}}" {{session('ccFiltroResponsableOportunidad') == $responsable->id ? "selected" : ""}}>{{$responsable->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkResponsableAprobacion" {{session('ccFiltroResponsableAprobacion') != null ? "checked" : ""}}> Resp. aprobación
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-control" name="selectResponsableAprobacion">
                                @foreach ($responsablesAprobacion as $responsable)
                                <option value="{{$responsable->id}}" {{session('ccFiltroResponsableAprobacion') == $responsable->id ? "selected" : ""}}>{{$responsable->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExportarLista" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Exportar lista</h4>
            </div>
            <div class="modal-body">
                <p>Se exportará la lista de acuerdo a los filtros aplicados (no se toma en cuenta el criterio de búsqueda ingresado)</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <a class="btn btn-primary" id="btnExportarLista" href="{{route("mgcp.cuadro-costos.exportar-lista")}}" target="_blank">Exportar</a>
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
    <script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>
    <link href='{{asset("assets/lobibox/dist/css/lobibox.min.css")}}' rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}'></script>
    <script src="{{ asset('mgcp/js/util.js?v=27') }}"></script>
    <script src='{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}'></script>
    <script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>

    <link href="{{asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-base-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-base-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-base-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-base-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-costo-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-costo-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-costo-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-costo-model.js')) }}"></script>
    <script>
        $(document).ready(function() {
            $(".sidebar-mini").addClass("sidebar-collapse");
            Util.seleccionarMenu(window.location);
            Util.activarDatePicker();

            const cuadroCostoView = new CuadroCostoView(0, new CuadroCostoModel('{{csrf_token()}}'));
            cuadroCostoView.listar();
            Util.activarFiltros('#tableDatos', cuadroCostoView.model);
        });
    </script>
@endsection
