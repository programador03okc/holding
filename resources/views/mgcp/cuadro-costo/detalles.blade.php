@extends('mgcp.layouts.app')

@section('estilos')
    <style>
        .panel .panel-body {
            padding: 4px !important;
        }

        #main-wrapper {
            margin: 5px !important;
        }

        table.bordered {
            margin-bottom: 0px;
        }

        table.bordered tbody td,
        table.bordered tbody tr,
        table.bordered thead th,
        table.bordered thead tr {
            border: 1px solid #ddd;
        }

        #tableTransformacionOtros td {
            border-top: none !important;
        }

        table.bordered tfoot tr,
        table.bordered tfoot td {
            border: none;
        }

        table.bordered tfoot td.bordered {
            border: 1px solid #ddd;
        }

        table td.info:hover {
            cursor: pointer;
        }

        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th,
        .table td {
            padding: 3px !important;
            vertical-align: middle !important;
        }

        div.modal table>tbody>tr>td {
            padding: 6px !important;
        }

        .sin-borde-top tr td,
        .sin-borde-top tr th {
            border-top: none !important;
        }

        td.numero-parte,
        td.marca,
        td.descripcion {
            text-transform: uppercase;
        }

        #trProveedorProducto td {
            vertical-align: top !important;
            border-top: none;
        }

        #spanNuevoProveedor {
            cursor: pointer;
            color: #337ab7;
        }

        #modalProforma table div.checkbox {
            margin-top: 3px;
            margin-bottom: 3px;
        }

        .dropdown-menu .divider {
            margin: 0px;
        }

        select.form-control {
            padding: 2px 2px !important;
        }

        #modalProveedores .box-header {
            padding: 0px;
        }

        table.table-bordered tfoot td:first-child {
            border: none !important;
        }

        select.origen-costeo {
            height: 24px !important;
        }

        caption {
            color: black;
        }

        #tableProveedores td {
            vertical-align: top !important;
        }

        div.resaltar {
            color: red;
            font-weight: bold;
        }

        #divOpcionesAdicionalesTransformacion div.checkbox {
            margin-right: 30px;
        }

        #modalTransformacion legend {
            margin-bottom: 10px;
            border-bottom: none;
        }

        td.success,
        td.danger,
        td.warning {
            text-transform: uppercase;
        }

        .monto-adjudicado-mas-igv {
            font-size: 1.2em;
        }
    </style>
@endsection

@section('cabecera')
Detalles del cuadro de presupuesto {{ $oportunidad->codigo_oportunidad }}
{{ $cuadroCosto->estado->estado == 'Inicial' ? '' : '(' . $cuadroCosto->estado->estado . ')' }}
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{ route('mgcp.home') }}">Inicio</a></li>
    <li class="active">Cuadros de presupuesto</li>
    <li><a href="{{ route('mgcp.cuadro-costos.lista') }}">Lista</a></li>
    <li class="active">Detalles</li>
</ol>
@endsection

@php
    $ordenCompra = $cuadroCosto->oportunidad->ordenCompraPropia;
    //Para evitar hacer 2 evaluaciones en el modal de información adicional
    $idCondicionCredito = $cuadroCosto->condicionCredito == null ? 0 : $cuadroCosto->condicionCredito->id;
@endphp

@section('cuerpo')
@include('mgcp.partials.acuerdo-marco.entidad.detalles',['seleccionarContacto' => true])
<div class="box box-solid">
    <div class="box-body">
        <div class="btn-group btn-group-sm" role="group">
            <button {{ $tipoEdicion == 'corporativo' && Auth::user()->tieneRol(126) ? '' : 'disabled' }} title="Tipo de cambio" data-toggle="modal" data-target="#modalTipoCambio" class="btn btn-default"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span> TC: <span id="spanTipoCambio">{{ $cuadroCosto->tipo_cambio }}</span></button>
            <!--<button {{ $tipoEdicion == 'corporativo' ? '' : 'disabled' }} data-toggle="modal" data-target="#modalIgv" class="btn btn-default"><span class="fa fa-percent"></span> IGV: <span id="spanIgv">{{ $cuadroCosto->igv }}%</span></button>-->
            <button {{ $tipoEdicion == 'corporativo' ? '' : 'disabled' }} title="Moneda" data-toggle="modal" data-target="#modalMonedaCuadro" class="btn btn-default">Subtotales: <span id="spanMonedaCuadro">{{ $cuadroCosto->moneda == 's' ? 'S/' : '$' }}</span></button>
            <button class="btn btn-default" id="btnDetallesEntidad" title="Información de entidad" data-id="{{ $oportunidad->entidad->id }}" {!! $ordenCompra !=null ? ' data-orden="' . $ordenCompra->id .
                '" data-tipo="' . $ordenCompra->tipo . '" ' : '' !!}><span class="fa fa-university"></span>
                Cliente</button>
            <button id="btnInformacionAdicional" data-toggle="modal" data-target="#modalInformacionAdicional" class="btn btn-default"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                Información adicional
            </button>
            <button data-toggle="modal" data-target="#modalResponsables" class="btn btn-default"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Responsables</button>

            <!--<button data-toggle="modal" data-target="#modalGastos" class="btn btn-default"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Gastos</button>-->

            @if ($tipoEdicion == 'corporativo' && ($cuadroCosto->estado_aprobacion == 1 || $cuadroCosto->estado_aprobacion == 5))
                <button id="btnSolicitarAprobacion" class="btn btn-default"><span class="glyphicon glyphicon-send"></span> Solicitar aprobación</button>
            @endif
            @if ($cuadroCosto->estado_aprobacion == 3 && ($oportunidad->id_responsable == Auth::user()->id || Auth::user()->tieneRol(28)))
                <button id="btnSolicitarRetiroAprobacion" class="btn btn-default"><span class="glyphicon glyphicon-send"></span> Solicitar retiro de aprobación</button>
            @endif
            @if ($cuadroCosto->estado_aprobacion == 4 && ($oportunidad->id_responsable == Auth::user()->id || Auth::user()->tieneRol(28)))
                <button id="btnSolicitarReapertura" class="btn btn-default"><span class="glyphicon glyphicon-send"></span> Solicitar reapertura de cuadro</button>
            @endif
            @if ($tipoEdicion == 'compras' && $cuadroCosto->estado_aprobacion == 3)
                <button data-toggle="modal" data-target="#modalFinalizarCuadro" class="btn btn-default">Finalizar cuadro</button>
            @endif
            @if ($ultimaSolicitud != null && $ultimaSolicitud->enviada_a == Auth::user()->id)
                @if ($cuadroCosto->estado_aprobacion == 2)
                    <button data-toggle="modal" data-target="#modalResponderSolicitud" id="btnReponderSolicitudes" class="btn btn-default">Responder solicitud</button>
                @elseif ($cuadroCosto->aprobacion_previa == 1)
                    <button data-toggle="modal" data-target="#modalResponderSolicitud" id="btnReponderSolicitudes" class="btn btn-default">Responder solicitud previa</button>
                @endif
            @endif
            @if ($ultimaSolicitud != null && $cuadroCosto->estado_aprobacion == 2)
                <a target="_blank" href="https://wa.me/{{$ultimaSolicitud->enviadaA->celular_usuario}}?text=Estimado,%20por%20favor%20aprobar%20el%20siguiente%20cuadro:%20{{route('mgcp.cuadro-costos.detalles',['id' => $oportunidad->id])}}" class="btn btn-default"><i class="fa fa-whatsapp" aria-hidden="true"></i> Enviar sol. aprob. por WhatsApp</a>
            @endif
            @php
            $cantidadSolicitudes=$cuadroCosto->cantidad_solicitudes;
            @endphp
            <button data-toggle="modal" data-target="#modalSolicitudes" id="btnListarSolicitudes" class="btn btn-default"><span class="fa fa-history"></span> Historial de solicitudes {{$cantidadSolicitudes>0 ? ('('.$cantidadSolicitudes.')') : ''}}</button>
            @if ($cuadroCosto->estado_aprobacion > 2 && Auth::user()->tieneRol(133))
            <button data-toggle="modal" data-target="#modalEnviarOrdenDespacho" class="btn btn-default">Enviar orden de despacho</button>
            @endif
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-body">
        Cliente: {{$cuadroCosto->oportunidad->entidad->nombre}}
    </div>
