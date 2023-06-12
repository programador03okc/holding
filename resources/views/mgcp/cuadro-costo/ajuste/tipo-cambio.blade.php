@extends('mgcp.layouts.app')

@section('cabecera')
Tipo de cambio para cuadros de presupuesto
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Cuadros de presupuesto</li>
    <li class="active">Ajustes</li>
    <li class="active">Tipo de cambio</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">
    
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
            @include('mgcp.partials.flashmsg')
                <form class="form-horizontal" id="formActualizar" method="post" action="{{route('mgcp.cuadro-costos.ajustes.tipo-cambio.actualizar')}}">
                    @csrf
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Tipo de cambio</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">S/</span>
                                <input type="text" class="form-control decimal" required name="tipo_cambio" value="{{$tipoCambio->tipo_cambio}}" placeholder="Tipo de cambio">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-4">
                            <button class="btn btn-primary" type="submit">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')


<script src="{{ asset('mgcp/js/util.js') }}"></script>
<script>
    $(document).ready(function() {
        Util.seleccionarMenu(window.location);
        Util.activarSoloDecimales();
        $('#formActualizar').submit(() => {
            $('button').prop('disabled', true).html(Util.generarPuntosSvg() + 'Actualizando');
        });

    });

</script>
@endsection
