<div id="main" class="container_fluid">
    <div class="row contentColumn">
        <div id="col-01">
            <div id="hash_workspace">hash workspace</div>
            <?php include "includes/aside_usuario.php"; ?>
            <?php include "includes/modulos.php"; ?>
        </div>
        <div id="sideModules">
            <ul></ul>
        </div>
        <div id="col-02" class="col-md-12">
            <div id="boxes-container">
                <div class="container-fluid">
                    <div class="row super-search-bar">
                        <div class="input-group col-md-12">
                            <select name="" id="super-search-bar" class="chosen" multiple='multiple'>
                                <option value="">Dashboard</option>
                                <option value="">Wordspace</option>
                                <option value="">Lembretes</option>
                                <option value="">Eventos</option>
                                <option value="">Arquivos</option>
                                <option value="">Download</option>
                                <option value="">Notícias</option>
                                <option value="">Itens Biblioteca</option>
                                <option value="">Imagens</option>
                            </select>
                        </div>
                    </div>
                    <div id="dashboard-tools">
                        <ul class="nav nav-pills">
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>WorkSpace</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Tarefas</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Lembretes</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Memo</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Eventos</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Arquivos</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Download</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
                            <li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
                        </ul>
                    </div>
                    <?php
                        $flashMsg = new flashMsg();
                        echo $twig->render('flashmsg.html.twig');
                        $flashMsg->clear();
                    ?>

                    <?php
                        require_once "includes/rastro.php";
                        if(!isset($HASH_SERVICO['tab'])):
                            $SERVICO = $HASH_SERVICO;
                            include 'includes/'. $SERVICO['metadata']['ws_arquivo'];
                        else:
                    ?>
                        <div id="container_tabs">
                            <ul class="nav nav-tabs" role="tablist" id="mainTabs">
                                <?php   foreach($HASH_SERVICO['filhos'] as $key => $value): ?>
                                    <?php if(isset($value['metadata']['ws_comportamento']) && $value['metadata']['ws_comportamento'] == 'tab'): ?>
                                        <li role="presentation" class="" data-tab="aba_<?=$key?>"><a href="#aba_<?=$key?>" aria-controls="aba_<?=$key?>" role="tab" data-toggle="tab"><?=$value['nome']?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                            <div class="tab-content" id="mainContainerTabs">
                                <?php foreach($HASH_SERVICO['filhos'] as $key => $value): ?>
                                    <?php  $SERVICO = $value; ?>
                                    <?php if(isset($value['metadata']['ws_comportamento']) && $value['metadata']['ws_comportamento'] == 'tab'): ?>
                                        <div role="tabpanel" class="tab-pane" id="aba_<?=$key?>">
                                            <?php include 'includes/'. $SERVICO['metadata']['ws_arquivo']; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    if($("#mainTabs > li").length){
        var tab   = GetQueryStringParams('tab');
        if (tab) {
            $("#mainTabs > li[data-tab='aba_"+ tab +"']").addClass('active');
            $("#mainContainerTabs > div[id='aba_"+ tab +"']").addClass('active');
        } else {
            $("#mainTabs > li:first").addClass('active');
            $("#mainContainerTabs > div:first").addClass('active');
        }
    }
    $('#mainTabs > li > a').click(function (e) {
        e.preventDefault();
        console.log('teste');
        $(this).tab('show');
    });
</script>
