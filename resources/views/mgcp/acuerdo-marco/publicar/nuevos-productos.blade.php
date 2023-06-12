@extends('mgcp.layouts.app')
@section('contenido')

@section('cabecera')
Publicar nuevos productos
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Publicar</li>
    <li class="active">Nuevos productos</li>
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
                <label class="col-sm-1 control-label">Empresa:</label>
                <div class="col-sm-3">
                    <select class="form-control" id="selectEmpresa" name="empresa">
                        @foreach($empresas as $empresa)
                        <option value="{{$empresa->id}}">{{$empresa->empresa}}</option>
                        @endforeach
                    </select>
                    <a id="aSubirArchivos" href="#">Subir archivos de precios</a>
                </div>
                <label class="col-sm-1 control-label">Tipo:</label>
                <div class="col-sm-3">
                    <select class="form-control" id="selectTipo">
                        <option value="1">Acuerdo vigente</option>
                        <option value="0">Nuevo acuerdo</option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <button id="btnIniciar" class="btn btn-primary">Iniciar</button>
                </div>
                <label class="col-sm-1 control-label">Resultado:</label>
                <div class="col-sm-2">
                    <div class="form-control-static" id="divProgreso">En espera</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Resultado</h3>
    </div>
    <div class="box-body">
        <table style="width: 100%" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th class="text-center">N°</th>
                    <th class="text-center">ID</th>
                    <th class="text-center">ID PC</th>
                    <th class="text-center">Producto</th>
                    <th class="text-center">Precio</th>
                    <th class="text-center">Moneda</th>
                    <th class="text-center">Resultado</th>
                </tr>
            </thead>
            <tbody id="tbodyProductos">

            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalSubirArchivos" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Subir archivos de precios</h4>
            </div>
            <div class="modal-body">
                <form id="formSubirArchivos" method="post" action="{{route('mgcp.acuerdo-marco.publicar.nuevos-productos.procesar-archivo')}}" class="form-horizontal">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="ruc" class="col-sm-3 control-label">Tipo</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="tipo">
                                <option value="1">Acuerdo vigente</option>
                                <option value="0">Nuevo acuerdo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ruc" class="col-sm-3 control-label">Empresa</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="empresa">
                                @foreach($empresas as $empresa)
                                    <option value="{{$empresa->id}}">{{$empresa->empresa}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ruc" class="col-sm-3 control-label">Archivo</label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control" name="archivo">
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
                <button type="button" id="btnSubirArchivo" class="btn btn-primary">Subir</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script>
    $(document).ready(function () {

        var filaActual = 0;
        var totalFilas = 0;
        var $progreso = $('#divProgreso');
        var $tbodyProductos = $('#tbodyProductos');
        var $selectEmpresa = $('#selectEmpresa');
        var $selectTipo = $('#selectTipo');
        var $botonIniciar = $('#btnIniciar');

        $('#aSubirArchivos').click(function(e){
            e.preventDefault();
            $('#modalSubirArchivos').modal('show');
        });

        $('#btnSubirArchivo').click(function(){
            $('#formSubirArchivos').submit();
        });

        $("#formSubirArchivos").on("submit", function (e) {
            e.preventDefault();
            var $boton = $('#btnSubirArchivo');
            $boton.prop('disabled', true);
            var formData = new FormData(document.getElementById("formSubirArchivos"));
            var $modal=$('#modalSubirArchivos');
            Util.mensaje($modal.find('div.mensaje'), 'warning', 'Procesando...',false);
            $.ajax({
                url: $("#formSubirArchivos").attr('action'),
                type: "post",
                dataType: "json",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    Util.mensaje($modal.find('div.mensaje'), data.tipo, data.mensaje);
                },
                error: function () {
                    Util.mensaje($modal.find('div.mensaje'), 'danger', 'Hubo un problema al subir el archivo. Por favor actualice la página e intente de nuevo');
                },
                complete: function () {
                    $boton.prop('disabled', false);
                }
            });
        });

        $('#btnIniciar').click(function () {
            filaActual = 0;
            totalFilas = 0;
            $selectEmpresa.prop('disabled', true);
            $selectTipo.prop('disabled', true);
            $botonIniciar.prop('disabled', true);
            $progreso.html('Obteniendo productos...');
            $.ajax({
                url: "{{route('mgcp.acuerdo-marco.publicar.nuevos-productos.obtener-productos')}}",
                type: 'post',
                //dataType: 'json',
                data: {idEmpresa: $selectEmpresa.val(), tipo: $selectTipo.val(), _token: "{{csrf_token()}}"},
                success: function (datos) {
                    $tbodyProductos.html(datos);
                    totalFilas = $tbodyProductos.find('tr').length;
                    procesar();
                },
                error: function () {
                    $selectEmpresa.prop('disabled', false);
                    $botonIniciar.prop('disabled', false);
                    $progreso.html('<span class="text-danger">Error al obtener productos. Por favor inténtelo de nuevo.</span>');
                }
            });
        });

        function procesar()
        {
            if (filaActual < totalFilas)
            {
                $progreso.html('Procesando ' + (filaActual + 1) + ' de ' + totalFilas);
                var $fila = $tbodyProductos.find('tr:eq(' + filaActual + ')');
                $fila.find('td.resultado').html('Procesando...');
                $.ajax({
                    url: "{{route('mgcp.acuerdo-marco.publicar.nuevos-productos.procesar')}}",
                    type: 'post',
                    data: {
                        idEmpresa: $selectEmpresa.val(),
                        tipo: $selectTipo.val(),
                        id: $fila.find('td.id').html(),
                        idPc: $fila.find('td.idPc').html(),
                        precio: $fila.find('td.precio').html(),
                        moneda: $fila.find('td.moneda').html(),
                        _token: "{{csrf_token()}}"},
                    success: function (datos) {
                        $fila.find('td.resultado').html('<span class="text-success">Procesado</span>');
                        filaActual++;
                    },
                    error: function () {
                        $fila.find('td.resultado').html('<span class="text-danger">Error</span>');
                    },
                    complete: function () {
                        procesar();
                    }
                });
            } else
            {
                $progreso.html('<span class="text-success">Fin de publicación</span>');
            }
        }
    });
</script>
@endsection
