<?php

    // echo '<PRE>';
    // var_dump($SERVICO);
    // die();
    require_once "connect.php";
    require_once "UUID.php";

    // $listaServicos = $dbh->query(
    //     'SELECT
    //         ts.id AS "ts_id", tsm.id_servico AS "fk_servico",
    //         tsm.id AS "tsm_id",
    //         ts.id_grupo, ts.id_pai, ts.id_tib,
    //         ts.nome,
    //         ts.descricao,
    //         ts.fluxo, ts.metanome,
    //         tsm.metanome,
    //         tsm.valor
    //     FROM
    //         tb_servico ts
    //     LEFT OUTER JOIN tb_servico_metadata tsm ON ts.id = tsm.id_servico ORDER BY ts.nome'
    // );

    $listaServicos = $dbh->query(
        'SELECT
            ts.id AS "ts_id",
            ts.id_grupo, ts.id_pai, ts.id_tib,
            ts.nome,
            ts.descricao,
            ts.fluxo, ts.metanome
        FROM
            tb_servico ts
        ORDER BY ts.nome'
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

    $servicosFilhos = $dbh->query( "SELECT id, descricao, fluxo, metanome, nome, id_grupo, id_pai, id_tib, visivel FROM tb_servico WHERE id_pai IS NOT NULL AND visivel = 't' ORDER BY nome ASC" );
    $filhos = $servicosFilhos->fetchAll( PDO::FETCH_ASSOC );


    //$queryGetServico = $dbh->prepare("SELECT *");

?>

<div class="modal fade" id="modalAtencao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Atenção!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">icon</div>
                    <div class="col-md-6">ASD</div>
                    <div class="col-md-6">DF</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div id="mensagens" class="alert alert-success hidden" role="alert" style="margin-top: 60px"></div>

<form action="includes/insertServico.php" method="post" accept-charset="utf-8" id="formServico" class="">
    <input type="hidden" value="" name="servico" />
    <div class="row wrapper wrapper-white">
        <div class="page-header">
            <h1><?php echo $HASH_SERVICO['nome']; ?></h1>
            <span><?php echo $HASH_SERVICO['descricao']; ?></span>
        </div>
        <div class="col-md-12 content">
            <div class="form-group-one-unit">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome_servico">Nome</label>
                            <input type="text" class="form-control input-sm" id="nome_servico" name="nome" placeholder="Nome Serviço">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="descricao_servico">Descrição</label>
                            <input type="text" class="form-control input-sm" id="descricao_servico" name="descricao" placeholder="Descrição Serviço">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="metanome_servico">Metanome</label>
                            <input type="text" class="form-control input-sm" id="metanome_servico" name="metanome" placeholder="Serviço Metanome">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="listaVisivel">Visivel</label>
                            <select name="visivel" id="listaVisivel" class="form-control chosen-select">
                                <option value="null" disabled="disabled">Escolha uma opção</option>
                                <option value="TRUE" selected="selected" >Sim</option>
                                <option value="FALSE">Não</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="selectTib">Escolha a TIB</label>
                            <select name="tib" id="selectTib" class="form-control chosen-select">
                                <option value="null">Escolha uma TIB</option>
                                <?php foreach( $countResult as $item): ?>
                                    <option value="<?php echo $item['id']; ?>"><?php echo  $item['nome'] . " | " . $item['qtd']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="listaServico">Escolha um serviço pai</label>
                            <select name="servico_pai" id="listaServico" class="form-control chosen-select">
                                <option value="null">Escolha um Serviço</option>
                                <?php
                                    foreach ($servicos as $k => $v) {
                                        echo "<option class='' value='".$v['ts_id']."'>".$v['nome']."</option>\n";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="listaFluxo">Escolha o fluxo</label>
                            <select name="fluxo" id="listaFluxo" class="form-control chosen-select">
                                <option value="null" selected='selected' >Escolha um Fluxo</option>
                                <option value="criar"   >Criar</option>
                                <option value="remover" >Remover</option>
                                <option value="editar"  >Editar</option>
                                <option value="share"   >Compartilhar</option>
                                <option value="comentar">Comentar</option>
                                <option value="aprovar" >Aprovar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="arquivo_servico">Arquivo</label>
                            <input type="text" class="form-control input-sm notMetadata" id="arquivo_servico" name="arquivo" placeholder="Arquivo Metadata"  data-metadata="ws_arquivo">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="comportamento">Comportamento</label>
                            <select name="comportamento" id="comportamento" class="form-control chosen-select notMetadata" data-metadata="ws_comportamento">
                                <option value="null" selected="selected" >Escolha um Comportamento</option>
                                <option value="tab"       >Tab</option>
                                <option value="action"    >Action</option>
                                <option value="listaction">List Action</option>
                                <option value="formaction">Form Action</option>
                                <option value="pagination">Pagination</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ordem">Ordem</label>
                            <input type="text" class="form-control input-sm" id="ordem" name="ordem" placeholder="Ordem">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="show">Tipo de Link (show)</label>
                            <select name="show" id="show" class="form-control chosen-select notMetadata" data-metadata="ws_show">
                                <option value="null" selected="selected" >Escolha um tipo de link</option>
                                <option value="ajax"  >Ajax</option>
                                <option value="modal" >Modal</option>
                                <option value="reload">Navegação</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row wrapper">
        <div class="page-header">
            <h1>Metadatas</h1>
            <span>Metadatas associados a esse serviço</span>
        </div>
        <div class="col-md-12 content content-btn">
            <div class="row">
                <button type="button" id="novoMetadata" class="btn btn-info btn-xs pull-right">Novo Metadata</button>
            </div>
        </div>
        <div class="col-md-12 content">
            <div class="form-group-one-unit containerMetadata">
                <div class="row contentMetadata_0">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="_arquivo_servico">MetaNome</label>
                            <input type="text" class="form-control input-sm not" id="metanome_metadata_0" name="metanome_metadata_0" placeholder="Metanome">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="valor_metadata">Valor</label>
                            <input type="text" class="form-control input-sm not" id="valor_metadata_0" name="valor_metadata_0" placeholder="Valor">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>Excluir</label>
                            <button type="button" class="btn btn-xs btn-danger btn-excluir"> <i class="fa fa-times"></i> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row wrapper">
        <div class="col-md-12 content content-btn">
            <div class="row">
                <?php if (isset($SERVICO['filhos'])):
                    foreach($SERVICO['filhos'] as $key => $value):
                        if(isset($value['metadata']['ws_comportamento']) && ($value['metadata']['ws_comportamento'] == 'formaction') ): ?>
                            <button type="submit" id="submitInsertMasterTib" class="btn btn-success btn-sm pull-right formaction" data-servico="<?= $value['id']; ?>" data-show="<?= $value['metadata']['ws_show']; ?>"><?= $value['nome']; ?></button>
                        <?php  endif;
                    endforeach;
                endif; ?>
            </div>
        </div>
    </div>

</form>

<script>


    $('.formaction').bind('click', function(e){
        var servico       = $(this).attr('data-servico');
        var comportamento = $(this).attr('data-show');
        if(typeof trataForm == "function") {
            var data      = trataForm($('#formServico'));
        } else {
            alert('');
        }
        return;

        if(show != 'reload') {
            e.preventDefault();
            var ret = ajaxFunction(servico, data, comportamento);
            console.log(ret);
        } else {
            $("#formServico").find("input[name*='servico']").val( servico )
        }
    });

    var cloneMetadata = $('div[class*="contentMetadata"]').clone();
    $("#novoMetadata").bind('click', function(e){
        var contador = $('.containerMetadata').find('div.row').length;
        var clone    = cloneMetadata.clone();
        clone.find('input[id*="metanome_metadata"]').attr('id', 'metanome_metadata_'+contador).attr('name', 'metanome_metadata_'+contador);
        clone.find('input[id*="valor_metadata"]').attr('id', 'valor_metadata_'+contador).attr('name', 'valor_metadata_'+contador);
        clone.attr('class', '').addClass('row').addClass('contentMetadata_' + contador);
        $('.containerMetadata').append(clone);
        $('.btn-excluir').bind('click', excluirClone);
    });

    $('.btn-excluir').on('click', excluirClone);

    function excluirClone(){
        var metadata = $(this).parent().parent().parent();
        metadata.remove();
    }

    // $('#formServico').submit(function(e){
    //     e.preventDefault();

    function trataForm(frm){
        var ob = new Object();
        frm.find('input, select').not('.not, .notMetadata, .chosen-search>input').each(function(indice, valor){
            var nome = $(valor).attr('name');
            ob[nome] = $(valor).val();
        });

        var arData = { 'servico': ob, 'metadata': new Array()};

        $('.containerMetadata > .row').each(function(indice, valor){
            var metanome = $(valor).find('input[name*="metanome_metadata"]').val();
            var valor    = $(valor).find('input[name*="valor_metadata"]').val();
            var ob       = { 'metanome' : metanome, 'valor': valor};

            if(metanome.length)
                arData.metadata.push(ob);
        });

        $('.notMetadata').each(function(index, value){
            ob = { 'metanome': $(value).attr('data-metadata'), 'valor': $(value).val() }
            if(ob.valor.length && ob.valor != 'null')
                arData.metadata.push(ob);
        });

        return arData;
    };

</script>
