$(document).ready(function(){
    $('#quantTrans1').setMask('99999999999999999');
    $('#quantTrans2').setMask('99999999999999999');
    $('#transReal1').datepicker();    
    $('#transReal2').datepicker();    
    $('#transData1').datepicker();    
    $('#transData2').datepicker(); 
    $("#estado").change(function(){
        var id_estado = $(this).val();
        $(this).selectCidade(id_estado);
        
    });
});
$.fn.selectCidade = function(id_estado){
    if(id_estado != null){
        $.ajax({
            type: "GET",
            url: '/sis/cidade/pairs/id_estado/'+id_estado,
            success: function(data){
                var list = data.list;
                var html =  "<option value='' >Selecione</option>";
                $.each(list, function(key, value){
                    html += "<option value='"+key+"'>"+value+"</option>";
                });
                $('#cidade').html(html);
            }
        });
    }
};