</div>

<div class="box box-solid" id="contenedorCcAm">
    <div class="box-header">
        <h3 class="box-title">Requerimiento de bienes para venta</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <div class="btn-group btn-group-sm" role="group" style="margin-bottom: 5px">
                @if ($tipoEdicion == 'corporativo')
                <button type="button" id="btnCcAmFila" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fila <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    @foreach ($tiposFila as $tipo)
                        <li><a class="nueva-fila" data-tipo="{{$tipo->id}}" href="#">{{$tipo->tipo}}</a></li>
                    @endforeach
                </ul>

                <!--<button id="btnCcAmFila" data-id="{{ $ccAm->id_cc }}" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fila</button>-->
                @endif
                <button id="btnCentroCosto" {{$tipoEdicion == 'corporativo' ? '' : 'disabled'}} data-id="{{ $ccAm->id_cc }}" class="btn btn-default"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Centro de costo:
                    <span class="centro-costo">
                        @php
                        if ($cuadroCosto->id_centro_costo==null) {
                            echo 'No seleccionado';
                        } else {
                            $centroCosto=$cuadroCosto->nivelCentroCosto;
                            echo $centroCosto->unidad.' - '.$centroCosto->division.(empty($centroCosto->segmento) ? '' : ' - '.$centroCosto->segmento);
                        }
                        @endphp
                    </span></button>
            </div>
            <table id="tableCcAm" class="table table-bordered table-condensed table-hover" style="width: 100%; font-size: x-small">
                <thead>
                    <tr>
                        <th width="3%" class="text-center">Tipo</th>
                        <th title="Part number" width="6%" class="text-center">Part No.</th>
                        <th width="6%" class="text-center">Marca</th>
                        <th style="width: 20%" class="text-center">Descripción del producto</th>
                        <th title="Precio de venta unitario en O/C" class="text-center">P.V.U. O/C<br>(sinIGV)
                            @if ($cuadroCosto->estado_aprobacion == 1)
                            <select id="selectMonedaPvu" data-campo="moneda_pvu" data-id="{{ $ccAm->id_cc }}">
                                <option value="s" {{ $ccAm->moneda_pvu == 's' ? 'selected' : '' }}>S/</option>
                                <option value="d" {{ $ccAm->moneda_pvu == 'd' ? 'selected' : '' }}>$</option>
                            </select>
                            @else
                            {{ $ccAm->moneda_pvu == 's' ? 'S/' : '$' }}
                            @endif
                        </th>
                        <th title="Flete en O/C" class="text-center">Flete O/C<br>(sinIGV) S/</th>
                        <th title="Cantidad" class="text-center">Cant.</th>
                        <th title="Garantía en meses" class="text-center">Garant.<br>meses</th>
                        <th style="width: 7%" class="text-center">Origen<br>costo</th>
                        <th title="Proveedor seleccionado" class="text-center">Proveedor<br>seleccionado</th>
                        <th title="Costo unitario seleccionado" class="text-center">Costo unit.<br>(sinIGV)</th>
                        <th title="Plazo de entrega del proveedor" class="text-center">Plazo<br>prov.</th>
                        <th title="Flete" class="text-center">Flete S/<br>(sinIGV)</th>
                        <th title="Fondo proveedor" class="text-center">Fondo<br>proveedor</th>
                        <th class="text-center">Costo de compra</th>
                        <th class="text-center">Costo de compra<br>en <span class="moneda">{{ $cuadroCosto->moneda == 's' ? 'soles' : 'dólares' }}</span></th>
                        <th class="text-center">Total flete<br>proveedor</th>
                        <th class="text-center">Costo compra<br>+ flete</th>
                        <th class="text-center">Monto<br>adjudic. en <span class="moneda">{{ $cuadroCosto->moneda == 's' ? 'soles' : 'dólares' }}</span></th>
                        <th class="text-center">Ganancia</th>
                        <th style="width: 8%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ccAmFilas as $fila)
                        @php
                            $pvu_oc = '';
                            $contEditableMS = '';
                            $colorFilaMS = '';
                            $nformato = 2;

                            if ($fila->pvu_oc != null) {
                                if (strpos($fila->pvu_oc, '.')) {
                                    $format_dec = explode('.', $fila->pvu_oc);
                                    $decimal = strlen($format_dec[1]);
                                    $nformato = ($decimal <= 2) ? 2 : $decimal;
                                }
                                $pvu_oc = number_format($fila->pvu_oc, $nformato);
                            }
                            if ($fila->id_tipo_fila != 4) {
                                $contEditableMS = 'contenteditable="true"';
                                $colorFilaMS = 'success';
                            }
                        @endphp
                    <tr>
                        <td>{{$fila->tipoFila->tipo_abreviado}}</td>
                        <td data-id="{{ $fila->id }}" {!! $tipoEdicion=='corporativo' && $fila->comprado == 0 && ($fila->id_tipo_fila == 1) ?
                            'class="success numero-parte text-center escape" contenteditable="true"' :
                            'class="numero-parte text-center"' !!} spellcheck="false">
                            {{ $fila->part_no }}
                        </td>
                        <td data-id="{{ $fila->id }}" {!! $tipoEdicion=='corporativo' && $fila->comprado == 0 && ($fila->id_tipo_fila == 1) ?
                            'class="success marca text-center" contenteditable="true"' : 'class="marca text-center"' !!}
                            data-campo="marca"
                            spellcheck="false">{{ $fila->marca }}</td>
                        <td data-id="{{ $fila->id }}" {!! $tipoEdicion=='corporativo' && $fila->comprado == 0 && ($fila->id_tipo_fila == 1) ?
                            'class="success descripcion" contenteditable="true"' : 'class="descripcion"' !!}
                            data-campo="descripcion"
                            spellcheck="false">{!! $fila->descripcion !!}
                        </td>
                        <td data-id="{{ $fila->id }}" {!! $tipoEdicion=='corporativo' && $fila->comprado == 0
                            ? 'class="text-right '.$colorFilaMS.' decimal0 pvu-oc" '.$contEditableMS.''
                            : 'class="text-right pvu-oc"' !!} data-campo="pvu_oc">
                            {{ $pvu_oc }}
                        </td>
                        <td data-id="{{ $fila->id }}" {!! $tipoEdicion=='corporativo' && $fila->comprado == 0
                            ? 'class="text-right '.$colorFilaMS.' decimal flete-oc" '.$contEditableMS.''
                            : 'class="text-right flete-oc"' !!} data-campo="flete_oc">
                            {{ $fila->flete_oc == null ? '' : number_format($fila->flete_oc, 2, '.', ',') }}
                        </td>
                        <td data-id="{{ $fila->id }}" {!! $tipoEdicion=='corporativo' && $fila->comprado == 0
                            ? 'class="text-center '.$colorFilaMS.' entero cantidad" '.$contEditableMS.''
                            : 'class="text-center cantidad"' !!} data-campo="cantidad">
                            {{ $fila->cantidad }}
                        </td>
                        <td data-id="{{ $fila->id }}" {!! $tipoEdicion=='corporativo' && $fila->comprado == 0 ?
                            'class="text-center '.$colorFilaMS.' entero garantia tab" '.$contEditableMS.'' :
                            'class="text-center garantia"' !!} data-campo="garantia">
                            {{ $fila->garantia }}
                        </td>
                        @if ($tipoEdicion == 'corporativo' && $fila->comprado == 0)
                        <td class="{{ $colorFilaMS }}">
                            <select data-id="{{ $fila->id }}" data-campo="id_origen_costeo" style="font-size: x-small;" class="form-control input-sm origen-costeo">
                                @foreach ($origenesCosteo as $origen)
                                <option value="{{ $origen->id }}" @if ($fila->id_origen_costeo == $origen->id) selected
                                    @endif>{{ $origen->origen }}</option>
                                @endforeach
                            </select>
                        </td>
                        @else
                        <td>
                            @foreach ($origenesCosteo as $origen)
                            @if ($fila->id_origen_costeo == $origen->id)
                            {{ $origen->origen }} @endif
                            @endforeach
                        </td>
                        @endif
                        <td data-id="{{ $fila->id }}" class="proveedor-nombre info"></td>
                        <td data-id="{{ $fila->id }}" class="proveedor-precio info text-right"></td>
                        <td data-id="{{ $fila->id }}" class="proveedor-plazo info text-center"></td>
                        <td data-id="{{ $fila->id }}" class="proveedor-flete info text-right"></td>
                        <td data-id="{{ $fila->id }}" class="proveedor-fondo info text-center"></td>
                        <td class="text-right costo-total"></td>
                        <td class="text-right costo-total-convertido"></td>
                        <td class="text-right flete-total"></td>
                        <td class="text-right costo-flete-total"></td>
                        <td class="text-right monto-adjudicado"></td>
                        <td class="text-right ganancia"></td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                @if ($fila->id_tipo_fila==1)
                                <button data-id="{{ $fila->id }}" title="Transformación" class="btn btn-xs transformacion {{ $fila->tieneTransformacion() ? 'btn-warning' : 'btn-default' }}"><span class="glyphicon glyphicon-transfer"></span></button>
                                @endif
                                <button data-id="{{ $fila->id }}" title="Comentarios" class="btn btn-xs comentarios {{ $fila->tieneComentarios() ? 'btn-info' : 'btn-default' }}"><span class="glyphicon glyphicon-comment"></span></button>

                                @if ($tipoEdicion == 'corporativo')
                                <button {{ $fila->comprado == 0 ? '' : 'disabled' }} title="Eliminar" class="btn btn-xs eliminar" data-id="{{ $fila->id }}"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                                @endif
                                @if (in_array($cuadroCosto->estado_aprobacion,[3,4,5]) || ($cuadroCosto->estado_aprobacion == 1 && $fila->comprado))
                                <button {{ $rolCompras ? '' : 'disabled' }} data-id="{{ $fila->id }}" class="compra btn btn-xs {{ $fila->comprado == 1 ? 'btn-success' : 'btn-default' }}"><span class="glyphicon glyphicon-shopping-cart"></span></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="15" class="text-right"><strong>Subtotales:</strong></td>
                        <td class="costo-compra-convertido text-right bordered"></td>
                        <td class="flete text-right bordered"></td>
                        <td class="costo-compra-mas-flete text-right bordered"></td>
                        <td class="monto-adjudicado text-right bordered"></td>
                        <td class="ganancia-total text-right bordered"></td>
                    </tr>
                    <tr>
                        <td class="text-right" colspan="19"><strong>Bienes para servicio:</strong></td>
                        <td class="text-right bordered bienes-servicio"></td>
                    </tr>
                    <tr>
                        <td class="text-right" colspan="19"><strong>Gastos generales:</strong></td>
                        <td class="text-right bordered gastos-generales"></td>
                    </tr>
                    <?php
                    /*@foreach ($gastos as $gasto)
                    @if (in_array($gasto->id, $ccGastos) && $gasto->id_operacion == 2)
                    <tr class="{{ $gasto->id }}">
                        <td colspan="18" class="text-right"><strong>{{ $gasto->concepto }}
                                ({{ $gasto->porcentaje }}%):</strong></td>
                        <td class="text-right bordered gasto" data-afectacion="{{ $gasto->id_afectacion }}" data-porcentaje="{{ $gasto->porcentaje }}" data-desde="{{ $gasto->desde }}" data-hasta="{{ $gasto->hasta }}"></td>
                    </tr>
                    @endif
                    @endforeach*/
                    ?>
                    <tr>
                        <td colspan="19" class="text-right"><strong>Ganancia real:</strong></td>
                        <td class="text-right bordered ganancia-real"></td>
                    </tr>
                    <tr>
                        <td colspan="19" class="text-right"><strong>Margen ganancia:</strong></td>
                        <td class="text-right bordered margen-ganancia"></td>
                    </tr>
                    <tr>
                        <td colspan="19" class="text-right"><strong>Monto adjudicado inc. IGV:</strong></td>
                        <td class="text-right bordered monto-adjudicado-mas-igv"></td>
                    </tr>
                    <tr>
                        <td colspan="19" class="text-right"><strong>Condición de crédito:</strong></td>
                        <td class="text-right bordered condicion-credito"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header">
        <h3 class="box-title">Requerimiento de bienes para servicio</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            @if ($tipoEdicion == 'corporativo')
            <div class="btn-group btn-group-sm" role="group" style="margin-bottom: 5px">
                <button data-id="{{ $ccBs->id_cc }}" id="btnCcBsFila" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fila</button>
            </div>
            @endif
            <table id="tableCcBs" class="table table-bordered table-condensed table-hover" style="width: 100%; font-size: x-small;">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">Part no.</th>
                        <th width="25%" class="text-center">Descripción</th>
                        <th width="7%" class="text-center">Und.</th>
                        <th class="text-center">Cant.</th>
                        <th placeholder="Proveedor seleccionado" class="text-center">Proveedor<br>seleccionado</th>
                        <th placeholder="Costo unitario seleccionado" class="text-center">Costo unit.<br>sel.(sinIGV)
                        </th>
                        <th class="text-center">Plazo ent.<br>proveedor</th>
                        <th class="text-center">Flete S/<br>sinIGV</th>
                        <th title="Fondo proveedor" class="text-center">Fondo<br>proveedor</th>
                        <th class="text-center">Costo compra</th>
                        <th class="text-center">Costo compra<br>en <span class="moneda">{{ $cuadroCosto->moneda == 's' ? 'soles' : 'dólares' }}</span></th>
                        <th class="text-center">Total flete</th>
                        <th class="text-center">Costo compra<br>+ flete (sinIGV)</th>
                        <th width="2%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ccBsFilas as $fila)
                    <tr>
                        <td data-id="{{ $fila->id }}" {!! $tipoEdicion=='corporativo' ? 'class="success numero-parte escape" contenteditable="true"' : 'class="numero-parte" ' !!} spellcheck="false">
                            {{ $fila->part_no }}
                        </td>
                        <td data-id="{{ $fila->id }}" data-campo="descripcion" {!! $tipoEdicion=='corporativo' ? 'class="success descripcion" contenteditable="true"' : 'class="descripcion"' !!} spellcheck="false">{{ $fila->descripcion }}</td>
                        <td data-id="{{ $fila->id }}" data-campo="unidad" {!! $tipoEdicion=='corporativo' ? 'class="text-center unidad success" contenteditable="true"' : '' !!} spellcheck="false">
                            {{ $fila->unidad }}
                        </td>
                        <td data-id="{{ $fila->id }}" data-campo="cantidad" {!! $tipoEdicion=='corporativo' ? 'class="text-center success cantidad tab" contenteditable="true"' : 'class="cantidad"' !!}>
                            {{ $fila->cantidad }}
                        </td>
                        <td data-id="{{ $fila->id }}" class="proveedor-nombre info"></td>
                        <td data-id="{{ $fila->id }}" class="proveedor-precio info text-right"></td>
                        <td data-id="{{ $fila->id }}" class="proveedor-plazo info text-center"></td>
                        <td data-id="{{ $fila->id }}" class="proveedor-flete info text-right"></td>
                        <td data-id="{{ $fila->id }}" class="proveedor-fondo info text-right"></td>
                        <td class="text-right costo-total"></span></td>
                        <td class="text-right costo-total-convertido"></td>
                        <td class="text-right flete-total"></td>
                        <td class="text-right costo-flete-total"></td>
                        <td class="text-center">
                            @if ($tipoEdicion == ' corporativo')
                            <button title="Eliminar" class="btn btn-xs eliminar" data-id="{{ $fila->id }}"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                            @endif
                            @if ($cuadroCosto->estado_aprobacion == 3 || $cuadroCosto->estado_aprobacion == 4)
                            <button {{ $tipoEdicion == 'compras' ? '' : 'disabled' }} data-id="{{ $fila->id }}" data-campo="comprado" class="compra btn btn-xs {{ $fila->comprado == 1 ? 'btn-success' : 'btn-default' }}"><span class="glyphicon glyphicon-shopping-cart"></span></button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" class="text-right"><strong>Subtotales:</strong></td>
                        <td class="text-right bordered costo-compra-convertido"></td>
                        <td class="text-right bordered flete"></td>
                        <td class="text-right bordered costo-compra-mas-flete"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{--  <div class="box box-solid">
    <div class="box-header">
        <h3 class="box-title">Gastos generales</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            @if ($tipoEdicion == 'corporativo')
            <div class="btn-group btn-group-sm" role="group" style="margin-bottom: 5px">
                <button id="btnCcGgFila" data-id="{{ $ccGg->id_cc }}" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Fila</button>
            </div>
            @endif
            <table id="tableCcGg" class="table table-bordered table-condensed table-hover" style="width: 100%; font-size: x-small ;">
                <thead>
                    <tr>
                        <th width="25%" class="text-center">Descripción</th>
                        <th class="text-center">Cat. gasto</th>
                        <th class="text-center">Und.</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">% Partic.</th>
                        <th class="text-center">Días</th>
                        <th class="text-center">Costo<br>diario S/</th>
                        <th class="text-center">Subtotal</th>
                        <th class="text-center">Otros</th>
                        <th class="text-center">Total</th>
                        <th width="2%" class="text-center">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ccGgFilas as $fila)
                    <tr>
                        <td data-id="{{ $fila->id }}" data-campo="descripcion" {!! $tipoEdicion=='corporativo' ? 'class="success escape" contenteditable="true"' : '' !!} spellcheck="false">
                            {{ $fila->descripcion }}
                        </td>
                        @if ($tipoEdicion == 'corporativo')
                        <td class="success">
                            <select data-id="{{ $fila->id }}" data-campo="id_categoria_gasto" style="font-size: x-small;" class="form-control input-sm categoria-gasto">
                                @foreach ($categoriasGasto as $cat)
                                <option value="{{ $cat->id }}" @if ($fila->id_categoria_gasto == $cat->id) selected
                                    @endif>{{ $cat->categoria }}</option>
                                @endforeach
                            </select>
                        </td>
                        @else
                        <td>
                            @foreach ($categoriasGasto as $cat)
                            @if ($fila->id_categoria_gasto == $cat->id)
                            {{ $cat->categoria }} @endif
                            @endforeach
                        </td>
                        @endif
                        <td data-id="{{ $fila->id }}" data-campo="unidad" {!! $tipoEdicion=='corporativo' ? 'class="success text-center" contenteditable="true"' : 'class="text-center"' !!}>
                            {{ $fila->unidad }}
                        </td>
                        <td data-id="{{ $fila->id }}" data-campo="personas" {!! $tipoEdicion=='corporativo' ? 'class="success text-center entero personas" contenteditable="true"' : 'class="text-center"' !!}>
                            {{ $fila->personas }}
                        </td>
                        <td data-id="{{ $fila->id }}" data-campo="porcentaje_participacion" {!! $tipoEdicion=='corporativo' ? 'class="success text-center entero participacion" contenteditable="true"' : 'class="text-center"' !!}>{{ $fila->porcentaje_participacion }}</td>
                        <td data-id="{{ $fila->id }}" data-campo="tiempo" {!! $tipoEdicion=='corporativo' ? 'class="success text-center decimal tiempo" contenteditable="true"' : 'class="text-center"' !!}>
                            {{ number_format($fila->tiempo, 2, '.', ',') }}
                        </td>
                        <td data-id="{{ $fila->id }}" data-campo="costo" {!! $tipoEdicion=='corporativo' ? 'class="success text-right tab decimal costo" contenteditable="true"' : 'class="text-right"' !!}>
                            {{ number_format($fila->costo, 2, '.', ',') }}
                        </td>
                        <td class="text-right subtotal"></td>
                        <td class="text-right otros"></td>
                        <td class="text-right total"></td>
                        <td class="text-center">
                            @if ($tipoEdicion == 'corporativo')
                            <button data-id="{{ $fila->id }}" class="btn btn-xs eliminar"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right" colspan="7"><strong>Subtotales:</strong></td>
                        <td class="text-right bordered subtotal"></td>
                        <td class="text-right bordered otros"></td>
                        <td class="text-right bordered total"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>  --}}

