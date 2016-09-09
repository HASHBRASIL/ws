<?php
/**
 * @author Vinicius Leônidas
 * @since 21/10/2013
 */
class Compra_Model_Bo_CampanhaItem extends App_Model_Bo_Abstract
{
	/**
	 * @var Compra_Model_Dao_CampanhaItem
	 */
	protected $_dao;

	public function __construct()
	{
		$this->_dao = new Compra_Model_Dao_CampanhaItem();
		parent::__construct();
	}

	/**
	 * @param Id $id da campanha
	 * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
	 */
	public function getItens($id_campanha, $id_item = null, $term = null, $valor = null){
			return $this->_dao->getItens($id_campanha, $id_item, $term, $valor);
	}
	public function getItensReferencia($id_campanha, $valor = null){
		return $this->_dao->getItensReferencia($id_campanha, $valor);
	}
	public function getItensNome($id_campanha, $valor = null){
		return $this->_dao->getItensNome($id_campanha, $valor);
	}

	public function saveFromRequestByCampanha( $request)
	{

	    $resultValidationRequest = $this->_validarByRequest($request);
	    if(!$resultValidationRequest){
	        throw new App_Validate_Exception();
	    }
        foreach ($request['id_item'] as $id_item){
            //populando o objeto
            $object                      = $this->get();
            $object->id_item             = $id_item;
            $object->id_criacao_usuario  = Zend_Auth::getInstance()->getIdentity()->usu_id;
            $object->dt_criacao          = date('Y-m-d H:i:s');
            $object->vl_unitario         = $request['valor_'.$id_item];
            $object->id_campanha         = $request['id_campanha'];

            $resultValidation = $this->_validar($object);
            if(!$resultValidation){
                throw new App_Validate_Exception();
            }
            try {
                $this->_preSave($object, $request);
                $object->save();
                $this->_postSave($object, $request);
            } catch (Exception $e) {
                    App_Validate_MessageBroker::addErrorMessage("O sistema se encontra fora do ar no momento. Caso persista entre em contato com o Administrador.".$e->getMessage());
                    throw new App_Validate_Exception();
            }
        }
    }

    protected function _validarByRequest($request)
    {
        if(count($request['id_item']) == 0){
            App_Validate_MessageBroker::addErrorMessage('Selecione ao menos um produto.');
            return false;
        }
        if(empty($request['id_campanha'])){
            App_Validate_MessageBroker::addErrorMessage('Escolha uma campanha.');
            return false;
        }
        foreach ($request['id_item'] as $idItem){
            $criteria = array(
                    'id_item = ?'     => $idItem,
                    'id_campanha = ?' => $request['id_campanha'],
                    'ativo = ?'       => App_Model_Dao_Abstract::ATIVO
                    );
            $campanhaItem = $this->find($criteria);
            if (count($campanhaItem) > 0){
                App_Validate_MessageBroker::addErrorMessage('O produto "'.$request['nome_item_'.$idItem].'" já existe para essa campanha.');
                return false;
                break;
            }
        }
        return true;
    }

    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter=null)
    {
        $object->vl_unitario = $this->_formatDecimal($object->vl_unitario);
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter=null)
    {
        $campanhaBo = new Compra_Model_Bo_Campanha();

        if(!empty($object->id_campanha_item) && $campanhaBo->betweenCampanha($object->id_campanha)){
            App_Validate_MessageBroker::addErrorMessage("A campanha está em vigência.");
            return false;
        }
        return true;
    }
}