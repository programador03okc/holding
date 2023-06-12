<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">PRINCIPAL</li>
            <li><a href="<?php echo e(route('mgcp.notificaciones.lista')); ?>"><i class="fa fa-bell-o"></i> <span> Notificaciones</span></a></li>
            
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i><span> Indicadores</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Dashboard
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.indicadores.dashboard.index')); ?>"><i class="fa fa-circle-o"></i> Comercial</a></li>
                            <li><a href="<?php echo e(route('mgcp.indicadores.meta.dashboard')); ?>"><i class="fa fa-circle-o"></i> Contabilidad</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Registro de Metas
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.indicadores.meta.meta-empresa-mensual')); ?>"><i class="fa fa-circle-o"></i> Por Empresa</a></li>
                            <li><a href="<?php echo e(route('mgcp.indicadores.meta.meta-division-mensual')); ?>"><i class="fa fa-circle-o"></i> Por División</a></li>
                            <!-- <li><a href="<?php echo e(route('mgcp.indicadores.meta.meta-corporativo-mensual')); ?>"><i class="fa fa-circle-o"></i> Por Corporativo</a></li> -->
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Reporte
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.indicadores.ventas-empresa')); ?>"><i class="fa fa-circle-o"></i> Por Empresa</a></li>
                            <li><a href="<?php echo e(route('mgcp.indicadores.ventas-division')); ?>"><i class="fa fa-circle-o"></i> Por División</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-pie-chart" aria-hidden="true"></i><span> Oportunidades</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('mgcp.oportunidades.nueva')); ?>"><i class="fa fa-circle-o"></i> Nueva</a></li>
                    <li><a href="<?php echo e(route('mgcp.oportunidades.lista')); ?>"><i class="fa fa-circle-o"></i> Lista</a></li>
                    <li><a href="<?php echo e(route('mgcp.oportunidades.resumen')); ?>"><i class="fa fa-circle-o"></i> Resumen</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i><span> Órdenes de compra</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Propias
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.ordenes-compra.propias.lista')); ?>"><i class="fa fa-circle-o"></i> Lista de O/C</a></li>
                            
                            <li><a href="<?php echo e(route('mgcp.ordenes-compra.propias.directas.nueva')); ?>"><i class="fa fa-circle-o"></i> Nueva O/C directa</a></li>

                            <li><a href="<?php echo e(route('mgcp.ordenes-compra.propias.indicadores.configuracion')); ?>"><i class="fa fa-circle-o"></i> Config. indicadores</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Públicas
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.ordenes-compra.publicas.lista')); ?>"><i class="fa fa-circle-o"></i> Lista de O/C</a></li>
                            <li><a href="<?php echo e(route('mgcp.ordenes-compra.publicas.analisis-ocp.lista')); ?>"><i class="fa fa-circle-o"></i> Análisis de O/C</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-th" aria-hidden="true"></i><span> Cuadros de presupuesto</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('mgcp.cuadro-costos.lista')); ?>"><i class="fa fa-circle-o"></i> Lista</a></li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Reportes
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.cuadro-costos.reportes.pendientes-cierre.index')); ?>"><i class="fa fa-circle-o"></i> Pendientes de cierre</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Ajustes
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.cuadro-costos.ajustes.aprobadores.index')); ?>"><i class="fa fa-circle-o"></i> Aprobadores</a></li>
                            <li><a href="<?php echo e(route('mgcp.cuadro-costos.ajustes.tipo-cambio.index')); ?>"><i class="fa fa-circle-o"></i> Tipo de cambio</a></li>
                            <li><a href="<?php echo e(route('mgcp.cuadro-costos.ajustes.fondos-proveedores.index')); ?>"><i class="fa fa-circle-o"></i> Fondos de proveedores</a></li>
                            <li><a href="<?php echo e(route('mgcp.cuadro-costos.ajustes.fondos-microsoft.index')); ?>"><i class="fa fa-circle-o"></i> Fondos de microsoft</a></li>
                            <li><a href="<?php echo e(route('mgcp.cuadro-costos.ajustes.licencias.index')); ?>"><i class="fa fa-circle-o"></i> Licencias</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-university" aria-hidden="true"></i><span> Acuerdo marco</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.notificaciones.lista')); ?>"><i class="fa fa-bell"></i> Notificaciones</a></li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Productos
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.productos.lista')); ?>"><i class="fa fa-circle-o"></i> Lista</a></li>
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.productos.historial-actualizaciones.lista')); ?>"><i class="fa fa-circle-o"></i> Historial de act.</a></li>
                            
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Proformas
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="treeview">
                                <a href="#"><i class="fa fa-circle-o"></i> Individual
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.vista-anterior.index')); ?>"><i class="fa fa-circle-o"></i> Compra ordinaria</a></li>
                                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.nueva-vista.index')); ?>"><i class="fa fa-circle-o"></i> Compra o. (nueva)</a></li>
                                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.proformas.individual.gran-compra.vista-anterior.index')); ?>"><i class="fa fa-circle-o"></i> Gran compra</a></li>
                                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.proformas.individual.gran-compra.nueva-vista.index')); ?>"><i class="fa fa-circle-o"></i> Gran c. (nueva)</a></li>
                                </ul>
                            </li>
                            <li class="treeview">
                                <a href="#"><i class="fa fa-circle-o"></i> Paquete
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.proformas.paquete.compra-ordinaria.index')); ?>"><i class="fa fa-circle-o"></i> Compra ordinaria</a></li>
                                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.proformas.paquete.gran-compra.index')); ?>"><i class="fa fa-circle-o"></i> Gran compra</a></li>
                                </ul>
                            </li>
                            <li class="treeview">
                                <a href="#"><i class="fa fa-circle-o"></i> Exportar
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.proformas.exportar.index')); ?>"><i class="fa fa-circle-o"></i> Proformas</a></li>
                                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.proformas.exportar.entidades')); ?>"><i class="fa fa-circle-o"></i> Entidades</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Empresas OKC
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.empresas.cambiar-claves')); ?>"><i class="fa fa-circle-o"></i> Cambiar claves</a></li>
                        </ul>
                    </li>
                    <li><a href="<?php echo e(route('mgcp.acuerdo-marco.entidades.lista')); ?>"><i class="fa fa-circle-o"></i> Entidades</a></li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Descargar
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.descargar.proformas.index')); ?>"><i class="fa fa-circle-o"></i> Proformas</a></li>
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.descargar.nuevos-productos.index')); ?>"><i class="fa fa-circle-o"></i> Nuevos productos</a></li>
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.descargar.productos-adjudicados.index')); ?>"><i class="fa fa-circle-o"></i> Productos adjudicados</a></li>
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.descargar.ordenes-compra-publicas.index')); ?>"><i class="fa fa-circle-o"></i> O/C públicas</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Publicar
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.publicar.stock-productos.index')); ?>"><i class="fa fa-circle-o"></i> Stock de productos</a></li>
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.publicar.stock-empresa.index')); ?>"><i class="fa fa-circle-o"></i> Stock por empresa</a></li>
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.publicar.plazos-entrega.index')); ?>"><i class="fa fa-circle-o"></i> Plazos de entrega</a></li>
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.publicar.nuevos-precios.index')); ?>"><i class="fa fa-circle-o"></i> Nuevos precios</a></li>
                            <li><a href="<?php echo e(route('mgcp.acuerdo-marco.publicar.nuevos-productos.index')); ?>"><i class="fa fa-circle-o"></i> Nuevos productos</a></li>
                        </ul>
                    </li>

                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-inbox" aria-hidden="true"></i><span> Integraciones</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('mgcp.integraciones.ceam.productos.index')); ?>"><i class="fa fa-circle-o"></i> Productos CEAM</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-user-circle-o" aria-hidden="true"></i><span> Usuarios</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('mgcp.usuarios.nuevo')); ?>"><i class="fa fa-circle-o"></i> Nuevo</a></li>
                    <li><a href="<?php echo e(route('mgcp.usuarios.lista')); ?>"><i class="fa fa-circle-o"></i> Lista</a></li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Logs
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?php echo e(route('mgcp.usuarios.logs.inicios-sesion.index')); ?>"><i class="fa fa-circle-o"></i> Inicios de sesión</a></li>
                            <li><a href="<?php echo e(route('mgcp.usuarios.logs.actividades-usuario.index')); ?>"><i class="fa fa-circle-o"></i> Actividades de usuarios</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
</aside><?php /**PATH C:\xampp\htdocs\mgcp\resources\views/mgcp/layouts/aside.blade.php ENDPATH**/ ?>