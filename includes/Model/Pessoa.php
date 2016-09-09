<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 9/12/15
 * Time: 4:28
 */
class Pessoa extends Base
{
    /**
     * @var string
     */
    private $table = 'tb_pessoa';
    /**
     * @param $idPessoa
     * @return array
     */
    function getPessoaById($idPessoa)
    {
        $stmt = $this->dbh->prepare("SELECT * FROM tb_pessoa p WHERE id = :id");
        $stmt->bindValue(':id', $idPessoa);
        $stmt->execute();
        $rsPessoa = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rsPessoa;
    }


    /**
     * @param $termo
     * @param int $limit
     * @return array
     */
    function getPessoaByNome($termo, $limit = 10)
    {
        $stmt = $this->dbh->prepare("SELECT * FROM tb_pessoa p WHERE nome ilike :nome LIMIT :limit ");
        $stmt->bindValue(':nome', "%" . $termo . "%");
        $stmt->bindValue(':limit', $limit);

        $stmt->execute();
        $rsPessoa = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rsPessoa;
    }

    /**
     * @param $id
     * @param $nome
     * @return bool|string
     */
    function criaPessoa( $id, $nome)
    {
        $stmt = $this->dbh->prepare("INSERT INTO {$this->table}( id, nome, dt_inclusao ) VALUES( :id, :nome, ( SELECT CURRENT_TIMESTAMP ) )");
        try {
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->execute();
            $retorno = true;

        } catch (PDOException $e) {
            $retorno = $e->getMessage();
        }
        return $retorno;
    }
    
    function euExisto( $id ){
    	$stmt = $this->dbh->prepare("SELECT * FROM tb_pessoa WHERE id = :id");
    	$stmt->bindValue(':id', $id);
	   	$stmt->execute();
	   	
	   	
    	if( $stmt->fetchAll(PDO::FETCH_ASSOC)){
    		$retorno = true;
    	} else {
    		$retorno = false;
    	}
    	
    	return $retorno;
    }
}
