$(document).ready(function(){
    $.fn.editable.defaults.inputclass = 'm-wrap';
    $.fn.editable.defaults.url = '/post';
    $.fn.editable.defaults.emptytext = '----';
    $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
    $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    //editando descrição do financeiro
    $('.editable.fin_descricao').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.id_agrupador_financeiro = $(this).attr('data-pk');
            params.fin_descricao           = params.value;
            return params;
        },
        display: function(value){
            if (!value) {
                return;
            }
            $(this).html(value);
            var id_correlato = $(this).attr('data-pk-correlato');
            if(id_correlato != ""){
                $('.editable.fin_descricao[data-pk="'+id_correlato+'"]').html(value);
            }
        }
    });
    //editando nota fiscal
    $('.editable.fin_nota_fiscal').editable({
        params: function(params) {
            //originally params contain pk, name and value
            params.id_agrupador_financeiro = $(this).attr('data-pk');
            params.fin_nota_fiscal           = params.value;
            return params;
        },
        display: function(value){
            if (!value) {
                return;
            }
            $(this).html(value);
            var id_correlato = $(this).attr('data-pk-correlato');
            if(id_correlato != ""){
                $('.editable.fin_nota_fiscal[data-pk="'+id_correlato+'"]').html(value);
            }
        }
    });

    //editando o workspace
    $('.editable.nome').attr('data-type', 'select2');
    $('.editable.nome').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        inputclass: 'input-large m-wrap',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_agrupador_financeiro = $(this).attr('data-pk');
            params.id_workspace            = params.value;
            return params;
        },
       select2: {
           placeholder: 'Selecione workspace',
           ajax: {
               url: '/auth/workspace/autocomplete',
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
           $.get('/auth/workspace/get/id/'+value, function(data){

               $this.html(data.nome);
           });
       }
    });

    //editando a entidade
    $('.editable.nome_razao').attr('data-type', 'select2');
    $('.editable.nome_razao').editable({
        placement: 'right',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        inputclass: 'input-large m-wrap',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_agrupador_financeiro = $(this).attr('data-pk');
            params.id_empresa              = params.value;
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
            params.id_agrupador_financeiro = $(this).attr('data-pk');
            params.fin_valor               = params.value;
            return params;
        },
        display: function(value){
            if (!value) {
                return;
            }
            $(this).html(value);
            var id_correlato = $(this).attr('data-pk-correlato');
            if(id_correlato != ""){
                $('.editable.fin_valor[data-pk="'+id_correlato+'"]').html(value);
            }
        }
    });
    $('.editable.fin_valor').on('shown', function(e, editable) {
        $('.decimal').setMask('decimal');
    });

});