<div class="modal fade" id="modalActualizarStockPrecio" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Actualizar <span class="tipo"></span> de producto en Perú Compras</h4>
            </div>
            <div class="modal-body">
                <p>
                    <strong>Producto: </strong><span class="producto"></span>
                </p>
                <table class="table">
                    <thead>
                        <tr>
                            <th width="15%" class="text-center">Empresa</th>
                            <th width="15%" class="text-center">Valor actual</th>
                            <th width="15%" class="text-center">Nuevo valor</th>
                            <th width="25%" class="text-center">Comentario</th>
                            <th width="15%" class="text-center">Operaciones</th>
                            <th width="15%" class="text-center">Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($empresa->id); ?>" data-empresa="<?php echo e($empresa->id); ?>">
                            <td class="text-center"><?php echo e($empresa->empresa); ?></td>
                            <td class="text-center valor-actual"></td>
                            <td class="text-center"><input type="text" class="form-control text-right" name="valor" placeholder="Valor"></td>
                            <td class="text-center"><textarea class="form-control" name="comentario" placeholder="Ingrese comentario"></textarea></td>
                            <td class="text-center"><button type="button" class="btn btn-default actualizar" data-empresa="<?php echo e($empresa->id); ?>">Actualizar</button></td>
                            <td class="text-center resultado"></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <p class="help-block"><strong>Nota:</strong> Los cambios pueden demorar hasta el día siguiente en hacer efecto.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/partials/acuerdo-marco/producto/actualizar-stock-precio.blade.php ENDPATH**/ ?>