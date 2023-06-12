@extends('mgcp.layouts.app')
@section('estilos')
    <style>
        small.help-block {
            margin-bottom: 0px;
        }

        #divProgreso {
            text-align: left !important;
        }
    </style>
@endsection

@section('cabecera') Descargar detalles de O/C públicas @endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Descargar</li>
    <li class="active">O/C públicas</li>
    <li class="active">Detalles</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Configuración</h3>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-1 control-label">Acuerdo:</label>
                <div class="col-sm-4">
                    <select class="form-control" id="selectAcuerdo">
                        @foreach ($acuerdos as $acuerdo)
                        <option value="{{$acuerdo->id}}">{{$acuerdo->descripcion}} {{$acuerdo->descripcion_larga}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <label class="col-sm-2 control-label">Fechas:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control text-center date-picker" id="txtDesde" value="{{date('d-m-Y')}}">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control text-center date-picker" id="txtHasta" value="{{date('d-m-Y')}}">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="row">
                        <div class="col-sm-6">
                            <button id="btnIniciar" class="btn btn-primary">Iniciar</button>
                            <button id="btnSiguiente" class="btn btn-default btn-sm">Siguiente</button>
                        </div>
                        <div class="col-sm-6 control-label" id="divProgreso">Progreso: 0 de 0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Data</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableOrdenes" class="table table-condensed table-bordered table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">Nro.</th>
                        <th class="text-center">ID</th>
                        <th class="text-center">RUC proveedor</th>
                        <th class="text-center">Proveedor</th>
                        <th class="text-center">RUC entidad</th>
                        <th class="text-center">Entidad</th>
                        <th class="text-center">Orden</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Monto</th>
                        <th style="width: 25%" class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody id="tbodyOrdenes">

                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@section('scripts')
    <script src='{{ asset("mgcp/js/util.js") }}'></script>
    <link href='{{asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")}}' rel="stylesheet" type="text/css" />
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

    <script>
        $(document).ready(function() {
            $(".sidebar-mini").addClass("sidebar-collapse");
            Util.seleccionarMenu(window.location);
            Util.activarDatePicker();
            //var id = 0;
            const $divProgreso = $('#divProgreso');
            const $tbody = $('#tbodyOrdenes');
            const $acuerdo = $('#selectAcuerdo');
            const $desde = $('#txtDesde');
            const $hasta = $('#txtHasta');
            let total;
            let contador;

            $('#btnSiguiente').on('click', () => {
                $tbody.find('tr:eq(' + contador + ')').find('td.estado').html('IGNORADO');
                contador++;
                procesar();
            });

            $('#btnIniciar').on('click', () => {
                total = 0;
                contador = 0;
                $acuerdo.prop('disabled', true);
                $desde.prop('disabled', true);
                $hasta.prop('disabled', true);

                //$('#btnIniciar').html(Util.generarPuntosSvg() + 'Procesando').prop('disabled', true);
                $('#btnIniciar').prop('disabled', true);
                $.ajax({
                    url: "{{ route('mgcp.acuerdo-marco.descargar.ordenes-compra-publicas.obtener-detalles') }}",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        acuerdo: $acuerdo.val(),
                        desde: $desde.val(),
                        hasta: $hasta.val(),
                        _token: "{{csrf_token()}}"
                    },
                    success: function(data) {
                        if (data.tipo == 'success') {
                            $tbody.html(data.resultado);
                            total = $tbody.find('td.estado').length;
                            $divProgreso.html('Progreso: ' + contador + ' de ' + total);
                            procesar();
                        } else {
                            alert(data.mensaje);
                        }
                    },
                    error: function() {
                        $acuerdo.prop('disabled', false);
                        $desde.prop('disabled', false);
                        $hasta.prop('disabled', false);
                        $('#btnIniciar').prop('disabled', false);
                        alert("Hubo un problema al obtener los códigos de las O/C. Por favor actualice la página e intente de nuevo")
                    }
                });
            });

            function procesar() {
                if (contador < total) {
                    let $fila = $tbody.find('tr:eq(' + contador + ')');
                    $fila.find('td.estado').html('Procesando...');
                    $.ajax({
                        url: "{{ route('mgcp.acuerdo-marco.descargar.ordenes-compra-publicas.procesar') }}",
                        type: 'post',
                        dataType: 'json',
                        data: {
                            id: $fila.find('td.id').html(),
                            acuerdo: $acuerdo.val(),
                            ruc_proveedor: $fila.find('td.ruc-proveedor').html(),
                            proveedor: $fila.find('td.proveedor').html(),
                            ruc_entidad: $fila.find('td.ruc-entidad').html(),
                            entidad: $fila.find('td.entidad').html(),
                            orden: $fila.find('td.orden').html(),
                            fecha: $fila.find('td.fecha').html(),
                            monto: $fila.find('td.monto').html(),
                            _token: "{{csrf_token()}}"
                        },
                        success: function(data) {
                            $fila.find('td.estado').html('<span class="text-' + data.tipo + '">' + data.mensaje + '</span>');
                        },
                        error: function() {
                            $fila.find('td.estado').html('<span class="text-danger">Error al procesar</span>');
                        },
                        complete: function() {
                            contador++;
                            $divProgreso.html('Progreso: ' + contador + ' de ' + total);
                            procesar();
                        }
                    });
                } else {
                    $acuerdo.prop('disabled', false);
                    $desde.prop('disabled', false);
                    $hasta.prop('disabled', false);
                    $('#btnIniciar').html('Iniciar').prop('disabled', false);
                    alert("Operación finalizada")
                }
            }
        });
    </script>
@endsection