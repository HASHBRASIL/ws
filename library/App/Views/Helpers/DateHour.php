<?php

class Zend_View_Helper_DateHour extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $datehour
     * @param  string $format
     * @param  string $default
     * @return string
     * @since 01/07/2013
     * @author Carlos Vinicius Bonfim da Silva
     */
    public function dateHour($datehour)
    {
    	if($datehour){

    		$date = new Zend_Date($datehour);
    		$datehour =  $date->toString('dd/MM/yyyy HH:mm:ss');
    		return $datehour;
    	}
    	return null;
    }
}
