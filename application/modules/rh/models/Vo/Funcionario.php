<?php
class Rh_Model_Vo_Funcionario extends App_Model_Vo_Row
{

    public function getEmpresa()
    {
    	return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa');
    }
    
    public function getDocumento()
    {
    	$documentoBo = new Rh_Model_Bo_DocumentoIdentidade();
    	$criteria = array(
    				'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
    				'id_rh_funcionario = ?' => $this->id_rh_funcionario
    			);
    	$documento = $documentoBo->find($criteria)->current();
    	
    	return $documento;
    }
    
    public function getAdmissao()
    {
    	$admissaoBo = new Rh_Model_Bo_Admissao();
    	$criteria = array(
    				'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
    				'id_rh_funcionario = ?' => $this->id_rh_funcionario
    			);
    	$admissao = $admissaoBo->find($criteria)->current();
    	
    	return $admissao;
    }
}