<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  11/06/2013
 */
class Financial_Model_Dao_Financial extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_financeiro";
    protected $_primary       = "fin_id";
    protected $_colsSearch = array('fin_id', 'emp.fantasia','emp.nome_razao', 'fin_descricao', 'stf_descricao', 'con_codnome', 'fin_emissao', 'fin_vencimento', 'fin_compensacao', 'fin_nota_fiscal', 'fin_valor');
    protected $_rowClass = 'Financial_Model_Vo_Financial';

    protected $_referenceMap    = array(
    		'Status' => array(
    				'columns'           => 'stf_id',
    				'refTableClass'     => 'Financial_Model_Dao_Status',
    				'refColumns'        => 'stf_id'
    		),
    		'Contas' => array(
    				'columns'           => 'con_id',
    				'refTableClass'     => 'Financial_Model_Dao_Contas',
    				'refColumns'        => 'con_id'
    		),
            'Moeda' => array(
                				'columns'           => 'moe_id',
                				'refTableClass'     => 'Financial_Model_Dao_Moeda',
                				'refColumns'        => 'moe_id'
            ),
            'Conta' => array(
                    'columns'           => 'con_id',
                    'refTableClass'     => 'Financial_Model_Dao_Contas',
                    'refColumns'        => 'con_id'
            ),
            'DocumentoInterno' => array(
                    'columns'           => 'tid_id',
                    'refTableClass'     => 'Financial_Model_Dao_DocumentoInterno',
                    'refColumns'        => 'tid_id'
            ),
            'DocumentoExterno' => array(
                    'columns'           => 'tie_id',
                    'refTableClass'     => 'Financial_Model_Dao_DocumentoExterno',
                    'refColumns'        => 'tie_id'
            ),
            'ModeloSintetico' => array(
                    'columns'           => 'fin_id',
                    'refTableClass'     => 'Rh_Model_Dao_ReferenciaFinanceiroModelo',
                    'refColumns'        => 'fin_id'
            ),
            'AgrupadorFinanceiro' => array(
                    'columns'           => 'id_agrupador_financeiro',
                    'refTableClass'     => 'Financial_Model_Dao_AgrupadorFinanceiro',
                    'refColumns'        => 'id_agrupador_financeiro_correlato'
            ),
            'Correlato' => array(
                    'columns'           => 'id_financeiro_correlato',
                    'refTableClass'     => 'Financial_Model_Dao_Financial',
                    'refColumns'        => 'fin_id'
            )
    );

    protected $_dependentTables = array('Financial_Model_Dao_SacadoFinanceiro');

    protected function _condPaginator(Zend_Db_Select $select)
    {
    }

    public function selectPaginator(array $options = null)
    {
    	$select = $this->_db->select();
    	$select->from(array('f'  => $this->_name), array("fin_id",
    													 "fin_descricao",
    													 "id_agrupador_financeiro",
    													 "fin_observacao",
    													 "fin_emissao",
    													 "fin_vencimento",
    													 "fin_compensacao",
    													 "fin_numero_doc",
    													 "fin_valor",
    													 "tipo_conta" => new Zend_Db_Expr("CASE agf.tmv_id WHEN '1' THEN 'Débito' ELSE 'Crédito' END"),
    	                                                 'tmv_id' => 'agf.tmv_id'
    	))
    	->joinInner(array('agf' => 'fin_tb_agrupador_financeiro'), 'agf.id_agrupador_financeiro = f.id_agrupador_financeiro',array("fin_nota_fiscal"))
    	->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome"))
    	->joinLeft(array('rel_fin' => 'fin_rel_sacado_financeiro'), 'f.fin_id = rel_fin.tb_financeiro_fin_id', array())
    	->joinLeft(array('con' => 'fin_tb_contas'), 'f.con_id = con.con_id', array("con_codnome"))
    	->joinLeft(array('emp' => 'tb_pessoa'), 'rel_fin.id_pessoa = emp.id', array("nome_razao" => "nome"));

//    	->joinLeft(array('pro' => 'tb_processo'), 'agf.pro_id = pro.pro_id', array("pro_codigo", 'pro_desc_produto'));

    	if(isset($options['grupoContasList'])){

    		if (count($options['grupoContasList']) > 0){

    			$select->where('agf.tmv_id IN (?)', $options['grupoContasList'] );
    		}
    	}

    	if(isset($options['empresaList'])){

	    	if (count($options['empresaList']) > 0){

	    		$select->where('emp.id IN (?)', $options['empresaList'] );
	    	}
    	}
    	if(isset($options['contas']) && !empty($options['contas'])){

    		$select->where('con.con_id = ?', $options['contas'] );
    	}

    	if(isset($options['statusFinanceiro'])){

    		if($options['statusFinanceiro'] == "liquidado"){

    			$select->where('f.fin_compensacao IS NOT NULL')
    			->where('f.con_id IS NOT NULL');
    		}

    		if($options['statusFinanceiro'] == "vencido"){


    			$select->where('f.fin_vencimento < ?', date("Y-m-d") )
    			->where('f.fin_compensacao IS NULL');
    		}

    		if($options['statusFinanceiro'] == "avencer"){


    			$select->where('f.fin_vencimento >= ?', date("Y-m-d") )
    			->where('f.fin_compensacao IS NULL');
    		}
    	}


    	if (isset($options['data_emissaoType'])){

	    	if($options['data_emissaoType'] == "entre"){

	    		if($options['data_emissao'] != ""){
	    			$select->where('f.fin_emissao >= ?', $options['data_emissao']);
	    		}

	    		if($options['data_emissao2'] != ""){
	    			$select->where('f.fin_emissao <= ?', $options['data_emissao2']);
	    		}

	    	}else{//exato

	    		if($options['data_emissao'] != ""){
	    			$select->where('f.fin_emissao = ?', $options['data_emissao']);
	    		}

	    	}
    	}


    	if (isset($options['data_vencimentoType'])){

    		if($options['data_vencimentoType'] == "entre"){

    			if($options['data_vencimento'] != ""){
    				$select->where('f.fin_vencimento >= ?', $options['data_vencimento']);
    			}

    			if($options['data_vencimento2'] != ""){
    				$select->where('f.fin_vencimento <= ?', $options['data_vencimento2']);
    			}

    		}else{//exato

    			if($options['data_vencimento'] != ""){
    				$select->where('f.fin_vencimento = ?', $options['data_vencimento']);
    			}
    		}
    	}

    	if (isset($options['data_compensacaoType'])){

    		if($options['data_compensacaoType'] == "entre"){

    			if($options['data_compensacao'] != ""){
    				$select->where('f.fin_compensacao >= ?', $options['data_compensacao']);
    			}

    			if($options['data_compensacao2'] != ""){
    				$select->where('f.fin_compensacao <= ?', $options['data_compensacao2']);
    			}

    		}else{//exato

    			if($options['data_compensacao'] != ""){
    				$select->where('f.fin_compensacao = ?', $options['data_compensacao']);
    			}

    		}

    	}

//    	$workspaceSession = new Zend_Session_Namespace('workspace');
//    	$workspaceBo = new Auth_Model_Bo_Workspace();
//    	$workspaceObj = $workspaceBo->get($workspaceSession->id_workspace);
//
//    	if ($workspaceObj->free_access != true){
//
//    		$select->where("agf.id_workspace = ?",$workspaceObj->id_workspace);
//
//    	}

        $identity = Zend_Auth::getInstance()->getIdentity();

        $select->where("agf.id_grupo = ?", $identity->time['id']);

    	$select->where('f.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    	->where('agf.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    	->order("fin_id DESC");
    	$this->_searchPaginator($select, $options);
    	$this->_condPaginator($select);
    	return $select;
    }

    public function _searchPaginator(Zend_Db_Select $select, $options = null)
    {
    	if(isset($options['searchField'])){
    		if($options['searchField'] == 'tipo_conta'){
    			$select->having("tipo_conta = ?", $options['searchString']);
    		}else{
    			parent::_searchPaginator($select, $options);
    		}
    	}
    }

    public function nextOrPreviousIdFinancial($id, $selectSearch = null){

    	$result = new stdClass();

    	if ($selectSearch != null){

    		$select = $this->selectPaginator($selectSearch)
    		->where("f.fin_id > {$id}");
    		$nextId = $this->_db->fetchAll($select,null, Zend_db::FETCH_OBJ);

    		$countNextId = count($nextId);
    		if ($countNextId >0){
    			$nextIdResult = $nextId[$countNextId-1]->fin_id;
    		}else{

    			$nextIdResult = null;
    		}

    		$select = $this->selectPaginator($selectSearch)->where("f.fin_id < {$id}")->limit(1);
    		$previousId = $this->_db->fetchRow($select,null, Zend_db::FETCH_OBJ);

    		if ($previousId){
    			$previousIdResult = (int) $previousId->fin_id;
    		}else{
    			$previousIdResult = null;
    		}

    		$result->nextId = $nextIdResult;
    		$result->previousId = $previousIdResult;

    		return $result;
    	}

    	$select = $this->_db->select();
    	$select->from(array('fin' => $this->_name), 'fin_id')
    	->where('fin.fin_id > ?', $id)
    	->where('fin.fin_excluido = ?', "0");
    	$nextId =  $this->_db->fetchOne($select, null, Zend_Db::FETCH_OBJ);

    	$select = $this->_db->select();
    	$select->from(array('fin' => $this->_name), 'fin_id')
    	->where('fin.fin_id < ?', $id)
    	->where('fin.fin_excluido = ?', "0")
    	->order('fin_id DESC');
    	$previousId = $this->_db->fetchOne($select, null, Zend_Db::FETCH_OBJ);


    	$result->nextId = $nextId;
    	$result->previousId = $previousId;

    	return $result;
    }

    public function findFinancialForGetLimiteAjax($id){

    	$select = $this->_db->select();
    	$select->from(array('f' => $this->_name))
    	->joinInner(array('af'=>'fin_tb_agrupador_financeiro'), 'f.id_agrupador_financeiro = af.id_agrupador_financeiro')
    	->joinInner(array('rel'=>'rel_sacado_financeiro'), 'f.fin_id = rel.tb_financeiro_fin_id')
    	->joinInner(array('stf'=>'fin_tb_status_financeiro'), 'f.stf_id = stf.stf_id')
    	->joinInner(array('cnt'=>'fin_tb_contas'), 'f.con_id = cnt.con_id')
    	->where('rel.empresas_id = ?', $id)
    	->where('f.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    	->order("f.fin_id DESC")
    	->where('f.stf_id NOT IN (?)', array(1,4/*Liquidado e cancelado*/));
    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);
    	}

    	public function sumVlTotalByProcesso($idProcesso)
    	{
    	    $select = $this->_db->select();
    	    $select->from($this->_name, new Zend_Db_Expr('sum(fin_valor)'))
    	           ->where('pro_id  = ?', $idProcesso)
    	           ->where('fin_excluido = ?', 0);
    	    return $this->_db->fetchOne($select);
    	}

    	public function getExtrato($request){
    		$select = $this->_db->select()
    		->from(array('f'  => $this->_name))
    		->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'f.id_agrupador_financeiro = agf.id_agrupador_financeiro', array("id_agrupador_financeiro", "tmv_id"))
    		->joinInner(array('rel'=>'fin_rel_sacado_financeiro'), 'f.fin_id = rel.tb_financeiro_fin_id')
    		->joinLeft(array('con'=>'fin_tb_contas'), 'f.con_id = con.con_id')

            ->joinLeft(array('emp' => 'tb_pessoa'), 'rel.id_pessoa_empresa = emp.id', array("nome_razao" => "nome"));

//    		->joinInner(array('emp'=>'tb_empresas'), 'rel.empresas_id = emp.id');

    		if(isset($request['grupoContasList'])){

    			if (count($request['grupoContasList']) > 0){

    				$select->where('agf.tmv_id IN (?)', $request['grupoContasList'] );
    			}
    		}

    		if(isset($request['contaList'])){

    			if(count($request['contaList'])>0){

    				$select->where('f.con_id IN (?)', $request['contaList'] );
    			}

    		}
    		if (isset($request["transferencia"]) && $request["transferencia"] == App_Model_Dao_Abstract::ATIVO){

    			$select->where('agf.transferencia = ?', App_Model_Dao_Abstract::ATIVO);
    		}

    		if ($request["compensado"]){
    		    $select->where('f.fin_compensacao is not null and f.con_id is not null');
    		}elseif ($request["compensado"] === '0'){
    		    $select->where('f.fin_compensacao is null and f.con_id is null');
    		}

    		if ($request['de_fin_inclusao']!=""){

    			$select->where('date(f.fin_compensacao) >= ?', $request['de_fin_inclusao']);
    		}
    		if ($request['ate_fin_inclusao']!=""){

    			$select->where('date(f.fin_compensacao) <= ?', $request['ate_fin_inclusao']);
    		}

    		if(isset($request['empresaList'])){

    		    if(count($request['empresaList'])>0){

    		    				$select->where('rel.id_pessoa_empresa IN (?)', $request['empresaList'] );
    		    }

    		}


    		if ($request['de_fin_competencia']!=""){
    		    $select->where('date(f.fin_competencia) >= ?', $request['de_fin_competencia']);
    		}
    		if ($request['ate_fin_competencia']!=""){
    		    $select->where('date(f.fin_competencia) <= ?', $request['ate_fin_competencia']);
    		}

    		if ($request['de_fin_compensacao']!=""){
    		    $select->where('date(f.fin_compensacao) >= ?', $request['de_fin_compensacao']);
    		}
    		if ($request['ate_fin_compensacao']!=""){
    		    $select->where('date(f.fin_compensacao) <= ?', $request['ate_fin_compensacao']);
    		}

    		if ($request['de_fin_vencimento']!=""){
    		    $select->where('date(f.fin_vencimento) >= ?', $request['de_fin_vencimento']);
    		}
    		if ($request['ate_fin_vencimento']!=""){
    		    $select->where('date(f.fin_vencimento) <= ?', $request['ate_fin_vencimento']);
    		}

//    		$workspaceSession = new Zend_Session_Namespace('workspace');
//    		$workspaceBo = new Auth_Model_Bo_Workspace();
//    		$workspaceObj = $workspaceBo->get($workspaceSession->id_workspace);
//
//    		if ($workspaceObj->free_access != true){
//
//    			$select->where("agf.id_workspace = ?",$workspaceObj->id_workspace);
//
//    		}

            $identity = Zend_Auth::getInstance()->getIdentity();

            $select->where("agf.id_grupo = ?", $identity->time['id']);


            $select->where('f.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    		->where('agf.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    		->order("f.fin_compensacao DESC");//App_Util_Functions::debug($request);
    		return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);
    	}

    	protected function initgetInfoFinancial(){

    		$select = $this->_db->select();
    		$select->from(array('f' => $this->_name))
    		->joinInner(array('af'=>'fin_tb_agrupador_financeiro'), 'f.id_agrupador_financeiro = af.id_agrupador_financeiro', array("fin_valor_agrupador" => "fin_valor"))
    		->joinLeft(array('cnt'=>'fin_tb_contas'), 'f.con_id = cnt.con_id')
    		->where('f.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    		->where('af.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    		->order("f.fin_vencimento ASC");

    		return $select;

    	}

    	public function getFinancialListAtrasados($idAgrupador){

    		$select = $this->initgetInfoFinancial();
    		$select->where('f.fin_compensacao IS NULL')
    		->where('f.id_agrupador_financeiro = ?', $idAgrupador)
    		->where('date(f.fin_vencimento) < ?', date("Y-m-d"));

    		return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    	}

    	public function getFinancialListHoje($idAgrupador){

    		$select = $this->initgetInfoFinancial();
    		$select->where('f.id_agrupador_financeiro = ?', $idAgrupador)
    		->where('f.fin_compensacao IS NULL')
    		->where('date(f.fin_vencimento) = ?', date("Y-m-d"));
    		return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    	}

    	public function getFinancialListSeteDias($idAgrupador){

    		$select = $this->initgetInfoFinancial();
    		$select->where('f.fin_compensacao IS NULL')
    		->where('f.id_agrupador_financeiro = ?', $idAgrupador)
    		->where('date(f.fin_vencimento) > ?', date("Y-m-d"))
    		->where('date(f.fin_vencimento) <= ?', date("Y-m-d", strtotime("+7 days")));
    		return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    	}
    	public function getFinancialListPago($idAgrupador){

    		$select = $this->initgetInfoFinancial();
    		$select->where('f.fin_compensacao IS NOT NULL')
    		->where('f.con_id IS NOT NULL')
    		->where('f.id_agrupador_financeiro = ?', $idAgrupador);
    		return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    	}

    	public function tksOlerite($idAgrupador){

    		$select = $this->_db->select();
    		$select->from(array('f' => $this->_name))
    		->joinInner(array('rel'=>'rel_rh_financeiro'), 'f.fin_id = rel.fin_id', array('referencia'))
    		->joinLeft(array('ms'=>'tb_rh_modelo_sintetico'), 'rel.id_rh_modelo_sintetico = ms.id_rh_modelo_sintetico')
    		->where('f.id_agrupador_financeiro = ?', $idAgrupador)
    		->where('f.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    		->where('ms.id_rh_natureza_sintetico <> 3')
    		->where('ms.exibir = ?', App_Model_Dao_Abstract::ATIVO);

    		return $this->_db->fetchAll($select);

    	}
    	/**
    	 * @todo entender esse codigo feito pelo Vinicius
    	 * talvez não é necessário mais
    	 * @param unknown $idAgrupador
    	 */
    	public function tksOleriteFgts($idAgrupador){

    		$select = $this->_db->select();
    		$select->from(array('f' => $this->_name))
    		->joinInner(array('rel'=>'rel_rh_financeiro'), 'f.fin_id = rel.fin_id', array('referencia'))
    		->joinLeft(array('ms'=>'tb_rh_modelo_sintetico'), 'rel.id_rh_modelo_sintetico = ms.id_rh_modelo_sintetico')
    		->where('f.id_agrupador_financeiro = ?', $idAgrupador)
    		->where('f.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    		->where('ms.id_rh_natureza_sintetico = 3')
    		->where('ms.exibir = ?', App_Model_Dao_Abstract::ATIVO);

    		return $this->_db->fetchRow($select);

    	}

    	public function inativar($id)
    	{
    		$row = $this->find($id)->current();

    		if(empty($row)){
    			App_Validate_MessageBroker::addErrorMessage('Este dado não pode ser excluído.');
    			return false;
    		}
    		if(isset($row->ativo)){
    			$row->ativo = self::INATIVO;
    			if($row->getCorrelato()){
    				$correlato = $row->getCorrelato();
    				$correlato->ativo = self::INATIVO;
    				$correlato->save();
    			}
    			$row->save();
    			return true;
    		} else {
    			App_Validate_MessageBroker::addErrorMessage('Este dado nao pode ser inativado.');
    		}
    	}
}