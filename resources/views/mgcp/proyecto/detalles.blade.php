@extends('mgcp.layouts.app')
@section('estilos')
<link href="{{ asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css"/>
<style type="text/css">
    ol.archivos li {
        margin-bottom: 10px;
    }
    .circle {
        width: 100px;
        margin: 6px 6px 20px;
        display: inline-block;
        position: relative;
        text-align: center;
        line-height: 1.2;
    }

    .circle canvas {
        vertical-align: top;
    }

    .circle strong {
        position: absolute;
        top: 30px;
        left: 0;
        width: 100%;
        text-align: center;
        line-height: 40px;
        font-size: 30px;
    }

    .circle strong i {
        font-style: normal;
        font-size: 0.6em;
        font-weight: normal;
    }

    .circle span {
        display: block;
        color: #aaa;
        margin-top: 12px;
    }

    #tableContactos td {
        border: none;
    }
    table.actividades small.help-block {
        margin-top: 0px;
        margin-bottom: 0px;
    }
</style>

@endsection

@section('cabecera')
Detalles de proyecto {{$proyecto->codigo_proyecto}}
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Proyectos</li>
    <li><a href="{{route('mgcp.proyectos.lista')}}">Lista</a></li>
    <li class="active">Detalles</li>
</ol>
@endsection

@section('cuerpo')
<?php

use App\mgcp\Proyecto\Comentario;
use App\mgcp\Proyecto\Actividad;
use App\mgcp\Proyecto\Fase;
use App\mgcp\Proyecto\DescripcionFase;
use Carbon\Carbon;

if ($proyecto->descripcion_fase->porcentaje <= 100) {
    $color_circulo = 'rgb(34,186,160)';
}
if ($proyecto->descripcion_fase->porcentaje <= 75) {
    $color_circulo = 'rgb(18,175,203)';
}
if ($proyecto->descripcion_fase->porcentaje <= 50) {
    $color_circulo = 'rgb(246,212,51)';
}
if ($proyecto->descripcion_fase->porcentaje <= 25) {
    $color_circulo = 'rgb(242,86,86)';
}
?>

