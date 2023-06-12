@extends('mgcp.layouts.app')
@section('estilos')
<link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('cabecera')
Historial de actualizaciones de producto
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Productos</li>
    <li class="active">Historial de act.</li>
</ol>
@endsection

@section('cuerpo')
@include('mgcp.partials.acuerdo-marco.producto.actualizar-stock-precio')
<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableHistorial" class="table table-condensed table-striped table-hover" style="font-size: small; width: 100%">
                <thead>
                    <tr>
                        <th class="text-center">Usuario</th>
                        <th class="text-center">Marca</th>
                        <th class="text-center" width="220">Modelo</th>
                        <th class="text-center">Nro. parte</th>
                        <th class="text-center">Detalle</th>
                        <th class="text-center">Comentario</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center" style="width: 10%">Herramientas</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
@section('scripts')
    <script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>
    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>
    <script src='{{ asset("mgcp/js/util.js?v=2") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-model.js?v=4") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-view.js?v=4") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/producto/historial-model.js?v=4") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/producto/historial-view.js?v=4") }}'></script>

    <script>
        $(document).ready(function () {
            //*****INICIALIZACION*****
            Util.seleccionarMenu(window.location);
            Util.activarSoloDecimales();
            
            const historialModel = new HistorialProductoModel('{{csrf_token()}}');
            const historialView = new HistorialProductoView(historialModel);
            historialView.listar();
            const productoModel = new ProductoModel('{{csrf_token()}}');
            const productoView = new ProductoView(productoModel);
            productoView.obtenerPrecioStockPortalEvent();
        });

    </script>
@endsection