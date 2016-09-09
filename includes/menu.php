<?php
/*
     * Descrição: - É necessário recuperar Entidades                                  | tb_entidade | rl_tb_entidade    |rl_perfil_entidade
     *            - Listar grupos                                                     | tb_grupo    | rl_grupo_pessoa
     *            - Add filhos menu                                                   | tb_itembiblioteca
     *            - Associar conteudo menu                                            | tp_itembiblioteca
     *            - Buscar cada area especifica para descobrir o campo ($sql)         | tp_itembiblioteca
     * Função:    "Retorna dados do banco identificar entidade e grupos"
     * Tabelas:   "| tp_entidade| rl_tb_entidade|rl_perfil_entidade|tb_grupo|rl_grupo_pessoa"
     *
     */

    $listQuery = $dbh->query( "SELECT * FROM tb_site WHERE id_grupo IN ( SELECT id FROM tb_grupo WHERE metanome = 'SITE' );" );
    $entity = $listQuery->fetchAll();

?>

<style>
#editmenutopo {display:none;}
#selectmeutipo{display:none;}
</style>

<div class="row wrapper">
    <div class="page-header">
        <h1><?php echo $nome; ?></h1>
        <span><?php echo $descricao; ?></span>
        <?php include 'includes/rastro.php' ?>
    </div>
    <div class="col-md-12 content">

        <div class="row">
            <div class="form-header">
                <h1>Master TIB <span>Selecione a tib desejada</span></h1>
            </div>
            <div class="form-group">
                <label for="">Selecione o site</label>
                <select name="selectEntity" id="selectEntity" class="col-md-3">
                    <option value=""></option>
                    <?php foreach($entity as $item): ?>
                    <option value="<?php echo $item['id_grupo']; ?>"><?php echo $item['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row wrapper wrapper-white">
    <div class="page-header">
        <h1>Gestão de Menu</h1>
        <span>Texto descritivo da sessão da página</span>
    </div>
    <div class="col-md-12 content">
        <div id="listMenu"></div>
    </div>
</div>

<script>
    $("#selectmenu").change(function(){
        if($('#selectmenu').val() == 'lateral'){
            $('#editmenulateral').show('slow');
        }else if($('#selectmenu').val() == 'topo'){
            $('#editmenutopo').show('slow');
        }
    }).chosen();
</script>
