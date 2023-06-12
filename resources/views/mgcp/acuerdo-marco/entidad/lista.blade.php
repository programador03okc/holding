@extends('mgcp.layouts.app')
@section('estilos')
<link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/lobibox/dist/css/lobibox.min.css')}}" rel="stylesheet" type="text/css" />
<style>

</style>
@endsection
@section('contenido')


@section('cabecera')
Lista de entidades
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Entidades</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableEntidades" class="table table-hover table-condensed table-striped" style="font-size: x-small; width:100%">
                <thead>
                    <tr>
                        <th style="width:5%" class="text-center">DNI/RUC</th>
                        <th style="width: 15%" class="text-center">Entidad</th>
                        <th style="width: 15%" class="text-center">Dirección</th>
                        <th style="width: 15%" class="text-center">Ubigeo</th>
                        <th class="text-center">Nombre responsable</th>
                        <th style="width: 10%" class="text-center">Cargo responsable</th>
                        <th style="width: 7%" class="text-center">Telféfono</th>
                        <th class="text-center">Correo</th>
                        <th style="width: 5%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEntidad" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar entidad</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>
                <form class="contenedor" id="formEntidad">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-sm-6">
                            <fieldset>
                                <legend>Detalles</legend>
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">DNI/RUC *</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" required name="ruc" placeholder="RUC">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Entidad *</label>
                                        <div class="col-sm-8">
                                            <textarea type="text" class="form-control" required name="nombre" placeholder="Nombre"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Dirección</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="direccion" placeholder="Dirección"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Ubigeo</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="ubigeo" placeholder="Ubigeo">
                                            <small style="margin-top: 5px" class="help-block">Ejemplo: IQUITOS / MAYNAS / LORETO</small>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <label class="col-sm-3 control-label">Semáforo</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static semaforo"></div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-sm-6">
                            <fieldset>
                                <legend>Responsable</legend>
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Nombre</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="responsable" placeholder="Nombre responsable">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Cargo</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="cargo" placeholder="Cargo responsable">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Teléfono</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="telefono" placeholder="Teléfono responsable">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Correo</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="correo" placeholder="Correo responsable">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <fieldset style="margin-top: 30px">
                        <legend>Contacto</legend>
                    </fieldset>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Nombre</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="nombre_contacto" placeholder="Teléfono contacto">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Cargo</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="cargo_contacto" placeholder="Cargo contacto">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Teléfono</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="telefono_contacto" placeholder="Teléfono contacto">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-3">
                                        Los campos con asterisco (*) son obligatorios
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Correo</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="correo_contacto" placeholder="Correo contacto">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Comentario</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="comentario" placeholder="Comentario"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mensaje-final"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnActualizarEntidad">Actualizar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}"></script>
<script src="{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}"></script>

<script src="{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}"></script>
<script src="{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}"></script>
<script src="{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}"></script>
<script src="{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}"></script>
<script src="{{ asset("mgcp/js/util.js?v=11") }}"></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=3") }}'></script>
<script src='{{ asset("mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=2") }}'></script>

<script>
    $(document).ready(function() {
        //*****INICIALIZACION*****
        Util.seleccionarMenu(window.location);
        const entidadModel = new EntidadModel('{{csrf_token()}}');
        const entidadView = new EntidadView(entidadModel);
        entidadView.listar('{{Auth::user()->tieneRol(60)}}' == '1');
        entidadView.obtenerDetallesEvent();
        entidadView.actualizarEvent();
    });

</script>
@endsection
