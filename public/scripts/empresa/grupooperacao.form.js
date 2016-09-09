$(document).ready(function(){
    $('img.add-tel').click(function(){
            if($('#telefone2').length == 0 ){
                $('div.telefone').append("<input type='text' name='ope_telefone2' id='telefone2'  class='telefone span10' style='display:inline-block; margin-top: 5px;'>").
                                  append(" <img src='/images/delete.png' data-tooltip title='excluir telefone' class='delete-tel tooltips' id-tel='2' style='cursor:pointer'>");
                if ($('.telefone').length == 4){
                    $('.add-tel').hide();
                }
            }
           
            else if($('#telefone3').length == 0){
                $('div.telefone').append("<input type='text' name='ope_telefone3' id='telefone3'  class='telefone span10' style='display:inline-block; margin-top: 5px;'>").
                                  append(" <img src='/images/delete.png' data-tooltip title='excluir telefone' class='delete-tel tooltips' id-tel='3' style='cursor:pointer'>");
                $('img.add-tel').hide('slow');
            }
            
            
    });
    
    $('body').on('click', '.delete-tel', function(){
        var id_tel = $(this).attr('id-tel');
        $(this).remove();
        $('#telefone'+id_tel).remove();
        $('.tooltip').hide();
        $('img.add-tel').css('display','inline-block');
    }); 
    
    
    
    $('img.add-email').click(function(){
        if($('#email2').length == 0 ){
            $('div.email').append("<input type='text' name='ope_email2' id='email2' class='email span10' style='display:inline-block; margin-top: 5px;'>").
                              append(" <img src='/images/delete.png' data-tooltip title='excluir email' class='delete-email tooltips' id-email='2' style='cursor:pointer'>");
            $('img.add-email').hide('slow');
        }
       
    }); 
    
    $('body').on('click', '.delete-email', function(){
        var id_email = $(this).attr('id-email');
        $(this).remove();
        $('#email'+id_email).remove();
        $('.tooltip').hide();
        $('img.add-email').css('display','inline-block');
    }); 
    
    
    if ($('#email2').length  >0){
        $('.add-email').hide();
        
    };
    
    
    if ($('.telefone').length == 4){
        $('.add-tel').hide();
        
    };
    
    $('#ope_id').submit(function() {
        var messageValidate = new Array();
        
        if ($('#ope_cpf_cnpj').val() == "" ){
            messageValidate[0] = "CNPJ / CPF em branco";            
        };
        
        
        if ($('#ope_nome').val() == ""){
            messageValidate[1] = "Nome em branco";            
        };
        
        if(messageValidate.length >0){
            alert(messageValidate);
            return false;
        }
        if (!messageValidate)
            return true;
        
      });    
    
    
    
        
        
    
    
    
   
    
    
    
    
});