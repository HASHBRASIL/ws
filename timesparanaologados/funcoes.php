<?php
include_once 'databaseconnect.php';


/**
 * @param $connection Variável de conexão PDO
 * @param int $param Objeto de retorno PDO, default PDO::FETCH_ASSOC
 * @return int Retorna ID da informação e-mail ou zero em caso de erro
 */
function resgataIdTpEmail($connection, $param = PDO::FETCH_ASSOC)
{
    $query = $connection->query("SELECT id FROM tp_informacao WHERE metanome = 'EMAIL'");
    $query->execute();
    $email = $query->fetch($param);
    return (empty($email)) ? 0 : $email;
}

/**
 * @param $connection Variável de conexão PDO
 * @param int $param Objeto de retorno PDO, default PDO::FETCH_ASSOC
 * @return int Retorna ID do grupo HASH ou zero em caso de erro
 */
function resgataNomeHash($connection, $param = PDO::FETCH_ASSOC)
{
    $query = $connection->query("SELECT id FROM tb_grupo WHERE metanome = 'HASH'");
    $query->execute();
    $hash = $query->fetch($param);
    return (empty($hash)) ? 0 : $hash;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $email Email a ser verificado
 * @param int $param Objeto de retorno PDO, default PDO::FETCH_ASSOC
 * @return array|int Caso tenha retorna um array com os ids das pessoas ou zero em caso de nunhum dado encontrado.
 */
function verificaEmailPorValor($connection, $email, $param = PDO::FETCH_ASSOC)
{
    $idTinfo = resgataIdTpEmail($connection, PDO::FETCH_OBJ)->id;
    $query = $connection->prepare("SELECT * FROM tb_informacao WHERE valor = :email AND id_tinfo = :idTinfo");
    $query->bindParam(':email', $email);
    $query->bindParam(':idTinfo', $idTinfo);
    $query->execute();
    $emailPessoa = $query->fetchAll($param);

    if (empty($emailPessoa)) {
        return 0;
    } else {
        $id = array();
        for ($i = 0; $i < count($emailPessoa); $i++) {
            array_push($id, $emailPessoa[$i]['id_pessoa']);
        }
        return $id;
    }
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param int $param Objeto de retorno PDO, default PDO::FETCH_ASSOC
 * @return int
 */
function verificaPessoaPorIdTbInformacao($connection, $id, $param = PDO::FETCH_ASSOC)
{
    if (count($id) > 1) {
        for ($i = 0; $i < count($id); $i++) {
            $query = $connection->prepare("SELECT * FROM tb_pessoa WHERE id = :id");
            $query->bindParam(':id', $id[$i]);
            $query->execute();
            $query->fetch($param);
        }
        return ($i) ? $i : 0;
    } else {
        return 0;
    }
}

/**
 * @param $connection Variável de conexão PDO
 * @param $nomeUsuario
 * @param int $param Objeto de retorno PDO, default PDO::FETCH_ASSOC
 * @return int
 */
function verificaUsuario($connection, $nomeUsuario, $param = PDO::FETCH_ASSOC)
{
    $query = $connection->prepare("SELECT * FROM tb_usuario WHERE nomeusuario = :nomeusuario");
    $query->bindParam(':nomeusuario', $nomeUsuario);
    $query->execute();
    $usuario = $query->fetchAll($param);
    return (empty($usuario)) ? 0 : $usuario;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $alias
 * @param int $param Objeto de retorno PDO, default PDO::FETCH_ASSOC
 * @return int
 */
function verificaAlias($connection, $alias, $param = PDO::FETCH_ASSOC)
{
    $query = $connection->prepare("SELECT * FROM tb_grupo_metadata WHERE metanome = 'ws_alias' AND valor = :alias");
    $query->bindParam(':alias', $alias);
    $query->execute();
    $alias = $query->fetchAll($param);
    return (empty($alias)) ? 0 : $alias;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param int $param Objeto de retorno PDO, default PDO::FETCH_ASSOC
 * @return int
 */
function verificaPessoaPorID($connection, $id, $param = PDO::FETCH_ASSOC)
{
    $query = $connection->prepare("SELECT id FROM tb_usuario WHERE :id");
    $query->bindParam(':id', $id);
    $query->execute();
    $pessoa = $query->fetch($param);
    return (empty($pessoa)) ? 0 : $pessoa;
}

/**
 * @param $connection Variável de conexão PDO
 * @param int $param Objeto de retorno PDO, default PDO::FETCH_ASSOC
 * @return int
 */
function resgatarIdEmailTpin($connection, $param = PDO::FETCH_ASSOC)
{
    $query = $connection->query("SELECT id FROM tp_informacao WHERE metanome = 'EMAIL'");
    $query->execute();
    $tpinEmail = $query->fetch($param);
    return (empty($tpinEmail)) ? 0 : $tpinEmail;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $nome
 * @return bool|string
 */
function criaPessoa($connection, $id, $nome)
{
    $query = $connection->prepare("INSERT INTO tb_pessoa( id, dt_inclusao, nome ) VALUES( :id, ( SELECT CURRENT_TIMESTAMP ), :nome )");

    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $nomeUsuario
 * @return bool|string
 */
function criaUsuario($connection, $id, $nomeUsuario)
{
    $query = $connection->prepare("INSERT INTO tb_usuario( id, completar_cadastro, nomeusuario ) VALUES( :id, TRUE, :nomeusuario )");

    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':nomeusuario', $nomeUsuario);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $nome
 * @param $idCriador
 * @param $idPai
 * @return bool|string
 */
function criaGrupoHashPessoal($connection, $id, $nome, $idCriador, $idPai)
{
    $query = $connection->prepare("INSERT INTO tb_grupo( id, nome, id_criador, id_pai, dt_inclusao ) VALUES( :id, :nome, :id_criador, :id_pai, ( SELECT CURRENT_TIMESTAMP ) )");

    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':id_criador', $idCriador);
        $query->bindParam(':id_pai', $idPai);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $idGrupo
 * @param $idPessoa
 * @param $nomeHahs
 * @return bool|string
 */
function criaRLGrupoHashPessoal($connection, $id, $idGrupo, $idPessoa, $nomeHahs)
{
    $query = $connection->prepare("INSERT INTO rl_grupo_pessoa( id, id_grupo, id_pessoa, nomehash, permissao, dt_inicio ) VALUES( :id, :id_grupo, :id_pessoa, :nomehash, 'X', ( SELECT CURRENT_TIMESTAMP ) )");

    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':id_grupo', $idGrupo);
        $query->bindParam(':id_pessoa', $idPessoa);
        $query->bindParam(':nomehash', $nomeHahs);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $idPessoa
 * @param $idTinfo
 * @param $idCriador
 * @param $valor
 * @return bool|string
 */
function criaInformacaoEmail($connection, $id, $idPessoa, $idTinfo, $idCriador, $valor)
{
    $query = $connection->prepare("INSERT INTO tb_informacao( id, id_pessoa, id_tinfo, id_criador, valor ) VALUES( :id, :id_pessoa, :id_tinfo, :id_criador, :valor )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':id_pessoa', $idPessoa);
        $query->bindParam(':id_tinfo', $idTinfo);
        $query->bindParam(':id_criador', $idCriador);
        $query->bindParam(':valor', $valor);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $idPessoa
 * @param $idInfo
 * @param $idGrupo
 * @return bool|string
 */
function criaRLGrupoInformacao($connection, $id, $idPessoa, $idInfo, $idGrupo)
{
    $query = $connection->prepare("INSERT INTO rl_grupo_informacao( id, id_pessoa, id_info, id_grupo ) VALUES( :id, :id_pessoa, :id_info, :id_grupo )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':id_pessoa', $idPessoa);
        $query->bindParam(':id_info', $idInfo);
        $query->bindParam(':id_grupo', $idGrupo);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $nome
 * @return bool|string
 */
function criaPessoaTime($connection, $id, $nome)
{
    $query = $connection->prepare("INSERT INTO tb_pessoa( id, nome, dt_inclusao ) VALUES( :id, :nome, ( SELECT CURRENT_TIMESTAMP ) )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $nome
 * @param $idCriador
 * @param $idRepresentacao
 * @param $idPai
 * @return bool|string
 */
function criaTime($connection, $id, $nome, $idCriador, $idRepresentacao, $idPai)
{
    $query = $connection->prepare("INSERT INTO tb_grupo( id, nome, id_criador, id_representacao, id_pai, dt_inclusao ) VALUES( :id, :nome, :id_criador, :id_representacao, :id_pai, ( SELECT CURRENT_TIMESTAMP ) )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':id_criador', $idCriador);
        $query->bindParam(':id_representacao', $idRepresentacao);
        $query->bindParam(':id_pai', $idPai);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $idGrupo
 * @param $metanome
 * @param $valor
 * @return bool|string
 */
function criaTimeAlias($connection, $id, $idGrupo, $metanome, $valor)
{
    $query = $connection->prepare("INSERT INTO tb_grupo_metadata( id, id_grupo, metanome, valor ) VALUES( :id, :id_grupo, :metanome, :valor )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':id_grupo', $idGrupo);
        $query->bindParam(':metanome', $metanome);
        $query->bindParam(':valor', $valor);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $nome
 * @param $idCriador
 * @param $idPai
 * @return bool|string
 */
function criaGrupoGeral($connection, $id, $nome, $idCriador, $idPai)
{
    $query = $connection->prepare("INSERT INTO tb_grupo( id, nome, id_criador, id_pai, dt_inclusao ) VALUES( :id, :nome, :id_criador, :id_pai, ( SELECT CURRENT_TIMESTAMP ) )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':id_criador', $idCriador);
        $query->bindParam(':id_pai', $idPai);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $idGrupo
 * @param $idPessoa
 * @return bool|string
 */
function criaRLUsuarioTime($connection, $id, $idGrupo, $idPessoa)
{
    $query = $connection->prepare("INSERT INTO rl_grupo_pessoa( id, id_grupo, id_pessoa, dt_inicio, permissao ) VALUES( :id, :id_grupo, :id_pessoa, ( SELECT CURRENT_TIMESTAMP ), 'X' )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':id_grupo', $idGrupo);
        $query->bindParam(':id_pessoa', $idPessoa);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $idGrupo
 * @param $idPessoa
 * @param $nomeHahs
 * @return bool|string
 */
function criaRLUsuarioGrupoGeral($connection, $id, $idGrupo, $idPessoa, $nomeHahs)
{
    $query = $connection->prepare("INSERT INTO rl_grupo_pessoa( id, id_grupo, id_pessoa, nomehash, dt_inicio, permissao ) VALUES( :id, :id_grupo, :id_pessoa, :nomehash, ( SELECT  CURRENT_TIMESTAMP ), 'X' )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':id_grupo', $idGrupo);
        $query->bindParam(':id_pessoa', $idPessoa);
        $query->bindParam(':nomehash', $nomeHahs);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @param $idGrupo
 * @param $idPessoa
 * @return bool|string
 */
function criaRLGrupoPessoa($connection, $id, $idGrupo, $idPessoa){
    $query = $connection->prepare("INSERT INTO rl_grupo_pessoa( id, id_grupo, id_pessoa ) VALUES( :id, :id_grupo, :id_pessoa )");
    try {
        $query->bindParam(':id', $id);
        $query->bindParam(':id_grupo', $idGrupo);
        $query->bindParam(':id_pessoa', $idPessoa);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }

    return $retorno;
}

/**
 * @param $connection Variável de conexão PDO
 * @param $id
 * @return bool|string
 */
function criaConvite($connection, $id){
    $query = $connection->prepare("INSERT INTO tb_convite( id, aceitogrupo ) VALUES( :id, TRUE )");
    try {
        $query->bindParam(':id', $id);
        $query->execute();
        $retorno = true;

    } catch (PDOException $e) {
        $retorno = $e->getMessage();
    }
    return $retorno;
}
