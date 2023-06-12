class EntidadModel {

  constructor(token) {
    this.token = token;
  }

  obtenerDetalles = (id) => {
    return $.ajax({
      url: route('mgcp.acuerdo-marco.entidades.detalles'),
      type: 'post',
      dataType: 'json',
      data: { id: id, _token: this.token }
    });
  }

  registrar = (data) => {
    return $.ajax({
      url: route('mgcp.acuerdo-marco.entidades.registrar'),
      type: 'post',
      dataType: 'json',
      data: data
    });
  }

  actualizar = (data) => {
    return $.ajax({
      url: route('mgcp.acuerdo-marco.entidades.actualizar'),
      type: 'post',
      dataType: 'json',
      data: data
    });
  }

  buscarRuc = (ruc) => {
    return $.ajax({
      url: route('mgcp.acuerdo-marco.entidades.buscar-ruc'),
      type: 'post',
      dataType: 'json',
      data:  { ruc: ruc, _token: this.token }
    });
  }

  buscarNombre = (nombre) => {
    return $.ajax({
      url: route('mgcp.acuerdo-marco.entidades.buscar-nombre'),
      type: 'post',
      dataType: 'json',
      data:  { nombre: nombre, _token: this.token }
    });
  }
}
