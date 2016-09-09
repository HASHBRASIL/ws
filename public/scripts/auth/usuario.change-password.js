$(document).ready(function(){
	
	$('#usu_senha').pstrength();
	
	var validation = false;
    var mensagem = "";
	$("#form_change_password").submit(function(){
		validation = false;
		
		if ($("#usu_senha").val() == "" ||  $("#usu_confirm_senha").val() == ""){
		
			mensagem  += "Senha ou a confirmação da senha está(ão) vazia(s).</br>";
			validation = true;
		}
			
		if ($("#usu_senha").val() != $("#usu_confirm_senha").val()){
			
			mensagem  += "Senhas não conferem.</br>";
			validation = true;
		}
		
		
		if ($('#usu_senha').val().length <= 5){
			
			mensagem  += "A senha não possui o número mínimo de caractéres.</br>";
			validation = true;
			
		}
		 
		if (validation == true){
	        $("#valueMessage").prepend(mensagem);
	        mensagem = "";
	        $('#dialog_message').dialog({
	            modal: true,
	            dialogClass: 'ui-dialog-grey',
	            position: [($(window).width() / 2) - (450 / 2), 200],
	            resizable: true,
	            title: "Alteração de Senha",
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