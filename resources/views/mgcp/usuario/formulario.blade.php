@extends('mgcp.layouts.app')

@section('cabecera')
@if ($operacion=='editar') Editar @else Nuevo @endif usuario
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Usuarios</li>
    @if ($operacion=='editar')
    <li class="active">Lista</li>
    @endif
    <li class="active">@if ($operacion=='editar') Editar @else Nuevo @endif</li>
</ol>
@endsection

@section('cuerpo')

@php
use App\Models\mgcp\Usuario\Rol;
use App\Models\mgcp\Usuario\RolUsuario;
@endphp

<div class="box box-solid">
    <div class="box-body">
        @include('mgcp.partials.errors')
        @include('mgcp.partials.flashmsg')
        <form class="form-horizontal bloquear-boton" role="form" method="POST" action="@if ($operacion=='editar') {{route('mgcp.usuarios.actualizar')}} @else {{route('mgcp.usuarios.registrar')}} @endif">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id_usuario" value="@if(isset($user)){{$user->id}}@endif">

            <div class="form-group">
                <label class="col-md-4 control-label">Nombre y apellido</label>
                <div class="col-md-6">
                    <input type="text" placeholder="Nombre y apellido" class="form-control" name="name" value="{{isset($user) ? $user->name : old('name')}}">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Nombre corto</label>
                <div class="col-md-6">
                    <input type="text" placeholder="Nombre corto" class="form-control" name="nombre_corto" value="{{isset($user) ? $user->nombre_corto : old('nombre_corto')}}">
                    <small class="help-block" style="margin-bottom: 0px">Ejemplo: Wilmar G.</small>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Correo</label>
                <div class="col-md-6">
                    <input type="email" placeholder="Correo electrónico" class="form-control" name="email" value="{{isset($user) ? $user->email : old('email')}}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Contraseña</label>
                <div class="col-md-6">
                    <input type="password" placeholder="Contraseña" class="form-control" name="password">
                    <small class="help-block" style="margin-bottom: 0px">Mínimo 6 caracteres, debe incluir al menos una mayúscula, una minúscula, un número y un caracter especial (asterisco, numeral, etc.)</small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Confirme contraseña</label>
                <div class="col-md-6">
                    <input type="password" placeholder="Confirmar contraseña" class="form-control" name="password_confirmation">
                </div>
            </div>
            @if ($operacion=='editar')
            <div class="form-group">
                <label class="col-md-4 control-label">Activo</label>
                <div class="col-md-3">
                    <select class="form-control" name="activo">
                        <option value="1" @if (isset($user)){{ $user->activo ? "selected" : "" }}@endif>Sí</option>
                        <option value="0" @if (isset($user)){{ $user->activo ? "" : "selected" }}@endif>No</option>
                    </select>
                </div>
            </div>
            @endif
            <div class="form-group">
                <label class="col-md-4 control-label">Roles:</label>
                <div class="col-md-6">
                    @foreach ($tiposRol as $tiporol)
                    <h5 style="font-weight: 700">{{$tiporol->tipo}}</h5>
                    @foreach (Rol::where('id_tipo',$tiporol->id)->orderBy('descripcion','asc')->get() as $role)
                    <div class="checkbox">
                        <label>
                            <input name="rol[]" value="{{$role->id}}" type="checkbox" @if ($operacion=='editar' && RolUsuario::where('id_usuario',$user->id)->where('id_rol',$role->id)->count()>0) checked @endif> {{$role->descripcion}}
                        </label>
                    </div>
                    @endforeach
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">{{$operacion=='editar' ? "Actualizar" : "Registrar"}}</button>
                    <a id="a_regresar" href="{{ route('mgcp.usuarios.lista') }}" class="btn btn-default">Regresar a la lista</a>
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
        var operacion = '{{$operacion}}';

        if (operacion == 'editar')
        {
            Util.seleccionarMenu("{{route('mgcp.usuarios.lista')}}");
        } else
        {
            Util.seleccionarMenu(window.location);
        }

        $('form').submit(function () {
            $('button[type=submit]').prop('disabled', true);
        });
    });
</script>
@endsection