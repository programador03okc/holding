class ProductoCeamView {
    constructor(model) {
        this.model = model;
    }

    listar = () => {
        const $tableProductos = $("#tableProductos").DataTable({
            search: {
                smart: false
            },
            pageLength: 50,
            dom: "Bfrtip",
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $("#tableProductos_filter");
                const $input = $filter.find("input");
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on("keyup", (e) => {
                    if (e.key == "Enter") {
                        $("#btnBuscar").trigger("click");
                    }
                });
                $("#btnBuscar").on("click", (e) => {
                    $tableProductos.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $("#tableProductos_filter input").prop("disabled", false);
                $("#btnBuscar").html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop("disabled", false);
                $("#spanCantFiltros").html($("#modalFiltros").find("input[type=checkbox]:checked").length);
                $("#tableProductos_filter input").trigger("focus");
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[0, "asc"], [1, "asc"]],
            ajax: {
                url: route("mgcp.integraciones.ceam.productos.lista"),
                type: "POST",
                data: function ( params ) {
                    return Object.assign(params, Util.objectifyForm($("#formFiltros").serializeArray()))
                },
            },
            columns: [
                { data: "acuerdo_marco" },
                { data: "categoria"},
                { data: "producto" },
                { data: "marca", className: "text-center" },
                { data: "part_no", className: "text-center" },
                {
                    render: function (data, type, row) {
                        if (row.tipo == 'MGC') {
                            return '<span class="verde">' + row.tipo + '</span>';
                        } else {
                            return '<span class="rojo">' + row.tipo + '</span>';
                        }
                    }, searchable: false
                },
                {
                    render: function () {
                        return '';
                    }, searchable: false
                }
            ],
            buttons: [
                // {
                //     text: '<span class="fa fa-filter" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                //     action: function () {
                //         $("#modalFiltros").modal("show");
                //     }, className: "btn-sm"
                // },
                {
                    text: '<span class="fa fa-upload" aria-hidden="true"></span> Importar lista',
                    action: function () {
                        $("#modalImportar").modal("show");
                    }, className: "btn-sm"
                }
            ]
        });

        $tableProductos.on("search.dt", function () {
            $("#tableProductos_filter input").attr("disabled", true);
            $("#btnBuscar").html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop("disabled", true);
        });

        $tableProductos.on("processing.dt", function (e, settings, processing) {
            if (processing) {
                $(e.currentTarget).LoadingOverlay("show", {imageAutoResize: true, progress: true, imageColor: "#3c8dbc", zIndex: 20});
            } else {
                $(e.currentTarget).LoadingOverlay("hide", true);
            }
        });
    }

    importar = () => {
        $("#btnImportar").on("click", (e) => {
            const $boton = $(e.currentTarget);
            const $modal = $('#modalImportar');
            const $form = $("#formImportar")[0];
            const $data = new FormData($form);

            $boton.attr("disabled", true);
            $boton.html(Util.generarPuntosSvg() + "Subiendo");
            this.model.importarProductos($data).then((datos) => {
                Util.notify(datos.alert, datos.message);
                if (datos.response == "ok") {
                    $modal.modal("hide");
                }
            }).fail(() => {
                Util.notify("error", "Hubo un problema al importar los productos. Por favor vuelva a intentarlo");
            }).always(() => {
                $boton.prop("disabled", false);
                $boton.html("Registrar");
            });
        });
    }
}