<div class="modal fade" id="modalCambiarCuadro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cambiar cuadro</h4>
            </div>
            <div class="modal-body">
                <div class="radio">
                    <label>
                        <input type="radio" name="cuadro" data-id="{{ $cuadroCosto->id }}" data-campo="tipo_cuadro" value="0" @if ($cuadroCosto->tipo_cuadro == 0) checked @endif>
                        Bienes para venta
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="cuadro" data-id="{{ $cuadroCosto->id }}" data-campo="tipo_cuadro" value="1" @if ($cuadroCosto->tipo_cuadro == 1) checked @endif>
                        Bienes para Acuerdo Marco
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEnviarOrdenDespacho" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar orden de despacho</h4>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" role="form" method="POST" id="formOrdenDespacho">
                    @csrf
                    <input type="hidden" name="idCuadro" value="{{ $cuadroCosto->id }}">
                    <div class="form-group">
                        <label>Mensaje para la orden</label>
                        <textarea class="form-control" rows="12" name="mensaje">
                            Por favor hacer seguimiento a este pedido. Vence: {{$ordenCompra != null ? $ordenCompra->fecha_entrega_format : ''}}
                            FECHA DE DESPACHO: 

                            Favor de generar documentación: 
                            • FACTURA 
                            • GUIA
                            • CERTIFICADO DE GARANTIA 
                            • CCI

                            Saludos,
                            {{ Auth::user()->name }}
                        </textarea>
                    </div>
                    <div class="form-group">
                        <label>Adjuntar archivos {!! $ordenCompra != null ? '<small>(los archivos de la O/C ya se incluyen con la orden de despacho)</small>' : '' !!}</label>
                        <input type="file" name="archivos[]" multiple="true" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnEnviarOrdenDespacho">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTransformacion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Transformación de producto</h4>
            </div>
            <div class="modal-body">
                <fieldset>
                    <legend>Producto base</legend>
                    <table id="tableProductoBase" class="table table-condensed table-bordered" style="width: 100%; font-size: small">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 15%">Nro. parte</th>
                                <th class="text-center" style="width: 15%">Marca</th>
                                <th class="text-center" style="width: 70%">Descripción del producto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="numero-parte text-center"></td>
                                <td class="marca text-center"></td>
                                <td class="descripcion"></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Producto transformado</legend>
                    <table id="tableProductoTransformado" class="table table-condensed table-bordered" style="width: 100%; font-size: small">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 15%">Nro. parte</th>
                                <th class="text-center" style="width: 15%">Marca</th>
                                <th class="text-center" style="width: 50%">Descripción del producto</th>
                                <th class="text-center" style="width: 20%">Comentario</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Opciones adicionales:</td>
                                <td colspan="3">
                                    <div class="form-inline" id="divOpcionesAdicionalesTransformacion">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="etiquetado" data-id="" data-campo="etiquetado_producto_transformado"> Etiquetado
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="bios" data-campo="bios_producto_transformado"> BIOS
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="officePreinstalado" data-campo="office_preinstalado_producto_transformado"> Office
                                                preinstalado
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="officeActivado" data-campo="office_activado_producto_transformado"> Office activado
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
                <fieldset>
                    <legend>Ingresos y salidas para transformación:</legend>

                    <table id="tableMovimientosTransformacion" class="table table-condensed table-bordered" style="width: 100%; font-size: small; margin-bottom: 5px">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 35%">Ingresa</th>
                                <th class="text-center" style="width: 30%">Sale</th>
                                <th class="text-center" style="width: 30%">Comentario</th>
                                <th class="text-center" style="width: 5%">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <button class="btn btn-xs btn-default {{ $tipoEdicion == 'corporativo' ? '' : 'hidden' }}" id="btnAgregarFilaMovimientoTransformacion"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar fila</button>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMonedaCuadro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Moneda para subtotales</h4>
            </div>
            <div class="modal-body">
                <p>Moneda utilizada para mostrar los subtotales (suma de costo de compra, total flete, etc.)</p>
                <div class="form-group">
                    <select class="form-control" id="selectMonedaCuadro" name="moneda" data-id="{{ $cuadroCosto->id }}" data-campo="moneda">
                        <option value="s" @if ($cuadroCosto->moneda == 's') selected @endif>Soles (S/)</option>
                        <option value="d" @if ($cuadroCosto->moneda == 'd') selected @endif>Dólares ($)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTipoCambio" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Tipo de cambio</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Monto:</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-addon">S/</div>
                                <input type="text" class="form-control decimal" data-id="{{ $cuadroCosto->id }}" value="{{ $cuadroCosto->tipo_cambio }}" data-campo="tipo_cambio" id="txtTipoCambio" name="tipoCambio" placeholder="Monto">
                            </div>
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

