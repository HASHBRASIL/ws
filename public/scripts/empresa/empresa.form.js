 /* Mascaras personalizadas */ 
$.mask.options = {
    attr: 'alt',              // an attr to look for the mask name or the mask itself
    mask: null,               // the mask to be used on the input
    type: 'fixed',            // the mask of this mask
    maxLength: -1,            // the maxLength of the mask
    defaultValue: '',         // the default value for this input
    textAlign: true,          // to use or not to use textAlign on the input
    selectCharsOnFocus: true, //selects characters on focus of the input
    setSize: false,           // sets the input size based on the length of the mask (work with fixed and reverse masks only)
    autoTab: false,            // auto focus the next form element
    fixedChars: '[(),.:/ -]', // fixed chars to be used on the masks.
    onInvalid: function(){},
    onValid: function(){},
    onOverflow: function(){},
    autoTab: false
};

$(document).ready(function(){
    $('.basic-toggle-button').toggleButtons({
        width: 100,
        label: {
            enabled: "Sim",
            disabled: "Não"
        },
        style: {
            // Accepted values ["primary", "danger", "info", "success", "warning"] or nothing
            enabled: "info",
            disabled: "danger"
        }
    });
	FormValidation.init();
    //Colocando as mascaras nos campos necessários
    $('#cnpj_cpf').setMask('99.999.999/9999-99');
    $('#estadual').setMask({mask:'9', type:'repeat'});
    $('#municipal').setMask({mask:'9', type:'repeat'});
    $('#cep').setMask({mask:'9', type:'repeat'});
    $('.telefone').maskTelefone();
    // chama a funçao responsavel por mudar os nomes dos campos se for juridico ou fisica
    $('#tps_id').tipoServico();
    //carrega a grid do endereco se tiver id
    carregarGrid();
    carregarGridContato();
    
    carregarGridFinanceiro();
    
    //conforme o tipo de empresa for mudado irá mudar os campos
    $('#tps_id').change(function(e){
        if($('#municipal').val() != "" || $('#estadual').val() != "" || $('#cnpj_cpf').val() != "" || $('#uasg').val() != "" ){
            var resultado = confirm("Os campos preenchidos poderam ser pedidos como CPF/CNPJ e/ou inscrição municipal e/ou inscrição estadual e/ou Uasg");
            if(!resultado){
                if($(this).val() == 1){
                    $(this).val(2);
                }else if($(this).val() == 2){
                    $(this).val(1);
                }
                return;
            }
        }
        $(this).tipoServico();
        $('#estadual').val('');
        $('#uasg').val('');
        $('#municipal').val('');
        $('#cnpj_cpf').val('');
    });

    //add um campo de telefone no maximo tres campos
    $('img.add-tel').click(function(){
        if($('#telefone2').length == 0 ){
            $('div.telefone').append("<input type='text' name='telefone2' id='telefone2' class='telefone span10' style='display:inline-block; margin-top: 5px;'>").
                              append(" <img src='/images/delete.png' data-tooltip title='excluir telefone' class='delete-tel tooltips' id-tel='2' style='cursor:pointer'>");
        }else if($('#telefone3').length == 0){
            $('div.telefone').append("<input type='text' name='telefone3' id='telefone3' class='telefone span10' style='display:inline-block; margin-top: 5px;'>").
                              append(" <img src='/images/delete.png' data-tooltip title='excluir telefone' class='delete-tel tooltips' id-tel='3' style='cursor:pointer'>");
            $('img.add-tel').hide('slow');
        }
        $('.telefone').maskTelefone();
    });
    
    //apaga o campo de telefone clicado
    $('body').on('click', '.delete-tel', function(){
        var id_tel = $(this).attr('id-tel');
        $(this).remove();
        $('#telefone'+id_tel).remove();
        $('.tooltip').hide();
        $('img.add-tel').css('display','inline-block');
    });
    
    //abre o dialog de cadastro de endereço
    $('a.endereco').click(function(e){
        e.preventDefault();
        $('.dialog_endereco').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Cadastrar endereço",
            width: 450,
            height: 570,
            buttons: [
                      {
		                 'class' : 'btn red',
		                 "text" : "Cancelar",
		                click: function() {
		                  $( this ).dialog( "close" );
		                }
                      },
                      {
		                 'class' : 'btn green',
		                 "text" : "Adicionar",
                    	  click: function() {
		                    if($('#cep').val() == ""){
		                        alert('Selecione um CEP');
		                        return;
		                    }
		                    if($('#nome_logradouro').val() == ""){
		                        alert('O campo endereço está vazio');
		                        return;
		                    }
		                    if($('#nome_logradouro').val() == ""){
		                        alert('O campo endereço está vazio');
		                        return;
		                    }
		                    if($('#nome_logradouro').val() == ""){
		                        alert('O campo endereço está vazio');
		                        return;
		                    }
		                    if($('#ufs_id').val() == ""){
		                        alert('Selecione um estado.');
		                        return;
		                    }
		                    if($('#cid_id').val() == ""){
		                        alert('Selecione uma cidade.');
		                        return;
		                    }
		                    
		                    $.ajax({
		                        type: "POST",
		                        url: '/sis/endereco/form',
		                        data: $('.form-endereco').serialize(),
		                        success: function(data){
		                            if(data.success == true){
		                                if($('#id_endereco').val() =="" ){
		                                    $.messageBox("Endereço inserido com sucesso.", 'success');
		                                    
		                                }else{
		                                    $.messageBox("Endereço atualizado com sucesso.", 'success');
		                                }
		                                $('.dialog_endereco').dialog('close');
		                                carregarGrid();
		                            }else{
		                                alert("Não foi possivel salvar o endereço.");
		                            }
		                        }
		                    });
		                }
        		}
              ],
              close: function(){
                  $('.tipo_endereco').attr('checked', false);
                  $('#cep').val('');
                  $('#tipo_logradouro').val('');
                  $('#nome_logradouro').val('');
                  $('#numero').val('');
                  $('#bairro').val('');
                  $('#complemento').val('');
                  $('#ufs_id').val('');
                  $('#cid_id').val('');
                  $('#id_endereco').val('');
              }
        });
        
    });
    
    // seleciona a cidade se o estado for modificado
    $('#ufs_id').change(function(){
        selectCidade(null);
    });
    
    //executa a ação de buscar o cep
    $('#bt_pesquisar_cep').click(function(e){
        findCEP();
    });
    
    // irá preencher os form e abrir o dialog do endereço
    $('body').on('click', 'a.editar_endereco', function(e){
        e.preventDefault();
        var id = $(this).attr('href');
        $.ajax({
            type: "GET",
            url: '/sis/endereco/get/id/'+id,
            success: function(data){
                $('#cep').val(data.cep);
                $('#tipo_logradouro').val(data.tipo_logradouro);
                $('#nome_logradouro').val(data.endereco);
                $('#numero').val(data.numero);
                $('#bairro').val(data.bairro);
                $('#complemento').val(data.complemento);
                $('#ufs_id').val(data.ufs_id);
                //se vier com o tipo de endereço irá marcar a opção
                if(data.id_tp_ref){
                    $.each(data.id_tp_ref, function(key, value){
                        $('.tipo_endereco[value="'+value+'"]').trigger('click');
                    });
                }
                selectCidade(null);
                setTimeout(function(){$('#cid_id').val(data.cid_id);},1500);
                $('#id_endereco').val(data.id);
                $('a.endereco').trigger('click');
            },
            error: function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });
    
    // irá inativar um endereço
    $('body').on('click', 'a.delete_endereco', function(e){
        e.preventDefault();
        var id = $(this).attr('href');
        var resultado = confirm('Deseja realmente excluir este dado?');
        if(resultado == false){
            return;
        }
        
        $.ajax({
            type: "GET",
            url: '/sis/endereco/delete/id/'+id,
            success: function(data){
                if(data[0].type == 'success'){
                    $.messageBox("Dado excluido com sucesso.", 'success');
                    carregarGrid();
                }else{
                    $.messageBox("Não foi possivel excluir o dado.", 'error');
                }
            },
            error: function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });
    

    // irá inativar um financeiro
    $('body').on('click', 'a.delete_financeiro', function(e){
        e.preventDefault();
        var id = $(this).attr('href');
        var resultado = confirm('Deseja realmente excluir este dado?');
        if(resultado == false){
            return;
        }
        
        $.ajax({
            type: "GET",
            url: '/financial/financial/delete/id/'+id,
            success: function(data){
                if(data[0].type == 'success'){
                    $.messageBox("Dado excluido com sucesso.", 'success');
                    carregarGridFinanceiro();
                }else{
                    $.messageBox("Não foi possivel excluir o dado.", 'error');
                }
            },
            error: function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });
    
    $( "#responsavel" ).autocomplete({
        source: "/empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#empresas_id_pai').val(ui.item.id);
        },
        search: function( event, ui ) {
            $('#empresas_id_pai').val("");
        }
      });
    
    $('.add_combo').click(function(e){
        e.preventDefault();
        var title = $(this).attr('data-original-title');
        var href  = $(this).attr('href');
        var $this = $(this);
        $('.dialog_combo').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (300 / 2), 200],
            resizable: true,
            title: title,
            width: 300,
            height: 200,
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Cancelar",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                      },
                      {
                         'class' : 'btn green',
                         "text" : "Adicionar",
                          click: function() {
                            var nome = $('#nome_combo').val();
                            if( nome == ""){
                                alert('preencha o campo nome.');
                                return;
                            }
                            
                            $.ajax({
                                type: "POST",
                                url: href,
                                data: {nome : $('#nome_combo').val()},
                                success: function(data){
                                    if(data.success == true){
                                        for (x in (data.id))
                                        {
                                            id= data.id[x];
                                        }
                                        $this.siblings('select').append('<option value="'+id+'">'+nome+'</option>').val(id);
                                        $this.siblings('select').val(id);
                                        $('.dialog_combo').dialog('close');
                                        $.messageBox("Dado inserido com sucesso.", 'success');
                                    }else{
                                        alert("Não foi possivel salvar o endereço.");
                                    }
                                }
                            });
                        }
                }
              ],
              close: function(){
                  $('#nome_combo').val('');
              }
        });
    });
    
    //abre o dialog de cadastro de contato
    $('a.contato').click(function(e){
        e.preventDefault();
        $('.dialog_contato').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Cadastrar contato",
            width: 450,
            height: 450,
            open: function(){
                $( "#aniversario" ).datepicker();
                $('.telefone').maskTelefone();
            },
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Cancelar",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                      },
                      {
                         'class' : 'btn green',
                         "text" : "Adicionar",
                          click: function() {
                            if($('#nome_contato').val() == ""){
                                alert('Selecione um CEP');
                                return;
                            }
                            
                            $.ajax({
                                type: "POST",
                                url: '/sis/contato/form',
                                data: {nome:$('#nome_contato').val(), radio: $('#radio').val(),
                                    email1:$('#email1').val(), email2 : $('#email2').val(),
                                    telefone1:$('#tel_contato1').val(), telefone2:$('#tel_contato2').val(), telefone3: $('#tel_contato3').val(),
                                    aniversario:$('#aniversario').val(), cre_id : $('#cre_id').val(),
                                    car_id:$('#car_id').val(), cdp_id:$('#cdp_id').val(), id:$('#id_contato').val(),
                                    id_empresas:$('#id').val()},
                                success: function(data){
                                    if(data.success == true){
                                        if($('#id_endereco').val() =="" ){
                                            $.messageBox("Contato inserido com sucesso.", 'success');
                                            
                                        }else{
                                            $.messageBox("Contato atualizado com sucesso.", 'success');
                                        }
                                        $('.dialog_contato').dialog('close');
                                        carregarGridContato();
                                    }else{
                                        alert("Não foi possivel salvar o endereço.");
                                    }
                                }
                            });
                        }
                }
              ],
              close: function(){
                  $('#nome_contato').val('');
                  $('#radio').val('');
                  $('#email1').val('');
                  $('#email2').val('');
                  $('#tel_contato1').val('');
                  $('#tel_contato2').val('');
                  $('#tel_contato3').val('');
                  $('#aniversario').val('');
                  $('#cre_id').val('');
                  $('#car_id').val('');
                  $('#cdp_id').val('');
                  $('#id_contato').val('');
              }
        });
        
    });
    
    // irá preencher os form e abrir o dialog do contato
    $('body').on('click', 'a.editar_contato', function(e){
        e.preventDefault();
        var id = $(this).attr('id_contato');
        $.ajax({
            type: "GET",
            url: '/sis/contato/get/id/'+id,
            success: function(data){
                $('#nome_contato').val(data.nome);
                $('#radio').val(data.radio);
                $('#email1').val(data.email1);
                $('#email2').val(data.email2);
                $('#tel_contato1').val(data.telefone1);
                $('#tel_contato2').val(data.telefone2);
                $('#tel_contato3').val(data.telefone3);
                $('#aniversario').val(data.aniversario);
                $('#cre_id').val(data.cre_id);
                $('#car_id').val(data.car_id);
                $('#cdp_id').val(data.cdp_id);
                $('#id_contato').val(data.id);
                $('a.contato').trigger('click');
            },
            error: function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });
    
    // irá inativar um contato
    $('body').on('click', 'a.delete_contato', function(e){
        e.preventDefault();
        var id = $(this).attr('id_contato');
        var resultado = confirm('Deseja realmente excluir este dado?');
        if(resultado == false){
            return;
        }
        
        $.ajax({
            type: "GET",
            url: '/sis/contato/delete/id/'+id,
            success: function(data){
                if(data[0].type == 'success'){
                    $.messageBox("Dado excluido com sucesso.", 'success');
                    carregarGridContato();
                }else{
                    $.messageBox("Não foi possivel excluir o dado.", 'error');
                }
            },
            error: function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });
    //add um campo de telefone no maximo tres campos
    $('body').on('click', 'img.add-email', function(){
        if($('#email2').length == 0 ){
            $('div.email').append("<input type='text' name='email2' id='email2' placeholder='Email' class='span10'>").
                              append(" <img src='/images/delete.png' title='excluir email' class='delete-email' id-email='2' style='cursor:pointer; margin-bottom: 10px;'>");
            $('img.add-email').hide('slow');
            $('.dialog_contato').dialog('option', 'height', 500);
        }
    });
    
    //apaga o campo de telefone clicado
    $('body').on('click', '.delete-email', function(){
        var id_email = $(this).attr('id-email');
        $(this).remove();
        $('#email'+id_email).remove();
        $('img.add-email').css('display','inline-block');
        $('.dialog_contato').dialog('option', 'height', 460);
    });
    
    //add um campo de telefone no maximo tres campos
    $('body').on('click', 'img.add-tel-cont', function(){
        if($('#tel_contato2').length == 0 ){
            $('div.tel_contato').append("<input type='text' name='tel_contato2' id='tel_contato2' placeholder='Telefone' class='span10 telefone'>").
                              append(" <img src='/images/delete.png' title='excluir telefone' class='delete-tel-cont' id-tel-contato='2' style='cursor:pointer; margin-bottom: 10px;'>");
            $('.dialog_contato').dialog('option', 'height', $('.dialog_contato').dialog('option', 'height')+50);
        }else if($('#tel_contato3').length == 0){
            $('div.tel_contato').append("<input type='text' name='tel_contato3' id='tel_contato3' placeholder='Telefone' class='span10 telefone'>").
            append(" <img src='/images/delete.png' title='excluir telefone' class='delete-tel-cont' id-tel-contato='3' style='cursor:pointer; margin-bottom: 10px;'>");
            $('img.add-tel-cont').hide('slow');
            $('img.add-tel-cont').hide('slow');
            $('.dialog_contato').dialog('option', 'height', $('.dialog_contato').dialog('option', 'height')+50);
        }
        $('.telefone').maskTelefone();
    });
    
    //apaga o campo de telefone clicado
    $('body').on('click', 'img.delete-tel-cont', function(){
        var id_tel_contato = $(this).attr('id-tel-contato');
        $(this).remove();
        $('#tel_contato'+id_tel_contato).remove();
        $('img.add-tel-cont').css('display','inline-block');
        $('.dialog_contato').dialog('option', 'height', $('.dialog_contato').dialog('option', 'height')-50);
    });
    

    $('.indicacao-toggle-button').toggleButtons({
        width: 170,
        label: {
            enabled: "Colaborador",
            disabled: "Marketing"
        },
        style: {
            // Accepted values ["primary", "danger", "info", "success", "warning"] or nothing
            enabled: "info",
            disabled: "danger"
        },
        onChange: function ($el, status, e) {
            if(status){
                $('#indicacao_nome').show();
                $( "#indicacao_nome" ).autocomplete({
                    source: "/empresa/empresa/autocomplete",
                    minLength: 2,
                    select: function( event, ui ) {
                        $('#id_empresa_indicacao').val(ui.item.id);
                    },
                    search: function( event, ui ) {
                        $('#id_empresa_indicacao').val('');
                    }
                  });
                $('#ind_id').hide().val('');
                
            }else{
                $('#ind_id').show();
                $('#indicacao_nome').hide().val('');
                $('#id_empresa_indicacao').val('');
            }
        }
    });
    
    if($('#indicacao').is(":checked")){
        $('#indicacao_nome').show();
        $( "#indicacao_nome" ).autocomplete({
            source: "/empresa/empresa/autocomplete",
            minLength: 2,
            select: function( event, ui ) {
                $('#id_empresa_indicacao').val(ui.item.id);
            },
            search: function( event, ui ) {
                $('#id_empresa_indicacao').val('');
            }
          });
        $('#ind_id').hide();
        $('#ind_id').hide().val('');
        
    }else{
        $('#ind_id').show();
        $('#indicacao_nome').hide().val('');
        $('#id_empresa_indicacao').val('');
    }
    
});

//modifica os campos conforme o tipo de serviço selecionado
$.fn.tipoServico = function(){
    var id = $(this).val();
    if(id == 2){
        $('#cnpj_cpf').setMask('999.999.999-99');
        $('.label_cnpj').text('CPF').append('<span class="required">*</span>');
        $('div.municipal').hide('slow');
        $('.div_uasg').hide('slow');
        $('.div_fantasia').hide('slow');
        $('.label_nome_razao').text("Nome").append('<span class="required">*</span>');
        $('.label_estadual').text("RG");
        $('div.div_pessoa_fisica').show('slow');
    }else if(id == 1){
        $('#cnpj_cpf').setMask('99.999.999/9999-99');
        $('.label_cnpj').text('CNPJ').append('<span class="required">*</span>');
        $('div.municipal').show('slow');
        $('.div_uasg').show('slow');
        $('.div_fantasia').show('slow');
        $('.label_nome_razao').text("Razão social").append('<span class="required">*</span>');
        $('.label_estadual').text("Inscrição Estadual");
        $('div.div_pessoa_fisica').hide('slow');
        
    }
};

//mascara do telefone se tiver mais que um local
$.fn.maskTelefone = function(){
    $(this).setMask('999999999999');
};

// carrega o combo da cidade conforme o uf selecionado
function selectCidade(txt_cidade){
    var id_estado = $('#ufs_id').val();
    if(id_estado == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: '/sis/cidade/pairs/id_estado/'+id_estado,
        success: function(data){
            var list = data.list;
            var html =  "<option >Cidade</option>";
            $.each(list, function(key, value){
                html += "<option value='"+key+"'>"+value+"</option>";
            });
            $('#cid_id').html(html);
        },
        complete: function(){
            if(txt_cidade != null){
                $('#cid_id > option').each(function(){
                    if($(this).text() == txt_cidade){
                        var CidadeVal = $(this).attr('value');
                        $('#cid_id').val(CidadeVal);
                    }
                });
            }
        }
    });
}

//busca o cep e preenche os campos
function findCEP() {
    if($.trim($("#cep").val()) != ""){
        $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+$("#cep").val(), function(){
            if(resultadoCEP["resultado"] == 1){
                $("#tipo_logradouro").val(unescape(resultadoCEP["tipo_logradouro"]));
                $("#nome_logradouro").val(unescape(resultadoCEP["logradouro"]));
                $("#bairro").val(unescape(resultadoCEP["bairro"]));
                $('#ufs_id > option').each(function(){
                    if($(this).text() == unescape(resultadoCEP["uf"])){
                        var value = $(this).attr('value');
                        $('#ufs_id').val(value);
                        selectCidade(unescape(resultadoCEP["cidade"]));
                    } 
                });
                //$("#city").val(unescape(resultadoCEP["cidade"]));
                //$("#state").val(unescape(resultadoCEP["uf"]));
                $("#numero").focus();
            }else{
                alert("Endereço não encontrado para o cep ");
            }
        });
    }
}

