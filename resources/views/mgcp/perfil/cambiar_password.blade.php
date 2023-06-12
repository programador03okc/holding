@extends('mgcp.layouts.app')
@section('contenido')


@section('cabecera')
Cambiar contraseña
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Cambiar contraseña</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">
        @include('mgcp.partials.errors')
        @include('mgcp.partials.flashmsg')
        <form class="form-horizontal" role="form" method="POST" action="{{route('mgcp.perfil.actualizar-password')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <label class="col-md-4 control-label">Nueva contraseña</label>
                <div class="col-md-4">
                    <input type="password" class="form-control" name="password" required>
                    <small class="help-block">Mínimo 6 caracteres, debe incluir al menos una mayúscula, una minúscula, un número y un caracter especial (asterisco, numeral, etc.)</small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Confirmar contraseña</label>
                <div class="col-md-4">
                    <input type="password" class="form-control" name="password_confirmation" required>
                    <small class="help-block">Debe ser igual a la nueva contraseña</small>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Cambiar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script>
    $(document).ready(function () {
        $('form').submit(function () {
            $('button[type=submit]').html(Util.generarPuntosSvg()+'Cambiando').prop('disabled', true);
        });
    });
</script>

@endsection