<div class="modal fade" id="modalIgv" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">IGV</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Valor:</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" class="form-control entero" data-campo="igv" id="txtIgv" value="{{ $cuadroCosto->igv }}">
                                <span class="input-group-addon">%</span>
                            </div>
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

<div class="modal fade" id="modalProveedores" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Lista de proveedores</h4>
            </div>
            <div class="modal-body">

                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Producto</h3>
                    </div>
                    <div class="box-body">
                        <div class="producto"></div>
                        <div class="cantidad"></div>
                    </div>
                </div>

                <div @if ($tipoEdicion=='ninguno' ) class="box box-solid hidden" @else class="box box-solid" @endif>
                    <div class="box-header with-border">
                        <h3 class="box-title">Agregar proveedor a la lista</h3>
                    </div>
                    <div class="box-body">
                        <form id="formProveedor">
                            @csrf
                            <input type="hidden" id="txtMonedaProveedor" value="s" name="moneda">
                            <input type="hidden" id="txtProveedorIdFila" name="idFila">
                            <table id="tableProveedores" class="table table-condensed" style="width: 100%; font-size: small; margin-top: 10px; margin-bottom: 0px">
                                <thead>
                                    <tr>
                                        <th style="width: 25%" class="text-center">Proveedor</th>
                                        <th style="width: 10%" class="text-center">Plazo ent.<br>días</th>
                                        <th class="text-center">Precio<br>(sin IGV)</th>
                                        <th class="text-center">Flete S/<br>(sin IGV)</th>
                                        <th style="width: 15%" class="text-center">Fondo</th>
                                        <th style="width: 20%" class="text-center">Comentario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="selectpicker" data-live-search="true" data-width="100%" name="proveedor" id="selectProveedor">
                                                @foreach ($proveedores as $proveedor)
                                                <option value="{{ $proveedor->id }}">
                                                    {{ $proveedor->razon_social }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <a href="#" id="aNuevoProveedor">Nuevo proveedor</a>
                                            <!--<span class="help-block" id="spanNuevoProveedor">Nuevo proveedor</span>-->
                                        </td>
                                        <td>
                                            <input type="number" id="txtPlazo" name="plazo" class="text-center form-control entero" placeholder="Plazo" value="1">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span id="spanMonedaSeleccionadaProveedor">S/</span> <span class="caret"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="#" class="moneda" data-moneda="s">S/</a></li>
                                                        <li><a href="#" class="moneda" data-moneda="d">$</a></li>
                                                    </ul>
                                                </div><!-- /btn-group -->
                                                <input id="txtPrecio" type="text" class="text-right form-control decimal" name="precio" placeholder="Precio">
                                            </div><!-- /input-group -->
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon">S/</span>
                                                <input type="text" id="txtFlete" class="text-right form-control decimal" name="flete" placeholder="Flete" value="0">
                                            </div>
                                        </td>
                                        <td id="tdFondoProveedor">

                                        </td>
                                        <td>
                                            <textarea id="txtComentario" class="form-control" name="comentario" placeholder="Opcional"></textarea>
                                        </td>

                                    </tr>
                                    <!--<tr>
                                                <td colspan="4"><button class="btn btn-xs btn-default" id="btnNuevoProveedor"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo proveedor</button></td>
                                            </tr>-->
                                </tbody>
                            </table>
                        </form>
                        <div class="text-center">
                            <button type="button" id="btnAgregarProveedorCuadro" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> Agregar a lista</button>
                        </div>
                    </div>
                </div>

                <div class="box box-solid last">
                    <div class="box-header">
                        <h3 class="box-title">Lista de proveedores</h3>
                    </div>
                    <div class="box-body">
                        <table id="tableProveedoresFila" class="table table-condensed" style="width: 100%; font-size: small; margin-top: 10px">
                            <thead>
                                <tr>
                                    <th class="text-center">Proveedor</th>
                                    <th width="5%" class="text-center">Plazo<br>ent.</th>
                                    <th width="5%" class="text-center">Moneda</th>
                                    <th width="10%" class="text-center">Precio<br>(sin IGV)</th>
                                    <th width="10%" class="text-center">Flete S/<br>(sin IGV)</th>
                                    <th width="15%" class="text-center">Fondo</th>
                                    <th width="15%" class="text-center">Comentario</th>
                                    <th width="5%" class="text-center">Selec.</th>
                                    <th width="10%" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if ($tipoEdicion == 'corporativo' || $tipoEdicion == 'compras')
                <button id="btnSeleccionarMejorPrecio" type="button" tabindex="-1" class="btn btn-success">Seleccionar
                    mejor precio</button>
                @endif
                <button type="button" tabindex="-1" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo proveedor</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="formNuevoProveedor">
                    @csrf
                    <div class="form-group">
                        <label class="col-sm-4 control-label">RUC / DNI:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="RUC / DNI" name="ruc" maxlength="12">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Proveedor:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="Razón social" name="razonSocial" maxlength="100">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnRegistrarProveedor">Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalInformacionAdicional" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Información adicional del cuadro</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#menuOc">O/C</a></li>
                    <li><a data-toggle="tab" href="#menuOportunidad">Oportunidad</a></li>
                    <li><a data-toggle="tab" href="#menuCondicionCredito">Condición de crédito</a></li>
                </ul>

                <div class="tab-content">
                    <div id="menuOc" class="tab-pane fade in active">
                        <br>
                        @if ($ordenCompra != null)
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Número de O/C</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $ordenCompra->nro_orden }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Empresa</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{!! $ordenCompra->empresa->empresa !!}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Entidad</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $ordenCompra->entidad->nombre }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Fecha de publicación</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">
                                        {{ $ordenCompra->fecha_publicacion_format }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Fecha de entrega</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $ordenCompra->fecha_entrega_format }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Lugar de entrega</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $ordenCompra->lugar_entrega }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Archivos</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{!! $ordenCompra->archivos !!}</div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="text-center">No hay O/C vinculada a este cuadro</div>
                        @endif
                    </div>
                    <div id="menuOportunidad" class="tab-pane fade">
                        <br>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Código</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $oportunidad->codigo_oportunidad }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Oportunidad</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $oportunidad->oportunidad }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Entidad</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $oportunidad->entidad->nombre }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Responsable</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $oportunidad->responsable->name }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Fecha límite</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static">{{ $oportunidad->fecha_limite }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="menuCondicionCredito" class="tab-pane fade">
                        <br>
                        <div class="form-horizontal">
                            @if ($tipoEdicion == 'corporativo')
                            <div class="form-group">
                                <div class="col-sm-3 col-sm-offset-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="optCondicionCredito" value="1" {{ $idCondicionCredito == 1 ? 'checked' : '' }}>
                                            Contado
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 col-sm-offset-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="optCondicionCredito" value="2" {{ $idCondicionCredito == 2 ? 'checked' : '' }}>
                                            Crédito
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-control">
                                        <option value="15" {{$cuadroCosto->dato_credito==15 ? 'selected' : ''}}>15 días</option>
                                        <option value="30" {{$cuadroCosto->dato_credito==30 ? 'selected' : ''}}>30 días</option>
                                        <option value="45" {{$cuadroCosto->dato_credito==45 ? 'selected' : ''}}>45 días</option>
                                        <option value="60" {{$cuadroCosto->dato_credito==60 ? 'selected' : ''}}>60 días</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 col-sm-offset-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="optCondicionCredito" value="3" {{ $idCondicionCredito == 3 ? 'checked' : '' }}>
                                            Cuotas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    @php $cantidad=10; @endphp
                                    <select class="form-control">
                                        @for ($i = 1; $i <= 10; $i++) 
                                            <option value="{{ $i }}" {{$cuadroCosto->dato_credito==$i ? 'selected' : ''}}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 col-sm-offset-2">
                                    <label>Requiere aprobación</label>
                                </div>
                                <div class="col-sm-4">
                                    <select name="nueva_aprobacion" id="nueva_aprobacion" class="form-control">
                                        <option value="0" {{ ($cuadroCosto->aprobacion_previa == 0) ? "selected" : "" }}>NO</option>
                                        <option value="1" {{ ($cuadroCosto->aprobacion_previa == 1 || $cuadroCosto->aprobacion_previa == 2) ? "selected" : "" }}>SI</option>
                                    </select>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <div class="col-sm-7 col-sm-offset-2">
                                    {{$cuadroCosto->condicion_credito_format}}
                                </div>
                            </div>
                            @endif
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

