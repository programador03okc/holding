class Indicador {
    constructor (token) {
        this.token = token;
    }

    searchCompany = (periodo, id, tipo) => {
        var row_int = '';
        var row_ext = '';
        var row_met = '';
        
        $.ajax({
            type: "POST",
            url: route('mgcp.indicadores.busqueda'),
            data: {
                _token: this.token,
                periodo: periodo,
                idperiodo: id,
                type: 'company'
            },
            dataType: "JSON",
            beforeSend: function(){
                $(document.body).LoadingOverlay("show", {
                    imageAutoResize: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: function (response) {
                $(document.body).LoadingOverlay("hide", true);
                if (response.response == 'ok') {
                    var datax = response.data;

                    var sum_ene_int = 0;
                    var sum_feb_int = 0;
                    var sum_mar_int = 0;
                    var sum_abr_int = 0;
                    var sum_may_int = 0;
                    var sum_jun_int = 0;
                    var sum_jul_int = 0;
                    var sum_ago_int = 0;
                    var sum_set_int = 0;
                    var sum_oct_int = 0;
                    var sum_nov_int = 0;
                    var sum_dic_int = 0;

                    var sum_ene_ext = 0;
                    var sum_feb_ext = 0;
                    var sum_mar_ext = 0;
                    var sum_abr_ext = 0;
                    var sum_may_ext = 0;
                    var sum_jun_ext = 0;
                    var sum_jul_ext = 0;
                    var sum_ago_ext = 0;
                    var sum_set_ext = 0;
                    var sum_oct_ext = 0;
                    var sum_nov_ext = 0;
                    var sum_dic_ext = 0;

                    datax.forEach(function(element, index) {
                        var ene_int = parseFloat(element.interna.ene);
                        var feb_int = parseFloat(element.interna.feb);
                        var mar_int = parseFloat(element.interna.mar);
                        var abr_int = parseFloat(element.interna.abr);
                        var may_int = parseFloat(element.interna.may);
                        var jun_int = parseFloat(element.interna.jun);
                        var jul_int = parseFloat(element.interna.jul);
                        var ago_int = parseFloat(element.interna.ago);
                        var set_int = parseFloat(element.interna.set);
                        var oct_int = parseFloat(element.interna.oct);
                        var nov_int = parseFloat(element.interna.nov);
                        var dic_int = parseFloat(element.interna.dic);

                        sum_ene_int += ene_int;
                        sum_feb_int += feb_int;
                        sum_mar_int += mar_int;
                        sum_abr_int += abr_int;
                        sum_may_int += may_int;
                        sum_jun_int += jun_int;
                        sum_jul_int += jul_int;
                        sum_ago_int += ago_int;
                        sum_set_int += set_int;
                        sum_oct_int += oct_int;
                        sum_nov_int += nov_int;
                        sum_dic_int += dic_int;

                        var ene_ext = parseFloat(element.externa.ene);
                        var feb_ext = parseFloat(element.externa.feb);
                        var mar_ext = parseFloat(element.externa.mar);
                        var abr_ext = parseFloat(element.externa.abr);
                        var may_ext = parseFloat(element.externa.may);
                        var jun_ext = parseFloat(element.externa.jun);
                        var jul_ext = parseFloat(element.externa.jul);
                        var ago_ext = parseFloat(element.externa.ago);
                        var set_ext = parseFloat(element.externa.set);
                        var oct_ext = parseFloat(element.externa.oct);
                        var nov_ext = parseFloat(element.externa.nov);
                        var dic_ext = parseFloat(element.externa.dic);

                        sum_ene_ext += ene_ext;
                        sum_feb_ext += feb_ext;
                        sum_mar_ext += mar_ext;
                        sum_abr_ext += abr_ext;
                        sum_may_ext += may_ext;
                        sum_jun_ext += jun_ext;
                        sum_jul_ext += jul_ext;
                        sum_ago_ext += ago_ext;
                        sum_set_ext += set_ext;
                        sum_oct_ext += oct_ext;
                        sum_nov_ext += nov_ext;
                        sum_dic_ext += dic_ext;

                        var txt_ene_int = Util.formatoNumero(ene_int, 2);
                        var txt_feb_int = Util.formatoNumero(feb_int, 2);
                        var txt_mar_int = Util.formatoNumero(mar_int, 2);
                        var txt_abr_int = Util.formatoNumero(abr_int, 2);
                        var txt_may_int = Util.formatoNumero(may_int, 2);
                        var txt_jun_int = Util.formatoNumero(jun_int, 2);
                        var txt_jul_int = Util.formatoNumero(jul_int, 2);
                        var txt_ago_int = Util.formatoNumero(ago_int, 2);
                        var txt_set_int = Util.formatoNumero(set_int, 2);
                        var txt_oct_int = Util.formatoNumero(oct_int, 2);
                        var txt_nov_int = Util.formatoNumero(nov_int, 2);
                        var txt_dic_int = Util.formatoNumero(dic_int, 2);

                        var txt_ene_ext = Util.formatoNumero(ene_ext, 2);
                        var txt_feb_ext = Util.formatoNumero(feb_ext, 2);
                        var txt_mar_ext = Util.formatoNumero(mar_ext, 2);
                        var txt_abr_ext = Util.formatoNumero(abr_ext, 2);
                        var txt_may_ext = Util.formatoNumero(may_ext, 2);
                        var txt_jun_ext = Util.formatoNumero(jun_ext, 2);
                        var txt_jul_ext = Util.formatoNumero(jul_ext, 2);
                        var txt_ago_ext = Util.formatoNumero(ago_ext, 2);
                        var txt_set_ext = Util.formatoNumero(set_ext, 2);
                        var txt_oct_ext = Util.formatoNumero(oct_ext, 2);
                        var txt_nov_ext = Util.formatoNumero(nov_ext, 2);
                        var txt_dic_ext = Util.formatoNumero(dic_ext, 2);

                        row_int += `
                        <tr>
                            <td class="text-left">`+ element.empresa +`</td>
                            <td class="text-right">`+ txt_ene_int +`</td>
                            <td class="text-right">`+ txt_feb_int +`</td>
                            <td class="text-right">`+ txt_mar_int +`</td>
                            <td class="text-right">`+ txt_abr_int +`</td>
                            <td class="text-right">`+ txt_may_int +`</td>
                            <td class="text-right">`+ txt_jun_int +`</td>
                            <td class="text-right">`+ txt_jul_int +`</td>
                            <td class="text-right">`+ txt_ago_int +`</td>
                            <td class="text-right">`+ txt_set_int +`</td>
                            <td class="text-right">`+ txt_oct_int +`</td>
                            <td class="text-right">`+ txt_nov_int +`</td>
                            <td class="text-right">`+ txt_dic_int +`</td>
                        </tr>`;

                        row_ext += `
                        <tr>
                            <td class="text-left">`+ element.empresa +`</td>
                            <td class="text-right">`+ txt_ene_ext +`</td>
                            <td class="text-right">`+ txt_feb_ext +`</td>
                            <td class="text-right">`+ txt_mar_ext +`</td>
                            <td class="text-right">`+ txt_abr_ext +`</td>
                            <td class="text-right">`+ txt_may_ext +`</td>
                            <td class="text-right">`+ txt_jun_ext +`</td>
                            <td class="text-right">`+ txt_jul_ext +`</td>
                            <td class="text-right">`+ txt_ago_ext +`</td>
                            <td class="text-right">`+ txt_set_ext +`</td>
                            <td class="text-right">`+ txt_oct_ext +`</td>
                            <td class="text-right">`+ txt_nov_ext +`</td>
                            <td class="text-right">`+ txt_dic_ext +`</td>
                        </tr>`;
                    });

                    var txt_sum_ene_int = Util.formatoNumero(sum_ene_int, 2);
                    var txt_sum_feb_int = Util.formatoNumero(sum_feb_int, 2);
                    var txt_sum_mar_int = Util.formatoNumero(sum_mar_int, 2);
                    var txt_sum_abr_int = Util.formatoNumero(sum_abr_int, 2);
                    var txt_sum_may_int = Util.formatoNumero(sum_may_int, 2);
                    var txt_sum_jun_int = Util.formatoNumero(sum_jun_int, 2);
                    var txt_sum_jul_int = Util.formatoNumero(sum_jul_int, 2);
                    var txt_sum_ago_int = Util.formatoNumero(sum_ago_int, 2);
                    var txt_sum_set_int = Util.formatoNumero(sum_set_int, 2);
                    var txt_sum_oct_int = Util.formatoNumero(sum_oct_int, 2);
                    var txt_sum_nov_int = Util.formatoNumero(sum_nov_int, 2);
                    var txt_sum_dic_int = Util.formatoNumero(sum_dic_int, 2);

                    var txt_sum_ene_ext = Util.formatoNumero(sum_ene_ext, 2);
                    var txt_sum_feb_ext = Util.formatoNumero(sum_feb_ext, 2);
                    var txt_sum_mar_ext = Util.formatoNumero(sum_mar_ext, 2);
                    var txt_sum_abr_ext = Util.formatoNumero(sum_abr_ext, 2);
                    var txt_sum_may_ext = Util.formatoNumero(sum_may_ext, 2);
                    var txt_sum_jun_ext = Util.formatoNumero(sum_jun_ext, 2);
                    var txt_sum_jul_ext = Util.formatoNumero(sum_jul_ext, 2);
                    var txt_sum_ago_ext = Util.formatoNumero(sum_ago_ext, 2);
                    var txt_sum_set_ext = Util.formatoNumero(sum_set_ext, 2);
                    var txt_sum_oct_ext = Util.formatoNumero(sum_oct_ext, 2);
                    var txt_sum_nov_ext = Util.formatoNumero(sum_nov_ext, 2);
                    var txt_sum_dic_ext = Util.formatoNumero(sum_dic_ext, 2);

                    var meta = response.meta;
                    var meta_ene = (meta != null) ? meta.ene : 0;
                    var meta_feb = (meta != null) ? meta.feb : 0;
                    var meta_mar = (meta != null) ? meta.mar : 0;
                    var meta_abr = (meta != null) ? meta.abr : 0;
                    var meta_may = (meta != null) ? meta.may : 0;
                    var meta_jun = (meta != null) ? meta.jun : 0;
                    var meta_jul = (meta != null) ? meta.jul : 0;
                    var meta_ago = (meta != null) ? meta.ago : 0;
                    var meta_set = (meta != null) ? meta.set : 0;
                    var meta_oct = (meta != null) ? meta.oct : 0;
                    var meta_nov = (meta != null) ? meta.nov : 0;
                    var meta_dic = (meta != null) ? meta.dic : 0;

                    var txt_meta_ene = Util.formatoNumero(meta_ene, 2);
                    var txt_meta_feb = Util.formatoNumero(meta_feb, 2);
                    var txt_meta_mar = Util.formatoNumero(meta_mar, 2);
                    var txt_meta_abr = Util.formatoNumero(meta_abr, 2);
                    var txt_meta_may = Util.formatoNumero(meta_may, 2);
                    var txt_meta_jun = Util.formatoNumero(meta_jun, 2);
                    var txt_meta_jul = Util.formatoNumero(meta_jul, 2);
                    var txt_meta_ago = Util.formatoNumero(meta_ago, 2);
                    var txt_meta_set = Util.formatoNumero(meta_set, 2);
                    var txt_meta_oct = Util.formatoNumero(meta_oct, 2);
                    var txt_meta_nov = Util.formatoNumero(meta_nov, 2);
                    var txt_meta_dic = Util.formatoNumero(meta_dic, 2);

                    var dif_ene = parseFloat(sum_ene_ext) - parseFloat(meta_ene);
                    var dif_feb = parseFloat(sum_feb_ext) - parseFloat(meta_feb);
                    var dif_mar = parseFloat(sum_mar_ext) - parseFloat(meta_mar);
                    var dif_abr = parseFloat(sum_abr_ext) - parseFloat(meta_abr);
                    var dif_may = parseFloat(sum_may_ext) - parseFloat(meta_may);
                    var dif_jun = parseFloat(sum_jun_ext) - parseFloat(meta_jun);
                    var dif_jul = parseFloat(sum_jul_ext) - parseFloat(meta_jul);
                    var dif_ago = parseFloat(sum_ago_ext) - parseFloat(meta_ago);
                    var dif_set = parseFloat(sum_set_ext) - parseFloat(meta_set);
                    var dif_oct = parseFloat(sum_oct_ext) - parseFloat(meta_oct);
                    var dif_nov = parseFloat(sum_nov_ext) - parseFloat(meta_nov);
                    var dif_dic = parseFloat(sum_dic_ext) - parseFloat(meta_dic);

                    var class_ene = (dif_ene < 0) ? 'text-danger' : 'text-primary';
                    var class_feb = (dif_feb < 0) ? 'text-danger' : 'text-primary';
                    var class_mar = (dif_mar < 0) ? 'text-danger' : 'text-primary';
                    var class_abr = (dif_abr < 0) ? 'text-danger' : 'text-primary';
                    var class_may = (dif_may < 0) ? 'text-danger' : 'text-primary';
                    var class_jun = (dif_jun < 0) ? 'text-danger' : 'text-primary';
                    var class_jul = (dif_jul < 0) ? 'text-danger' : 'text-primary';
                    var class_ago = (dif_ago < 0) ? 'text-danger' : 'text-primary';
                    var class_set = (dif_set < 0) ? 'text-danger' : 'text-primary';
                    var class_oct = (dif_oct < 0) ? 'text-danger' : 'text-primary';
                    var class_nov = (dif_nov < 0) ? 'text-danger' : 'text-primary';
                    var class_dic = (dif_dic < 0) ? 'text-danger' : 'text-primary';

                    var txt_dif_ene = Util.formatoNumero(dif_ene, 2);
                    var txt_dif_feb = Util.formatoNumero(dif_feb, 2);
                    var txt_dif_mar = Util.formatoNumero(dif_mar, 2);
                    var txt_dif_abr = Util.formatoNumero(dif_abr, 2);
                    var txt_dif_may = Util.formatoNumero(dif_may, 2);
                    var txt_dif_jun = Util.formatoNumero(dif_jun, 2);
                    var txt_dif_jul = Util.formatoNumero(dif_jul, 2);
                    var txt_dif_ago = Util.formatoNumero(dif_ago, 2);
                    var txt_dif_set = Util.formatoNumero(dif_set, 2);
                    var txt_dif_oct = Util.formatoNumero(dif_oct, 2);
                    var txt_dif_nov = Util.formatoNumero(dif_nov, 2);
                    var txt_dif_dic = Util.formatoNumero(dif_dic, 2);

                    row_int += `
                    <tr>
                        <th class="text-right">Total</th>
                        <th class="text-right">`+ txt_sum_ene_int +`</th>
                        <th class="text-right">`+ txt_sum_feb_int +`</th>
                        <th class="text-right">`+ txt_sum_mar_int +`</th>
                        <th class="text-right">`+ txt_sum_abr_int +`</th>
                        <th class="text-right">`+ txt_sum_may_int +`</th>
                        <th class="text-right">`+ txt_sum_jun_int +`</th>
                        <th class="text-right">`+ txt_sum_jul_int +`</th>
                        <th class="text-right">`+ txt_sum_ago_int +`</th>
                        <th class="text-right">`+ txt_sum_set_int +`</th>
                        <th class="text-right">`+ txt_sum_oct_int +`</th>
                        <th class="text-right">`+ txt_sum_nov_int +`</th>
                        <th class="text-right">`+ txt_sum_dic_int +`</th>
                    </tr>`;

                    row_ext += `
                    <tr>
                        <th class="text-right">Total</th>
                        <th class="text-right">`+ txt_sum_ene_ext +`</th>
                        <th class="text-right">`+ txt_sum_feb_ext +`</th>
                        <th class="text-right">`+ txt_sum_mar_ext +`</th>
                        <th class="text-right">`+ txt_sum_abr_ext +`</th>
                        <th class="text-right">`+ txt_sum_may_ext +`</th>
                        <th class="text-right">`+ txt_sum_jun_ext +`</th>
                        <th class="text-right">`+ txt_sum_jul_ext +`</th>
                        <th class="text-right">`+ txt_sum_ago_ext +`</th>
                        <th class="text-right">`+ txt_sum_set_ext +`</th>
                        <th class="text-right">`+ txt_sum_oct_ext +`</th>
                        <th class="text-right">`+ txt_sum_nov_ext +`</th>
                        <th class="text-right">`+ txt_sum_dic_ext +`</th>
                    </tr>`;

                    row_met += `
                        <tr>
                            <td class="text-left">Ventas Terceros</td>
                            <td class="text-right">`+ txt_sum_ene_ext +`</td>
                            <td class="text-right">`+ txt_sum_feb_ext +`</td>
                            <td class="text-right">`+ txt_sum_mar_ext +`</td>
                            <td class="text-right">`+ txt_sum_abr_ext +`</td>
                            <td class="text-right">`+ txt_sum_may_ext +`</td>
                            <td class="text-right">`+ txt_sum_jun_ext +`</td>
                            <td class="text-right">`+ txt_sum_jul_ext +`</td>
                            <td class="text-right">`+ txt_sum_ago_ext +`</td>
                            <td class="text-right">`+ txt_sum_set_ext +`</td>
                            <td class="text-right">`+ txt_sum_oct_ext +`</td>
                            <td class="text-right">`+ txt_sum_nov_ext +`</td>
                            <td class="text-right">`+ txt_sum_dic_ext +`</td>
                        </tr>
                        <tr>
                            <td class="text-left">Meta</td>
                            <td class="text-right">`+ txt_meta_ene +`</td>
                            <td class="text-right">`+ txt_meta_feb +`</td>
                            <td class="text-right">`+ txt_meta_mar +`</td>
                            <td class="text-right">`+ txt_meta_abr +`</td>
                            <td class="text-right">`+ txt_meta_may +`</td>
                            <td class="text-right">`+ txt_meta_jun +`</td>
                            <td class="text-right">`+ txt_meta_jul +`</td>
                            <td class="text-right">`+ txt_meta_ago +`</td>
                            <td class="text-right">`+ txt_meta_set +`</td>
                            <td class="text-right">`+ txt_meta_oct +`</td>
                            <td class="text-right">`+ txt_meta_nov +`</td>
                            <td class="text-right">`+ txt_meta_dic +`</td>
                        </tr>
                        <tr>
                            <th class="text-right">Total</th>
                            <th class="text-right `+ class_ene +`">`+ txt_dif_ene +`</th>
                            <th class="text-right `+ class_feb +`">`+ txt_dif_feb +`</th>
                            <th class="text-right `+ class_mar +`">`+ txt_dif_mar +`</th>
                            <th class="text-right `+ class_abr +`">`+ txt_dif_abr +`</th>
                            <th class="text-right `+ class_may +`">`+ txt_dif_may +`</th>
                            <th class="text-right `+ class_jun +`">`+ txt_dif_jun +`</th>
                            <th class="text-right `+ class_jul +`">`+ txt_dif_jul +`</th>
                            <th class="text-right `+ class_ago +`">`+ txt_dif_ago +`</th>
                            <th class="text-right `+ class_set +`">`+ txt_dif_set +`</th>
                            <th class="text-right `+ class_oct +`">`+ txt_dif_oct +`</th>
                            <th class="text-right `+ class_nov +`">`+ txt_dif_nov +`</th>
                            <th class="text-right `+ class_dic +`">`+ txt_dif_dic +`</th>
                        </tr>`;

                    ////////////// resultados
                    if (tipo == 1) {
                        $("#result-int").html(row_int);
                        $("#result-ext").html(row_ext);
                        $("#result-meta").html(row_met);
                    }
                }
            }
        }).fail( function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }

    searchDivision = (periodo, id, tipo) => {
        var row_int = '';
        var row_ext = '';
        var row_met = '';
        
        $.ajax({
            type: "POST",
            url: route('mgcp.indicadores.busqueda'),
            data: {
                _token: this.token,
                periodo: periodo,
                idperiodo: id,
                type: 'division'
            },
            dataType: "JSON",
            beforeSend: function(){
                $(document.body).LoadingOverlay("show", {
                    imageAutoResize: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: function (response) {
                $(document.body).LoadingOverlay("hide", true);
                if (response.response == 'ok') {
                    var datax = response.data;

                    var sum_ene_int = 0;
                    var sum_feb_int = 0;
                    var sum_mar_int = 0;
                    var sum_abr_int = 0;
                    var sum_may_int = 0;
                    var sum_jun_int = 0;
                    var sum_jul_int = 0;
                    var sum_ago_int = 0;
                    var sum_set_int = 0;
                    var sum_oct_int = 0;
                    var sum_nov_int = 0;
                    var sum_dic_int = 0;

                    var sum_ene_ext = 0;
                    var sum_feb_ext = 0;
                    var sum_mar_ext = 0;
                    var sum_abr_ext = 0;
                    var sum_may_ext = 0;
                    var sum_jun_ext = 0;
                    var sum_jul_ext = 0;
                    var sum_ago_ext = 0;
                    var sum_set_ext = 0;
                    var sum_oct_ext = 0;
                    var sum_nov_ext = 0;
                    var sum_dic_ext = 0;

                    datax.forEach(function(element, index) {
                        var ene_int = parseFloat(element.interna.ene);
                        var feb_int = parseFloat(element.interna.feb);
                        var mar_int = parseFloat(element.interna.mar);
                        var abr_int = parseFloat(element.interna.abr);
                        var may_int = parseFloat(element.interna.may);
                        var jun_int = parseFloat(element.interna.jun);
                        var jul_int = parseFloat(element.interna.jul);
                        var ago_int = parseFloat(element.interna.ago);
                        var set_int = parseFloat(element.interna.set);
                        var oct_int = parseFloat(element.interna.oct);
                        var nov_int = parseFloat(element.interna.nov);
                        var dic_int = parseFloat(element.interna.dic);

                        sum_ene_int += ene_int;
                        sum_feb_int += feb_int;
                        sum_mar_int += mar_int;
                        sum_abr_int += abr_int;
                        sum_may_int += may_int;
                        sum_jun_int += jun_int;
                        sum_jul_int += jul_int;
                        sum_ago_int += ago_int;
                        sum_set_int += set_int;
                        sum_oct_int += oct_int;
                        sum_nov_int += nov_int;
                        sum_dic_int += dic_int;

                        var ene_ext = parseFloat(element.externa.ene);
                        var feb_ext = parseFloat(element.externa.feb);
                        var mar_ext = parseFloat(element.externa.mar);
                        var abr_ext = parseFloat(element.externa.abr);
                        var may_ext = parseFloat(element.externa.may);
                        var jun_ext = parseFloat(element.externa.jun);
                        var jul_ext = parseFloat(element.externa.jul);
                        var ago_ext = parseFloat(element.externa.ago);
                        var set_ext = parseFloat(element.externa.set);
                        var oct_ext = parseFloat(element.externa.oct);
                        var nov_ext = parseFloat(element.externa.nov);
                        var dic_ext = parseFloat(element.externa.dic);

                        sum_ene_ext += ene_ext;
                        sum_feb_ext += feb_ext;
                        sum_mar_ext += mar_ext;
                        sum_abr_ext += abr_ext;
                        sum_may_ext += may_ext;
                        sum_jun_ext += jun_ext;
                        sum_jul_ext += jul_ext;
                        sum_ago_ext += ago_ext;
                        sum_set_ext += set_ext;
                        sum_oct_ext += oct_ext;
                        sum_nov_ext += nov_ext;
                        sum_dic_ext += dic_ext;

                        var txt_ene_int = Util.formatoNumero(ene_int, 2);
                        var txt_feb_int = Util.formatoNumero(feb_int, 2);
                        var txt_mar_int = Util.formatoNumero(mar_int, 2);
                        var txt_abr_int = Util.formatoNumero(abr_int, 2);
                        var txt_may_int = Util.formatoNumero(may_int, 2);
                        var txt_jun_int = Util.formatoNumero(jun_int, 2);
                        var txt_jul_int = Util.formatoNumero(jul_int, 2);
                        var txt_ago_int = Util.formatoNumero(ago_int, 2);
                        var txt_set_int = Util.formatoNumero(set_int, 2);
                        var txt_oct_int = Util.formatoNumero(oct_int, 2);
                        var txt_nov_int = Util.formatoNumero(nov_int, 2);
                        var txt_dic_int = Util.formatoNumero(dic_int, 2);

                        var txt_ene_ext = Util.formatoNumero(ene_ext, 2);
                        var txt_feb_ext = Util.formatoNumero(feb_ext, 2);
                        var txt_mar_ext = Util.formatoNumero(mar_ext, 2);
                        var txt_abr_ext = Util.formatoNumero(abr_ext, 2);
                        var txt_may_ext = Util.formatoNumero(may_ext, 2);
                        var txt_jun_ext = Util.formatoNumero(jun_ext, 2);
                        var txt_jul_ext = Util.formatoNumero(jul_ext, 2);
                        var txt_ago_ext = Util.formatoNumero(ago_ext, 2);
                        var txt_set_ext = Util.formatoNumero(set_ext, 2);
                        var txt_oct_ext = Util.formatoNumero(oct_ext, 2);
                        var txt_nov_ext = Util.formatoNumero(nov_ext, 2);
                        var txt_dic_ext = Util.formatoNumero(dic_ext, 2);

                        row_int += `
                        <tr id="int-`+ element.id +`">
                            <td class="text-left">`+ element.division +`</td>
                            <td class="text-right td-int-ene">`+ txt_ene_int +`</td>
                            <td class="text-right td-int-feb">`+ txt_feb_int +`</td>
                            <td class="text-right td-int-mar">`+ txt_mar_int +`</td>
                            <td class="text-right td-int-abr">`+ txt_abr_int +`</td>
                            <td class="text-right td-int-may">`+ txt_may_int +`</td>
                            <td class="text-right td-int-jun">`+ txt_jun_int +`</td>
                            <td class="text-right td-int-jul">`+ txt_jul_int +`</td>
                            <td class="text-right td-int-ago">`+ txt_ago_int +`</td>
                            <td class="text-right td-int-set">`+ txt_set_int +`</td>
                            <td class="text-right td-int-oct">`+ txt_oct_int +`</td>
                            <td class="text-right td-int-nov">`+ txt_nov_int +`</td>
                            <td class="text-right td-int-dic">`+ txt_dic_int +`</td>
                        </tr>`;

                        row_ext += `
                        <tr id="ext-`+ element.id +`">
                            <td class="text-left">`+ element.division +`</td>
                            <td class="text-right td-ext-ene">`+ txt_ene_ext +`</td>
                            <td class="text-right td-ext-feb">`+ txt_feb_ext +`</td>
                            <td class="text-right td-ext-mar">`+ txt_mar_ext +`</td>
                            <td class="text-right td-ext-abr">`+ txt_abr_ext +`</td>
                            <td class="text-right td-ext-may">`+ txt_may_ext +`</td>
                            <td class="text-right td-ext-jun">`+ txt_jun_ext +`</td>
                            <td class="text-right td-ext-jul">`+ txt_jul_ext +`</td>
                            <td class="text-right td-ext-ago">`+ txt_ago_ext +`</td>
                            <td class="text-right td-ext-set">`+ txt_set_ext +`</td>
                            <td class="text-right td-ext-oct">`+ txt_oct_ext +`</td>
                            <td class="text-right td-ext-nov">`+ txt_nov_ext +`</td>
                            <td class="text-right td-ext-dic">`+ txt_dic_ext +`</td>
                        </tr>`;
                    });
                    
                    ////////////// resultados
                    if (tipo == 1) {
                        $("#result-int").html(row_int);
                        $("#result-ext").html(row_ext);
                    }
                }

            }
        }).fail( function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }

    dashboard = (periodo = 0, id_periodo = 0, mes = 0, tipo) => {
        $.ajax({
            type: "POST",
            url: route('mgcp.indicadores.busqueda-dashboard'),
            data: {
                _token: this.token,
                periodo: periodo,
                id_periodo: id_periodo,
                mes: mes
            },
            dataType: "JSON",
            beforeSend: function(){
                $(document.body).LoadingOverlay("show", {
                    imageAutoResize: true,
                    imageColor: "#3c8dbc"
                });
            },
            success: function (response) {
                $(document.body).LoadingOverlay("hide", true);
				
				if (tipo == 'todo') {
                    $("#title-anual").text("Meta Anual del " + response.periodo);
                    $("#title-mensual").text("Meta mensual de " + response.nombre_mes);
                } else if (tipo == 'anual') {
                    $("#title-anual").text("Meta Anual del " + response.periodo);
                } else if (tipo == 'mensual') {
                    $("#title-mensual").text("Meta mensual de " + response.nombre_mes);
                }
				
                var total_meta = parseFloat(response.total_meta);
                var total_vent = parseFloat(response.total_venta);
                var mes_vent = parseFloat(response.ventas_mes);
                var mes_meta = parseFloat(response.meta_mes);
                
                var pct_total = (total_meta > 0) ? ((total_vent * 100) / total_meta) : 0;
                var dif_total = total_meta - total_vent;
                var txt_total_meta = $.number(total_meta, 2, '.', ',');
                var txt_total_vent = $.number(total_vent, 2, '.', ',');
                var txt_dif_total = $.number(dif_total, 2, '.', ',');
                var txt_pct_total = $.number(pct_total, 2, '.', ',');
                
                if ((tipo == 'todo') || (tipo == 'anual')) {
                    $("#meta_total").text('S/ ' + txt_total_meta);
                    $("#venta_total").text('S/ ' + txt_total_vent);
                    $("#diff_total").text('S/ ' + txt_dif_total);
                    $("#ptc_anual").text(txt_pct_total + '%');
                }
                
                var pct_mes = (mes_meta > 0) ? ((mes_vent * 100) / mes_meta) : 0;
                var dif_mes = mes_meta - mes_vent;
                var txt_mes_meta = $.number(mes_meta, 2, '.', ',');
                var txt_mes_vent = $.number(mes_vent, 2, '.', ',');
                var txt_dif_mes = $.number(dif_mes, 2, '.', ',');
                var txt_pct_mes = $.number(pct_mes, 2, '.', ',');
                
                if ((tipo == 'todo') || (tipo == 'mensual')) {
                    $("#meta_mes").text('S/ ' + txt_mes_meta);
                    $("#venta_mes").text('S/ ' + txt_mes_vent);
                    $("#diff_mes").text('S/ ' + txt_dif_mes);
                    $("#ptc_mensual").text(txt_pct_mes + '%');
                }
                
                var dif_ptc_tot = (100 - pct_total);
                var dif_ptc_mes = (100 - pct_mes);
                var txt_dif_ptc_tot = $.number(dif_ptc_tot, 2, '.', ',');
                var txt_dif_ptc_mes = $.number(dif_ptc_mes, 2, '.', ',');

                if (tipo == 'todo' || tipo == 'anual') {
                    var ctx = document.getElementById("chartAnual").getContext("2d");
                    if (window.graficoAnual) {
                        window.graficoAnual.clear();
                        window.graficoAnual.destroy();
                    }

                    window.graficoAnual = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Avance", "Faltante"],
                            datasets: [
                                    {
                                    data: [txt_pct_total, txt_dif_ptc_tot],
                                    backgroundColor: ["#46BFBD", "#F7464A"]
                                }
                            ]
                        }
                    });
                }

                if (tipo == 'todo' || tipo == 'mensual') {
                    var ctx = document.getElementById("chartMensual").getContext("2d");
                    if (window.graficoMensual) {
                        window.graficoMensual.clear();
                        window.graficoMensual.destroy();
                    }
                    
                    window.graficoMensual = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Avance", "Faltante"],
                            datasets: [
                                    {
                                    data: [txt_pct_mes, txt_dif_ptc_mes],
                                    backgroundColor: ["#46BFBD", "#F7464A"]
                                }
                            ]
                        }
                    });
                }
            }
        }).fail( function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}