@extends('mgcp.layouts.app')
@section('contenido')

@section('cabecera')
Componentes
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Publicar</li>
    <li class="active">Componentes</li>
</ol>
@endsection


@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
    </div>
</div>
@endsection

@section('scripts')
<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script>
    $(document).ready(function () {
        Util.seleccionarMenu(window.location);
    });
</script>
@endsection