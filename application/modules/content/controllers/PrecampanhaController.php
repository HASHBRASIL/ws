<?php

    class Content_PrecampanhaController extends App_Controller_Action_Twig
    {
        /**
        * @var Content_Model_Bo_Precampanha
        */
        
        const IB_MSGTMP = 'TPMSGTEMPLATE';

        protected $_bo;

        public function init()
        {
            parent::init();
            $this->_bo = new Content_Model_Bo_Precampanha();
            
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
        }

        public function gridAction() {
            
            $header = array();
            $header[] = array('campo' => 'nome', 'label' => 'Nome');
            $header[] = array('campo' => 'indicacao', 'label' => 'Indicação');
            $header[] = array('campo' => 'info', 'label' => $this->servico['metadata']['ws_label']);

            $this->_gridSelect = $this->_bo->getSelectGrid($this->identity->time['id'],$this->servico['metadata']['ws_campo']);

            $this->_countGridSelect = $this->_bo->getCountSelectGrid($this->identity->time['id'],$this->servico['metadata']['ws_campo']);

            $this->header = $header;

            parent::gridAction();

        }

        public function formcampanhaAction() {
            $modelTib = new Config_Model_Bo_Tib();
            $modelIb = new Content_Model_Bo_ItemBiblioteca();
            $post = $this->getRequest()->getParams();
            $tib = current($modelTib->getTipoItemBibliotecaByMetanome(self::IB_MSGTMP))['id'];
            $titulo = "";
            $mensagens = array();
            if (isset($post['id'])){
                $idPai = $this->getRequest()->getParam('id');
                $itens = $modelIb->getFilhosByIdPai($idPai);
                $template = $modelTib->getFilhosById($this->servico['id_tib'])->toArray();
    //          x($itens);
                foreach ($template as $itemTemplate) {
                    foreach ($itens as $item) {
                        if($itemTemplate['metanome'] == 'nome' && $item['id_tib'] == $itemTemplate['id']) {
                            $titulo = $item['valor'];
                        }
                        if($itemTemplate['metanome'] == 'preprocjson' && $item['id_tib'] == $itemTemplate['id']) {
                            $arrayMensagens = json_decode($item['valor']);
                            foreach ($arrayMensagens as $mensagem) {
                                $msgs = $modelIb->getFilhosByIdPai($mensagem->msg);
                                foreach ($msgs as $msg) {
                                    $retorno = current($modelTib->getTemplateById($msg['id_tib']));
                                    if($retorno['metanome'] == 'titulo'){
                                        $mensagens[$msg['id']] = $msg['valor'];
                                    }
                                }
                            }
                        }
                    }
    //                if ($itemTemplate == t)
                }
            }
            
            
            $arrayMsg = $modelIb->getIbByTibAndGrupo($tib, $this->_grupo);
    //        x($arrayMsg);
            
            $arrCampos = array();
            $perfil = 'Vínculo';
            $arrCampos['Vínculo'][0] = array(
                    'id'            => 'titulo',
                    'ordem'         => '0',
                    'obrigatorio'   => true,
                    'nome'          => 'Título da Campanha',
                    'descricao'     => 'Título da Campanha',
                    'metanome'      => 'titulo',
                    'valor'         => $titulo,
                    'tipo'          => 'text',
                    'perfil'        => 'Vínculo',
                    'metadatas'     => array(
                            'ws_ordemLista'         => '1',
                            'ws_style'              => 'col-md-4'
                    )
            );
            $arrCampos['Vínculo'][1] = array(
                    'id'            => 'tpmsg',
                    'ordem'         => '1',
                    'obrigatorio'   => true,
                    'multiple'      => 'multiple',
                    'nome'          => 'Tipo Mensagem',
                    'descricao'     => 'Tipo de Mensagem que será disparado o',
                    'metanome'      => 'TPMSG',
                    'tipo'          => 'ref_itemBiblioteca',
                    'perfil'        => 'Vínculo',
                    'items'     => $arrayMsg,
    //                'valor'         => $mensagens,
                    'metadatas'     => array(
                            'ws_ordemLista'         => '2',
                            'ws_style'              => 'col-md-4 select-multiple',
                            'ws_style_object'   => 'select2-skin'
                    )
            );        
            
            $this->view->file = 'form.html.twig';
            $this->view->data = array('perfis' => array($perfil),'campos' => $arrCampos);
        }


    }