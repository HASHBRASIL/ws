  <?php

  $Sitequery =$dbh->query("SELECT id, id_grupo, nome FROM tb_site");
  $Site = $Sitequery->fetchAll();

  $querylistbox =$dbh->query("SELECT * FROM cms_tpbox");
  $Listbox = $querylistbox->fetchAll();

  $querylistcomp =$dbh->query("SELECT * FROM cms_comportamento");
  $Listcomp = $querylistcomp->fetchAll();


  require_once 'includes/connect.php';

  $queryCount = $dbh->prepare("select count(ib.id) as qtd, tib.nome, tib.id from
    tb_itembiblioteca ib right outer join
    (select id,nome from tp_itembiblioteca where tipo = 'Master') tib on (ib.id_tib = tib.id)
    group by tib.nome, tib.id
    having count(ib.id) > 0
    order by qtd desc");
  $queryCount->execute();
  $countResult = $queryCount->fetchAll();


  ?>


  <style type="text/css">

  .grid {background: #004756;border:0;margin: 0;}
  /* #gridster > .row { width: 910px;}*/
  .gridster > section > ul   { margin: 0; padding: 0; }
  .gridster > section > ul > li { background: #ADD8E6; list-style: none; }
  #overlayForm {display:none;}
  #loadmore {display:none;}

  </style>


  <div id="overlayForm">
    <div class="box">

        <div class="panel panel-default">
            <div class="panel-heading"><h1><?php echo $nome; ?></h1></div>
            <div class="panel-body">
                <select name="masters" id="mastersgrid" class="col-md-12">
                    <option value=""></option>
                    <?php foreach($countResult as $item): ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo  $item['nome'] . " | " . $item['qtd']; ?></option>
                <?php endforeach; ?>
            </select>

            <div class="page-header" id="desc_master" >
                <h1><?php echo $descricao; ?> <small></small></h1>
            </div>
            <!-- <a href="index.php?pagina=includes/createDataMaster" class="btn btn-default pull-right" style="display: none;" id="novoItem">Novo</a> -->
            <div id="list_master" style="margin-top: 80px"></div>
            <div id="list_master" style="margin-top: 80px">
                <table class='table table-hover' id='tableListMaster'>
                    <thead></thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn btn-default pull-right" id="loadmore">Loadmore</button>
            </div>
        </div>
    </div>
  </div>
</div>


<div class="col-md-12">
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading"><h1>Lista de Templates</h1></div>
            <div class="panel-body">

                <div class="page-header" id="entity" >
                    <h1>Site <small></small></h1>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Selecione o Site</label>
                        <select id="Site" class="form-control">
                            <option value="" selected></option>
                            <?php
                            foreach($Site as $key => $value){
                                echo "<option value='". $value['id_grupo'] . '_' . $value['id'] . "'>" . $value['nome'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" id="containerSelect">
                    </div>
                </div>
                <div class="list-group col-md-6">
                    <button type="button" class="list-group-item" id="TLP_1">Template 1</button>
                    <button type="button" class="list-group-item" id="TLP_2">Template 2</button>
                    <button type="button" class="list-group-item" id="TLP_3">Template 3</button>
                    <button type="button" class="list-group-item" id="TLP_4">Template 4</button>
                    <button type="button" class="list-group-item" id="TLP_5">Template 5</button>
                    <button type="button" class="list-group-item" id="tp_select">Monte seu Template </button>


                    <select class="selectnewbox" id="selectbox" style="display: none">
                        <?php
                        foreach($Listbox as $item){
                            echo '<option value="' . $item['altura'] . '_' .$item['largura'] . '" data-idbox="'. $item['id'] .'"> '. $item['nome'] .'</option>';
                        }

                        ?>
                    </select>
                </div>


                <div class="gridster col-md-10">
                    <section class="grid">
                        <ul>
                        </ul>
                    </section>
                </div>

            </div>
            <button type="button" class="btn btn-default" id="lock">Lock Grid</button>
            <button type="button" class="btn btn-default" id="unlock">Unlock Grid</button>
            <button type="submit" class="btn btn-default pull-right" id="saveLayout">Salvar Layout</button>
        </div>
    </div>
</div>




<script type="text/javascript">



var gridster = $(".gridster ul").gridster({
    autogenerate_stylesheet: false,
    widget_margins: [15, 15],
    widget_base_dimensions: [ 300 , 120],
    max_col:10,
    min_col:3
}).data('gridster');
gridster.generate_stylesheet({rows: 50, cols: 10});



var serialization = [
                        [{col: 5,row: 1,size_x: 1,size_y: 3},//TPL1
                        {col: 4,row: 1,size_x: 1,size_y: 2},
                        {col: 1,row: 1,size_x: 2,size_y: 2},
                        {col: 1,row: 3,size_x: 2,size_y: 1},
                        {col: 3,row: 1,size_x: 1,size_y: 3},
                        {col: 4,row: 6,size_x: 2,size_y: 1},
                        {col: 4,row: 3,size_x: 1,size_y: 3},
                        {col: 3,row: 4,size_x: 1,size_y: 2},
                        {col: 1,row: 6,size_x: 3,size_y: 1},
                        {col: 5,row: 4,size_x: 1,size_y: 2},
                        {col: 1,row: 4,size_x: 2,size_y: 1},
                        {col: 1,row: 5,size_x: 2,size_y: 1}],

                        [{col: 1,row: 1,size_x: 2,size_y: 1},//TPL2
                        {col: 1, row: 4,size_x: 2,size_y: 3},
                        {col: 3, row: 1,size_x: 1,size_y: 2},
                        {col: 3, row: 7,size_x: 3,size_y: 1},
                        {col: 3, row: 3,size_x: 1,size_y: 2},
                        {col: 4, row: 1,size_x: 2,size_y: 3},
                        {col: 1, row: 2,size_x: 1,size_y: 2},
                        {col: 3, row: 5,size_x: 1,size_y: 2},
                        {col: 2, row: 2,size_x: 1,size_y: 2},
                        {col: 4, row: 4,size_x: 1,size_y: 3},
                        {col: 1, row: 7,size_x: 2,size_y: 1},
                        {col: 5, row: 4,size_x: 1,size_y: 3}],

                        [{col: 3,row: 1,size_x: 1,size_y: 3},//TPL3
                        {col: 1,row: 4,size_x: 2,size_y: 2},
                        {col: 1,row: 1,size_x: 2,size_y: 3},
                        {col: 3,row: 4,size_x: 1,size_y: 3},
                        {col: 4,row: 1,size_x: 1,size_y: 2},
                        {col: 4,row: 3,size_x: 2,size_y: 3},
                        {col: 3,row: 7,size_x: 3,size_y: 1},
                        {col: 1,row: 6,size_x: 2,size_y: 1},
                        {col: 1,row: 7,size_x: 2,size_y: 1},
                        {col: 4,row: 6,size_x: 2,size_y: 1},
                        {col: 5,row: 1,size_x: 1,size_y: 2}],

                        [{col: 1,row: 4, size_x: 3,size_y: 1},//TPL4
                        {col: 3,row: 1, size_x: 1,size_y: 3},
                        {col: 2,row: 1, size_x: 1,size_y: 3},
                        {col: 1,row: 1, size_x: 1,size_y: 3},
                        {col: 4,row: 1, size_x: 1,size_y: 2},
                        {col: 4,row: 3, size_x: 1,size_y: 2},
                        {col: 1,row: 5, size_x: 1,size_y: 2},
                        {col: 2,row: 6, size_x: 2,size_y: 1},
                        {col: 2,row: 5, size_x: 2,size_y: 1},
                        {col: 5,row: 1, size_x: 1,size_y: 3},
                        {col: 4,row: 5, size_x: 1,size_y: 2},
                        {col: 5,row: 4, size_x: 1,size_y: 3}],




                        [{col: 1,row: 2,size_x: 2,size_y: 1},//TPL6
                        {col: 1,row: 1,size_x: 3,size_y: 1},
                        {col: 3,row: 2,size_x: 1,size_y: 3},
                        {col: 4,row: 1,size_x: 2,size_y: 2},
                        {col: 1,row: 3,size_x: 2,size_y: 3},
                        {col: 3,row: 5,size_x: 3,size_y: 1},
                        {col: 4,row: 3,size_x: 1,size_y: 2},
                        {col: 3,row: 7,size_x: 3,size_y: 1},
                        {col: 5,row: 3,size_x: 1,size_y: 2},
                        {col: 3,row: 6,size_x: 3,size_y: 1},
                        {col: 1,row: 6,size_x: 1,size_y: 2},
                        {col: 2,row: 6,size_x: 1,size_y: 2}]



                        ];


                        $('button[id*="TLP_"]').click(function(){
                            id = $(this).attr('id').split("_")[1];
                            gridster.remove_all_widgets();
                            $.each(serialization[id-1], function(key, value) {
                                var strTools = '<div class="containerTools"><button class="btn btn-default removeBox"><span class="glyphicon glyphicon-trash"></span></button></br>';
                                strTools    += '<input type="text" id="param" /></br><select id="selectcomp" class="form-control"><option value="" selected></option>';
                                strTools    += '<?php foreach($Listcomp as $key => $value){ echo "<option value=". $value["id"] . ">" . $value["nome"] . "</option>";}?>'
                                strTools    += '</select>';
                                //ao escolher um template premade o id_tpbox vem setado default mudar isso somehow (problema do paulo do futuro)
                                gridster.add_widget('<li />',value.size_x, value.size_y, value.col,value.row).attr('data-ordem', key+1).attr('data-id',$('#selectbox > option:selected').attr('data-idbox')).html(strTools);
                            });
                            $("[id$='selectcomp']").chosen();
                            $('.removeBox').click(removeBox);


                        });


                        $('#tp_select').one( "click", function(){
                            gridster.remove_all_widgets();
                            $('.selectnewbox').show("slow").chosen({width: "500px"});
                        });

    //lock grid
    $('#lock').click(function() {
        gridster.disable()
        $('.containerTools').hide();
    });
    //unlock grid
    $('#unlock').click(function() {
        gridster.enable()
        $('.containerTools').show();
    });


    function removeBox() {
        gridster.remove_widget($(this).parent().parent())
    };

    //cria um unico box
    $('.selectnewbox').change(function(){
        var strTools = '<div class="containerTools"><button class="btn btn-default removeBox"><span class="glyphicon glyphicon-trash"></span></button></br>';
        strTools    += '<input type="text" id="param" /></br><select id="selectcomp" class="form-control"><option value="" selected></option>';
        strTools    += '<?php foreach($Listcomp as $key => $value){ echo "<option value=". $value["id"] . ">" . $value["nome"] . "</option>";}?>';
        strTools    += '</select>';
        //strTools  += '<button type="button" class="btn btn-default pinEdit" id="pin">Pin Conteudo</button>'; CRIA BOTAO PARA PINAR NOTICIAS !!!!!!!!!!DESCOMENTAR!!!!!!

        var largura      = parseInt($('.selectnewbox').val().split("_")[0]);
        var altura       = parseInt($('.selectnewbox').val().split("_")[1]);

        //attr('data-ordem', $('.gridster > section > ul').children().length)


        gridster.add_widget('<li />', altura, largura, 1, 8).attr('data-id',$('#selectbox > option:selected').attr('data-idbox')).html(strTools);
        $('.removeBox').click(removeBox);
        $("[id$='selectcomp']").chosen();
            // LEMBRAR DE CONTINUAR ESSA PARTE DE PIN DE CONTEUDO(pegar uma noticia e associar o ib_id a uma box no campo param)!!!!!!!DESCOMENTAR!!!!!!!!!!!
            // $('.pinEdit').click(function(){
            // $.fn.popup.defaults.transition = 'all 0.8s';
            // $('#overlayForm').popup('show');
            // $('#mastersgrid').chosen({width:'100%'});


            // });

});


  $("#Site").chosen({width: "500px"});
  $("#Site").change(function(e){
    var id = $(this).children('option:selected').val().split("_")[0];
    getGrupo(id);

  });

  function getGrupo(id_grupo){

    $.ajax({
        type: "POST",
        url: 'includes/ajaxGetgrupo.php',
        data: {id: id_grupo},
        success: function(retorno){

            var rtn    = JSON.parse(retorno);
            var div    = $(document.createElement('div')).addClass('form-group');
            var select = $(document.createElement('select')).addClass('form-control selectGrupo').attr('id','Grupo');
            var option = $(document.createElement('option')).attr('value','').attr('selected','selected').attr('disabled','disabled');
            var label  = $(document.createElement('label')).html('Selecione Grupo');
            select.append(option);
            $.each(rtn, function(ind, vle){
                var opt = $(document.createElement('option'));

                opt.html(vle['nome']).attr('value', vle['id']);
                select.append(opt);

            });

            div.append(label);
            div.append(select);

            $('#containerSelect').html(div);
            select.chosen().change(function(e){

                id_grupoarea = $(this).children('option:selected').val();
                getGrupoarea(id_grupoarea);

            });
        }
    });
  }



  function getGrupoarea(id_grupoarea){

    $.ajax({
        type: "POST",
        url: 'includes/ajaxGetgrupo.php',
        data: {id_grupoarea: id_grupoarea},
        success: function(retorno){

            var rtn    = JSON.parse(retorno);
            var div    = $(document.createElement('div')).addClass('form-group').addClass('Grupoarea');
            var select = $(document.createElement('select')).addClass('form-control selectGrupoarea').attr('id','Grupoarea');
            var option = $(document.createElement('option')).attr('value','').attr('selected','selected').attr('disabled','disabled');
            var label  = $(document.createElement('label')).html('Seleciona o Grupo area');
            select.append(option);
            $.each(rtn, function(ind, vle){
                var opt = $(document.createElement('option'));

                opt.html(vle['nome']).attr('value', vle['id']);
                select.append(opt);

            });

            div.append(label);
            div.append(select);

            $('#containerSelect > div.Grupoarea').remove();
                $('#containerSelect').append(div);
                select.chosen();


        }
    });
  }

  $('#saveLayout').click (function(){
    serialize = new Array();
    $.each($('.gridster > section > ul > li'),function(i ,v){

            serialize.push({
                'coluna'    : $(v).attr('data-col'),
                'linha'     : $(v).attr('data-row'),
                'ordem'     : i+1,
                'param'     : $(v).find('input[id="param"]').val(),
                'id_tpbox'  : $(v).attr('data-id')});

        })
             //console.log(serialize)
            saveTp($('#Site').children('option:selected').val().split("_")[1], $('#Grupo').children('option:selected').val(), $('#Grupoarea').children('option:selected').val(),serialize);


        });

  function saveTp (id_site, id_grupo, id_grupoarea,serialize){

    $.ajax({
        type: "POST",
        url: 'includes/Insertbox.php',
        data: {'id_site': id_site, 'id_grupo': id_grupo, 'id_grupoarea':id_grupoarea, data: serialize},
        success: function(retorno){

            console.log(retorno)

        }
    });
  }

</script>
