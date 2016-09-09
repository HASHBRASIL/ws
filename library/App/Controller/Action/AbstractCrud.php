<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  26/03/2013
 */
abstract class App_Controller_Action_AbstractCrud extends App_Controller_Action
{

	/**
	 * @var string
	 */
	protected $_redirectUnauthorizedWorkspace = "grid";
	/**
     * @var array
     */
    protected $_aclActionAnonymous = array();

    /**
     * @var boolean
     */
    protected $_authAnonymous = false;

    /**
     * @var boolean
     */
    protected $_applyMessagesAfter = true;

    /**
     * @var boolean
     */
    protected $_formSuccess = false;

    /**
     * @var App_Validate_MessageBroker
     */
    protected $_messageBroker;

    /**
     * @var App_Model_Bo_Abstract
     */
    protected $_bo;

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
     * @var boolean
     */
    protected $_isCompany = false;

    /**
     * @see Zend_Controller_Action::init()
     * @desc inicializa o param $_messageBroker como objeto do App_Validate_MessageBroker
     * Verifica se o usuário está logado se não estiver ele será redirecionado para o login
     */
    public function init()
    {
    	$this->_id = $this->getRequest()->getParam('id');
    	$this->_messageBroker = App_Validate_MessageBroker::getInstance();

//    	$menuBo = new Auth_Model_Bo_Menu();
//    	$this->view->menusPaisList = $menuBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_au_parent_menu IS NULL"));
        parent::init();
    }

    /**
     * (non-PHPdoc)
     * @see Só altere este método se você realmente souber o que estiver fazendo. Se sobrescrever na controller não haverá
     * verificação de auth, acl, root, ou tempo ocioso de sessao!
     *
     * @see Zend_Controller_Action::preDispatch()
     * @desc Verifica se o usuário está logado e se tem ACL, se não estiver logado ele será redirecionado para o login
     * e se não estiver autenticado para a pagina de forbidden.
     */
    public function preDispatch()
    {
        // @todo acl removido
        return;
        if (!$this->_authAnonymous){

	    	if (Zend_Auth::getInstance()->hasIdentity()){

				//guardando informacoes da pagina visitada para uso tanto da sessao expirada quanto do travamento da tela
	    		$timeSession = new Zend_Session_Namespace( 'timeSessionExpire' );
	    		$url = $this->view->url();
	    		$timeSession->redirect = substr( $url, 1 );
	    		$timeSession->idUser = Zend_Auth::getInstance()->getIdentity()->usu_id;

	    		//testando se sessão por tempo ocioso expirou
	    		if(!isset($timeSession->limiteTime)){

	    			$timeSession->limiteTime = (time()+ (30 * 60));//30 minutos

	    		}else{

		    		if(time() > $timeSession->limiteTime){

		    			//As requisições ajax não posssuem redirecionamento para sessao ociososa. Isto foi deliberado pois possuimos muitos ajax
		    			//e teriamos que tratar cada um deles para emitir uma mensagem de alerta. Carlos Vinicius e Ellyson de Jesus
		    			if (!$this->getRequest()->isXmlHttpRequest()){

			    			$this->_redirect('auth/forbidden/session');

		    			}

		    		}else{
		    			$timeSession->limiteTime = (time()+ (30 * 60));//30 minutos
		    		}

	    		}

	    		//O root é um recurso que dá acesso as Actions em todo sistema sem passar pelas validações de ACL. Isto deve ser aplicado a um user se e somente se este for desenvolvedor.
	    		//Deliberado por Carlos Vinicius e Ellyson de Jesus em 11/09/2013
	    		if (Zend_Auth::getInstance()->getStorage()->read()->root == true){
	    			return;
	    		}

	    		//trazendo o acl
	    		$storage =Zend_Auth::getInstance()->getStorage()->read();
	    		$acl = $storage->acl;

	    		$module = $this->getRequest()->getModuleName();
	    		$controller = $this->getRequest()->getControllerName();
	    		$action = $this->getRequest()->getActionName();
	    		$resource = "{$module}-{$controller}";
				//se a action for anonima nao faz validacoes de permissao
	    		foreach ($this->_aclActionAnonymous as $actionAnonymouskey => $actionAnonymous) {
		    		if($action == $actionAnonymous ){
		    			return;
		    		}
	    		}

	    		if ($acl->has($resource)) {

	    			$authorized = false;

					foreach ($acl->getRoles() as $role){
		    			if ($acl->isAllowed($role, $resource, $action)) {
		    				//esta logado e autorizado
		    				$authorized = true;
		    				break;
		    			}else{
		    				//
		    			}
					}
					if(!$authorized){
						$this->_redirect("/auth/forbidden/index/error/resource-not-found:0001");//nao tem o recurso esperado
					}
	    		}else{
	    			$this->_redirect("/auth/forbidden/index/error/resource-not-found:0002");//nao tem nenhum recurso
	    		}

	    	}else{
	    		$this->_redirect('auth/index/login');
	    	}
        }
    }

