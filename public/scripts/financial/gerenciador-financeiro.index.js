$(document).ready(function(){

	getConsolidated();

	$( ".reload" ).click(function() {
			getConsolidated();
	});

	setInterval(function(){

		getConsolidated();

	},90000);

	setInterval(function(){
		var anterior = $('.consolidatedAnAccount:visible + .consolidatedAnAccount').length > 0 ? $('.consolidatedAnAccount:visible + .consolidatedAnAccount') : $('.consolidatedAnAccount:first');$('.consolidatedAnAccount').hide();anterior.show(); },10000);

	$( "#pesquisarButton" ).click(function(e) {
        e.preventDefault();

		$.ajax({
            type: "GET",
            url: "financial/gerenciador-financeiro/grid/",
            data: $("#formAjax").serialize(),
            success: function(data){
                    $("#gridAction").html(data);
            }
        });

	});

});

function getConsolidated(){

	$.ajax({
        type: "POST",
        url: 'financial/gerenciador-financeiro/consolidated-an-account-ajax',
        success: function(data){

        	if (data.success == true){


        		$( "#consolidatedAnAccount_3monthAgoReceive" ).empty().append('<li style = "text-align: center" >Últimos 3 Meses a Receber</li>' );
        		$( "#consolidatedAnAccount_6monthAgoReceive" ).empty().append('<li style = "text-align: center" >Últimos 6 Meses a Receber</li>' );
        		$( "#consolidatedAnAccount_12monthAgoReceive" ).empty().append('<li style = "text-align: center" >Últimos 12 Meses a Receber</li>' );
        		$( "#consolidatedAnAccount_3monthAgoPay" ).empty().append('<li style = "text-align: center" >Últimos 3 Meses a Pagar</li>' );
        		$( "#consolidatedAnAccount_6monthAgoPay" ).empty().append('<li style = "text-align: center" >Últimos 6 Meses a Pagar</li>' );
        		$( "#consolidatedAnAccount_12monthAgoPay" ).empty().append('<li style = "text-align: center" >Últimos 12 Meses a Pagar</li>' );


        		$.each( data._3monthAgoReceive, function( key, value ) {

        			$( "#consolidatedAnAccount_3monthAgoReceive" ).append( '<li><span class="sale-info">'+value.con_codnome+'</span>'+
        													'<span class="sale-num">'+value.total_financeiro+'</span></li>' );

        		});

        		$.each( data._6monthAgoReceive, function( key, value ) {

        			$( "#consolidatedAnAccount_6monthAgoReceive" ).append( '<li><span class="sale-info">'+value.con_codnome+'</span>'+
        													'<span class="sale-num">'+value.total_financeiro+'</span></li>' );

        		});
        		$.each( data._12monthAgoReceive, function( key, value ) {

        			$( "#consolidatedAnAccount_12monthAgoReceive" ).append( '<li><span class="sale-info">'+value.con_codnome+'</span>'+
        													'<span class="sale-num">'+value.total_financeiro+'</span></li>' );

        		});
        		$.each( data._3monthAgoPay, function( key, value ) {

        			$( "#consolidatedAnAccount_3monthAgoReceive" ).append( '<li><span class="sale-info">'+value.con_codnome+'</span>'+
        													'<span class="sale-num">'+value.total_financeiro+'</span></li>' );

        		});

        		$.each( data._6monthAgoPay, function( key, value ) {

        			$( "#consolidatedAnAccount_6monthAgoPay" ).append( '<li><span class="sale-info">'+value.con_codnome+'</span>'+
        													'<span class="sale-num">'+value.total_financeiro+'</span></li>' );

        		});
        		$.each( data._12monthAgoPay, function( key, value ) {

        			$( "#consolidatedAnAccount_12monthAgoPay" ).append( '<li><span class="sale-info">'+value.con_codnome+'</span>'+
        													'<span class="sale-num">'+value.total_financeiro+'</span></li>' );

        		});

        	}else{
        		alert("Contacte o administrador. Não foi possível carregar o menu de contas. AjxErr1");return false;
        	}
        }
    });

}