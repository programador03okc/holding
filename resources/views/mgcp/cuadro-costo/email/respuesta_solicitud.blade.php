<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h3>Respuesta de solicitud</h3>
        {{$autor->name}} ha respondido su solicitud de {{$tipoSolicitud}} de cuadro de presupuesto.
        <br>
        <h4>Detalles:</h4>
        <ul>
            <li>Aprobada: <strong>@if ($solicitud->aprobada==1) Sí @else No @endif</strong></li>
            <li>Comentario: {{$solicitud->comentario_aprobador}}</li>
        </ul>
        
        <h4>Información de oportunidad:</h4>
        <ul>
            <li>Código: {{$oportunidad->codigo_oportunidad}}</li>
            <li>Oportunidad: {{$oportunidad->oportunidad}}</li>
            <li>Responsable: {{$oportunidad->responsable->name}}</li>
            <li>Fecha límite: {{$oportunidad->fecha_limite}}</li>
            <li>Cliente: {{$oportunidad->entidad->nombre}}</li>
            <li>Grupo: {{$oportunidad->grupo->grupo}}</li>
            <li>Tipo de negocio: {{$oportunidad->tiponegocio->tipo}}</li>
        </ul>
        <p>
        Para ver el cuadro, haga clic <a href="{{$url}}">aquí</a>.
        </p>
        <hr>
    </body>
</html>
