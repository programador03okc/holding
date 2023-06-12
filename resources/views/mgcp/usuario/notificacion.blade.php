@extends('mgcp.layouts.app')

@section('cabecera')
Notificaciones
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#">Inicio</a></li>
    <li class="active">Notificaciones</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        @include('mgcp.partials.flashmsg')
        <div class="col-sm-12">
            <table style="width: 100%; font-size: small" id="tableDatos" class="table table-condensed">
                <thead>
                    <tr>
                        <th style="width: 10%" class="text-center">Fecha</th>
                        <th class="text-center">Mensaje</th>
                        <th style="width: 10%" class="text-center">Enlace visitado</th>
                        <th style="width: 10%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>

    <script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}'></script>
    <script src='{{ asset("mgcp/js/util.js?v=2") }}'></script>
<script>
    $(document).ready(function () {

        Util.seleccionarMenu(window.location);

        var $tableDatos = $('#tableDatos').DataTable({
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
                {orderable: false, targets: [3]},
                {className: "text-center", targets: [0, 2, 3]},
                {render: function (data, type, row) {
                        return row.mensaje;
                    }, targets: 1
                },
                {render: function (data, type, row) {
                        if (row.leido == 1)
                        {
                            return 'Sí';
                        } else
                        {
                            return 'No';
                        }
                    }, targets: 2
                },
                {render: function (data, type, row) {
                        var botones = '<a target="_blank" href="' + "{{url('/mgcp/notificaciones/ver')}}/" + row.id + '" title="Ver" class="btn btn-primary btn-xs visitar"><span class="glyphicon glyphicon-eye-open"></span></a>';
                        botones += ' <button data-id="' + row.id + '" title="Eliminar" class="btn btn-danger btn-xs eliminar"><span class="glyphicon glyphicon-remove"></span></button>'

                        return botones;
                    }, targets: 3
                },
            ],
            order: [[0, "desc"]],
            ajax: {
                url: "{{ route('mgcp.notificaciones.data-lista') }}",
                type: "POST",
                data: {_token: "{{csrf_token()}}"},

            },
            columns: [
                {data: 'fecha', searchable: false},
                {data: 'mensaje'},
                {data: 'leido', searchable: false},
            ],
            buttons: [
            ],
            rowCallback: function (row, data) {
                if (data.leido == '0')
                {
                    $(row).addClass('bg-info');
                }

                /*if (data.cashflow.manual > 0 || data.cashflow.additional_repayment > 0) {
                 $(row).addClass('fontThick');
                 }
                 if (data.cashflow.position !== 'L') {
                 $(row).addClass('selectRow');
                 }*/
            }
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

        $('#tableDatos').on('click', 'a.visitar', function (e) {
            e.preventDefault();
            $(this).closest('tr').removeClass('bg-info');
            $(this).closest('tr').find('td:eq(2)').html("Sí");
            window.open($(this).attr('href'));
        });

        $('#tableDatos').on('click', 'button.eliminar', function (e) {
            var $boton = $(this);
            $.ajax({
                url: '{{route("mgcp.notificaciones.eliminar")}}',
                data: {id: $boton.data('id'), _token: '{{csrf_token()}}'},
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $boton.prop('disabled', true);
                },
                success: function (data) {
                    if (data.tipo == 'success')
                    {
                        $boton.closest('tr').fadeOut(function () {
                            $tableDatos.ajax.reload();
                        });
                    } else
                    {
                        alert(data.mensaje);
                    }
                },
                error: function () {
                    alert("Hubo un problema al eliminar la notificación. Por favor actualice la página e intente de nuevo");
                }
            });
        });
    });
</script>
@endsection