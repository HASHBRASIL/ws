<?php
/**
 * HashWS
 */

/**
 * Controller de gestão de serviço.
 *
 * @author Maykel S. Braz
 */
class Content_ServicoController extends App_Controller_Action_Twig
{
    public function manageAction()
    {
        $idServico = $this->getRequest()->getParam('idServico');
        $servicos = (new Config_Model_Bo_Servico())->getServicosFilhos(null);
        array_unshift($servicos, ['id' => 'todos', 'nome' => 'TODOS']);

        $this->view->data = [
                'idServico' => $idServico,
                'servicos' => $servicos,
        ];
    }

    public function listTreeAction()
    {
        $this->identity  = Zend_Auth::getInstance()->getIdentity();
        $idServico = $this->_request->getParam('idServico');
        $this->bo = new Config_Model_Bo_Servico();

        $arvore = [];

        $pai = '#';
        if ('todos' == $idServico) {
            $idServico = null;

            $arvore[] = [
                'id' => 'pai',
                'parent' => '#',
                'text' => 'TODOS',
                'type' => 'pai',
            ];

            $pai = 'pai';
        }

        foreach ($servicos = (new Config_Model_Bo_Servico())->getServicosFilhos($idServico, true) as $servico) {
            $arvore[] = [
                'id' => $servico['id'],
                'parent' => empty($servico['id_pai'])?$pai:$servico['id_pai'],
                'text' => empty($servico['nome'])?'S/N':$servico['nome'],
                'type' => $servico['id_pai']
            ];
        }

        $this->_helper->json($arvore);
    }

    public function saveTreeAction()
    {
        try {
            $idPai = $this->getRequest()->getParam('idPai');
            if (empty($idPai)) {
                throw new App_Validate_Exception('novo_servico_id_pai_vazio_exception');
            }

            $dadosRequisicao = $this->getRequest()->getParams();
            $dados = [
                'id' => $dadosRequisicao['uuid'],
                'id_pai' => $dadosRequisicao['idPai'],
                'ordem' => 1 + (int)$dadosRequisicao['pos']
            ];

            if (isset($dadosRequisicao['nome'])) {
                $dados['nome'] = $dadosRequisicao['nome'];
            }

            if (isset($dadosRequisicao['descricao'])) {
                $dados['descricao'] = $dadosRequisicao['descricao'];
            }

            if (isset($dadosRequisicao['metanome'])) {
                $dados['metanome'] = $dadosRequisicao['metanome'];
            }

            if (isset($dadosRequisicao['rota'])) {
                $dados['rota'] = $dadosRequisicao['rota'];
            }

            if (isset($dadosRequisicao['visivel'])) {
                $dados['visivel'] = $dadosRequisicao['visivel'];
            }

            $servicoBo = new Config_Model_Bo_Servico();
            $dados = $servicoBo->salvarServico($dados);

            $this->_helper->json(['uuid' => $dados['id'], 'msg' => '']);
        } catch (App_Validate_Exception $avex) {

            $this->_helper->json(array('msg' => $avex->getMessage()));
        }
    }

    public function deleteTreeAction()
    {
        try {
            $uuid = $this->getRequest()->getParam('uuid');
            if (empty($uuid)) {
                throw new App_Validate_Exception('novo_servico_uuid_vazio');
            }

            // -- Apagando permissões do serviço
            (new Config_Model_Bo_RlPermissaoPessoa())
                ->apagaPermissoesDoServico($uuid);

            // -- Apagando metadados do serviço
            (new Config_Model_Bo_ServicoMetadata())
                ->apagaMetadadosDoServico($uuid);

            (new Config_Model_Bo_Servico())
                ->delServico($uuid);

            $this->_helper->json(['msg' => '']);
        } catch (App_Validate_Exception $avex) {

            $this->_helper->json(array('msg' => $avex->getMessage()));
        }
    }

