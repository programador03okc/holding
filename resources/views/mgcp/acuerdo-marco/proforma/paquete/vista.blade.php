@extends('mgcp.layouts.app')
@section('estilos')
    <style>
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

        table.requerimiento th {
            border-bottom: none !important;
        }

        #divBodyProformas div.panel-primary>div.panel-heading {
            background-color: #3c8dbc !important;
            border-color: #3c8dbc !important;
        }

        table.requerimiento thead a {
            color: white;
        }

        /*Contenido de proformas*/
        #divBodyProformas div.panel-heading {
            padding: 2px 2px;
        }

        #divBodyProformas div.panel-body {
            padding: 0px;
        }

        #divBodyProformas td {
            padding: 0px 10px !important;
        }

        #divBodyProformas table {
            margin-bottom: 0px !important;
        }

        #divBodyProformas div.panel {
            margin-bottom: 15px !important;
        }

        .separador-left {
            /*background-color: #ebf5fa;*/
            border-left: 2px solid #bebebeb4 !important;
        }

        .separador-right {
            border-right: 2px solid #bebebeb4 !important;
        }

        .separador-top {
            border-top: 2px solid #bebebeb4;
        }

        .text-small {
            font-size: 0.875em;
        }
        .fondo-plomo {
            background-color: #f9f9f9;
        }
        
    </style>
@endsection

@section('cabecera')
Proformas de {{$tipoProforma=='1' ? 'compra ordinaria' : 'gran compra'}} por paquete
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{ route('mgcp.home') }}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Proformas</li>
    <li class="active">Paquete</li>
    <li class="active">{{$tipoProforma=='1' ? 'Compra ordinaria' : 'Gran compra'}}</li>
</ol>
@endsection

@section('cuerpo')

