<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/11/2013
 */
class Processo_ComentarioController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Comentario
     */
    protected $_bo;

    public function init()
    {
        $this->_helper->layout->setLayout('novo_hash');
    	$this->_bo = new Processo_Model_Bo_Comentario();
        parent::init();
        $this->_aclActionAnonymous = array('get');
        $this->_id = $this->getParam('id_comentario');
    }

    public function chatAction()
    {
        $this->_helper->layout()->disableLayout();
        $idProcesso         = $this->getParam('id_processo');
        $idEmpresa          = $this->getParam('id_empresa');
        $listComentario     = $this->_bo->find(array(
                                                'id_processo = ?' => $idProcesso,
                                                'id_corporativa = ?' => $idEmpresa,
                                                'ativo = ?' => App_Model_Dao_Abstract::ATIVO
                                            )
                                        );
        $this->view->listComentario    = $listComentario;
        $this->view->idEmpresa         = $idEmpresa;
    }

    public function getAction()
    {
        $idComentario = $this->getParam('id_comentario');
        $comentario = $this->_bo->get($idComentario);
        $comentarioArray = $comentario->toArray();
        $usuario = $comentario->getUsuario()->nome;
        $comentarioArray['nome_usuario'] = $usuario;
        $comentarioArray['data_format'] = $this->_bo->date($comentario->dt_criacao, 'dd/MM/YYYY HH:mm:ss');
        $this->_helper->json($comentarioArray);
    }

    public function gridByProcessoAction()
    {
        $this->_helper->layout()->disableLayout();
        $idProcesso = $this->getParam('id_processo');
        $comentarioList = $this->_bo->find(array('id_processo = ?' => $idProcesso, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO));
        $this->view->comentarioList = $comentarioList;
    }

}