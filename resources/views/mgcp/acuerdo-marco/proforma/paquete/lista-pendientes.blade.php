@extends('mgcp.layouts.app')

@section('cabecera')
Proformas por paquete
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Proformas</li>
    <li class="active">Paquete</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">
        <p>Hay {{$total}} proformas por paquete pendientes de cotizar:</p>
        <ul>
            <li>Compra ordinaria: {{$ordinaria->count()}}</li>
            <li>Gran compra: {{$granCompra->count()}}</li>
        </ul>
        <p>Puede cotizarlas ingresando a <a href="https://www.catalogos.perucompras.gob.pe/AccesoGeneral" target="_blank">Perú Compras</a></p>
    </div>
</div>


@endsection

@section('scripts')


<script src="{{ asset("mgcp/js/util.js") }}"></script>
<script>
    $(document).ready(function() {
        Util.seleccionarMenu(window.location);
    });

</script>
@endsection
