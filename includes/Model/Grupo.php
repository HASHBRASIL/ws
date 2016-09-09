<?php
/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 27/11/15
 * Time: 17:58
 */

class Grupo extends Base
{

    /**
     * @var string
     */
    protected $table = "tb_grupo";

    /**
     * @param $idGrupo
     * @return array
     */
    function getFilho($idGrupo)
    {
        $stmt = $this->dbh->prepare(
                "with recursive gf AS (
                    select id from tb_grupo where id = :id_grupo
                UNION
                    select grupo.id from tb_grupo grupo join gf on grupo.id_pai = gf.id
                )
                select id from gf"

        );

        $stmt->bindValue("id_grupo", $idGrupo);

        $stmt->execute();
        $rsFilho = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $rsFilho;
    }

    /**
     * @param $idPai
     * @param int $param
     */
    function getGruposByIDPai( $idPai, $param = PDO::FETCH_ASSOC )
    {
        $stmt = $this->dbh->prepare( "SELECT * FROM {$this->table} WHERE id_pai = :idPai ORDER BY nome ASC" );
        $stmt->bindParam( ':idPai', $idPai );
        $stmt->execute();
        $gruposByIDPai = $stmt->fetchAll( $param );
        //var_dump( $gruposByIDPai );
        return ( empty( $gruposByIDPai ) ) ? false : $gruposByIDPai;
    }


    /**
     * @param $idUsuario
     * @param int $param
     * @return array
     */
    function getProdutosByIDUser( $idUsuario, $param = PDO::FETCH_ASSOC )
    {
        $stmt = $this->dbh->prepare(
            "WITH RECURSIVE getGEM AS (
                SELECT * FROM {$this->table} WHERE id IN ( SELECT id_grupo FROM rl_grupo_pessoa WHERE id_pessoa = :idUsuario )
            UNION
                SELECT g.* FROM {$this->table} g JOIN getGEM gg ON ( gg.id_pai = g.id )
            ) SELECT * FROM getGEM WHERE id_representacao IS NOT NULL ORDER BY nome"
        );
        $stmt->bindParam( ':idUsuario', $idUsuario );
        $stmt->execute();
        $entidades = $stmt->fetchAll( $param );
        return $entidades;
    }

    /**
     * @param $idPai
     * @param $metanome
     * @param int $param
     */
    function getGruposByIDPaiByMetanome( $idPai, $metanome, $param = PDO::FETCH_ASSOC )
    {   
        $stmt = $this->dbh->prepare( "with recursive getgrupos as (select * from {$this->table} where id = :idPai union select g.* from tb_grupo g join getgrupos gg on (g.id_pai = gg.id)) select * from getgrupos where metanome = :metanome" );
        $stmt->bindParam( ':idPai', $idPai );
        $stmt->bindParam( ':metanome', $metanome );
        $stmt->execute();
        
        $gruposByIDPaiByMetanome = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ( empty( $gruposByIDPaiByMetanome ) ) ? false : $gruposByIDPaiByMetanome;
    }

    /**
     * @param $id
     * @param $nome
     * @param $id_criador
     * @param $descricao
     * @return bool|string
     */
    function createGrupo( $id, $nome, $id_criador, $descricao )
    {
        $stmt = $this->dbh->prepare("INSERT INTO {$this->table}( id, nome, id_criador, descricao, dt_inclusao ) VALUES( :id, :nome, :id_criador, :descricao, ( SELECT CURRENT_TIMESTAMP ) )");
        try {
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':id_criador', $id_criador);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->execute();
            $retorno = true;

        } catch (PDOException $e) {
            $retorno = $e->getMessage();
        }
        return $retorno;
    }

    function getTimeByID( $idTime, $param = PDO::FETCH_ASSOC )
    {
        $stmt = $this->dbh->prepare( "SELECT * FROM {$this->table} WHERE id = :id AND id_representacao IS NOT NULL" );
        $stmt->bindParam( ':id', $idTime );
        $stmt->execute();
        $times = $stmt->fetch( $param );
        return ( empty($times) )?false:$times;
    }

    function getTimeByNome( $nome, $param = PDO::FETCH_ASSOC )
    {
        $stmt = $this->dbh->prepare( "SELECT * FROM {$this->table} WHERE nome = :nome AND id_representacao IS NOT NULL" );
        $stmt->bindParam( ':nome', $nome );
        $stmt->execute();
        $times = $stmt->fetch( $param );
        return ( empty($times) )?false:$times;
    }

    function updateTime( $id, $nome, $descricao )
    {
        $stmt = $this->dbh->prepare( "UPDATE {$this->table} SET nome = :nome, descricao = :descricao WHERE id = :id" );
        $stmt->bindParam( ':id', $id);
        $stmt->bindParam( ':nome', $nome);
        $stmt->bindParam( ':descricao', $descricao );
        $updatetime = $stmt->execute();
        return $updatetime;
    }

    function getGrupoByMetanome( $metanome )
    {
    	$stmt = $this->dbh->prepare( "SELECT * FROM {$this->table} WHERE metanome = :meta" );
    	$stmt->bindParam( ':meta', $metanome );
    	$stmt->execute();

    	$rs =  $stmt->fetchAll(PDO::FETCH_ASSOC);
    	
    	return $rs[0];
    }

}