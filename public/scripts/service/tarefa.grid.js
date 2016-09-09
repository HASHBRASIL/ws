$(document).ready(function(){
    $( "#minuto" ).spinner({
        min: 0
    });
    $('#minuto').setMask('999999999');
    gridTarefa();
    $('#novoForm').click(function(e){
        e.preventDefault();
        $('#dialog-form').dialog({
            title: "Cadastrar tarefa",
            modal: true,
            width: 400,
            buttons: {
                "Cancelar": function() {
                    $( this ).dialog( "close" );
                },
                "Salvar": function() {
                    var nome = $('#nome').val();
                    var minuto = $('#minuto').val();
                    var id_tarefa = $('#id_tarefa').val();
                    if( validar(nome, "O campo nome está vazio") || validar(minuto, "O campo minuto está vazio.") ){
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: "/service/tarefa/form",
                        data: {'nome': nome, 'tempo_extimado' : minuto,
                               'id_servico': $('#id_servico').val(),
                               'id_tarefa' : id_tarefa },
                        success: function(e){
                            if(e.success){
                                if(id_tarefa == ""){
                                    $.messageBox("Tarefa inserido com sucesso.", 'success');
                                }else{
                                    $.messageBox("Tarefa atualizado com sucesso.", 'success');
                                }
                                gridTarefa();
                                $('#dialog-form').dialog('close');
                            }else{
                                alert('Não foi possivel salvar no momento.');
                            }
                        },
                        error: function(e){
                            alert("Sistema está fora do ar entre em contato com o administrador.");
                        }
                    });
                }
            },
        close: function(){
            $('#minuto').val('');
            $('#nome').val('');
        }
        });
    });
    
    //editar tarefa
    $("body").on("click", ".editar-tarefa", function(e){
        e.preventDefault();
        var id_tarefa    = $(this).attr("href");
        var nome         = $(this).attr('name');
        var tempo        = $(this).attr("tempo");

        $('#minuto').val(tempo);
        $('#nome').val(nome);
        $('#id_tarefa').val(id_tarefa);
        $("#novoForm").trigger("click");
      });
    
    //deletar tarefa
    $("body").on("click", ".deletar-tarefa", function(e){
        e.preventDefault();
        var href = $(this).attr("href");
        $.ajax({
            type: "GET",
            url: href,
            success: function(e){
                if(e[0].type == "success"){
                    $.messageBox(e[0].text, 'success');
                }else{
                    $.messageBox(e[0].text, 'success');
                }
            },
            error: function(e){
                alert("Sistema está fora do ar entre em contato com o administrador.");
            }
        });
    });
});

function validar(value, mensagem){
    if(value == ""){
        alert(mensagem);
        return true;
    }
    return false;
}

function gridTarefa(){
    $.ajax({
        type: 'GET',
        url: "/service/tarefa/grid/id_servico/"+$('#id_servico').val(),
        success: function(data){
            $('#grid-tarefa').html(data);
        },
        error: function(){
            alert("Não foi possivel carregar a tabela de tarefa. Tente novamente mais tarde.");
        }
    });
}