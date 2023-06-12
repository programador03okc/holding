@extends('mgcp.layouts.app')
@section('estilos')
<style>
</style>
@endsection

@section('cabecera')
Reporte de O/C públicas por productos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">Reporte O/C públicas</li>
    <li class="active">Productos</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table width="100%" id="tableProductos" class="table table-bordered table-condensed table-striped table-hover" width="100%">
                <thead>
                    <tr>
                        <th class="text-center">Catálogo</th>
                        <th class="text-center">Categoría</th>
                        <th class="text-center">Marca</th>
                        <th class="text-center">Modelo</th>
                        <th class="text-center">Nro. parte</th>
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

<!--****REPORTE PRODUCTOS****-->
<div class="modal fade" id="modalFiltrosProductos" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Filtros para reporte de productos</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFecha" @if (session('rpProductoFechaEntre')!==null)  checked @endif> Fecha
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaDesde" class="form-control date-picker" value="@if (session('rpProductoDesde')!==null){{session('rpProductoDesde')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaHasta" class="form-control date-picker" value="@if (session('rpProductoHasta')!==null){{session('rpProductoHasta')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkMarca" @if (session('rpProductoMarca')!==null) checked @endif> Marca
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectMarca" class="form-control">
                                @foreach ($marcas as $marca)
                                <option value="{{$marca->marca}}" @if (session('rpProductoMarca')==$marca->marca) selected @endif>{{$marca->marca}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkCatalogo" @if (session('rpProductoCatalogo')!==null) checked @endif> Catálogo
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectCatalogo" class="form-control">
                                @foreach ($catalogos as $catalogo)
                                <option value="{{$catalogo->descripcion}}" @if (session('rpProductoCatalogo')==$catalogo->descripcion) selected @endif>{{$catalogo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFechaFormalizacion" @if (session('rpProductoFecha')!==null) checked @endif> Sólo órdenes con fecha de formalización
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

<div class="modal fade" id="modalDatosProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ver datos adicionales de producto</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetallesProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detalles de reporte de producto</h4>
            </div>
            <div class="modal-body">
                <div><strong>Marca: </strong><span class="marca"></span> / <strong>Nro. parte: </strong><span
                        class="nro-parte"></span></div>
                <table id="tableDetallesProducto" class="table table-condensed table-striped table-hover" width="100%"
                    style="cellspacing: 0; font-size: x-small;">
                    <thead>
                        <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Entidad</th>
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
<script src='{{ asset("mgcp/js/acuerdo-marco/orden-compra/publica/reporte/producto.js?v=2") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/producto.js?v=4") }}'></script>
<script>
$(document).ready(function() {
    var actualizarListaProductos = false;
    var tcUsd = parseFloat('{{$ultimoPrecio}}');
    Util.seleccionarMenu(window.location);
    Producto.init('{{csrf_token()}}');
    ReporteProducto.init('{{csrf_token()}}');
    Util.activarDatePicker();

    //*****PRODUCTO*****
    $('#tableProductos').on("click", "a.producto", function (e) {
            e.preventDefault();
            Producto.detallesPorMMN($(this), "{{ route('mgcp.acuerdo-marco.productos.detalles-por-mmn') }}");
        });

    //*****REPORTE*****
    ReporteProducto.resumen("{{ route('mgcp.acuerdo-marco.ordenes-compra.publicas.reportes.productos.resumen') }}");

    $('#modalFiltrosProductos').on('hidden.bs.modal', function(e) {
        if (actualizarListaProductos) {
            actualizarListaProductos = false;
            $('#tableProductos').DataTable().ajax.reload();
        }
    });

    $('#tableProductos').on('click', 'a.producto-nroparte', function(e) {
        e.preventDefault();
        ReporteProducto.obtenerNumeroParte($(this), "");
    });

    $('#modalFiltrosProductos').find('input,select').change(function() {
        actualizarListaProductos = true;
        ReporteProducto.actualizarFiltros("{{ route('mgcp.acuerdo-marco.ordenes-compra.publicas.reportes.productos.actualizar-filtros') }}");
    });

    $('#tableProductos').on('click', 'a.reporteDetalles', function(e) {
        e.preventDefault();
        ReporteProducto.detalles($(this),"{{ route('mgcp.acuerdo-marco.ordenes-compra.publicas.reportes.productos.detalles') }}", tcUsd);
    });

    $('#modalDetallesProducto').on('hidden.bs.modal', function (e) {
        $('#tableDetallesProducto').DataTable().destroy();
        $('#tableDetallesProducto').find('tbody').empty();
    });
});
</script>
@endsection