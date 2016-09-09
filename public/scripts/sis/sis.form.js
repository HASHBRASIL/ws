$.mask.options = {
attr: 'alt',              // an attr to look for the mask name or the mask itself
mask: null,               // the mask to be used on the input
type: 'fixed',            // the mask of this mask
maxLength: -1,            // the maxLength of the mask
defaultValue: '',         // the default value for this input
textAlign: true,          // to use or not to use textAlign on the input
selectCharsOnFocus: true, //selects characters on focus of the input
setSize: false,           // sets the input size based on the length of the mask (work with fixed and reverse masks only)
autoTab: false,            // auto focus the next form element
fixedChars: '[(),.:/ -]', // fixed chars to be used on the masks.
onInvalid: function(){},
onValid: function(){},
onOverflow: function(){}
};
$(document).ready(function(){
   
	$('#cpf_cnpj').keyup(function(event) {
        var cpf = $('#cpf_cnpj').unmaskedVal();
        if(cpf.length > 14){
            $('#cpf_cnpj').setMask("99.999.999/9999-99");
        }else{
            $('#cpf_cnpj').setMask("999.999.999-999");
        }
    });
	
	$("#deleteLogo").click(function() {
		  var id = $(this).attr("value");
			$.ajax({
		        type: "POST",
		        url: "/sis/index/delete-image-ajax/type/logo/id_proprietario/"+id,
		        success: function(data){
		        	if (data.success == true){
		        		$("#boxLogo").empty();
		        		$("#boxLogo").prepend('<div class="span6 alert alert-success" style="margin-left: 30px;">Imagem removida com sucesso <button class="close" data-dismiss="alert"></button> </div>');
		        	}else{
		        		alert("Contacte o administrador. A imagem do logotipo não pode ser removida. AjxErr001");
		        	}
		        }
		    });
	});
	$("#deleteLogoReport").click(function() {
		  var id = $(this).attr("value");
			$.ajax({
		        type: "POST",
		        url: "/sis/index/delete-image-ajax/type/logo-report/id_proprietario/"+id,
		        success: function(data){
		        	
		        	if (data.success == true){
		        		$("#boxLogoReport").empty();
		        		$("#boxLogoReport").prepend('<div class="span6 alert alert-success" style="margin-left: 30px;">Imagem removida com sucesso <button class="close" data-dismiss="alert"></button> </div>');
		        	}else{
		        		alert("Contacte o administrador. A imagem do relatório não pode ser removida. AjxErr002");
		        	}
		        }
		    });
	});
	
	
});