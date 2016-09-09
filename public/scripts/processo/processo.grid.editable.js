$(document).ready(function(){
    $.fn.editable.defaults.inputclass = 'm-wrap';
    $.fn.editable.defaults.url = '/post';
    $.fn.editable.defaults.emptytext = '----';
    $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
    $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';

 // Editando a entidade do ticket
    $('.editable.cliente').attr('data-type', 'select2');
    $('.editable.cliente').editable({
        placement: 'right',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        inputclass: 'input-large m-wrap',
        params: function(params) {
            //originally params contain pk, name and value
            params.pro_id = $(this).attr('data-pk');
            params.empresas_id = params.value;
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

 // ----------Editando a entidade do ticket------------------------------------
    $('.editable.status').attr('data-type', 'select2');
    $('.editable.status').editable({
        placement: 'right',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        inputclass: 'input-large m-wrap',
        params: function(params) {
            //originally params contain pk, name and value
            params.pro_id = $(this).attr('data-pk');
            params.sta_id = params.value;
            return params;
        },
       select2: {
           placeholder: 'Selecione entidade',
           ajax: {
               url: 'processo/status/autocomplete',
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
           $.get('processo/status/get/id/'+value, function(data){
               $this.html(data.sta_descricao);
           });
       }
    });
    //------------------------- Editando Descrição -----------------------------
    $('.editable.pro_desc_produto').attr('data-type', 'textarea');
    $('.editable.pro_desc_produto').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.pro_id               = $(this).attr('data-pk');
            params.pro_desc_produto     = params.value;
            return params;
        }
    });
    //-----------------------Editando Quantidade -------------------------------
    $('.editable.pro_quantidade').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.pro_id               = $(this).attr('data-pk');
            params.pro_quantidade       = params.value;
            return params;
        },
        inputclass : 'm-wrap quantidade',
        clear: false
    });
    $('.editable.pro_quantidade').on('shown', function(e, editable) {
        $('input.quantidade').setMask('integer');
    });
    //-----------------------Editando Valor unitario----------------------------
    $('.editable.pro_vlr_unt').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.pro_id            = $(this).attr('data-pk');
            params.pro_vlr_unt       = params.value;
            return params;
        },
        inputclass : 'm-wrap decimal',
        clear: false
    });
    $('.editable.pro_vlr_unt').on('shown', function(e, editable) {
        $('input.decimal').setMask('decimal');
    });
    //-----------------------Editando Valor unitario----------------------------
    $('.editable.pro_vlr_pedido').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.pro_id            = $(this).attr('data-pk');
            params.pro_vlr_pedido    = params.value;
            return params;
        },
        inputclass : 'm-wrap decimal',
        clear: false
    });
    $('.editable.pro_vlr_pedido').on('shown', function(e, editable) {
        $('input.decimal').setMask('decimal');
    });
});