//se tiver id_empresa irá carregar a grid do endereço
function carregarGrid(){
    if($('#id').val() != ""){
        App.blockUI($('div.grid_endereco'));
    }
    
    var id_empresa = $('#id').val();
    if(id_empresa != ""){
        $.ajax({
            type: "GET",
            url: "/empresa/empresa/grid-endereco/id_empresa/"+id_empresa,
            success: function(data){
                App.unblockUI($('div.grid_endereco'));
                $(".grid_endereco").html(data);
            }
        });
    }
    
}

//se tiver id_empresa irá carregar a grid do endereço
function carregarGridFinanceiro(){
    if($('#id').val() != ""){
    	$(".grid_financeiro").css("background-color","#F3F3F3");
    	$(".grid_financeiro").html("<img src='/images/loading-ajax.gif' style='margin: 0 auto;display:block'></img>");
    }
    var id_empresa = $('#id').val();
    if(id_empresa != ""){
        $.ajax({
            type: "GET",
            url: "/empresa/empresa/grid-financeiro/idEmpresa/"+id_empresa,
            success: function(data){
                $(".grid_financeiro").html(data).css("background-color","#FFFFFF");
            }
        });
    }
    
}
function carregarGridContato(){
    if($('#id').val() != ""){
        App.blockUI($('div.grid_contato'));
    }
    
    var id_empresa = $('#id').val();
    if(id_empresa != ""){
        $.ajax({
            type: "GET",
            url: "/empresa/empresa/grid-contato/id_empresa/"+id_empresa,
            success: function(data){
                App.unblockUI($('div.grid_contato'));
                $(".grid_contato").html(data);
            }
        });
    }
    
}
var FormValidation = function () {


    return {
        //main function to initiate the module
        init: function () {

            // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#form_corporativo');

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                	nome_razao: {
                        required: true
                    },
                    cnpj_cpf: {
                        required: true,
                    },
                    tps_id: {
                        required: true
                    }
                },
                messages: { // custom messages for radio buttons and checkboxes
                	nome_razao: {
                        required: "Campo obrigatório."
                    },
                    cnpj_cpf: {
                        required: "Campo obrigatório.",
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    App.scrollTo(form1, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.help-inline').removeClass('ok'); // display OK icon
                    $(element)
                        .closest('.control-group').removeClass('success').addClass('error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change dony by hightlight
                    $(element)
                        .closest('.control-group').removeClass('error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .addClass('valid') // mark the current input as valid and display OK icon
                    .closest('.control-group').removeClass('error').addClass('success'); // set success class to the control group
                },

            });


        }

    };

}();