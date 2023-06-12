<?php if(count($errors) > 0): ?>
<div class="alert alert-danger">
    <p>Se encontraron los siguientes errores:</p>
    <ul>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/partials/errors.blade.php ENDPATH**/ ?>