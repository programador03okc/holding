class CentroCostoView {
    constructor(model) {
        this.model = model;
    }

    cargarModalCentroCostos(obj) {
        tempObjectBtnCentroCostos = obj;

        $('#modal-centro-costos').modal({
            show: true
        });
        this.listarCentroCostos();
    }

    listarCentroCostosEvent() {
        //requerimientoView.limpiarTabla('listaCentroCosto');
        $('#btnCentroCosto').on('click', (e) => {
            //$('#modal-centro-costos').find('div.modal-body').html('');
            $('#modal-centro-costos').modal('show');
        });

        $("#modal-centro-costos").on('shown.bs.modal', () => {
            $('#modal-centro-costos').find('div.modal-body').LoadingOverlay("show", {
                progress: true,
                imageColor: "#3c8dbc",
                zIndex: 2000
            });
            this.model.obtenerLista().then((data) => {
                this.construirCentroCostos(data);
            }).fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Problema al procesar su solicitud',
                    text: 'Por favor actualice la página e intente de nuevo'
                })
                $("#modal-centro-costos").modal('hide');
            }).always(() => {
                $('#modal-centro-costos div.modal-body').LoadingOverlay("hide", true);
            });
        });
        /*requerimientoCtrl.obtenerCentroCostos().then(function (res) {
            requerimientoView.construirCentroCostos(res);
        }).catch(function (err) {
            console.log(err)
        })*/
    }

    construirCentroCostos(data) {
        let html = '';
        data.forEach((padre, index) => {
            if (padre.id_padre == null) {
                html += `
                <div id='${index}' class="panel panel-primary" style="width:100%; overflow: auto;">
                <h5 class="panel-heading" style="margin: 0;">
                <i class="fa fa-chevron-down"></i>
                    &nbsp; ${padre.descripcion} 
                </h5>
                <div id="pres-${index}" class="oculto" style="width:100%;">
                    <table class="table table-bordered table-condensed partidas" id='listaCentroCosto' style="font-size:0.9em">
                        <thead>
                            <tr>
                            <td style="width:5%"></td>
                            <td style="width:90%"></td>
                            <td style="width:5%"></td>
                            </tr>
                        </thead>
                        <tbody>`;

                data.forEach(hijo => {
                    if (padre.id_centro_costo == hijo.id_padre) {
                        if ((hijo.id_padre > 0) && (hijo.estado == 1)) {
                            if (hijo.nivel == 2) {
                                html += `
                                <tr id="com-${hijo.id_centro_costo}">
                                    <td><strong>${hijo.codigo}</strong></td>
                                    <td><strong>${hijo.descripcion}</strong></td>
                                    <td style="width:5%; text-align:center;"></td>
                                </tr> `;
                            }
                        }
                        data.forEach(hijo3 => {
                            if (hijo.id_centro_costo == hijo3.id_padre) {
                                if ((hijo3.id_padre > 0) && (hijo3.estado == 1)) {
                                    // console.log(hijo3);
                                    if (hijo3.nivel == 3) {
                                        html += `
                                        <tr id="com-${hijo3.id_centro_costo}">
                                            <td>${hijo3.codigo}</td>
                                            <td>${hijo3.descripcion}</td>
                                            <td style="width:5%; text-align:center;">
                                                ${hijo3.seleccionable ? `<button class="btn btn-success btn-xs seleccionar" data-id="${hijo3.id_centro_costo}">Seleccionar</button>` : ''}
                                            </td>
                                        </tr> `;
                                    }
                                }
                                data.forEach(hijo4 => {
                                    if (hijo3.id_centro_costo == hijo4.id_padre) {
                                        console.log(hijo4);
                                        if ((hijo4.id_padre > 0) && (hijo4.estado == 1)) {
                                            if (hijo4.nivel == 4) {
                                                html += `
                                                <tr id="com-${hijo4.id_centro_costo}">
                                                    <td>${hijo4.codigo}</td>
                                                    <td>${hijo4.descripcion}</td>
                                                    <td style="width:5%; text-align:center;">
                                                        ${hijo4.seleccionable ? `<button class="btn btn-success btn-xs seleccionar" data-id="${hijo4.id_centro_costo}">Seleccionar</button>` : ''}
                                                    </td>
                                                </tr> `;
                                            }
                                        }
                                    }
                                });
                            }

                        });
                    }
                });
                html += `
                </tbody>
            </table>
        </div>
    </div>`;
            }
        });
        document.querySelector("div[name='centro-costos-panel']").innerHTML = html;
        $('#modal-centro-costos div.modal-body').LoadingOverlay("hide", true);
    }

    /*selectCentroCosto(idCentroCosto, codigo, descripcion) {
        tempObjectBtnCentroCostos.nextElementSibling.querySelector("input").value = idCentroCosto;
        tempObjectBtnCentroCostos.textContent = 'Cambiar';

        let tr = tempObjectBtnCentroCostos.closest("tr");
        tr.querySelector("p[class='descripcion-centro-costo']").textContent = descripcion
        tr.querySelector("p[class='descripcion-centro-costo']").setAttribute('title', codigo);
        this.updateCentroCostoItem(tempObjectBtnCentroCostos.nextElementSibling.querySelector("input"));
        $('#modal-centro-costos').modal('hide');
        tempObjectBtnCentroCostos = null;
        // componerTdItemDetalleRequerimiento();
    }*/
}