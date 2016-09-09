<?php
    require_once "connect.php";
    require_once "includes/encoding.php";

    $master = (isset($_GET['idMaster'])) ? $_GET['idMaster'] : $SERVICO['id_tib'];

    if (isset($_GET['id'])) {
        $idItem = $_GET['id'];
    } else if (isset($_GET['metadata'])){
        $idItem = getIdItembibliotecaByMetanome('ws_ibmetadata', $_GET['metadata'], $dbh);
    } else {
        $idItem = getIdItembibliotecaByMetanome('ws_ibmetadata', $SERVICO['metadata']['ws_ibmetadata'], $dbh);
    }

    $fluxo = ( empty( $_GET['fluxo'] ) ) ? -1 : $_GET['fluxo'];

    // $itemQuery = $dbh->prepare(
    //     "SELECT
    //         ib.id_ib_pai, ib.id AS id_ib, ib.valor, tib.id AS id_tib, tib.nome, tib.tipo,vis.valor AS visivel, ordem.valor AS ordem, ordemLista.valor AS ordemlista, tib.descricao
    //     FROM tb_itembiblioteca ib
    //     JOIN tp_itembiblioteca tib ON (ib.id_tib = tib.id)
    //     LEFT OUTER JOIN
    //         ( SELECT id_tib,valor, id_tib_pai FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_visivel' AND tp_itembiblioteca_metadata.id_tib_pai = :master ) vis ON ( tib.id = vis.id_tib )
    //     LEFT OUTER JOIN
    //         ( SELECT id_tib,valor, id_tib_pai FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordemLista' AND tp_itembiblioteca_metadata.id_tib_pai = :master ) ordemLista ON ( tib.id = ordemLista.id_tib )
    //     LEFT OUTER JOIN
    //         ( SELECT id_tib,valor, id_tib_pai FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordem' AND tp_itembiblioteca_metadata.id_tib_pai = :master ) ordem ON ( tib.id = ordem.id_tib )
    //     WHERE ib.id_ib_pai = :idItem
    //     ORDER BY ordem;");

    $itemQuery = $dbh->prepare(
        "with recursive gettib as (
            SELECT id,id_tib_pai from tp_itembiblioteca where id = (select id_tib from tb_itembiblioteca where id = :idItem)
            union
            select tp.id, tp.id_tib_pai from tp_itembiblioteca tp, gettib where tp.id_tib_pai = gettib.id
        ) select 
        ib.id_ib_pai, ib.id AS id_ib, ib.valor, tib.id AS id_tib, tib.nome, tib.tipo,vis.valor AS visivel, ordem.valor AS ordem, ordemLista.valor AS ordemlista, tib.descricao
        from
        (select * from tp_itembiblioteca where id in (select id from gettib where id_tib_pai is not null)) tib 
        left outer join (select id,id_ib_pai,id_tib,valor from tb_itembiblioteca where id_ib_pai = :idItem) ib on (tib.id = ib.id_tib)
        left outer join (select id_tib,valor, id_tib_pai FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_visivel' AND tp_itembiblioteca_metadata.id_tib in (select id from gettib where id_tib_pai is not null)) vis on (tib.id = vis.id_tib)
        left outer join (SELECT id_tib,valor, id_tib_pai FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordemLista' AND tp_itembiblioteca_metadata.id_tib in (select id from gettib where id_tib_pai is not null) ) ordemLista ON ( tib.id = ordemLista.id_tib )
        left outer join (SELECT id_tib,valor, id_tib_pai FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordem' AND tp_itembiblioteca_metadata.id_tib in (select id from gettib where id_tib_pai is not null) ) ordem ON ( tib.id = ordem.id_tib)
        order by ordem.valor");

    $itemQuery->bindParam(':idItem', $idItem);
    //$itemQuery->bindParam(':master', $master);
    try {
        $itemQuery->execute();
        $data = $itemQuery->fetchAll(PDO::FETCH_ASSOC);
        // echo "<pre>";
        // var_dump($data);
        // die();
    } catch (PDOException $e) {
        var_dump($e);
    }
?>

<div class="row wrapper">
    <div class="page-header">
        <h1><?php echo $SERVICO['nome']; ?></h1>
        <span><?php echo $SERVICO['descricao']; ?></span>
    </div>
    <div class="col-md-12 content">
<!--         <div class="form-header">
            <h1>Título do Formuláro <span>Breve descriçao do formulário</span></h1>
        </div>
 -->        <form action="includes/updateDataMaster.php" id="formUpdate" method="POST" accept-charset="utf-8">
            <?php
            foreach ($data as $key => $value) {
                if (!empty($value['visivel'])) {
                    echo '<div class="form-group" >';
                    echo '<label>' . $value['nome'] . '</label>';
                    echo createFormElement($value['tipo'], $value['valor'], $value['descricao'], ['id' => $value['nome'] . '_editavel', 'class' => 'disabled form-control ckeditor', 'name' => $value['id_tib']]);
                    echo '</div>';
                }
            }
            ?>
            <input type="hidden" name="idData" value="<?php echo $idItem; ?>" />
            <input type="hidden" name="idMaster" value="<?php echo $master; ?>" />
            <div class="row">
                <button type="submit" id="submitUpdateMaster" class="btn btn-sm btn-info pull-right margin-left">Salvar</button>
                <button type="submit" id="duplicateMaster" class="btn btn-sm btn-success pull-right">Duplicar</button>
            </div>
        </form>
        <div id="mensagens" class="alert alert-success hidden" role="alert" style="margin-top: 55px"></div>

    </div>
</div>


<script>
    fluxo = '<?php echo ucfirst( $fluxo ) ?>';
    if( fluxo != -1 ){
        $("#submitUpdateMaster").text( fluxo );
    }

    $("#formUpdate button[type=submit]").click(function (e) {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $("#formUpdate").submit(function (e) {
        e.preventDefault();

        var valBtn = $("button[type=submit][clicked=true]").attr('id');
        if ( valBtn == 'duplicateMaster' ) {
            $("#formUpdate").attr('action', 'includes/duplicateDataMaster.php');
            var tipoMSG = 'duplicar';
        }

        var data = $("#formUpdate").serializeArray();

        // console.log( data );
        // Percorre todos os textareas do form
        $.each($(this).find( "textarea" ), function (i, v) {
            // atribui a variavel editor a instancia do ckeditor pega pelo id do textarea
            // onde v é todo o objeto textarea
            editor = CKEDITOR.instances[$(v).attr( 'id' )].getData();
            // percorre todo o array arData
            $.each(data, function (indice, value) {
                // e pergunta se o name do arData é o mesmo do textarea
                if ( value['name'] == $(v).attr( 'name' ) ) {
                    // caso seja, no indice que bateu de o name do arData e o name do textarea são iguas
                    // ele atribui o valor retornado pela variavel editor.
                    data[indice]['value'] = editor;
                }

            });

        });

        $.ajax({
            url: $( "#formUpdate" ).attr( 'action' ),
            type: 'POST',
            data: data,
        }).done(function (response) {
            console.log(response);
            if ( tipoMSG === 'duplicar' ) {
                mostraMSG( '#mensagens', 3, true );
            } else {
                mostraMSG( '#mensagens', 2, true );
            }
        });

    });

    function showDiv() {
        var div_view = document.getElementById( 'view' );
        document.getElementById( 'edit' ).style.display = "block";
        div_view.setAttribute( 'class', 'col-md-6' );
    }
</script>
