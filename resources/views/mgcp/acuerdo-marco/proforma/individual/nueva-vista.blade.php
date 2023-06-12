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
            padding: 1px !important;
        }

        #divBodyProformas table {
            margin-bottom: 0px !important;
        }

        #divBodyProformas div.panel {
            margin-bottom: 10px !important;
        }
    </style>
@endsection

@section('cabecera')
Proformas de {{ $tipoProforma == '1' ? 'compra ordinaria' : 'gran compra' }} individual
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Proformas</li>
    <li class="active">Individual</li>
    <li class="active">{{ $tipoProforma == '1' ? 'Compra ordinaria' : 'Gran compra' }} (nueva)</li>
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
                        <span id="spanCantFiltros">0</span>
                    </button>
                    <button class="btn btn-default" data-toggle="modal" data-target="#modalUltimaActualizacionLista"><span class="glyphicon glyphicon-time" aria-hidden="true"></span> Ver última act. de lista</button>
                    @if ($tipoProforma=='1')
                    <button class="btn btn-default" data-toggle="modal" data-target="#modalIngresarFletePorLote"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span> Flete por lote...</button>
                    @endif
                    <button class="btn btn-default" data-toggle="modal" data-target="#modalProformasEnviar"><span class="fa fa-share-square-o" aria-hidden="true"></span> Enviar cotiz. a P.C.</button>
                    @if (Auth::user()->tieneRol(68))
                    <button class="btn btn-default" data-tipo="{{ $tipoProforma }}" id="btnDescargarAnalisis"><span class="fa fa-download"></span> Formato de análisis</button>
                    @endif
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
                            <div class="fom-control-static mensaje-filtros">Seleccione los filtros que desee aplicar y cierre este cuadro para continuar. Para esta vista y por motivos de rendimiento, los filtros "Fecha de emisión" y "Estado" siempre están activos</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" checked disabled> Fecha de emisión
                                    <input type="hidden" name="chkFechaEmision" value="on">
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
                            <select name="selectEmpresa[]" class="selectpicker empresa" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
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
                                    <input type="checkbox" name="chkEstado" checked> Estado
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEstado" class="form-control">
                                @foreach($estados as $estado)
                                <option value="{{$estado->estado}}" {{$estado->estado=='PENDIENTE' ? 'selected' : ''}}>{{$estado->estado}}</option>
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
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkTipoCarga"  @if (session('proformaTipoCarga') !== null) checked @endif> Tipo de carga
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectTipoCarga" class="form-control">
                                <option value="" {{ session('proformaTipoCarga') == null ? 'selected' : '' }} disabled>Elija una opción</option>
                                <option value="MANUAL" {{ session('proformaTipoCarga') == 'MANUAL' ? 'selected' : '' }}>MANUAL</option>
                                <option value="MASIVO" {{ session('proformaTipoCarga') == 'MASIVO' ? 'selected' : '' }}>MASIVO</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkMPG"  @if (session('proformaMPG') !== null) checked @endif> Mayor probabilidad de ganar
                                </label>
                            </div>
                        </label>
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
                        <tr class="{{$empresa->id}}" data-empresa="{{$empresa->id}}">
                            <td class="text-center">{{$empresa->empresa}}</td>
                            <td class="text-center valor-actual"></td>
                            <td class="text-center"><input type="text" class="form-control text-right" name="valor" placeholder="Valor"></td>
                            <td class="text-center"><textarea class="form-control" name="comentario" placeholder="Ingrese comentario"></textarea></td>
                            <td class="text-center"><button type="button" class="btn btn-default actualizar" data-empresa="{{$empresa->id}}">Actualizar</button></td>
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

<!--<div class="modal fade" id="modalDctoVolumen" tabindex="-1" role="dialog">
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
</div>-->

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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Calculadora para proforma <span class="proforma"></span></h4>
            </div>
            <div class="modal-body">
                <p><strong>Producto:</strong> <span class="producto"></span></p>
                <p><strong>Cantidad:</strong> <span class="cantidad"></span></p>
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
                                    <th style="width: 30%" class="text-center">Costo</th>
                                    <th style="width: 10%" class="text-center">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCalculadoraProducto">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><button class="btn btn-xs btn-primary" id="btnCalculadoraAgregarFila"><span class="glyphicon glyphicon-plus"></span> Fila</button></td>
                                    <td class="text-right"><strong>Costo total:</strong></td>
                                    <td class="text-right" id="tdCalculadoraCostoTotal"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <fieldset>
                    <legend>Empresas y precios a publicar</legend>
                </fieldset>
                <div class="row">
                    <div class="col-sm-12">
                        <form id="formCalculadoraPreciosPublicar">
                            @csrf
                            <table class="table table-condensed" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 15%" class="text-center">Empresa</th>
                                        <th class="text-center">Estado prof.</th>
                                        <th class="text-center">Precio unit.<br>base</th>
                                        <th style="width: 10%" class="text-center">Sel.</th>
                                        <th style="width: 15%" class="text-center">Margen ganancia<br>al costo</th>
                                        <th style="width: 10%" class="text-center">Precio a publicar USD</th>
                                        <th style="width: 10%" class="text-center">Flete a publicar PEN</th>
                                        <th style="width: 10%" class="text-center">Flete a no enviarse PEN</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyCalculadoraEmpresas">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAplicarPreciosCalculadora">Aplicar precios a la proforma</button>
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
                <p>Se ingresará el flete a las proformas:</p>
                <ul>
                    <li>Que tengan estado PENDIENTE</li>
                    <li>Que no tengan flete ingresado</li>
                    <li>Que sean parte del resultado de los filtros aplicados y el criterio de búsqueda ingresado</li>
                </ul>
                <p>El monto del flete se ingresará de forma automática, dependiendo del precio del producto</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnIngresarFletePorLote">Ingresar</button>
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

