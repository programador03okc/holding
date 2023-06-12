@extends('mgcp.layouts.app')

@section('cabecera') Fondo de Microsoft @endsection

@section('estilos')
    <link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/lobibox/dist/css/lobibox.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .mb-3 {
            margin-bottom: 10px;
        }
        .mb-4 {
            margin-bottom: 15px;
        }
        .mb-5 {
            margin-bottom: 20px !important;
        }
        .importe {
            margin: 0;
            margin-bottom: 20px;
            color: #7c7b7b;
        }
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="#">Inicio</a></li>
        <li class="active">Cuadros de presupuesto</li>
        <li class="active">Ajustes</li>
        <li class="active">Fondo de Microsoft</li>
    </ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-header with-border"><h3 class="box-title">Lista de fondos de microsoft</h3></div>
    <div class="box-body" id="divContenedor">
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="form-inline text-center">
                    <button type="button" class="btn btn-sm btn-default" onclick="abrirModalBolsa();">
                        <span class="fa fa-dollar"></span> Crear bolsa
                    </button>
                    <button type="button" class="btn btn-sm btn-default" onclick="abrirModalFondo();">
                        <span class="fa fa-plus"></span> Crear fondo
                    </button>
                    <button type="button" class="btn btn-sm btn-default" onclick="abrirModalMovimiento();">
                        <span class="fa fa-exchange"></span> Nuevo movimiento
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row" id="resultado-fondo"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-bolsa" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
            <form class="form-horizontal bloquear-boton" id="form-bolsa" method="POST">
                @csrf

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar tipo de bolsa</h4>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5>Descripción</h5>
                            <input type="text" name="bolsa_descripcion" class="form-control input-sm" placeholder="Ingresar descripción" required>
                        </div>
                        <div class="col-md-4">
                            <h5>Importe</h5>
                            <input type="text" name="bolsa_importe" class="form-control input-sm text-right number" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnBolsa">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-fondo" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form class="form-horizontal bloquear-boton" id="form-fondo" method="POST">
                @csrf

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar fondo de microsoft</h4>
                </div>
                <div class="modal-body" id="divContenedorFondo">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h5>Part Number</h5>
                            <input type="text" name="fondo_part_no" class="form-control input-sm" placeholder="Ingresar part number" required>
                        </div>
                        <div class="col-md-8">
                            <h5>Descripción</h5>
                            <input type="text" name="fondo_descripcion" class="form-control input-sm" placeholder="Ingresar descripción" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h5>Tipo de Fondo</h5>
                            <select name="tipo_fondo" class="form-control input-sm" required>
                                <option value="" disabled selected>Elija una opción</option>
                                <option value="VARIABLE">VARIABLE</option>
                                <option value="FIJO">FIJO</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Tipo de bolsa</h5>
                            <select name="tipo_bolsa_id" class="form-control input-sm tipo_bolsa"></select required>
                        </div>
                        <div class="col-md-4">
                            <h5>Importe</h5>
                            <input type="text" name="fondo_importe" class="form-control input-sm text-right number" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnFondo">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-movimiento" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
            <form class="form-horizontal bloquear-boton" id="form-movimiento" method="POST">
                @csrf

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar movimiento de fondos</h4>
                </div>
                <div class="modal-body" id="divContenedorMovimiento">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Fecha</h5>
                            <input type="date" name="mov_fecha" class="form-control input-sm text-center" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <h5>Tipo de movimiento</h5>
                            <select name="tipo_movimiento" class="form-control input-sm" onchange="tipoMovimiento(this.value);" required>
                                <option value="" selected disabled>Elija una opción</option>
                                <option value="1">Asignación de Fondo</option>
                                <option value="2">Transferencia</option>
                                <option value="3">Salida</option>
                                <option value="4">Incremento de Bolsa</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6 bolsa combos d-none">
                            <h5>Bolsa</h5>
                            <select name="bolsa_id" class="form-control input-sm bolsa_id"></select>
                        </div>
                        <div class="col-md-6 origen combos d-none">
                            <h5>Fondo Origen</h5>
                            <select name="fondo_microsoft_origen_id" class="form-control input-sm fn-origen"></select>
                        </div>
                        <div class="col-md-6 destino combos d-none">
                            <h5>Fondo Destino</h5>
                            <select name="fondo_microsoft_destino_id" class="form-control input-sm fn-destino"></select>
                        </div>
                        <div class="col-md-6">
                            <h5>Importe</h5>
                            <input type="text" name="mov_importe" class="form-control input-sm text-center number" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <textarea name="mov_descripcion" class="form-control input-sm" rows="5" style="resize: none;" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarMovimiento();">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>
    <script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('mgcp/js/util.js?v=2') }}"></script>
    <script>
        const token = '{{ csrf_token() }}';
        $(document).ready(function () {
            $(".sidebar-mini").addClass("sidebar-collapse");
            Util.seleccionarMenu(window.location);
            $('input.number').number(true, 2);
            listar();

            $('#divContenedor').on('click', 'button.mostrar', (e) => {
                const $boton = $(e.currentTarget);
                $boton.removeClass('mostrar').addClass('ocultar').html('<span class="fa fa-minus"></span>')
                $boton.closest('div.box').find('div.box-body').fadeIn(300);
            });
    
            $('#divContenedor').on('click', 'button.ocultar', (e) => {
                const $boton = $(e.currentTarget);
                $boton.removeClass('ocultar').addClass('mostrar').html('<span class="fa fa-plus"></span>')
                $boton.closest('div.box').find('div.box-body').fadeOut(300);
            });

            $("#form-bolsa").on("submit", function() {
                const $boton = $("#btnBolsa");
                $boton.attr('disabled', true);
                $boton.html(Util.generarPuntosSvg() + 'Registrando');
                var data = $(this).serializeArray();

                $.ajax({
                    type: "POST",
                    url : route('mgcp.cuadro-costos.ajustes.fondos-microsoft.registrar-bolsa'),
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        if (response.response == 'ok') {
                            listar();
                            $('#modal-fondo').modal('hide');
                        }
                        Util.notify(response.alert, response.message);
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });

            $("#form-fondo").on("submit", function() {
                const $boton = $("#btnFondo");
                $boton.attr('disabled', true);
                $boton.html(Util.generarPuntosSvg() + 'Registrando');
                var data = $(this).serializeArray();

                $.ajax({
                    type: "POST",
                    url : route('mgcp.cuadro-costos.ajustes.fondos-microsoft.registrar-fondo'),
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        if (response.response == 'ok') {
                            listar();
                            $('#modal-bolsa').modal('hide');
                        }
                        Util.notify(response.alert, response.message);
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });
        });

        function listar() {
            const $contenedor = $('#divContenedor');
            $.ajax({
                type: 'GET',
                url: route('mgcp.cuadro-costos.ajustes.fondos-microsoft.listar'),
                dataType: 'JSON',
                beforeSend: function(){
                    $contenedor.LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc",
                        zIndex: 10
                    });
                },
                success: function (response) {
                    $contenedor.LoadingOverlay("hide", true);
                    $("#resultado-fondo").html(response);
                }
            }).fail( function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }

        function listarCombo(tipo, modelo = 0) {
            var $contenedor = '';
            if (tipo == 'bolsa') {
                $contenedor = $('#divContenedorFondo');
            } else {
                $contenedor = $('#divContenedorMovimiento');
            }
            var row = '';
            $.ajax({
                type: 'POST',
                url: route('mgcp.cuadro-costos.ajustes.fondos-microsoft.listar-combo'),
                data: {tipo: tipo, _token: token},
                dataType: 'JSON',
                beforeSend: function(){
                    $contenedor.LoadingOverlay("show", {
                        imageAutoResize: true,
                        progress: true,
                        imageColor: "#3c8dbc",
                        zIndex: 10
                    });
                },
                success: function (response) {
                    $contenedor.LoadingOverlay("hide", true);
                    if (response.length > 0) {
                        response.forEach(function (element, index) {
                            row += `<option value="`+ element.id +`">`+ element.descripcion +`</option>`;
                        });
                    }
                    if (tipo == 'bolsa') {
                        if (modelo > 0) {
                            $('.bolsa_id').html(row);
                        } else {
                            $('.tipo_bolsa').html(row);
                        }
                    } else {
                        if (modelo > 0) {
                            $('.fn-destino').html(row);
                        } else {
                            $('.fn-origen').html(row);
                            $('.fn-destino').html(row);
                        }
                    }
                }
            }).fail( function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }

        function guardarMovimiento() {
            var data = $('#form-movimiento').serializeArray();
            $.ajax({
                type: "POST",
                url : route('mgcp.cuadro-costos.ajustes.fondos-microsoft.registrar-movimiento'),
                data: data,
                dataType: "JSON",
                success: function (response) {
                    if (response.response == 'ok') {
                        listar();
                    }
                    Util.notify(response.alert, response.message);
                }
            }).fail( function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }

        function abrirModalBolsa() {
            $("#btnBolsa").attr('disabled', false);
            $('#form-bolsa')[0].reset();
            $('#modal-bolsa').modal('show');
        }

        function abrirModalFondo() {
            $('#form-fondo')[0].reset();
            listarCombo('bolsa');
            $('#modal-fondo').modal('show');
        }

        function abrirModalMovimiento() {
            $('#form-movimiento')[0].reset();
            listarCombo('fondo');
            $('#modal-movimiento').modal('show');
        }

        function tipoMovimiento(value) {
            $('.combos').addClass('d-none');
            switch (value) {
                case '1':
                    listarCombo('fondo', 1);
                    $('.destino').removeClass('d-none');
                break;
                case '2':
                    listarCombo('fondo', 0);
                    $('.origen').removeClass('d-none');
                    $('.destino').removeClass('d-none');
                break;
                case '3':
                    listarCombo('fondo', 1);
                    $('.destino').removeClass('d-none');
                break;
                case '4':
                    listarCombo('bolsa', 1);
                    $('.bolsa').removeClass('d-none');
                break;
            }
        }
    </script>
@endsection