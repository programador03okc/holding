<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{{$nombreSistema}}</title>
        <style>
            body {
                font-size: 12px;
            }
            table {
                margin: 0px auto;
            }
            h2 {
                text-align: center;
            }
            .text-justify {
                text-align: justify;
            }
            .text-center {
                text-align: center;
            }
            hr {
                border: none;
                height: 1px;
                color: #333;
                background-color: #333;
            }
            table tr td {
                vertical-align: top;
            }
            table.oportunidad td {
                padding-bottom: 10px;
            }
            table.oportunidad tr td:nth-child(1) {
                width: 10%;
                text-align: right;
                font-weight: bold;
                padding-right: 5px;
            }
            table.oportunidad tr td:nth-child(2) {
                width: 25%;
            }
            table.oportunidad tr td:nth-child(3) {
                width: 10%;
                text-align: right;
                font-weight: bold;
                padding-right: 5px;
            }

            table.oportunidad tr td:nth-child(5) {
                width: 10%;
                text-align: right;
                font-weight: bold;
                padding-right: 10px;
            }

            table.status tr td:nth-child(1) {
                width: 12%;
            }
            table.status tr td:nth-child(2) {
                width: 28%;
            }
            table.status tr td:nth-child(3) {
                width: 50%;
                padding-left: 15px;
                padding-right: 15px;
            }
            table.status tr td:nth-child(4) {
                width: 10%;
            }

            table.actividades tr td:nth-child(1) {
                width: 12%;
            }
            table.actividades tr td:nth-child(2) {
                width: 12%;
            }
            table.actividades tr td:nth-child(3) {
                width: 16%;
            }
            table.actividades tr td:nth-child(4) {
                padding-left: 15px;
            }

            table.comentarios tr td:nth-child(1) {
                width: 12%;
            }
            table.comentarios tr td:nth-child(2) {
                width: 28%;
            }
            table.comentarios tr td:nth-child(3) {
                padding-left: 15px;
            }
            table.status td, table.actividades td, table.comentarios td {
                padding-bottom: 15px;
            }

        </style>
    </head>
    <body>
        <img src="{{ asset('mgcp/img/logo.png') }}" width="130px">
        <h2 style="text-decoration: underline">Módulo de Gestión Comercial y Proyectos</h2>
        <h3 style="text-decoration: underline">Oportunidad {{$oportunidad->codigo_oportunidad}}</h3>
        <table class="oportunidad" style="width: 100%">
            <tbody>
                <tr>
                    <td style="width: 5%">Oportunidad:</td>
                    <td style="width: 95%" colspan="5" class="text-justify">{{$oportunidad->oportunidad}}</td>
                </tr>
                <tr>
                    <td style="width: 5%">Cliente:</td>
                    <td style="width: 15%">{{$oportunidad->entidad->nombre}}</td>
                    <td>Probabilidad:</td>
                    <td>{{ucwords($oportunidad->probabilidad)}}</td>
                    <td>Fecha límite:</td>
                    <td>{{$oportunidad->fecha_limite}}</td>
                </tr>
                <tr>
                    <td>Importe:</td>
                    <td>{{$oportunidad->monto}}</td>
                    <td>Margen:</td>
                    <td>{{$oportunidad->margen}}%</td>
                    <td>Responsable:</td>
                    <td>{{$oportunidad->responsable->name}}</td>
                </tr>
                <tr>
                    <td>Negocio:</td>
                    <td>{{$oportunidad->tipoNegocio->tipo}}</td>
                    <td>Grupo:</td>
                    <td>{{$oportunidad->grupo->grupo}}</td>
                    <td>Estado:</td>
                    <td>{{$oportunidad->estado->estado}}</td>
                </tr>
                <tr>
                    <td>Contacto:</td>
                    <td>{{$oportunidad->nombre_contacto}}</td>
                    <td>Teléfono:</td>
                    <td>{{$oportunidad->telefono_contacto}}</td>
                    <td>Correo:</td>
                    <td>{{$oportunidad->correo_contacto}}</td>
                </tr>
                <tr>
                    <td>Cargo:</td>
                    <td>{{$oportunidad->cargo_contacto}}</td>
                    <td>Reportado:</td>
                    <td>{{$oportunidad->reportado_por}}</td>
                </tr>
            </tbody>
        </table>

        <hr>

        <h3 style="text-decoration: underline">Status</h3>
        <table class="status" width="100%">
            @if (count($status)>0)
            @foreach ($status as $estado)
            <tr>
                <td>{{$estado->created_at}}</td>
                <td>{{$estado->usuario->name}}</td>
                <td class="text-justify">{{$estado->detalle}}</td>
                <td>{{$estado->estado->estado}}</td>
            </tr>
            @endforeach
            @else
            <tr><td>Sin status</td></tr>
            @endif
        </table>

        <hr>

        <h3 style="text-decoration: underline">Actividades</h3>
        <table class="actividades" width="100%">
            @if (count($actividades)>0)
            @foreach ($actividades as $actividad)
            <tr>
                <td>Inicio:<br>{{$actividad->fecha_inicio}}</td>
                <td>Fin:<br>{{$actividad->fecha_fin}}</td>
                <td>{{$actividad->usuario->name}}</td>
                <td class="text-justify">{{$actividad->detalle}}</td>
            </tr>
            @endforeach
            @else
            <tr><td>Sin actividades</td></tr>
            @endif
        </table>

        <hr>

        <h3 style="text-decoration: underline">Comentarios</h3>
        <table class="comentarios" width="100%">
            @if (count($comentarios)>0)
            @foreach ($comentarios as $comentario)
            <tr>
                <td>{{$comentario->created_at}}</td>
                <td>{{$comentario->publicado_por}}</td>
                <td class="text-justify">{{$comentario->comentario}}</td>
            </tr>
            @endforeach
            @else
            <tr><td>Sin comentarios</td></tr>
            @endif
        </table>
    </body>
</html>