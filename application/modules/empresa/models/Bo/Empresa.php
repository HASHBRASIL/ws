<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_Model_Bo_Empresa extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Dao_Empresa
     */
    protected $_dao;

    /**
     * @var integer
     */
    const TRANSPORTADOR = 1;
    const GRUPO         = 1;

    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_Empresa();
        parent::__construct();
    }

    public function getAutocomplete($term, $ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        $fornecedorBo = new Empresa_Model_Bo_Fornecedor();
        $grupoBo      = new Empresa_Model_Bo_EmpresaGrupo();

        $fornecedor   = $fornecedorBo->getAutocomplete($term, $ativo, $chave, $valor, $ordem, $limit);
        $grupo        = $grupoBo->getAutocomplete($term, $ativo, $chave, $valor, $ordem, $limit);

        $empresa = $fornecedor + $grupo;

        return $empresa;
    }

    public function getAutocompleteEmpresa($term, $ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null)
    {
        return $this->_dao->getAutocomplete($term, $ativo, $chave, $valor,
            $ordem, $limit);
    }


    public function getAutocompleteByCnpj($term, $where = null, $chave = null, $valor = null,
            $ordem = null, $limit = null)
    {
        $list = $this->_dao->getAutocompleteByCnpj($term, $chave, $valor,
            $where, $ordem, $limit);
        foreach ($list as &$value){
            $value['value'] = $this->_formatCnpj($value['value']);
            $value['label'] = $this->_formatCnpj($value['label']);
        }

        return $list;
    }
    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->nome_razao)){
            App_Validate_MessageBroker::addErrorMessage('O campo razão social está vazio.');
            return false;
        }

        if(empty($object->cnpj_cpf)){
            if($object->tps_id == Sis_Model_Bo_TipoPessoa::JURIDICA){
                App_Validate_MessageBroker::addErrorMessage('O campo CNPJ está vazio.');
            }else{
                App_Validate_MessageBroker::addErrorMessage('O campo CPF está vazio.');
            }
            return false;
        }

        $cnpjCpf = str_replace(array('.', '-', '/'), '', $object->cnpj_cpf);

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

        if($object->tps_id == Sis_Model_Bo_TipoPessoa::JURIDICA){
            $Validate = new App_Validate_Cnpj();
            if(!$Validate->isValid($object->cnpj_cpf)){
                App_Validate_MessageBroker::addErrorMessage("'{$object->cnpj_cpf}' não é um CNPJ válido");
                return false;
            }
        }else if ($object->tps_id == Sis_Model_Bo_TipoPessoa::FISICA){
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

    public function getAllEmpresa()
    {
        return $this->_dao->getAllEmpresa();
    }

    public function getAutocompleteTransportador($term, $ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        if($ativo){
            $where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        return $this->_dao->getAutocompleteCaracteristica(Empresa_Model_Bo_Caracteristica::TRANSPORTADOR, $term, $chave, $valor, $where, $ordem, $limit);
    }

    /**
     * @desc autentica o usuário
     * @param int $cpf
     * @param string $password
     * @return boolean true se o usuario tiver autenticado e false
     */
    public function authenticate($cpf_cnpj, $password)
    {
        $config     = Zend_Registry::get('config');
        $auth       = Zend_Auth::getInstance();
        $empresa    = $this->_dao->findEmpresaByCnpj($cpf_cnpj);

        if(empty($empresa)){
            return false;
        }
        $adapter = new  Zend_Auth_Adapter_DbTable();
        $adapter->setTableName('tb_usuarios')
        ->setIdentityColumn('usu_id')
        ->setCredentialColumn('usu_senha');
        $adapter->setIdentity($empresa->usu_id)
        ->setCredential(md5($password));

        $result = $auth->authenticate($adapter);
        //verifica se a autenticação foi válida
        if( $result->isValid() ){
            $storage = $auth->getStorage();
            $aclBo		= new Auth_Model_Bo_Acl();
            $acl		= $aclBo->registerAcl($empresa->usu_id);
           	$empresa->acl = $acl;
           	
           	$workspaceBo	= new Auth_Model_Bo_Workspace();
           	$workspaceList = $workspaceBo->registerWorkspace();
           	$empresa->workspace = $workspaceList;
           	
            $storage->write($empresa);
            
            return true;
        }

        return false;
    }

    private function _formatCnpj($cnpj)
    {
        if(strlen($cnpj) > 11){
            $primeiraParte = substr( $cnpj, 0, 2 );
            $segundaParte = substr( $cnpj, 2, 3 );
            $terceiraParte = substr( $cnpj, 5, 3 );
            $divisor = substr( $cnpj, 8, 4 );
            $identificador = substr( $cnpj, 12, 2 );

            return "$primeiraParte.$segundaParte.$terceiraParte/$divisor-$identificador";
        }else if(!empty($cnpj)) {
            $primeiraParte = substr( $cnpj, 0, 3 );
            $segundaParte = substr( $cnpj, 3, 3 );
            $terceiraParte = substr( $cnpj, 6, 3 );
            $identificador = substr( $cnpj, 9, 2 );

            return "$primeiraParte.$segundaParte.$terceiraParte-$identificador";
        }
        return "";
    }

    public function countEmpresas(){

    	return $this->_dao->countEmpresas();

    }
    public function countEmpresasLastDays(){

    	return $this->_dao->countEmpresasLastDays();

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

    public function getEmpresaRelatorio($cond,$resSql = false)
    {
        return $this->_dao->getEmpresaRelatorio($cond, $resSql);
    }

    public function paginatorInativos( array $options)
    {
    	$data = $this->_dao->selectPaginatorInativos($options);

    	$paginator = Zend_Paginator::factory($data);
    	$paginator->setCurrentPageNumber(
    			isset($options['page'])
    			? $options['page']
    			: 1
    	)->setItemCountPerPage(
    			isset($options['itens'])
    			? $options['itens']
    			: 250
    	)->setPageRange(PHP_INT_MAX);

    	if( isset( $options[ "searchString" ] ) && empty( $options[ "searchString" ] ) ){
    		unset( $options[ "searchString" ] );
    		unset( $options[ "search" ] );
    		unset( $options[ "searchField" ] );
    	}

    	return $paginator;
    }

    /**
     * @param boolean $ativo
     * @return array ($key => $value)
     */
    public function getFuncionarioPairs($ativo = true, $chave = null, $valor = 'nome_razao',
                            $ordem = null, $limit = null )
    {
    	$where = null;
        if($ativo){
            $where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        return $this->_dao->fetchPairsFuncionario($chave, $valor, $where, $ordem, $limit);
    }

    /**
     * @param boolean $ativo
     * @return array ($key => $value)
     */
    public function getGrupoPairs($ativo = true, $chave = null, $valor = 'nome_razao',
                            $ordem = null, $limit = null )
    {
    	$where = null;
        if($ativo){
            $where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        return $this->_dao->fetchPairsGrupo($chave, $valor, $where, $ordem, $limit);
    }
    public function autocompleteGeral($term, $criteria = null, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        if(count($criteria) > 4){
            $where = array();
            unset($criteria['controller']);
            unset($criteria['module']);
            unset($criteria['action']);
            unset($criteria['term']);
            foreach ($criteria as $key => $value){
                $where[$key.' in(?)'] = $value;
            }

        }
        $where["ativo = ?"] =  App_Model_Dao_Abstract::ATIVO;

        return $this->_dao->getAutocomplete($term, $chave, $valor, $where, $ordem, $limit);
    }
    
    public function getListFaturadoWithAgrupadorAndWorkspacePerTransacao($grupoId = null, $workspace = null){
    	
    	return $this->_dao->getListFaturadoWithAgrupadorAndWorkspacePerTransacao($grupoId, $workspace);
    }
    
    public function getListFaturadoWithAgrupadorAndWorkspacePerTicket($grupoId = null, $workspace = null){
    	 
    	return $this->_dao->getListFaturadoWithAgrupadorAndWorkspacePerTicket($grupoId, $workspace);
    }

    /**
     * @param boolean $ativo
     * @return array ($key => $value)
     */
    public function getCaracteristicaPairs($caracteristica, $ativo = true, $where = null, $limit = null)
    {
    	$where = null;
    	if($ativo){
    		$where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
    	}
    	return $this->_dao->fetchPairsCaracteristica($caracteristica, $where = null, $limit = null);
    }
}