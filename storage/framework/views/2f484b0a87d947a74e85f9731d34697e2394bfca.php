<div class="modal fade" id="modalEntidad" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Información de cliente</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>
                <div class="contenedor" id="formEntidad">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-sm-6">
                            <fieldset style="margin-bottom: 10px;">
                                <legend>Detalles</legend>
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">DNI/RUC</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static ruc"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Nombre</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static nombre"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Dirección</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static direccion"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Ubigeo</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static ubigeo"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Semáforo</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static semaforo"></div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-sm-6">
                            <fieldset>
                                <legend>Responsable</legend>
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Nombre</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static responsable"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Cargo</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static cargo"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Teléfono</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static telefono"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Correo</label>
                                        <div class="col-sm-8">
                                            <div class="form-control-static correo"></div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <fieldset style="margin-top: 30px">
                        <legend>Contactos</legend>
                    </fieldset>
                    <?php if(Auth::user()->tieneRol(60)): ?>
                    <button style="margin-bottom: 15px" type="button" id="btnAgregarContactoEntidad" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table id="tableContactosEntidad" class="table table-condensed table-hover table-striped" style="font-size: small">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 20%">Nombre</th>
                                            <th class="text-center" style="width: 10%">Teléfono</th>
                                            <th class="text-center" style="width: 15%">Cargo</th>
                                            <th class="text-center" style="width: 10%">Correo</th>
                                            <th class="text-center">Dirección</th>
                                            <th class="text-center" style="width: 10%">Horario</th>
                                            <?php if(isset($seleccionarContacto)): ?>
                                            <th class="text-center">Usar en esta O/C</th>
                                            <?php endif; ?>
                                            <th class="text-center" style="width: 10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mensaje-final"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalAgregarContactoEntidad" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar contacto</h4>
            </div>
            <div class="modal-body">
                <form>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="idEntidad">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" class="form-control" maxlength="100" required name="nombre" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label>Teléfono *</label>
                        <input type="text" class="form-control" maxlength="50" required name="telefono" placeholder="Teléfono">
                    </div>
                    <div class="form-group">
                        <label>Cargo</label>
                        <input type="text" class="form-control" maxlength="50" name="cargo" placeholder="Cargo">
                    </div>
                    <div class="form-group">
                        <label>Correo</label>
                        <input type="text" class="form-control" maxlength="100" name="correo" placeholder="Correo">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" class="form-control" maxlength="255" name="direccion" placeholder="Dirección">
                    </div>
                    <div class="form-group">
                        <label>Horario</label>
                        <input type="text" class="form-control" maxlength="255" name="horario" placeholder="Horario">

                    </div>
                    <div class="form-group">
                        <span class="help-block">* Campos obligatorios</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnRegistrarContactoEntidad">Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarContactoEntidad" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar contacto</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <form>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="idContacto">
                    <input type="hidden" name="idEntidad">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" class="form-control" maxlength="100" required name="nombre" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label>Teléfono *</label>
                        <input type="text" class="form-control" maxlength="50" required name="telefono" placeholder="Teléfono">
                    </div>
                    <div class="form-group">
                        <label>Cargo</label>
                        <input type="text" class="form-control" maxlength="50" name="cargo" placeholder="Cargo">
                    </div>
                    <div class="form-group">
                        <label>Correo</label>
                        <input type="text" class="form-control" maxlength="100" name="correo" placeholder="Correo">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" class="form-control" maxlength="255" name="direccion" placeholder="Dirección">
                    </div>
                    <div class="form-group">
                        <label>Horario</label>
                        <input type="text" class="form-control" maxlength="255" name="horario" placeholder="Horario">

                    </div>
                    <div class="form-group">
                        <span class="help-block">* Campos obligatorios</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnActualizarContactoEntidad">Actualizar</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/partials/acuerdo-marco/entidad/detalles.blade.php ENDPATH**/ ?>