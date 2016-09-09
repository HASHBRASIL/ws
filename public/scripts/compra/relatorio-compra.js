$(document).ready(function(){
	$.ajax({
		type: "GET",
		url: '/compra/relatorio-compra/grid-campanha',
		success: function(data){
			if(data){
				$('#grids').html(data);
			}
		}
	});
	$('body').on('click', 'a.compra', function(evt){
		evt.preventDefault();
		$.ajax({
			type: "GET",
			url: '/compra/relatorio-compra/grid-compra',
			success: function(data){
				if(data){
					$('#grids').html(data);
				}
			}
		});
	});
	$('body').on('click', 'a.campanha', function(evt){
		evt.preventDefault();
		$.ajax({
			type: "GET",
			url: '/compra/relatorio-compra/grid-campanha',
			success: function(data){
				if(data){
					$('#grids').html(data);
				}
			}
		});
	});
});
