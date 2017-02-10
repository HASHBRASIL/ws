<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vincularcracha
 *
 * @author Felipe Lino <felipe@titaniumtech.com.br>
 */
class Cms_VincularcrachaController extends App_Controller_Action_Twig
{
    /**
     * @var Content_Model_Bo_Ib
     */
    protected $_bo;

    public function init()
    {
        parent::init();
        $this->_bo = new Content_Model_Bo_ItemBiblioteca();
        $objGrupo = new Config_Model_Bo_Grupo();

        if (isset($this->servico['id_grupo'])){
            $this->_grupo = $this->servico['id_grupo'];
            
        } elseif (isset($this->servico['metadata']['ws_grupo'])){
            
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'],$this->servico['metadata']['ws_grupo']);
//            x($this->servico['metadata']['ws_grupo']);
            if(!empty($grupos)){
                $this->_grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $this->_grupo = $this->identity->grupo['id'];
            
        }

    }
    
    public function vincularAction(){
        $idItemBiblioteca = $this->getRequest()->getParam('id');
        $modelServico = new Config_Model_Bo_Servico();
        $modelGrupo = new Config_Model_Bo_Grupo();
        if (isset($this->servico['id_grupo'])){
            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])){
            $grupos = $modelGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'],$this->servico['metadata']['ws_grupo']);
            if(!empty($grupos)){
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }
        $arrayTimes = $modelGrupo->getGrupoByTime($this->identity->time['id']);
//        x($arrayTimes);
        $arrCampos = array();
        $perfil = 'Vínculo';
        $arrCampos['Vínculo'][0] = array(
                'id'            => 'site',
                'ordem'         => '0',
                'obrigatorio'   => true,
                'multiplo'      => false,
                'nome'          => 'Site',
                'descricao'     => 'Site a ser vinculado o Crachá',
                'metanome'      => 'CMSCRACHA',
                'tipo'          => 'ref_itemBiblioteca',
                'perfil'        => 'Vínculo',
                'items'		=> $arrayTimes,
                'metadatas'     => array(
                        'ws_ordemLista'         => '1',
                        'ws_style'              => 'col-md-4',
                        'ws_style_object'	=> 'select2-skin'
                )
        );
//        x($arrCampos);
        $this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => array($perfil),'campos' => $arrCampos, 'id' => $idItemBiblioteca);
    }
    
    public function salvarvinculoAction(){
        
        $modelServico = new Config_Model_Bo_Servico();
        $modelGrupo = new Config_Model_Bo_Grupo();
        $modelGrupoMetadata = new Config_Model_Bo_GrupoMetadata();
        if (isset($this->servico['id_grupo'])){
            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])){
            $grupos = $modelGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'],$this->servico['metadata']['ws_grupo']);
            if(!empty($grupos)){
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }
        
        $idCracha = $this->getRequest()->getParam('id');
        $idSite = $this->getRequest()->getParam('site_CMSCRACHA');
        $metanome = 'cms_cracha';
        $modelGrupoMetadata->updateMeta($idSite, $metanome, $idCracha);
        
        $servico = $modelServico->getServicoByMetanome($this->servico['metadata']['ws_target']);
        $servicoDestino = current($servico)['id'];
        
        $response = array(
            'success' => true,
            'msg' => $this->_translate->translate("Vinculado com Sucesso!"),
            'data' => array('target' => array('servico' => $servicoDestino))
        );
        $this->_helper->json($response);
    }
}
