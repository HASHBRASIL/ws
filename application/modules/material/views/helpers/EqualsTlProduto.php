<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/06/2013
 */
class Zend_View_Helper_EqualsTlProduto extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function equalsTlProduto($tlProduto, $idNfe)
    {
        $nfeBo = new Material_Model_Bo_Nfe();
        if($nfeBo->equalsTlProduto($tlProduto, $idNfe)){
            return "";
        }else{
            return "distinct_total";
        }
    }
}