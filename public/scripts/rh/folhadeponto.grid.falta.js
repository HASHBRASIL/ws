$(document).ready(function(){
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		if($(e.target).attr('href') == '#tab_falta'){
			gridFalta();
		}
	});
	
	$('body').on('click', 'a.dsr_falta', function(e){
		var id_falta = $(this).attr('data-pk');
		var dsr 	 = $(this).attr('data-dsr');
		var data 	 = $(this).attr('data-data');
		if(dsr == 0){
			dsr = 1;
		}else{
			dsr = 0;
		}
		$.post('/rh/falta/form', {id_falta:id_falta, dsr:dsr, data:data}, function(data){
			if(data.success == true){
				gridFalta();
			}else{
				alert('Não foi possivel alterar este dados.');
			}
		});
	});
});

function gridFalta(){
	$.ajax({
		type : "POST",
		url : "/rh/falta/grid-by-ponto",
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
			$('#tab_falta').html(data);
		    $(".tooltips").tooltip();
		    $('a.hora_falta').editable({
		        type: 'text',
		        format: 'hh:ii',
		        url: '/rh/falta/form',
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
		        	params.id_falta = params.pk;
		        	params.hora = params.value;
		            return params;
		        }
		    });
		    $('a.hora_falta').editable({
		        type: 'text',
		        format: 'hh:ii',
		        url: '/rh/falta/form',
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
		        	params.id_falta = params.pk;
		        	params.hora = params.value;
		            return params;
		        }
		    });
		    
		    $('a.tipo_falta').attr('data-type', 'select2');
		    $('a.tipo_falta').editable({
		        type: 'text',
		        url: '/rh/falta/form',
		        send: 'auto',  
		        source: [
		                 {id: '1', text: 'Banco de Horas'},
		                 {id: '0', text: 'Desconto'},
		              ],
		              select2: {
		                  placeholder: '--- Selecione ---',
		              },
		        unsavedclass: null,
		        emptyclass: null,
		        savenochange: false,
		        emptytext: '-------',
		        inputclass: 'm-wrap',
		        pk: $(this).attr('data-pk'),
		        validate: function(value) {
		            if (value == ""){
		               return "Falta invalido!";  
		            }  
		            
		        },
		        params: function(params) {
		            //originally params contain pk, name and value
		        	console.log(params);
		        	params.id_falta = params.pk;
	        		params.banco_horas = params.value;
		            return params;
		        }
		    });
		}
	});
}