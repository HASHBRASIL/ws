$(document).ready(function(even) {
	
	$("#tipoPessoa").select2();
	
	$( "#responsavel" ).autocomplete({
        source: "/empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#empresas_id_pai').val(ui.item.id);
        },
        search: function( event, ui ) {
            $('#empresas_id_pai').val("");
        }
    });
	
	$("#grupoGeografico").change(function(){
		var grupo = $('#grupoGeografico').val();
		
		if(grupo != 0){
			$('.grupo-geografico').hide();
			$('.grupo-geografico').find("span").removeClass("checked").children("input").removeAttr("checked");
		}else{
			$('.grupo-geografico').show();
		}
	});
	
	$("#tipoPessoa").change(function(){
		
		if($("#tipoPessoa").val() == 0){
			$(".line > .checker > span").removeClass ( 'checked' );
            $('#empresas_id_pai').val("");
			$('.pessoa-juridica').hide();
			$('.pessoa-fisica').hide();
			$('.todos').show();
			return false;
		}
		if($("#tipoPessoa").val() == 2){
			$(".line > .checker > span").removeClass ( 'checked' );
            $('#empresas_id_pai').val("");
			$('.todos').hide();
			$('.pessoa-juridica').hide();
			$('.pessoa-fisica').show();
			return false;
		}
		if($("#tipoPessoa").val() == 1){
			$(".line > .checker > span").removeClass ( 'checked' );
            $('#empresas_id_pai').val("");
			$('.todos').hide();
			$('.pessoa-fisica').hide();
			$('.pessoa-juridica').show();
			return false;
		}
	});
});
