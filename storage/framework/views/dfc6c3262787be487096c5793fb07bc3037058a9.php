

<?php $__env->startSection('cabecera'); ?>
<?php if($operacion=='editar'): ?> Editar <?php else: ?> Nuevo <?php endif; ?> usuario
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Usuarios</li>
    <?php if($operacion=='editar'): ?>
    <li class="active">Lista</li>
    <?php endif; ?>
    <li class="active"><?php if($operacion=='editar'): ?> Editar <?php else: ?> Nuevo <?php endif; ?></li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<?php
use App\Models\mgcp\Usuario\Rol;
use App\Models\mgcp\Usuario\RolUsuario;
?>

<div class="box box-solid">
    <div class="box-body">
        <?php echo $__env->make('mgcp.partials.errors', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('mgcp.partials.flashmsg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <form class="form-horizontal bloquear-boton" role="form" method="POST" action="<?php if($operacion=='editar'): ?> <?php echo e(route('mgcp.usuarios.actualizar')); ?> <?php else: ?> <?php echo e(route('mgcp.usuarios.registrar')); ?> <?php endif; ?>">
            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
            <input type="hidden" name="id_usuario" value="<?php if(isset($user)): ?><?php echo e($user->id); ?><?php endif; ?>">

            <div class="form-group">
                <label class="col-md-4 control-label">Nombre y apellido</label>
                <div class="col-md-6">
                    <input type="text" placeholder="Nombre y apellido" class="form-control" name="name" value="<?php echo e(isset($user) ? $user->name : old('name')); ?>">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Nombre corto</label>
                <div class="col-md-6">
                    <input type="text" placeholder="Nombre corto" class="form-control" name="nombre_corto" value="<?php echo e(isset($user) ? $user->nombre_corto : old('nombre_corto')); ?>">
                    <small class="help-block" style="margin-bottom: 0px">Ejemplo: Wilmar G.</small>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Correo</label>
                <div class="col-md-6">
                    <input type="email" placeholder="Correo electrónico" class="form-control" name="email" value="<?php echo e(isset($user) ? $user->email : old('email')); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Contraseña</label>
                <div class="col-md-6">
                    <input type="password" placeholder="Contraseña" class="form-control" name="password">
                    <small class="help-block" style="margin-bottom: 0px">Mínimo 6 caracteres, debe incluir al menos una mayúscula, una minúscula, un número y un caracter especial (asterisco, numeral, etc.)</small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Confirme contraseña</label>
                <div class="col-md-6">
                    <input type="password" placeholder="Confirmar contraseña" class="form-control" name="password_confirmation">
                </div>
            </div>
            <?php if($operacion=='editar'): ?>
            <div class="form-group">
                <label class="col-md-4 control-label">Activo</label>
                <div class="col-md-3">
                    <select class="form-control" name="activo">
                        <option value="1" <?php if(isset($user)): ?><?php echo e($user->activo ? "selected" : ""); ?><?php endif; ?>>Sí</option>
                        <option value="0" <?php if(isset($user)): ?><?php echo e($user->activo ? "" : "selected"); ?><?php endif; ?>>No</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label class="col-md-4 control-label">Roles:</label>
                <div class="col-md-6">
                    <?php $__currentLoopData = $tiposRol; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tiporol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <h5 style="font-weight: 700"><?php echo e($tiporol->tipo); ?></h5>
                    <?php $__currentLoopData = Rol::where('id_tipo',$tiporol->id)->orderBy('descripcion','asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="checkbox">
                        <label>
                            <input name="rol[]" value="<?php echo e($role->id); ?>" type="checkbox" <?php if($operacion=='editar' && RolUsuario::where('id_usuario',$user->id)->where('id_rol',$role->id)->count()>0): ?> checked <?php endif; ?>> <?php echo e($role->descripcion); ?>

                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary"><?php echo e($operacion=='editar' ? "Actualizar" : "Registrar"); ?></button>
                    <a id="a_regresar" href="<?php echo e(route('mgcp.usuarios.lista')); ?>" class="btn btn-default">Regresar a la lista</a>
                </div>
            </div>
        </form>


    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src='<?php echo e(asset("mgcp/js/util.js")); ?>'></script>
<script>
    $(document).ready(function () {
        var operacion = '<?php echo e($operacion); ?>';

        if (operacion == 'editar')
        {
            Util.seleccionarMenu("<?php echo e(route('mgcp.usuarios.lista')); ?>");
        } else
        {
            Util.seleccionarMenu(window.location);
        }

        $('form').submit(function () {
            $('button[type=submit]').prop('disabled', true);
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp\resources\views/mgcp/usuario/formulario.blade.php ENDPATH**/ ?>