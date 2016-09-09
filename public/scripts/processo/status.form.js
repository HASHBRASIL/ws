$(document).ready(function(){

	$("#quickSearchButton").click(function() {
		  var id = $("#quickSearch").val();
			$.ajax({
		        type: "POST",
		        url: "processo/status/quick-search-ajax/key/sta_id/id/"+id,
		        data: { id: id},
		        success: function(data){

		        	if (data.success == "true"){
		        		window.location.href="processo/status/form/sta_id/"+id;
		        	}else{
		        		alert("Código Não encontrado.");
		        	}
		        }
		    });
	});

});