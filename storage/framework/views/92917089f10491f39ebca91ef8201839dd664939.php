<header class="main-header">
    <a href="<?php echo e(route('mgcp.home')); ?>" class="logo">
        <span class="logo-mini">MGC</span>
        <span class="logo-lg">Gestión Comercial</span>
    </a>

    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button"><span class="sr-only">Toggle navigation</span></a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php if(Auth::user()->tieneRol(125)): ?>
                <li class="dropdown notifications-menu indicador" id="liOcIndicadorDiario">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="monto-abreviado"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">O/C propias de hoy (<span class="fecha"></span>)</li>
                        <li>
                            <ul class="menu">
                                <li><a href="#"><i class="fa icono"></i> <span class="monto"></span></a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="dropdown notifications-menu indicador" id="liOcIndicadorMensual">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="monto-abreviado"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">
                            O/C propias del mes (<span class="fecha"></span>)
                        </li>
                        <li>
                            <ul class="menu">
                                <li><a href="#"><i class="fa icono"></i> <span class="monto"></span></a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="dropdown notifications-menu">
                    <a href="<?php echo e(route('mgcp.notificaciones.lista')); ?>" class="dropdown-toggle">
                        <i class="fa fa-bell-o"></i>
                        <span id="spanNotificaciones" class="label label-default">0</span>
                    </a>
                    <!--
                    <ul class="dropdown-menu">
                        <li class="header">Tienes 0 notifications</li>
                        <li>
                            <ul class="menu">
                                <li>
                                    <a href="#"><i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                                        page and may cause design problems
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="<?php echo e(route('mgcp.notificaciones.lista')); ?>">Ver todo</a></li>
                    </ul>
                    -->
                </li>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="hidden-xs"><?php if(!is_null(Auth::user())): ?> <?php echo e(Auth::user()->name); ?> <?php endif; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?php echo e(route('mgcp.perfil.cambiar-password')); ?>" class="btn btn-default btn-flat">Cambiar contraseña</a>
                            </div>
                            <div class="pull-right">
                                <a href="<?php echo e(route('logout')); ?>" class="btn btn-default btn-flat">Cerrar sesión</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header><?php /**PATH C:\xampp\htdocs\mgcp\resources\views/mgcp/layouts/header.blade.php ENDPATH**/ ?>