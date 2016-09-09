<?php
require_once "connect.php";


$queryCount = $dbh->prepare(
	"SELECT
        count(ib.id) AS qtd, tib.nome, tib.id
    FROM
        tb_itembiblioteca ib RIGHT OUTER JOIN
    (
        SELECT id,nome FROM tp_itembiblioteca WHERE tipo = 'Master') tib ON (ib.id_tib = tib.id
    )
    GROUP BY tib.nome, tib.id
    ORDER BY qtd DESC
");

# Seleciona os itens da tabela cms_tptemplate para popular o select
$selectTodosCMSTPTemplate = $dbh->prepare("SELECT * FROM cms_tptemplate ORDER BY nome");
$selectTodosCMSTPTemplate->execute();

$queryCount->execute();
$countResult = $queryCount->fetchAll();
?>


<div class="row wrapper">
    <div class="page-header">
        <h1><?php echo $nome; ?></h1>
        <span><?php echo $descricao; ?></span>
        <?php include "includes/rastro.php"; ?>
    </div>
    <div class="col-md-12 content">
        <div class="form-header">
            <h1>Selecione Tib <span>Breve descriçao do formulário</span></h1>
        </div>

        <div class="form-group">
            <label for="master">Selecione uma Tib</label>
            <select name="masters" id="selectTib" class="chosen-select" data-list="includes/listTemplate.php">
                <option value=""></option>
                <?php foreach ($countResult as $item): ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['nome'] . " | " . $item['qtd']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>


        <div class="form-header">
            <h1>Campos Tib <span>Breve descriçao do formulário</span></h1>
        </div>

        <div id="listTemplate"></div>
    </div>
</div>

<div class="row wrapper wrapper-white">
    <div class="page-header">
        <h1>Inclusão de HTML</h1>
        <span>Texto descritivo da sessão da página</span>
    </div>
    <div class="col-md-12 content" >
        <form id="cmsTemplate" method="post" action="includes/updateCMSTPTemplate.php">
            <div class="form-group">
                <label for="selectDadosTemplate">Template</label>
                <select id="selectDadosTemplate" class="form-control" name="selectDadosTemplate">
                    <option value="n">Selecione algo</option>
                    <?php
                        $string = "<option value='%s'>%s</option>";
                        while ($resSelectTodosCMSTPTemplate = $selectTodosCMSTPTemplate->fetch(PDO::FETCH_ASSOC)) {
                            # Popula o select com o resultado da busca na tabela cms_tptemplate
                            printf($string, $resSelectTodosCMSTPTemplate['id'], $resSelectTodosCMSTPTemplate['nome']);
                        }
                    ?>
                </select>
            </div>

            <div class="row">
                <div id="mostrarDados">
                    <div id="editorDados">
                        <pre id="myAceEditor"></pre>
                    </div>
                </div>
            </div>

            <div class="row">
                <input class="btn btn-success btn-sm pull-right" id="btnAcao" type="submit" value="Salvar" name="salvar">
            </div>
            <div id="mensagens" class="alert alert-success hidden" role="alert" style="margin-top: 40px"></div>
        </form>
    </div>
</div>

<style type="text/css">
    #myAceEditor {
        margin: 0;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 500px;
    }

</style>

