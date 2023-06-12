@extends('mgcp.layouts.app')

@section('cabecera')
Nuevo proyecto
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="#">Inicio</a></li>
    <li class="active">Proyectos</li>
    <li class="active">Nuevo</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        @include('mgcp.partials.flashmsg')
        @include('mgcp.partials.errors')
        <form class="form-horizontal" id="formRegistrarProyecto" role="form" method="POST" action="{{ route('mgcp.proyectos.registrar') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="form-group">
                <label class="col-sm-2 control-label">Código:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control disabled" id="codigo" name="codigo"
                           value="{{ $codigo }}" disabled>
                </div>
                <label class="col-sm-2 control-label">Responsable:</label>
                <div class="col-sm-4">
                    <select name="responsable" class="form-control">
                        @foreach ($responsables as $responsable)
                        <option @if (old('responsable')==$responsable->id) selected @endif value="{{$responsable->id}}">{{$responsable->name}}</option>
                        @endforeach
                    </select>
                </div> 
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Fecha prob. de cierre:</label>
                <div class="col-sm-4">
                    <input type="text" autocomplete="off" value="{{old('fecha_cierre')}}" placeholder="dd-mm-aaaa" required name="fecha_cierre" class="form-control date-picker">
                </div>
                <label class="col-sm-2 control-label">Urgencia:</label>
                <div class="col-sm-4">
                    <select class="form-control" name="urgencia">
                        <option @if (old('urgencia') == 'Alta') selected @endif value="Alta">Alta</option>
                        <option @if (old('urgencia') == 'Media') selected @endif value="Media">Media</option>
                        <option @if (old('urgencia') == 'Baja') selected @endif value="Baja">Baja</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Cliente:</label>
                <div class="col-sm-9">
                    <select name="cliente" id="cboClientes"
                            class="js-example-data-array js-states form-control" tabindex="-1"
                            style="display: none; width: 100%">
                    </select>     
                </div>
                <div class="col-sm-1">
                    <button class="btn btn-default" id="btnNuevoCliente" type="button">Nuevo
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Nombre del proyecto:</label>
                <div class="col-sm-10">
                    <textarea class="form-control" placeholder="Nombre del proyecto" value="{{old('nombre_proyecto')}}" name="nombre_proyecto" required=""></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Monto:</label>
                <div class="col-sm-7">
                    <div class="input-group">
                        <div class="input-group-addon">S/</div>
                        <input type="text" class="form-control number" placeholder="Monto" value="{{old('monto')}}" name="monto" required="">
                    </div>
                </div>
                <label class="col-sm-1 control-label">Contactos:</label>
                <div class="col-sm-2 text-right">
                    <button class="btn btn-default" data-toggle="modal" data-target="#modal_contactos" id="btnContactos" type="button">Agregar contactos</button>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Fase:</label>
                <div class="col-sm-10">
                    <input type="text" disabled class="form-control" value="FASE 1 - ACERCAMIENTO">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Detalle de fase:</label>
                <div class="col-sm-10">
                    <textarea class="form-control" required placeholder="Detalle de fase" name="detalle">{{old('detalle')}}</textarea>
                </div>
            </div>
            <br>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" id="btnRegistrar">Registrar</button>
            </div>

            <div class="modal fade" id="modal_contactos" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Agregar contactos</h4>
                        </div>
                        <div class="modal-body">

                            <div class="box box-solid">
                                <div class="box-body">
                                    <table class="table table-condensed">
                                        <tbody>
                                            <tr class="no-top">
                                                <td>
                                                    <label>Nombre:</label><br>
                                                    <input id="txtNombre" type="text" class="form-control" placeholder="Nombre">
                                                </td>
                                                <td>
                                                    <label>Teléfono:</label><br>
                                                    <input id="txtTelefono" type="text" class="form-control" placeholder="Teléfono">
                                                </td>
                                                <td>
                                                    <label>Correo:</label><br>
                                                    <input id="txtCorreo" type="text" class="form-control" placeholder="Correo">
                                                </td>
                                                <td>
                                                    <label>&nbsp;</label><br>
                                                    <button type="button" id="btnAgregarContacto" class="btn btn-primary">Agregar</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="box box-solid last">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Lista de contactos</h3>
                                </div>
                                <div class="box-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Nombre</th>
                                                <th class="text-left">Teléfono</th>
                                                <th class="text-left">Correo</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody_contactos">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="modalNuevoCliente" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo Cliente</h4>
            </div>
            <div class="modal-body">
                <form id="formCliente" class="form-horizontal">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="ruc" class="col-sm-3 control-label">RUC</label>
                        <div class="col-sm-9">
                            <input type="text" maxlength="11" class="form-control" placeholder="RUC" id="ruc" name="ruc">
                            <small class="help-block">Debe tener máximo 11 dígitos</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="razonsocial" class="col-sm-3 control-label">Razón Social</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control validar" placeholder="Razón social" id="razonsocial"
                                   name="razon_social">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefono" class="col-sm-3 control-label">Teléfono</label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control" placeholder="Teléfono" id="telefono" name="telefono" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="direccion" class="col-sm-3 control-label">Dirección</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="direccion" placeholder="Dirección" name="direccion" />
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
                <button type="button" id="btnClienteRegistrar" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>
