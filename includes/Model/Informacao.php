<?php

/**
 * User: eric
 * Date: 18/01/16
 * Time: 11:35
 */
class Informacao extends Base
{
    function euExisto($id_tinfo, $id_pessoa, $id_time)
    {
    	$stmt = $this->dbh->prepare(
    			"select	inf.* from tb_informacao inf join rl_grupo_informacao rgi on (inf.id = rgi.id_info) where	inf.id_tinfo = :id_tinfo and inf.id_pessoa = :id_pessoa and rgi.id_grupo = :id_time");
    	
    	$stmt->bindValue(':id_tinfo',			$id_tinfo);
    	$stmt->bindValue(':id_pessoa',			$id_pessoa);
    	$stmt->bindValue(':id_time',			$id_time);
    	$stmt->execute();
    	
    	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    	if(count( $rs) > 0){
		return $rs[0]['id'];
    	} else {
    		return false;
    	}
    }
    
    function criarNova( $id_tinfo, $id_pessoa, $valor, $dtype = null, $id_pai = null ){
    	
    	if ( is_array($id_tinfo) ){
    		$id_tinfo	=	$id_tinfo['id'];
    	}
    	
    	$campos	=	'id, id_tinfo, id_pessoa, valor, id_pai';
    	$values	=	':id, :id_tinfo, :id_pessoa, :valor, :id_pai';
    	
    	$chave	=	UUID::v4();
    	
    	$query = $this->dbh->prepare("INSERT INTO tb_informacao (".$campos.") VALUES (".$values.")");
    	$query->bindParam('id', 		$chave);
    	$query->bindParam('id_tinfo',	$id_tinfo);
    	$query->bindParam('id_pessoa',	$id_pessoa);
    	$query->bindParam('valor', 	$valor);
	$query->bindParam('id_pai',	$id_pai);

    	//echo "RESULTADO DA QUERY " . $query->execute() . " " . $id_pessoa;
    	
    	return $chave;
    }

    function updateValor($id, $valor) {
    	$query = $this->dbh->prepare("UPDATE tb_informacao set valor = :valor where id = :id");
    	$query->bindParam(':id', 		$id);
    	$query->bindParam(':valor', 	$valor);
    	$query->execute();
    }

    function deletar($id) {
    	$query = $this->dbh->prepare("DELETE FROM tb_informaca WHERE id = :id");
    	$query->bindParam(':id', 		$id);
    	$query->execute();
    }

    function CPFouCNPJJaCadastrado( $valor, $pessoaFisica = null, $retornaPessoa = null ){
    	
    	$objTpInfo	=	new TpInformacao();
    	if ( is_null($pessoaFisica)){
    		$tinfo	=	$objTpInfo->getByMetanome('CNPJ');
    	} else {
    		$tinfo	=	$objTpInfo->getByMetanome('CPF');
    	}

    	if ( is_null( $valor) || empty( $valor) || $valor == ''){
    		return false;
    	}
    		
    	
    	$stmt = $this->dbh->prepare("select	* from	tb_informacao where	valor = ? and id_tinfo = ?");
    	$stmt->bindValue(1,			$valor);
    	$stmt->bindValue(2,			$tinfo['id']);
    	$stmt->execute();
    	 
    	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
/*    	
    	if( $rs[0]['id_pessoa'] == '3d9f1643-96f4-4f76-94a3-aaf327121270'){
    		x($valor,0);
    		x($tinfo);
    	}
  */  	
    	if(count( $rs) > 0){
    		if( is_null( $retornaPessoa ) ){
    			return true;
    		}
    		return $rs[0]['id_pessoa'];
    	} else {
    		return false;
    	}   	
    }
    
    function informacaoJaCadastrada($id_tinfo, $id_pessoa, $retornarInformacao = FALSE)
    {
    	
    	if ( is_array($id_tinfo) ){
    		$id_tinfo	=	$id_tinfo['id'];
    	}    	 
    	
    	$stmt = $this->dbh->prepare(
    			"select	* from	tb_informacao where	id_tinfo = :id_tinfo and id_pessoa = :id_pessoa");
    	 
    	$stmt->bindValue(':id_tinfo',			$id_tinfo);
    	$stmt->bindValue(':id_pessoa',			$id_pessoa);
    	$stmt->execute();
    	 
    	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    	if(count( $rs) > 0){
    		if ( $retornarInformacao ) {
    			return $rs[0];
    		} else {
    			return true;
    		}
    		
    	} else {
    		return false;
    	}
    }
    
    function informacaoComValorJaCadastrado($id_tinfo, $id_pessoa, $valor)
    {
    	if ( is_array($id_tinfo) ){
    		$id_tinfo	=	$id_tinfo['id'];
    	}
    	 
    	$stmt = $this->dbh->prepare(
    			"select	* from	tb_informacao where	id_tinfo = :id_tinfo and id_pessoa = :id_pessoa and valor = :valor");
    
    	$stmt->bindValue(':id_tinfo',			$id_tinfo);
    	$stmt->bindValue(':id_pessoa',			$id_pessoa);
    	$stmt->bindValue(':valor',			$valor);
    	$stmt->execute();
    
    	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    	if(count( $rs) > 0){
   			return true;
    	} else {
    		return false;
    	}
    }
}
