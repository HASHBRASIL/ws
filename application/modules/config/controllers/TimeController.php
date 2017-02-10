<?php
/**
 * HashWS
 */

/**
 * Gestão de times.
 *
 * @author Maykel S. Braz
 */
class Config_TimeController extends App_Controller_Action_Twig
{
    /**
     * Ao criar um novo time, vincule-o ao time atual.
     */
    const VINC_TIME_ATUAL = 0;
    /**
     * Ao criar um novo time, isole-o (cadastre-o sob o time associado ao produto selecionado)
     */
    const VINC_ISOLADO = 1;

    public function init() {
        $this->_translate = Zend_Registry::get('Zend_Translate');
        parent::init();
    }

    public function createAction()
    {
        $perfil = 'Novo time';
        $camposFormulario[$perfil][] = [
            'id' => 'time',
            'obrigatorio' => true,
            'nome' => $this->_translate->translate('nome_time'),
            'descricao' => $this->_translate->translate('nome_novo_time'),
            'metanome' => 'NOMETIME',
            'tipo' => 'text',
            'perfil' => $perfil,
        ];

        $camposFormulario[$perfil][] = [
            'id' => 'alias',
            'obrigatorio' => true,
            'nome' => $this->_translate->translate('alias'),
            'descricao' => $this->_translate->translate('alias_novo_time'),
            'metanome' => 'ALIASTIME',
            'tipo' => 'text',
        ];

        $camposFormulario[$perfil][] = [
            'id' => 'produto',
            'obrigatorio' => true,
            'nome' => $this->_translate->translate('produto'),
            'descricao' => $this->_translate->translate('produto_novo_usuario'),
            'metanome' => 'PRODUTOTIME',
            'tipo' => 'ref_itemBiblioteca',
            'items' => [
                ['id' => 'hash', 'valor' => $this->_translate->translate('hash')],
                ['id' => 'hpc', 'valor' => $this->_translate->translate('hpc')],
                ['id' => 'elegie', 'valor' => $this->_translate->translate('elegie')],
            ],
            'metadatas' => ['ws_style_object' => 'select2-skin']
        ];

        $camposFormulario[$perfil][] = [
            'id' => 'vinculacao',
            'obrigatorio' => true,
            'nome' => $this->_translate->translate('vinculacao'),
            'descricao' => $this->_translate->translate('descricao_vinculacao'),
            'metanome' => 'VINCULACAO',
            'tipo' => 'ref_itemBiblioteca',
            'items' => [
                ['id' => self::VINC_TIME_ATUAL, 'valor' => $this->_translate->translate('time_atual')],
                ['id' => self::VINC_ISOLADO, 'valor' => $this->_translate->translate('time_isolado')],
            ],
            'metadatas' => ['ws_style_object' => 'select2-skin']
        ];

        $this->view->file = 'form.html.twig';
        $this->view->data = [
            'perfis' => [$perfil],
            'campos' => $camposFormulario
        ];
    }

    public function insertAction()
    {
        try {
            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            $dadosNovoTime = $this->getRequest()->getPost();

            if (empty($dadosNovoTime['time_NOMETIME'])) {
                throw new App_Validate_Exception('time_nao_pode_ser_vazio');
            }
            if (empty($dadosNovoTime['alias_ALIASTIME'])) {
                throw new App_Validate_Exception('alias_nao_pode_ser_vazio');
            }
            if (!ctype_alnum($dadosNovoTime['time_NOMETIME'])) {
                throw new App_Validate_Exception('alias_carac_invalido');
            }
            if ((new Config_Model_Bo_GrupoMetadata())->findByAlias($dadosNovoTime['time_NOMETIME'])) {
                throw new App_Validate_Exception('alias_ja_usado');
            }

            $grupoBo = new Config_Model_Bo_Grupo();
            switch ($dadosNovoTime['vinculacao_VINCULACAO']){
                case self::VINC_TIME_ATUAL:
                    $idTimePai = $this->identity->time['id'];
                    break;
                case self::VINC_ISOLADO:
                    $idTimePai = current(
                        $grupoBo->getGrupoByMetanome(Config_Model_Bo_Grupo::META_GRUPO)
                    )['id'];
                    break;
                default:
                    throw new App_Validate_Exception('opcao_de_vinculacao_invalida');
            }

            // -- Cria o novo time
            $idTime = $grupoBo->criaTime(
                $this->identity->id,
                $idTimePai,
                $dadosNovoTime['time_NOMETIME'],
                $dadosNovoTime['alias_ALIASTIME']
            );

            // -- Salva o nome do produto escolhido
            (new Config_Model_Bo_Informacao())->addInformacao(
                $this->identity->id,
                Config_Model_Bo_TipoInformacao::META_PRODUTOINICIAL,
                $dadosNovoTime['produto_PRODUTOTIME']
            );

            // -- Chama o robo de configuração do workspace
            $filedir = Zend_Registry::getInstance()
                ->get('config')
                ->get('filedir');

            $url = $filedir->remoto . 'content/robo/configura-workspace/codigo/' . $this->identity->id . '/time/' . $idTime;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);

            $res = curl_exec($ch);
            curl_close($ch);

            Zend_Db_Table::getDefaultAdapter()->commit();

            $response = array(
                'success' => true,
                'msg' => $this->_translate->translate('aguarde_enquanto_configuramos'),
            );
        } catch (App_Validate_Exception $ex) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $response = [
                'success' => false,
                'msg' => $this->_translate->translate($ex->getMessage())
            ];
        }

        $this->_helper->json($response);
    }
}