<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Dao_PlanoContas extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_plano_contas";
    protected $_primary       = "plc_id";
    protected $_namePairs	  = "plc_descricao";

    protected $_rowClass = 'Financial_Model_Vo_PlanoContas';

    protected $_referenceMap    = array(
    		'PlanoContas' => array(
    				'columns'           => 'plc_id_pai',
    				'refTableClass'     => 'Financial_Model_Dao_PlanoContas',
    				'refColumns'        => 'plc_id'
    		),
    		'GrupoContas' => array(
    				'columns'           => 'grc_id',
    				'refTableClass'     => 'Financial_Model_Dao_GrupoContas',
    				'refColumns'        => 'grc_id'
            ),'Grupo' => array(
            'columns'           => 'id_grupo',
            'refTableClass'     => 'Legacy_Model_Dao_Grupo',
            'refColumns'        => 'id'
    		)
    );

    protected $_dependentTables = array('Financial_Model_Dao_Financial');

    public function selectPaginator(array $options = null)
    {
        $select = $this->_db->select()->from(array('pc' => $this->_name),
            array(
                'id' => 'plc_id',
                'plc_descricao',
                'plc_id_pai', // trazer descricao pai
//                'grc_id', // trazer tipo
                'plc_oculta',
                'plc_transferencia',
                'plc_resultado',
                'plc_contabil',
                'g.nome' // trazer nome do time
            ));

        $select->joinLeft(array('pcpai' => $this->_name), 'pcpai.plc_id = pc.plc_id_pai', array('plc_descricao_pai' => 'plc_descricao'));

        $select->join(array('gc' => 'fin_tb_grupo_contas'), 'gc.grc_id = pc.grc_id',
            array('grc_id', 'grc_descricao'));

        $select->join(array('g' => 'tb_grupo'), 'g.id = pc.id_grupo', array("nome_grupo" => "nome"));

        $identity = Zend_Auth::getInstance()->getIdentity();

        $select->where("pc.id_grupo = ?", $identity->time['id']);

        return $select;
    }

    public function getListPlanoContas ($type = null){

    	$idGrupoSession = new Zend_Session_Namespace('workspace');

	    $select = $this->_db->select()->from(array('pc' => $this->_name) )
	    ->joinInner(array('grc'=>'fin_tb_grupo_contas'), 'pc.grc_id = grc.grc_id')
	    ->where('grc.grc_ativo = ?', parent::ATIVO);


        $identity = Zend_Auth::getInstance()->getIdentity();

        $select->where("id_grupo = ?", $identity->time['id']);

	   	if (isset($type)){

	    	if ($type == 1 || $type == "d"){

	    		$select->where('pc.grc_id = ?', 2/*Id referente a pagar*/);

	    	}else if ($type == 2 || $type ==  "c"){

	    		$select->where('pc.grc_id = ?', 1/*Id referente a receber*/);

	    	}

	    }

	   	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    }

    /**
    * @var plcId para trazer um plano de contas especifico
    * @var workspace para filtrar pelo workspace em plano de contas e agrupador financeiro
    */
    public function getListPlanoWithAgrupadorAndWorkspacePerTransacao($plcId = null, $idGrupo = null){

    	$select = $this->_db->select()->from(array('pc' => $this->_name),array("pc.plc_cod_contabil","pc.plc_id", new Zend_Db_Expr('sum(agf.fin_valor) AS fin_valor')))
	    ->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'pc.plc_id = agf.plc_id', array("agf.pro_id"))
	    ->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
            ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));
//	    ->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));

    	if (isset($plcId)){

    		$select->where("pc.plc_id = ?", $plcId);
    	}

    	if ($idGrupo){
            $select->where('wk.id = ?', $idGrupo);

//    		$select->where("pc.id_workspace = {$idGrupo} or pc.id_workspace is null")
//    		->where('agf.id_workspace = ?', $idGrupo);
    	}

    	$select->group(array("wk.nome", "pc.plc_cod_contabil", "agf.tmv_id", "pc.plc_id", "agf.pro_id", "tmv_descricao"));

    	$select->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO);

//    	->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO);

    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    }

    /**
     * @var plcId para trazer um plano de contas especifico
     * @var workspace para filtrar pelo workspace em plano de contas e agrupador financeiro
     */
    public function getListPlanoWithAgrupadorAndWorkspacePerTicket($plcId = null, $idGrupo = null){

    	$select = $this->_db->select()->from(array('pc' => $this->_name), array("plc_cod_contabil", "plc_id" , new Zend_Db_Expr('sum(fin.fin_valor) AS fin_valor')))
    	->joinInner(array('fin'=>'fin_tb_financeiro'), 'fin.plc_id = pc.plc_id', array(null))
    	->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'agf.id_agrupador_financeiro = fin.id_agrupador_financeiro', array(null))
    	->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
            ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));

    	if (isset($plcId)){

    		$select->where("pc.plc_id = ?", $plcId);
    	}

    	if ($idGrupo){

            $select->where('wk.id = ?', $idGrupo);
    	}

        $select->group(array("wk.nome", "pc.plc_cod_contabil", "agf.tmv_id", "pc.plc_id", "agf.pro_id", "tmv_descricao"));
//    	$select->group(array("wk.nome", "pc.plc_cod_contabil", "agf.tmv_id"));

    	$select->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO)
//    	->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("fin.ativo = ?",App_Model_Dao_Abstract::ATIVO);

    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    }

}