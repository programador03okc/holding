<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h3>Aprobación de cuadro de presupuesto</h3>
    <p>El cuadro de presupuesto {{$oportunidad->codigo_oportunidad}} ha sido aprobado.
        @if($replicacionRequerimiento!=null)
        Adicionalmente, en el Sistema Agile, 
        @switch ($replicacionRequerimiento->estado)
        @case('sin_cambios')
        el requerimiento {{$replicacionRequerimiento->requerimiento->codigo}} no ha sufrido cambios.
        @break
        @case('nuevo')
        se ha creado el requerimiento {{$replicacionRequerimiento->requerimiento->codigo}}.
        @break
        @case('actualizado')
        el requerimiento {{$replicacionRequerimiento->requerimiento->codigo}} ha sido actualizado.
        @break
        @case('por_regularizar')
        se ha marcado el requerimiento {{$replicacionRequerimiento->requerimiento->codigo}} al estado "Por regularizar".
        @break
        @endswitch
        @endif
    </p>

    <h4>Detalles de solicitud:</h4>
    <ul>
        <li>Aprobado por: {{$autor->name}}</li>
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