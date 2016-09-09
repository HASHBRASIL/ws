$(document).ready(function(){
	
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		if($(e.target).attr('href') == '#tab_extra'){
			gridExtra();
		}
	});


	$('body').on('click', 'button.aprovar-gerente', function(e){
		e.preventDefault();
		var id_extra = $(this).attr('data-id');
		$.post('/rh/extra/aprovar-gerente',{id_extra:id_extra}, function(data){
			if(data.success){
				gridExtra();
			}else{
				alert(data.mensagem[0].text);
			}
			
		});
	});

	$('body').on('click', 'button.aprovar-diretor', function(e){
		e.preventDefault();
		var id_extra = $(this).attr('data-id');
		$.post('/rh/extra/aprovar-diretor',{id_extra:id_extra}, function(data){
			if(data.success){
				gridExtra();
			}else{
				alert(data.mensagem[0].text);
			}
			
		});
	});

	$('body').on('click', 'button.reprovar-gerente', function(e){
		e.preventDefault();
		var id_extra = $(this).attr('data-id');
		$.post('/rh/extra/reprovar-gerente',{id_extra:id_extra}, function(data){
			if(data.success){
				gridExtra();
			}else{
				alert(data.mensagem[0].text);
			}
			
		});
	});

	$('body').on('click', 'button.reprovar-diretor', function(e){
		e.preventDefault();
		var id_extra = $(this).attr('data-id');
		$.post('/rh/extra/reprovar-diretor',{id_extra:id_extra}, function(data){
			if(data.success){
				gridExtra();
			}else{
				alert(data.mensagem[0].text);
			}
			
		});
	});
});

function gridExtra(){
	$.ajax({
		type : "POST",
		url : "/rh/extra/grid-by-ponto",
		data : {
			data_inicial : getDataPeriodo(),
			ponto : $("#pontoId").val(),
			funcionario : $("#id_funcionario").val()
		},
		success : function(data){
			if(typeof data == 'object'){
				alert(data.message);
				return;
			}
			$('#tab_extra').html(data);
		    $(".tooltips").tooltip();
		    $('a.hora_extra').editable({
		        type: 'text',
		        format: 'hh:ii',
		        url: '/rh/extra/form',
		        send: 'auto',
		        unsavedclass: null,
		        emptyclass: null,
		        savenochange: false,
		        emptytext: '-------',
		        inputclass: 'm-wrap hora',
		        pk: $(this).attr('data-pk'),
		        validate: function(value) {
		            hrs = (value.substring(0,2));  
		            min = (value.substring(3,5));  

		            if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){
		                console.log('validate');
		               return "Hora invalida!";  
		            }  
		            
		        },
		        params: function(params) {
		            //originally params contain pk, name and value
		        	params.id_extra = params.pk;
		        	params.hora = params.value;
		            return params;
		        }
		    });

		    $('a.extra_extra').attr('data-type', 'select2');
		    $('a.extra_extra').editable({
		        type: 'text',
		        format: 'hh:ii',
		        url: '/rh/extra/form',
		        send: 'auto',  
		        source: [
		                 {id: '10', text: '10%'},
		                 {id: '20', text: '20%'},
		                 {id: '30', text: '30%'},
		                 {id: '40', text: '40%'},
		                 {id: '50', text: '50%'},
		                 {id: '60', text: '60%'},
		                 {id: '70', text: '70%'},
		                 {id: '80', text: '80%'},
		                 {id: '90', text: '90%'},
		                 {id: '100', text: '100%'},
		                 {id: '0', text: 'Banco de horas'}
		              ],
		              select2: {
		                  placeholder: 'Selecione',
		              },
		        unsavedclass: null,
		        emptyclass: null,
		        savenochange: false,
		        emptytext: '-------',
		        inputclass: 'm-wrap',
		        pk: $(this).attr('data-pk'),
		        validate: function(value) {
		            if (value == ""){
		               return "Extra invalido!";  
		            }  
		            
		        },
		        params: function(params) {
		            //originally params contain pk, name and value
		        	console.log(params);
		        	params.id_extra = params.pk;
		        	if(params.value == 0){
		        		params.banco_horas = 1;
		        		params.porcentagem = null;
		        	}else{
		        		params.banco_horas = 0;
		        		params.porcentagem = params.value;
		        	}
		            return params;
		        }
		    });
		}
	});
}