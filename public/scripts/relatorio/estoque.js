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
    
    $( "#dialog" ).dialog({
        autoOpen: false,
        modal: true,
        buttons: {
            "Cancelar" : function(){
                $(this).dialog('close');
            },
            "Gerar" : function(){
                $('input.tgrupo').each(function() {
                    //Verifica qual estÃ¡ selecionado
                    if ($(this).is(':checked'))
                        tgrupo = parseInt($(this).val());
                });
                if(tgrupo == 0){
                    $("#frm").attr("action","status");
                }
                if(tgrupo == 1){
                    $("#frm").attr("action","empresa");
                }
                if(tgrupo == 2){
                    $("#frm").attr("action","plc");
                }
                $('#frm').submit();
                $(this).dialog('close');

            }
        }
     
    });
    $( "body" ).on('click', '#opener', function(e) {
        e.preventDefault();
        $( "#dialog" ).dialog( "open" );
    });
	
	var countCompanySelected = 0;
	var countPlcSelected = 0;
	
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
        source: "/empresa/empresa/autocomplete",
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
		
		$("#localCompany").prepend(	'<input class=" remove_'+countForRemove+'" type="text" id = "'+idCompany+'" value="'+value+'" disabled="disabled" style="display: inline-block; width: 60%;">'+
									'<input class=" remove_'+countForRemove+'" type="hidden" name="empresaList[]" value="'+idCompany+'">'+
									'<img class="removeEmpresa remove_'+countForRemove+'" removeInput = "'+countForRemove+'" src="/images/delete.png" style="cursor:pointer;padding-left: 10px;" data-tooltip="" class="" data-tooltip title="Remover empresa"></span>');
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
	
function prependInDivCountPlcSelected(){
        
        $("#plcSelected").empty();
        
        if (countPlcSelected == 1){
            
            $("#plcSelected").prepend("Plano de Contas Selecionado: "+countPlcSelected);
            
        }else{
            
            $("#plcSelected").prepend("Planos de Contas Selecionados: "+countPlcSelected);
            
        }
        
    }

    $( "#plc" ).autocomplete({
        source: "/relatorio/planocontas/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#plc_id").attr("idPlc",ui.item.id);
            $("#plc_id").attr("value",ui.item.value);
        },
        search: function( event, ui ) {
            $("#plc_id").attr("idPlc","");
            $("#plc_id").attr("value","");
        }
      });
    
    var countForRemove = 0;
    $('#addPlc').click(function(){
        
        var idPlc = $("#plc_id").attr("idPlc");
        var value = $("#plc_id").attr("value");
        
        if (idPlc == "" || value == ""){
            
            return false;
            
        }
        
        $("#localPlc").prepend( '<input class=" remove_'+countForRemove+'" type="text" id = "'+idPlc+'" value="'+value+'" disabled="disabled" style="display: inline-block; width: 60%;">'+
                                    '<input class=" remove_'+countForRemove+'" type="hidden" name="plcList[]" value="'+idPlc+'">'+
                                    '<img class="removePlc remove_'+countForRemove+'" removeInput = "'+countForRemove+'" src="/images/delete.png" style="cursor:pointer;padding-left: 10px;" data-tooltip="" class="" data-tooltip title="Remover plano de contas"></span>');
         $( "#plc" ).val("");
         $("#plc_id").attr("idPlc", "");
         $("#plc_id").attr("value", "");
         countForRemove++;
         countPlcSelected++;
         prependInDivCountPlcSelected();
    });
    
    $(document).on("click", ".removePlc", function(){
        
        var removeInput = $(this).attr("removeInput"); 
        
        $(".remove_"+removeInput).remove();
        $('.tooltip').hide();
        countPlcSelected--;
        prependInDivCountPlcSelected();
        
    });
	
	$("#dataInicial, #dataFinal, #data_vencimento, #data_vencimento2, #dataInicial, #dataFinal").setMask({mask:'99/99/9999',autoTab: false});
	
	$( ".datepicker" ).datepicker({
		  defaultDate: "+1w",
	      changeMonth: true,
	      numberOfMonths: 3,
	    });
	
	$("#dataInicial").change(function(){
	    var date = $(this).datepicker('getDate');
	    var newDate = increaseAMonthInDate(date);
	    $('#dataFinal').datepicker('setDate', newDate );
	});
	
	$("#data_vencimento").change(function(){
	    var date = $(this).datepicker('getDate');
	    var newDate = increaseAMonthInDate(date);
	    $('#data_vencimento2').datepicker('setDate', newDate );
	});
	
	$("#dataInicial").change(function(){
	    var date = $(this).datepicker('getDate');
	    var newDate = increaseAMonthInDate(date);
	    $('#dataFinal').datepicker('setDate', newDate );
	});
	
	$(document).on("change", "#data_condicao", function(){
		
		var option = $("#data_condicao option:selected").val();
		if (option == "exato"){
			$("#dataFinal").fadeOut("slow");
		}else{
			$("#dataFinal").fadeIn("slow");
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
			$("#dataFinal").fadeOut("slow");
		}else{
			$("#dataFinal").fadeIn("slow");
		}
	});
	
	$('#submit').click(function(){
			$("#id_empresa").empty();
			//$("#empresa").remove();
			
	});
	
});