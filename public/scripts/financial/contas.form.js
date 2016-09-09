$(document).ready(function(){

	/*METODO PARA HABILITAR O AJAX DA PESQUISA RAPIDA QUANDO FOR PRECIONADO ENTER*/
	$("#quickSearch").keypress(function(e) {
		  if(e.which == 13) {
			  var id = $("#quickSearch").val();
				$.ajax({
			        type: "POST",
			        url: "financial/contas/quick-search-ajax/con_id/"+id,
			        data: { id: id},
			        success: function(data){

			        	if (data.success == "true"){
			        		 window.location.href="financial/contas/form/con_id/"+id;
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
		        url: "financial/contas/quick-search-ajax/con_id/"+id,
		        data: { id: id},
		        success: function(data){

		        	if (data.success == "true"){
		        		window.location.href="financial/contas/form/con_id/"+id;
		        	}else{
		        		alert("Código Não encontrado.");
		        	}
		        }
		    });
	});
});