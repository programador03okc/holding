@extends('mgcp.layouts.app')

@section('cabecera')
Configuración de indicadores
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">O/C propias</li>
    <li class="active">Configuración de indicadores</li>
</ol>
@endsection

@section('cuerpo')

 @include('mgcp.partials.flashmsg')

<div class="box box-solid">
    <div class="box-body">
        <form id="formActualizar" method="post" action="{{route('mgcp.ordenes-compra.propias.indicadores.actualizar-configuracion')}}">
            @csrf
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <table id="tablePorMonto" class="table table-condensed table-hover" style="font-size: small; width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">Tipo</th>
                                <th class="text-center" style="width: 25%">Rojo</th>
                                <th class="text-center" style="width: 25%">Amarillo</th>
                                <th class="text-center" style="width: 25%">Verde</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($indicadores as $indicador)
                            <tr>
                                <td class="text-center">{{ $indicador->tipo == 1 ? 'Diario' : 'Mensual' }}</td>
                                <input type="hidden" name="id[]" value="{{ $indicador->id }}">
                                <input type="hidden" name="tipo[]" value="{{ $indicador->tipo }}">
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">Hasta S/</span>
                                        <input type="text" required placeholder="Monto" class="form-control entero" name="rojo[]" value="{{ number_format($indicador->rojo) }}">
                                    </div>

                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">Hasta S/</span>
                                        <input type="text" required placeholder="Monto" class="form-control entero" name="amarillo[]" value="{{ number_format($indicador->amarillo) }}">
                                    </div>
                                </td>
                                <td class="text-center">Superior a amarillo</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        <button class="btn btn-primary" type="submit">Actualizar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('mgcp/js/util.js') }}"></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            Util.activarSoloEnteros();
            $('#formActualizar').submit(()=>{
                $('button').prop('disabled', true).html(Util.generarPuntosSvg() + ' Actualizando');
            });
        });
    </script>
@endsection
