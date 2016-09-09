$(document).ready(function(){
	
	$('.recycle').click(function(){
		var id = $(this).attr("value");
		
		$.ajax({
            type: "POST",
            url: '/empresa/empresa/recycle-empresa-ajax',
            data: {id : id},
            success: function(data){
                if(data.success == true){
                	$.messageBox("Registro restaurado com sucesso", 'success');
                	$("#recycle-"+id).parent("td").parent("tr").fadeOut("slow");
                }else{
                	$.messageBox("O Registro não pode ser restaurado com sucesso", 'error');
                }
            }
        });
	});
	
});