@include('mgcp.partials.acuerdo-marco.orden-compra.publica.ofertas-por-producto')
@include('mgcp.partials.acuerdo-marco.entidad.detalles')
@include('mgcp.partials.acuerdo-marco.producto.detalles')

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Controles</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-inline text-center">
                    <button data-toggle="modal" data-target="#modalFiltros" class="btn btn-default"><span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros:
                        <span id="spanCantFiltros">0</span></button>
                    <button class="btn btn-default" data-toggle="modal" data-target="#modalUltimaActualizacionLista"><span class="glyphicon glyphicon-time" aria-hidden="true"></span> Ver última act. de lista</button>
                    <!--<button class="btn btn-default" data-toggle="modal" data-target="#modalIngresarFletePorLote"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span> Flete por lote...</button>-->
                    <button class="btn btn-default" data-toggle="modal" data-target="#modalProformasEnviar"><span class="fa fa-share-square-o" aria-hidden="true"></span> Enviar cotiz. a P.C.</button>
                    <div class="input-group">
                        <input type="text" id="txtCriterio" class="form-control" placeholder="Buscar...">
                        <span class="input-group-btn">
                            <button id="btnBuscar" class="btn btn-primary" type="button"><span class="glyphicon glyphicon-search"></span></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid" id="divContenedorProformas">
    <div class="box-header with-border">
        <h3 class="box-title">Proformas</h3>
    </div>
    <div class="box-body" id="divBodyProformas">
    </div>
    <div class="box-footer text-center" id="divFooterProformas">
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
                    <input type="hidden" name="tipoProforma" value="{{$tipoProforma}}">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="fom-control-static mensaje-filtros">Seleccione los filtros que desee aplicar y
                                cierre este cuadro para continuar</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="form-control-static" style="font-weight: normal !important">
                                Fecha de emisión
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEmisionDesde" class="form-control date-picker actualizar" value="@if (session('proformaFechaEmisionDesde') !==null) {{ session('proformaFechaEmisionDesde') }}@else{{ $fechaActual->addMonths(-1)->format('d-m-Y') }} @endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEmisionHasta" class="form-control date-picker actualizar" value="@if (session('proformaFechaEmisionHasta') !==null) {{ session('proformaFechaEmisionHasta') }}@else{{ $fechaActual->addMonths(1)->format('d-m-Y') }} @endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFechaLimite" @if (session("proformaFechaLimiteDesde") !==null) checked @endif> Fecha límite
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaLimiteDesde" class="form-control date-picker" value="@if (session('proformaFechaLimiteDesde') !==null) {{ session('proformaFechaLimiteDesde') }} @else{{ date('d-m-Y') }} @endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaLimiteHasta" class="form-control date-picker" value="@if (session('proformaFechaLimiteHasta') !==null) {{ session('proformaFechaLimiteHasta') }} @else{{ date('d-m-Y') }} @endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstado" checked> Estado
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEstado" class="form-control">
                                @foreach ($estados as $estado)
                                <option value="{{ $estado->estado }}" {{ $estado->estado == 'PENDIENTE' ? 'selected' : '' }}>
                                    {{ $estado->estado }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <input type="hidden" id="txtCriterioHidden" name="criterio">
                    <input type="hidden" id="txtNroPaginaHidden" name="pagina" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalActualizarDataPortal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Actualizar <span class="tipo"></span> en Perú Compras</h4>
            </div>
            <div class="modal-body">
                <p>
                    <strong>Producto: </strong><span class="producto"></span>
                </p>
                <table class="table">
                    <thead>
                        <tr>
                            <th width="15%" class="text-center">Empresa</th>
                            <th width="15%" class="text-center">Valor actual</th>
                            <th width="15%" class="text-center">Nuevo valor</th>
                            <th width="25%" class="text-center">Comentario</th>
                            <th width="15%" class="text-center">Operaciones</th>
                            <th width="15%" class="text-center">Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($empresas as $empresa)
                        <tr class="{{ $empresa->id }}" data-empresa="{{ $empresa->id }}">
                            <td class="text-center">{{ $empresa->empresa }}</td>
                            <td class="text-center valor-actual"></td>
                            <td class="text-center"><input type="text" class="form-control text-right" name="valor" placeholder="Valor"></td>
                            <td class="text-center"><textarea class="form-control" name="comentario" placeholder="Ingrese comentario"></textarea></td>
                            <td class="text-center"><button type="button" class="btn btn-default actualizar" data-empresa="{{ $empresa->id }}">Actualizar</button></td>
                            <td class="text-center resultado"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="help-block"><strong>Nota:</strong> Los cambios pueden demorar hasta el día siguiente en hacer
                    efecto.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalActualizarListaPortal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar cotizaciones a Perú Compras</h4>
            </div>
            <div class="modal-body">
                <table class="table table-condensed table-hover table-striped" id="tableActualizarPortal" style="font-size: x-small ;">
                    <thead>
                        <tr>
                            <th class="text-center">Proforma</th>
                            <th class="text-center">Producto</th>
                            <th class="text-center">Part N°</th>
                            <th class="text-center">Empresa</th>
                            <th class="text-center">Lugar entrega</th>
                            <th class="text-center">Fecha límite</th>
                            <th class="text-center">Última edición</th>
                            <th class="text-center">Precio publicar</th>
                            <th class="text-center">Flete publicar</th>
                            <th class="text-center">Selec.</th>
                            <th width="12%" class="text-center">Resultado</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyActualizarPortal">
                    </tbody>
                </table>
                <div id="divActualizarPortalMensaje">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="btnActualizarPortalCerrar">Cerrar</button>
                <button type="text" class="btn btn-primary" id="btnActualizarPortalAceptar">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLugarEntrega" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Lugar de entrega para requerimiento <span class="requerimiento"></span></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorialActualizaciones" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Historial de actualizaciones</h4>
            </div>
            <div class="modal-body">
                <p>
                    <strong>Producto: </strong><span class="producto"></span>
                </p>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Usuario</th>
                            <th class="text-center">Empresa</th>
                            <th class="text-center">Detalle</th>
                            <th class="text-center">Comentarios</th>
                            <th width="15%" class="text-center">Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyHistorial">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{--  <div class="modal fade" id="modalDctoVolumen" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Descuento por volumen</h4>
            </div>
            <div class="modal-body">
                <p>
                    <strong>Producto: </strong><span class="producto"></span>
                </p>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Empresa</th>
                            <th class="text-center">Compra mínima</th>
                            <th class="text-center">Compra máxima</th>
                            <th class="text-center">Precio</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDctoVolumen">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>  --}}

<div class="modal fade" id="modalEntidad" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Información de Entidad</h4>
            </div>
            <div class="modal-body">
                <div id="divEntidadMensaje" class="text-center">Obteniendo datos</div>
                <div class="form-horizontal sin-bottom" id="divInfoEntidad">
                    <div class="form-group">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><b>RUC:</b></label>
                            <div class="col-sm-9">
                                <div class="form-control-static ruc"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><b>Entidad:</b></label>
                            <div class="col-sm-9">
                                <div class="form-control-static entidad"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><b>Dirección:</b></label>
                            <div class="col-sm-9">
                                <div class="form-control-static direccion"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><b>Ubigeo:</b></label>
                            <div class="col-sm-9">
                                <div class="form-control-static ubigeo"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><b>Responsable:</b></label>
                            <div class="col-sm-9">
                                <div class="form-control-static responsable"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><b>Teléfono:</b></label>
                            <div class="col-sm-9">
                                <div class="form-control-static telefono"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><b>Cargo:</b></label>
                            <div class="col-sm-9">
                                <div class="form-control-static cargo"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><b>Correo:</b></label>
                            <div class="col-sm-9">
                                <div class="form-control-static correo"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOfertasOc" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ver ofertas en órdenes de compra públicas</h4>
            </div>
            <div class="modal-body">
                <p><strong>Producto:</strong> <span class="producto"></span></p>
                <div class="text-center" id="divMensajeOc">

                </div>
                <div id="divContenedorOc">
                    <table class="table display compact dataTable" style="font-size: x-small; width: 100%;" id="tableOrdenCompra">
                        <thead>
                            <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Proveedor</th>
                                <th class="text-center">Entidad / Lugar de entrega</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center">Costo envío</th>
                                <th class="text-center">Días entrega</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyOfertasOc">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnOfertasOcToggle">Ocultar órdenes sin fecha</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCalculadora" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Calculadora</h4>
            </div>
            <div class="modal-body">
                <p><strong>Producto:</strong> <span class="producto">COMPUTADORA DE ESCRITORIO : PROCESADOR: INTEL CORE
                        I7-8700 3.20 GHz RAM: 16 GB DDR4 2666 333 MHz
                        ALMACENAMIENTO: 2 TB HDD 7200 RPM LAN: SI WLAN: NO USB: SI VGA: SI HDMI: NO SIST. OPER: WINDOWS
                        10 PRO 64 BITS ESPAÑOL UNIDAD OPTICA: SI TECLADO:
                        SI MOUSE: SI SUITE OFIMATICA: NO G. F: 36 MESES ON-SITE UNIDAD LENOVO THINKCENTRE M920S
                        10SKS00U00 (<a target="_blank" href="https://saeusceprod01.blob.core.windows.net/contproveedor/Documentos/Productos/63828.pdf">Descargar
                            ficha</a>)</span></p>
                <p><strong>Cantidad:</strong> <span class="producto">25</span></p>
                <br>
                <fieldset>
                    <legend>Costos</legend>
                </fieldset>
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2">
                        <table class="table table-condensed" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th colspan="2" class="text-center">Concepto</th>
                                    <th style="width: 25%" class="text-center">Costo</th>
                                    <th style="width: 10%" class="text-center">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td colspan="2">Flete interno</td>
                                    <td><input type="text" class="form-control text-right input-sm" value="0"></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><input type="text" class="form-control input-sm" placeholder="Concepto" value="LENOVO 10SKS00P00"></td>
                                    <td><input type="text" class="form-control text-right input-sm" placeholder="Costo" value="1300"></td>
                                    <td class="text-center"><span class="text-danger glyphicon glyphicon-remove"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><input type="text" class="form-control input-sm" placeholder="Concepto" value="HDD 1TB"></td>
                                    <td><input type="text" class="form-control text-right input-sm" placeholder="Costo" value="-20"></td>
                                    <td class="text-center"><span class="text-danger glyphicon glyphicon-remove"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><input type="text" class="form-control input-sm" placeholder="Concepto" value="SSD 512GB"></td>
                                    <td><input type="text" class="form-control text-right input-sm" placeholder="Costo" value="50"></td>
                                    <td class="text-center"><span class="text-danger glyphicon glyphicon-remove"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><input type="text" class="form-control input-sm" placeholder="Concepto" value="WIFI"></td>
                                    <td><input type="text" class="form-control text-right input-sm" placeholder="Costo" value="20"></td>
                                    <td class="text-center"><span class="text-danger glyphicon glyphicon-remove"></span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><button class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-plus"></span> Item</button></td>
                                    <td class="text-right"><strong>Costo total:</strong></td>
                                    <td class="text-right"><strong>1,350.00</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <fieldset>
                    <legend>Empresas y precios a publicar</legend>
                </fieldset>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <table class="table table-condensed" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">Empresa</th>
                                    <th class="text-center">Precio unit.<br>base</th>
                                    <th style="width: 25%" class="text-center">Margen ganancia<br>al costo</th>
                                    <th style="width: 10%" class="text-center">Precio a publicar</th>
                                    <th style="width: 10%" class="text-center">Flete a publicar PEN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <i style="color: green" class="fa fa-circle" aria-hidden="true"></i> Proyectec
                                    </td>
                                    <td class="text-center">1,549.34</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-right" placeholder="Margen" value="10">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>

                                    <td class="text-center success" contenteditable="true">1,485.00</td>
                                    <td class="text-center success" contenteditable="true">0.00</td>
                                </tr>
                                <tr>
                                    <td>
                                        <i style="color: red" class="fa fa-circle" aria-hidden="true"></i> Smart Value
                                    </td>
                                    <td class="text-center">1,546.21</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-right" placeholder="Margen" value="10.5">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>

                                    <td class="text-center success" contenteditable="true">1,491.75</td>
                                    <td class="text-center success" contenteditable="true">0.00</td>
                                </tr>
                                <tr>
                                    <td>
                                        <i style="color: yellow" class="fa fa-circle" aria-hidden="true"></i> OK
                                        Computer
                                    </td>
                                    <td class="text-center">1,551.09</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-right" placeholder="Margen" value="11">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="text-center success" contenteditable="true">1,498.50</td>
                                    <td class="text-center success" contenteditable="true">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Aplicar precios a la
                    proforma</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
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
                    <li>Requerimiento: <span class="requerimiento"></span></li>
                    <li>Producto: <span class="producto"></span></li>
                    <li>Entidad: <span class="entidad"></span></li>
                    <li>Empresa: <span class="empresa"></span></li>
                    <li>Precio publicar: <span class="precio-publicar"></span></li>
                    <li>Flete publicar: <span class="flete-publicar"></span></li>
                </ul>
                <p>Al deshacer la cotización, esta proforma podrá volverse a cotizar. Los precios ingresados no se
                    eliminarán. ¿Desea continuar?</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnDeshacerCotizacion">Deshacer cotización</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProformasEnviar" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 80%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar cotizaciones a Perú Compras</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial">
                </div>
                <p>Seleccione las proformas cotizadas que desea enviar al portal y haga clic en el botón Enviar para
                    continuar. Al filtrar la lista, las proformas ocultas permanecen seleccionadas</p>
                <div class="table-responsive">
                    <table class="table table-condensed" style="font-size: x-small; width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 3%" class="text-center">N°</th>
                                <th style="width: 7%" class="text-center">Requerimiento</th>
                                <th class="text-center">Fecha límite
                                    <!--<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarFechaLimite">-->
                                </th>
                                <th style="width: 9%" class="text-center">Empresa
                                    <!--<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarEmpresa">-->
                                </th>
                                <th style="width: 15%" class="text-center">Lugar entrega</th>
                                <th style="width: 7%" class="text-center">Proforma</th>
                                <th style="width: 7%" class="text-center">Producto</th>
                                <th style="width: 7%" class="text-center">Part N°</th>
                                
                                <th style="width: 9%" class="text-center">Última edición
                                    <!--<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarUsuario">-->
                                </th>
                                <th class="text-center">Precio publicar</th>
                                <th class="text-center">Flete publicar</th>
                                <th class="text-center">Selec.
                                    <!--<br>
                                    <input type="checkbox" id="chkSeleccionarTodo">-->
                                </th>
                                <th width="12%" class="text-center">Estado envío</th>
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
                <a target="_blank" href="{{ route('mgcp.acuerdo-marco.descargar.proformas.index') }}" class="btn btn-default">Ir a descarga de proformas</a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src='{{ asset("assets/moment/moment.min.js") }}'></script>
<link href='{{asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")}}' rel="stylesheet" type="text/css" />
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

<link href='{{ asset("assets/datatables/css/dataTables.bootstrap.min.css") }}' rel="stylesheet" type="text/css" />
<link href='{{ asset("assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css") }}' rel="stylesheet" type="text/css" />
<script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
<script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
<script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
<script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>

<script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
<link href='{{ asset("assets/bootstrap-select/css/bootstrap-select.min.css") }}' rel="stylesheet" type="text/css" />
<script src='{{ asset("assets/bootstrap-select/js/bootstrap-select.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-select/js/i18n/defaults-es_ES.min.js") }}'></script>

<script src='{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}'></script>
<link href='{{asset("assets/lobibox/dist/css/lobibox.min.css")}}' rel="stylesheet" type="text/css" />
<script src='{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}'></script>

<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/contacto-model.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/contacto-view.js?v=11") }}'></script>

<script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-model.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/producto/producto-view.js?v=11") }}'></script>

<script src='{{ asset("mgcp/js/acuerdo-marco/descarga/proforma/descarga-proforma-view.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/descarga/proforma/descarga-proforma-model.js?v=11") }}'></script>
<script src='{{ asset("mgcp/js/orden-compra/publica/orden-compra-publica-model.js?v=20") }}'></script>
<script src='{{ asset("mgcp/js/orden-compra/publica/orden-compra-publica-view.js?v=20") }}'></script>

<script src='{{ asset("mgcp/js/acuerdo-marco/proforma/paquete/paquete-view.js?v=5") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/proforma/paquete/paquete-model.js?v=2") }}'></script>

<script src='{{ asset("mgcp/js/acuerdo-marco/proforma/calculadora-model.js?v=22") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/proforma/calculadora-view.js?v=24") }}'></script>

<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script>
    $(document).ready(function() {
        //*****INICIALIZACION*****
        Util.seleccionarMenu(window.location);
        Util.activarSoloDecimales();
        Util.activarDatePicker();

        const token='{{csrf_token()}}';
        const tipoProforma='{{$tipoProforma}}';
        const ocPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
        const productoView = new ProductoView(new ProductoModel(token));
        const entidadView = new EntidadView(new EntidadModel(token));
        const descargaProformaView = new DescargaProformaView(new DescargaProformaModel(token));
        const calculadoraView = new CalculadoraView(new CalculadoraModel(token));

        const paqueteView = new PaqueteView(new PaqueteModel(token,tipoProforma),tipoProforma,'{{Auth::user()->id}}');
        paqueteView.obtenerProformas();
        paqueteView.mostrarDetallesProformaEvent();
        paqueteView.actualizarFiltrosEvent();
        paqueteView.realizarBusquedaEvent();
        paqueteView.paginarResultadoEvent();
        paqueteView.actualizarSeleccionEvent();
        paqueteView.actualizarPrecioPublicarEvent();
        paqueteView.actualizarCostoEnvioEvent();
        paqueteView.enviarCotizacionesEvent();
        /*
        
        
        proformaView.actualizarCamposEvent();
        
        proformaView.gestionarComentariosEvent();
        proformaView.ingresarFletePorLoteEvent();
        proformaView.mostrarLugarEntregaEvent();
        proformaView.deshacerCotizacionEvent();
        proformaView.mostrarDetallesProformaEvent();
        proformaView.enviarCotizacionesEvent();
*/

        descargaProformaView.obtenerFechasUltimaDescargaEvent();
        ocPublicaView.verOfertasPorMMNEvent();
        entidadView.obtenerDetallesEvent();
        productoView.obtenerDetallesEvent();

        $('div.dropdown a').on('click', (e) => {
            e.preventDefault();
        })

    });
</script>
@endsection