<div class="box box-solid">
    <div class="box-body">

        @include('mgcp.partials.flashmsg')
        @include('mgcp.partials.errors')

        <div class="text-right" style="margin-bottom: 20px">
            <div class="btn-group text-right" role="group">
                @if (Auth::user()->tieneRol(13) || $proyecto->id_responsable == Auth::user()->id)
                <button data-id="{{$proyecto->id}}" title="Editar proyecto" class="btn btn-default" id="btnEditar"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                @endif
                @if (Auth::user()->tieneRol(26) || $proyecto->id_responsable == Auth::user()->id)
                <button title="Ver contactos" data-toggle="modal" data-target="#modalContactos" class="btn btn-default"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></button>
                @endif
                @if (Auth::user()->tieneRol(27) || $proyecto->id_responsable == Auth::user()->id)
                <button title="Enviar proyecto por correo" class="btn btn-default" id="btnEnviarCorreo"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></button>
                @endif
                <button title="Ver todos los archivos adjuntos" data-toggle="modal" data-target="#modalTodosLosArchivos" class="btn btn-default"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span></button>
                <a target="_blank" href="{{ route('mgcp.proyectos.imprimir',$proyecto->id) }}" title="Imprimir" class="btn btn-default"><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-10">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><b>Nombre:</b></label>
                        <div class="col-md-10">
                            <div class="form-control-static"> {{$proyecto->nombre}}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><b>Cliente:</b></label>
                        <div class="col-md-10">
                            <div class="form-control-static"> {{$proyecto->entidad->razon_social}}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><b>Fecha de cierre:</b></label>
                        <div class="col-md-2">
                            <div class="form-control-static">{{$proyecto->fecha_cierre}}</div>
                        </div>
                        <label class="col-md-2 control-label"><b>Urgencia:</b></label>
                        <div class="col-md-4">
                            <div class="form-control-static">{{$proyecto->urgencia}}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><b>Responsable:</b></label>
                        <div class="col-md-10">
                            <div class="form-control-static">{{$proyecto->responsable->name}}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><b>Monto:</b></label>
                        <div class="col-md-10">
                            <div class="form-control-static">{{$proyecto->monto_format}}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><b>Estado:</b></label>
                        <div class="col-md-10">
                            <div class="form-control-static">
                                @if ($proyecto->estado->id!==1)
                                <span class="text-danger"><strong>{{$proyecto->estado->estado}}</strong></span>
                                @else
                                {{$proyecto->estado->estado}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 text-center">
                <div class="circle"
                     data-value="{{$proyecto->descripcion_fase->porcentaje/100}}"
                     data-thickness="15"
                     data-startAngle="-90"
                     data-animation-start-value="1.0"
                     data-fill="{&quot;color&quot;: &quot;{{$color_circulo}}&quot;}">
                    <strong>{{$proyecto->descripcion_fase->porcentaje}}%</strong>
                </div>
            </div>
        </div>

        <div class="tabs" role="tabpanel" style="margin-top:20px;">
            <ul class="nav nav-tabs">
                @foreach ($proyectosFases as $fase)
                <li role="presentation" data-toggle="tooltip" data-placement="top" @if (count($proyectosFases)==$fase->id_fase) class="active" @endif>
                    <a data-toggle="tab" href="#tab_fase_{{$fase->id_fase}}">Fase {{$fase->id_fase}}</a>
                </li>
                @endforeach
                @if ($fase->id_fase<15 && $proyecto->estado->id==1)
                @if (Auth::user()->tieneRol(25) || $proyecto->id_responsable == Auth::user()->id)
                <li role="presentation" data-toggle="tooltip" data-placement="top">
                    <a data-toggle="tab" href="#tab_elevar_fase">Elevar fase</a>
                </li>
                @endif
                @endif
            </ul>
            <div class="tab-content">
                @foreach ($proyectosFases as $fase)
                @php
                if (count($proyectosFases)==$fase->id_fase) 
                { $class="tab-pane fade in active"; }
                else
                { $class="tab-pane fade"; }
                @endphp
                <div id="tab_fase_{{$fase->id_fase}}" class="{{$class}}">

                    <fieldset class="fieldset" style="margin-top:20px;">
                        <legend>Fase {{$fase->id_fase}} - {{$fase->descripcionFase->descripcion}}</legend>
                        <div class="form-horizontal">
                            <label class="col-md-2 control-label"><b>Detalle:</b></label>
                            <div class="col-md-9">
                                <div class="form-control-static text-justify">{{$fase->detalles}} <small>({{$fase->fecha}}, {{$fase->fecha_humans}})</small></div>
                            </div>
                        </div>
                    </fieldset>
                    <br><br><br>
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                @php
                                $comentarios = Comentario::where('id_proyecto_fase', $fase->id)->orderBy('fecha', 'asc')->get();
                                $actividades = Actividad::where('id_proyecto_fase', $fase->id)->orderBy('fecha_creacion', 'asc')->get();
                                @endphp
                                <a aria-expanded="true" href="#coment_{{$fase->id}}" role="tab" data-toggle="tab">
                                    Comentarios ({{count($comentarios)}})
                                </a>
                            </li>
                            <li role="presentation">
                                <a aria-expanded="false" href="#activ_{{$fase->id}}" role="tab" data-toggle="tab">
                                    Actividades ({{count($actividades)}})
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">



                            <div id="coment_{{$fase->id}}" class="tab-pane fade in active">
                                <fieldset class="fieldset" style="margin-top:20px;">
                                    <legend class="nv-legend">Comentarios:</legend>

                                    @if (count($comentarios)>0)
                                    @foreach ($comentarios as $comentario)
                                    <div class="row comentarios">
                                        <div class="col-md-8 col-md-offset-2">
                                            <div class="timeline-comment">
                                                <div class="timeline-comment-header">

                                                    <p><strong>{{$comentario->usuario->name}}</strong> <small>{{$comentario->fecha}} ({{$comentario->fecha_humans}})</small>:</p>
                                                </div>
                                                <p class="timeline-comment-text text-justify">
                                                    {{$comentario->comentario}}
                                                    @if ($comentario->archivos()->count()>0)
                                                    <br>
                                                    <button type="button" data-comentarioid="{{$comentario->id}}" class="btn btn-default btn-xs archivos"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Ver archivos</button>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="text-center">
                                        Sin comentarios
                                    </div>
                                    @endif
                                </fieldset>
                                @if (count($proyectosFases)==$fase->descripcionFase->id)
                                <br>
                                @if (Auth::user()->tieneRol(15) || $proyecto->id_responsable==Auth::user()->id)
                                <fieldset class="fieldset">
                                    <legend class="nv-legend">Nuevo comentario:</legend>
                                    <form enctype="multipart/form-data" class="form-horizontal" role="form" method="POST" action="{{ route('mgcp.proyectos.ingresar-comentario') }}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="codigo" value="{{ $proyecto->id }}">
                                        <div class="form-group">
                                            <div class="col-md-2 control-label">Comentario:</div>
                                            <div class="col-md-4">
                                                <textarea class="form-control" placeholder="Ingrese nuevo comentario" name="comentario" required=""></textarea>
                                            </div>
                                            <div class="col-md-2 control-label">Adjuntar archivos:</div>
                                            <div class="col-md-4">
                                                <input type="file" name="archivos[]" multiple="true" class="form-control">
                                                <p class="help-block">Tamaño máximo: {{ini_get("upload_max_filesize")}}B</p> 
                                            </div>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary">Registrar</button>
                                        </div>
                                    </form>
                                </fieldset>
                                @endif
                                @endif
                            </div>

                            <div id="activ_{{$fase->id}}" class="tab-pane fade">
                                <fieldset class="fieldset" style="margin-top:20px;">
                                    <legend>Actividades:</legend>

                                    <div class="col-sm-10 col-sm-offset-1">
                                        @if (count($actividades)>0)
                                        <table class="table table-condensed table-hover actividades">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Por</th>
                                                    <th class="text-center">Fecha inicio</th>
                                                    <th class="text-center">Fecha fin</th>
                                                    <th class="text-center">Responsable</th>
                                                    <th class="text-center">Detalle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($actividades as $actividad)
                                                <tr>
                                                    <td>{{$actividad->autor->name}}<br><small class="help-block">({{$actividad->fecha_humans}})</small></td>
                                                    <td class="text-center">{{$actividad->fecha_inicio}}</td>
                                                    <td class="text-center">{{$actividad->fecha_fin}}</td>
                                                    <td>{{$actividad->responsable}}</td>
                                                    <td>{{$actividad->actividad}}</td>
                                                </tr>

                                                @endforeach 
                                            </tbody>
                                        </table>
                                        @else
                                        <div class="text-center">
                                            Sin actividades
                                        </div>
                                        @endif
                                    </div>

                                </fieldset>
                                @if (count($proyectosFases)==$fase->descripcionFase->id)
                                <br>
                                @if (Auth::user()->tieneRol(15) || $proyecto->id_responsable == Auth::user()->id) 
                                <fieldset class="fieldset">
                                    <legend class="nv-legend">Nueva actividad:</legend>
                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('mgcp.proyectos.ingresar-actividad') }}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="codigo" value="{{ $proyecto->id }}">
                                        <div class="form-group">
                                            <div class="col-md-2 control-label">Inicio:</div>
                                            <div class="col-md-4">
                                                <input type="text" autocomplete="false" placeholder="dd-mm-aaaa" required class="form-control date-picker" name="fecha_inicio" value="{{date('d-m-Y')}}">
                                            </div>
                                            <div class="col-md-2 control-label">Fin:</div>
                                            <div class="col-md-4">
                                                <input type="text" autocomplete="false" placeholder="dd-mm-aaaa" required class="form-control date-picker" name="fecha_fin" value="{{date('d-m-Y')}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-2 control-label">Responsable:</div>
                                            <div class="col-md-4">
                                                <input type="text" placeholder="Ingrese responsable" required class="form-control" name="responsable" value="{{Auth::user()->name}}">
                                            </div>
                                            <div class="col-md-2 control-label">Actividad:</div>
                                            <div class="col-md-4">
                                                <textarea class="form-control" placeholder="Ingrese nueva actividad" name="nueva_actividad" required=""></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary">Registrar</button>
                                        </div>
                                    </form>
                                </fieldset>
                                @endif 
                                @endif
                            </div>




                        </div>
                    </div>

                </div>  
                @endforeach
                @if ($fase->descripcionFase->id<15 && $proyecto->estado->id==1)
                @if (Auth::user()->tieneRol(25) || $proyecto->id_responsable == Auth::user()->id) 
                <div id="tab_elevar_fase" class="tab-pane fade">
                    <fieldset class="fieldset" style="margin-top: 20px">
                        <legend>Elevar a fase {{$proyecto->descripcion_fase->id+1}} - {{DescripcionFase::find($proyecto->descripcion_fase->id+1)->descripcion}}</legend>
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('mgcp.proyectos.elevar-fase') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="codigo" value="{{ $proyecto->id }}">
                            <div class="row">
                                <label class="col-md-2 control-label"><b>Detalle:</b></label>
                                <div class="col-md-9">
                                    <textarea class="form-control" placeholder="Ingrese nuevo detalle" name="nuevo_detalle" required=""></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <br>
                                    <button id="btnElevarFase" type="submit" class="btn btn-primary">Elevar Fase</button>
                                </div>
                            </div>

                        </form>
                    </fieldset>
                </div>
                @endif 
                @endif
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEditar" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar proyecto <span id="spanCodigoProyecto"></span></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" id="txtId">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <select name="cliente" class="form-control">
                                @foreach ($clientes as $cliente)
                                <option value="{{$cliente->id}}">{{$cliente->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Proyecto</label>
                        <div class="col-sm-10">
                            <textarea name="nombre" class="form-control validar" placeholder="Nombre del proyecto"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Responsable</label>
                        <div class="col-sm-10">
                            <select name="responsable" class="form-control">
                                @foreach ($responsables as $responsable)
                                <option value="{{$responsable->id}}">{{$responsable->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Monto</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-addon">S/</div>
                                <input type="text" class="form-control number" placeholder="Monto" name="monto" required="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">F. cierre</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="dd-mm-aaaa" required name="fecha_cierre" class="form-control date-picker validar">
                        </div>
                        <label class="col-sm-2 control-label">Urgencia</label>
                        <div class="col-sm-4">
                            <select name="urgencia" class="form-control">
                                <option value="Alta">Alta</option>
                                <option value="Media">Media</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Estado</label>
                        <div class="col-sm-10">
                            <select name="estado" class="form-control">
                                @foreach ($estados as $estado)
                                <option value="{{$estado->id}}">{{$estado->estado}}</option>
                                @endforeach
                            </select>
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
                <button type="submit" id="btnEditarAceptar" class="btn btn-primary">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalArchivos" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Archivos adjuntos</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalTodosLosArchivos" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Todos los archivos adjuntos</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Fase</th>
                            <th class="text-center">Archivo</th>
                            <th class="text-center">Fecha de subida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proyectosFases as $fase)
                        @foreach ($fase->comentarios()->orderBy('id','asc')->get() as $comentario)
                        @if (count($comentario->archivos)>0)
                        @foreach ($comentario->archivos as $archivo)
                        <tr>
                            <td>Fase {{$fase->descripcionFase->id}} - {{$fase->descripcionFase->descripcion}}</td>
                            <td><a target="_blank" href="{{url('/')}}/files/proyectos/comentarios/{{$archivo->id}}/{{$archivo->archivo}}">{{$archivo->archivo}}</a></td>
                            <td class="text-center">{{$comentario->fecha}}</td>
                        </tr>
                        @endforeach
                        @endif
                        @endforeach
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalCorreo" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar proyecto por correo</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="codigo" value="{{$proyecto->id}}">
                    <div class="form-group">
                        <label class="col-md-2 control-label">A:</label>
                        <div class="col-sm-5">
                            <input type="text" id="txt_correo_msj" class="form-control validar" name="correo" placeholder="Correo">
                        </div>
                        <div class="col-sm-5">
                            <select class="form-control" name="dominio">
                                <option value="okcomputer">@okcomputer.com.pe</option>
                                <option value="proyectec">@proyectec.com.pe</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Mensaje:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control validar" name="mensaje" placeholder="Mensaje para destinatario"></textarea>
                        </div> 
                    </div>
                    <div class="form-group mensaje">
                    </div> 
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnCorreoAceptar" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalContactos" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar contactos</h4>
            </div>
            <div class="modal-body">
                @if (Auth::user()->tieneRol(26) || $proyecto->id_responsable==Auth::user()->id)
                <div class="box box-solid">
                    <div class="box-body">
                        <form id="formContactos">
                            <input type="hidden" name="codigo" value="{{$proyecto->id}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <table class="table table-condensed">
                                <tbody>
                                    <tr class="no-top">
                                        <td>
                                            <label>Nombre:</label><br>
                                            <input id="txtNombre" name="nombre" type="text" class="form-control" placeholder="Nombre">
                                        </td>
                                        <td>
                                            <label>Teléfono:</label><br>
                                            <input id="txtTelefono" name="telefono" type="text" class="form-control" placeholder="Teléfono">
                                        </td>
                                        <td>
                                            <label>Correo:</label><br>
                                            <input id="txtCorreo" type="text" name="correo" class="form-control" placeholder="Correo">
                                        </td>
                                        <td>
                                            <label>&nbsp;</label><br>
                                            <button type="button" id="btnAgregarContacto" class="btn btn-default">Agregar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                @endif
                <div class="box box-solid last">
                    <div class="box-header with-border">
                        <h3 class="box-title">Lista de contactos</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-left">Nombre</th>
                                    <th class="text-left">Teléfono</th>
                                    <th class="text-left">Correo</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tbodyContactos">
                                @foreach ($proyecto->contactos()->orderBy('nombre','asc')->get() as $contacto)
                                <tr>
                                    <td>{{$contacto->nombre}}</td>
                                    <td>{{$contacto->telefono}}</td>
                                    <td>{{$contacto->correo}}</td>
                                    <td class="text-center">
                                        @if (Auth::user()->tieneRol(26) || $proyecto->id_responsable==Auth::user()->id)
                                        <span data-id="{{$contacto->id}}" style="cursor: pointer" title="Eliminar" class="eliminarContacto glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>
<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script src='{{ asset("assets/jquery-circle-progress/dist/circle-progress.js") }}'></script>

<script>
    $(document).ready(function () {

        Util.seleccionarMenu("{{route('mgcp.proyectos.lista')}}");
        var usuarioResponsable = '{{$proyecto->id_responsable}}';
        var usuarioActual = '{{Auth::user()->id}}';
        $('input.number').number(true, 2);

        Util.activarDatePicker();

        $('.circle').circleProgress();

        $('#btnAgregarContacto').click(function () {
            var $boton = $(this);
            $boton.prop('disabled', true);
            $.ajax({
                url: '{{route("mgcp.proyectos.agregar-contacto")}}',
                data: $('#formContactos').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.tipo == 'success')
                    {
                        var contacto = '<tr>';
                        contacto += '<td>' + data.nombre + '</td>';
                        contacto += '<td>' + data.telefono + '</td>';
                        contacto += '<td>' + data.correo + '</td>';
                        contacto += '<td class="text-center"><span data-id="' + data.id + '" style="cursor: pointer" title="Elimnar" class="eliminarContacto glyphicon glyphicon-remove" aria-hidden="true"></span></td>';
                        contacto += '</tr>';
                        $('#tbodyContactos').append(contacto);
                        $('#tableNuevoContacto').find('input[type=text]').val('');
                    } else
                    {
                        alert(data.mensaje);
                    }
                    /*switch (data.mensaje)
                     {
                     case 'guardado':
                     
                     break;
                     case 'error_validacion':
                     alert("Error: Ingrese un nombre de contacto antes de guardar.")
                     break;
                     default:
                     alert('Error: No se pudo registrar el contacto. Por favor actualice la página e inténtelo de nuevo.');
                     break;
                     
                     }*/
                },
                error: function () {
                    alert('Hubo un problema al agregar el contacto. Por favor actualice la página e inténtelo de nuevo');
                },
                complete: function () {
                    $boton.attr('disabled', false);
                }
            });
        });

        $('#tbodyContactos').on("click", "span.eliminarContacto", function () {
            var $fila = $(this);
            if (confirm("¿Desea eliminar este contacto?"))
            {
                $.ajax({
                    url: '{{route("mgcp.proyectos.eliminar-contacto")}}',
                    data: {codigo: $(this).data('id'), _token: '{{csrf_token()}}'},
                    type: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        if (data.tipo == 'success')
                        {
                            $fila.closest('tr').fadeOut(300, function () {
                                $(this).remove();
                            });
                        } else
                        {
                            alert(data.mensaje);
                        }
                    },
                    error: function (xhr, status) {
                        alert('Error: No se pudo eliminar el contacto. Por favor actualice la página e inténtelo de nuevo.');
                    }
                });
            }
        });

        //*****EDITAR PROYECTO*****
        $('#btnEditar').click(function () {
            var id = $(this).data('id');
            var $modal = $('#modalEditar');
            var $aceptar = $('#btnEditarAceptar');
            $modal.modal('show');
            $modal.find('div.mensaje').html('<div class="text-center">Obteniendo datos...</div>');
            $aceptar.prop('disabled', true);
            $.ajax({
                url: '{{route("mgcp.proyectos.ajax-detalles")}}',
                data: {id: id, _token: '{{csrf_token()}}'},
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $modal.find('input[type=text]').val('').prop('disabled', true);
                    $modal.find('textarea').val('').prop('disabled', true);
                    $modal.find('select').prop('disabled', true);
                },
                success: function (json) {
                    $('#txtId').val(id);
                    $('#spanCodigoProyecto').html(json.codigo_proyecto);
                    $modal.find('select[name=cliente]').val(json.id_entidad);
                    $modal.find('textarea[name=nombre]').val(json.nombre);
                    $modal.find('select[name=responsable]').val(json.id_responsable);
                    $modal.find('input[name=monto]').val(json.monto);
                    $modal.find('input[name=fecha_cierre]').val(json.fecha_cierre);
                    $modal.find('select[name=estado]').val(json.id_estado);
                    $modal.find('select[name=urgencia]').val(json.urgencia);

                    $modal.find('input[type=text]').prop('disabled', false);
                    $modal.find('textarea').prop('disabled', false);
                    $modal.find('select').prop('disabled', false);
                    $modal.find('div.mensaje').html('');
                    $aceptar.prop('disabled', false);
                },
                error: function (xhr, status) {
                    $modal.find('div.mensaje').html('Error al obtener datos. Por favor actualice la página e inténtelo de nuevo.');
                }
            });
        });

        $('#btnEditarAceptar').click(function () {
            var $modal = $('#modalEditar');
            /*if (util.camposVacios($modal))
             {
             return false;
             }*/
            $.ajax({
                url: '{{route("mgcp.proyectos.actualizar")}}',
                data: $modal.find('form').serialize(),
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $modal.find('div.mensaje').html('<div class="text-center">Procesando...</div>');
                    $modal.find('input[type=text]').attr('disabled', true);
                    $modal.find('textarea').attr('disabled', true);
                    $modal.find('select').attr('disabled', true);
                    $modal.find('button').attr('disabled', true);
                },
                error: function (xhr, status) {
                    $modal.find('div.mensaje').html('<strong>Hubo un error al editar los datos. Por favor actualice la página e inténte de nuevo.</strong>');
                },
                success: function (data) {

                    if (data.tipo == 'success')
                    {
                        alert(data.mensaje);
                        $modal.modal('hide');
                        location.reload();
                    } else
                    {
                        Util.mensaje($modal.find('div.mensaje'), data.tipo, data.mensaje);
                    }
                },
                complete: function (xhr, status) {
                    $modal.find('input[type=text]').prop('disabled', false);
                    $modal.find('textarea').prop('disabled', false);
                    $modal.find('select').prop('disabled', false);
                    $modal.find('button').prop('disabled', false);
                }
            });
        });
        //*****FIN EDITAR PROYECTO*****

        /*$('#btn_editar').click(function () {
         var codigo = $(this).attr('data-codigo');
         var $modal = $('#modal_editar');
         $modal.modal('toggle');
         $modal.find('div.mensaje').html('<div class="text-center">Obteniendo datos...</div>');
         $.ajax({
         url: '',
         data: {codigo: codigo, _token: '{{csrf_token()}}'},
         type: 'POST',
         dataType: 'json',
         beforeSend: function () {
         $modal.find('input[type=text]').val('');
         $modal.find('input').attr('disabled', true);
         $modal.find('textarea').attr('disabled', true);
         $modal.find('select').attr('disabled', true);
         $modal.find('button').attr('disabled', true);
         },
         success: function (json) {
         $('#txt_codigo').val(codigo);
         $('#span_okcodigo').html(json.okccodigo);
         $modal.find('select[name=cliente]').val(json.entidad_id);
         $modal.find('textarea[name=nombre_proyecto]').val(json.nombre_proyecto);
         $modal.find('select[name=responsable]').val(json.responsable_id);
         $modal.find('input[name=monto]').val(json.monto);
         $modal.find('input[name=fecha_cierre]').val(json.fecha_cierre);
         $modal.find('select[name=estado]').val(json.estado_id);
         $modal.find('select[name=urgencia]').val(json.urgencia);
         },
         error: function (xhr, status) {
         alert('Disculpe, existió un problema');
         },
         complete: function () {
         $modal.find('div.mensaje').html('');
         $modal.find('input').attr('disabled', false);
         $modal.find('select').attr('disabled', false);
         $modal.find('button').attr('disabled', false);
         $modal.find('textarea').attr('disabled', false);
         }
         });
         });*/

        /*$('#btn_editar_aceptar').click(function () {
         var $modal = $('#modal_editar');
         if (util.camposVacios($modal))
         {
         return false;
         }
         $.ajax({
         url: '',
         data: $modal.find('form').serialize(),
         type: 'POST',
         dataType: 'json',
         beforeSend: function () {
         $modal.find('div.mensaje').html('<div class="text-center">Procesando...</div>');
         $modal.find('input').attr('disabled', true);
         $modal.find('textarea').attr('disabled', true);
         $modal.find('select').attr('disabled', true);
         $modal.find('button').attr('disabled', true);
         },
         error: function (xhr, status) {
         alert('Disculpe, existió un problema');
         },
         success: function (json) {
         if (json.mensaje == 'editado')
         {
         alert("Proyecto editado correctamente.")
         $modal.modal('hide');
         location.reload();
         } else
         {
         util.mensaje($modal, json.mensaje);
         }
         },
         complete: function (xhr, status) {
         $modal.find('div.mensaje').html('');
         $modal.find('input').attr('disabled', false);
         $modal.find('select').attr('disabled', false);
         $modal.find('button').attr('disabled', false);
         $modal.find('textarea').attr('disabled', false);
         }
         });
         });*/

        $('#btnEnviarCorreo').click(function () {
            var $modal = $('#modalCorreo');
            $modal.find('input[type=text]').val('');
            $modal.find('textarea').val('');
            $modal.modal('show');
        });

        $('#btnCorreoAceptar').click(function () {
            var $modal = $('#modalCorreo');
            /*if (util.camposVacios($modal))
             {
             return false;
             }*/
            $.ajax({
                url: '{{route("mgcp.proyectos.enviar-correo")}}',
                data: $modal.find('form').serialize(),
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $modal.find('div.mensaje').html('<div class="text-center">Enviando...</div>');
                    $modal.find('input').attr('disabled', true);
                    $modal.find('select').attr('disabled', true);
                    $modal.find('button').attr('disabled', true);
                    $modal.find('textarea').attr('disabled', true);
                },
                error: function (xhr, status) {
                    alert('Hubo un problema al acceder a los datos. Por favor actualice la página e intente de nuevo');
                },
                success: function (json) {
                    if (json.mensaje == 'enviado')
                    {
                        alert("El correo ha sido enviado a " + json.correo + ".")
                        $modal.modal('hide');
                    } else
                    {
                        util.mensaje($modal, json.mensaje);
                    }
                },
                complete: function (xhr, status) {
                    $modal.find('input').attr('disabled', false);
                    $modal.find('select').attr('disabled', false);
                    $modal.find('button').attr('disabled', false);
                    $modal.find('textarea').attr('disabled', false);
                    $modal.find('div.mensaje').html('');
                }
            });
        });



        var limite = '{{ini_get("upload_max_filesize")}}';
        limite = parseInt(limite.substring(0, limite.length - 1));
        $('input[type=file]').bind('change', function () {
            var numArchivos = this.files.length;
            var sumaArchivos = 0;
            for (var i = 0; i < numArchivos; i++)
            {
                sumaArchivos += this.files[i].size;
                if (this.files[i].size / 1024 / 1024 > limite)
                {
                    alert("Error: El archivo " + this.files[i].name + " supera el límite permitido de " + limite + "MB.");
                    $(this).val('');
                    //Sale de la funcion para evitar la advertencia de abajo
                    return false;
                }
            }
            if (sumaArchivos / 1024 / 1024 > limite)
            {
                alert("Error: La suma del tamaño de los archivos supera el límite permitido de " + limite + "MB.");
                $(this).val('');
            }
        });

        $('div.comentarios').on('click', 'button.archivos', function () {
            var url = "{{url('/')}}";
            var $modal = $('#modalArchivos');
            var $body = $modal.find('div.modal-body');
            $body.html('<div class="text-center">Obteniendo archivos...</div>');
            $modal.modal('show');
            $.ajax({
                url: '{{route("mgcp.proyectos.ver-archivos")}}',
                data: {id_comentario: $(this).data('comentarioid'), _token: '{{csrf_token()}}'},
                type: 'POST',
                dataType: 'json',
                success: function (datos) {
                    var cadena = '<ol class="archivos">';
                    for (var indice in datos) {
                        cadena += '<li><a target="_blank" href="' + url + '/files/proyectos/comentarios/' + datos[indice].id + '/' + datos[indice].archivo + '">' + datos[indice].archivo + '</a></li>';
                    }
                    cadena += '</ol>';
                    $body.html(cadena);
                },
                error: function (xhr, status) {
                    alert('Hubo un problema al acceder a los datos. Por favor actualice la página e intente de nuevo');
                }
            });
        });

        $('form').submit(function () {
            $('button[type=submit]').html('Registrando...').attr('disabled', true);
        });
    });
</script>


@endsection