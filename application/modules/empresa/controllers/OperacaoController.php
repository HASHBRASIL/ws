<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Empresa_OperacaoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Empresa_Model_Bo_GrupoOperacoes
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Empresa_Model_Bo_GrupoOperacoes();
        parent::init();
    }
}