<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  14/08/2013
 */
class Processo_ReciboController extends App_Controller_Action_AbstractCrud
{

    /**
     * @var Processo_Model_Bo_Processo
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Processo_Model_Bo_Processo();
    }
    public function ordemServicoAction()
    {
        $idProcesso = $this->getParam('pro_id');
        $processo = $this->_bo->get($idProcesso);
        $caminhoXml = APPLICATION_PATH."/../data/jxml/ordemServico.jrxml";
        if($processo->getEmpresa()){
            $params = array(
                    'nome_razao'   => $processo->getEmpresa()->nome_razao,
                    'cnpj_cpf'     => $processo->getEmpresa()->cnpj_cpf,
                    'telefone'     => $processo->getEmpresa()->telefone1,
                    'usuario'      => Zend_Auth::getInstance()->getIdentity()->nome_razao,
                    'cod_processo'  => $idProcesso
            );
        }
        //App_Util_Functions::debug($params);
        $jasperRelatorio = new App_Util_Jasper($caminhoXml, $params);
        $jasperRelatorio->abrir();

    }
}