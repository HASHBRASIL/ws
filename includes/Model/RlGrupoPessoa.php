<?php

/**
 * Created by PhpStorm.
 * User: jamessom
 * Date: 11/12/15
 * Time: 19:50
 */
class RlGrupoPessoa extends Base
{
    /**
     * @var string
     */
    protected $table = "rl_grupo_pessoa";

    /**
     * @param $valor
     * @param int $param
     * @return array
     */
    function getNomeHashByValor( $valor, $param = PDO::FETCH_ASSOC )
    {
        $stm = $this->dbh->prepare( "SELECT * FROM {$this->table} WHERE nomehash = :nomehash" );
        $stm->bindParam( ':nomehash', $valor );
        $stm->execute();
        $nomeHash = $stm->fetchAll( $param );
        return $nomeHash;
    }

    /**
     * @param $idUser
     * @param $valor
     * @param int $param
     * @return array|bool
     */
    function getNomeHashByValorAndIDUser( $idUser, $valor, $param = PDO::FETCH_ASSOC )
    {
        $stm = $this->dbh->prepare( "SELECT * FROM {$this->table} WHERE nomehash = :nomehash AND id_pessoa = :idUser" );
        $stm->bindParam( ':idUser', $idUser );
        $stm->bindParam( ':nomehash', $valor );
        $stm->execute();
        $nomeHash = $stm->fetchAll( $param );
        return ( empty( $nomeHash ) ) ? false : $nomeHash;
    }

    /**
     * @param $idGrupo
     * @param int $param
     * @return array
     */
    function getNomeHashByIDGrupo( $idGrupo, $param = PDO::FETCH_ASSOC )
    {
        $stmt = $this->dbh->prepare(
            "SELECT
                RLGP.id, RLGP.id_grupo, RLGP.id_pessoa, RLGP.nomehash, TBG.id idGrupo
            FROM
                {$this->table}
            INNER JOIN
                tb_grupo TBG
                ON
                RLGP.id_grupo = TBG.id
            WHERE
                TBG.id = :idGrupo"
        );
        $stmt->bindParam( ':idGrupo', $idGrupo );
        $stmt->execute();
        $nomeHash = $stmt->fetchAll( $param );
        return $nomeHash;
    }


    /**
     * @param $id
     * @param $idGrupo
     * @param $idPessoa
     * @param $nomeHahs
     * @return bool|string
     */
    function criaRLGrupoPesso( $id, $idGrupo, $idPessoa, $nomeHahs)
    {
        $stmt = $this->dbh->prepare("INSERT INTO {$this->table}( id, id_grupo, id_pessoa, nomehash, permissao, dt_inicio ) VALUES( :id, :id_grupo, :id_pessoa, :nomehash, 'X', ( SELECT CURRENT_TIMESTAMP ) )");

        try {
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':id_grupo', $idGrupo);
            $stmt->bindParam(':id_pessoa', $idPessoa);
            $stmt->bindParam(':nomehash', $nomeHahs);
            $stmt->execute();
            $retorno = true;

        } catch (PDOException $e) {
            $retorno = $e->getMessage();
        }

        return $retorno;
    }
}