<script src='{{ asset("assets/select2/js/select2.min.js") }}'></script>
<link href="{{asset('assets/select2/css/select2.css')}}" rel="stylesheet" type="text/css"/>
<script src='{{ asset("assets/select2/js/i18n/es.js") }}'></script>
<link href="{{asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css"/>
<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>

<script>
    $(document).ready(function () {
        /*$('.date-picker').datepicker({
         language: "es",
         orientation: "top auto",
         format: 'dd-mm-yyyy',
         autoclose: true
         });*/
        Util.seleccionarMenu(window.location);
        Util.activarDatePicker();

        $('#btnAgregarContacto').click(function () {
            var $nombre = $('#txtNombre');
            var $tel = $('#txtTelefono');
            var $correo = $('#txtCorreo');
            if ($nombre.val() == '')
            {
                alert("Ingrese un nombre de contacto");
            } else
            {
                $('#tbody_contactos').append('<tr><td><input type="hidden" name="nombre[]" value="' + $nombre.val() + '">' + $nombre.val() + '</td><td><input type="hidden" name="telefono[]" value="' + $tel.val() + '">' + $tel.val() + '</td><td><input type="hidden" name="correo[]" value="' + $correo.val() + '">' + $correo.val() + '</td><td class="text-center"><span style="cursor: pointer" title="Retirar" class="retirarContacto glyphicon glyphicon-remove" aria-hidden="true"></span></td></tr>')
                $nombre.val('');
                $tel.val('');
                $correo.val('');
            }
        });

        $('#tbody_contactos').on("click", "span.retirarContacto", function () {
            $(this).closest('tr').remove();
        });

        $('input.number').number(true, 2);

        $('#btnClienteRegistrar').click(function () {
            var $modal = $('#modalNuevoCliente');
            $.ajax({
                url: '{{route("mgcp.entidades.registrar")}}',
                data: $('#formCliente').serialize(),
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $modal.find('mensaje').html('<div class="text-center">Procesando...</div>');
                    $modal.find('input').attr('disabled', true);
                    $modal.find('button').attr('disabled', true);
                },
                error: function (xhr, status) {
                    Util.mensaje($modal.find('div.mensaje'), 'danger', 'Hubo un problema al guardar los datos. Por favor actualice la página e intente de nuevo.');
                },
                success: function (data) {
                    Util.mensaje($modal.find('div.mensaje'), data.tipo, data.mensaje);
                    if (data.tipo == 'success')
                    {
                        $modal.find('input[type=text]').val('');
                    }
                },
                complete: function (xhr, status) {
                    $modal.find('input').attr('disabled', false);
                    $modal.find('select').attr('disabled', false);
                    $modal.find('button').attr('disabled', false);
                }
            });
        });

        $("#btnNuevoCliente").click(function () {
            var $modal = $('#modalNuevoCliente');
            $modal.find('input[type=text],input[type=tel]').val('');
            $modal.modal('toggle');
        });

        $('#modalNuevoCliente').on('shown.bs.modal', function () {
            $('#modalNuevoCliente').find('input[name=ruc]').focus();
        });

        $('#formRegistrarProyecto').submit(function () {
            $('#btnRegistrar').html('Registrando...').attr('disabled', true);
        });


        $("#cboClientes").select2({
            language: "es",
            placeholder: "Buscar Cliente",
            //minimumInputLength: 0,
            ajax: {
                url: "{{ route('mgcp.entidades.buscar') }}",
                dataType: 'json',
                delay: 250,
                method: 'POST',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        _token: '{{ csrf_token() }}'
                    };
                },
                processResults: function (data, page) {
                    var lista = new Array();
                    for (i = 0; i < data[0].TotalRows; i++) {
                        lista[i] = {id: data[0].Rows[i][0], text: data[0].Rows[i][2]};
                    }
                    return {
                        results: lista//data[0].Rows
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1
        });
    });
</script>
@endsection