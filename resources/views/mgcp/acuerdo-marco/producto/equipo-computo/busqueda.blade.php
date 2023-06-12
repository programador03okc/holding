@extends('mgcp.layouts.app')

@section('cabecera')
    Búsqueda de equipos de cómputo
@endsection

@section('estilos')
<style>
    small.help-block {
        margin-top: 0px;
        margin-bottom: 0px;
    }
    #tableCabecera th {
        vertical-align: middle;
        text-align: right;
        border-top: none;
    }
    #tableCabecera td {
        border-top: none;
    }
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('mgcp.home') }}">Inicio</a></li>
        <li class="active">Acuerdo marco</li>
        <li class="active">Productos</li>
        <li class="active">Búsq. eq. cómputo</li>
    </ol>
@endsection

@section('cuerpo')

    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Criterios</h3>
        </div>
        <div class="box-body">
            <p>El sistema generará un listado de acuerdo a los criterios seleccionados. 
                Para exportar la lista, no es necesario realizar primero una búsqueda. Si elige al menos una opción de pantalla, se exluirán los equipos sin pantalla
            </p>
            <form class="form-horizontal" method="POST" target="_blank" action="{{route('mgcp.acuerdo-marco.proformas.generar-archivo-exportar')}}">
                @csrf
                <table class="table" id="tableCabecera" style="width: 100%; font-size: small">
                    <tbody>
                        <tr>
                            <th style="width: 13.33%">Categoría</th>
                            <td style="width: 20%">
                                <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="8" name="categoria[]">
                                @foreach ($categorias as $categoria)
                                <option value="{{$categoria->categoria}}" selected>{{$categoria->categoria}}</option>
                                @endforeach
                            </select>
                            </td>
                            <th style="width: 13.33%">Marca del equipo</th>
                            <td style="width: 20%" colspan="2">
                                <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="8" name="marca[]">
                                @foreach ($marcas as $marca)
                                <option value="{{$marca->marca}}" selected>{{$marca->marca}}</option>
                                @endforeach
                                </select>
                            </td>
                            <th>Almacenamiento (en GB)</th>
                            <td>
                                <input type="number" class="form-control entero" placeholder="Desde">
                                <small class="help-block">Desde</small>
                            </td>
                            <td>
                                <input type="number" class="form-control entero" placeholder="Hasta">
                                <small class="help-block">Hasta</small>
                            </td>
                            
                        </tr>
                        <tr>
                            <th>Modelo procesador</th>
                            <td>
                                <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="8" name="empresa[]">
                                    @foreach ($procesadorModelos as $modelo)
                                    <option value="{{$modelo->procesador_modelo}}" selected>{{$modelo->procesador_modelo}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <th style="width: 13.33%">Sist. operativo</th>
                            <td style="width: 20%" colspan="2">
                                <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="8" name="empresa[]">
                                    @foreach ($sistemasOperativos as $sistema)
                                    <option value="{{$sistema->sistema_operativo}}" {{(strpos($sistema->sistema_operativo, 'WINDOWS 10') !== false) ? "selected" : ""}}>{{$sistema->sistema_operativo}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <th>Memoria RAM (en GB)</th>
                            <td>
                                <input type="number" class="form-control entero" placeholder="Desde">
                                <small class="help-block">Desde</small>
                            </td>
                            <td>
                                <input type="number" class="form-control entero" placeholder="Hasta">
                                <small class="help-block">Hasta</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Suite ofimática</th>
                            <td>
                                <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="8" name="empresa[]">
                                    <option selected>SÍ</option>
                                    <option selected>NO</option>
                                </select>
                            </td>
                            <th>Res. pantalla</th>
                            <td colspan="2">
                                <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="8" name="empresa[]">
                                    @foreach ($pantallaResoluciones as $resolucion)
                                    <option value="{{$resolucion->pantalla_resolucion}}">{{$resolucion->pantalla_resolucion}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <th>Tamaño de pantalla (en pulgadas)</th>
                            <td>
                                <input type="number" class="form-control entero" placeholder="Desde">
                                <small class="help-block">Desde</small>
                            </td>
                            <td>
                                <input type="number" class="form-control entero" placeholder="Hasta">
                                <small class="help-block">Hasta</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Libre</th>
                            <td>
                                <input type="text" class="form-control" placeholder="Ejemplo: THINKPAD">
                                <small class="help-block">Modelo del equipo, etc</small>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="3">
                                <button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                                <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-download-alt"></span> Exportar</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
    </div>

    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Resultado</h3>
        </div>
        <div class="box-body">
            <table id="tableProductos" class="table table-condensed table-hover table-striped"
                    style="font-size: x-small; width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">Acuerdo</th>
                            <th class="text-center">Producto</th>
                            <th style="width: 10%" class="text-center">Marca</th>
                            <th style="width: 10%" class="text-center">Modelo</th>
                            <th style="width: 10%" class="text-center">Nro. parte</th>
                            @foreach ($empresas as $empresa)
                                <th class="text-center">{{ $empresa->empresa }}</th>
                            @endforeach
                            <th class="text-center" style="width: 10%">Herramientas</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
        </div>
    </div>

@endsection

@section('scripts')
<link href='{{asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")}}' rel="stylesheet" type="text/css" />
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

<link href='{{ asset("assets/bootstrap-select/css/bootstrap-select.min.css") }}' rel="stylesheet" type="text/css" />
<script src='{{ asset("assets/bootstrap-select/js/bootstrap-select.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-select/js/i18n/defaults-es_ES.js") }}'></script>

    <script src="{{ asset('mgcp/js/util.js') }}"></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            Util.activarDatePicker();
            Util.activarSoloEnteros();
            Util.activarSoloDecimales();
        });

    </script>
@endsection
