<?php
require_once "connect.php";
require_once "functions.php";

$master = $_POST['id_master'];
$inputType = array('text', 'textarea', 'date', 'list', 'imagem');


$query = $dbh->prepare(
    "SELECT tib.id, tib.descricao, tib.metanome, tib.nome, tib.tipo,vis.valor AS visivel, ordemLista.valor AS ordemLista, CAST(coalesce(ordem.valor, '-1') AS integer)  AS ordem
    FROM tp_itembiblioteca tib
    LEFT OUTER JOIN ( SELECT id_tib, valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_visivel' AND tp_itembiblioteca_metadata.id_tib_pai = :master ) vis ON ( tib.id = vis.id_tib )
    LEFT OUTER JOIN ( SELECT id_tib, valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordemLista' AND tp_itembiblioteca_metadata.id_tib_pai = :master ) ordemLista ON ( tib.id = ordemLista.id_tib )
    LEFT OUTER JOIN ( SELECT id_tib, valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordem' AND tp_itembiblioteca_metadata.id_tib_pai = :master ) ordem ON ( tib.id = ordem.id_tib )
    WHERE tib.id_tib_pai = :master
    ORDER BY ordem ASC;"
);

$query->bindParam(':master', $master);

if ($query->execute()) {
    $tib = $query->fetchAll();
}
?>
<form action="includes/updateTemplate.php" method="POST" accept-charset="utf-8" class="form-horizontal" id="form-tib">
    <?php
    for ($i = 0; $i < count($tib); $i++):
        $tipo = $tib[$i]['tipo'];
        ?>
        <div class="form-group form-inline row" data-ordem="<?php echo $i; ?>" id="<?php echo $tib[$i]['id']; ?>" data-id="rowItem_<?php echo $i; ?>">
            <input type="text" placeholder="Nome" name="<?php echo $tib[$i]['id']; ?>_nome" class="form-control" value="<?php echo $tib[$i]['nome']; ?>" />

            <div class="input-group">
                <input type="text" class="form-control" placeholder="Descrição" name="<?php echo $tib[$i]['id']; ?>_descricao" data-id="<?php echo $tib[$i]['id']; ?>" value="<?php echo $tib[$i]['descricao']; ?>">
                <input type="text" class="form-control" placeholder="Ordem da Lista" name="<?php echo $tib[$i]['id']; ?>_lista" value="<?php echo $tib[$i]['ordemlista']; ?>" />
                <div class="input-group-btn">


                    <div class="btn btn-default btn-icon-change">
                        <i class="fa <?php echo ($tib[$i]['visivel']) ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                    </div>
                    <div class="btn btn-default btn-icon-change moveUp">
                        <i class="fa fa-chevron-up"></i>
                    </div>
                    <div class="btn btn-default btn-icon-change moveDown">
                        <i class="fa fa-chevron-down"></i>
                    </div>
                    <div class="btn btn-default btn-icon-change remove">
                        <i class="fa fa-trash-o"></i>
                    </div>

                    <select name="<?php echo $tib[$i]['id']; ?>_tipo" data-id="<?php echo $tib[$i]['id']; ?>" class="form-control selectTipo">
                        <option value="">Tipo Campo</option>
                        <?php
                        foreach ($inputType as $item) {
                            if ($item == $tib[$i]['tipo']) {
                                echo '<option value="' . $item . '" selected="selected">' . strToUpper($item) . '</option>';
                            } else {
                                echo '<option value="' . $item . '">' . strToUpper($item) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <input type="hidden" data-id="<?php echo $tib[$i]['id']; ?>" value="<?php echo $tib[$i]['ordemlista']; ?>" name="<?php echo $tib[$i]['id']; ?>_wslista">
            <input type="hidden" data-id="<?php echo $tib[$i]['id']; ?>" value="<?php echo $tib[$i]['ordem']; ?>" name="<?php echo $tib[$i]['id']; ?>_wsordem">
            <input type="hidden" data-id="<?php echo $tib[$i]['id']; ?>" value="<?php echo $tib[$i]['visivel']; ?>" name="<?php echo $tib[$i]['id']; ?>_wsvisivel">
            <input type="hidden" data-id="<?php echo $tib[$i]['id']; ?>" value="<?php echo (int) $tib[$i]['visivel']; ?>" name="<?php echo $tib[$i]['id']; ?>_visivel">
            <input type="hidden" data-id="<?php echo $tib[$i]['id']; ?>" value="<?php echo $i; ?>" name="<?php echo $tib[$i]['id']; ?>_ordem">
            <input type="hidden" data-id="<?php echo $tib[$i]['id']; ?>" value="false" name="<?php echo $tib[$i]['id']; ?>_new">
        </div>
        <?php
    endfor;
    ?>
    <input type="hidden" name="help_deleted" id="deleted" value="" />
    <input type="hidden" name="help_idMaster" id="idMaster" value="<?php echo $master; ?>" />
</form>
<button id="createClone" class="btn btn-default pull-left">Novo campo</button>
<button type="submit" id="updateTemplate" form="form-tib" class="btn btn-default pull-right">Salvar</button>

<script>

    $("#form-tib").submit(function (e) {
        e.preventDefault();

        data = $('#form-tib').serializeArray();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: data
        }).done(function (response) {
            // console.log(response);
            if (response == 'error') {
                // alert('Erro ao salvar!');
                mostraMSG("#mensagem", 4, false);
            } else {
                // console.log(response);
                mostraMSG("#mensagem", 1, false);

            }
            $('#selectTib').trigger('change');
        });
    });

    function cloneObj(uuid) {
        var newObj = $('#form-tib').find('div[data-id="rowItem_0"]').clone();
        var lastId = $("#form-tib >div:last").attr("data-id").split('_')[1];

        newObj.attr('data-id', 'rowItem_' + (++lastId)).attr('id', uuid).attr('data-new', 'true');

        newObj.find('input[name*="_nome"]').val('').attr('name', uuid + '_nome');
        newObj.find('input[name*="_descricao"]').val('').attr('name', uuid + '_descricao');
        newObj.find('input[name*="_visivel"]').val('1').attr('name', uuid + '_visivel');
        newObj.find('input[name*="_lista"]').val('').attr('name', uuid + '_lista');
        newObj.find('input[name*="_metadata"]').val('').attr('name', uuid + '_metadata');
        newObj.find('input[name*="_new"]').val('true').attr('name', uuid + '_new');
        newObj.find('input[name*="_ordem"]').attr('name', uuid + '_ordem').val(lastId);
        newObj.find('input[name*="_wslista"]').attr('name', uuid + '_wslista').val('');
        newObj.find('input[name*="_wsordem"]').attr('name', uuid + '_wsordem').val('');
        newObj.find('input[name*="_wsvisivel"]').attr('name', uuid + '_wsvisivel').val('');
        newObj.find('select[name*="_tipo"]').attr('name', uuid + '_tipo');


        if (newObj.find('div.input-group-btn > div.btn-icon-change > i.fa-eye-slash').length)
            newObj.find('div.input-group-btn > div.btn-icon-change > i.fa-eye-slash').removeClass('fa-eye-slash').addClass('fa-eye');


        $("#form-tib >div:last").after(newObj);
        $("#form-tib >div:last").find('input[name*="_ordem"]').val(lastId);
        $("#form-tib >div:last").find('.btn-icon-change').click(btnForms);
    }

    $("#createClone").click(function (e) {
        $.ajax({
            url: 'includes/geraUuid.php',
            type: 'POST'
        }).done(function (response) {
            cloneObj(response);
        });
    });

    $('.btn-icon-change').click(btnForms);

    function btnForms(e) {
        if ($(this).find('i').hasClass('fa-eye')) {

            $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
            $(this).parent().parent().parent().find("input[name*='_visivel']").val('0');

        } else if ($(this).find('i').hasClass('fa-eye-slash')) {

            $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            $(this).parent().parent().parent().find("input[name*='_visivel']").val('1');

        }

        if ($(this).hasClass('moveUp')) {
            moveObjs('moveUp', $(this));
        } else if ($(this).hasClass('moveDown')) {
            moveObjs('moveDown', $(this));
        }


        if ($(this).hasClass('remove')) {

            if ($(this).parent().parent().parent().parent().find("div[data-id*='rowItem_']").length > 1) {

                if ($(this).parent().parent().parent().find("input[type='hidden'][name*='_new']").val() == 'false') {
                    if ($('#deleted').val().length > 0) {
                        var deleteA = new Array($('#deleted').val());
                    } else {
                        var deleteA = new Array();
                    }

                    deleteA.push($(this).parent().parent().parent().attr('id'));
                    $("#deleted").val(deleteA.toString());
                }
                $(this).parent().parent().parent().remove();

                $('[data-id*="rowItem_"]').each(function (i, v) {
                    $(v).attr('data-id', 'rowItem_' + i);
                });
            }
        }
    }

    function moveObjs(direction, objeto) {
        var obj = objeto.parent().parent().parent();

        if (direction == "moveUp")
            var objPrev = obj.prev();
        else
            var objPrev = obj.next();

        if (objPrev.length) {
            var id = obj.attr('data-id');
            obj.attr('data-id', objPrev.attr('data-id'));
            objPrev.attr('data-id', id);
            obj.find('input[name*="_ordem"]').val(obj.attr('data-id').split("_")[1]);
            objPrev.find('input[name*="_ordem"]').val(objPrev.attr('data-id').split("_")[1]);
            if (direction == 'moveUp')
                obj.insertBefore(objPrev);
            else
                obj.insertAfter(objPrev);
        }
    }

</script>






























