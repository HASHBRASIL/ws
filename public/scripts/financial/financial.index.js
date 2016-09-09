$(document).ready(function(){

	$("#marcarTodos").on("click", function(event){

        if(this.checked){
            $("div .checker").children("span").addClass("checked");
            $('.checkbox').attr("checked", "checked");

        }else{

            $("div .checker").children("span").removeClass("checked");
           	$('.checkbox').removeAttr("checked");
        }

    });

	var countCompanySelected = 0;

	function increaseAMonthInDate(date){

		var date = new Date( Date.parse( date ) );
	    date.setDate( date.getDate() + 30 );
	    var newDate = date.toDateString();
	    newDate = new Date( Date.parse( newDate ) );

		return newDate;
	}

	function prependInDivCountCompanySelected(){

		$("#companySelected").empty();

		if (countCompanySelected == 1){

			$("#companySelected").prepend("Empresa Selecionada: "+countCompanySelected);

		}else{

			$("#companySelected").prepend("Empresas Selecionadas: "+countCompanySelected);

		}

	}

    $( "#empresa" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#id_empresa").attr("idCompany",ui.item.id);
            $("#id_empresa").attr("value",ui.item.value);
        },
        search: function( event, ui ) {
        	$("#id_empresa").attr("idCompany","");
            $("#id_empresa").attr("value","");
        }
      });

    var countForRemove = 0;
	$('#addEmpresa').click(function(){

		var idCompany = $("#id_empresa").attr("idCompany");
		var value = $("#id_empresa").attr("value");

		if (idCompany == "" || value == ""){

			return false;

		}

		$("#localCompany").prepend(	'<input class=" remove_'+countForRemove+'" type="text" id = "'+idCompany+'" value="'+value+'" disabled="disabled" style="display: inline-block; width: 60%; margin-top:8px; margin-bottom:8px;">'+
									'<input class=" remove_'+countForRemove+'" type="hidden" name="empresaList[]" value="'+idCompany+'">'+
									'<img class="removeEmpresa remove_'+countForRemove+'" removeInput = "'+countForRemove+'" src="images/delete.png" style="cursor:pointer;padding-left: 10px;" data-tooltip="" class="" data-tooltip title="Remover empresa"></span>');
		 $( "#empresa" ).val("");
		 $("#id_empresa").attr("idCompany", "");
		 $("#id_empresa").attr("value", "");
		 countForRemove++;
		 countCompanySelected++;
		 prependInDivCountCompanySelected();
	});

	$(document).on("click", ".removeEmpresa", function(){

		var removeInput = $(this).attr("removeInput");

		$(".remove_"+removeInput).remove();
		$('.tooltip').hide();
		countCompanySelected--;
		prependInDivCountCompanySelected();

	});

	$("#data_emissao, #data_emissao2, #data_vencimento, #data_vencimento2, #data_compensacao, #data_compensacao2").setMask({mask:'99/99/9999',autoTab: false});

	$( ".datepicker" ).datepicker({
		  defaultDate: "+1w",
	      changeMonth: true,
	      numberOfMonths: 3,
	    });

	$("#data_emissao").change(function(){
	    var date = $(this).datepicker('getDate');
	    var newDate = increaseAMonthInDate(date);
	    $('#data_emissao2').datepicker('setDate', newDate );
	});

	$("#data_vencimento").change(function(){
	    var date = $(this).datepicker('getDate');
	    var newDate = increaseAMonthInDate(date);
	    $('#data_vencimento2').datepicker('setDate', newDate );
	});

	$("#data_compensacao").change(function(){
	    var date = $(this).datepicker('getDate');
	    var newDate = increaseAMonthInDate(date);
	    $('#data_compensacao2').datepicker('setDate', newDate );
	});

	$(document).on("change", "#data_emissaoType", function(){

		var option = $("#data_emissaoType option:selected").val();
		if (option == "exato"){
			$("#data_emissao2").fadeOut("slow");
		}else{
			$("#data_emissao2").fadeIn("slow");
		}
	});

	$(document).on("change", "#data_vencimentoType", function(){

		var option = $("#data_vencimentoType option:selected").val();
		if (option == "exato"){
			$("#data_vencimento2").fadeOut("slow");
		}else{
			$("#data_vencimento2").fadeIn("slow");
		}
	});

	$(document).on("change", "#data_compensacaoType", function(){

		var option = $("#data_compensacaoType option:selected").val();
		if (option == "exato"){
			$("#data_compensacao2").fadeOut("slow");
		}else{
			$("#data_compensacao2").fadeIn("slow");
		}
	});

	$('#submit').click(function(){

			var countCheckedStatus = 0;
			countCheckedStatus = $( "input:checked" ).length;
			if(countCheckedStatus <= 0){
				alert("Selecione ao menos um status financeiro");
				return false;
			}
			$("#id_empresa").remove();
			$("#empresa").remove();
			$("#marcarTodos").remove();
	});

	$('.openTab').click(function(){

		$(".loadAjax").show();

		var type = $(this).attr('name');
		var options;

		switch(type)
		{
		case "AVC":
			options = {statusFinanceiro : "avencer", grupoContasList : "2"};
			break;
		case "AVD":
			options = {statusFinanceiro : "avencer", grupoContasList : "1"};
			break;
		case "LC":
			options = {statusFinanceiro : "liquidado", grupoContasList : "2"};
			break;
		case "LD":
			options = {statusFinanceiro : "liquidado", grupoContasList : "1"};
			break;
		case "VC":
			options = {statusFinanceiro : "vencido", grupoContasList : "2"};
			break;
		case "VD":
			options = {statusFinanceiro : "vencido", grupoContasList : "1"};
			break;
		case "PP":
			options = {pendenciaProcesso : "true"};
			break;
		default:

		}

		$("#"+type).empty();

		if (type == "PP"){

			$.ajax({
			    type: "POST",
			    url: 'financial/financial/get-financial-with-processo-incompatible-per-models/',
			    data: {options:options},
			    success: function(data){

			    	$("#"+type).html(data);

			    },
			    complete: function(){
			    	$(".loadAjax").hide();
			    }
			});

		}else{

			$.ajax({
			    type: "POST",
			    url: 'financial/financial/get-financial-per-models/',
			    data: {options:options},
			    success: function(data){

			    	$("#"+type).html(data);

			    },
			    complete: function(){
			    	$(".loadAjax").hide();
			    }
			});

		}

	});


	$('#gerar-pdf').click(function(e){
	    e.preventDefault();
	    window.location.href = 'financial/financial/grid-pdf?'+$('#form_pesquisa').serialize();
	});
});
