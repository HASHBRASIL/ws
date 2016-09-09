<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_Model_Dao_Segmento extends App_Model_Dao_Abstract
{
    protected $_name = "tb_segmento_atividade";
    protected $_primary = "seg_id";

    protected $_namePairs = 'seg_descricacao';

    protected $_dependentTables = array('Empresa_Model_Dao_Empresa');
    
    public function inativar($id)
    {
    	$row = $this->find($id)->current();
    
    	if(empty($row)){
    		App_Validate_MessageBroker::addErrorMessage('Este dado não pode ser excluído.');
    		return false;
    	}
    
    	if(isset($row->seg_ativo)){
    		$row->seg_ativo = self::INATIVO;
    		$row->save();
    		return true;
    	} else {
    		App_Validate_MessageBroker::addErrorMessage('Este dado nao pode ser inativado.');
    	}
    }
    
}