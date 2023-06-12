@extends('mgcp.layouts.app')

@section('cabecera')
Sin permiso
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">
        <p>Su usuario no tiene permiso para acceder a este formulario</p>
        <br>
        <div class="text-center">
            <a href="{{ route('mgcp.home') }}" class="btn btn-primary">Salir</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script>
    $(document).ready(function () {
        //*****INICIALIZACION*****
        Util.seleccionarMenu(window.location);
    });
</script>
@endsection