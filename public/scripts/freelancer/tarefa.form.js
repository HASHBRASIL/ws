$(document).ready(function(){

	$('#horas_trabalhadas').spinner();

	$('#dt_inicio, #dt_fim').setMask({mask : '99/99/9999 99:99', autoTab: false });

	//aparece o calendário no campo data fim
	$("#dt_inicio").datetimepicker({
			format: "dd/mm/yyyy hh:ii",
			language: 'pt-BR',
			autoclose: true,
			todayBtn: true,
			pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
			minuteStep: 5,
	}).on('changeDate', function(ev){
			$('#dt_fim').datetimepicker('setStartDate', Date.parse($('#dt_inicio').val()));
	});
	//aparece o calendário no campo data fim
	$("#dt_fim").datetimepicker({
			format: "dd/mm/yyyy hh:ii",
			autoclose: true,
			todayBtn: true,
			language: 'pt-BR',
			linkFormat: "yyyy-mm-dd hh:ii",
			pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
			minuteStep: 5,
	}).on('changeDate', function(ev){
			$('#dt_inicio').datetimepicker('setEndDate', Date.parse($('#dt_fim').val()));
	});

	$( "#slider-range" ).slider({
			range: "max",
			min: 0,
			max: 100,
			value: $('#percentual_completado').val(),
			slide: function(event,ui){
				$('#percentual_completado').val(ui.value);
			}

	});

	$( "#id_empresa_selected" ).autocomplete({
		source: "/empresa/empresa/autocomplete",
		minLength: 2,
		select: function( event, ui ) {

			$('#id_empresa').val(ui.item.id);

		},
		search: function( event, ui ) {
			$('#id_empresa').val("");
		}
	});

});
