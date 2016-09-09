<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  17/10/2013
 */
class Compra_Model_Bo_Campanha extends App_Model_Bo_Abstract
{
    /**
     * @var Compra_Model_Dao_Campanha
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Compra_Model_Dao_Campanha();
        parent::__construct();
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	if(empty($object->nome)){
    		App_Validate_MessageBroker::addErrorMessage('O campo nome está vazio.');
    		return false;
    	}
    	if(empty($object->dt_inicio)){
    		App_Validate_MessageBroker::addErrorMessage('O campo data inicial está vazio.');
    		return false;
    	}
    	if(empty($object->dt_fim)){
    		App_Validate_MessageBroker::addErrorMessage('O campo data fim está vazio.');
    		return false;
    	}
    	if(empty($object->id_tp_comissao)){
    		App_Validate_MessageBroker::addErrorMessage('Escolha um tipo de comissão.');
    		return false;
    	}
    	if(empty($object->porcent_comissao)){
    		App_Validate_MessageBroker::addErrorMessage('O campo comissão está vazio.');
    		return false;
    	}
    	if(empty($object->porcent_multa)){
    		App_Validate_MessageBroker::addErrorMessage('O campo multa está vazio.');
    		return false;
    	}
    	$dtFim = new Zend_Date($this->date($object->dt_fim, 'yyyy/MM/dd HH:mm:ss'));
    	if($dtFim->isEarlier($object->dt_inicio)){
    	    App_Validate_MessageBroker::addErrorMessage("A data do fim da campanha é menor do que a data inicial.");
    	    return false;
    	}
    	return true;
    }

    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	$object->vl_min_compra 	= $this->_formatDecimal($object->vl_min_compra);
    	$object->vl_max_compra	= $this->_formatDecimal($object->vl_max_compra);
    	$object->vl_adicional 	= $this->_formatDecimal($object->vl_adicional);
    	$object->qtd_compra		= str_replace(".", "",$object->qtd_compra);
    	$object->dt_inicio		= $this->date($object->dt_inicio, 'yyyy/MM/dd HH:mm:ss');
    	$object->dt_fim			= $this->date($object->dt_fim, 'yyyy/MM/dd HH:mm:ss');

    }


    public function betweenCampanha($idCampanha, Zend_Date $date = null)
    {
        $campanha = $this->get($idCampanha);
        if(empty($date)){
            $date = new Zend_Date();
        }
        if($date->isLater($campanha->dt_inicio) && $date->isEarlier($campanha->dt_fim)){
            return true;
        }
        return false;
    }
}