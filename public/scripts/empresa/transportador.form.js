$(document).ready(function(){
    selectCidade();
    $('#estado').change(function(){
        selectCidade();
    });
});
function selectCidade(){
    var id_estado = $('#estado').val();
    if(id_estado == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: '/sis/cidade/pairs/id_estado/'+id_estado,
        success: function(data){
            var list = data.list;
            var html = "---- Selecione ----";
            list.each(function(key, value){
                html += "<option value='"+key+"'>"+value+"</option>";
            });
            $('#estado').html(html);
        }
    });
}