<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="es">
    <head>
        <title>Módulo de Gestión Comercial y Proyectos - OK Computer</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <?php

        use App\mgcp\Proyecto\Comentario;
        use App\mgcp\Proyecto\Actividad;
        ?>
        <img src="{{ asset('img/logo.png') }}" width="130px">
        <h2 style="text-decoration: underline">Módulo de Gestión Comercial y Proyectos</h2>
        <h3 style="text-decoration: underline">Proyecto {{$proyecto->codigo_proyecto}}</h3>
        <table class="oportunidad" width="100%">
            <tbody>
                <tr>
                    <td>Cliente:</td>
                    <td>{{$proyecto->entidad->razon_social}}</td>
                    <td>Responsable:</td>
                    <td>{{$proyecto->responsable->name}}</td>
                    <td>Avance:</td>
                    <td>{{$proyecto->fase_porcentaje}}%</td>
                </tr>
                <tr>
                    <td>Monto:</td>
                    <td>{{$proyecto->monto_format}}</td>
                    <td>Cierre:</td>
                    <td>{{$proyecto->fecha_cierre}}</td>
                    <td>Urgencia:</td>
                    <td>{{$proyecto->urgencia}}</td>
                </tr>
                <tr>
                    <td>Estado:</td>
                    <td>{{$proyecto->estado->estado}}</td>
                    <td>Proyecto:</td>
                    <td colspan="3" class="text-justify">{{$proyecto->nombre}}</td>
                </tr>
            </tbody>
        </table>
        @foreach ($proyectosFases as $fase)
        <?php
        $comentarios = Comentario::where('proyecto_fase_id', $fase->id)->orderBy('fecha', 'asc')->get();
        $actividades = Actividad::where('proyecto_fase_id', $fase->id)->orderBy('fecha_creacion', 'asc')->get();
        ?>
        <hr>
        <h4><u>Fase {{$fase->fase_id}} - {{$fase->descripcionFase->descripcion}}</u></h4>
        <h4>Detalle:</h4>
        <table style="margin-left:auto; margin-right:auto; margin-bottom: 10px" width="90%">
            <tr>
                <td>{{$fase->detalles}}</td>
            </tr>
        </table>
        <h4>Comentarios:</h4>
        <table style="margin-left:auto; margin-right:auto; margin-bottom: 10px" width="90%">
            @if (count($comentarios)>0)
            <thead>
                <tr>
                    <th style="text-align: left"><strong>Fecha</strong></th>
                    <th style="text-align: left"><strong>Autor</strong></th>
                    <th style="text-align: left"><strong>Comentario</strong></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($comentarios as $comentario)
                <tr>
                    <td style="width: 30%;vertical-align: top">{{$comentario->fecha}}</td>
                    <td style="width: 20%;vertical-align: top; padding-right:10px">{{$comentario->usuario->name}}</td>
                    <td style="width: 50%;vertical-align: top; text-align: justify">{{$comentario->comentario}}</td>
                </tr>
                @endforeach
            </tbody>
            @else
            <tr>
                <td style="vertical-align: top">Sin comentarios</td>
            </tr>
            @endif
        </table>
        <h4>Actividades:</h4>
        <table style="margin-left:auto; margin-right:auto; margin-bottom: 10px; width: 90%">
            @if (count($actividades)>0)
            <thead>
                <tr>
                    <th style="text-align: left"><strong>Inicio</strong></th>
                    <th style="text-align: left"><strong>Fin</strong></th>
                    <th style="text-align: left"><strong>Responsable</strong></th>
                    <th style="text-align: left"><strong>Detalle</strong></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($actividades as $actividad)
                <tr>
                    <td style="width: 15%;vertical-align: top">{{$actividad->fecha_inicio}}</td>
                    <td style="width: 15%;vertical-align: top">{{$actividad->fecha_fin}}</td>
                    <td style="width: 20%;vertical-align: top; padding-right:10px">{{$actividad->responsable}}</td>
                    <td style="width: 50%;vertical-align: top; text-align: justify">{{$actividad->actividad}}</td>
                </tr>
                @endforeach 
            </tbody>
            @else
            <tr>
                <td style="vertical-align: top">Sin actividades</td>
            </tr>
            @endif
        </table>
        @endforeach

    </body>
</html>
