@extends('mgcp.layouts.app')
@section('estilos')
<link href="{{ asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css"/>

<link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
<style>
    /*.panel .panel-body {
        padding: 4px !important;
    }
    #main-wrapper {
        margin: 5px !important;
    }
    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th, .table td {
        padding: 5px !important;
        vertical-align: middle !important;
    }*/
    td.danger {
        background-color: #f2dede !important;
    }
    strong.underline {
        text-decoration: underline;
    }
</style>
@endsection

@section('cabecera')
Lista de proyectos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#">Inicio</a></li>
    <li class="active">Proyectos</li>
    <li class="active">Lista</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">

        <div class="table-responsive">
            <table id="tableProyectos" class="table table-bordered table-condensed table-hover table-striped" style="font-size: small; width: 100%">
                <thead>
                    <tr>
                        <th width="12%" class="text-center">Cliente</th>
                        <th width="12%" class="text-center">Proyecto</th>
                        <th width="9%" class="text-center">Monto</th>
                        <th width="14%" class="text-center">Último status</th>
                        <th width="8%" class="text-center">Actualizado</th>
                        <th width="10%" class="text-center">Responsable</th>
                        <th width="7%" class="text-center">Estado</th>
                        <th width="7%" class="text-center">Fase</th>
                        <th width="7%" class="text-center">Fecha<br>cierre</th>
                        <th width="7%" class="text-center">Urgencia</th>
                        <th width="7%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirmar eliminación</h4>
            </div>
            <div class="modal-body proyecto">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarAceptar" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar proyecto <span id="spanCodigoProyecto"></span></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" id="txtId">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <select name="cliente" class="form-control">
                                @foreach ($clientes as $cliente)
                                <option value="{{$cliente->id}}">{{$cliente->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Proyecto</label>
                        <div class="col-sm-10">
                            <textarea name="nombre" class="form-control validar" placeholder="Nombre del proyecto"></textarea>
                        </div>
                    </div>
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
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Monto</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-addon">S/</div>
                                <input type="text" class="form-control number" placeholder="Monto" name="monto" required="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">F. cierre</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="dd-mm-aaaa" required name="fecha_cierre" class="form-control date-picker validar">
                        </div>
                        <label class="col-sm-2 control-label">Urgencia</label>
                        <div class="col-sm-4">
                            <select name="urgencia" class="form-control">
                                <option value="Alta">Alta</option>
                                <option value="Media">Media</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Estado</label>
                        <div class="col-sm-10">
                            <select name="estado" class="form-control">
                                @foreach ($estados as $estado)
                                <option value="{{$estado->id}}">{{$estado->estado}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 mensaje">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" id="btnEditarAceptar" class="btn btn-primary">Guardar</button>
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
                    {{csrf_field()}}
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFechaCierre" @if (session('proy_fecha_cierre_desde')!==null) checked @endif> Fecha cierre
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" value="@if(session('proy_fecha_cierre_desde')!==null){{session('proy_fecha_cierre_desde')}}@else{{date('d-m-Y')}}@endif" class="form-control date-picker" name="fechaCierreDesde" placeholder="dd-mm-aaaa">
                            <small class="help-block">Desde</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" value="@if(session('proy_fecha_cierre_hasta')!==null){{session('proy_fecha_cierre_hasta')}}@else{{date('d-m-Y')}}@endif" class="form-control date-picker" name="fechaCierreHasta" placeholder="dd-mm-aaaa">
                            <small class="help-block">Hasta</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkResponsable" @if (session('proy_responsable')!==null) checked @endif> Responsable
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="selectResponsable">
                                @foreach ($responsables as $responsable)
                                <option value="{{$responsable->id}}" @if (session('proy_responsable')==$responsable->id) selected @endif>{{$responsable->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstado" @if (session('proy_estado')!==null) checked @endif> Estado
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="selectEstado">
                                @foreach ($estados as $estado)
                                <option value="{{$estado->id}}" @if (session('proy_estado')=='Alta') selected @endif>{{$estado->estado}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkUrgencia" @if (session('proy_urgencia')!==null) checked @endif> Urgencia
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="selectUrgencia">
                                <option value="Alta" @if (session('proy_urgencia')=='Alta') selected @endif>Alta</option>
                                <option value="Media" @if (session('proy_urgencia')=='Media') selected @endif>Media</option>
                                <option value="Baja" @if (session('proy_urgencia')=='Baja') selected @endif>Baja</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
            <div class="modal-body text-justify" id="divStatusProyecto">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

<script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
<script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
<script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
<script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>

<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
<script src='{{ asset("mgcp/js/actualizar-datos.js") }}'></script>
<script>
    $(document).ready(function () {

        //*****INICIALIZACION*****
        var url = "{{url('/mgcp')}}";
        var idUsuario = "{{Auth::user()->id}}";
        var puedeEditar = "{{Auth::user()->tieneRol(13)}}";
        var puedeEliminar = "{{Auth::user()->tieneRol(14)}}";
        var actualizarListado = false;
        Util.seleccionarMenu(window.location);
        Util.activarDatePicker();
        Util.activarSoloEnteros();

        $('input.number').number(true, 2);
        //$.fn.dataTable.moment('DD-MM-YYYY');
        /*$('body').on("keypress", ".entero", function (event) {
         util.soloEnteros(event);
         });*/

        /*$('.date-picker').datepicker({
         language: "es",
         orientation: "top auto",
         format: 'dd-mm-yyyy',
         autoclose: true
         });*/

        ActualizarDatos.init('{{csrf_token()}}');
        //*****FIN INICIALIZACION*****

        //****FILTROS****
        $('#modalFiltros').on('hidden.bs.modal', function (e) {
            if (actualizarListado)
            {
                actualizarListado = false;
                $tableProyectos.ajax.reload();
            }
        });

        $('#modalFiltros').find('input[type=checkbox]').change(function () {
            actualizarListado = true;
            ActualizarDatos.filtros("{{ route('mgcp.proyectos.actualizar-filtros') }}");
        });


        $('#modalFiltros').find('select').change(function () {
            if ($(this).closest('div.form-group').find('input[type=checkbox]').is(':checked'))
            {
                actualizarListado = true;
                ActualizarDatos.filtros("{{ route('mgcp.proyectos.actualizar-filtros') }}");
            }
        });

        $('#modalFiltros').find('input.date-picker').change(function () {
            if ($(this).closest('div.form-group').find('input[type=checkbox]').is(':checked'))
            {
                actualizarListado = true;
                ActualizarDatos.filtros("{{ route('mgcp.proyectos.actualizar-filtros') }}");
            }
        });
        //*****FIN FILTROS*****

        //*****LISTADO DE PROYECTOS*****
        var $tableProyectos = $('#tableProyectos').DataTable({
            pageLength: 50,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableProyectos_filter');
                var $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.unbind();
                $input.bind('keyup', function (e) {
                    if (e.keyCode == 13) {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').click(function () {
                    $tableProyectos.search($input.val()).draw();
                });
                $input.focus();
            },
            drawCallback: function (settings) {
                $('#tableProyectos_filter input').attr('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableProyectos_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[8, "desc"]],
            ajax: {
                url: "{{ route('mgcp.proyectos.data-lista') }}",
                type: "POST",
                data: {_token: "{{csrf_token()}}"},
                complete: function () {
                    ActualizarDatos.contarFiltros();
                }
            },
            columns: [
                {data: 'razon_social', name: 'entidades.razon_social'},
                {data: 'codigo_proyecto'},
                {data: 'monto', searchable: false},
                {data: 'detalles', name: 'fases.detalles'},
                {data: 'fecha', name: 'fases.fecha', searchable: false},
                {data: 'name', name: 'users.name'},
                {data: 'estado' , name: 'estados.estado'},
                {data: 'porcentaje',name:'descripciones_fases.porcentaje', searchable: false},
                {data: 'fecha_cierre', searchable: false},
                {data: 'urgencia'},
                {data: 'nombre'}
            ],
            columnDefs: [
                {orderable: false, targets: [3, 10]},
                {className: "text-center", targets: [4, 6, 7, 8, 9, 10]},
                {className: "text-justify", targets: [1, 3]},
                {className: "text-right", targets: [2]},
                {
                    targets: 8,
                    createdCell: function (td, cellData, rowData, row, col) {
                        if (rowData.id_fase < 4 && (rowData.estado == 'En progreso'))
                        {
                            td.setAttribute('class', 'danger text-center');
                        }
                    }
                },
                {render: function (data, type, row) {
                        return '<a href="' + url + '/proyectos/detalles/' + row.id + '"><strong class="underline">' + row.codigo_proyecto + '</strong><br>' + row.nombre + '</a>';
                    }, targets: 1
                },
                {render: function (data, type, row) {
                        return row.monto_format;
                    }, targets: 2
                },
                {render: function (data, type, row) {
                        return row.porcentaje + '%';
                    }, targets: 7
                },
                /*{render: function (data, type, row) {
                        return row[14];
                    }, targets: 8
                },*/
                {render: function (data, type, row) {
                        var botones = '';
                        if (idUsuario == row.id_usuario || puedeEditar == 1)
                        {
                            botones += '<button data-id="' + row.id + '" class="btn btn-primary editar btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>';
                        }
                        if (puedeEliminar == 1)
                        {
                            botones += '<button data-proyecto="'+row.nombre.split('"').join('')+'" data-id="' + row.id + '" class="btn btn-danger eliminar btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
                        }
                        return botones;
                    }, targets: 10
                }
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                    action: function () {
                        $('#modalFiltros').modal('show');
                    },
                    className: 'btn btn-sm'
                }
            ]
        });

        $tableProyectos.on('search.dt', function () {
            $('#tableProyectos_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
        //*****FIN LISTADO DE PROYECTOS*****

        //*****EDITAR PROYECTO*****
        $('#tableProyectos tbody').on('click', 'button.editar', function () {
            var id = $(this).data('id');
            var $modal = $('#modalEditar');
            var $aceptar = $('#btnEditarAceptar');
            $modal.modal('show');
            $modal.find('div.mensaje').html('<div class="text-center">Obteniendo datos...</div>');
            $aceptar.prop('disabled', true);
            $.ajax({
                url: '{{route("mgcp.proyectos.ajax-detalles")}}',
                data: {id: id, _token: '{{csrf_token()}}'},
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $modal.find('input[type=text]').val('').prop('disabled', true);
                    $modal.find('textarea').val('').prop('disabled', true);
                    $modal.find('select').prop('disabled', true);
                },
                success: function (json) {
                    $('#txtId').val(id);
                    $('#spanCodigoProyecto').html(json.codigo_proyecto);
                    $modal.find('select[name=cliente]').val(json.id_entidad);
                    $modal.find('textarea[name=nombre]').val(json.nombre);
                    $modal.find('select[name=responsable]').val(json.id_responsable);
                    $modal.find('input[name=monto]').val(json.monto);
                    $modal.find('input[name=fecha_cierre]').val(json.fecha_cierre);
                    $modal.find('select[name=estado]').val(json.id_estado);
                    $modal.find('select[name=urgencia]').val(json.urgencia);

                    $modal.find('input[type=text]').prop('disabled', false);
                    $modal.find('textarea').prop('disabled', false);
                    $modal.find('select').prop('disabled', false);
                    $modal.find('div.mensaje').html('');
                    $aceptar.prop('disabled', false);
                },
                error: function (xhr, status) {
                    $modal.find('div.mensaje').html('Error al obtener datos. Por favor actualice la página e inténtelo de nuevo.');
                }
            });
        });

        $('#btnEditarAceptar').click(function () {
            var $modal = $('#modalEditar');
            /*if (util.camposVacios($modal))
             {
             return false;
             }*/
            $.ajax({
                url: '{{route("mgcp.proyectos.actualizar")}}',
                data: $modal.find('form').serialize(),
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $modal.find('div.mensaje').html('<div class="text-center">Procesando...</div>');
                    $modal.find('input[type=text]').attr('disabled', true);
                    $modal.find('textarea').attr('disabled', true);
                    $modal.find('select').attr('disabled', true);
                    $modal.find('button').attr('disabled', true);
                },
                error: function (xhr, status) {
                    Util.mensaje($modal.find('div.mensaje'), 'danger', 'Hubo un error al editar los datos. Por favor actualice la página e inténte de nuevo.');
                    //$modal.find('div.mensaje').html('<strong>Hubo un error al editar los datos. Por favor actualice la página e inténte de nuevo.</strong>');
                },
                success: function (data) 
                {  
                    Util.mensaje($modal.find('div.mensaje'), data.tipo, data.mensaje);
                    if (data.tipo == 'success')
                    {
                        $tableProyectos.ajax.reload();
                    }
                },
                complete: function (xhr, status) {
                    $modal.find('input[type=text]').prop('disabled', false);
                    $modal.find('textarea').prop('disabled', false);
                    $modal.find('select').prop('disabled', false);
                    $modal.find('button').prop('disabled', false);
                }
            });
        });
        //*****FIN EDITAR PROYECTO*****

        //*****ELIMINAR*****
        $('#tableProyectos tbody').on("click", "button.eliminar", function (e) {
            var $modal = $('#modalConfirmarEliminar');
            $modal.find('div.proyecto').html('<strong>Proyecto:</strong> ' + $(this).data('proyecto'));
            $modal.modal('show');
            $('#btnEliminarAceptar').data('id', $(this).data('id'));
        });
        $('#btnEliminarAceptar').click(function () {
            var $modal = $('#modalConfirmarEliminar');
            var $boton = $(this);
            $modal.find('button').prop('disabled', true);
            $.ajax({
                url: '{{route("mgcp.proyectos.eliminar")}}',
                data: {id: $boton.data('id'), _token: '{{csrf_token()}}'},
                type: 'POST',
                dataType: 'json',
                error: function (xhr, status) {
                    alert('Error: No se pudo eliminar el proyecto. Actualice la página e inténte de nuevo.');
                },
                success: function (data) {
                    alert(data.mensaje);
                    if (data.tipo=='success')
                    {
                        $tableProyectos.ajax.reload();
                        $modal.modal('hide');
                    }
                    /*switch (json.mensaje)
                    {
                        case 'eliminado':
                            alert("El proyecto se ha eliminado correctamente.");
                            $tableProyectos.ajax.reload();
                            $modal.modal('hide');
                            break;
                        case 'error_eliminar':
                            alert("Error: No se pudo eliminar el proyecto. Actualice la página e inténtelo nuevamente.");
                            break;
                    }*/
                },
                complete: function () {
                    $modal.find('button').prop('disabled', false);
                }
            });
        });
        //*****FIN ELIMINAR*****
    });

</script>
@endsection