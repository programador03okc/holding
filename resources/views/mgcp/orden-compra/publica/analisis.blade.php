@extends('mgcp.layouts.app')
@section('estilos')
    <link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/lobibox/dist/css/lobibox.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css"/>
    <style>
        .mb-3 {
            margin-bottom: 10px;
        }
        .mb-4 {
            margin-bottom: 15px;
        }
        .mb-5 {
            margin-bottom: 20px !important;
        }
        fieldset {
            padding: 10px;
            border: 1px solid #ddd;
        }
        fieldset legend {
            width: auto;
            padding: 0 10px;
            border: 0;
            margin-bottom: 0;
            font-weight: 600;
        }
        .modal-xl {
            width: 75%;
        }
        .modal .bootstrap-select .dropdown-menu {
            min-width: 100% !important;
            max-width: 100% !important;
            font-size: 12.5px
        }
        .modal .dropdown-menu .form-control {
            height: 30px;
        }
        .modal .bootstrap-select > .dropdown-toggle {
            border-radius: 0;
        }
        .modal .bootstrap-select > .btn {
            font-size: 13px !important;
        }

        table.cabecera {
            margin-bottom: 0px;
        }
    
        table.cabecera td {
            border-top: none !important;
            padding: 0px !important;
        }
    
        .table>thead>tr>th {
            vertical-align: middle;
        }
    
        table.ocp th {
            border-bottom: none !important;
        }
    
        #divBodyAnalisis div.panel-primary>div.panel-heading {
            background-color: #3c8dbc !important;
            border-color: #3c8dbc !important;
        }
    
        table.ocp thead a {
            color: white;
        }
    
        /*Contenido de analisis de oc*/
        #divBodyAnalisis div.panel-heading {
            padding: 2px 2px;
        }
    
        #divBodyAnalisis div.panel-body {
            padding: 0px;
        }
    
        #divBodyAnalisis td {
            padding: 1px !important;
        }
    
        #divBodyAnalisis table {
            margin-bottom: 0px !important;
        }
    
        #divBodyAnalisis div.panel {
            margin-bottom: 10px !important;
        }
    </style>
@endsection
@section('contenido')


@section('cabecera')
Análisis OC públicas
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">Análisis</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid mb-4">
    <div class="box-header with-border"><h3 class="box-title">Controles y filtros</h3></div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-inline text-center">
                    <button type="button" class="btn btn-sm btn-default" onclick="abrirFiltros();">
                        <span class="fa fa-filter"></span> Filtros <span id="spanCantFiltros">0</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-default" onclick="abrirModal();">
                        <span class="fa fa-plus"></span> Agregar registro
                    </button>
                    <button type="button" class="btn btn-sm btn-default" onclick="exportar();">
                        <span class="fa fa-download"></span> Exportar
                    </button>
                    <div class="input-group input-group-sm">
                        <input type="text" id="txtCriterio" class="form-control" placeholder="Buscar...">
                        <span class="input-group-btn">
                            <button  type="button" class="btn btn-primary" id="btnBuscar"><span class="fa fa-search"></span></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid" id="divContenedorAnalisis">
    <div class="box-header with-border"><h3 class="box-title">Lista de órdenes públicas</h3></div>
    <div class="box-body" id="divBodyAnalisis"></div>
    <div class="box-footer text-center" id="divFooterAnalisis"></div>
</div>

