<?php
class Financial_CentroCustoController extends App_Controller_Action_TwigCrud
{
    /**
     * @var Financial_Model_Bo_CentroCusto
     */
    protected $_bo;

    public function init()
    {
        parent::init();
        $this->_bo = new Financial_Model_Bo_CentroCusto();
        $this->_helper->layout()->setLayout('novo_hash');
        $this->_redirectDelete = "home.php?servico=" . $this->servico['id_pai'];
        $this->_id = $this->getParam("id");
    }


    public function _initForm()
    {
        $this->view->comboCentroPai = $this->_bo->getPairs();
    }


//    /**
//     * @desc Pagina inicial do centro de custo aonde irá mostrar a lista de
//     * centro de custo cadastrados. Irá mostrar somente os centro de custos ativos
//     */
//    public function indexAction()
//    {
//
//        $identity = Zend_Auth::getInstance()->getIdentity();
//
//        $return = $this->_bo->find(array(
//            "id_grupo is null or id_grupo = ?" => $identity->grupo['id'],
//            'ativo = ?' => App_Model_Dao_Abstract::ATIVO
//        ));
//
//
//        $this->view->listCentroCusto = $return;
//
//    }

//    public function formAction()
//    {
//        $cec_id = $this->getParam('id');
//        $centroCusto = $this->_bo->get($cec_id);
//
//        if($this->getRequest()->isPost()){
//            $request     = $this->getAllParams();
//
//            if(empty($request['cec_id_pai'])){
//                $request['cec_id_pai'] = null;
//            }
//
//            try {
//                $grupo = $this->_bo->saveFromRequest($request, $centroCusto);
//                App_Validate_MessageBroker::addSuccessMessage('Dado gravado com sucesso');
//                $this->redirect('service/centro-custo/index');
//            }
//            catch (App_Validate_Exception $e){
//            }
//            catch (Exception $e){
//                App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro inesperado. entre em contato com o administrador.");
//            }
//        }
//
//        $this->view->centroCusto = $centroCusto;
//        $this->view->comboCentroPai = array('' => "---- Selecione ----") + $this->_bo->getPairs();
//    }

}
