<?php
//gerar menu

    require_once "connect.php";

    $listaServicos = $dbh->query(
        'SELECT
            ts.id AS "ts_id", tsm.id_servico AS "fk_servico",
            tsm.id AS "tsm_id",
            ts.id_grupo, ts.id_pai, ts.id_tib,
            ts.nome,
            ts.descricao,
            ts.fluxo, ts.metanome,
            tsm.metanome,
            tsm.valor
        FROM
            tb_servico ts
        LEFT OUTER JOIN tb_servico_metadata tsm ON ts.id = tsm.id_servico ORDER BY ts.nome'
    );

    $servicos = $listaServicos->fetchAll( PDO::FETCH_ASSOC );

    $tipoMaster = "Master";
    $queryCount = $dbh->prepare(
        "SELECT
            count(ib.id) AS qtd, tib.nome, tib.id
        FROM
            tb_itembiblioteca ib RIGHT OUTER JOIN ( SELECT id,nome FROM tp_itembiblioteca WHERE tipo = :tipoMaster) tib ON (ib.id_tib = tib.id  )
        GROUP BY tib.nome, tib.id
        ORDER BY qtd DESC"
    );

    try{

        $queryCount->bindParam( ':tipoMaster', $tipoMaster );
        $queryCount->execute();
        $countResult = $queryCount->fetchAll( PDO::FETCH_ASSOC );

    }catch ( PDOException $e ){
         var_dump( $e->getMessage );
    }

    $queryBuscaMetanome = $dbh->query( "SELECT metanome FROM tb_servico WHERE metanome IS NOT NULL" );
    $buscaMetanome = $queryBuscaMetanome->fetchAll( PDO::FETCH_ASSOC );
?>

<div class="modal fade" id="modalAtencao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Atenção!</h4>
            </div>
            <div class="modal-body">
                É necessário ter uma Tib ou um arquivo atrelado a serviço a ser criado.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div id="mensagens" class="alert alert-success hidden" role="alert" style="margin-top: 60px"></div>

<form action="includes/insertMenu.php" method="post" accept-charset="utf-8" id="formServico" class="">

    <div class="row wrapper">
        <div class="page-header">
            <h1><?php echo $nome; ?></h1>
            <span><?php echo $descricao; ?></span>
            <?php include "rastro.php"; ?>
        </div>
        <div class="col-md-12 content">
            <div class="form-group-one-unit">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome_servico">Nome</label>
                            <input type="text" class="form-control input-sm" id="nome_menu" name="_nome_menu" placeholder="Nome Menu">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="descricao_servico">Descrição</label>
                            <input type="text" class="form-control input-sm" id="descricao_menu" name="_descricao_menu" placeholder="Descrição menu">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="arquivo_servico">Arquivo</label>
                            <input type="text" class="form-control input-sm" id="arquivo_menu" name="_arquivo_menu" placeholder="Arquivo Menu">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="arquivo_metanome">Metanome</label>
                            <input type="text" class="form-control input-sm" id="metanome_menu" name="_metanome_menu" placeholder="Menu Metanome">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row wrapper wrapper-white">
        <div class="page-header">
            <h1>Ações do Menu</h1>
            <span>Texto descritivo da sessão da página</span>
        </div>
        <div class="col-md-12 content">
            <div class="form-header">
                <h1>Serviço <span>Breve descriçao do formulário</span></h1>
            </div>
            <div class="form-group-one-unit">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Escolha a TIB</label>
                            <select name="_masters" id="selectTib" class="form-control chosen-select">
                                <option value="null"></option>
                                <?php foreach( $countResult as $item): ?>
                                    <option value="<?php echo $item['id']; ?>"><?php echo  $item['nome'] . " | " . $item['qtd']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Escolha um servico pai</label>
                            <select name="_servico" id="listaServico" class="form-control chosen-select">
                                <option value="null">Escolha um Serviço</option>
                                <?php

                                    foreach ($servicos as $k => $v) {
                                        $classe = "";
                                        if( pai ){
                                            $classe .= " category ";
                                        }

                                        if( filho ){
                                            $classe .= " item ";
                                        }

                                        echo "<option class='".$classe."' value='".$v['ts_id']."'>".$v['nome']."</option>\n";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Escolha o fluxo</label>
                            <select name="_fluxo" id="listaFluxo" class="form-control chosen-select">
                                <option value="null"    >Escolha um Fluxo</option>
                                <option value="criar"   >Criar</option>
                                <option value="remover" >Remover</option>
                                <option value="editar"  >Editar</option>
                                <option value="share"   >Compartilhar</option>
                                <option value="comentar">Comentar</option>
                                <option value="aprovar" >Aprovar</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <button type="submit" id="submitInsertMasterTib" class="btn btn-success btn-sm pull-right">Salvar</button>
            </div>
        </div>
    </div>
</form>

<script>
    var arrMetanome = <?php echo json_encode( $buscaMetanome ); ?>

    $( "#metanome_servico" ).on( 'input', function( e ){
        $.each( arrMetanome, function( chave, valor ){
            if( $( "#metanome_servico" ).val().toLowerCase() == valor['metanome'].toLowerCase() ){
                $( "#metanome_servico" ).parent().addClass( 'bg-danger' );
                $('#submitInsertMasterTib').attr('disabled', 'disabled');
                return false;
            }else{
                $( "#metanome_servico" ).parent().removeClass( 'bg-danger' );
                $('#submitInsertMasterTib').removeAttr('disabled');
            }
        } );
    });

    $("#formServico").submit(function(e){
        e.preventDefault();

        data = $('#formServico').serializeArray();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: data,
        }).done(function( response ) {
            if( response == 'error' ){
                // console.log( response );
                // alert( 'Erro ao salvar!\n' );
                $('#mensagens').removeClass('hidden').removeClass('alert-success').addClass('alert-danger').show();
                $("#mensagens").text( "Ocorreu algum erro, entre em contato com o suporte." );
            }
            // else if( response == 'arquivo' ){
            //  $( '#modalAtencao' ).modal( 'show' );
            // }
            else{
                // console.log( response );
                // Mostra a mensagem com os dados retornados
                $('#mensagens').removeClass('hidden').show();
                $("#mensagens").text( response );
            }
        });
    });
</script>
