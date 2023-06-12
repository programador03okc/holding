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
        <h3>Oportunidad eliminada</h3>
        {{$autor->name}} ha eliminado una oportunidad: 
        <ul>
            <li>Código: {{$oportunidad->codigo_oportunidad}}</li>
            <li>Oportunidad: {{$oportunidad->oportunidad}}</li>
            <li>Responsable: {{$oportunidad->responsable->name}}</li>
            <li>Cliente: {{$oportunidad->entidad->nombre}}</li>
            <li>Fecha límite: {{$oportunidad->fecha_limite}}</li>
            <li>Grupo: {{$oportunidad->grupo->grupo}}</li>
            <li>Tipo de negocio: {{$oportunidad->tiponegocio->tipo}}</li>
            <li>Probabilidad: {{ucfirst($oportunidad->probabilidad)}}</li>
            <li>Importe: {{$oportunidad->monto}}</li>
            <li>Margen: {{$oportunidad->margen}}%</li>
            <li>Estado: {{$oportunidad->estado->estado}}</li>
            <li>Último status: {{$oportunidad->ultimo_status}}</li>
        </ul>
        <p>
            Póngase en contacto con el responsable a través de su correo: <a href="mailto:{{$autor->email}}">{{$autor->email}}</a>.
        </p>
        <hr>
    </body>
</html>