<div class="modal fade" role="dialog" id="modal-centro-costos">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal-centro-costos"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Seleccionar centro de costo</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel-group" id="accordion" name="centro-costos-panel" role="tablist" aria-multiselectable="true">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" class="close" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
    <label id="indice_item" style="display: none;"></label>
</div>

<div class="modal fade" id="modalSolicitudes" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Historial de solicitudes</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>
                <div class="table-responsive">
                    <table width="100%" class="table table-condensed table-striped" id="tableSolicitudes" style="font-size: small">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 10%">Fecha de solicitud</th>
                                <th class="text-center" style="width: 10%">Tipo</th>
                                <th class="text-center" style="width: 15%">Enviada por</th>
                                <th class="text-center">Comentario de solicitante</th>
                                <th class="text-center" style="width: 15%">Enviada a</th>
                                <th class="text-center" style="width: 10%">Estado de aprobación</th>
                                <th class="text-center">Comentario de aprobador</th>
                                <th style="width: 10%" class="text-center">Fecha de respuesta</th>
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

<div class="modal fade" id="modalResponderSolicitud" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Responder solicitud</h4>
            </div>
            <div class="modal-body">
                <form>
                    @if ($ultimaSolicitud != null)
                    <div class="form-group">
                        <label>Tipo</label>
                        <div class="form-control-static {{ in_array($ultimaSolicitud->tiposolicitud->id, [2, 3]) ? 'resaltar' : '' }}">{{ $ultimaSolicitud->tiposolicitud->tipo }}</div>
                    </div>
                    <div class="form-group">
                        <label>Solicitado por</label>
                        <div class="form-control-static">{{$ultimaSolicitud->enviadaPor->name}}</div>
                    </div>
                    <div class="form-group">
                        <label>Comentario del solicitante</label>
                        <div class="form-control-static">{{$ultimaSolicitud->comentario_solicitante}}</div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label>Aprobar solicitud</label>
                        <select class="form-control" id="selectResponderAprobar">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comentario</label>
                        <textarea class="form-control" id="txtResponderComentario" placeholder="Ingrese comentario..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnResponderSolicitud">Responder</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVistaPreviaCuadro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Solicitar aprobación de cuadro {{ $oportunidad->codigo_oportunidad }}</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>

                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Detalles del cuadro</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="tableVistaPrevia" class="table table-condensed table-hover table-striped" style="font-size: x-small; width: 100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 15%">Part No.</th>
                                        <th class="text-center" style="width: 35%">Descripción</th>
                                        <th class="text-center" style="width: 10%">Cant.</th>
                                        <th class="text-center" style="width: 20%">Proveedor</th>
                                        <th class="text-center" style="width: 10%">Costo compra + flete</th>
                                        <th class="text-center" style="width: 10%">Ganancia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-right" colspan="5"><strong>Bienes para servicio:</strong></td>
                                        <td class="text-right bordered bienes-servicio"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="5"><strong>Gastos generales:</strong></td>
                                        <td class="text-right bordered gastos-generales"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Ganancia real:</strong></td>
                                        <td class="text-right bordered ganancia-real"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Margen ganancia:</strong></td>
                                        <td class="text-right bordered margen-ganancia"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Condición de crédito:</strong></td>
                                        <td class="text-right bordered condicion-credito"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="box box-solid last">
                    <div class="box-header with-border">
                        <h3 class="box-title">Datos de solicitud</h3>
                    </div>
                    <div class="box-body">
                        <form class="form-horizontal">
                            @csrf
                            <input type="hidden" name="cuadro" value="{{ $cuadroCosto->id }}">
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-offset-2 control-label">Tipo</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="tipo">
                                        <option value="{{ $tiposSolicitud[0]->id }}">
                                            {{ $tiposSolicitud[0]->tipo }}
                                        </option>
                                        @if ($cuadroCosto->estado_aprobacion == 1)
                                        <option value="{{ $tiposSolicitud[3]->id }}">
                                            {{ $tiposSolicitud[3]->tipo }}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-offset-2 control-label">Comentario</label>
                                <div class="col-sm-5">
                                    <textarea class="form-control" name="comentario" placeholder="Comentario"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary enviar-solicitud">Enviar solicitud</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalResponsables" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Responsables del cuadro de presupuesto</h4>
            </div>
            <div class="modal-body">

                <div class="box box-solid">
                    <div class="box-body">
                        <table style="width: 100%; margin-bottom: 0px" class="table table-condensed">
                            <thead>
                                <tr>
                                    <th class="text-center">Corporativo</th>
                                    <th class="text-center" style="width: 15%">Porcentaje</th>
                                    <th class="text-center" style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $oportunidad->responsable->name }} <small>(Resp. oportunidad)</small>
                                    </td>
                                    <td class="text-right">
                                        @php($porcentaje = 0)
                                        @if ($tipoEdicion == 'corporativo')
                                        <input type="text" id="txtPorcentajeResponsable" data-id="{{ $cuadroCosto->id }}" data-campo="porcentaje_responsable" maxlength="3" value="{{ $cuadroCosto->porcentaje_responsable }}" class="form-control entero porcentaje input-sm text-right">
                                        @else
                                        <input type="hidden" value="{{ $cuadroCosto->porcentaje_responsable }}" class="porcentaje">
                                        {{ $cuadroCosto->porcentaje_responsable }}%
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @if ($tipoEdicion == 'corporativo')
                        <button style="margin-bottom: 0px" id="btnAgregarResponsable" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus"></span> Agregar
                            responsable</button>
                        @endif
                    </div>
                </div>

                <div class="box box-solid last">
                    <div class="box-body">

                        <table style="width: 100%" class="table table-condensed" id="tableResponsables">
                            <thead>
                                <tr>
                                    <th class="text-center">Otros responsables</th>
                                    <th class="text-center" style="width: 15%">Porcentaje</th>
                                    <th class="text-center" style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($responsables as $resp)
                                <tr>
                                    <td>
                                        @if ($tipoEdicion == 'corporativo')
                                        <select data-id="{{ $resp->id }}" class="form-control input-sm responsable">
                                            @foreach ($corporativos as $corporativo)
                                            <option value="{{ $corporativo->id }}" @if ($corporativo->id ==
                                                $resp->id_responsable) selected @endif>
                                                {{ $corporativo->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @else
                                        @foreach ($corporativos as $corporativo)
                                        @if ($corporativo->id == $resp->id_responsable)
                                        {{ $corporativo->name }}
                                        @endif
                                        @endforeach
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($tipoEdicion == 'corporativo')
                                        <input value="{{ $resp->porcentaje }}" data-id="{{ $resp->id }}" type="text" class="form-control porcentaje input-sm text-right responsable">
                                        @else
                                        <input value="{{ $resp->porcentaje }}" data-id="{{ $resp->id }}" type="hidden" class="porcentaje responsable">
                                        {{ $resp->porcentaje }}%
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($tipoEdicion == 'corporativo')
                                        <button data-id="{{ $resp->id }}" title="Retirar responsable" class="btn btn-xs btn-default eliminar"><span class="glyphicon glyphicon-remove"></span></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="border-top: 0px" class="text-right"><strong>Total porcentaje:</strong>
                                    </td>
                                    <td style="width: 15%; border-top: 0px" class="text-right">
                                        <span id="strongTotalPorcentaje"></span>
                                    </td>
                                    <td style="width: 5%; border-top: 0px"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEnviarSolicitud" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Titulo</h4>
            </div>
            <div class="modal-body">
                <form method="post">
                    @csrf
                    <input type="hidden" name="cuadro" value="{{ $cuadroCosto->id }}">
                    <input type="hidden" name="tipo" value="{{ $cuadroCosto->estado_aprobacion == 4 ? '3' : '2' }}">
                    <p class="mensaje">Ingrese el motivo de la solicitud</p>
                    <textarea class="form-control" name="comentario"></textarea>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary enviar-solicitud">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFinalizarCuadro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Finalizar cuadro de presupuesto</h4>
            </div>
            <div class="modal-body">
                El cuadro se dará por finalizado.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnFinalizarCuadro">Finalizar</button>
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
                <div class="box box-solid {{ $cuadroCosto->estado_aprobacion == 4 ? 'last' : '' }}">
                    <div class="box-body">
                        <table class="table table-condensed" style="font-size:small">
                            <thead>
                                <tr>
                                    <th width="25%" class="text-center">Usuario</th>
                                    <th width="25%" class="text-center">Fecha</th>
                                    <th width="50%" class="text-center">Comentario</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyComentarios">
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($cuadroCosto->estado_aprobacion != 4)
                <div class="box box-solid last">
                    <div class="box-header with-border">
                        <h3 class="box-title">Nuevo comentario</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <textarea id="txtFilaComentario" placeholder="Ingrese un comentario" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="text-center">

                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                @if ($cuadroCosto->estado_aprobacion != 4)
                <button type="button" data-tabla="" data-id="0" id="btnRegistrarComentario" class="btn btn-primary">Registrar</button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLicencias" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Seleccionar licencia</h4>
            </div>
            <div class="modal-body">
                <p>Después de elegir la licencia, podrá ingresar la cantidad, proveedor, etc.</p><br>
                <table class="table table-condensed table-striped table-hover" style="font-size: small">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 20%">Marca</th>
                            <th class="text-center" style="width: 20%">Nro. Parte</th>
                            <th class="text-center" style="width: 50%">Descripción</th>
                            <th class="text-center" style="width: 10%">Selec.</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyLicencias"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFondosMS" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Seleccionar Fondo de Microsoft</h4>
            </div>
            <div class="modal-body">
                <table class="table table-condensed table-striped table-hover" style="font-size: small">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 20%">Marca</th>
                            <th class="text-center" style="width: 20%">Nro. Parte</th>
                            <th class="text-center" style="width: 50%">Descripción</th>
                            <th class="text-center" style="width: 10%">Selec.</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyFondoMS"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="hide">
    <select id="selectCategoriasGasto">
        @foreach ($categoriasGasto as $categoria)
        <option value="{{ $categoria->id }}">{{ $categoria->categoria }}</option>
        @endforeach
    </select>

    <select id="selectCorporativos">
        @foreach ($corporativos as $corporativo)
        <option value="{{ $corporativo->id }}">{{ $corporativo->name }}</option>
        @endforeach
    </select>

    <select id="selectOrigenesCosteo">
        @foreach ($origenesCosteo as $origen)
        <option value="{{ $origen->id }}">{{ $origen->origen }}</option>
        @endforeach
    </select>
</div>
@endsection


@section('scripts')
    <link href="{{ asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js') }}"></script>
    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>
    <link href="{{ asset('assets/lobibox/dist/css/lobibox.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>

    <script src="{{ asset('mgcp/js/util.js') }}?v={{ filemtime(public_path('mgcp/js/util.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-base-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-base-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-base-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-base-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-detalle-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-detalle-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-producto-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-producto-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-costo-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-costo-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/cuadro-costo-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/cuadro-costo-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/acuerdo-marco-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/acuerdo-marco-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/acuerdo-marco-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/acuerdo-marco-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/bien-servicio-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/bien-servicio-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/bien-servicio-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/bien-servicio-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/gasto-general-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/gasto-general-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/gasto-general-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/gasto-general-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/proveedor-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/proveedor-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/proveedor-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/proveedor-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/comentario-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/comentario-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/solicitud-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/solicitud-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/solicitud-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/solicitud-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/responsable-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/responsable-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/responsable-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/responsable-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/transformacion-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/transformacion-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/transformacion-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/transformacion-view.js')) }}"></script>

    <script src="{{ asset('mgcp/js/orden-compra/propia/orden-compra-propia-view.js') }}?v={{ filemtime(public_path('mgcp/js/orden-compra/propia/orden-compra-propia-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/orden-compra/propia/orden-compra-propia-model.js') }}?v={{ filemtime(public_path('mgcp/js/orden-compra/propia/orden-compra-propia-model.js')) }}"></script>

    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/entidad-view.js') }}?v={{ filemtime(public_path('mgcp/js/acuerdo-marco/entidad/entidad-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/entidad-model.js') }}?v={{ filemtime(public_path('mgcp/js/acuerdo-marco/entidad/entidad-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/contacto-model.js') }}?v={{ filemtime(public_path('mgcp/js/acuerdo-marco/entidad/contacto-model.js')) }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/contacto-view.js') }}?v={{ filemtime(public_path('mgcp/js/acuerdo-marco/entidad/contacto-view.js')) }}"></script>

    <script src="{{ asset('mgcp/js/cuadro-costos/centro-costo-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/centro-costo-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/centro-costo-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/centro-costo-model.js')) }}"></script>

    <script src="{{ asset('mgcp/js/cuadro-costos/fondo-proveedor-view.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/fondo-proveedor-view.js')) }}"></script>
    <script src="{{ asset('mgcp/js/cuadro-costos/fondo-proveedor-model.js') }}?v={{ filemtime(public_path('mgcp/js/cuadro-costos/fondo-proveedor-model.js')) }}"></script>

    <script>
        $(document).ready(function() {
            //**INICIALIZACION DE SISTEMA
            $(".sidebar-mini").addClass("sidebar-collapse");
            Util.seleccionarMenu("{{ route('mgcp.cuadro-costos.lista') }}");
            Util.activarDatePicker();
            Util.activarSoloEnteros();
            Util.activarSoloDecimales();

            const token = '{{ csrf_token() }}';
            const tipoEdicion = '{{ $tipoEdicion }}';
            const idUsuario = '{{ Auth::user()->id }}';
            const idCuadro = "{{ $cuadroCosto->id }}";


            const fondoProveedorView = new FondoProveedorView(new FondoProveedorModel(token),
                "{{ asset('mgcp/img/spinner_24.gif')}}");

            const entidadView = new EntidadView(new EntidadModel(token));
            const contactoView = new ContactoEntidadView(new ContactoEntidadModel(token),
                '{{ Auth::user()->tieneRol(60) }}', true);
            entidadView.obtenerDetallesEvent();

            const proveedorView = new ProveedorView(tipoEdicion, new ProveedorModel(token), fondoProveedorView,
                null);
            const comentarioView = new ComentarioView(null);
            const cuadroCostoView = new CuadroCostoView(idCuadro, new CuadroCostoModel(token));
            cuadroCostoView.actualizarCampoEvent();
            cuadroCostoView.finalizarCuadroEvent();
            cuadroCostoView.seleccionarCentroCostoEvent();
            cuadroCostoView.enviarOrdenDespachoEvent();
            const acuerdoMarcoView = new AcuerdoMarcoView(idCuadro, new AcuerdoMarcoModel(token), proveedorView,
                comentarioView);
            acuerdoMarcoView.obtenerDetallesFilas();
            acuerdoMarcoView.listarLicenciasEvent();
            acuerdoMarcoView.listarFondoMSEvent();
            const bienesServicioView = new BienServicioView(idCuadro, new BienServicioModel(token), proveedorView,
                comentarioView);
            const gastosGeneralesView = new GastoGeneralView(idCuadro, new GastoGeneralModel(token));
            const solicitudView = new SolicitudView(idCuadro, new SolicitudModel(token));
            const responsableView = new ResponsableView(idCuadro, new ResponsableModel(token));
            const transformacionView = new TransformacionView(new TransformacionModel(token), idCuadro,
                tipoEdicion);
            const ordenCompraPropiaView = new OrdenCompraPropiaView(new OrdenCompraPropiaModel(token), {},
                '{{ Auth::user()->id }}');
            transformacionView.obtenerDetallesEvent();
            transformacionView.actualizarCheckboxEvent(new AcuerdoMarcoModel(token));
            transformacionView.actualizarFilaEvent();
            transformacionView.eliminarFilaEvent();
            transformacionView.agregarFilaEvent();
            ordenCompraPropiaView.cambiarContactoEvent();

            const centroCostoView = new CentroCostoView(new CentroCostoModel(token));
            centroCostoView.listarCentroCostosEvent();

        });
    </script>
@endsection