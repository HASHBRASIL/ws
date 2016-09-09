<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  09/07/2013
 */
class Zend_View_Helper_HasMovimento extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function hasMovimento($idNfe)
    {
        if($idNfe){
            $movimentoBo = new Material_Model_Bo_Movimento();
            $criteria = array('id_nfe = ?' => $idNfe);
            $movimento = $movimentoBo->find($criteria);
            if(count($movimento)){
                return true;
            }
        }
        return false;
    }
}