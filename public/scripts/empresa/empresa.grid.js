$(document).ready(function(){
    
    var searchField = $('#searchField').val();
    if(searchField == ""){
        $('#searchField').val('all');
    }
    
    $('body').on('click', 'a.detail', function(e){
        e.preventDefault();
        var id = $(this).attr('href');
        $.ajax({
            type: "GET",
            url: '/empresa/empresa/get/id/'+id,
            success: function(data){
                //se for juridica
                if(data.tps_id == 1){
                    $('.label_nome_razao').text("Razão Social");
                    $('.label_estadual').text('Inscrição Estadual');
                    $('.label_cnpj_cpf').text('CNPJ');
                    $('tr.label_fantasia').show();
                    $('tr.label_uasg').show();
                    $('tr.label_municipal').show();
                    
                //se for fisica
                }else if(data.tps_id == 2){
                    $('.label_nome_razao').text("Nome");
                    $('.label_estadual').text('RG');
                    $('.label_cnpj_cpf').text('CPF');
                    $('tr.label_fantasia').hide();
                    $('tr.label_uasg').hide();
                    $('tr.label_municipal').hide();
                    
                }
                var telefone = data.telefone1? data.telefone1 : "";
                telefone = data.telefone2 ? telefone+"/"+data.telefone2:telefone+"";
                telefone = data.telefone3? telefone+'/'+data.telefone3:telefone+"";
                $('div.dialog_detail').dialog({
                    modal: true,
                    resizable: false,
                    title: "Resumo",
                    dialogClass: 'ui-dialog-green',
                    position: [($(window).width() / 2) - (500 / 2), 150],
                    width: 700,
                    height: 'auto',
                    open: function(){
                    	$('.value_nome_razao_title').text(data.nome_razao ? data.nome_razao: "-");
                        $('.value_nome_razao').text(data.nome_razao ? data.nome_razao: "-");
                        $('.value_fantasia').text(data.fantasia ? data.fantasia: "-");
                        $('.value_cnpj_cpf').text(data.cnpj_cpf ? data.cnpj_cpf : "-");
                        $('.value_uasg').text(data.uasg ? data.uasg: "-");
                        $('.value_estadual').text(data.estadual ? data.estadual: "-");
                        $('.value_municipal').text(data.municipal ? data.municipal: "-");
                        $('.value_telefone').text( telefone ? telefone: "-");
                        $('.value_site').text(data.site ? data.site: "-");
                        $('.value_email_corporativo').prepend(data.email_corporativo ? "<a href='mailto:"+data.email_corporativo+"' target='_top'>"+data.email_corporativo+"</a>" : "-");
                        $('.value_transportador').text(data.transportador == 1 ? 'Sim' : "não");
                        $('.value_cliente').text(data.tipo_cliente ? data.tipo_cliente: "-");
                        $('.value_portal').text(data.portal ? data.portal: "-");
                        $('.value_mail_marketing').text(data.mail_marketing ? data.mail_marketing: "-");
                        $('.value_segmento').text(data.segmento ? data.segmento: "-");
                        $('.value_fornecedor').text(data.tipo_fornecedor ? data.tipo_fornecedor: "-");
                        $('.value_indicacao').text(data.indicacao ? data.indicacao: "-");
                        $('.value_observ').text(data.observacoes ? data.observacoes: "-");
                        $('.value_tipo_funcionario').text(data.tipo_funcionario ? data.tipo_funcionario: "-");
                    },
                    close: function(){
                      }
                });
            },
            error: function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });
});