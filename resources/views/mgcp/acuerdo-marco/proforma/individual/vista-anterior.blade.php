@extends('mgcp.layouts.app')

@section('estilos')
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
        .help-block {
            margin-bottom: 0px;
        }
    </style>
@endsection

@section('cabecera')
Proformas de {{$tipoProforma==1 ? 'compra ordinaria' : 'gran compra'}} individual
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Proformas</li>
    <li class="active">Individual</li>
    <li class="active">{{$tipoProforma==1 ? 'Compra ordinaria' : 'Gran compra'}}</li>
</ol>
@endsection

@section('cuerpo')

@include('mgcp.partials.acuerdo-marco.producto.actualizar-stock-precio')
@include('mgcp.partials.acuerdo-marco.producto.detalles')
@include('mgcp.partials.acuerdo-marco.producto.historial-actualizaciones')
@include('mgcp.partials.acuerdo-marco.orden-compra.publica.ofertas-por-producto')
@include('mgcp.partials.acuerdo-marco.entidad.detalles')

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableProformas" class="table table-striped table-condensed table-hover" style="width: 100%; font-size: x-small;">
                <thead>
                    <tr>
                        <th style="width: 8%" class="text-center">Requerimiento</th>
                        <th style="width: 8%" class="text-center">Proforma</th>
                        <th class="text-center">Entidad</th>
                        <th style="width: 6%" class="text-center">F.emisión<br>/ F.límite</th>
                        <th style="width: 8%" class="text-center">Categoría</th>
                        <th style="width: 9%" class="text-center">Producto</th>
                        <th style="width: 9%" class="text-center">Nro. parte</th>
                        <th style="width: 8%" class="text-center" title="Inicio de entrega / Fin de entrega">InicioEnt.<br>/ FinEnt.</th>
                        <th style="width: 6%" title="Herramientas" class="text-center">Herram.</th>
                        <th style="width: 5%" title="Empresa" class="text-center">Emp.</th>
                        <th style="width: 10%" class="text-center">Lugar de<br>entrega</th>
                        <th style="width: 5%" title="Precio unitario base / Software educativo" class="text-center">Prec.Un.B./<br>Soft.Educ.</th>
                        <th class="text-center">Cant.</th>
                        <th style="width: 7%" class="text-center">Estado</th>
                        @if ($tipoProforma=='2')
                        <th style="width: 5%" class="text-center">Plazo<br>entrega</th>
                        @endif
                        <th style="width: 5%" class="text-center">Precio<br>publicar</th>
                        <th style="width: 5%" class="text-center">Flete<br>publicar</th>
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
                                <label>
                                    <input type="checkbox" name="chkFechaEmision" @if (session('proformaFechaEmisionDesde')!==null) checked @endif> Fecha de emisión
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEmisionDesde" class="form-control date-picker" value="@if (session('proformaFechaEmisionDesde')!==null){{session('proformaFechaEmisionDesde')}}@else{{$fechaActual->addMonths(-1)->format('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEmisionHasta" class="form-control date-picker" value="@if (session('proformaFechaEmisionHasta')!==null){{session('proformaFechaEmisionHasta')}}@else{{$fechaActual->addMonths(1)->format('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFechaLimite" @if (session('proformaFechaLimiteDesde')!==null) checked @endif> Fecha límite
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaLimiteDesde" class="form-control date-picker" value="@if (session('proformaFechaLimiteDesde')!==null){{session('proformaFechaLimiteDesde')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaLimiteHasta" class="form-control date-picker" value="@if (session('proformaFechaLimiteHasta')!==null){{session('proformaFechaLimiteHasta')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEmpresa" @if (session('proformaEmpresas')!==null) checked @endif> Empresa
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEmpresa" class="form-control">
                                @foreach ($empresas as $empresa)
                                <option @if (session()->has('proformaEmpresas') && in_array($empresa->id,session('proformaEmpresas')))
                                    selected
                                    @endif
                                    value="{{$empresa->id}}">{{$empresa->empresa}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkCatalogo" @if (session('proformaCatalogos')!==null) checked @endif> Catálogos
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectCatalogo[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                @foreach ($catalogos as $catalogo)
                                <option @if (session()->has('proformaCatalogos') && in_array($catalogo->id,session('proformaCatalogos')))
                                    selected
                                    @endif
                                    value="{{$catalogo->id}}">{{$catalogo->catalogo}} ({{$catalogo->acuerdo_marco}})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkDepartamento" @if (session('proformaDepartamentos')!==null) checked @endif> Departamentos
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectDepartamento[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                @foreach ($departamentos as $departamento)
                                <option @if (session()->has('proformaDepartamentos') && in_array($departamento->id,session('proformaDepartamentos')))
                                    selected
                                    @endif
                                    value="{{$departamento->id}}">{{$departamento->nombre}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstado" @if (session('proformaEstado')!==null) checked @endif> Estado
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEstado" class="form-control">
                                @foreach($estados as $estado)
                                <option value="{{$estado->estado}}" {{session('proformaEstado')==$estado->estado ? 'selected' : ''}}>{{$estado->estado}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkMarca" @if (session('proformaMarcas')!==null) checked @endif> Marca
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectMarca[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                @foreach ($marcas as $marca)
                                <option @if (session()->has('proformaMarcas') && in_array($marca->marca,session('proformaMarcas')))
                                    selected
                                    @endif
                                    value="{{$marca->marca}}">{{$marca->marca}}
                                </option>
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

<div class="modal fade" id="modalProformasEnviar" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar cotizaciones a Perú Compras</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial">
                </div>
                <p>Seleccione las proformas cotizadas que desea enviar al portal y haga clic en el botón Enviar para continuar. Puede filtrar la lista por empresa o usuario que realizó la cotización. Al filtrar la lista, las proformas ocultas permanecen seleccionadas</p>
                <div class="table-responsive">
                    <table class="table table-condensed table-hover table-striped" style="font-size: x-small; width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 3%" class="text-center">N°</th>
                                <th style="width: 7%" class="text-center">Proforma</th>
                                <th style="width: 7%" class="text-center">Producto</th>
                                <th style="width: 7%" class="text-center">Part N°</th>
                                <th style="width: 9%" class="text-center">Empresa<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarEmpresa">
                                </th>
                                <th style="width: 15%" class="text-center">Lugar entrega</th>
                                <th class="text-center">Fecha límite<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarFechaLimite">
                                </th>
                                <th style="width: 9%" class="text-center">Última edición<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarUsuario">
                                </th>
                                <th class="text-center">Precio publicar</th>
                                <th class="text-center">Flete publicar</th>
                                <th class="text-center">Selec.<br>
                                    <input type="checkbox" id="chkSeleccionarTodo">
                                </th>
                                <th width="12%" class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProformasEnviar">
                        </tbody>
                    </table>
                </div>
                <div id="divProformasEnviarMensaje">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="text" class="btn btn-primary" id="btnEnviarProformas">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUltimaActualizacionLista" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Última actualización de lista</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <div class="table-responsive">
                    <table class="table table-condensed table-hover table-striped" style="width: 100%;font-size: small;">
                        <thead>
                            <tr>
                                <th style="width: 25%" class="text-center">Empresa</th>
                                <th style="width: 35%" class="text-center">Realizada por</th>
                                <th class="text-center">Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyUltimaActualizacionLista">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <a target="_blank" href="{{route('mgcp.acuerdo-marco.descargar.proformas.index')}}" class="btn btn-default">Ir a descarga de proformas</a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalIngresarFletePorLote" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ingresar flete por lote</h4>
            </div>
            <div class="modal-body">
                <p>Sólo se ingresará el flete a las siguientes proformas:</p>
                <ul>
                    <li>Con estado PENDIENTE</li>
                    <li>Que no tengan flete ingresado</li>
                    <li>Que sean parte del resultado de los filtros aplicados (no se toma en cuentra el criterio de búsqueda ingresado)</li>
                </ul>
                <p>El monto del flete se ingresará de forma automática dependiendo del precio del producto</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnIngresarFletePorLote">Ingresar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeshacerCotizacion" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Deshacer cotización</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <p>Información de la proforma:</p>
                <ul>
                    <li>Número: <span class="proforma"></span></li>
                    <li>Requer.: <span class="requerimiento"></span></li>
                    <li>Producto: <span class="producto"></span></li>
                    <li>Entidad: <span class="entidad"></span></li>
                    <li>Empresa: <span class="empresa"></span></li>
                    <li>Precio publicar: <span class="precio-publicar"></span></li>
                    <li>Flete publicar: <span class="flete-publicar"></span></li>
                </ul>
                <p>Al deshacer la cotización, esta proforma podrá volverse a cotizar. Los precios ingresados no se eliminarán. ¿Desea continuar?</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnDeshacerCotizacion">Deshacer cotización</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFondosDisponibles" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Fondos disponibles de proveedores</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableFondosProforma" style="width: 100%" class="table table-condensed table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60%">Descripción</th>
                                <th class="text-center" style="width: 20%">Valor unitario</th>
                                <th class="text-center" style="width: 20%">Cantidad disponible</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalComentarios" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Comentarios</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Lista</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed table-hover table-stripped" style="font-size:small">
                            <thead>
                                <tr>
                                    <th width="25%" class="text-center">Usuario</th>
                                    <th width="50%" class="text-center">Comentario</th>
                                    <th width="25%" class="text-center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyComentarios">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box box-solid last">
                    <div class="box-header with-border">
                        <h3 class="box-title">Nuevo comentario</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <textarea placeholder="Ingrese un comentario" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnRegistrarComentario" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <link href="{{ asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js') }}"></script>

    <link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js') }}"></script>

    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js') }}"></script>

    <link href="{{ asset('assets/lobibox/dist/css/lobibox.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>

    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('mgcp/js/util.js?v=27') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/producto/producto-model.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/producto/producto-view.js?v=11') }}"></script>

    <script src="{{ asset('mgcp/js/acuerdo-marco/descarga/proforma/descarga-proforma-view.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/descarga/proforma/descarga-proforma-model.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/producto/historial-model.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/producto/historial-view.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/orden-compra/publica/orden-compra-publica-model.js?v=21') }}"></script>
    <script src="{{ asset('mgcp/js/orden-compra/publica/orden-compra-publica-view.js?v=21') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/contacto-model.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/contacto-view.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/fondo-proveedor-view.js?v=13') }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/fondo-proveedor-model.js?v=13') }}"></script>

    <script type="module">
        import COVistaAnteriorModel from "{{ asset('mgcp/js/acuerdo-marco/proforma/individual/compra-ordinaria/co-vista-anterior-model.js?v=23') }}";
        import COVistaAnteriorView from "{{ asset('mgcp/js/acuerdo-marco/proforma/individual/compra-ordinaria/co-vista-anterior-view.js?v=23') }}";

        import ProformaIndividualModel from "{{ asset('mgcp/js/acuerdo-marco/proforma/individual/proforma-individual-model.js?v=23') }}";
        import GCVistaAnteriorView from "{{ asset('mgcp/js/acuerdo-marco/proforma/individual/gran-compra/gc-vista-anterior-view.js?v=23') }}";

        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            Util.activarSoloDecimales();
            Util.activarDatePicker();
            $(".sidebar-mini").addClass("sidebar-collapse");

            const token = '{{csrf_token()}}';
            const tipoProforma = '{{$tipoProforma}}';
            const entidadView = new EntidadView(new EntidadModel(token));
            const contactoView = new ContactoEntidadView(new ContactoEntidadModel(token), '{{Auth::user()->tieneRol(60)}}');
            const ocPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
            const historialView = new HistorialProductoView(new HistorialProductoModel(token));

            const productoView = new ProductoView(new ProductoModel(token));
            const descargaProformaView = new DescargaProformaView(new DescargaProformaModel(token));
            const fondo = new FondoProveedorView(new FondoProveedorModel(token));

            let proformaView;
            if (tipoProforma == '1') {
                proformaView = new COVistaAnteriorView(new COVistaAnteriorModel(token), "{{Auth::user()->id}}");
                proformaView.ingresarFletePorLoteEvent();
            } else {
                proformaView = new GCVistaAnteriorView(new ProformaIndividualModel(token), "{{Auth::user()->id}}");
            }
            proformaView.listarProformas("{{Auth::user()->tieneRol(44)}}", "{{Auth::user()->tieneRol(123)}}");
            proformaView.enviarCotizacionesEvent();
            proformaView.deshacerCotizacionEvent();
            proformaView.actualizarCamposEvent();

            Util.activarFiltros('#tableProformas');

            historialView.obtenerHistorialEvent();
            ocPublicaView.verOfertasPorMMNEvent();
            entidadView.obtenerDetallesEvent();
            productoView.obtenerPrecioStockPortalEvent();
            productoView.obtenerDetallesEvent();
            descargaProformaView.obtenerFechasUltimaDescargaEvent();
            fondo.obtenerFondosParaProformaEvent();
        });
    </script>
@endsection