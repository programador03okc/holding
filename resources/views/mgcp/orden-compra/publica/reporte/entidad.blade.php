@extends('mgcp.layouts.app')
@section('estilos')
<style>
</style>
@endsection

@section('cabecera')
Reporte de O/C públicas por entidades
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">Reporte O/C públicas</li>
    <li class="active">Entidades</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableEntidades" class="table table-bordered table-condensed table-striped table-hover"
                width="100%">
                <thead>
                    <tr>
                        <th class="text-center">Entidad</th>
                        <th class="text-center">Catálogo</th>
                        <th class="text-center">Categoría</th>
                        <th class="text-center">Marca</th>
                        <th class="text-center">Ctd. de órdenes</th>
                        <th class="text-center">Ctd. de productos</th>
                        <th class="text-center">Monto</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>



<!--****REPORTE ENTIDADES****-->
<div class="modal fade" id="modalFiltrosEntidades" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Filtros para reporte de entidades</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFecha" @if (session('rpEntidadFechaEntre')!==null) checked @endif> Fecha
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaDesde" class="form-control date-picker"
                                value="@if (session('rpEntidadDesde')!==null){{session('rpEntidadDesde')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaHasta" class="form-control date-picker"
                                value="@if (session('rpEntidadHasta')!==null){{session('rpEntidadHasta')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkMarca" @if (session('rpEntidadMarca')!==null) checked @endif> Marca
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectMarca" class="form-control">
                                @foreach ($marcas as $marca)
                                <option value="{{$marca->marca}}" @if (session('rpEntidadMarca')==$marca->marca) selected @endif>{{$marca->marca}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkCatalogo" @if (session('rpEntidadCatalogo')!==null) checked @endif> Catálogo
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">

                            <select name="selectCatalogo" class="form-control">
                                @foreach ($catalogos as $catalogo)
                                <option value="{{$catalogo->descripcion}}" @if (session('rpEntidadCatalogo')==$catalogo->descripcion) selected @endif>{{$catalogo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFechaFormalizacion" @if (session('rpEntidadFecha')!==null) checked @endif> Sólo órdenes con fecha de
                                    formalización
                                </label>
                            </div>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetallesEntidad" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detalles de reporte de entidad</h4>
            </div>
            <div class="modal-body">
                <div>
                    <strong>Entidad: </strong><span class="entidad"></span>
                </div>
                <div>
                    <strong>Categoría: </strong><span class="categoria"></span> / <strong>Marca: </strong><span class="marca"></span>
                </div>
                <table id="tableDetallesEntidad" class="table table-condensed table-striped table-hover" width="100%"
                    style="cellspacing: 0; font-size: x-small;">
                    <thead>
                        <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Producto</th>
                            <th class="text-center">O/C</th>
                            <th class="text-center">Proveedor</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-center">Precio soles</th>
                            <th class="text-center">Precio dólares</th>
                            <th class="text-center">Costo envío</th>
                            <th class="text-center">Subtotal</th>
                            <th class="text-center">Plazo ent.</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
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
<link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css"/>

<script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

<script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
<script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
<script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
<script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>

<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/orden-compra/publica/reporte/entidad.js?v=2") }}'></script>
<script>
$(document).ready(function() {
    var actualizarListaEntidades = false;
    var tcUsd = parseFloat('{{$ultimoPrecio}}');
    Util.seleccionarMenu(window.location);
    ReporteEntidad.init('{{csrf_token()}}');
    Util.activarDatePicker();

    //*****ENTIDADES*****
    ReporteEntidad.resumen(
        "{{ route('mgcp.acuerdo-marco.ordenes-compra.publicas.reportes.entidades.resumen') }}");

    $('#modalFiltrosEntidades').on('hidden.bs.modal', function(e) {
        if (actualizarListaEntidades) {
            actualizarListaEntidades = false;
            $('#tableEntidades').DataTable().ajax.reload();
        }
    });

    $('#modalFiltrosEntidades').find('input,select').change(function() {
        actualizarListaEntidades = true;
        ReporteEntidad.actualizarFiltros(
            "{{ route('mgcp.acuerdo-marco.ordenes-compra.publicas.reportes.entidades.actualizar-filtros') }}"
        );
    });

    $('#tableEntidades').on('click', 'a.reporteDetalles', function(e) {
        e.preventDefault();
        ReporteEntidad.detalles($(this),
            "{{ route('mgcp.acuerdo-marco.ordenes-compra.publicas.reportes.entidades.detalles') }}",
            tcUsd);
    });

    $('#modalDetallesEntidad').on('hidden.bs.modal', function (e) {
        $('#tableDetallesEntidad').DataTable().destroy();
        $('#tableDetallesEntidad').find('tbody').empty();
    });
});
</script>
@endsection