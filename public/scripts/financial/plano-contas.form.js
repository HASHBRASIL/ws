$(document).ready(function(){

	/*METODO PARA HABILITAR O AJAX DA PESQUISA RAPIDA QUANDO FOR PRECIONADO ENTER*/
	$("#quickSearch").keypress(function(e) {
		  if(e.which == 13) {
			  var id = $("#quickSearch").val();
				$.ajax({
			        type: "POST",
			        url: "financial/plano-contas/quick-search-ajax/plc_id/"+id,
			        data: { id: id},
			        success: function(data){

			        	if (data.success == "true"){
			        		 window.location.href="financial/plano-contas/form/plc_id/"+id;
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
		        url: "financial/plano-contas/quick-search-ajax/id/"+id,
		        data: { id: id},
		        success: function(data){

		        	if (data.success == "true"){
		        		 window.location.href="financial/plano-contas/form/plc_id/"+id;
		        	}else{
		        		alert("Código Não encontrado.");
		        	}
		        }
		    });
	});

	$("#plc_id_pai").change(function(){

		var plc_id_pai_text = $("#plc_id_pai option:selected").text();
		var retorno = plc_id_pai_text.split(" ");
		var text = retorno[0]+".";
		$("#plc_cod_contabil").val(text);
	});

});