<?php
class Material_Model_Bo_Item extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Item
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Item();
        parent::__construct();
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $request = new Zend_Controller_Request_Http();
        $params  = $request->getParams();

        if(empty($object->id_grupo) && empty($object->id_subgrupo) && empty($object->id_classe)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um grupo, subgrupo ou classe");
            return false;
        }

        if(empty($object->nome)){
            App_Validate_MessageBroker::addErrorMessage("O campo nome está vazio.");
            return false;
        }
        $criteria = array('nome = ?' => $object->nome, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        if(!empty($object->id_item)){
            $criteria = $criteria+array('id_item <> ?'=> $object->id_item);
        }
        $item = $this->find($criteria);

        if(count($item)){
            App_Validate_MessageBroker::addErrorMessage("Este produto já existe.");
            return false;
        }


        if(empty($params['materia_prima']) && empty($params['revenda']) && empty($params['produto_finalizado'])){
            App_Validate_MessageBroker::addErrorMessage("Selecione um tipo de serviço.");
            return false;
        }

        if(empty($object->id_tipo_unidade_compra)){
            App_Validate_MessageBroker::addErrorMessage("Selecione uma unidade de compra.");
            return false;
        }

        if(empty($object->id_tipo_unidade_consumo)){
            App_Validate_MessageBroker::addErrorMessage("Selecione uma unidade de consumo.");
            return false;
        }

        $subgrupoBo     = new Material_Model_Bo_Subgrupo();
        $classeBo       = new Material_Model_Bo_Classe();

        if(!empty($params['combo_grupo']) && empty($params['combo_subgrupo'])){
            $idGrupo = $params['combo_grupo'];
            $criteria = array('id_grupo = ?' => $idGrupo, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
            $subgrupo = $subgrupoBo->find($criteria);
            if(count($subgrupo)){
                App_Validate_MessageBroker::addErrorMessage("Selecione um subgrupo.");
                return false;
            }
        }

        if(!empty($params['combo_subgrupo']) && empty($params['combo_classe'])){
            $idSubgrupo   = $params['combo_subgrupo'];
            $criteria     = array('id_subgrupo = ?' => $idSubgrupo, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
            $classe       = $classeBo->find($criteria);
            if(count($classe)){
                App_Validate_MessageBroker::addErrorMessage("Selecione uma classe.");
                return false;
            }
        }

        return true;
    }

    public function getListItem($id_grupo = null, $subgrupo = null, $classe = null)
    {
        return $this->_dao->getListItem($id_grupo, $subgrupo, $classe);
    }

    /**
     * se não vier por post a materia prima eu desmarco.
     * (non-PHPdoc)
     * @see App_Model_Bo_Abstract::_preSave()
     */
    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $grupoBo = new Material_Model_Bo_Grupo();
        $subgrupoBo = new Material_Model_Bo_Subgrupo();
        $classeBo = new Material_Model_Bo_Classe();

        if(!empty($request['combo_grupo']) && empty($request['combo_subgrupo']) && empty($request['combo_classe'])){
            $object->id_grupo     = $request['combo_grupo'];
            $object->id_subgrupo  = null;
            $object->id_classe    = null;
        }

        if(!empty($request['combo_subgrupo']) && empty($request['combo_classe'])){
            $object->id_subgrupo  = $request['combo_subgrupo'];
            $object->id_grupo     = null;
            $object->id_classe    = null;
        }

        if(!empty($request['combo_classe'])){
            $object->id_classe     = $request['combo_classe'];
            $object->id_grupo      = null;
            $object->id_subgrupo   = null;
        }

        if(empty($request['materia_prima'])){
            $object->materia_prima = 0;
        }

        if(empty($request['revenda'])){
            $object->revenda = 0;
        }

        if(empty($request['produto_finalizado'])){
            $object->produto_finalizado = 0;
        }

        $object->valor_revenda = $this->_formatDecimal($object->valor_revenda);
    }

    /**
     * @desc Irá pegar todos os id_grupo é retornar em um array
     * @return array
     */
    public function getIdGrupo()
    {
        return $this->_dao->getCol('id_grupo');
    }

    /**
     * @desc Irá pegar todos os id_subgrupo é retornar em um array
     * @return array
     */
    public function getIdSubgrupo()
    {
        return $this->_dao->getCol('id_subgrupo');
    }

    public function duplicate($item, $id_dupicate)
    {
        $criteria                                      = array('id_item = ?' => $id_dupicate);
        $itemDuplicate                                 = $this->find($criteria)->current();
        $itemDuplicate['id_item']                      = null;
        $itemDuplicate['id_unidade_rastreabilidade']   = null;
        $item->setFromArray($itemDuplicate->toArray());
        return $item;
    }

    public function getAutocompleteToSumEstoque($term, $ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        $where = null;
        if($ativo){
            $where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }

//        if ($this->_hasWorkspace) {
//
//            $workspaceSession = new Zend_Session_Namespace('workspace');
//
////            if (!$workspaceSession->id_workspace){
////                $array = array();
////                return $array ;
////            }
//
////            if ($workspaceSession->free_access != true){
////
////                if ($this->_getRegistersWithoutWorkspace){
////                    $where["id_workspace IS NULL or id_workspace = {$workspaceSession->id_workspace} "] =  "";
////                }else{
////                    $where["id_workspace = ?"] = $workspaceSession->id_workspace;
////                }
////
////            }
//        }

        return $this->_dao->getAutocompleteToSumEstoque($term, $chave, $valor, $where, $ordem, $limit);
    }

    public function findProdutoByRequest($request)
    {
        unset($request['module']);
        unset($request['controller']);
        unset($request['action']);
        return $this->_dao->findProdutoByRequest($request);
    }

    public function mudarGrupo($request)
    {
        $this->_dao->getAdapter()->beginTransaction();
        try {
            foreach ($request['produto-checkbox'] as $id_produto){
                $produto = $this->get($id_produto);
                $produto->id_grupo         = null;
                $produto->id_subgrupo      = null;
                $produto->id_classe        = null;
                if(!empty($request['id_classe'])){
                    $produto->id_classe        = $request['id_classe'];
                }elseif(!empty($request['id_subgrupo'])){
                    $produto->id_subgrupo      = $request['id_subgrupo'];
                }elseif (!empty($request['id_grupo'])){
                    $produto->id_grupo           = $request['id_grupo'];
                }
                $produto->save();
            }
            $this->_dao->getAdapter()->commit();
        } catch (Exception $e) {
            $this->_dao->getAdapter()->rollBack();
            throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

}