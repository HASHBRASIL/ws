<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Dao_Contas extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_contas";
    protected $_primary       = "con_id";
    protected $_namePairs	  = "con_codnome";

    protected $_rowClass = 'Financial_Model_Vo_Contas';
    protected $_dependentTables = array('Financial_Model_Dao_Financial');

    protected $_referenceMap    = array(
    		'Bancos' => array(
    				'columns'           => 'bco_id',
    				'refTableClass'     => 'Financial_Model_Dao_Bancos',
    				'refColumns'        => 'bco_id'
    		),
    		'TipoContaBanco' => array(
    				'columns'           => 'tcb_id',
    				'refTableClass'     => 'Financial_Model_Dao_TipoContaBanco',
    				'refColumns'        => 'tcb_id'
    		),'Grupo' => array(
    				'columns'           => 'id_grupo',
    				'refTableClass'     => 'Legacy_Model_Dao_Grupo',
    				'refColumns'        => 'id'
    		)

    );


    public function selectPaginator(array $options = null)
    {
        $select = $this->_db->select()->from($this->_name,
            array('id' => 'con_id', 'con_codnome', 'con_agencia', 'con_age_digito', 'con_numero', 'con_digito'));

        $select->join(array('b' => 'fin_tb_bancos'), 'fin_tb_contas.bco_id = b.bco_id', array('bco_id', 'bco_nome'));
        $select->join(array('tpcb' => 'fin_tb_tipo_contabanco'), 'fin_tb_contas.tcb_id = tpcb.tcb_id',
            array('tcb_id', 'tcb_descricao'));
        $select->join(array('g' => 'tb_grupo'), 'g.id = fin_tb_contas.id_grupo', array('nome'));

        $identity = Zend_Auth::getInstance()->getIdentity();

        // @todo ver com o fernando qual a regra para mostrar as contas (times filhos e outros configurações)
        $select->where("fin_tb_contas.id_grupo = ?", $identity->time['id']);

        $select->where('ativo = ?', self::ATIVO);

        return $select;
    }



    public function getContasPerWorkspace($date = null, $type = null)
    {

    	$workspaceSession = new Zend_Session_Namespace('workspace');

    	$selectFinancial = $this->_db->select();
    	$selectFinancial->from(array('fin' => 'fin_tb_financeiro'), new Zend_Db_Expr('sum(fin.fin_valor)'))
    	->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'agf.id_agrupador_financeiro = fin.id_agrupador_financeiro', null)
    	->where('con.con_id  = fin.con_id')
    	->where('fin.ativo  = ?', App_Model_Dao_Abstract::ATIVO);

    	if($date){

    		$selectFinancial->where("date(fin.dt_criacao) >= ?", $date);
    	}

    	if($type){

    		if ($type == Financial_Model_Bo_Contas::ARECEBER){

    			$selectFinancial->where("agf.tmv_id = ?", Financial_Model_Bo_Contas::ARECEBER);

    		}else if($type == Financial_Model_Bo_Contas::APAGAR){

    			$selectFinancial->where("agf.tmv_id = ?", Financial_Model_Bo_Contas::APAGAR);

    		}

    	}

        $identity = Zend_Auth::getInstance()->getIdentity();

        $selectFinancial->where("agf.id_grupo = ?", $identity->time['id']);

    	$select = $this->_db->select();
    	$select->from(array('con'  => $this->_name), array(
    			"con_id",
    			"con_codnome",
    			'total_financeiro' => new Zend_Db_Expr("(".$selectFinancial.")")
    	));



//    	if(!$workspaceSession->free_access){
//
//    		$select->where("id_workspace = {$workspaceSession->id_workspace} or con.id_workspace is null");
//    	}

    	return $this->_db->fetchAll($select,null,Zend_Db::FETCH_OBJ);
    }


    /**
     * @var plcId para trazer um plano de contas especifico
     * @var workspace para filtrar pelo workspace contas e agrupador financeiro
     */
    public function getListContaWithFinanceiroAndWorkspacePerTicket($conId = null, $idGrupo = null){

    	$select = $this->_db->select()->from(array('con' => $this->_name),array("con_codnome","con.con_id", new Zend_Db_Expr('sum(fin.fin_valor) AS fin_valor')))
    	->joinInner(array('fin'=>'fin_tb_financeiro'), 'fin.con_id = con.con_id', array(null))
    	->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'agf.id_agrupador_financeiro = fin.id_agrupador_financeiro', array(null))
    	->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'agf.tmv_id = tmv.tmv_id', array("tmv_descricao"))
            ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));

    	if (isset($conId)){

    		$select->where("con.con_id = ?", $conId);
    	}

    	if ($idGrupo){

            $select->where('wk.id = ?', $idGrupo);
    	}

    	$select->group(array("wk.nome", "con.con_id", "agf.tmv_id", "tmv.tmv_descricao"));

    	$select
//            ->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("fin.ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("con.ativo = ?",App_Model_Dao_Abstract::ATIVO);

    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);
    }


    public function findByAccount($conta)
    {
        $identity = Zend_Auth::getInstance()->getIdentity();

        $select = $this->select()->from($this->_name, array('*'))
            ->join(array('b' => 'fin_tb_bancos'), 'fin_tb_contas.bco_id = b.bco_id', array(''))
            ->where("ltrim(bco_comp, '0') = ltrim(?, '0')", $conta->routingNumber)
            ->where("? ilike '%' || con_numero || '%'", $conta->accountNumber);

        $select->where('id_grupo = ?', $identity->time['id']);
        $select->where('ativo = ?', self::ATIVO);

        $row = $this->fetchRow($select);

        return $row;
    }

}

