<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h3><u>
                @if ($tipo=='status')
                Nuevo status 
                @endif
                @if ($tipo=='actividad')
                Nueva actividad 
                @endif
                @if ($tipo=='comentario')
                Nuevo comentario 
                @endif
                en oportunidad de negocio
            </u></h3>
        {{$autor->name}} ({{$autor->email}}) ha ingresado 
        @if ($tipo=='status')
        un status.
        @endif
        @if ($tipo=='actividad')
        una actividad.
        @endif
        @if ($tipo=='comentario')
        un comentario
        @endif
        : {{$data}}
        <br>
        <hr>
        
        <h4><u>Oportunidad:</u></h4>
        <table width="100%">
            <tr>
                <td style="vertical-align: top"><strong>Oportunidad:</strong></td><td style="vertical-align: top; text-align: justify" colspan="3">{{$oportunidad->oportunidad}}</td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong>Código:</strong></td><td style="vertical-align: top">{{$oportunidad->codigo_oportunidad}}</td>
                <td style="vertical-align: top"><strong>Responsable:</strong></td><td style="vertical-align: top">{{$oportunidad->responsable->name}}</td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong>Fecha límite:</strong></td><td style="vertical-align: top">{{$oportunidad->fecha_limite}}</td>
                <td style="vertical-align: top"><strong>Cliente:</strong></td><td style="vertical-align: top">{{$oportunidad->entidad->nombre}}</td>
            </tr>
            <tr> 
                <td style="vertical-align: top"><strong>Grupo:</strong></td><td style="vertical-align: top">{{$oportunidad->grupo->grupo}}</td>
                <td style="vertical-align: top"><strong>Tipo de negocio:</strong></td><td style="vertical-align: top">{{$oportunidad->tiponegocio->tipo}}</td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong>Probabilidad:</strong></td><td style="vertical-align: top">{{ucwords($oportunidad->probabilidad)}}</td>
                <td style="vertical-align: top"><strong>Importe:</strong></td><td style="vertical-align: top">{{$oportunidad->monto}}</td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong>Margen:</strong></td><td style="vertical-align: top">{{$oportunidad->margen}}%</td>
                <td style="vertical-align: top"><strong>Contacto:</strong></td><td style="vertical-align: top">{{$oportunidad->nombre_contacto}}</td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong>Teléfono:</strong></td><td style="vertical-align: top">{{$oportunidad->telefono_contacto}}</td>
                <td style="vertical-align: top"><strong>Cargo:</strong></td><td style="vertical-align: top">{{$oportunidad->cargo_contacto}}</td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong>Correo:</strong></td><td style="vertical-align: top">{{$oportunidad->correo_contacto}}</td>
                <td style="vertical-align: top"><strong>Estado:</strong></td><td  style="vertical-align: top">{{$oportunidad->estado->estado}}</td>
            </tr>
        </table>

        <hr>

        <h4><u>Status</u></h4>
        @php
        $status=$oportunidad->status;
        @endphp
        <table class="status" width="100%">
            <thead>
                <tr>
                    <th style="text-align: center">Fecha</th>
                    <th style="text-align: center">Usuario</th>
                    <th style="text-align: center">Detalles</th>
                    <th style="text-align: center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @if (count($status)>0)

                @foreach ($status as $fila)
                <tr @if ($tipo=='status' && $data==$fila->detalle) style="font-weight: bold" @endif>
                     <td style="width: 15%;vertical-align: top;text-align: center">{{$fila->created_at}}</td>
                    <td style="width: 20%;vertical-align: top;text-align: center">{{$fila->usuario->name}}</td>
                    <td style="width: 45%;vertical-align: top; text-align: justify">
                        {{$fila->detalle}}  
                    </td>
                    <td style="width: 20%;vertical-align: top; padding-left: 20px; text-align: center">{{$fila->estado->estado}}</td>
                </tr>
                @endforeach

                @else
                <tr>
                    <td colspan="4" style="vertical-align: top;text-align: center">Sin status</td>
                </tr>
                @endif
            </tbody>
        </table>

        <hr>

        <h4><u>Actividades</u></h4>
        @php
        $actividades=$oportunidad->actividades;
        @endphp

        <table width="100%">
            <thead>
                <tr>
                    <th style="text-align: center">Inicio</th>
                    <th style="text-align: center">Fin</th>
                    <th style="text-align: center">Usuario</th>
                    <th style="text-align: center">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @if (count($actividades)>0)

                @foreach ($actividades as $actividad)
                <tr @if ($tipo=='actividad' && $data==$actividad->detalle) style="font-weight: bold" @endif>
                     <td style="width: 15%;vertical-align: top;text-align: center">{{$actividad->fecha_inicio}}</td>
                    <td style="width: 15%;vertical-align: top;text-align: center">{{$actividad->fecha_fin}}</td>
                    <td style="width: 20%;vertical-align: top;text-align: center">{{$actividad->usuario->name}}</td>
                    <td style="width: 50%;vertical-align: top; text-align: justify">{{$actividad->detalle}}</td>
                </tr>
                @endforeach

                @else
                <tr><td colspan="4" style="vertical-align: top;text-align: center">Sin actividades</td></tr>
                @endif
            </tbody>
        </table>

        <hr>

        <h4><u>Comentarios</u></h4>
        @php
        $comentarios=$oportunidad->comentarios;
        @endphp
        <table class="comentarios" width="100%">
            <thead>
                <tr>
                    <th style="text-align: center;">Fecha</th>
                    <th style="text-align: center;">Usuario</th>
                    <th style="text-align: center;">Comentario</th>
                </tr>
            </thead>
            <tbody>
                @if (count($comentarios)>0)
                @foreach ($comentarios as $comentario)

                <tr @if ($tipo=='comentario' && $data==$comentario->comentario) style="font-weight: bold" @endif>

                     <td  style="width: 20%;vertical-align: top">{{$comentario->created_at}}</td>
                    <td  style="width: 20%;vertical-align: top">{{$comentario->publicado_por}}</td>
                    <td style="width: 60%;vertical-align: top; text-align: justify">{{$comentario->comentario}}</td>
                </tr>
                @endforeach

                @else
                <tr>
                    <td style="vertical-align: top;text-align: center;">Sin comentarios</td>
                </tr>
                @endif
            </tbody>
        </table>

    </body>
</html>
