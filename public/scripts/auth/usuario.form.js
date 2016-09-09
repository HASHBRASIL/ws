$(document).ready(function(){
	
	$( "#editSenha" ).click(function() {
		  $("#passwordBox").fadeIn();
	});
	
	$('#usu_senha').pstrength();
	
	$('.basic-toggle-button').toggleButtons({
        width: 100,
        label: {
            enabled: "Sim",
            disabled: "Não"
        },
        style: {
            // Accepted values ["primary", "danger", "info", "success", "warning"] or nothing
            enabled: "info",
            disabled: "danger"
        }
    });
	
    $( "#vinculo" ).autocomplete({
        source: "/empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
        	
        	$.ajax({
                type: "POST",
                url: '/auth/usuario/get-vinculo-usuario-exist-ajax',
                data: {idEmpresa : ui.item.id},
                success: function(data){
                    if(data.success == true){
                    	if(data.exist == true){
                    		
                    		$("#valueMessage").prepend("Este vínculo não pode ser criado pois o mesmo já existe no sistema");
                    		$('#dialog_message').dialog({
                	            modal: true,
                	            dialogClass: 'ui-dialog-grey',
                	            position: [($(window).width() / 2) - (450 / 2), 200],
                	            resizable: true,
                	            title: "Validação de Vínculo de usuário",
                	            width: 450,
                	            height: 250,
                	            buttons: [
                	                      {
                			                 'class' : 'btn gree',
                			                 "text" : "Cancelar",
                			                click: function() {
                			                	$("#valueMessage").empty();
                			                  $( this ).dialog( "close" );
                			                }
                	                      }
                	              ],
                	              close: function(){
                	            	  $("#valueMessage").empty();
                	              }
                	        });
                    		
                        	$('#id_empresa').val("");
                        	$('#vinculo').val("");
                        	return false;
                        }else{
                        	$('#id_empresa').val(ui.item.id);
                        }
                    }else{
                    	alert("Não foi possível conectar ao servidor. AjaxErro01");
                    }
                }
            });
            
        },
        search: function( event, ui ) {
            $('#id_empresa').val("");
        }
      });
    
    var validation = false;
    var mensagem = "";
	$("#form_usuario").submit(function(){
		validation = false;
		
		if ($("#usu_senha").val() == "" ||  $("#usu_confirm_senha").val() == ""){
			
			if ($("#usu_id").val() == "" ){
		
			mensagem  += "Senha ou a confirmação da senha está(ão) vazia(s).</br>";
			validation = true;
			}
		}
			
		if ($("#usu_senha").val() != $("#usu_confirm_senha").val()){
			
			mensagem  += "Senhas não conferem.</br>";
			validation = true;
		}
		
		if ($('#id_empresa').val() == ""){
			
			mensagem  += "Selecione um vínculo para criar o acesso do usuário.</br>";
			validation = true;
		}
		
		if ($('#usu_senha').val().length <= 5){
			
			if ($("#usu_id").val() == ""){
				
				mensagem  += "A senha não possui o número mínimo de caractéres.</br>";
				validation = true;
				
			}else if ($("#usu_id").val() != "" && $("#usu_senha").val() != "" ||  $("#usu_confirm_senha").val() != ""){
				
				mensagem  += "A senha não possui o número mínimo de caractéres.</br>";
				validation = true;
				
			}
			
		}
		 
		if (validation == true){
	        $("#valueMessage").prepend(mensagem);
	        mensagem = "";
	        $('#dialog_message').dialog({
	            modal: true,
	            dialogClass: 'ui-dialog-grey',
	            position: [($(window).width() / 2) - (450 / 2), 200],
	            resizable: true,
	            title: "Validação de Vínculo de usuário",
	            width: 450,
	            height: 250,
	            buttons: [
	                      {
			                 'class' : 'btn gree',
			                 "text" : "Cancelar",
			                click: function() {
			                	$("#valueMessage").empty();
			                  $( this ).dialog( "close" );
			                }
	                      }
	              ],
	              close: function(){
	            	  $("#valueMessage").empty();
	              }
	        });
	        return false;
		}
	});
    	
});