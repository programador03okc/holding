@extends('mgcp.layouts.app')
@section('contenido')

@section('cabecera') Cambiar claves de Peru Compras @endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Empresas</li>
    <li class="active">Cambiar claves</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        <form class="form-horizontal" method="POST" action="{{ route('mgcp.acuerdo-marco.empresas.actualizar-claves') }}" style="padding: 20px;">
            <input type="hidden" name="empresa" value="{{ $empresa->id }}">
            <div class="form-group">
                <label class="col-sm-4 control-label">Nueva clave U1</label>
                <div class="col-sm-4">
                    <input type="password" name="clave_uno" class="form-control" placeholder="Ingrese nueva clave del usuario 1">
                    <div class="help-block text-justify">Dejar en blanco si no desea cambiar la clave del usuario 1</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Nueva clave U2</label>
                <div class="col-sm-4">
                    <input type="password" name="clave_dos" class="form-control" placeholder="Ingrese nueva clave del usuario 2">
                    <div class="help-block text-justify">Dejar en blanco si no desea cambiar la clave del usuario 2</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Nueva clave U3</label>
                <div class="col-sm-4">
                    <input type="password" name="clave_tres" class="form-control" placeholder="Ingrese nueva clave del usuario 3">
                    <div class="help-block text-justify">Dejar en blanco si no desea cambiar la clave del usuario 3</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4 text-center">
                    <button type="submit" class="btn btn-primary">Actualizar claves</button>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4">
                    @include('mgcp.partials.flashmsg')
                    @include('mgcp.partials.errors')
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
            Util.seleccionarMenu(window.location);
            $('form').submit(function () {
                $('button[type=submit]').prop('disabled', true);
            });
        });
    </script>
@endsection