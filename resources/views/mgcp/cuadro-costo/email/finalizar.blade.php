<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h3>Cuadro de presupuesto finalizado</h3>
        {{$autor->name}} ha finalizado el cuadro de presupuesto {{$oportunidad->codigo_oportunidad}}.
        <br>
        
        <h4>Información de oportunidad:</h4>
        <ul>
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
