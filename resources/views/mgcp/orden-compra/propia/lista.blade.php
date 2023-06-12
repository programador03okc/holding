@extends('mgcp.layouts.app')
@section('estilos')
    <link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/lobibox/dist/css/lobibox.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .sin-borde-top tr td,
        .sin-borde-top tr th {
            border-top: none !important;
        }
        #tableOportunidades td a:hover {
            cursor: pointer
        }
        /*div.modal legend {
            font-size: 16px;
            font-weight: bold;
        }*/
        .arriba {
            margin-bottom: 3px;
        }
        ::placeholder {
            color: black;
            opacity: 0.4;
        }
        #modalDetallesPc table td {
            font-size: small !important;
        }
        input.upper {
            text-transform: uppercase;
        }
    </style>
@endsection

@section('contenido')
@php
    use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
@endphp
@section('cabecera') Lista de órdenes de compra propias @endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">O/C propias</li>
    <li class="active">Lista</li>
</ol>
@endsection

@section('cuerpo')

@include('mgcp.partials.acuerdo-marco.entidad.detalles',['seleccionarContacto' => true])

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableOrdenes" class="table table-striped table-hover table-condensed" style="font-size: 0.8em; width: 100%">
                <thead>
                    <tr>
                        <th class="text-center">N°</th>
                        <th class="text-center">AM</th>
                        <th style="width:15%" class="text-center">Entidad</th>
                        <th class="text-center">Fecha<br>publicación</th>
                        <th class="text-center">Estado O/C</th>
                        <th class="text-center">Fecha<br>estado</th>
                        <th class="text-center">Estado entrega</th>
                        <th class="text-center">Inicio/fin<br>entrega</th>
                        <th class="text-center">Monto total</th>
                        <th style="width:5%" class="text-center">O.C. (fís.)/<br>SIAF</th>
                        <!--<th width="5%" class="text-center">Cód. gasto /<br>Factura</th>-->
                        <th style="width:6%" class="text-center">Factura /<br>OCC(Softlink)</th>
                        <th class="text-center">Guía /<br>Fecha guía</th>
                        <th title="Etapa adquisición" class="text-center">Etapa adq.</th>
                        <th class="text-center">Responsable</th>
                        <th style="width:9%" class="text-center">CP, estado y F.aprob.</th>
                        <th style="width:5%" class="text-center">Acciones</th>
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
                                <label title="Fecha de publicación">
                                    <input type="checkbox" name="chkFechaPublicacion" @if (session('ocFiltroFechaPublicacionDesde')!==null) checked @endif> Fecha publicación
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaPublicacionDesde" class="form-control date-picker" value="@if (session('ocFiltroFechaPublicacionDesde')!==null){{session('ocFiltroFechaPublicacionDesde')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaPublicacionHasta" class="form-control date-picker" value="@if (session('ocFiltroFechaPublicacionHasta')!==null){{session('ocFiltroFechaPublicacionHasta')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label title="Fecha estado">
                                    <input type="checkbox" name="chkFechaEstado" @if (session('ocFiltroFechaEstadoDesde')!==null) checked @endif> Fecha estado
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEstadoDesde" class="form-control date-picker" value="@if (session('ocFiltroFechaEstadoDesde')!==null){{session('ocFiltroFechaEstadoDesde')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEstadoHasta" class="form-control date-picker" value="@if (session('ocFiltroFechaEstadoHasta')!==null){{session('ocFiltroFechaEstadoHasta')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label title="Fecha entrega">
                                    <input type="checkbox" name="chkFechaEntrega" @if (session('ocFiltroFechaEntregaDesde')!==null) checked @endif> Fecha entrega
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEntregaDesde" class="form-control date-picker" value="@if (session('ocFiltroFechaEntregaDesde')!==null){{session('ocFiltroFechaEntregaDesde')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEntregaHasta" class="form-control date-picker" value="@if (session('ocFiltroFechaEntregaHasta')!==null){{session('ocFiltroFechaEntregaHasta')}}@else{{date('d-m-Y')}}@endif">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEmpresa" @if (session('ocFiltroEmpresa')!==null) checked @endif> Empresas
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEmpresa[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="5">
                                @foreach ($empresas as $empresa)
                                <option @if (session()->has('ocFiltroEmpresa'))
                                    @if (in_array($empresa->id,session('ocFiltroEmpresa'))) selected @endif @endif
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
                                    <input type="checkbox" name="chkMarca" @if (session('ocFiltroMarca')!==null) checked @endif> Marcas
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectMarca[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="5">
                                @foreach ($marcas as $marca)
                                <option @if (session()->has('ocFiltroMarca'))
                                    @if (in_array($marca->marca,session('ocFiltroMarca'))) selected @endif @endif
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
                                    <input type="checkbox" name="chkEntidad" @if (session('ocFiltroEntidad')!==null) checked @endif> Entidad
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEntidad" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="false" data-size="10">
                                @foreach ($entidades as $entidad)
                                <option value="{{$entidad->id}}" @if (session('ocFiltroEntidad')==$entidad->id) selected @endif>{{$entidad->nombre}}</option>
                                @endforeach
                            </select>
                            <small style="margin-bottom:0px" class="help-block">Sólo se muestran las entidades que emitieron O/C a las empresas</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstadoOc" @if (session('ocFiltroEstadoOc')!==null) checked @endif> Estado O/C
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="estadoOc[]" class="selectpicker" data-live-search="true" data-width="100%" multiple data-size="5">
                                @foreach ($estadosOc as $estado)
                                <option @if (session()->has('ocFiltroEstadoOc'))
                                    @if (in_array($estado->estado_oc,session('ocFiltroEstadoOc'))) selected @endif @endif
                                    value="{{$estado->estado_oc}}">{{$estado->estado_oc}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstadoCuadro" @if (session('ocFiltroEstadoCuadro') !== null) checked @endif> Estado cuadro
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="estadoCuadro">
                                @foreach ($estadosCuadro as $estado)
                                <option value="{{$estado->id}}" @if (session('ocFiltroEstadoCuadro') == $estado->id) selected @endif>{{$estado->estado}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstadoEntrega" @if (session('ocFiltroEstadoEntrega') !== null) checked @endif> Estado entrega
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="estadoEntrega">
                                @foreach ($estadosEntrega as $estado)
                                <option value="{{$estado->estado_entrega}}" @if (session('ocFiltroEstadoEntrega') == $estado->estado_entrega) selected @endif>{{$estado->estado_entrega}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkAm" @if (session('ocFiltroAm') !== null) checked @endif> Acuerdo marco
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="acuedoMarco[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                @foreach ($acuerdos as $acuerdo)
                                <option @if (session()->has('ocFiltroAm'))
                                    @if (in_array($acuerdo->id,session('ocFiltroAm'))) selected @endif @endif
                                    value="{{$acuerdo->id}}">{{$acuerdo->descripcion}} - {{$acuerdo->descripcion_larga}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkTipo" @if (session('ocFiltroTipo')!==null) checked @endif> Tipo de O/C
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="tipoOc">
                                <option value="0" @if (session('ocFiltroTipo')==0) selected @endif>Directa</option>
                                <option value="1" @if (session('ocFiltroTipo')==1) selected @endif>Compra ordinaria</option>
                                <option value="2" @if (session('ocFiltroTipo')==2) selected @endif>Gran compra</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label title="Etapa adquisición">
                                    <input type="checkbox" name="chkEtapaAdq" @if (session('ocFiltroEtapaAdq')!==null) checked @endif> Etapa adq.
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="etapaAdq">
                                @foreach ($etapas as $etapa)
                                <option value="{{$etapa->id}}" @if (session('ocFiltroEtapaAdq')==$etapa->id) selected @endif>{{$etapa->etapa}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (Auth::user()->tieneRol(48))
                    <div class="form-group">
                        <label class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" disabled checked> Sólo ve órdenes donde es responsable
                                </label>
                            </div>
                        </label>
                    </div>
                    @else
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkCorporativo" @if (session('ocFiltroCorporativo')!==null) checked @endif> Corporativo
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="corporativo">
                                @foreach ($corporativos as $corporativo)
                                <option value="{{$corporativo->id}}" @if (session('ocFiltroCorporativo')==$corporativo->id) selected @endif>{{$corporativo->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFlagEstado" @if (session('ocFiltroFlagEstado')!==null) checked @endif> Flag de estados
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="flagEstado">
                                @foreach ($flags as $flag)
                                <option value="{{$flag->color}}" @if (session('ocFiltroFlagEstado') == $flag->color) selected @endif>{{$flag->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkSolAprob24h" {{session('ocFiltroSolAprob24h') != null ? "checked" : ""}}> Con sol. aprob. después de 24h
                                </label>
                            </div>
                        </div>
                    </div>
                    {{--  <div class="form-group">
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkSinCuadro" {{session('ocFiltroSinCuadro') != null ? "checked" : ""}}> Sin cuadro asignado
                                </label>
                            </div>
                        </div>
                    </div>  --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDescargarOrdenes" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Descargar O/C desde Perú Compras</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>
                <p>Seleccione las empresas que desee descargar. Al iniciar el proceso, puede cerrar esta ventana y la descarga continuará</p>
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-hover" style="width: 100%;font-size:small">
                        <thead>
                            <tr>
                                <!--<th class="text-center" style="width: 5%">N°</th>-->
                                <th style="width: 15%" class="text-center">Empresa</th>
                                <th style="width: 35%" class="text-center">Última descarga</th>
                                <th style="width: 15%" class="text-center">Seleccionar<br><input type="checkbox" id="chkSeleccionarTodo" checked></th>
                                <th style="width: 35%" class="text-center">Progreso</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDescargarOc">
                            @foreach ($empresas as $empresa)
                            <tr>
                                <td class="empresa">{{$empresa->empresa}}</td>
                                <td class="text-center fecha"></td>
                                <td class="text-center">
                                    <input type="checkbox" checked>
                                    @php
                                    $catalogos = Catalogo::obtenerCatalogosPorEmpresa($empresa->id);
                                    foreach ($catalogos as $catalogo) {
                                        echo '<input type="hidden" class="pendiente" data-empresa="' . $empresa->id . '" data-catalogo="' . $catalogo->id . '" value="">';
                                    }
                                    @endphp
                                </td>
                                <td class="resultado text-center"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mensaje-final"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnDescargarOrdenes">Descargar</button>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{route('mgcp.ordenes-compra.propias.exportar-lista')}}" target="_blank">
    <div class="modal fade" id="modalExportarLista" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Exportar lista</h4>
                </div>
                <div class="modal-body">
                    @csrf
                    <p>Se exportará la lista de órdenes de acuerdo a los filtros aplicados (no se toma en cuenta el criterio de búsqueda ingresado). Considere actualizar la lista (con la opción Descargar O/C desde Perú Compras) antes de exportarla</p>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="incluirProductos" value="1"> Incluir lista de productos vendidos de OCAM
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" id="btnExportarLista">Exportar</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="modalEstadosOcPortal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Estados de <span class="orden-compra"></span></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetallesPc" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detalles de Orden de Compra</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
                <div class="box box-solid @if (!Auth::user()->tieneRol(36)) last @endif">
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
                @if (Auth::user()->tieneRol(36))
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
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                @if (Auth::user()->tieneRol(36))
                <button type="button" id="btnRegistrarComentario" class="btn btn-primary">Registrar</button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTransportes" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Transportes para</h4>
            </div>
            <div class="modal-body">

                <div class="box box-solid @if (!Auth::user()->tieneRol(36)) last @endif">
                    <div class="box-body">

                        <table class="table table-condensed" style="font-size:small">
                            <thead>
                                <tr>
                                    <th class="text-center">Fecha despacho</th>
                                    <th class="text-center">Transportista</th>
                                    <th class="text-center">Nro. guía</th>
                                    <th style="width: 10%" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTransportes">
                            </tbody>
                        </table>

                    </div>
                </div>

                @if (Auth::user()->tieneRol(36))
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Nuevo transporte</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div>Fecha despacho</div>
                                    <input id="txtTransporteFecha" type="text" class="form-control date-picker" placeholder="dd-mm-aaaa">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div>Transportista</div>
                                    <div id="divTransportistas" style="margin-bottom: 5px">

                                    </div>
                                    <a id="aNuevoTransportista" href="#">Nuevo transportista</a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div>Nro. guía</div>
                                    <input id="txtTransporteNroGuia" type="text" class="form-control" placeholder="Nro. guía">
                                </div>
                            </div>
                            <div class="col-md-2" style="padding-left: 5px">
                                <div class="form-group">
                                    <div style="visibility: hidden">Nro. guía</div>
                                    <button type="button" id="btnNuevoTransporte" class="btn btn-primary">Registrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalActualizarDespacho" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{Auth::user()->tieneRol(51) ? 'Actualizar' : 'Ver'}} despacho</h4>
            </div>
            <div class="modal-body">
                <p>Orden: <span class="orden"></span></p>
                <form id="formActualizarDespacho">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="tipo">
                    <div class="radio" style="margin-bottom: 1.5em">
                        <label>
                            <input type="radio" name="despachada" value="0">
                            No despachada
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="despachada" value="1">
                            Despachada
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Transportista</label>
                        <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" data-size="5" name="transportista">
                            <option value="0">No seleccionado</option>
                            @foreach ($transportistas as $transportista)
                            <option value="{{$transportista->id_contribuyente}}">{{$transportista->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Flete real</label>
                        <div class="input-group">
                            <span class="input-group-addon">S/</span>
                            <input type="text" class="form-control decimal" name="fleteReal" placeholder="Flete real">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Fecha de salida</label>
                        <input type="text" class="form-control date-picker" name="fechaSalida" placeholder="Fecha de salida">
                    </div>
                    <div class="form-group">
                        <label>Fecha de llegada al cliente</label>
                        <input type="text" class="form-control date-picker" name="fechaLlegada" placeholder="Fecha de llegada">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                @if (Auth::user()->tieneRol(51))
                <button type="button" id="btnActualizarDespacho" class="btn btn-primary">Actualizar</button>
                @endif
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalInformacionAdicional" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Información adicional de <span class="orden-compra"></span></h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Lugar de entrega</label>
                        <div class="col-sm-8">
                            <div class="form-control-static lugar-entrega limpiar"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Archivos</label>
                        <div class="col-sm-8">
                            <div class="form-control-static archivos limpiar"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCambiarContacto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cambiar contacto</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableContactosEntidadSeleccionar" class="table table-condensed table-hover table-striped" style="font-size: small">
                        <thead>
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Teléfono</th>
                                <th class="text-center">Cargo</th>
                                <th class="text-center">Correo</th>
                                <th class="text-center">Dirección</th>
                                <th class="text-center">Horario</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrearCuadroCosto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Crear cuadro de costos para la orden <span class="orden-compra"></span></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txtIdOc">
                <input type="hidden" id="txtTipoOc">
                <p>Para crear un cuadro de costo, debe vincular o crear una oportunidad para esta orden de compra</p>
                <br>
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tabVincular">Vincular oportunidad</a></li>
                    <li><a data-toggle="tab" href="#tabCrear">Crear oportunidad</a></li>
                </ul>

                <div class="tab-content">
                    <div id="tabVincular" class="tab-pane fade in active">
                        <br>
                        <!--<div>Seleccione  la oportunidad a vincular con la O/C.</div>-->
                        <table id="tableOportunidades" class="table table-hover table-striped table-condensed" style="width: 100%; font-size: x-small ;">
                            <thead>
                                <tr>
                                    <th class="text-center">Entidad</th>
                                    <th class="text-center">Oportunidad</th>
                                    <th class="text-center">Importe</th>
                                    <th class="text-center">Fecha<br>creación</th>
                                    <th class="text-center">Fecha<br>límite</th>
                                    <th class="text-center">Resp.</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Grupo</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div id="tabCrear" class="tab-pane fade">
                        <br>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="col-sm-3 control-label">Descripción</div>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="txtOportunidadDescripcion" name="descripcion" placeholder="Descripción de oportunidad"></textarea>
                                </div>
                            </div>
                            @if (Auth::user()->tieneRol(4))
                            <div class="form-group">
                                <div class="col-sm-3 control-label">Responsable</div>
                                <div class="col-sm-8">
                                    <select class="form-control" id="selectOportunidadResponsable">
                                        @foreach ($corporativos as $corporativo)
                                        <option value="{{$corporativo->id}}">{{$corporativo->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block">El resto de campos se podrá editar después de crearse la oportunidad<br>
                                        El responsable de la oportunidad será responsable de esta orden de compra
                                    </span>
                                </div>
                            </div>
                            @endif
                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-3">
                                    <button class="btn btn-primary" id="btnCrearOportunidadDesdeOc">Crear oportunidad</button>
                                </div>
                            </div>
                            </form>
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

<div class="hidden">
    <select id="selectEtapas">
        @foreach ($etapas as $etapa)
        <option value="{{$etapa->id}}">{{$etapa->etapa}}</option>
        @endforeach
    </select>
    <select id="selectCorporativos">
        <option value="0">No asignado</option>
        @foreach ($corporativos as $corporativo)
        <option value="{{$corporativo->id}}">{{$corporativo->nombre_corto}}</option>
        @endforeach
    </select>
    <select id="selectUsuarios">
        @foreach ($usuarios as $usuario)
        <option value="{{$usuario->id}}">{{$usuario->nombre_corto}}</option>
        @endforeach
    </select>

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
    <script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=13") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=13") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/entidad/contacto-model.js?v=13") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/entidad/contacto-view.js?v=14") }}'></script>
    <script src='{{ asset("mgcp/js/orden-compra/propia/orden-compra-propia-view.js") }}?v={{ filemtime(public_path("mgcp/js/orden-compra/propia/orden-compra-propia-view.js")) }}'></script>
    <script src='{{ asset("mgcp/js/orden-compra/propia/orden-compra-propia-model.js?v=20") }}'></script>
    <script src='{{ asset("mgcp/js/orden-compra/propia/comentario-oc-view.js?v=12") }}'></script>
    <script src='{{ asset("mgcp/js/orden-compra/propia/comentario-oc-model.js?v=12") }}'></script>
    <script src='{{ asset("mgcp/js/orden-compra/publica/orden-compra-publica-view.js?v=11") }}'></script>
    <script src='{{ asset("mgcp/js/orden-compra/publica/orden-compra-publica-model.js?v=11") }}'></script>
    <script src='{{ asset("mgcp/js/oportunidad/oportunidad-model.js?v=11") }}'></script>
    <script src='{{ asset("mgcp/js/oportunidad/oportunidad-view.js?v=11") }}'></script>

    <script src='{{ asset("mgcp/js/orden-compra/propia/despacho-view.js?v=10") }}'></script>
    <script src='{{ asset("mgcp/js/orden-compra/propia/despacho-model.js?v=10") }}'></script>
    <script>
        $(document).ready(function() {
            //Util.activarDatePicker();
            Util.seleccionarMenu(window.location);
            Util.activarSoloDecimales();
            $(".sidebar-mini").addClass("sidebar-collapse");
            
            //var contenido = '';
            const permisos = {
                editarGuiaFecha: "{{Auth::user()->tieneRol(32)}}",
                editarCodGastoFactura: "{{Auth::user()->tieneRol(33)}}",
                editarEtapaAdq: "{{Auth::user()->tieneRol(34)}}",
                editarCobrado: "{{Auth::user()->tieneRol(35)}}",
                editarOtros: "{{Auth::user()->tieneRol(47)}}",
                crearCuadro: "{{Auth::user()->tieneRol(50)}}",
            };

            const token = '{{csrf_token()}}';
            const entidadView = new EntidadView(new EntidadModel(token));
            const contactoView = new ContactoEntidadView(new ContactoEntidadModel(token), '{{Auth::user()->tieneRol(60)}}', true);
            const comentarioView = new ComentarioOcView(new ComentarioOcModel(token));
            const ordenCompraPropiaView = new OrdenCompraPropiaView(new OrdenCompraPropiaModel(token), permisos, '{{Auth::user()->id}}');
            const ordenCompraPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
            const oportunidadView = new OportunidadView(new OportunidadModel(token));
            const despachoView = new DespachoView(new DespachoModel(token), '{{Auth::user()->tieneRol(51)}}');

            entidadView.obtenerDetallesEvent();
            comentarioView.listarComentariosEvent();
            comentarioView.registrarComentarioEvent();
            ordenCompraPublicaView.obtenerEstadosPortalEvent();
            ordenCompraPropiaView.actualizarCamposEvent();
            ordenCompraPropiaView.crearCuadroCostoEvent();
            ordenCompraPropiaView.descargarDesdePortalEvent();
            oportunidadView.listarParaOc();
            oportunidadView.crearOportunidadDesdeOcEvent(ordenCompraPropiaView.rutaCuadroCosto);
            ordenCompraPropiaView.listar();
            ordenCompraPropiaView.informacionAdicionalEvent();
            ordenCompraPropiaView.cambiarContactoEvent();
            ordenCompraPropiaView.verProductosEvent();
            despachoView.obtenerDetallesEvent();
            despachoView.actualizarDespachoEvent();
            Util.activarFiltros('#tableOrdenes', ordenCompraPropiaView.model);
        });
    </script>
@endsection