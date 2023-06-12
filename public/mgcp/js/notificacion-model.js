class NotificacionModel {

    constructor(token) {
      this.token = token;
    }
  
    obtenerNoLeidas = (id) => {
      return $.ajax({
        url: route("mgcp.notificaciones.cantidad-no-leidas"),
        type: 'post',
        dataType: 'json',
        data: { _token: this.token }
      });
    }
}