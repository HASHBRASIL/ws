$(document).ready(function(){

	/*METODO PARA HABILITAR O AJAX DA PESQUISA RAPIDA QUANDO FOR PRECIONADO ENTER*/
	$("#quickSearch").keypress(function(e) {
		  if(e.which == 13) {
			  var id = $("#quickSearch").val();
				$.ajax({
			        type: "POST",
			        url: "financial/credito/quick-search-ajax/id_credito/"+id,
			        data: { id: id},
			        success: function(data){

			        	if (data.success == "true"){
			        		 window.location.href="financial/credito/form/id_credito/"+id;
			        	}else{
			        		alert("Código Não encontrado.");
			        	}
			        }
			    });
		  }
	});

	$("#quickSearchButton").click(function() {
		  var id = $("#quickSearch").val();
			$.ajax({
		        type: "POST",
		        url: "financial/credito/quick-search-ajax/id_credito/"+id,
		        data: { id: id},
		        success: function(data){

		        	if (data.success == "true"){
		        		window.location.href="financial/credito/form/id_credito/"+id;
		        	}else{
		        		alert("Código Não encontrado.");
		        	}
		        }
		    });
	});

	$( ".datepicker" ).datepicker({
		  defaultDate: "+1w",
	      changeMonth: true,
	});

	$( "#empresa_sacado" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#empresas_id").val(ui.item.id);
        },
        search: function( event, ui ) {
        }
      });
});