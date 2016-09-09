$(document).ready(function(){
    $.fn.editable.defaults.inputclass = 'm-wrap';
    $.fn.editable.defaults.url = '/post';
    $.fn.editable.defaults.emptytext = '----';
    $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
    $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    // Editando a entidade do ticket
    $('.editable.fin_empresa').attr('data-type', 'select2');
    $('.editable.fin_empresa').editable({
        placement: 'right',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        inputclass: 'input-large m-wrap',
        params: function(params) {
            //originally params contain pk, name and value
            params.fin_id = $(this).attr('data-pk');
            params.empresa_sacado_selected = params.value;
            return params;
        },
       select2: {
           placeholder: 'Selecione entidade',
           ajax: {
               url: 'empresa/empresa/autocomplete',
               dataType: 'json',
               data: function (term, page) {
                   return { term: term };
               },
               results: function (data, page) {
                   return { results: data };
               }
           },
           formatResult: function (item) {
               return item.value;
           },
           formatSelection: function (item) {
               return item.value;
           }
       },
       display: function(value){
           if (!value) {
               return;
           }
           var $this = $(this);
           $.get('empresa/empresa/get/id/'+value, function(data){
               $this.html(data.nome_razao);
           });
       }
    });

    //editando descrição do financeiro
    $('.editable.fin_descricao_ticket').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.fin_id            = $(this).attr('data-pk');
            params.fin_descricao     = params.value;
            return params;
        }
    });

    //editando valor do financeiro
    $('.editable.fin_valor').editable({
        clear: false,
        inputclass: 'decimal m-wrap',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.fin_id       = $(this).attr('data-pk');
            params.fin_valor    = params.value;
            return params;
        }
    });
    $('.editable.fin_valor').on('shown', function(e, editable) {
        $('.decimal').setMask('decimal');
    });
    $.fn.datepicker.defaults.language= "pt-BR";
    //editando a emissão do financeiro
    $('.editable.fin_emissao').attr('data-type', 'date');
    $('.editable.fin_emissao').editable({
        format: 'dd/mm/yyyy',
        viewformat: 'dd/mm/yyyy',
        datepicker:{
            language: "pt-BR",
        },
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.fin_id         = $(this).attr('data-pk');
            params.fin_emissao    = params.value;
            return params;
        }
    });

    //editando a vencimento do financeiro
    $('.editable.fin_vencimento').attr('data-type', 'date');
    $('.editable.fin_vencimento').editable({
        format: 'dd/mm/yyyy',
        viewformat: 'dd/mm/yyyy',
        datepicker:{
            language: "pt-BR",
        },
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.fin_id         = $(this).attr('data-pk');
            params.fin_vencimento    = params.value;
            return params;
        }
    });

    //editando a vencimento do financeiro
    $('.editable.fin_compensacao').attr('data-type', 'date');
    $('.editable.fin_compensacao').editable({
        format: 'dd/mm/yyyy',
        viewformat: 'dd/mm/yyyy',
        datepicker:{
            language: "pt-BR",
        },
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
            var id = $(this).attr('data-pk');
            if($('.con_id[data-pk="'+id+'"]').text() == "----"){
                return 'Selecione uma conta para esse ticket.';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.fin_id         = $(this).attr('data-pk');
            params.fin_compensacao    = params.value;
            return params;
        }
    });

    //editando o conta
    $('.editable.con_id').attr('data-type', 'select2');
    $('.editable.con_id').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        inputclass: 'input-large m-wrap',
        params: function(params) {
            //originally params contain pk, name and value
            params.fin_id        = $(this).attr('data-pk');
            params.con_id        = params.value;
            return params;
        },
       select2: {
           placeholder: 'Selecione workspace',
           ajax: {
               url: 'financial/contas/autocomplete',
               dataType: 'json',
               data: function (term, page) {
                   return { term: term };
               },
               results: function (data, page) {
                   return { results: data };
               }
           },
           formatResult: function (item) {
               return item.value;
           },
           formatSelection: function (item) {
               return item.value;
           }
       },
       display: function(value){
           if (!value) {
               return;
           }
           var $this = $(this);
           $.get('financial/contas/get/id_conta/'+value, function(data){

               $this.html(data.con_codnome);
           });
       }
    });
});