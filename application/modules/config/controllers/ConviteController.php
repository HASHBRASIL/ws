<?php
class Config_ConviteController extends App_Controller_Action_Twig
{
    /**
     * @var Config_Model_Bo_Convite
     */
    protected $_bo;
    public function init()
    {
        
        $this->_bo = new Config_Model_Bo_Convite();
        $objGrupo = new Config_Model_Bo_Grupo();

        if (isset($this->servico['id_grupo'])) {
            $this->_grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])) {

            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);

            if (!empty($grupos)) {
                $this->_grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $this->_grupo = $this->identity->grupo['id'];
        }
        
        parent::init();
    }

    public function gridpendenteAction() {
        
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $header = array();
        $header[] = array('campo' => 'convidada', 'label' => 'Pessoa Convidada');
        $header[] = array('campo' => 'responsavel', 'label' => 'Indicado por');
        $header[] = array('campo' => 'aceitegrupo', 'label' => 'Foi Aceito Pelo Time?');
        $header[] = array('campo' => 'aceitepessoa', 'label' => 'Já Aceitou o Convite?');
        
        
        $this->header = $header;
        
        $select = $this->_bo->getConvitesAprovacaoTimeGrid($this->identity->time['id']);
        
        $this->_gridSelect = $select;
        $this->view->filedir = $filedir;

        parent::gridAction();
    }

    public function aceitaAction() {
        $id = $this->getRequest()->getParam('id');
        $status = true;
        $this->_bo->mudaStatusConvite($id, $status);
        
        $modelServico = new Config_Model_Bo_Servico();
        $servico = $modelServico->getServicoByMetanome($this->servico['metadata']['ws_target']);
        $servicoDestino = current($servico)['id'];
        
        $response = array(
            'success' => 'true',
            'msg' => $this->_translate->translate("Aceito com sucesso!"),
            'data' => array('target' => array('servico' => $servicoDestino))
        );
        $this->_helper->json($response);
    }
    
    public function aceitemultAction() {
        
        $checkboxs = $this->getRequest()->getParam('checkbox');
        
        foreach ($checkboxs as $idCheck) {
            $check = explode("_", $idCheck);
            $status = 'true';
            $this->_bo->mudaStatusConvite($check[1], $status);
        }
        
        $modelServico = new Config_Model_Bo_Servico();
        $servico = $modelServico->getServicoByMetanome($this->servico['metadata']['ws_target']);
        $servicoDestino = current($servico)['id'];
        
        $response = array(
            'success' => 'true',
            'msg' => $this->_translate->translate("Todos selecionados aceitos com sucesso!"),
            'data' => array('target' => array('servico' => $servicoDestino))
        );
        $this->_helper->json($response);
    }
    
    public function rejeitaAction() {
        $id = $this->getRequest()->getParam('id');     
        $status = 'false';
        $this->_bo->mudaStatusConvite($id, $status);
       
        $modelServico = new Config_Model_Bo_Servico();
        $servico = $modelServico->getServicoByMetanome($this->servico['metadata']['ws_target']);
        $servicoDestino = current($servico)['id'];
       
        $response = array(
            'success' => true,
            'msg' => $this->_translate->translate("Rejeitado com sucesso!"),
            'data' => array('target' => array('servico' => $servicoDestino))
        );
        $this->_helper->json($response);
    }
    
    public function rejeitamultAction() {
        $checkboxs = $this->getRequest()->getParam('checkbox');
        
        foreach ($checkboxs as $idCheck) {
            $check = explode("_", $idCheck);
            $status = 'false';
            $this->_bo->mudaStatusConvite($check[1], $status);
        }
        
        $modelServico = new Config_Model_Bo_Servico();
        $servico = $modelServico->getServicoByMetanome($this->servico['metadata']['ws_target']);
        $servicoDestino = current($servico)['id'];
        
        $response = array(
            'success' => 'true',
            'msg' => $this->_translate->translate("Todos selecionados rejeitados com sucesso!"),
            'data' => array('target' => array('servico' => $servicoDestino))
        );
        $this->_helper->json($response);
    }
    
}