<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/01/2013
 */
class Zend_View_Helper_TextAtributoByEstoque extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function textAtributoByEstoque($idEstoque)
    {
        $estoqueOpcaoBo = new Material_Model_Bo_EstoqueOpcao();
        $estoqueOpcaoList =  $estoqueOpcaoBo->getListAll($idEstoque);
        $html = '';
        if(count($estoqueOpcaoList) > 0){
            foreach ($estoqueOpcaoList as $key => $estoqueOpcao){
                if($key > 0)
                    $html .= ' | ';

                $html .= $estoqueOpcao['nome_atributo'].': '.$estoqueOpcao['nome_opcao'];
            }
        }
        return $html;

    }
}