    public function showPropTreeAction()
    {
        $this->_helper->layout->disableLayout();
        $retorno = array();
        try {
            $retorno['id'] = $id = $this->getRequest()->getParam('uuid');

            if (empty($id)) {
                throw new App_Validate_Exception('novo_servico_uuid_vazio');
            }

            $servicoBo = new Config_Model_Bo_Servico();
            $dados = $servicoBo->getServico($id);

            $campos = ['descricao', 'fluxo', 'metanome', 'nome', 'visivel', 'ordem', 'rota'];
            $camposReadonly = ['id', 'id_pai', 'dt_inclusao', 'dtype'];

            foreach($campos as $campo){
                $retorno['itens'][$campo] = $dados[$campo];
            }

            foreach($camposReadonly as $campo){
                $retorno['itensReadonly'][$campo] = $dados[$campo];
            }

            $servicoMetaBo = new Config_Model_Bo_ServicoMetadata();
            $dadosFiltros = current($servicoMetaBo
                    ->getByMetanome($id, Config_Model_Bo_ServicoMetadata::METANOME_FILTRO)
            );

            $metadados = [];
            foreach ($servicoMetaBo->getByServico($id) as $metadado) {
                if (Config_Model_Bo_ServicoMetadata::METANOME_FILTRO == $metadado['metanome']) {
                    continue;
                }

                $metadados[] = [
                    'campo' => $metadado['metanome'],
                    'valor' => $metadado['valor'],
                    'id' => $metadado['id']
                ];
            }

            $retorno['id_metanome_filtro'] = $dadosFiltros['id'];
            $retorno['filtros'] = json_decode($dadosFiltros['valor']);
            $retorno['metadados'] = $metadados;
        } catch (App_Validate_Exception $avex) {

            $this->_helper->json(['msg' => $avex->getMessage()]);
        }

        $this->view->data = $retorno;
    }

    public function savePropTreeAction()
    {
        try {
            if (!($idServico = $this->getRequest()->getParam('id'))) {
                throw new App_Validate_Exception('novo_servico_uuid_vazio');
            }

            $dadosRequisicao = $this->getRequest()->getParams();
            $campos = ['descricao', 'fluxo', 'metanome', 'nome', 'visivel', 'ordem', 'rota'];

            if (!(in_array($dadosRequisicao['coluna'], $campos))) {
                throw new App_Validate_Exception('novo_servico_coluna_inexistente');
            }

            $dados = ['id' => $idServico];
            $dados[$dadosRequisicao['coluna']] = $dadosRequisicao['valor'];

            $servicoBo = new Config_Model_Bo_Servico();
            $dados = $servicoBo->salvarServico($dados);

            $this->_helper->json(['uuid' => $dados['id'], 'msg' => '']);
        } catch (App_Validate_Exception $avex) {

            $this->_helper->json(['msg' => $avex->getMessage()]);
        }
    }

    public function addFiltroTreeAction()
    {
        try {
            $dados = [];

            if (!($dados['id_servico'] = $this->getRequest()->getParam('iditem'))) {
                throw new App_Validate_Exception('novo_servico_uuid_vazio');
            }

            $dados['id'] = $this->getRequest()->getParam('id');
            $dados['valor'] = json_encode($this->getRequest()->getParam('wsfiltro'));
            $dados['metanome'] = Config_Model_Bo_ServicoMetadata::METANOME_FILTRO;

            $servicoMetaBo = new Config_Model_Bo_ServicoMetadata();
            $dados = $servicoMetaBo->salvarMetanome($dados);

        } catch (App_Validate_Exception $avex) {

            $this->_helper->json(['msg' => $avex->getMessage()]);
        }

        $this->_helper->json(['id' => $dados['id'], 'msg' => '']);
    }

    public function copiaTreeAction()
    {
        try {
            Zend_Db_Table::getDefaultAdapter()->beginTransaction();

            (new Config_Model_Bo_Servico())->copiarServico(
                $this->getRequest()->getParam('copiado'),
                $this->getRequest()->getParam('colado')
            );

            Zend_Db_Table::getDefaultAdapter()->commit();
        } catch (Exception $ex) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->_helper->json(array('msg' => $ex->getMessage()));
        }

        $this->_helper->json(['msg' => '']);
    }

    public function addMetaTreeAction()
    {
        $retorno = ['msg' => ''];
        try {
            $dados = [];
            if (!($dados['id_servico'] = $this->getRequest()->getParam('iditem'))) {
                throw new App_Validate_Exception('novo_servico_uuid_vazio');
            }

            $servicoMetaBo = new Config_Model_Bo_ServicoMetadata();
            foreach ($this->getRequest()->getParam('metadados') as $metadado) {
                $dados['id'] = $metadado['id'];
                $dados['valor'] = $metadado['valor'];
                $dados['metanome'] = $metadado['campo'];

                $servicoMetaBo->salvarMetanome($dados);
            }
        } catch (Exception $ex) {
            $retorno['msg'] = $ex->getMessage();
        }

        $this->_helper->json($retorno);
    }

    public function remMetaTreeAction()
    {
        $retorno = ['msg' => ''];

        try {
            // -- Apagando metadados do serviço
            (new Config_Model_Bo_ServicoMetadata())
                ->apagaMetadado($this->getRequest()->getParam('idmetadado'));

        } catch (Exception $ex) {
            $retorno['msg'] = $ex->getMessage();
        }

        $this->_helper->json($retorno);
    }
}
