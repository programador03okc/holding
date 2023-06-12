@extends('mgcp.layouts.app')

@section('cabecera')
Nueva oportunidad
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Oportunidades</li>
    <li class="active">Nueva</li>
</ol>
@endsection

@section('cuerpo')


<div class="box box-solid">
    <div class="box-body">
        @include('mgcp.partials.flashmsg')
        @include('mgcp.partials.errors')

        <form class="form-horizontal bloquear-boton" id="formCrearOportunidad" role="form" method="POST" action="{{ route('mgcp.oportunidades.registrar') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id_empresa" value="{{ Auth::user()->id_empresa }}">

            @if (Auth::user()->tieneRol(4))
            <div class="form-group">
                <label class="col-sm-2 control-label">Código</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control disabled" id="codigo" name="codigo" value="{{ $codigo }}" disabled>
                </div>
                <label class="col-sm-2 control-label">Responsable *</label>
                <div class="col-sm-4">
                    <select name="responsable" class="form-control">
                        @foreach ($responsables as $responsable)
                            <option value="{{ $responsable->id }}" @if ($responsable->id == Auth::user()->id) selected @endif>{{ $responsable->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @else
            <div class="form-group">
                <label for="codigo" class="col-sm-2 control-label">Código</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control disabled" id="codigo" name="codigo" value="{{ $codigo }}" disabled>
                </div>
            </div>
            @endif

            <div class="form-group">
                <label class="col-sm-2 control-label">Cliente *</label>
                <div class="col-sm-4">
                    <select class="form-control select2" name="cliente" id="selectEntidad" style="width: 100%;">
                    </select>
                    <a href="#" id="aNuevaEntidad">Nuevo cliente</a>
                </div>
                <label class="col-sm-2 control-label">Reportado por</label>
                <div class="col-sm-4">
                    <input maxlength="100" list="personas" type="text" class="form-control" placeholder="Persona que reportó la oportunidad" name="reportado_por" value="{{old('reportado_por')}}">
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label">Oportunidad *</label>
                <div class="col-sm-10">
                    <textarea class="form-control" placeholder="Descripción de la oportunidad de negocio" name="oportunidad" required="">{{old('oportunidad')}}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Probabilidad *</label>
                <div class="col-sm-4">
                    <select name="probabilidad" class="form-control">
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
                <label class="col-sm-2 control-label">Fecha límite *</label>
                <div class="col-sm-4">
                    <input type="text" autocomplete="off" placeholder="dd-mm-aaaa" required name="fecha_limite" value="{{old('fecha_limite')}}" class="form-control date-picker">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Status *</label>
                <div class="col-sm-10">
                    <textarea class="form-control" placeholder="Status o avance de la oportunidad" name="status">{{old('fecha_limite')}}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Moneda *</label>
                <div class="col-sm-1">
                    <select name="tipo_moneda" class="form-control">
                        <option value="s">S/</option>
                        <option value="d">$</option>
                    </select>
                </div>
                <label class="col-sm-1 control-label">Importe *</label>
                <div class="col-sm-2">
                    <input type="text" placeholder="Importe" required="" name="importe" value="{{old('importe')}}" class="form-control number">
                </div>
                <label class="col-sm-2 control-label">Margen (%) *</label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input required="" maxlength="3" placeholder="Porcentaje de margen" type="text" name="margen" value="{{old('margen')}}" class="form-control entero">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Grupo *</label>
                <div class="col-sm-4">
                    <select name="grupo" id="select_grupo" class="form-control">
                        @foreach ($grupos as $grupo)
                        <option value="{{$grupo->id}}">{{$grupo->grupo}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="col-sm-2 control-label">Tipo de negocio *</label>
                <div class="col-sm-4">
                    <select class="form-control" name="tipo_negocio">
                        @foreach ($tiposNegocio as $tipo)
                        <option value="{{$tipo->id}}">{{$tipo->tipo}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Nombre del contacto</label>
                <div class="col-sm-4">
                    <input type="text" maxlength="100" name="nombre_contacto" value="{{old('nombre_contacto')}}" placeholder="Nombre" class="form-control">
                </div>
                <label class="col-sm-2 control-label">Cargo del contacto</label>
                <div class="col-sm-4">
                    <input type="text" maxlength="100" name="cargo_contacto" value="{{old('cargo_contacto')}}" placeholder="Cargo" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Teléfono del contacto</label>
                <div class="col-sm-4">
                    <input type="tel" maxlength="45" name="telefono_contacto" value="{{old('telefono_contacto')}}" placeholder="Teléfono" class="form-control">
                </div>
                <label class="col-sm-2 control-label">Correo del contacto</label>
                <div class="col-sm-4">
                    <input type="email" maxlength="100" name="correo_contacto" value="{{old('correo_contacto')}}" placeholder="Correo electrónico" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2">
                    <div class="form-control-static">Los campos con asterisco (*) son obligatorios</div>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" id="btnRegistrarOportunidad">Registrar</button>
            </div>
        </form>

    </div>
</div>

<div class="modal fade" id="modalNuevaEntidad" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo Cliente</h4>
            </div>
            <div class="modal-body">
                <form id="formNuevaEntidad" class="form-horizontal">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="ruc" class="col-sm-3 control-label">DNI /RUC *</label>
                        <div class="col-sm-9">
                            <input type="text" maxlength="11" min="8" class="form-control" required placeholder="DNI / RUC" name="ruc">
                            <small class="help-block">Debe tener 8 dígitos para DNI u 11 para RUC</small>
                            <div class="mensaje"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nombre *</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control validar" required placeholder="Nombre / Razón social" name="nombre">
                            <div class="mensaje"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="direccion" class="col-sm-3 control-label">Dirección</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Dirección" name="direccion" />
                            <small class="help-block">Ejemplo: AV. SIMON BOLIVAR NRO 344</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="direccion" class="col-sm-3 control-label">Ubigeo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Ubigeo" name="ubigeo" />
                            <small class="help-block">Ejemplo: LIMA / LIMA / PUEBLO LIBRE</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="telefono" class="col-sm-3 control-label">Teléfono</label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control" placeholder="Teléfono" name="telefono" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3">
                            <div class="form-control-static">Los campos con asteriscos (*) son obligatorios</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12" id="divNuevaEntidadMensaje">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnNuevaEntidadRegistrar" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <link href='{{asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")}}' rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>
    <link href='{{asset("assets/select2/css/select2.css")}}' rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/select2/js/select2.min.js") }}'></script>
    <script src='{{ asset("assets/select2/js/i18n/es.js") }}'></script>
    <script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
    <link href='{{asset("assets/lobibox/dist/css/lobibox.min.css")}}' rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}'></script>

    <script src='{{ asset("mgcp/js/util.js?v=20") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=20") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=20") }}'></script>
    <script src='{{ asset("mgcp/js/oportunidad/oportunidad-view.js?v=20") }}'></script>
    <script src='{{ asset("mgcp/js/oportunidad/oportunidad-model.js?v=20") }}'></script>


    <script>
        $(document).ready(function() {

            Util.seleccionarMenu(window.location);
            Util.activarDatePicker();
            Util.activarSoloEnteros();
            $('input.number').number(true, 2);

            const token = '{{csrf_token()}}';
            const oportunidadView = new OportunidadView(new OportunidadModel(token));
            const entidadView = new EntidadView(new EntidadModel(token));
            entidadView.nuevaEvent();
            entidadView.buscarEvent();
            oportunidadView.nuevaEvent();
        });

    </script>
@endsection
