<?php
 /*  @author Julyeser Santos Silva
  *  @since  23/07/2013
 */

class Empresa_GrupoOperacaoController extends App_Controller_Action_AbstractCrud{
    //public $_bo;
    public $_bo_empresas_grupo;

    public function init()
        {


            $this->_bo = new Empresa_Model_Bo_GrupoOperacoes();
            $this->_bo_empresas_grupo = new Empresa_Model_Bo_EmpresaGrupo();


            $this->_helper->layout()->setLayout('metronic');
            parent::init();
            $this->_id = $this->getParam('ope_id');

            $this->_redirectDelete = 'empresa/grupo-operacao/grid';
        }

    public function _initForm(){
        $select = $this->_bo_empresas_grupo->getSelect();
        $this->view->empresas_grupo = $select;
    }

    public function gridAction(){
        $this->view->registros = $this->_bo->getSelect();
    }

    public function relatorioAction(){


    }









}