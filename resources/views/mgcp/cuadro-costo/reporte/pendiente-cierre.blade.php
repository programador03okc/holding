@extends('mgcp.layouts.app')

@section('cabecera')
Reporte de cuadros pendientes de cierre
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Cuadros de presupuesto</li>
    <li class="active">Reportes</li>
    <li class="active">Pendientes de cierre</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">
    
        <div class="row">
            <div class="col-sm-12">
            @include('mgcp.partials.flashmsg')
                <p>Se generará un archivo en Excel con la lista de cuadros pendientes de cierre, con fechas desde el mes anterior hasta fin de este año</p>
                <br>
                <form class="form-horizontal" id="formGenerar" target="_blank" method="post" action="{{route('mgcp.cuadro-costos.reportes.pendientes-cierre.generar-archivo')}}">
                    @csrf
                    
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-5">
                            <button class="btn btn-primary" type="submit">Generar</button>
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
