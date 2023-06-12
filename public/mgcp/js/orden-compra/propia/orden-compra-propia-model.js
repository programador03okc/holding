class OrdenCompraPropiaModel {
    constructor(token) {
        this.token = token;
    }

    actualizarCampo = (id, campo, tipoOrden, valor) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.actualizar-campo'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, tipoOrden: tipoOrden, _token: this.token }
        });
    }

    actualizarFiltros = (data) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.actualizar-filtros'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }

    vincularOportunidad = (idOc, tipo, idOportunidad) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.vincular-oportunidad'),
            type: 'post',
            dataType: 'json',
            data: { idOc: idOc, tipo: tipo, idOportunidad: idOportunidad, _token: this.token }
        });
    }

    cambiarDespacho = (data) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.cambiar-despacho'),
            type: 'post',
            data: data
        });
    }

    obtenerInformacionAdicional = (id, tipo) => {
        //
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.obtener-informacion-adicional'),
            type: 'post',
            dataType: 'json',
            data: { id: id, tipo: tipo, _token: this.token }
        });
    }

    cambiarContacto = (idOrden, idContacto, tipoOrden) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.cambiar-contacto'),
            type: 'post',
            dataType: 'json',
            data: { idOrden: idOrden, idContacto: idContacto, tipoOrden: tipoOrden, _token: this.token }
        });
    }

    descargarOcDesdePortal = (idEmpresa, idCatalogo) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.acuerdo-marco.descargar-desde-portal'),
            type: 'post',
            dataType: 'json',
            data: { idEmpresa: idEmpresa, idCatalogo: idCatalogo, _token: this.token }
        });
    }

    obtenerProductos = (idOrden) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.acuerdo-marco.obtener-productos'),
            type: 'post',
            dataType: 'json',
            data: { idOrden: idOrden, _token: this.token }
        });
    }

    obtenerFechaDescargaEmpresa = (idEmpresa) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.acuerdo-marco.obtener-fecha-descarga-empresa'),
            type: 'post',
            data: { idEmpresa: idEmpresa, _token: this.token }
        });
    }

    actualizarFechaDescargaEmpresa = (idEmpresa) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.acuerdo-marco.actualizar-fecha-descarga-empresa'),
            type: 'post',
            data: { idEmpresa: idEmpresa, _token: this.token }
        });
    }

    /*obtenerDetallesParaDescargarOc = (id) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.obtener-detalles-para-descargar-oc'),
            type: 'post',
            data: { _token: this.token }
        });
    }*/


}