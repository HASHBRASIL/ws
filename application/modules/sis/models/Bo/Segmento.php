<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_Model_Bo_Segmento extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Segmento
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Segmento();
        parent::__construct();
    }

    /**
     * @param boolean $ativo
     * @return array ($key => $value)
     */
    public function getPairs($ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        $where = null;
        if($ativo){
            $where = array("seg_ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }
    
    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	
    	$object->seg_ativo = 1/*ativo*/;
    	
    }
    
    

}