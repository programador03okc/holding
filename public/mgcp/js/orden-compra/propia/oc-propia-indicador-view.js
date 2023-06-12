class OcPropiaIndicadorView {
    constructor(model) {
        this.model = model;
    }

    obtenerIndicadorDiario = () => {
        this.model.obtenerIndicadorDiario().then((data)=>{
            const $contenedor=$('#liOcIndicadorDiario');
            $contenedor.find('span.monto-abreviado').html(data.monto).addClass(data.estado);
            $contenedor.find('i.icono').addClass(data.icono).addClass('text-'+data.estado);
            $contenedor.find('span.monto').html(data.monto);
            $contenedor.find('span.fecha').html(data.fecha);
        })
    }

    obtenerIndicadorMensual = () => {
        this.model.obtenerIndicadorMensual().then((data)=>{
            const $contenedor=$('#liOcIndicadorMensual');
            $contenedor.find('span.monto-abreviado').html(data.monto).addClass(data.estado);
            $contenedor.find('i.icono').addClass(data.icono).addClass('text-'+data.estado);
            $contenedor.find('span.monto').html(data.monto);
            $contenedor.find('span.fecha').html(data.fecha);
        })
    }
}