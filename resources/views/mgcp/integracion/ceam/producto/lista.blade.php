@extends('mgcp.layouts.app')
@section('estilos')
    <link href='{{ asset("assets/datatables/css/dataTables.bootstrap.min.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css") }}' rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/lobibox/dist/css/lobibox.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .dataTables_wrapper .dataTables_filter input[type="search"] {
            width: 450px;
        }
        #tableProductos {
            color: #000;
        }
        @media (max-width: 968px) {
            .dataTables_wrapper .dataTables_filter input[type="search"] {
                width: auto;
            }
        }
    </style>
@endsection

@section('cabecera') Lista de productos @endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{ route('mgcp.home') }}">Inicio</a></li>
    <li class="active">Integraciones</li>
    <li class="active">CEAM</li>
    <li class="active">Productos</li>
    <li class="active">Lista</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableProductos" class="table table-condensed table-hover table-striped" style="font-size: 12px; width: 100%">
                <thead>
                    <tr>
                        <th style="width: 15%" class="text-center">Acuerdo</th>
                        <th style="width: 15%" class="text-center">Categoría</th>
                        <th class="text-center">Producto</th>
                        <th style="width: 10%" class="text-center">Marca</th>
                        <th style="width: 10%" class="text-center">Nro. parte</th>
                        <th style="width: 5%" class="text-center">Tipo</th>
                        <th style="width: 5%" class="text-center" style="width: 10%">Herramientas</th>
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
                        <div class="col-sm-4 form-control-static">
                            Sólo los catálogos:
                        </div>
                        <div class="col-sm-8">
                            @foreach ($catalogos as $catalogo)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkCatalogo[]" value="{{ $catalogo->id }}" @if (session()->has('prod_catalogos') && in_array($catalogo->id,session('prod_catalogos'))) checked @endif> 
                                    {{ $catalogo->descripcion_catalogo }}
                                    <small class="help-block" style="display: inline">({{ $catalogo->descripcion_am }})</small>
                                </label>
                            </div>
                            @endforeach
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

<div class="modal fade" id="modalImportar" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Importar lista de productos CEAM</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="formImportar" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Archivo</label>
                        <div class="col-sm-8">
                            <input type="file" name="archivo" class="form-control" style="margin-bottom: 10px;">
                            <a href="{{route('mgcp.integraciones.ceam.productos.descargar-plantilla')}}" target="_blank">Descargar plantilla</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnImportar" class="btn btn-primary">Subir</button>
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
    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>

    <script src='{{ asset("mgcp/js/util.js?v=11") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-model.js?v=10") }}'></script>
    <script src='{{ asset("mgcp/js/integracion/ceam/producto/producto-ceam-view.js?v=1") }}'></script>
    <script>
        $(document).ready(function() {
            //*****INICIALIZACION*****
            Util.seleccionarMenu(window.location);
            Util.activarSoloDecimales();
            $(".sidebar-mini").addClass("sidebar-collapse");

            const token = '{{ csrf_token() }}';
            const productoView = new ProductoCeamView(new ProductoModel(token));
            productoView.listar();
            productoView.importar();
            Util.activarFiltros('#tableProductos', productoView.model);
        });
    </script>
@endsection