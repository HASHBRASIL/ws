<?php

abstract class App_Controller_Action_TwigCrud extends App_Controller_Action_Twig
{

    /**
     * @var boolean
     */
    protected $_formSuccess = false;

    /**
     * @var App_Validate_MessageBroker
     */
    protected $_messageBroker;


    /**
     * @var String
     */
    protected $_redirectDelete = null;

    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_redirectFormSuccess = null;

    /**
     * @var string
     */
    protected $_messageFormSuccess = null;


    /**
     * @desc irá inicializar antes do formAction irá executar primeiro para
     * depois ir no formAction
     */
    public function init()
    {
        $this->_id = $this->getParam('id');
        parent::init();
    }

    public function _initForm()
    {

    }

    public function _initEditForm($object)
    {

    }

    /**
     * Salva o dado que vem via post por ajax e retorna um json
     */
    public function formAction()
    {
        $this->_initForm();
//        var_dump($this->_id);
        $request = $this->getAllParams();
        $object = $this->_bo->get($this->_id);
        $object->setFromArray($request);

        $this->_initEditForm($object);

        //Checando se registro possui workspace, se possuir ele valida se está no workspace correto, caso contrário redireciona para a action _redirectUnauthorizedWorkspace.
        if (isset($object->id_grupo) && !empty($object->id_grupo)) {
            // verificar a regra para isso
        }

        // verificar se vem via post
        if ($this->getRequest()->isPost()) {
            try {
                $this->_bo->saveFromRequest($request, $object);

//                $this->_bo->_dao->rollBack();
//                exit('nao deveria nem ter chegado aqui');

                if ($this->servico['ws_show'] != 'dropdown') {
                    $target = (isset($this->servico['ws_target']) && $this->servico['ws_target'])
                        ? $this->servico['ws_target']
                        : $this->servico['id_pai'];

                } else {
                    $target = null;
                }


                if ($this->getRequest()->isXmlHttpRequest()) {
                    $response = array(
                        'success' => true,
                        'msg' => $this->_translate->translate("Dados salvos com sucesso"),
                        'data' => array('target' => array('servico' => $target)),
                        'id' => $object->getPrimaryKey()
                    );
                    $this->_helper->json($response);
                }

                $this->_addMessageSuccess("Dados salvos com sucesso", "home.php?servico=" . $target);

            } catch (App_Validate_Exception $e) {
                //verifica se e pelo ajax
                if ($this->getRequest()->isXmlHttpRequest()) {
                    $response = array('success' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTraceAsString());
                    $this->_helper->json($response);
                } else {
                }
            } catch (Exception $e) {
                //verifica se e pelo ajax
                if ($this->getRequest()->isXmlHttpRequest()) {
                    $response = array('success' => false, 'msg' => 'Não foi possível realizar a operação solicitada. ' . $e->getMessage(), 'trace' => $e->getTraceAsString());
                    $this->_helper->json($response);
                }

                $this->_addMessageError($e->getMessage());
            }
        }

        $this->view->vo = $object;
    }

    /**
     * @desc irá inativar o dado apartir do params recebido pelo request com a chave id
     */
    public function deleteAction()
    {
        //Desativar a view
        $this->_helper->viewRenderer->setNoRender();

        //verifica se e pelo ajax
        if($this->getRequest()->isXmlHttpRequest()){
            $this->_bo->inativar($this->_id);
            $response = $this->_mensagemJson();

            $this->_helper->json($response);
        } else {
            $this->_bo->inativar($this->_id);

            $servicoTarget = $this->servico['ws_target'] ?: $this->servico['id_pai'];

            $this->redirect($this->view->baseUrl(  'home.php?servico=' . $servicoTarget));
//            if(!$this->_redirectDelete){
//                $this->_helper->redirector->gotoSimple( 'grid',
//                    $this->getRequest()->getControllerName(),
//                    $this->getRequest()->getModuleName());
//            } else {
//                $this->_redirect($this->_redirectDelete);
//            }
        }
    }

//    public function uploadAction()
//    {
//        $upload = new Zend_File_Transfer_Adapter_Http();
//
//        if (! $upload->isValid()){
//            exit('erro!');
////            $resposta = array('error' => true, 'msg' => 'Ocorreu uma falha no envio do documento.', 'messages' => current($upload->getMessages()));
//        } else {
//            $info         = $upload->getFileInfo();
//            $fileContent = file_get_contents($info['file']['tmp_name']);
//
//            try {
//                $this->_bo->upload($upload, $fileContent);
//
//                $target = (isset($this->servico['ws_target']) && $this->servico['ws_target'])
//                    ? $this->servico['ws_target']
//                    : $this->servico['id_pai'];
//
//                if ($this->getRequest()->isXmlHttpRequest()) {
//                    $response = array(
//                        'success' => true,
//                        'msg' => $this->_translate->translate("Dados salvos com sucesso"),
//                        'data' => array('target' => array('servico' => $target))
//                    );
//                    $this->_helper->json($response);
//                }
//
//                if ($target) {
//                    $this->_addMessageSuccess("Dados salvos com sucesso", "home.php?servico=" . $target);
//                } else {
//                    $this->_addMessageSuccess("Dados salvos com sucesso");
//                }
//
//            } catch (App_Validate_Exception $e) {
//                throw $e;
////                //verifica se e pelo ajax
////                if ($this->getRequest()->isXmlHttpRequest()) {
////                    $response = array('success' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTraceAsString());
////                    $this->_helper->json($response);
////                } else {
////                }
//            } catch (Exception $e) {
//                throw $e;
////                //verifica se e pelo ajax
////                if ($this->getRequest()->isXmlHttpRequest()) {
////                    $response = array('success' => false, 'msg' => 'Não foi possível realizar a operação solicitada. ' . $e->getMessage(), 'trace' => $e->getTraceAsString());
////                    $this->_helper->json($response);
////                }
////
////                $this->_addMessageError($e->getMessage());
//            }
//
//        }
////        $this->_addMessageSuccess("Dados salvos com sucesso");
////        exit();
//
//    }

}