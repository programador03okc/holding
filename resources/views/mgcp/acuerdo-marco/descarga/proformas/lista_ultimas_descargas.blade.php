@extends('mgcp.layouts.app')

@section('cabecera')
    Lista de últimas descargas de proformas
@endsection

@section('estilos')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
        <li class="active">Acuerdo marco</li>
        <li class="active">Descargar</li>
        <li class="active">Proformas</li>
        <li class="active">Lista últ. desc.</li>
    </ol>
@endsection

@section('cuerpo')
    <div class="box box-solid">
        <div class="box-body">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="table-responsive">
                    <table id="tableLista" class="table table-condensed table-hover table-striped table-bordered" style="font-size: small; width: 100%">
                        <thead>
                        <tr>
                            <th style="width: 11%" class="text-center">Empresa</th>
                            <th style="width: 13%" class="text-center">Iniciada por</th>
                            <th style="width: 6%" class="text-center">Fin de última descarga</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($descargas as $descarga)
                                <tr>
                                    <td class="text-center">{{$descarga->empresa}}</td>
                                    <td class="text-center">{{$descarga->usuario}}</td>
                                    <td class="text-center">{{$descarga->fecha_fin}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <link href="{{asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")}}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}"></script>
    <script src="{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}"></script>

    <link href="{{ asset("assets/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset("assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css") }}" rel="stylesheet" type="text/css"/>

    <script src="{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}"></script>
    <script src="{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}"></script>
    <script src="{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}"></script>

    <script src="{{ asset("mgcp/js/util.js") }}"></script>
    <script src="{{ asset("assets/jquery-number/jquery.number.min.js") }}"></script>
    <script src="{{ asset("mgcp/js/actualizar-datos.js") }}"></script>
    <script>
        $(document).ready(function () {

            //*****INICIALIZACION*****

            var url = "{{url('/mgcp')}}";
            var idUsuario = "{{Auth::user()->id}}";
            var puedeEditar = "{{Auth::user()->tieneRol(5)}}";
            var puedeEliminar = "{{Auth::user()->tieneRol(6)}}";
            var actualizarListado = false;

            $('input.number').number(true, 2);

            Util.seleccionarMenu(window.location);
            Util.activarSoloEnteros();
            Util.activarDatePicker();
            //$.fn.dataTable.moment('DD-MM-YYYY');
            /*$('body').on("keypress", ".entero", function (event) {
             Util.soloEnteros(event);
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
                    $tableOportunidades.ajax.reload();
                }
            });

            $('#modalFiltros').find('input[type=checkbox]').change(function () {
                actualizarListado = true;
                ActualizarDatos.filtros("{{ route('mgcp.oportunidades.actualizar-filtros') }}");
            });


            $('#modalFiltros').find('select').change(function () {
                if ($(this).closest('div.form-group').find('input[type=checkbox]').is(':checked'))
                {
                    actualizarListado = true;
                    ActualizarDatos.filtros("{{ route('mgcp.oportunidades.actualizar-filtros') }}");
                }
            });

            $('#modalFiltros').find('input.date-picker').change(function () {
                if ($(this).closest('div.form-group').find('input[type=checkbox]').is(':checked'))
                {
                    actualizarListado = true;
                    ActualizarDatos.filtros("{{ route('mgcp.oportunidades.actualizar-filtros') }}");
                }
            });
            //*****FIN FILTROS*****

            //*****LISTADO DE OPORTUNIDADES*****
            var $tableOportunidades = $('#tableOportunidades').DataTable({
                pageLength: 50,
                dom: 'Bfrtip',
                processing: true,
                serverSide: true,
                initComplete: function (settings, json) {
                    var $filter = $('#tableOportunidades_filter');
                    var $input = $filter.find('input');
                    $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                    $input.unbind();
                    $input.bind('keyup', function (e) {
                        if (e.keyCode == 13) {
                            $('#btnBuscar').trigger('click');
                        }
                    });
                    $('#btnBuscar').click(function () {
                        $tableOportunidades.search($input.val()).draw();
                    });
                },
                drawCallback: function (settings) {
                    $('#tableOportunidades_filter input').attr('disabled', false);
                    $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                    $('#tableOportunidades_filter input').focus();
                },
                language: {
                    url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
                order: [[6, "desc"]],
                ajax: {
                    url: "{{ route('mgcp.oportunidades.data-lista') }}",
                    type: "POST",
                    data: {_token: "{{csrf_token()}}"},
                    complete: function () {
                        ActualizarDatos.contarFiltros();
                    }
                },
                columns: [
                    {data: 'entidad', name: 'entidades.entidad'},
                    {data: 'oportunidad'},
                    {data: 'probabilidad', searchable:false},
                    {data: 'oportunidad'},
                    {data: 'importe', searchable:false},
                    {data: 'created_at', searchable:false},
                    {data: 'fecha_limite', searchable:false},
                    {data: 'margen', searchable:false},
                    {data: 'name', name: 'users.name'},
                    {data: 'estado', name: 'estados.estado'},
                    {data: 'grupo', name: 'grupos.grupo'},
                    {data: 'tipo', name: 'tipos_negocio.tipo'},
                    {data: 'codigo_oportunidad'}
                ],
                columnDefs: [
                    {orderable: false, targets: [3, 12]},
                    {className: "text-center", targets: [2, 4, 5, 6, 7, 8, 9, 10, 11, 12]},
                    {
                        targets: 6,
                        createdCell: function (td, cellData, rowData, row, col) {
                            /*console.log(rowData.estado_id+" - "+rowData.dias_diferencia);
                             if (rowData.estado_id < 4 && (rowData[12] == 1 || rowData[12] == 2 || rowData[12] == 3))
                             {
                             td.setAttribute('class', 'danger text-center');
                             }*/
                        }
                    },
                    {render: function (data, type, row) {
                            return '<a class="azul" href="' + url + '/oportunidades/detalles/' + row.id + '"><strong class="underline">' + row.codigo_oportunidad + '</strong><br>' + row.oportunidad + '</a>';
                        }, targets: 1
                    },
                    {render: function (data, type, row) {
                            return row.probabilidad.charAt(0).toUpperCase() + row.probabilidad.slice(1);
                        }, targets: 2
                    },
                    {render: function (data, type, row) {
                            if (row.ultimo_status.length > 100)
                            {
                                return row.ultimo_status.substring(0, 105) + '...<a class="verStatus" href="#" data-status="' + row.ultimo_status.split('"').join('') + '">Ver más</a>';
                            } else
                            {
                                return row.ultimo_status;
                            }
                        }, targets: 3
                    },
                    {render: function (data, type, row) {
                            return row.monto;
                        }, targets: 4
                    },
                    {render: function (data, type, row) {
                            return row.margen + '%';
                        }, targets: 7
                    },
                    {render: function (data, type, row) {
                            var botones = '';
                            if (idUsuario == row.id_responsable || puedeEditar == 1)
                            {
                                botones += '<button style="margin-right: 2px" data-id="' + row.id + '" class="btn btn-primary editar btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>';
                            }
                            if (puedeEliminar == 1)
                            {
                                botones += '<button data-oportunidad="' + row.oportunidad.split('"').join('') + '" data-id="' + row.id + '" class="btn btn-danger eliminar btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
                            }
                            return botones;
                        }, targets: 12
                    }
                ],
                buttons: [
                    {
                        text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                        action: function () {
                            $('#modalFiltros').modal('show');
                        },
                        className: 'btn-sm'
                    }
                ]
            });

            $tableOportunidades.on('search.dt', function () {
                $('#tableOportunidades_filter input').attr('disabled', true);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
            });
            //*****FIN LISTADO DE OPORTUNIDADES*****

            //*****EDITAR OPORTUNIDAD*****
            $('#tableOportunidades tbody').on("click", "button.editar", function () {
                var codigo = $(this).data('id');
                var $modal = $('#modalEditar');
                var $aceptar = $('#btnEditarAceptar');
                $modal.modal('show');
                $modal.find('div.mensaje').html('<div class="text-center">Obteniendo datos...</div>');
                $aceptar.prop('disabled', true);
                $.ajax({
                    url: '{{route("mgcp.oportunidades.json-detalles")}}',
                    data: {id: codigo, _token: '{{csrf_token()}}'},
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        $modal.find('input[type=text]').val('').prop('disabled', true);
                        $modal.find('textarea').val('').prop('disabled', true);
                        $modal.find('select').prop('disabled', true);
                    },
                    success: function (json) {
                        $('#txt_codigo').val(codigo);
                        $('#spanCodigoOportunidad').html(json.codigo_oportunidad);
                        $modal.find('select[name=cliente]').val(json.id_entidad);
                        $modal.find('textarea[name=oportunidad]').val(json.oportunidad);
                        $modal.find('select[name=responsable]').val(json.id_responsable);
                        $modal.find('select[name=probabilidad]').val(json.probabilidad);
                        $modal.find('select[name=tipo_moneda]').val(json.moneda);
                        $modal.find('input[name=importe]').val(json.importe);
                        $modal.find('input[name=margen]').val(json.margen);
                        $modal.find('input[name=fecha_limite]').val(json.fecha_limite);
                        $modal.find('input[name=nombre_contacto]').val(json.nombre_contacto);
                        $modal.find('input[name=cargo_contacto]').val(json.cargo_contacto);
                        $modal.find('input[name=telefono_contacto]').val(json.telefono_contacto);
                        $modal.find('input[name=correo_contacto]').val(json.correo_contacto);
                        $modal.find('select[name=grupo]').val(json.id_grupo);
                        $modal.find('select[name=tipo_negocio]').val(json.id_tipo_negocio);
                        $modal.find('input[name=reportado_por]').val(json.reportado_por);
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
                /*if (Util.camposVacios($modal))
                 {
                 return false;
                 }*/
                $.ajax({
                    url: '{{route("mgcp.oportunidades.actualizar")}}',
                    data: $modal.find('form').serialize(),
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        //$modal.find('div.mensaje').html('<div class="text-center">Procesando...</div>');
                        //$modal.find('input[type=text],textarea,select,button').attr('disabled', true);
                        /*$modal.find('textarea').attr('disabled', true);
                         $modal.find('select').attr('disabled', true);*/
                        Util.mensaje($modal.find('div.mensaje'), 'info', 'Procesando...');
                        $modal.find('button').attr('disabled', true);
                    },
                    error: function (xhr, status) {
                        Util.mensaje($modal.find('div.mensaje'), 'danger', 'Hubo un problema al actualizar los datos. Por favor actualice la página e inténte de nuevo');
                        //$modal.find('div.mensaje').html('<strong>Hubo un error al editar los datos. Por favor actualice la página e inténte de nuevo.</strong>');
                    },
                    success: function (data) {
                        if (data.tipo == 'success')
                        {
                            $tableOportunidades.ajax.reload();
                        }
                        Util.mensaje($modal.find('div.mensaje'), data.tipo, data.mensaje)
                        /*if (json.mensaje == 'editado')
                         {
                         alert("Oportunidad editada correctamente.")
                         $modal.modal('hide');
                         $tableOportunidades.ajax.reload();
                         } else
                         {
                         util.mensaje($modal, json.mensaje);
                         }*/
                    },
                    complete: function (xhr, status) {
                        $modal.find('button').attr('disabled', false);
                        /*$modal.find('input[type=text]').prop('disabled', false);
                         $modal.find('textarea').prop('disabled', false);
                         $modal.find('select').prop('disabled', false);
                         $modal.find('button').prop('disabled', false);*/
                        //$modal.find('input[type=text],textarea,select,button').attr('disabled', false);
                    }
                });
            });
            //*****FIN EDITAR OPORTUNIDAD*****

            //*****VER STATUS*****
            $('#tableOportunidades tbody').on("click", "a.verStatus", function (e) {
                e.preventDefault();
                $('#modalStatus').modal('show');
                $('#divStatusOportunidad').html($(this).data('status'));
            });
            //*****FIN VER STATUS*****

            //*****ELIMINAR*****
            $('#tableOportunidades tbody').on("click", "button.eliminar", function (e) {
                var $modal = $('#modalConfirmarEliminar');
                $modal.find('div.oportunidad').html('<strong>Oportunidad:</strong> ' + $(this).data('oportunidad'));
                $modal.modal('show');
                $('#btnEliminarAceptar').data('id', $(this).data('id'));
            });
            $('#btnEliminarAceptar').click(function () {
                var $modal = $('#modalConfirmarEliminar');
                var $boton = $(this);
                $modal.find('button').prop('disabled', true);
                $.ajax({
                    url: '{{route("mgcp.oportunidades.eliminar")}}',
                    data: {id: $boton.data('id'), _token: '{{csrf_token()}}'},
                    type: 'POST',
                    dataType: 'json',
                    error: function (xhr, status) {
                        alert('Hubo un problema al eliminar la oportunidad. Por favor actualice la página e intente de nuevo.');
                    },
                    success: function (data) {
                        alert(data.mensaje);
                        if (data.tipo == 'success')
                        {
                            $tableOportunidades.ajax.reload();
                            $modal.modal('hide');
                        }
                        /*switch (json.mensaje)
                         {
                         case 'eliminado':
                         alert("La oportunidad se ha eliminado correctamente.");
                         $tableOportunidades.ajax.reload();
                         $modal.modal('hide');
                         break;
                         case 'error_eliminar':
                         alert("Error: No se pudo eliminar la oportunidad. Actualice la página e inténtelo nuevamente.");
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
