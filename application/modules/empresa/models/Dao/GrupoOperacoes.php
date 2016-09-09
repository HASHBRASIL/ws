<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  19/06/2013
 */
class Empresa_Model_Dao_GrupoOperacoes extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_operacoes";
    protected $_primary       = "ope_id";
    protected $_namePairs     = "ope_nome";

    protected $_rowClass = 'Empresa_Model_Vo_GrupoOperacoes';
    protected $_dependentTables = array('Financial_Model_Dao_Financial');


    public function inativar($id)
    {
        $row = $this->find($id)->current();

        if(empty($row)){
            App_Validate_MessageBroker::addErrorMessage('Este dado não pode ser excluído.');
            return false;
        }

        if(isset($row->ope_ativo)){
            $row->ope_ativo = parent::INATIVO;
            $row->save();
            return true;
        } else {
            App_Validate_MessageBroker::addErrorMessage('Este dado nao pode ser inativado.');
        }
    }

    public function getListGrupoWithAgrupadorAndWorkspacePerTransacao($ope_id = null, $workspace = null){

    	$select = $this->_db->select()->from(array('ope' => $this->_name), array("ope.ope_nome","ope.ope_id",new Zend_Db_Expr('sum(agf.fin_valor) AS fin_valor')))
    	->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'ope.ope_id = agf.ope_id', array(null))
    	->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
            ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));

//    	->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));

    	if (isset($ope_id)){

    		$select->where("ope.ope_id = ?", $ope_id);
    	}

    	if ($workspace){

    		$select->where('agf.id_grupo = ?', $workspace);
    	}

    	$select->group(array("wk.nome", "ope.ope_id", "agf.tmv_id",  "tmv_descricao"));
//        $select->group(array("wk.nome", "emp.id", "agf.tmv_id", "tmv_descricao"));

    	$select->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO)
//    	->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("ope.ope_ativo = ?",App_Model_Dao_Abstract::ATIVO);

    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    }

    public function getListGrupoWithFinanceiroAndWorkspacePerTicket($ope_id = null, $workspace = null){

//    	$select = $this->_db->select()->from(array('ope' => $this->_name),array("ope.ope_nome","ope.ope_id", new Zend_Db_Expr('sum(fin.fin_valor) AS fin_valor')))
//    	->joinInner(array('fin'=>'tb_financeiro'), 'ope.ope_id = fin.ope_id', array(null))
//    	->joinInner(array('agf'=>'tb_agrupador_financeiro'), 'fin.id_agrupador_financeiro = agf.id_agrupador_financeiro', array(null))
//    	->joinInner(array('tmv'=>'tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
//    	->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));


        $select = $this->_db->select()->from(array('ope' => $this->_name), array("ope.ope_nome","ope.ope_id",new Zend_Db_Expr('sum(fin.fin_valor) AS fin_valor')))
            ->joinInner(array('fin'=>'fin_tb_financeiro'), 'ope.ope_id = fin.ope_id', array(null))
            ->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'fin.id_agrupador_financeiro = agf.id_agrupador_financeiro', array(null))
            ->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
            ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));

    	if (isset($ope_id)){

    		$select->where("ope.ope_id = ?", $ope_id);
    	}

        if ($workspace){

            $select->where('agf.id_grupo = ?', $workspace);
        }

//    	$select->group(array("wk.nome", "ope.ope_id", "agf.tmv_id"));
        $select->group(array("wk.nome", "ope.ope_id", "agf.tmv_id",  "tmv_descricao"));

    	$select->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO)
//    	->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("ope.ope_ativo = ?",App_Model_Dao_Abstract::ATIVO)
    	->where("fin.ativo = ?",App_Model_Dao_Abstract::ATIVO);

    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

    }

}