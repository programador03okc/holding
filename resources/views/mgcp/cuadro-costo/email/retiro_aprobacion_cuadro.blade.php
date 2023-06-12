<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h3>Retiro de aprobación de cuadro de presupuesto</h3>
        <p>Se ha retiro la aprobación del cuadro de presupuesto {{$oportunidad->codigo_oportunidad}}. 
        @if ($requerimiento!=null)
         En el Sistema Agile, el requerimiento {{$requerimiento->codigo}} ha sido puesto en pausa hasta la reaprobación del cuadro.
        @endif
        </p>
        
        <h4>Detalles de la solicitud de retiro:</h4>
        <ul>
            <li>Solicitado por: {{$solicitud->enviadaPor->name}}</li>
            <li>Motivo: {{$solicitud->comentario_solicitante}}</li>
            <li>Aprobado por: {{$solicitud->enviadaA->name}}</li>
            <li>Comentario del aprobador: {{$solicitud->comentario_aprobador}}</li>
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
        @if ($requerimiento!=null)
        Para ver la lista de requerimientos, haga clic <a href="https://erp.okccloud.com/logistica/gestion-logistica/requerimiento/listado/index">aquí</a>
        @endif
        </p>
        <hr>
    </body>
</html>
