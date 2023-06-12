<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h3>Nueva solicitud</h3>
        <p>{{$autor->name}} ha solicitado {{$tipoSolicitud}} de un cuadro de presupuesto.</p>  
        {{empty($comentario) ? '' : 'Comentario del usuario: '.$comentario.''}}
        <br>
        <h4>Información del cuadro:</h4>
        <ul>
            <li>Costo total: {{$cuadro->costo_total_format}}</li>
            <li>Precio de venta total: {{$cuadro->precio_venta_total_format}}</li>
            <li>Ganancia: {{$cuadro->ganancia_real_format}}</li>
            <li>Margen de ganancia: {{$cuadro->margen_ganancia_format}}</li>
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
        Para ver más detalles y responder la solicitud, haga clic <a href="{{$url}}">aquí</a>.
        </p>
        <hr>
    </body>
</html>
