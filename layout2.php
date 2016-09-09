
<?php
    $flashMsg = new flashMsg();
    echo $twig->render('flashmsg.html.twig');
    $flashMsg->clear();
?>

<?php
    if(!$HASH_SERVICO['tab']):
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
        $(this).tab('show');
    });
</script>
