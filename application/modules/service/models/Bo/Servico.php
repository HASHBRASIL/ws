<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  08/04/2013
 */
class Service_Model_Bo_Servico extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_Servico
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_Servico();
        parent::__construct();
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $request = new Zend_Controller_Request_Http();
        $params = $request->getParams();

        if(empty($object->nome)){
            App_Validate_MessageBroker::addErrorMessage("O campo nome está vazio.");
            return false;
        }

        if(empty($object->unidade)){
            App_Validate_MessageBroker::addErrorMessage("O campo unidade está vazio.");
            return false;
        }

        if(empty($object->id_tipo_unidade)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um tipo de unidade.");
            return false;
        }

        if(empty($object->id_grupo) && empty($object->id_subgrupo) && empty($object->id_classe)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um grupo, subgrupo ou classe.");
            return false;
        }

        if(isset($params['tipo_servico_interno']) && !isset($params['id_centro_custo'])){
            App_Validate_MessageBroker::addErrorMessage("Selecione um centro de custo.");
            return false;
        }

        if(!isset($params['tipo_servico_interno']) && !isset($params['tipo_servico_externo'])){
            App_Validate_MessageBroker::addErrorMessage("Selecione um tipo de serviço.");
            return false;
        }

        return true;
    }

    /**
     * @todo provisorio pois não é a metida certa pois esta vindo como varchar do form
     */
    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->id_grupo)){
            $object->id_grupo = null;
        }

        if(empty($object->id_subgrupo)){
            $object->id_subgrupo = null;
        }

        if(empty($object->id_classe)){
            $object->id_classe = null;
        }

        if(empty($request['tipo_servico_interno'])){
            $object->tipo_servico_interno = 0;
        }

        if(empty($request['tipo_servico_externo'])){
            $object->tipo_servico_externo = 0;
        }

    }

    protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $servicoEmpresaBo   = new Service_Model_Bo_ServicoEmpresa();
        $servicoCentroBo    = new Service_Model_Bo_ServicoCentroCusto();

        $servicoCentroBo->deleteByServico($object->id_servico);
        $servicoEmpresaBo->deleteByServico($object->id_servico);

        $idEmpresaFornecedor = isset($request['id_empresa_fornecedor']) ? $request['id_empresa_fornecedor'] : null;
        $idCentroCusto       = isset($request['id_centro_custo']) ? $request['id_centro_custo'] : null;

        $this->_saveAssociativa($servicoEmpresaBo, $idEmpresaFornecedor, 'id_empresas', $object->id_servico);
        $this->_saveAssociativa($servicoCentroBo, $idCentroCusto, 'cec_id', $object->id_servico);

    }

    private function _saveAssociativa($bo, $value, $name, $id_servico)
    {
        if(isset($value)){
            foreach ($value as $id){
                $centroCusto                = $bo->get();
                $centroCusto->id_servico    = $id_servico;
                $centroCusto->$name         = $id;
                $centroCusto->save();
            }
        }
    }

    public function getAll($idGrupo = null, $idSubgrupo = null, $idClasse = null )
    {
        return $this->_dao->getAll($idGrupo, $idSubgrupo, $idClasse);
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

}