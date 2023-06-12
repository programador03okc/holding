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
        <h3>Nueva oportunidad registrada</h3>
        {{$autor->name}} ha registrado una oportunidad: 
        <ul>
            <li>Código: {{$oportunidad->codigo_oportunidad}}</li>
            <li>Oportunidad: {{$oportunidad->oportunidad}}</li>
            @if ($oportunidad->responsable->name!=$autor->name)
            <li>Responsable: {{$oportunidad->responsable->name}}</li>
            @endif
            <li>Fecha límite: {{$oportunidad->fecha_limite}}</li>
            <li>Cliente: {{$oportunidad->entidad->nombre}}</li>
            <li>Grupo: {{$oportunidad->grupo->grupo}}</li>
            <li>Tipo de negocio: {{$oportunidad->tiponegocio->tipo}}</li>
            <li>Probabilidad: {{ucwords($oportunidad->probabilidad)}}</li>
            <li>Importe: {{$oportunidad->monto}}</li>
            <li>Margen: {{$oportunidad->margen}}%</li>
            <li>Status: {{$oportunidad->ultimo_status}}</li>
            <li>Estado: {{$oportunidad->estado->estado}}</li>
        </ul>
        <p>
        Para ver más detalles, haga clic <a href="{{ route('mgcp.oportunidades.detalles',['oportunidad'=>$oportunidad->id]) }}">aquí</a>.
        </p>
        <hr>
    </body>
</html>
