class PeruCompras {
    constructor(token) {
        this.token = token;
    }

    obtenerAcuerdos = (idEmpresa, pagina, ruta) => {
        return $.ajax({
            url: ruta,
            type: "post",
            dataType: "json",
            data: { idEmpresa: idEmpresa, pagina: pagina, _token: this.token }
        });
    }

    obtenerCatalogos = (idEmpresa, idAcuerdo, ruta) => {
        return $.ajax({
            url: ruta,
            type: "post",
            dataType: "json",
            data: { idEmpresa: idEmpresa, idAcuerdo: idAcuerdo, _token: this.token }
        });
    }

    obtenerProformas = (idEmpresa, idAcuerdo, idCatalogo, tipoProforma, tipoContratacion, diasAntiguedad, ruta) => {
        return $.ajax({
            url: ruta,
            type: "post",
            dataType: "json",
            data: { idEmpresa: idEmpresa, idAcuerdo: idAcuerdo, idCatalogo: idCatalogo, tipoProforma: tipoProforma, tipoContratacion: tipoContratacion, diasAntiguedad: diasAntiguedad, _token: this.token }
        });
    }

    obtenerProvincias = (idEmpresa, idDepartamento, ruta) => {
        return $.ajax({
            url: ruta,
            type: "post",
            dataType: "json",
            data: { idEmpresa: idEmpresa, idDepartamento: idDepartamento, _token: this.token }
        });
    }
}