@extends('mgcp.layouts.app')

@section('cabecera')
Notificaciones de acuerdo marco
@endsection

@section('estilos')
<style>
    #modalVerNotificacion div.modal-body div.form-group {
        margin-bottom: 0px !important;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Notificaciones</li>
</ol>
@endsection


@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        @include('mgcp.partials.flashmsg')
        <div class="col-sm-12">
            <table style="width: 100%; font-size: small" id="tableDatos" class="table table-condensed table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width: 15%" class="text-center">Emitido por</th>
                        <th style="width: 15%" class="text-center">Destinatario</th>
                        <th style="width: 10%" class="text-center">Acuerdo marco</th>
                        <th style="width: 10%" class="text-center">Orden de compra</th>
                        <th style="width: 15%" class="text-center">Asunto</th>
                        <th style="width: 10%" class="text-center">Fecha</th>
                        <th style="width: 5%" class="text-center">Estado</th>
                        <th style="width: 5%" class="text-center">Plazo</th>
                        <th style="width: 5%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalActualizarLista" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Actualizar lista</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnActualizarLista">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorialNotificacion" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Historial de notificación</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <table id="tableHistorialNotificacion" style="width: 100%" class="table table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Fecha de notificación</th>
                            <th class="text-center">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerNotificacion" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title titulo">Notificación</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-modal"></div>
                <div class="panel panel-default">
                    <div class="panel-heading orden-compra">Orden de compra</div>
                    <div class="panel-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Acuerdo Marco</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static acuerdo-marco data"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label tipo-entidad-1">Entidad</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static entidad-1 data"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading subtitulo">Detalles</div>
                    <div class="panel-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Fecha y hora de envío</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static fecha-hora data"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label tipo-entidad-2">Entidad</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static entidad-2 data"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Asunto</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static asunto data"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Mensaje</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static text-justify mensaje data"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Denominación de documento</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static denominacion-documento data"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Documento adjunto</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static documento-adjunto data"></div>
                                </div>
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
@endsection

@section('scripts')
    <link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />

    <script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>
    <link href="{{asset('assets/lobibox/dist/css/lobibox.min.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/notificacion/notificacion-model.js?v=4") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/notificacion/notificacion-view.js?v=6") }}'></script>
    <script src='{{ asset("mgcp/js/util.js") }}'></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);

            const token = '{{csrf_token()}}';
            const notificacionAmView = new NotificacionAmView(new NotificacionAmModel(token));
            notificacionAmView.listar();
            notificacionAmView.actualizarListaEvent();
            notificacionAmView.obtenerFechasDescargaEvent();
            notificacionAmView.verNotificacionEvent();
            notificacionAmView.historialNotificacionEvent();
        });
    </script>
@endsection
