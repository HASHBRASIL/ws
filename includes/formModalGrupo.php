<form action="includes/updateGrupo.php" method="post" accept-charset="utf-8" id="updateGrupo" class="updateGrupo" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            <div class="form-header" style="margin-left:20px">
                <h1>Editar grupo <span>formulário de edição de grupo</span></h1>
            </div>

            <div class="col-md-12 content">
                <div class="form-group-one-unit">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Entidade</label>
                                <select name="lsentidades" id="lsentidades" class="form-control chosen-select">
                                    <option value="null">Escolha uma Entidade</option>
                                    <?php foreach( $entidades as $entidade): ?>
                                        <option value="<?php echo $entidade['id']; ?>"><?php echo  $entidade['nome']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Escolha um Grupo Pai</label>
                                <select name="lstgrupos" id="lstGrupo" class="form-control chosen-select">
                                    <option value="null">Escolha um Grupo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Visibilidade</label>
                                <select name="lsvisibilidade" id="lsvisibilidade" class="form-control chosen-select">
                                    <option value="null">Escolha um tipo de visibildiade</option>
                                    <option value="t"   >Público</option>
                                    <option value="f"   >Privado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control input-sm" id="nome" name="nome" placeholder="Nome do Grupo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="alias">Alias</label>
                                <input type="text" class="form-control input-sm" id="alias" name="alias" placeholder="Alias do Grupo">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descricao_grupo">Descrição</label>
                                <textarea class="form-control" id="editor" name="descricao_grupo" placeholder="Descrição do Grupo" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="userID"  value="<?php echo $SIDU; ?>">
                    <input type="hidden" name="idGrupo" value="">
                </div>
            </div>

            <div class="col-md-12 dadosRetorno"></div>
        </div>
    </div>
</form>
