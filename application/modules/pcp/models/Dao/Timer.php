<?php
class Pcp_Model_Dao_Timer extends App_Model_Dao_Abstract
{

    protected $_name         = "tb_pcp_timer";
    protected $_primary      = "id_timer";

    public function getWorkedHours($idTimer)
    {
    	$select = $this->_db->select();
    	$select->from(array('tim' => $this->_name),array("worked_hours" => new Zend_Db_Expr("HOUR(TIMEDIFF(NOW(), tim.inicio_work))")))
    	->where('tim.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    	->where('tim.id_timer = ?', $idTimer);
    	
    	return $this->_db->fetchRow($select);
    }
    
    public function getTimerList()
    {
    
    	$select = $this->_db->select();
    	$select->from(array('tim' => $this->_name))
    	->joinInner(array("emp" => "tb_empresas"), "tim.empresas_id = emp.id ", array("nome_razao"))
    	->joinInner(array("pro" => "tb_processo"), "tim.pro_id = pro.pro_id ", array("pro_codigo"))
    	->where('tim.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    	->where('emp.ativo = ?', App_Model_Dao_Abstract::ATIVO);
    	 
    	return $this->_db->fetchAll($select,null,Zend_Db::FETCH_OBJ);
    
    }
}