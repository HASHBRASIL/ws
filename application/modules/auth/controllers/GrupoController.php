<?php

class Auth_GrupoController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Auth_Model_Bo_Workspace
     */
    protected $_bo;
    protected $_aclActionAnonymous = array("register-workspace", 'autocomplete', 'get');

    public function init()
    {
        parent::init();
//    	$this->_redirectDelete = ("/auth/workspace/grid");
//        $this->_bo = new Auth_Model_Bo_Workspace();
        $this->_helper->layout()->setLayout('metronic');
        $this->_id = $this->getRequest()->getParam('id_workspace');

    }

    public function changeTimeAction()
    {
        $this->_helper->layout->disableLayout();
        $identity = Zend_Auth::getInstance()->getIdentity();

        $idTime = $this->getParam('id');

        if ($identity->times[$idTime]) {
            $identity->time = $identity->times[$idTime];
            $time = $identity->times[$idTime];

            $idServicoModulo = $time['modulos'][0]['id_servico'];

            $identity->modulo = $identity->servicos[$idServicoModulo];

            $servicosAtual = array();

            foreach ($identity->servicos as $key => $servico) {

                if (array_key_exists($key, $identity->permission[$time['id']]) && ($identity->permission[$time['id']][$key] > date('Y-m-d'))) {
                    $servicosAtual[$key] = $servico;
                } else {
                    unset($servicosAtual[$servico['id_pai']]['filhos'][$key]);
                }
            }

            $grupoBo = new Legacy_Model_Bo_Grupo();
            $rsGrupos = $grupoBo->getGruposId($identity->id, $time['id']);

            $grupos = array();
            foreach ($rsGrupos as $k => $rowGrupo) {
                $grupos[$rowGrupo['id']] = $rowGrupo;
            }

            $identity->grupos = $grupos;
            $identity->grupo = current($identity->grupos);

            $identity->servicosAtual = $servicosAtual;

            $times = $grupoBo->getGroupList($time['id']);
            $listaGrupos = $grupoBo->getGroups($times);
            $identity->timesColigados = $listaGrupos;
        } else {
            throw new Exception('Time selecionado não encontrado');
        }

        $this->_helper->json(array("success" => true));
    }


    public function changeGrupoAction()
    {
        $this->_helper->layout->disableLayout();
        $identity = Zend_Auth::getInstance()->getIdentity();

        $idGrupo = $this->getParam('id');

        if (array_key_exists($idGrupo, $identity->grupos)) {
            $identity->grupo = $identity->grupos[$idGrupo];
        } else {
            throw new Exception('Grupo selecionado não encontrado');
        }

        $this->_helper->json(array("success" => true));
    }


    public function changeModuleAction()
    {
        $this->_helper->layout->disableLayout();
        $identity = Zend_Auth::getInstance()->getIdentity();

        $idModulo = $this->getParam('id');

        if (array_key_exists($idModulo, $identity->permission[$identity->time['id']])) {
            if ($identity->permission[$identity->time['id']][$idModulo] < date('Y-m-d')) {
                throw new Exception('Data de validade expirada');
            }
        }
        $identity->modulo = $identity->servicos[$idModulo];

        $this->_helper->json(array("success" => true));
    }

    public function registerWorkspaceAction(){

        $this->noRenderAndNoLayout();

        $workspace = $this->getAllParams();

        $workspaceSession = new Zend_Session_Namespace('workspace');
        $proprietarioSession = new Zend_Session_Namespace('proprietario');

        $workspaceSession->unsetAll();
        $proprietarioSession->unsetAll();

        $workspaceObj = $this->_bo->get($workspace["id"]);

        $workspaceSession->id_workspace = $workspaceObj->id_workspace;
        $workspaceSession->name_workspace = $workspaceObj->nome;
        $workspaceSession->free_access = $workspaceObj->free_access;

        $proprietarioBo = new Sis_Model_Bo_Sis();
        $proprietario = $proprietarioBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, 'id_workspace = ?' => $workspaceSession->id_workspace))->current();
        $proprietarioSession->proprietario = $proprietario;

        $this->_helper->json(array("success" => true));

    }

}

