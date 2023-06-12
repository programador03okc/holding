<div class="flash-message">
    <?php $__currentLoopData = ['danger', 'warning', 'success', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(Session::has('alert-' . $msg)): ?>
        <div class="alert alert-<?php echo e($msg); ?>">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?php switch($msg):
                case ('success'): ?>
                    <span class="glyphicon glyphicon-ok"></span> 
                <?php break; ?>
                <?php case ('danger'): ?>
                    <span class="glyphicon glyphicon-remove"></span> 
                <?php break; ?>
                <?php case ('warning'): ?>
                    <span class="glyphicon glyphicon-warning-sign"></span> 
                <?php break; ?>
            <?php endswitch; ?>
            <?php echo Session::get('alert-' . $msg); ?>

        </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/partials/flashmsg.blade.php ENDPATH**/ ?>