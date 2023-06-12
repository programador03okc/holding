@extends('mgcp.layouts.app')

@section('cabecera') Lista de oportunidades @endsection

@section('estilos')
    <style>
        small.help-block {
            margin-bottom: 0px;
        }

        div.modal li {
            margin-bottom: 5px;
        }

        .group-table {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Oportunidades</li>
    <li class="active">Lista</li>
</ol>
@endsection

@section('cuerpo')


<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableOportunidades" class="table table-condensed table-hover table-striped" style="font-size: small; width: 100%">
                <thead>
                    <tr>
                        <th style="width: 11%" class="text-center">Cliente</th>
                        <th style="width: 13%" class="text-center">Oportunidad</th>
                        <th style="width: 6%" class="text-center">Prob.</th>
                        <th style="width: 13%" class="text-center">Status</th>
                        <th style="width: 6%" class="text-center">Importe</th>
                        <th style="width: 6%" class="text-center">Fecha<br>creación</th>
                        <th style="width: 6%" class="text-center">Fecha<br>límite</th>
                        <th style="width: 7%" class="text-center">Margen</th>
                        <th style="width: 8%" class="text-center">Responsable</th>
                        <th style="width: 6%" class="text-center">Estado</th>
                        <th style="width: 6%" class="text-center">Grupo</th>
                        <th style="width: 6%" class="text-center">Tipo</th>
                        <th style="width: 6%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEliminarOportunidad" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Eliminar oportunidad</h4>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de eliminar la oportunidad?</p>
                <div class="detalles">
                </div>
                <div class="mensaje-final">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarOportunidadAceptar" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarOportunidad" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar Oportunidad <span class="codigo-oportunidad"></span></h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>
                <form class="form-horizontal" id="formEditarOportunidad">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" id="txtIdOportunidad">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <!--<input type="text" disabled class="form-control" name="cliente" placeholder="Cliente">-->
                            <select name="cliente" class="form-control">
                                @foreach ($clientes as $cliente)
                                <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (Auth::user()->tieneRol(4))
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Responsable</label>
                        <div class="col-sm-10">
                            <select name="responsable" class="form-control">
                                @foreach ($responsables as $responsable)
                                <option value="{{$responsable->id}}">{{$responsable->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Oportunidad</label>
                        <div class="col-sm-10">
                            <textarea name="oportunidad" class="form-control" required placeholder="Oportunidad"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Probabilidad</label>
                        <div class="col-sm-4">
                            <select name="probabilidad" class="form-control">
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Límite</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Fecha límite" required name="fecha_limite" class="form-control validar date-picker">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Moneda</label>
                        <div class="col-sm-4">
                            <select name="tipo_moneda" class="form-control">
                                <option value="d">$</option>
                                <option value="s">S/</option>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Importe</label>
                        <div class="col-sm-4">
                            <input type="text" name="importe" required class="form-control validar number" placeholder="Importe">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Margen %</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control entero" required name="margen" placeholder="Margen">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Grupo</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="grupo" id="select_grupo">
                                @foreach ($grupos as $grupo)
                                <option value="{{$grupo->id}}">{{$grupo->grupo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Tipo</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="tipo_negocio">
                                @foreach ($tiposNegocio as $tipo)
                                <option value="{{$tipo->id}}">{{$tipo->tipo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Contacto</label>
                        <div class="col-sm-4">
                            <input type="text" name="nombre_contacto" class="form-control" placeholder="Nombre">
                        </div>
                        <label class="col-sm-2 control-label">Cargo</label>
                        <div class="col-sm-4">
                            <input type="text" name="cargo_contacto" class="form-control" placeholder="Cargo">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Teléfono</label>
                        <div class="col-sm-4">
                            <input type="text" name="telefono_contacto" class="form-control" placeholder="Teléfono">
                        </div>
                        <label class="col-sm-2 control-label">Correo</label>
                        <div class="col-sm-4">
                            <input type="text" name="correo_contacto" class="form-control" placeholder="Correo">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Reportado por</label>
                        <div class="col-sm-10">
                            <input maxlength="100" list="personas" type="text" class="form-control" placeholder="Nombre" name="reportado_por">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 mensaje-final">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" id="btnEditarOportunidadAceptar" class="btn btn-primary">Actualizar</button>
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
                    <p><small>Seleccione los filtros que desee aplicar y cierre este cuadro para continuar</small></p>
                    <fieldset class="group-table">
                        @csrf

                        @if (Auth::user()->tieneRol(45))
                        <div class="form-group">
                            <div class="col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" checked> Sólo veo oportunidades donde soy responsable
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkFechaLimite" id="chkFechaLimite" @if (session('oport_fecha_limite_desde')!==null) checked @endif> Fecha límite
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value="@if(session('oport_fecha_limite_desde')!==null){{session('oport_fecha_limite_desde')}}@else{{date('d-m-Y')}}@endif" class="form-control date-picker" name="fechaLimiteDesde" placeholder="dd-mm-aaaa">
                                <small class="help-block">Desde</small>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value="@if(session('oport_fecha_limite_hasta')!==null){{session('oport_fecha_limite_hasta')}}@else{{date('d-m-Y')}}@endif" class="form-control date-picker" name="fechaLimiteHasta" placeholder="dd-mm-aaaa">
                                <small class="help-block">Hasta</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkFechaCreacion" id="chkFechaCreacion" @if (session('oport_fecha_creacion_desde')!==null) checked @endif> Fecha creación
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value="@if(session('oport_fecha_creacion_desde')!==null){{session('oport_fecha_creacion_desde')}}@else{{date('d-m-Y')}}@endif" class="form-control date-picker" name="fechaCreacionDesde" placeholder="dd-mm-aaaa">
                                <small class="help-block">Desde</small>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value="@if(session('oport_fecha_creacion_hasta')!==null){{session('oport_fecha_creacion_hasta')}}@else{{date('d-m-Y')}}@endif" class="form-control date-picker" name="fechaCreacionHasta" placeholder="dd-mm-aaaa">
                                <small class="help-block">Hasta</small>
                            </div>
                        </div>
                        @if (!Auth::user()->tieneRol(45))
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkResponsable" id="chkResponsable" @if (session('oport_responsable')!==null) checked @endif> Responsable
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" name="selectResponsable">
                                    @foreach ($responsables as $responsable)
                                    <option value="{{$responsable->id}}" @if (session('oport_responsable')==$responsable->id) selected @endif>{{$responsable->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkTipoNegocio" id="chkTipoNegocio" @if (session('oport_tipo_negocio')!==null) checked @endif> Tipo negocio
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" name="selectTipoNegocio">
                                    @foreach ($tiposNegocio as $tipo)
                                    <option value="{{$tipo->id}}" @if (session('oport_tipo_negocio')==$tipo->id) selected @endif>{{$tipo->tipo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkProbabilidad" id="chkProbabilidad" @if (session('oport_probabilidad')!==null) checked @endif> Probabilidad
                                    </label>
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <select class="form-control" name="selectProbabilidad">
                                    <option value="alta" @if (session('oport_probabilidad')=='alta' ) selected @endif>Alta</option>
                                    <option value="media" @if (session('oport_probabilidad')=='media' ) selected @endif>Media</option>
                                    <option value="baja" @if (session('oport_probabilidad')=='baja' ) selected @endif>Baja</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkEstado" id="chkEstado" @if (session('oport_estado')!==null) checked @endif> Estado
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" name="selectEstado">
                                    @foreach ($estados as $estado)
                                    <option value="{{$estado->id}}" @if (session('oport_estado')==$estado->id) selected @endif>{{$estado->estado}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalStatus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Status</h4>
            </div>
            <div class="modal-body text-justify" id="divStatusOportunidad">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<form id="frm_imprimir" action="" method="POST" target="_blank">
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <div class="codigos">

    </div>
</form>



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

    <script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
    <link href='{{asset("assets/lobibox/dist/css/lobibox.min.css")}}' rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}'></script>
    <script src='{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}'></script>

    <script src='{{ asset("mgcp/js/util.js?v=11") }}'></script>
    <script src='{{ asset("mgcp/js/oportunidad/oportunidad-view.js?v=11") }}'></script>
    <script src='{{ asset("mgcp/js/oportunidad/oportunidad-model.js?v=10") }}'></script>
    <script>
        /*$(document).ajaxStart(function() {
            Pace.restart()
        });*/
        $(document).ready(function() {

            //*****INICIALIZACION*****
            Util.seleccionarMenu(window.location);
            Util.activarSoloEnteros();
            Util.activarDatePicker();
            $('input.number').number(true, 2);

            const token = '{{csrf_token()}}';
            const idUsuario = "{{Auth::user()->id}}";
            const permisos = {
                puedeEditar: "{{Auth::user()->tieneRol(5)}}",
                puedeEliminar: "{{Auth::user()->tieneRol(6)}}"
            };
            const oportunidadView = new OportunidadView(new OportunidadModel(token));
            oportunidadView.listarTodas(idUsuario, permisos);
            oportunidadView.editarEvent();
            oportunidadView.eliminarEvent();
            oportunidadView.verStatusEvent();
            Util.activarFiltros('#tableOportunidades', oportunidadView.model);
        });
    </script>
@endsection