<div class="modal fade" id="modalData" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form class="form-horizontal bloquear-boton" id="formRegistro" method="POST">
                @csrf

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registro de Ordenes de Compra Públicas</h4>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <h6>Fecha</h6>
                            <input type="text" name="fecha" class="form-control input-sm text-center date-picker" value="{{ date('d-m-Y') }}">
                        </div>
                        <div class="col-md-2">
                            <h6>Fecha convocatoria</h6>
                            <input type="text" name="fecha_convocatoria" class="form-control input-sm text-center date-picker" value="{{ date('d-m-Y') }}" onchange="activarTipoCambio(this.value);">
                        </div>
                        <div class="col-md-2">
                            <h6>Cantidad</h6>
                            <input type="number" name="cantidad" class="form-control input-sm text-center" step="any" min="0" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <h6>Entidad</h6>
                            <select name="id_entidad" class="selectpicker" title="Elija una entidad" data-live-search="true" data-width="100%" data-actions-box="false" data-size="10">
                                @foreach ($entidades as $entidad)
                                    <option value="{{ $entidad->id }}">{{ $entidad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12 text-right">
                            <label class="text-danger" id="tcSbs"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset>
                                <legend>1° Datos de la empresa</legend>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6>Empresa</h6>
                                        <select name="id_empresa" class="form-control input-sm" required>
                                            <option value="" selected disabled>Elija una empresa</option>
                                            @foreach ($empresas as $empresa)
                                                <option value="{{ $empresa->id }}">{{ $empresa->empresa }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Part Number</h6>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="part_number" class="form-control text-left" id="txtPN" placeholder="PN del producto">
                                            <input type="hidden" name="id_producto">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-flat" id="btnPN" onclick="buscarProducto(1);">
                                                    <span class="fa fa-search"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <h6>Descripción</h6>
                                        <textarea name="descripcion" class="form-control input-sm" rows="3" style="resize: none;" readonly></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6>Precio costo (USD)</h6>
                                        <input type="text" name="costo" class="form-control input-sm text-center" value="0.00">
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Precio soles (S/)</h6>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="precio_sol" class="form-control text-center" id="txtSol" value="0.00">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-flat" id="btnCalcSol" onclick="calcular(1, 'int', 'soles');">
                                                    <span class="fa fa-th-list"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <h6>Precio dolares (USD)</h6>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="precio_dol" class="form-control text-center" id="txtDol" value="0.00">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-flat" id="btnCalcDol" onclick="calcular(1, 'int', 'dolares');">
                                                    <span class="fa fa-th-list"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Total (USD)</h6>
                                        <input type="text" name="total" class="form-control input-sm text-right" value="0.00" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Margen (%)</h6>
                                        <input type="text" name="margen" class="form-control input-sm text-right" value="0.00%" readonly>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset>
                                <legend>2° Datos de la competencia</legend>
                                <div class="row mb-3">
                                    <div class="col-md-7">
                                        <h6>Proveedor</h6>
                                        <select name="id_proveedor" class="selectpicker" title="Elija un proveedor" data-live-search="true" data-width="100%" data-actions-box="false" data-size="10">
                                            @foreach ($proveedores as $proveedor)
                                                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <h6>Part Number</h6>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="part_number_ext" class="form-control text-left" id="txtPNext" placeholder="PN del producto">
                                            <input type="hidden" name="id_producto_ext">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-flat" id="btnPNext" onclick="buscarProducto(2);">
                                                    <span class="fa fa-search"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <h6>Descripción</h6>
                                        <textarea name="descripcion_ext" class="form-control input-sm" rows="3" style="resize: none;" readonly></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6>Precio costo (USD)</h6>
                                        <input type="text" name="costo_ext" class="form-control input-sm text-center" value="0.00">
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Precio soles (S/)</h6>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="precio_sol_ext" class="form-control text-center" id="txtSolExt" value="0.00">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-flat" id="btnCalcSolExt" onclick="calcular(2, 'ext', 'soles');">
                                                    <span class="fa fa-th-list"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <h6>Precio dolares (USD)</h6>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="precio_dol_ext" class="form-control text-center" id="txtDolExt" value="0.00">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-flat" id="btnCalcDolExt" onclick="calcular(2, 'ext', 'dolares');">
                                                    <span class="fa fa-th-list"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Total (USD)</h6>
                                        <input type="text" name="total_ext" class="form-control input-sm text-right" value="0.00" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Margen (%)</h6>
                                        <input type="text" name="margen_ext" class="form-control input-sm text-right" value="0.00%" readonly>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarRegistro();">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDatosProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ver datos adicionales del producto</h4>
            </div>
            <div class="modal-body">
                <div class="contenido"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
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
                    <input type="hidden" id="txtCriterioHidden" name="criterio">
                    <input type="hidden" id="txtNroPaginaHidden" name="pagina" value="1">

                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="fom-control-static mensaje-filtros">Seleccione los filtros que desee aplicar y cierre este cuadro para continuar. Para esta vista y por motivos de rendimiento, los filtros "Fecha de emisión" y "Estado" siempre están activos</div>
                        </div>
                    </div>
                    <div class="form-group mb-5">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" checked disabled> Fecha de registro
                                    <input type="hidden" name="chkFechaOcp" value="on">
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="date" name="fechaOcpDesde" class="form-control text-center" value="@if (session('ocpAnalisisFechaDesde')!==null){{session('ocpAnalisisFechaDesde')}}@else{{$fechaActual->addMonths(-1)->format('Y-m-d')}}@endif">
                            <small class="help-block">Desde (dd/mm/aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="date" name="fechaOcpHasta" class="form-control text-center" value="@if (session('ocpAnalisisFechaHasta')!==null){{session('ocpAnalisisFechaHasta')}}@else{{$fechaActual->addMonths(1)->format('Y-m-d')}}@endif">
                            <small class="help-block">Hasta (dd/mm/aaaa)</small>
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEmpresa" @if (session('ocpAnalisisEmpresa')!==null) checked @endif> Empresa
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEmpresa[]" class="selectpicker empresa" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                @foreach ($empresas as $empresa)
                                <option @if (session()->has('ocpAnalisisEmpresa') && in_array($empresa->id, session('ocpAnalisisEmpresa')))
                                    selected
                                    @endif
                                    value="{{ $empresa->id }}">{{ $empresa->empresa }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEntidad" @if (session('ocpAnalisisEntidad')!==null) checked @endif> Entidad
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEntidad[]" class="selectpicker entidad" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                @foreach ($entidades as $entidad)
                                <option @if (session()->has('ocpAnalisisEntidad') && in_array($entidad->id, session('ocpAnalisisEntidad')))
                                    selected
                                    @endif
                                    value="{{ $entidad->id }}">{{ $entidad->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkProveedor" @if (session('ocpAnalisisProveedor')!==null) checked @endif> Proveedor
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectProveedor[]" class="selectpicker entidad" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                @foreach ($proveedores as $proveedor)
                                <option @if (session()->has('ocpAnalisisProveedor') && in_array($proveedor->id, session('ocpAnalisisProveedor')))
                                    selected
                                    @endif
                                    value="{{ $proveedor->id }}">{{ $proveedor->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkMarca" @if (session('ocpAnalisisMarca')!==null) checked @endif> Marca
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" name="ocpMarca" class="form-control" placeholder="Escriba la marca" value="@if (session('ocpAnalisisMarca') !== null) {{ session('ocpAnalisisMarca') }} @endif">
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkModelo" @if (session('ocpAnalisisModelo')!==null) checked @endif> Modelo
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" name="ocpModelo" class="form-control" placeholder="Escriba el modelo" value="@if (session('ocpAnalisisModelo') !== null) {{ session('ocpAnalisisModelo') }} @endif">
                        </div>
                    </div>
                    
                    <div class="form-group mb-5">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkProcesador" @if (session('ocpAnalisisProcesador')!==null) checked @endif> Procesador
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" name="ocpProcesador" class="form-control" placeholder="Escriba el procesador" value="@if (session('ocpAnalisisProcesador') !== null) {{ session('ocpAnalisisProcesador') }} @endif">
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
    <script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>

    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js') }}"></script>

    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

    <script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>

    <script src='{{ asset("mgcp/js/util.js?v=27") }}'></script>
    <script src='{{ asset("mgcp/js/moment.min.js?v=1") }}'></script>

    <script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-model.js?v=11") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-view.js?v=11") }}'></script>
    <script src="{{ asset('mgcp/js/orden-compra/publica/orden-compra-publica-analisis.js?v=11') }}"></script>
    <script>
        const token = '{{ csrf_token() }}';
        let tcSbs = 1;
        let actualizar = false;
        const productoView = new ProductoView(new ProductoModel(token));
        productoView.obtenerDetallesEvent();
    </script>
@endsection
