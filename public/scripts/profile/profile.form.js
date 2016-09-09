$(document).ready(function(){
	
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
	
	$("#module_profile").on("change", function(event){
		var moduleProfile = $("#module_profile").val();
			
			$.ajax({
	            type: "POST",
	            url: '/resource/resource/get-resource-by-module-ajax',
	            data: { moduleProfile: moduleProfile},
	            success: function(data){
	            	
	            	if (data.success == true){
	            		
	            		$("#resourceBox").empty();
	            		
	            		$.each( data.data, function( key, value ) {

	            			$("#resourceBox").prepend('<div class="span2">'+
												  		'<label class="control-label">'+value.name_resource+': </label>'+
												  		'<div class="basic-toggle-button">'+
												  			'<input type="checkbox" class="toggle" name="resourceList[]" value="'+value.id_au_resource+'" />'+
												  		'</div>'+
								                      '</div>');
	            			
	            		});
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
	            		
	            		
	            	}else{
	            		
	            		alert("Contacte o administrador. ErrAjax1");
	            	}
	            }
	        });
	});
	
	$( "#marcarTodos" ).on( "click", function() {
		
		if (this.checked){
			$(".toggle:checked"). removeAttr("checked");
			$( ".toggle" ).trigger( "click" );
		}else{
			$( ".toggle" ).trigger( "click" );
		}
		});
		
	
});