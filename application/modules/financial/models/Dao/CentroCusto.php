<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Dao_CentroCusto extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_centro_custo";
    protected $_primary       = "cec_id";
    protected $_namePairs	  = "cec_descricao";

    protected $_dependentTables = array('Financial_Model_Dao_Financial');



    public function selectPaginator(array $options = null)
    {
        $select = $this->_db->select()->from($this->_name,
            array('id' => 'cec_id', 'cec_descricao', 'cec_codigo', 'cec_id_pai', 'cec_oculta', 'cec_operacional'));

        $select->join(array('cec_pai' => 'fin_tb_centro_custo'), 'fin_tb_centro_custo.cec_id = cec_pai.cec_id',
            array('cec_descricao_pai' => 'cec_descricao'));
        $select->join(array('g' => 'tb_grupo'), 'g.id = fin_tb_centro_custo.id_grupo', array('nome'));

        $identity = Zend_Auth::getInstance()->getIdentity();

        // @todo ver com o fernando qual a regra para mostrar as contas (times filhos e outros configurações)
        $select->where("fin_tb_centro_custo.id_grupo = ?", $identity->time['id']);

        return $select;
    }

    /**
     * @var plcId para trazer um plano de contas especifico
     * @var workspace para filtrar pelo workspace em centro de custos e agrupador financeiro
     */
    public function getListCentroCustoWithFinanceiroAndWorkspacePerTransacao($cecId = null, $idGrupo = null){

    	$select = $this->_db->select()->from(array('cec' => $this->_name), array("cec.cec_id", "cec.cec_descricao", new Zend_Db_Expr('sum(agf.fin_valor) AS fin_valor')))
    	->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'agf.cec_id = cec.cec_id', array(''))
    	->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
//    	->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));
        ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));

    	if (isset($cecId)){

    		$select->where("cec.cec_id = ?", $cecId);
    	}

    	if ($idGrupo){

            $select->where('wk.id = ?', $idGrupo);
    	}

    	$select->group(array("wk.nome", "cec.cec_id", "agf.tmv_id", "tmv.tmv_descricao"));

    	$select->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO)
//    	->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("cec.ativo = ?",App_Model_Dao_Abstract::ATIVO);

    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    }

    /**
     * @var plcId para trazer um plano de contas especifico
     * @var workspace para filtrar pelo workspace em centro de custos e agrupador financeiro
     */
    public function getListCentroCustoWithFinanceiroAndWorkspacePerTicket($cecId = null, $idGrupo = null){

    	$select = $this->_db->select()->from(array('cec' => $this->_name), array("cec.cec_id","cec.cec_descricao",new Zend_Db_Expr('sum(fin.fin_valor) AS fin_valor')))
    	->joinInner(array('fin'=>'fin_tb_financeiro'), 'fin.cec_id = cec.cec_id', array(null))
    	->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'agf.id_agrupador_financeiro = fin.id_agrupador_financeiro', array(null))
    	->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
//    	->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));
        ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));

    	if (isset($cecId)){

    		$select->where("cec.cec_id = ?", $cecId);
    	}

    	if ($idGrupo){

            $select->where('wk.id = ?', $idGrupo);
    	}

        $select->group(array("wk.nome", "cec.cec_id", "agf.tmv_id", "tmv.tmv_descricao"));
//    	$select->group(array("wk.nome", "cec.cec_id", "agf.tmv_id"));

    	$select->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO)
//    	->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("cec.ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("fin.ativo = ?",App_Model_Dao_Abstract::ATIVO);

    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    }
}

