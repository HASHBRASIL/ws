<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/02/2014
 */
class Comercial_Model_Bo_Cliente extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Dao_Empresa
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_Empresa();
        parent::__construct();
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->nome_razao)){
            App_Validate_MessageBroker::addErrorMessage('O campo razão social está vazio.');
            return false;
        }

        if(empty($object->cnpj_cpf) && !empty($object->id)){
            $cliente = $this->find(array('id = ?' => $object->id));
            if($object->tps_id == Sis_Model_Bo_TipoPessoa::JURIDICA && !empty($cliente->current()->cnpj_cpf)){
                App_Validate_MessageBroker::addErrorMessage('O campo CNPJ está vazio.');
            }elseif ( !empty($cliente->current()->cnpj_cpf) ){
                App_Validate_MessageBroker::addErrorMessage('O campo CPF está vazio.');
            }
            return false;
        }

        $cnpjCpf = str_replace(array('.', '-', '/'), '', trim($object->cnpj_cpf));

        if(!empty($cnpjCpf)){
            /*checando se cpf/cnpj ja existe*/
            if (count($this->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO,"cnpj_cpf = ?" => $cnpjCpf)))>0 && $object->id == null){

            	App_Validate_MessageBroker::addErrorMessage("Este CNPJ/CPF já está em uso");
            	return false;

            }elseif ($object->id != null){

            	$empresaObj = $this->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO,"id = ?" => $object->id))->current();
    	        	if ($cnpjCpf != $empresaObj->cnpj_cpf){

    	        		if (count($this->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO,"cnpj_cpf = ?" => $cnpjCpf)))>0){

    	        			App_Validate_MessageBroker::addErrorMessage("Este CNPJ/CPF já está em uso");
    	        			return false;
    	        		}

    	        	}
            }
        }

        if($object->tps_id == Sis_Model_Bo_TipoPessoa::JURIDICA && !empty($object->cnpj_cpf)){
            $Validate = new App_Validate_Cnpj();
            if(!$Validate->isValid($object->cnpj_cpf)){
                App_Validate_MessageBroker::addErrorMessage("'{$object->cnpj_cpf}' não é um CNPJ válido");
                return false;
            }
        }else if ($object->tps_id == Sis_Model_Bo_TipoPessoa::FISICA && !empty($object->cnpj_cpf)){
            $validate = new App_Validate_Cpf();
            if(!$validate->isValid($object->cnpj_cpf)){
                App_Validate_MessageBroker::addErrorMessage("'{$object->cnpj_cpf}' não é um CPF válido");
                return false;
            }
        }
        return true;
    }

    /**
     * Antes de salvar irá passar por aki
     */
    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->cnpj_cpf = str_replace(array('.', '-', '/'), '', $object->cnpj_cpf);
        $object->telefone1 = str_replace(array('.', '-', '/', '(', ')', ' '), '', $object->telefone1);
        $object->telefone2 = str_replace(array('.', '-', '/', '(', ')', ' '), '', $object->telefone2);
        $object->telefone3 = str_replace(array('.', '-', '/', '(', ')', ' '), '', $object->telefone3);
        if(empty($object->dt_cadastro)){
            $object->dt_cadastro = date('Y-m-d H:i:s');
        }

        if(empty($request['transportador'])){
            $object->transportador = 0;
        }

        if(empty($request['grupo'])){
            $object->grupo = 0;
        }
    }

    /**
     * Antes de salvar irá passar por aki
     */
    protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(isset($request['grupo_geografico'])){
            $grupoEmpresaBo = new Sis_Model_Bo_GrupoGeograficoEmpresa();
            $grupoEmpresaBo->deleteByEmpresa($object->id);
            foreach ($request['grupo_geografico'] as $id_grupo_geografico){
                $grupoEmpresa                         = $grupoEmpresaBo->get();
                $grupoEmpresa->id_empresa             = $object->id;
                $grupoEmpresa->id_grupo_geografico    = $id_grupo_geografico;
                $grupoEmpresa->save();
            }
        }

        if(isset($request['caracteristica'])){
            $caracteristicaEmpresaBo = new Empresa_Model_Bo_CaracteristicaEmpresa();
            $caracteristicaEmpresaBo->deleteByEmpresa($object->id);
            foreach ($request['caracteristica'] as $id_caracteristica){
                $caracteristicaEmpresa                         = $caracteristicaEmpresaBo->get();
                $caracteristicaEmpresa->id_empresa             = $object->id;
                $caracteristicaEmpresa->id_caracteristica      = $id_caracteristica;
                $caracteristicaEmpresa->save();
            }
        }
    }

    /**
     * Gera paginacao
     * @param mixed $data
     * @param array $options
     * @return Zend_Paginator
     * @throws Exception if data type invalid
     */
    public function paginator( array $options)
    {
        $data = $this->_dao->selectPaginator($options, 'dt_cadastro DESC');

        $paginator = Zend_Paginator::factory($data);
        $paginator->setCurrentPageNumber(
                isset($options['page'])
                ? $options['page']
                : 1
        )->setItemCountPerPage(
                isset($options['itens'])
                ? $options['itens']
                : 50
        )->setPageRange(PHP_INT_MAX);

        if( isset( $options[ "searchString" ] ) && empty( $options[ "searchString" ] ) ){
            unset( $options[ "searchString" ] );
            unset( $options[ "search" ] );
            unset( $options[ "searchField" ] );
        }

        return $paginator;
    }


}