<div class="modal fade" id="modalAnalisis" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Analisis</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <div class="box box-solid last">
                    <div class="box-body">
                        <form id="formAnalisis">
                            @csrf
                            <input type="hidden" name="id_proforma_analisis" id="id_proforma_analisis" value="0">
                            <input type="hidden" name="id_proforma" id="id_proforma" value="0">
                            <input type="hidden" name="codigo_proforma" id="codigo_proforma" value="">
                            <input type="hidden" name="id_tipo_proforma" id="id_tipo_proforma" value="{{ $tipoProforma }}">
                            <input type="hidden" name="cantidad" id="cantidad" value="0">

                            <div class="row mb-4">
                                <div class="col-md-12 text-right">
                                    <label class="text-danger" id="tcSbsText"></label>
                                    <input type="hidden" name="tcSbs" id="tcSbs" value="0">
                                </div>
                            </div>
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
                                            <button type="button" class="btn btn-default btn-flat" id="btnPNext">
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
                                            <button type="button" class="btn btn-default btn-flat" id="btnCalcSolExt">
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
                                            <button type="button" class="btn btn-default btn-flat" id="btnCalcDolExt">
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
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnRegistrarAnalisis" class="btn btn-primary">Registrar</button>
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
                <p>Al deshacer la cotización, esta proforma podrá volverse a cotizar. Los precios ingresados no se eliminarán. ¿Desea continuar?</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnDeshacerCotizacion">Deshacer cotización</button>
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
@endsection

@section('scripts')
    <link href='{{asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")}}' rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

    <link href='{{ asset("assets/datatables/css/dataTables.bootstrap.min.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css") }}' rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>

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

    <script src='{{ asset("mgcp/js/acuerdo-marco/proforma/calculadora-model.js?v=26") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/proforma/calculadora-view.js?v=26") }}'></script>
    <script src='{{ asset("mgcp/js/moment.min.js?v=1") }}'></script>
    <script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
    <script src='{{ asset("mgcp/js/util.js?v=27") }}'></script>
    <script type="module">

    import CONuevaVistaModel from '{{ asset("mgcp/js/acuerdo-marco/proforma/individual/compra-ordinaria/co-nueva-vista-model.js?v=23") }}'
    import CONuevaVistaView from '{{ asset("mgcp/js/acuerdo-marco/proforma/individual/compra-ordinaria/co-nueva-vista-view.js?v=23") }}'
    import ProformaNuevaVistaModel from '{{ asset("mgcp/js/acuerdo-marco/proforma/individual/proforma-nueva-vista-model.js?v=23") }}'
    import ProformaIndividualView from '{{ asset("mgcp/js/acuerdo-marco/proforma/individual/proforma-individual-view.js?v=23") }}'

    $(document).ready(function() {
        //*****INICIALIZACION*****
        $(".sidebar-mini").addClass("sidebar-collapse");
        
        Util.seleccionarMenu(window.location);
        Util.activarSoloDecimales();
        Util.activarDatePicker();
        const token = '{{csrf_token()}}';
        const tipoProforma = '{{$tipoProforma}}';
        const ocPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
        const productoView = new ProductoView(new ProductoModel(token));
        const entidadView = new EntidadView(new EntidadModel(token));
        const descargaProformaView = new DescargaProformaView(new DescargaProformaModel(token));
        const calculadoraView = new CalculadoraView(new CalculadoraModel(token));

        let proformaNvView;
        let proformaModel;
        if (tipoProforma == '1') {
            proformaModel = new CONuevaVistaModel(token);
            proformaNvView = new CONuevaVistaView(proformaModel, "{{Auth::user()->id}}");
            proformaNvView.ingresarFletePorLoteEvent();
        } else {
            proformaModel = new ProformaNuevaVistaModel(token);
            proformaNvView = new CONuevaVistaView(proformaModel, "{{Auth::user()->id}}");
        }
        const proformaIndView = new ProformaIndividualView(proformaModel, "{{Auth::user()->id}}");
        proformaNvView.mostrarLugarEntregaEvent();
        proformaNvView.obtenerProformas();
        proformaNvView.mostrarDetallesProformaEvent();
        proformaNvView.actualizarCantidadFiltrosAplicados();
        proformaNvView.paginarResultadoEvent();
        proformaNvView.realizarBusquedaEvent();
        proformaNvView.actualizarFiltrosEvent();
        proformaNvView.gestionarComentariosEvent();
        proformaNvView.gestionarAnalisisEvent();
        proformaNvView.descargarAnalisisEvent();
        
        proformaIndView.actualizarCamposEvent();
        proformaIndView.deshacerCotizacionEvent();
        proformaIndView.enviarCotizacionesEvent();
        proformaIndView.actualizarRestringirEvent();
        proformaIndView.probabilidadGanar();

        calculadoraView.listarEvent();
        calculadoraView.calcularCostoTotalEvent();
        calculadoraView.calcularMargenGanancialEvent();
        calculadoraView.agregarFilaEvent();
        calculadoraView.eliminarFilaEvent();
        calculadoraView.actualizarCampoCostoEvent();
        calculadoraView.aplicarPreciosProformaEvent();
        calculadoraView.seleccionarFilasCalculadoraEvent();

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