    /**
     * @desc manda a mensagem para view
     */
    protected function _applyMessages()
    {
        if( !$this->_applyMessagesAfter && $this->_formSuccess){
            return;
        }
        if( $this->_messageBroker->hasMessages() ) {
            $this->view->messages = $this->_messageBroker->getMessageList();
        }
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
            if(!$this->_redirectDelete){
                $this->_helper->redirector->gotoSimple( 'index',
                                                        $this->getRequest()->getControllerName(),
                                                        $this->getRequest()->getModuleName());
            } else {
                $this->_redirect($this->_redirectDelete);
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::postDispatch()
     * @desc applica a mensagem depois que executar todas as funções necessária
     */
    public function postDispatch()
    {
        $this->_applyMessages();
        parent::postDispatch();
    }


    /**
     * @desc trata as mensagens para poder retornar no json.
     * @return array
     */
    protected function _mensagemJson()
    {
        $message = App_Validate_MessageBroker::getInstance();
        if($message->hasMessages()){
            $messageList = $message->getMessageList();
            return $messageList;
        }
    }

    /**
     * @desc irá receber o params term e passar os dados em json.
     */
    public function autocompleteAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocomplete($term);
        $this->_helper->json($list);
    }

    /**
     * @desc irá inicializar antes do formAction irá executar primeiro para
     * depois ir no formAction
     */
    protected function _initForm()
    {
    }

    /**
     * Salva o dado que vem via post por ajax e retorna um json
     */
    public function formAction()
    {
        $this->_initForm();
        $request         = $this->getAllParams();
        $object          = $this->_bo->get($this->_id);

        //Checando se registro possui workspace, se possuir ele valida se está no workspace correto, caso contrário redireciona para a action _redirectUnauthorizedWorkspace.
        if(isset($object->id_grupo) && !empty($object->id_grupo)){
            // não precisa.

//        	$workspaceBO 	= new Auth_Model_Bo_Workspace();

//        	if (!$workspaceBO->validateRegisterWithWorkspace($object->id_workspace)){
//        		$this->redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/{$this->_redirectUnauthorizedWorkspace}");
//        	}

        }

        // verificar se vem via post
        if($this->getRequest()->isPost()){
            try {
                $this->_bo->saveFromRequest($request, $object);
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array( 'success' => true, 'id' => $object->getPrimaryKey());
                    $this->_helper->json($response);
                }

                if(empty($this->_messageFormSuccess)){
                    if(empty($this->_id)){
                        App_Validate_MessageBroker::addSuccessMessage("Dado inserido com sucesso");
                    }else{
                        App_Validate_MessageBroker::addSuccessMessage("Dado atualizado com sucesso");
                    }
                }else{
                    App_Validate_MessageBroker::addSuccessMessage($this->_messageFormSuccess);
                }

                if($this->_redirectFormSuccess){
                    $this->_redirect($this->_redirectFormSuccess);
                }
            }
            catch (App_Validate_Exception $e){
                //verifica se e pelo ajax
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array('success' => false , 'mensagem' => $this->_mensagemJson());
                    $this->_helper->json($response);
                }
            }
            catch (Exception $e){
                //verifica se e pelo ajax
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array('success' => false, 'message'=>'Errado '.$e->getMessage() );
                    $this->_helper->json($response);
                }
                App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro inesperado. Entre em contato com o administrador.<br>ERROR: ".$e->getMessage());
            }
        }

        $this->view->vo = $object;
    }

    public function noRenderAndNoLayout(){

	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
    }

}