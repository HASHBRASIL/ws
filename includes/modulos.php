<?php
    require_once 'connect.php';
    require_once 'functions.php';


    $idServico = (isset($_GET['servico']) )? $_GET['servico']: getIdServico('hash_dashboard', $dbh)[0]['id'];
    $idUsuario = $_SESSION['USUARIO']['ID'];

    $queryEntidades = $dbh->prepare("WITH RECURSIVE getGEM AS (
                                    SELECT * FROM tb_grupo WHERE id IN ( SELECT id_grupo FROM rl_grupo_pessoa WHERE id_pessoa = :idUsuario )
                                    UNION
                                    SELECT g.* FROM tb_grupo g JOIN getGEM gg ON ( gg.id_pai = g.id )
                                    ) SELECT * FROM getGEM WHERE id_representacao IS NOT NULL ORDER BY nome");


    $queryModules = $dbh->prepare("SELECT sv.*, svm.valor as icone
                                   FROM tb_servico AS sv
                                   LEFT OUTER JOIN (SELECT * FROM tb_servico_metadata WHERE metanome = 'ws_icone') AS svm
                                   ON ( sv.id = svm.id_servico )
                                   WHERE sv.id_pai IS NULL");

    $queryGetServiceFather = $dbh->prepare("WITH RECURSIVE tb_pai AS
                                            (
                                                SELECT id, descricao, nome, id_pai FROM tb_servico where id = :idServico
                                            UNION
                                                SELECT sv.id, sv.descricao, sv.nome, sv.id_pai FROM tb_servico sv JOIN tb_pai pai ON ( sv.id = pai.id_pai )
                                            ) SELECT * FROM tb_pai where id_pai IS NULL");

    $queryGetServiceFather->bindParam('idServico', $idServico);
    $queryGetServiceFather->execute();
    $getServicoFather = $queryGetServiceFather->fetchAll(PDO::FETCH_ASSOC);

    $queryModules->execute();
    $modules = $queryModules->fetchAll(PDO::FETCH_ASSOC);

    $queryEntidades->bindParam(':idUsuario', $idUsuario);
    $queryEntidades->execute();
    $entidades = $queryEntidades->fetchAll(PDO:: FETCH_ASSOC);
?>
<div id="entity">
    <div id="selectedEntity">
        <span class="titleSquare"><?php echo $entidades[0]['nome']; ?></span>
        <i class="fa fa-angle-right"></i>
    </div>
    <ul id="listaEntidades">
        <?php foreach($entidades as $key => $value): ?>
        <li><a href="#" data-entidade="<?php echo $value['id']; ?>" ><?php echo $value['nome']; ?></a></li>
        <?php endforeach; ?>
    </ul>
    <button id="btnModules">
        <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
    </button>
    <div id="modulesPopup">
        <span></span>
        <ul>
            <?php foreach($modules as $key => $value): ?>
                <li data-toggle="tooltip" data-placement="right" title="<?php echo $value['nome']; ?>" data-id="<?php echo $value['id'];?>">
                    <i class="fa <?php echo $value['icone']; ?>"></i>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<div id="profile">
    <div class="titleModule">Título Menu</div>
    <ul id="navPrincipal">
        <li><a href="home.php?servico=bf5908fe-b8d8-420a-b353-25bc6da174e4"><i class="fa fa-cubes"></i>Teste Gestão de pessoa</a>
        </li>
    </ul>
</div>
<div class="subMenu sub-with-1-column container">
    <div class="row">
        <div class="col-md-12 headerSubMenu">
            <h1>Texto de Explicação do SubMenu</h1>
        </div>
        <div class="containerSubMenu">
            <div class="row">
                <div class="col-md-12">

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var arServicos = new Array();
    var arModules  = <?php echo json_encode($modules); ?>;
    var servico    = <?php echo json_encode($getServicoFather[0]['id']); ?>;
    var subMenu    = $('.subMenu').clone();
    $('.subMenu').remove();

    callMenuModules( servico );

    $(document).ready(function(){
        $.each(arModules, function(i, v){
            var li = $(document.createElement('li')).attr('data-toggle', 'tooltip').attr('data-placement', 'right').attr('title', v['nome']).attr('data-id', v['id']);
            var i  = $(document.createElement('i')).addClass('fa').addClass(v['icone']);
            li.append(i);
            $('#sideModules > ul').append(li);
            li.click(function(e){
                callMenuModules( v['id'] );
            });
        });
    });

    $(document).mouseup(function (e){
        var arObjects = new Array('#listaEntidades', '#modulesPopup');
        $.each(arObjects, function(i, v){
            var container = $(v);

            if (!container.is(e.target) && container.has(e.target).length === 0){
                container.hide();
            }
        });
    });

    $('#listaEntidades > li > a').click(function(e){
        e.preventDefault();
        $('#listaEntidades').hide();
        $('#entity').find('.titleSquare').text($(this).text());
        $.ajax({
            url: "includes/ajaxMudaTime.php",
            type: "POST",
            data: { "entidade": $( this ).attr( 'data-entidade' )  },
            success: function ( res ) {
                // console.log( res );
            }

        });

    });

    $('#selectedEntity').click(function(event){
        $('#listaEntidades').show();
    }).css('cursor', 'pointer');

    $('#modulesPopup').hide().find(' ul > li').click(function(){
        var check = false;
        clone = $(this).clone();
        var id = $(this).attr('data-id');

        $('#sideModules > ul > li').each(function(i, v){
            if($(this).attr('data-id') == clone.attr('data-id'))
                check = true;
        });
        callMenuModules( id );

        $(this).parent().parent().hide();
    });

    function callMenuModules(id){
        $.ajax({
            url: 'includes/ajaxShowMenuModules.php',
            type: 'POST',
            data: {idModulo: id },
            success: function(result){
                $('#navPrincipal').html('');
                arServicos = JSON.parse(result);
                $('#profile > div.titleModule').text( arServicos['modulo']['nome'] );
                $.each(arServicos[id], function (index, value) {
                    if (value['raiz'] == 'FILHO') {
                        geraMenuPrincipal(value, $('#navPrincipal'));
                    }
                });
            }
        });
    }

    $('#btnModules').click(function(e){
        e.preventDefault();
        $("#modulesPopup").show();
    });

    function clickMenu(event) {
        pai = $(this).parent();
        if (pai.children('.submenu').is(':visible')) {
            pai.children('.submenu').slideUp(200);
            pai.find('i.arrowMenu').addClass('fa-caret-left').removeClass('fa-caret-down');
        } else {
            pai.children('.submenu').slideDown(200);
            pai.find('i.arrowMenu').removeClass('fa-caret-left').addClass('fa-caret-down');
        }
    }

    function geraMenuPrincipal(value, olContainer){
        var li              = $(document.createElement('li'));
        var a               = $(document.createElement('a'));
        var span            = $(document.createElement('span')).text(value['nome']);
        var iconePrimario   = $(document.createElement('i')).addClass('fa fa-cubes');
        var iconeSecundario = $(document.createElement('i')).addClass('fa fa-caret-right arrowMenu');

        a.append(span);
        li.append(a);
        olContainer.append(li);

        if(olContainer.attr('id') == 'navPrincipal')
            a.prepend(iconePrimario);



        if (value['tem_filho']) {

            a.append(iconeSecundario);
            var clone     = subMenu.clone();
            var container = clone.find('.containerSubMenu > div.row > div.col-md-12');
            var ul        = $(document.createElement('ul'));
            clone.hide();
            container.append(ul);
            li.append(clone);

            li.mouseover(function(){
                if((li.offset().top + clone.height()) > $(window).height() ){
                    clone.css('top', -1*(clone.height() - li.height()) + 'px');
                }
                li.find('a').addClass('hover');
                clone.show();
            }).mouseout(function(){
                clone.hide();
                li.find('a').removeClass('hover');
            });

            var contador = 0;
            var coluna   = 1;

            $.each(arServicos[value['id']], function (ind, val) {
                if(contador >= 15){
                    coluna++;
                    clone.removeClass('sub-with-'+(coluna-1)+'-column').addClass('sub-with-'+coluna+'-column');
                    clone.find('.containerSubMenu > .row > div[class^="col-md"]').attr('class', '').addClass('col-md-' + (12/coluna));
                    container  = $(document.createElement('div')).addClass('col-md-' + (12/coluna));
                    ul.parent().parent().append(container);
                    ul         = $(document.createElement('ul'));
                    container.append(ul);

                    contador = 0;
                }
                contador = geraMenuBalao(val, ul, contador, true);
            });

        }

        if ( value['aba'] == 1) {
            a.attr('href', "home.php?servico=" + value['id_pai'] + "&tab=" + value['id']);
        } else if ( ( value['arquivo'] != "" ) && ( value['arquivo'] != null ) ) {
            a.attr('href', "home.php?servico=" + value['id']);
        } else if ( ( value['id_tib'] != "" ) && ( value['id_tib'] != null ) ) {
            a.attr('href', "home.php?servico=" + value['id'] + "&master=" + value['id_tib'] + "&fluxo=" + value['fluxo']);
        } else if( value['tab'] > 0 ) {
            a.attr('href', "home.php?servico=" + value['id']);
        }
    }

    function geraMenuBalao(value, olContainer, count, first){
        var li   = $(document.createElement('li'));
        var a    = $(document.createElement('a'));
        var span = $(document.createElement('span')).text(value['nome']);
        var i    = $(document.createElement('i')).addClass('fa fa-angle-right');

        if( ( ( value['arquivo'] == "" ) || ( value['arquivo'] == null ) ) &&
            ( value['aba'] == 0 ) &&
            ( ( value['id_tib'] == "" ) || ( value['id_tib'] == null ) ) &&
            ( !value['tab'] ) ) {

            span.addClass('first-sub');
            li.append(span);
            olContainer.append(li);
        }else{
            if(first || value['tab']){
                li.append(a);
                a.append(span);
                span.addClass('first-sub');
                li.append(a);
                olContainer.append(li);
            }else{
                li.append(a);
                a.append(i).append(span);
                olContainer.append(li);
            }
        }

        count ++;
        if (value['tem_filho']) {
            var ul = $(document.createElement('ul'));
            li.append(ul);

            $.each(arServicos[value['id']], function (ind, val) {
                count = geraMenuBalao(val, ul, count, false);
            });

        }
        if( value['tab'] > 0 ){
            a.attr('href', "home.php?servico=" + value['id']);
        }else if ( value['aba'] == 1) {
            a.attr('href', "home.php?servico=" + value['id_pai'] + "&tab=" + value['id']);
        } else if ( ( value['arquivo'] != "" ) && ( value['arquivo'] != null ) ) {
            a.attr('href', "home.php?servico=" + value['id']);
        } else if ( ( value['id_tib'] != "" ) && ( value['id_tib'] != null ) ) {
            a.attr('href', "home.php?servico=" + value['id'] + "&master=" + value['id_tib'] + "&fluxo=" + value['fluxo']);
        }

        return count;
    }

    $("ul.submenu > li > a > i.fa.fa-cubes").remove();
    $('[data-toggle="tooltip"]').tooltip();
</script>
