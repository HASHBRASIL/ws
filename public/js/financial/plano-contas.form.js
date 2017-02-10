$(document).ready(function() {

    $("select[name='plc_id_pai']").on('change', function () {

        var plc_id_pai_text = $("select[name='plc_id_pai'] option:selected").text();
        var retorno = plc_id_pai_text.split(" ");
        var text = retorno[0] + ".";
        $("input[name='plc_cod_contabil']").val(text);
    });
});