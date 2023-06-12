<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h3>Retiro de aprobación de cuadro de presupuesto</h3>
        <p>Se ha retiro la aprobación del cuadro de presupuesto <?php echo e($oportunidad->codigo_oportunidad); ?>. 
        <?php if($requerimiento!=null): ?>
         En el Sistema Agile, el requerimiento <?php echo e($requerimiento->codigo); ?> ha sido puesto en pausa hasta la reaprobación del cuadro.
        <?php endif; ?>
        </p>
        
        <h4>Detalles de la solicitud de retiro:</h4>
        <ul>
            <li>Solicitado por: <?php echo e($solicitud->enviadaPor->name); ?></li>
            <li>Motivo: <?php echo e($solicitud->comentario_solicitante); ?></li>
            <li>Aprobado por: <?php echo e($solicitud->enviadaA->name); ?></li>
            <li>Comentario del aprobador: <?php echo e($solicitud->comentario_aprobador); ?></li>
        </ul>
        <h4>Información de oportunidad:</h4>
        <ul>
            <li>Código: <?php echo e($oportunidad->codigo_oportunidad); ?></li>
            <li>Oportunidad: <?php echo e($oportunidad->oportunidad); ?></li>
            <li>Responsable: <?php echo e($oportunidad->responsable->name); ?></li>
            <li>Fecha límite: <?php echo e($oportunidad->fecha_limite); ?></li>
            <li>Cliente: <?php echo e($oportunidad->entidad->nombre); ?></li>
            <li>Grupo: <?php echo e($oportunidad->grupo->grupo); ?></li>
            <li>Tipo de negocio: <?php echo e($oportunidad->tiponegocio->tipo); ?></li>
        </ul>
        <p>
        Para ver el cuadro, haga clic <a href="<?php echo e($url); ?>">aquí</a>. 
        <?php if($requerimiento!=null): ?>
        Para ver la lista de requerimientos, haga clic <a href="https://erp.okccloud.com/logistica/gestion-logistica/requerimiento/listado/index">aquí</a>
        <?php endif; ?>
        </p>
        <hr>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\mgcp\resources\views/mgcp/cuadro-costo/email/retiro_aprobacion_cuadro.blade.php ENDPATH**/ ?>