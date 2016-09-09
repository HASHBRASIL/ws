<?php $servicos = getServicos( $dbh, null, PDO::FETCH_OBJ ); ?>
<div class="modal fade" id="modalAtencao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <!-- formulario -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-action="atualizar">Atualizar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"   >Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="row-wrapper">
    <div class="page-header">
        <h1><?= $SERVICO['nome']; ?></h1>
        <span><?= $SERVICO['descricao']; ?></span>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="tableServico" class="table table-condensed table-striped">
                    <thead>
                        <td>ID</td>
                        <td>DType</td>
                        <td>Descrição</td>
                        <td>Fluxo</td>
                        <td>Metanome</td>
                        <td>Nome</td>
                        <td>id_grupo</td>
                        <td>Grupo Pai</td>
                        <td>TIB</td>
                        <td>Visivel</td>
                        <td>Ações</td>
                    </thead>
                    <?php
                        foreach ( $servicos as $servico ) {
                            $editar  = '<button class="btn btn-xs btn-warning editar" type="button" data-id='.$servico->id.'><i class="fa fa-pencil-square-o"></i></button>';
                            $deletar = '<button class="btn btn-xs btn-danger deletar" type="button" data-id='.$servico->id.'><i class="fa fa-trash-o"></i></button>';
                            echo "<tr>";
                                echo "<td>$servico->id</td>";
                                echo "<td>$servico->dtype</td>";
                                echo "<td>$servico->descricao</td>";
                                echo "<td>$servico->fluxo</td>";
                                echo "<td>$servico->metanome</td>";
                                echo "<td>$servico->nome</td>";
                                echo "<td>$servico->id_grupo</td>";
                                echo "<td>$servico->id_pai</td>";
                                echo "<td>$servico->id_tib</td>";
                                echo "<td>$servico->visivel</td>";
                                echo "<td class='btnAcoes'>$editar $deletar</td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
</script>