<script src="https://cloud9ide.github.io/emmet-core/emmet.js"></script>
<script type="text/javascript">

    var editor = ace.edit("myAceEditor");
    editor.session.setMode("ace/mode/html");
    editor.setTheme("ace/theme/monokai");
    // Habilita o emmet para o editor
    editor.setOption("enableEmmet", true);
    // Desabilita mensagem no js:
    // Automatically scrolling cursor into view after selection change this will be disabled in the next version set editor.$blockScrolling = Infinity to disable this message
    editor.$blockScrolling = Infinity;

    // Desabilita o botão de salvar/atualizar
    $('#btnAcao').prop('disabled', true);
    // Esconde o editor
    $("#mostrarDados").hide();

    // Ao mudar de estado do selectTib, onde esta o varlor do idTIB
    $('#selectTib').change(function () {
        // Faz requisição ajax para
        // pega o idTIB
        callAjax({id_master: $(this).children('option:selected').val()}, '#listTemplate', 'includes/listTemplate.php');
        // Retorna o id da TIB
        var selectTib = $("#selectTib").chosen().val();

        // Esconde o editor
        $("#mostrarDados").hide();

        // Seleciona o valor padrão do select e atualiza o chosen
        $("#selectDadosTemplate").find('option[value="n"]').attr('selected', true);
        $('#selectDadosTemplate').trigger("chosen:updated");

        // Ao mudar a tib esconde as mensagens que eventualmente estejam aparecendo.

        $( "#mensagens" ).fadeOut();

	});

	$( "#selectDadosTemplate" ).chosen().change(function(){

		var idcmstptemplate = $( "#selectDadosTemplate" ).chosen().val();
		var dadosDoACEEditor = editor.getValue();

		// Habilita botão
		$('#btnAcao').prop('disabled', false);
		// Mostra editor
		$("#mostrarDados").show();
		// Esconde as mensagem que eventualmente estejam aparecendo ao se mudar o tipo de template.
		$( "#mensagens" ).fadeOut( "slow" );

		// Se o dado do template for nulo ele esconde o editor.
		if(idcmstptemplate == 'n'){
			$("#mostrarDados").hide();
		}

		$.ajax({
	        url: 'includes/updateCMSTPTemplate.php',
	        type: 'POST',
	        data: { idTib: $( "#selectTib" ).chosen().val(), idCMSTPTemplate:  idcmstptemplate, dadosEditor:dadosDoACEEditor, busca: 'vai' },
	    }).done(function( response ) {

	    	dados = JSON.parse( response );
	    	// console.log( dados );

	    	if( dados.dadoParaEditor == null ){
	    		editor.setValue( '' );
	    		$( '#btnAcao' ).val( "Salvar" );
	    		$( '#cmsTemplate' ).attr( 'action', 'includes/insertCMSTPTemplate.php' );
	    	}
	    	else{
				editor.setValue( dados.dadoParaEditor );
	    	 	$( '#btnAcao' ).val( "Atualizar" );
	    	 	$( '#cmsTemplate' ).attr( 'action', 'includes/updateCMSTPTemplate.php' );
	    	}

	    });

	});

	// Ajax para insert e update
	$( "#cmsTemplate" ).submit(function(e){
		e.preventDefault();

		var selectDadosTemplate = $( "#selectDadosTemplate" ).val();
		var dadosDoACEEditor    = editor.getValue();
		var selectTib           = $( "#selectTib" ).chosen().val();

		// Remove barra e ponto para fazer a verificação se é update ou select
		var myFormAttr = $( '#cmsTemplate' ).attr( 'action' );
		var removeBarra = myFormAttr.split("/");
		var removePonto = removeBarra[1].split(".");

		// console.log( removePonto[0] );

		// Update
		if( removePonto[0] == "updateCMSTPTemplate" ){

			$.ajax( {
		        url: $( this ).attr( 'action' ),
		        type: 'POST',
		        data: { idTib: selectTib, dadosDoSelect: selectDadosTemplate, dadosDoEditor: dadosDoACEEditor, update: 'vai', idcmstemplate: dados.idcmstemplate },
		    } ).done( function( response ) {
		    	if( response == 'error' ){
		    	 	// alert( 'Erro ao atualizar!\n' );
		    	 	// $('#mensagens').removeClass('hidden').removeClass('alert-success').addClass('alert-danger').show();
    	 			// $("#mensagens").text( "Ocorreu algum erro, entre em contato com o suporte." );
    	 			mostraMSG('#mensagens', 4, false);
		    	}else{
		    	 	// console.log( response );
		    	 	// Mostra a mensagem com os dados retornados
		    	 	// $('#mensagens').removeClass('hidden').show();
		    	 	// $("#mensagens").text( response );
		    	 	mostraMSG('#mensagens', 2, true);
		    	}
		    });

		}

		// Insert
		if( removePonto[0] == "insertCMSTPTemplate" ){
			// console.log( removePonto[0] );

			// console.log("idtib: "+selectTib+"\n");
			// console.log("idtemmplate: "+selectDadosTemplate+"\n");
			// console.log("dadosEditor: "+dadosDoACEEditor+"\n");

			// console.log($(this).attr('action'))

			$.ajax({
		        url: $(this).attr('action'),
		        type: 'POST',
		        data: { idTib: selectTib, dadoSelect: selectDadosTemplate, dadosEditor:dadosDoACEEditor },
		    }).done(function(response) {
		    	if( response == 'error' ){
		    		console.log("a");
		    	 	// alert( 'Erro ao salvar!\n' );
		    	 	$('#mensagens').removeClass('hidden').removeClass('alert-success').addClass('alert-danger').show();
    	 			$("#mensagens").text( "Ocorreu algum erro, entre em contato com o suporte." );
    	 			// mostraMSG('#mensagens', 4, false);

		    	}else{
		    	 	console.log( response );
		    	 	// Mostra a mensagem com os dados retornados
		    	 	$('#mensagens').removeClass('hidden').show();
		    	 	$("#mensagens").text( response );
		    	 	// mostraMSG('#mensagens', 1, true);
		    	}
		    });

		}
	});



        $("#mensagens").fadeOut();





</script>
