@extends('mgcp.layouts.app')
@section('estilos')
<link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>

<link href="{{asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css"/>
<style>

</style>
@endsection
@section('contenido')


@section('cabecera')
Órdenes de compra públicas
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">O/C públicas</li>
</ol>
@endsection

@section('cuerpo')

@include('mgcp.partials.acuerdo-marco.producto.actualizar-stock-precio')
@include('mgcp.partials.acuerdo-marco.producto.historial-actualizaciones')
@include('mgcp.partials.acuerdo-marco.producto.detalles')
@include('mgcp.partials.acuerdo-marco.entidad.detalles')

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableOrdenes" class="table table-hover table-condensed table-striped" style="font-size: small; width:100%">
                <thead>
                    <tr>
                        <th style="width:10%" class="text-center">O/C</th>
                        <th style="width:7%" class="text-center">Fecha</th>
                        <th style="width:15%" class="text-center">Entidad</th>
                        <th style="width:15%" class="text-center">Proveedor</th>
                        <th style="width:9%" class="text-center">Categoría</th>
                        <th style="width:10%" class="text-center">Producto</th>
                        <th style="width:8%" class="text-center">Nro Parte</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Precio soles</th>
                        <th class="text-center">Precio dólares</th>
                        <th class="text-center">Costo envío</th>
                        <th style="width:6%" class="text-center">Días entrega</th>
                        <th style="width:6%" class="text-center">Lugar entrega</th>
                        <th style="width:8%" class="text-center">Herram.</th>
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
                        <label class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkOdenesProducto" @if (session('oc_ordenes_producto')!==null) checked @endif> Órdenes con productos que tengo publicados
                                </label>
                            </div>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkOrdenesFecha" @if (session('oc_ordenes_fecha')!==null) checked @endif> Órdenes con fecha
                                </label>
                            </div>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFecha" @if (session('oc_filtro_fecha')!==null) checked @endif> Fecha
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaDesde" class="form-control date-picker" value="@if (session('oc_fecha_desde')!==null){{session('oc_fecha_desde')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaHasta" class="form-control date-picker" value="@if (session('oc_fecha_hasta')!==null){{session('oc_fecha_hasta')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
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

@endsection

@section('scripts')
<script src="{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}"></script>
<script src="{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}"></script>

<script src="{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}"></script>
<script src="{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}"></script>
<link href="{{asset('assets/lobibox/dist/css/lobibox.min.css')}}" rel="stylesheet" type="text/css" />
<script src="{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}"></script>
<script src="{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}"></script>
<script src="{{ asset("mgcp/js/util.js?v=11") }}"></script>
<script src='{{ asset("mgcp/js/orden-compra/publica/orden-compra-publica-model.js?v=13") }}'></script>
<script src='{{ asset("mgcp/js/orden-compra/publica/orden-compra-publica-view.js?v=13") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=12") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=12") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/contacto-model.js?v=12") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/contacto-view.js?v=12") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/producto/historial-model.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/producto/historial-view.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-model.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-view.js?v=11") }}'></script>
<script>
    $(document).ready(function () {
        Util.activarDatePicker();
        Util.seleccionarMenu(window.location);
        const token = '{{csrf_token()}}';
        const entidadView = new EntidadView(new EntidadModel(token));
        const contactoView = new ContactoEntidadView(new ContactoEntidadModel(token),'{{Auth::user()->tieneRol(60)}}');
        const ocPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
        const historialView = new HistorialProductoView(new HistorialProductoModel(token));
        const productoView = new ProductoView(new ProductoModel(token));

        historialView.obtenerHistorialEvent();
        entidadView.obtenerDetallesEvent();
        productoView.obtenerPrecioStockPortalEvent();
        productoView.obtenerDetallesEvent();
        ocPublicaView.listar();
        Util.activarFiltros('#tableOrdenes', ocPublicaView.model);
    });

</script>
@endsection
