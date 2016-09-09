<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  09/04/2013
 */
class Sis_TipoUnidadeController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_TipoUnidade
     */
    protected $_bo;

    protected $_redirectDelete = '/sis/tipo-unidade/grid';

    /**
     * irá criar um objeto do BO
     * @see App_Controller_Action_AbstractCrud::init()
     */
    public function init()
    {
        $this->_bo = new Sis_Model_Bo_TipoUnidade();
				$this->_aclActionAnonymous = array('form', 'grid', 'delete');
		    $this->_helper->layout()->setLayout('metronic');
        parent::init();
    }

    /**
     * Salva o dado que vem via post por ajax e retorna um json
     */
    public function formAction()
    {
        // verificar se vem via post
        if($this->getRequest()->isPost()){
            $id_tipo_unidade = $this->getParam('id_tipo_unidade');
            $request         = $this->getAllParams();
            $tipoUnidade     = $this->_bo->get($id_tipo_unidade);

            try {
                $this->_bo->saveFromRequest($request, $tipoUnidade);
                $response = array(
                              'success' => true,
                              'tipo_unidade' => array(
                                                  'id'   => $tipoUnidade->id_tipo_unidade,
                                                  'nome' => $tipoUnidade->nome
                                                )
                                 );
                $this->_helper->json($response);
            }
            catch (App_Validate_Exception $e){
                //verifica se e pelo ajax
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array('success' => false, 'mensagem' => $this->_mensagemJson());
                    $this->_helper->json($response);
                }
            }
            catch (Exception $e){
                $response = array('success' => false, 'message'=>'Errado '.$e->getMessage() );
                $this->_helper->json($response);
            }
        }
    }
    public function gridAction(){
    	
    	$this->view->unidadeList = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO));
    	
    }
}