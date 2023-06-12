class NotificacionView {
    constructor(model) {
      this.model = model;
    }

    obtenerNoLeidas=()=>{
        const $spanNotificaciones = $('#spanNotificaciones');
        this.model.obtenerNoLeidas().then((data)=>{
            $spanNotificaciones.html(data.mensaje);
            if (data.mensaje > 0) {
                $spanNotificaciones.removeClass('label-default');
                $spanNotificaciones.addClass('label-warning');
            } else {
                $spanNotificaciones.removeClass('label-warning');
                $spanNotificaciones.addClass('label-default');
            }
        })
    }
}