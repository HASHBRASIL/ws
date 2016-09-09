$(document).ready(function(){
    $.fn.editable.defaults.inputclass = 'm-wrap';
    $.fn.editable.defaults.url = '/post';
    $.fn.editable.defaults.emptytext = '----';
    $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
    $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    
	$('body').on('click', 'a.deleteMoldal', function(e){
        e.preventDefault();
        var id = $(this).attr("value");
        $("#valueDelete").prepend(name);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-grey',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir Feedback",
            width: 450,
            height: 200,
            buttons: [
                      {
		                 'class' : 'btn gree',
		                 "text" : "Cancelar",
		                click: function() {
		                  $( this ).dialog( "close" );
		                }
                      },
                      {
		                 'class' : 'btn red',
		                 "text" : "Excluir",
                    	  click: function() {
                    		  window.location.href="/sis/feedback/delete/id/"+id;
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
        
    });
	
    //------------------------------- editando status --------------------------
    $('.editable.status_feed').attr('data-type', 'select2');
    $('.editable.status_feed').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        inputclass: 'input-large m-wrap',
        params: function(params) {
            //originally params contain pk, name and value
            params.id               = $(this).attr('data-pk');
            params.id_status_feed   = params.value;
            return params;
        },
       select2: {
           placeholder: 'Selecione Status',
           ajax: {
               url: '/sis/status-feedback/autocomplete',
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
           $.get('/sis/status-feedback/get/id/'+value, function(data){    

               $this.html(data.nome);
           });
       }
    }); 
});