@extends('mgcp.layouts.app')

@section('cabecera') Logs de inicio de sesión @endsection

@section('estilos')
    <link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #modalVerNotificacion div.modal-body div.form-group {
            margin-bottom: 0px !important;
        }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#">Inicio</a></li>
    <li class="active">Usuarios</li>
    <li class="active">Logs</li>
    <li class="active">Inicios de sesión</li>
</ol>
@endsection


@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        <div class="col-sm-12">
            <table style="width: 100%; font-size: small" id="tableDatos" class="table table-condensed table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width: 15%" class="text-center">Fecha</th>
                        <th style="width: 25%" class="text-center">Usuario</th>
                        <th style="width: 15%" class="text-center">IP</th>
                        <th style="width: 10%" class="text-center">País</th>
                        <th style="width: 10%" class="text-center">Región</th>
                        <th style="width: 15%" class="text-center">Ciudad</th>
                        <th class="text-center">Tipo disp.</th>
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
                    <p><small>Seleccione los filtros que desee aplicar y cierre este cuadro para continuar</small></p>
                    <fieldset class="group-table">
                        @csrf
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkUsuario" id="chkUsuario" @if (session('logloginUsuario')!==null) checked @endif> Usuario
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" name="selectUsuario">
                                    @foreach ($usuarios as $usuario)
                                    <option value="{{$usuario->id}}" @if (session('logloginUsuario')==$usuario->id) selected @endif>{{$usuario->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4">
                                <div class="checkbox">
                                    <label title="Fecha">
                                        <input type="checkbox" name="chkFecha" @if (session('logloginDesde')!==null) checked @endif> Fecha
                                    </label>
                                </div>
                            </label>
                            <div class="col-sm-4">
                                <input type="text" name="fechaDesde" class="form-control date-picker" value="@if (session('logloginDesde')!==null){{session('logloginDesde')}}@else{{date('d-m-Y')}}@endif">
                                <small class="help-block">Desde (dd-mm-aaaa)</small>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" name="fechaHasta" class="form-control date-picker" value="@if (session('logloginHasta')!==null){{session('logloginHasta')}}@else{{date('d-m-Y')}}@endif">
                                <small class="help-block">Hasta (dd-mm-aaaa)</small>
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
@endsection

@section('scripts')
    <script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/notificacion/notificacion-model.js?v=4") }}'></script>
    <script src='{{ asset("mgcp/js/acuerdo-marco/notificacion/notificacion-view.js?v=6") }}'></script>
    <script src='{{ asset("mgcp/js/util.js") }}'></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);

            const $tableDatos = $('#tableDatos').DataTable({
                pageLength: 20,
                dom: 'Bfrtip',
                serverSide: true,
                initComplete: function (settings, json) {
                    var $filter = $('#tableDatos_filter');
                    var $input = $filter.find('input');
                    $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                    $input.unbind();
                    $input.bind('keyup', function (e) {
                        if (e.keyCode == 13) {
                            $('#btnBuscar').trigger('click');
                        }
                    });
                    $('#btnBuscar').click(function () {
                        $tableDatos.search($input.val()).draw();
                    });
                },
                drawCallback: function (settings) {
                    $('#tableDatos_filter input').attr('disabled', false);
                    $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                    $('#tableDatos_filter input').focus();
                },
                language: {
                    url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
                columnDefs: [
                ],
                order: [[0, "desc"]],
                ajax: {
                    url: "{{ route('mgcp.usuarios.logs.inicios-sesion.data-lista') }}",
                    type: "POST",
                    data: {_token: "{{csrf_token()}}"},
                },
                columns: [
                    {data: 'fecha', className: 'text-center', searchable: false},
                    {data: 'name', name:'users.name'},
                    {data: 'ip', className: 'text-center'},
                    {data: 'pais', className: 'text-center'},
                    {data: 'region'},
                    {data: 'ciudad'},
                    {data: 'tipo_dispositivo'}
                ],
                buttons: [
                ]
            });

            $tableDatos.on('search.dt', function () {
                $('#tableDatos_filter input').attr('disabled', true);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
            });

            $tableDatos.on('processing.dt', function (e, settings, processing) {
                if (processing) {
                    $(e.currentTarget).LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc"
                    });
                } else {
                    $(e.currentTarget).LoadingOverlay("hide", true);
                }
            });

        });
    </script>
@endsection
