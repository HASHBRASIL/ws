<?php
session_start();
require_once 'databaseconnect.php';
require_once 'funcoes.php';
require_once 'classes/UUID.php';

$aliasForm = ( isset( $_POST['alias'] ) ) ? preg_replace("/[^a-zA-Z0-9]/", "", $_POST['alias']) : false;
$nomeForm  = ( isset( $_POST['nome'] ) )  ? strtolower($_POST['nome'])  : false;

$alias = ( verificaAlias( $dbh, $aliasForm ) == 0 ) ? $aliasForm : false;
$nome  = ( verificaUsuario($dbh, $nomeForm) == 0 ) ? $nomeForm : false;
$time  = ( isset( $_POST['time'] ) )  ? $_POST['time']  : false;
$email = ( isset( $_POST['email'] ) ) ? $_POST['email'] : false;

if( $alias && $nome )
{
    //1. Procurar um registro em tb_grupo com o campo metanome = 'HASH' e armazenar o ID em memória
    $idHash = resgataNomeHash($dbh, PDO::FETCH_OBJ)->id;
    //2. Procurar um registro em tp_informacao com o campo metanome = 'EMAIL' e armazenar o ID em memória
    $idTpEmail = resgataIdTpEmail($dbh, PDO::FETCH_OBJ)->id;

    try {

        $dbh->beginTransaction();

        // 3. Criar um registro em 'tb_pessoa' com a pessoa referente ao usuário contendo:
        $idPessoa = UUID::v4();
        $nomePessoa = $nome;
        $pessoa = criaPessoa($dbh, $idPessoa, $nomePessoa);
        # var_dump($pessoa);

        // 4. Criar um registro em tb_usuario contendo:
        $idUsuario = $idPessoa;
        $nomeUsuario = strtolower($nome);
        $usuario = criaUsuario($dbh, $idUsuario, $nomeUsuario);
        # var_dump($usuario);

        // 5. Criar um Registro em tb_grupo para o grupo #pessoal (usuario) do usuário cadastrado contendo:
        $idGrupoPessoal = UUID::v4();
        $idCriadorGrupoPessoal = $idUsuario;
        $nomeGrupoPessoal = "@" . strtolower($nomeUsuario);
        $idPaiGrupoPessoal = $idHash;
        $grupoPessoal = criaGrupoHashPessoal( $dbh, $idGrupoPessoal, $nomeGrupoPessoal, $idCriadorGrupoPessoal, $idPaiGrupoPessoal );
        # var_dump( $grupoPessoal );

        // 6. Criar um registro em rl_grupo_pessoa para a relação entre o usuário e seu grupo pessoal contendo:
        $idRLGrupoHashPessoal = UUID::v4();
        $idGrupoHahsPessoal = $idGrupoPessoal;
        $id_Pessoa = $idPessoa;
        $nomeHash = "@".$nomeUsuario;
        $RLGrupoHahsPessoal = criaRLGrupoHashPessoal($dbh, $idRLGrupoHashPessoal, $idGrupoHahsPessoal, $id_Pessoa, $nomeHash);
        # var_dump( $RLGrupoHahsPessoal );

        //7. Criar um registro em tb_informacao com o registro do email a ser cadastrado contendo:
        $idEmailInformacao = UUID::v4();
        $idPessoaInformacao = $idPessoa;
        $idTinfo = $idTpEmail;
        $idCriadorInformacao = $idPessoa;
        $valorInformacao = $email;
        $criaEmailInformacao = criaInformacaoEmail( $dbh, $idEmailInformacao, $idPessoaInformacao, $idTinfo, $idCriadorInformacao, $valorInformacao );
        # var_dump( $criaEmailInformacao );

        // 8. Criar um registro em rl_grupo_informacao relacionando a posse da informação do email da pessoa a seu grupo pessoal contendo:
        $idRLGrupoInformacao = UUID::v4();
        $idPessoaRLGrupoInformacao = $idPessoa;
        $idInfoRLGrupoInformacao = $idEmailInformacao;
        $idGrupoRLGrupoInformacao = $idGrupoPessoal;
        $rlGrupoInformacao = criaRLGrupoInformacao($dbh, $idRLGrupoInformacao, $idPessoaRLGrupoInformacao, $idInfoRLGrupoInformacao, $idGrupoRLGrupoInformacao);
        # var_dump( $rlGrupoInformacao );

        // 9. Criar um Registro em tb_pessoa para o time cadastrado contendo:
        $idPessoaTime = UUID::v4();
        $nomePessoaTime = $time;
        $criaPessoaRepresentante = criaPessoaTime( $dbh, $idPessoaTime, $nomePessoaTime );
        # var_dump( $criaPessoaRepresentante );

        //10. Criar um Registro em tb_grupo para o time cadastrado contendo:
        $idGrupoTime = UUID::v4();
        $nomeGrupoTime = $time;
        $idCridorGrupoTime = $idUsuario;
        $idRepresentacaoGrupoTime = $idPessoaTime;
        $idPaiGrupoTime = $idHash;
        $criaTime = criaTime($dbh, $idGrupoTime, $nomeGrupoTime, $idCridorGrupoTime, $idRepresentacaoGrupoTime, $idPaiGrupoTime);
        # var_dump( $criaTime );

        //11. Criar um registro em tb_grupo_metadata com o alias do time (para o apontamento na home.php de acordo com o dominio informado) contendo:
        $idTimeAlias = UUID::v4();
        $idGrupoTimeAlias = $idGrupoTime;
        $metanomeTimeAlias = 'ws_alias';
        $valorTimeAlias = $alias;
        $timeAlias = criaTimeAlias( $dbh, $idTimeAlias, $idGrupoTimeAlias, $metanomeTimeAlias, $valorTimeAlias );
        # var_dump( $timeAlias );

        //12. Criar um Registro em tb_grupo para o grupo #geral do time cadastrado:
        $idGrupoGeral = UUID::v4();
        $nomeGrupoGeral = 'Geral';
        $idCriadorGrupoGeral = $idUsuario;
        $idPaiGrupoGeral = $idGrupoTime;
        $GrupoGeral = criaGrupoGeral( $dbh, $idGrupoGeral, $nomeGrupoGeral, $idCriadorGrupoGeral, $idPaiGrupoGeral );
        # var_dump( $GrupoGeral );

        //13. Criar um registro em rl_grupo_pessoa para a relação entre o usuário e seu time contendo:
        $idRLGPUsuarioTime = UUID::v4();
        $idGrupoRLGPUsuarioTime = $idGrupoTime;
        $idPessoaRLGPUsuarioTime = $idPessoa;
        $RLGPUsuarioTime = criaRLUsuarioTime( $dbh, $idRLGPUsuarioTime, $idGrupoRLGPUsuarioTime, $idPessoaRLGPUsuarioTime );
        # var_dump( $RLGPUsuarioTime );

        //14. Criar um registro em rl_grupo_pessoa para a relação entre o usuário e o grupo geral:
        $idRLGPUsuarioGG = UUID::v4();
        $idGrupoRLGPUsuarioGG = $idGrupoGeral;
        $idPessoaRLGPUsuarioGG = $idPessoa;
        $nomehashRLGPUsuarioGG = '#geral';
        $RLGPUsuarioGG = criaRLUsuarioGrupoGeral( $dbh, $idRLGPUsuarioGG, $idGrupoRLGPUsuarioGG, $idPessoaRLGPUsuarioGG, $nomehashRLGPUsuarioGG );
        # var_dump( $RLGPUsuarioGG );

        $dbh->commit();
        $_SESSION['time']['id'] = $idGrupoTime;
        echo 1;
    }
    catch (PDOException $e)
    {
        echo 0;
        //$dbh->rollBack();
    }
}
else
{
    echo 10;
}
