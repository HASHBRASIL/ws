<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 21/08/2014
 */
class Rh_Model_Vo_CalculoPonto extends App_Model_Vo_Row
{

    public function getExtraList()
    {
    	$extraBo = new Rh_Model_Bo_Extra();
    	$criteria = array('id_calculo_ponto = ?' => $this->id_calculo_ponto);
    	$extraList = $extraBo->find($criteria);
    	if(count($extraList) == 0){
    		$extraBo->saveByCalculoPonto($this);
    		$extraList = $extraBo->find($criteria);
    		return $extraList;
    	}
    	return $extraList;
    }
    
    public function hasHoraExtraEquals()
    {
    	$extraBo = new Rh_Model_Bo_Extra();
    	$this->getExtraList();
    	$extraList = $extraBo->sumHoraExtra($this->id_calculo_ponto);
    	if($extraList == $this->hora_extra){
    		return true;
    	}
    	return false;
    }

    public function getFaltaList()
    {
    	$faltaBo = new Rh_Model_Bo_Falta();
    	$criteria = array('id_calculo_ponto = ?' => $this->id_calculo_ponto);
    	$faltaList = $faltaBo->find($criteria);
    	if(count($faltaList) == 0){
    		$faltaBo->saveByCalculoPonto($this);
    		$faltaList = $faltaBo->find($criteria);
    		return $faltaList;
    	}
    	return $faltaList;
    }
    
    public function hasHoraFaltaEquals()
    {
    	$faltaBo = new Rh_Model_Bo_Falta();
    	$this->getFaltaList();
    	$faltaList = $faltaBo->sumHoraFalta($this->id_calculo_ponto);
    	if($faltaList == $this->hora_falta){
    		return true;
    	}
    	return false;
    }
}