//Muestra la trazabilidad
function formatTimeLine(table_id, id, row, token) {
    $.ajax({
        type: 'POST',
        url: route('mgcp.ordenes-compra.propias.trazabilidad-despacho'),
        // url: 'getTrazabilidadOrdenDespacho/' + id,
        data: {id: id, _token: token},
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            // console.log(response.length);
/*
            if (response.length > 0) {
                var html = `<div style="overflow-x:scroll;">
                <div class="row" >
                <div class="col-md-12">
                
                  <div style="display:inline-block;width:100%;">
                    <ul class="timeline timeline-horizontal">
                    <input type="button" id="btn_cerrar_transportista" class="btn btn-success" 
                        onClick="agregarEstadoEnvio(${id});" value="Agregar"/>`;

                response.forEach(element => {

                    if (element.accion == 2) {
                        html += `<li class="timeline-item">
                        <div class="timeline-badge bggreendark"><i class="glyphicon glyphicon-time"></i></div>
                        <div class="timeline-panel bordergreendark">
                            <div class="timeline-heading">
                            <p><small class="text-muted colorgreendark">${element.fecha_transportista !== null
                                ? formatDate(element.fecha_transportista) + '<br>'
                                : ''}
                            <strong>${element.estado_doc.toUpperCase()}</strong><br>
                            ${element.observacion !== null ? element.observacion + '<br>' : ''}
                            ${element.razon_social_transportista !== null ? element.razon_social_transportista + '<br>' : 'Propia'}
                            ${element.codigo_envio !== null ? ('Cod.Envío: ' + element.codigo_envio + '<br>') : ''}
                            ${element.importe_flete !== null ? ('<strong>Flete real: S/' + element.importe_flete + (element.credito ? ' (Crédito)' : '') + '</strong>') : ''}</small><br></p>
                            </div>
                        </div>
                        </li>`;
                    }
                    else {
                        html += `<li class="timeline-item">
                        <div class="timeline-badge ${element.accion == 3 ? 'bggreenlight' :
                                ((element.accion == 4 || element.accion == 5) ? 'bgyellow' :
                                    (element.accion == 6 ? 'bgfuxia' :
                                        (element.accion == 7 ? 'bgorange' : 'bgdark')))}">
                        <i class="glyphicon glyphicon-time"></i></div>
                        <div class="timeline-panel ${element.accion == 3 ? 'bordergreenlight' :
                                ((element.accion == 4 || element.accion == 5) ? 'borderyellow' :
                                    (element.accion == 6 ? 'borderfuxia' :
                                        (element.accion == 7 ? 'borderorange' : 'borderdark')))} ">

                            ${element.accion !== 1 ?
                                `<i class="fas fa-trash-alt red" style="cursor:pointer;" title="Eliminar estado de envío"
                                onClick="eliminarTrazabilidadEnvio(${element.id_obs});"></i>`
                                : ''}
    
                            <div class="timeline-heading">
                            <p><small class="text-muted ${element.accion == 3 ? 'colorgreenlight' :
                                ((element.accion == 4 || element.accion == 5) ? 'coloryellow' :
                                    (element.accion == 6 ? 'colorfuxia' :
                                        (element.accion == 7 ? 'colororange' : 'colordark')))}">
                            ${element.accion == 1 ?
                                (element.fecha_despacho_real !== null ? formatDate(element.fecha_despacho_real) + '<br>' :
                                    (element.fecha_despacho !== null ? formatDate(element.fecha_despacho) : ''))
                                : (element.fecha_estado !== null ? formatDate(element.fecha_estado) + '<br>' : '')}
                            <strong>${element.estado_doc.toUpperCase()}</strong><br>
                            ${element.observacion !== null ? element.observacion + '<br>' : ''}
                            ${element.nombre_corto}<br>
                            ${element.gasto_extra !== null ? ('<strong>Gasto extra: S/' + element.gasto_extra + '</strong><br>') : ''}
                            </small></p>
                            </div>
                        </div>
                        </li>`;
                    }
                });
                // ${element.plazo_excedido ? '<strong class="red">PLAZO EXCEDIDO</strong><br>' : ''}
                html += `</ul>
                        </div>
                    </div>
                </div>
                </div>`;
                row.child(html).show();
            } else {
                Lobibox.notify("warning", {
                    title: false,
                    size: "mini",
                    rounded: true,
                    sound: false,
                    delayIndicator: false,
                    msg: "Aún no hay estados de envío ingresados."
                });
            }*/
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}