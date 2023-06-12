@extends('mgcp.layouts.app')

@section('cabecera')
Nueva O/C directa
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{ route('mgcp.home') }}">Inicio</a></li>
    <li class="active">Órdenes de compra</li>
    <li class="active">Propias</li>
    <li class="active">Nueva O/C directa</li>
</ol>
@endsection

@section('cuerpo')


<form method="POST" action="{{ route('mgcp.ordenes-compra.propias.directas.registrar') }}" id="formRegistrarOc" enctype="multipart/form-data" role="form">
    <input type="hidden" name="id_empresa" value="{{ Auth::user()->id_empresa }}">
    @csrf

    <div class="box box-solid">
        <div class="box-body">
            @include('mgcp.partials.flashmsg')
            @include('mgcp.partials.errors')
            <p>Todos los campos son requeridos. Puede marcar la opción <em>Crear oportunidad y cuadro de costos</em> si no los ha creado anteriormente en otro formulario para esta O/C</p><br>
            <div class="form-horizontal">

                <div class="form-group">
                    <label class="col-sm-2 control-label">OCC (Softlink)</label>
                    <div class="col-sm-4">
                        <input type="text" placeholder="OCC" required name="occ" value="{{ old('occ') }}" class="form-control">
                    </div>

                    <label class="col-sm-2 control-label">Responsable</label>
                    @if(Auth::user()->tieneRol(4))
                    <div class="col-sm-4">
                        <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" data-size="10" name="responsable">
                            @foreach ($responsables as $responsable)
                            <option value="{{ $responsable->id }}" @if ($responsable->id == Auth::user()->id) selected @endif>{{ $responsable->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="col-sm-4 form-control-static">
                        <input type="hidden" name="responsable" value="{{ Auth::user()->id }}">
                        {{Auth::user()->name}}
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Cliente</label>
                    <div class="col-sm-4">
                        <select class="form-control select2" name="cliente" id="selectEntidad" style="width: 100%;">
                        </select>
                        <a href="#" id="aNuevaEntidad">Nuevo cliente</a>
                    </div>
                    <label class="col-sm-2 control-label">Monto total (inc.IGV)</label>

                    <div class="col-sm-4">
                        <div class="input-group" id="divMonto">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span id="spanMoneda">{{ old('moneda')==null ? "S/" : (old('moneda')=="s" ? "S/" : "$") }}</span> <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" class="moneda" data-moneda="s">S/</a></li>
                                    <li><a href="#" class="moneda" data-moneda="d">$</a></li>
                                </ul>
                            </div>
                            <input type="hidden" id="txtMoneda" name="moneda" value="{{ old('moneda') ?? "s" }}">
                            <input type="text" class="form-control" id="txtMontoTotal" required name="monto_total" value="{{ old('monto_total') }}" placeholder="Monto total">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Fecha publicación</label>
                    <div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="dd-mm-aaaa" required name="fecha_publicacion" value="{{ old('fecha_publicacion') == null ? date('d-m-Y') : old('fecha_publicacion') }}" class="form-control date-picker">
                        <small class="help-block" style="margin-bottom: 0px">dd-mm-aaaa</small>
                    </div>
                    <label class="col-sm-2 control-label">Fecha entrega</label>
                    <div class="col-sm-4">
                        <input type="text" autocomplete="off" placeholder="dd-mm-aaaa" required name="fecha_entrega" value="{{ old('fecha_entrega') == null ? date('d-m-Y') : old('fecha_entrega') }}" class="form-control date-picker">
                        <small class="help-block" style="margin-bottom: 0px">dd-mm-aaaa</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Lugar de entrega</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" name="lugar_entrega" required placeholder="Lugar de entrega">{{ old('lugar_entrega') }}</textarea>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked name="crearOportunidad" value="1"> Crear oportunidad y cuadro de presupuestos automáticamente
                            </label>
                        </div>
                    </div>
                    <label class="col-sm-2 control-label">Archivos</label>
                    <div class="col-sm-4">
                        <input type="file" multiple name="archivos[]" class="form-control" required name="archivos">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label hidden" id="lblTipoCambio">Tipo de cambio</label>
                    <div class="col-sm-4 hidden" id="divTipoCambio">
                        <div class="input-group">
                            <span class="input-group-addon">S/</span>
                            <input type="text" class="form-control decimal" name="tipo_cambio" id="txtTipoCambio" placeholder="Tipo de cambio" value="{{ old('tipo_cambio') ?? $tipoCambio }}">
                        </div>
                        <small style="margin-bottom: 0px;" class="help-block">T.C. de la fecha de publicación de la O/C</small>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 col-sm-offset-6">
                    <button type="submit" class="btn btn-primary" id="btnRegistrarOc">Registrar</button>
                </div>
            </div>
        </div>
    </div>
</form>

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
    <link href="{{ asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js') }}"></script>
    <link href="{{ asset('assets/select2/css/select2.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/select2/js/i18n/es.js') }}"></script>
    <link href="{{ asset('assets/lobibox/dist/css/lobibox.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('assets/jquery-number/jquery.number.min.js') }}"></script>
    <script src="{{ asset('mgcp/js/util.js?v=10') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=11') }}"></script>
    <script src="{{ asset('mgcp/js/orden-compra/propia/orden-compra-directa-view.js?v=21') }}"></script>

    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            Util.seleccionarMenu(window.location);
            Util.activarDatePicker();
            Util.activarSoloDecimales();
            $('#txtMontoTotal').number(true, 2);
            $('#txtTipoCambio').number(true, 3);

            const token = '{{ csrf_token() }}';
            const entidadView = new EntidadView(new EntidadModel(token));
            const ordenCompraView = new OrdenCompraDirectaView();
            entidadView.nuevaEvent();
            entidadView.buscarEvent();
            ordenCompraView.nuevaEvent();
            ordenCompraView.cambiarMonedaEvent();
            ordenCompraView.mostrarTipoCambio("{{ old('moneda') ?? 's' }}");
        });
    </script>
@endsection