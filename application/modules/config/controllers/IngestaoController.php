<?php
/**
 * HashWS
 */

/**
 * Gerencia as requisições de inscrição.
 *
 */
class Config_IngestaoController extends App_Controller_Action
{

    const CARGO_PREFEITO = "PREFEITO";
    const CARGO_VICEPREFEITO = "VICE-PREFEITO";

    /**
     * @var Zend_Session_Namespace Armazena dados da inscricação antes da persistência.
     */
    protected $session;

 	public function init()
    {
        parent::init();

        $this->_translate = Zend_Registry::get('Zend_Translate');

        $this->_helper->layout()->setLayout('publico');
        $this->session = new Zend_Session_Namespace('inscricao');
    }

    /**
     * configuração padrão para definir o uso de twig em todas as actions do controller
     * @todo pode-se criar um App_controller_Action_Twig para facilitar isso
     */
    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }

    public function indexAction()
    {
        $idIbPai = $this->getParam('codigo');

        $ibBo = new Content_Model_Bo_ItemBiblioteca();
        $dados = $ibBo->getValoresFilhosNomeados($idIbPai);

//        $ibBo->atualizastatuscand($idIbPai,'A');
        if (empty($dados)) {
            throw new App_Validate_Exception('ingestao_sem_dados');
        }

        $nomeGuerraOriginal = $nomeGuerra = $numero = $cpf = $email = $tibIdEmail = '';
        $nome = $sobrenome = '';
//x($dados);
        foreach ($dados AS $dado) {

            switch ($dado['metanome']) {
                case Content_Model_Bo_TpItemBiblioteca::META_NOMEGUERRA:
                    $nomeGuerraOriginal = $dado['valor'];
                    $nomeGuerra = $this->limparString(strtolower($dado['valor']));
                    break;
                case Content_Model_Bo_TpItemBiblioteca::META_NUMERO:
                    $numero = $dado['valor'];
                    break;
                case Content_Model_Bo_TpItemBiblioteca::META_CPF:
                    $this->session->cpf = $dado['valor'];
                    break;
                case Content_Model_Bo_TpItemBiblioteca::META_EMAIL:
//                    $this->session->email = $dado['valor'];
                    $this->session->email = 'maykelsbdev@gmail.com';
                    $tibIdEmail = $dado['id'];
                    break;
                case Content_Model_Bo_TpItemBiblioteca::META_NOME:
                    $nome = explode(' ', ucwords(strtolower($dado['valor'])));
                    $this->session->nome = current($nome);
                    $this->session->nome2 = end($nome);
                    break;
                case Content_Model_Bo_TpItemBiblioteca::META_IDPESSOA:
                    $this->session->idpessoa = $dado['valor'];
                    break;
            }
        }

        if (empty($nomeGuerra) || empty($numero)) {
            throw new App_Validate_Exception('ingestao_incompleta');
        }

        $this->session->usuario = $this->session->time = $this->session->alias
            = "{$nomeGuerra}{$numero}";
        $this->session->produto = 'hpc';

        // -- A criação deste e-mail para quando houver duplicidades está sendo avaliada
//        if ($ibBo->verificaDuplicidade($tibIdEmail, $this->session->email)) {
//            $this->session->email = "{$this->session->usuario}@hash.ws";
//        }

        $this->view->data = [
            'nome' => $nomeGuerraOriginal,
//            'formAction' => "/config/ingestao/confirmacao/codigo/{$idIbPai}",
            'formAction' => "/config/ingestao/confirmacao2/codigo/{$idIbPai}",
        ];
    }

    public function confirmacao2Action()
    {
        if (!$this->getRequest()->isPost()) {
            throw new App_Validate_Exception('ingestao_requisicao_invalida');
        }

        if (empty($this->getParam('servico')) || empty($this->getParam('privacidade'))) {
            throw new App_Validate_Exception('aceitar_antes_de_continuar');
        }

        $senha = $this->getParam('senha');
        $senha2 = $this->getParam('senha2');

        if (($senha != $senha2) || empty($senha)) {
            throw new App_Validate_Exception('As senhas informadas não conferem.');
        }

        // -- Criação do usuário, pessoa seus grupos e seus módulos inicias
        $pessoaBo = new Legacy_Model_Bo_Pessoa();

        // -- Cria pessoa, se necessário
        $idPessoa = $this->session->idpessoa;
        if (empty($idPessoa)) {
            $infoPessoa = Config_Model_Bo_TipoInformacao::META_EMAIL . "={$this->session->email}"
                . Config_Model_Bo_TipoInformacao::META_LEMBRETESENHA . "={$this->getParam('lembrete')}";

            list($idPessoa) = $pessoaBo->criar_usuario(
                $this->session->nome,
                $this->session->nome2,
                $this->session->usuario,
                $senha,
                $infoPessoa
            );

            $idPessoa = current($idPessoa)['criar_usuario'];
        }

        // -- salvar lembrete de senha
        $infoBo = new Config_Model_Bo_Informacao();
        $infoBo->addInformacao(
            $idPessoa,
            Config_Model_Bo_TipoInformacao::META_LEMBRETESENHA,
            $this->getParam('lembrete')
        );
        // -- Salva de acordos
        $infoBo->addInformacao(
            $idPessoa,
            Config_Model_Bo_TipoInformacao::META_CONCORDATPRIVACIDADE,
            'true'
        );
        $infoBo->addInformacao(
            $idPessoa,
            Config_Model_Bo_TipoInformacao::META_CONCORDATSERVICO,
            'true'
        );

        // -- Atualizar senha
        $usuarioBo = new Auth_Model_Bo_Usuario();
        list(, $salt, $senhaCriptografada) = $usuarioBo->criaPassword($senha);
        $usuarioBo->update([
            'salt' => $salt,
            'password_encrypted' => $senhaCriptografada
        ], $idPessoa);

        $url = $this->_helper->configuracao('filedir', 'remoto')
            . 'content/robo/ingestao-confirmacao2'
            . "/pessoa/{$idPessoa}"
            . "/ib/{$this->getParam('codigo')}"
            . "/nome/{$this->session->nome}"
            . "/nome2/{$this->session->nome2}"
            . "/time/{$this->session->time}"
            . "/alias/{$this->session->alias}"
            . "/usuario/{$this->session->usuario}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $res = curl_exec($ch);
        curl_close($ch);

        $this->view->file = 'configurando.html.twig';
        return;
    }

    public function confirmacaoAction()
    {
        Zend_Registry::set('TRANSACAO_INGESTAO', true);
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();

        if (!$this->getRequest()->isPost()) {
            throw new App_Validate_Exception('ingestao_requisicao_invalida');
        }

        if (empty($this->getParam('servico')) || empty($this->getParam('privacidade'))) {
            throw new App_Validate_Exception('aceitar_antes_de_continuar');
        }

        $senha = $this->getParam('senha');
        $senha2 = $this->getParam('senha2');

        if (($senha != $senha2) || empty($senha)) {
            throw new App_Validate_Exception('As senhas informadas não conferem.');
        }

        // -- Encontrando a imagem do candidato e formatando o cracha
        $idIbPai = $this->getParam('codigo');
        $ibBo = new Content_Model_Bo_ItemBiblioteca();
        $ibsCandidato = $ibBo->getFilhosByIdPai($idIbPai);

        $buscarPar = false;
        $cargo = null;

        foreach ($ibsCandidato as $item) {
            if($item['valor'] == self::CARGO_PREFEITO) {
                $buscarPar  = true;
                $cargo = self::CARGO_VICEPREFEITO;
            } elseif ($item['valor'] == self::CARGO_VICEPREFEITO){
               $buscarPar  = true;
               $cargo = self::CARGO_PREFEITO;
            } else {
                continue;
            }
        }

        $imagemCandidato = NULL;
        if ($buscarPar) {
            $par = (new Content_Model_Bo_Precampanha)->getParCandidatoSemColigacao($idIbPai, $cargo);
            if (count($par) > 0) {
                $parCandidato = current($par)['nomeguerra'];
            }
        }

        $tibBo = new Content_Model_Bo_TpItemBiblioteca();
        $tibIdCandidato = current($tibBo->getTipoByMetanome('TPINGCANDTSE')->toArray())['id'];
        $tibIdCracha = current($tibBo->getTipoByMetanome(Content_Model_Bo_TpItemBiblioteca::META_CRACHA)->toArray())['id'];

        $arrayCrachaFormatado = $this->formataIbsProCracha(
            $ibsCandidato,
            $tibIdCandidato,
            $tibIdCracha,
            $parCandidato,
            $imagemCandidato
        );

        try {

            $pessoaBo = new Legacy_Model_Bo_Pessoa();

            // -- Criando time e pessoa
            list(, $idPessoa, $idTime)
                = $pessoaBo->criaPessoaETime(
                    $this->session->usuario,
                    $this->session->email,
                    $this->session->time,
                    strtolower($this->session->alias),
                    $this->session->produto,
                    $this->session->cpf,
                    $imagemCandidato
                );

            // -- Salva a senha e lembrete
            $usuarioBo = new Auth_Model_Bo_Usuario();
            $dados = [];
            list(, $dados['salt'], $dados['password_encrypted'])
                = $usuarioBo->criaPassword($senha);
            $usuarioBo->update($dados, $idPessoa);

            (new Config_Model_Bo_Informacao())
                ->addInformacao(
                    $idPessoa,
                    'LEMBRETESENHA',
                    $this->getParam('lembrete')
                );

            // -- Criacao do cracha
            $idIbCrachaPai = $ibBo->insere(
                $tibIdCracha,
                $idPessoa,
                $arrayCrachaFormatado
            );

            // -- Instala o workspace
            (new Legacy_Model_Bo_Pessoa())
                ->salvaDeAcordoEInstalaModulos($idPessoa, $idTime);

            // -- encontra o grupo cracha
            $idGrupoCracha = current((new Config_Model_Bo_Grupo())
                ->getGrupoByTimeEMetanome($idTime, Config_Model_Bo_Grupo::META_CRACHA))['id'];

            // -- associa a nova ib ao grupo cracha
            (new Content_Model_Bo_RlGrupoItem())->relacionaItem($idGrupoCracha, $idIbCrachaPai);
            $rowset  = (new Legacy_Model_Bo_Grupo())->getSiteByCriador($idPessoa);

            $grupoMetaBo = new Config_Model_Bo_GrupoMetadata;
            foreach ($rowset as $row) {
                $idMetadata = $grupoMetaBo
                    ->listMetaByMetanome($row['id'], Config_Model_Bo_GrupoMetadata::META_CRACHA)
                    ->toArray();

                if ($idMetadata) {
                    $grupoMetaBo
                        ->updateMeta($row['id'], Config_Model_Bo_GrupoMetadata::META_CRACHA, $idIbCrachaPai);
                } else {
                    $grupoMetaBo
                        ->insere($row['id'], Config_Model_Bo_GrupoMetadata::META_CRACHA, $idIbCrachaPai);
                }
            }

            Zend_Db_Table::getDefaultAdapter()->commit();

            // -- Enviar o e-mail de ativação
            $this->emailAtivacao();

            // -- Redireciona para configuração do perfil
            $this->redirect("/config/ingestao/perfil/codigo/{$idPessoa}");
            die();

        } catch (Exception $ex) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();

            $this->_addMessageError($ex->getMessage());
            $this->redirect("/config/ingestao/index/codigo/{$this->getParam('codigo')}");
            die();
        }
    }

    /**
     * Faz a correspondência entre os metadados do crachá com os metadados da inscrição.
     *
     * IMPORTANTE: $imagemCandidato é um parâmetro de saída, pois o caminho da image
     * do candidato é utilizado no restante do processo de instalação além de ser
     * utilizado na criação do crachá.
     *
     * @param array $ibsCandidato Dados de ingestão do candidato
     * @param string $tibIdCandidato UUID do tib de ingestão do candidato
     * @param string $tibIdCracha UUID do tib do crachá
     * @param string $parCandidato Parceiro do candidato
     * @param string $imagemCandidato Caminho da imagem do candidato - parâmetro de saída
     * @return mixed[] Metadados do crachá já preparados para persistência
     */
    protected function formataIbsProCracha($ibsCandidato, $tibIdCandidato, $tibIdCracha, $parCandidato, &$imagemCandidato)
    {
        // -- Nomeando as tibs de ingestão
        $dadosTibCandidado = (new Content_Model_Bo_TpItemBiblioteca())
            ->getTipoByIdPai($tibIdCandidato)
            ->toArray();

        $tibsCandidato = [];
        foreach ($dadosTibCandidado as $tibCand) {
            $tibsCandidato[$tibCand['id']] = $tibCand['metanome'];
        }

        // -- Nomeando as tibs do crachá
        $dadosTibsCracha = (new Content_Model_Bo_TpItemBiblioteca())
            ->getTipoByIdPai($tibIdCracha)
            ->toArray();
        $tibsCracha = [];
        foreach ($dadosTibsCracha as $tibCracha) {
            $tibsCracha[$tibCracha['metanome']] = $tibCracha['id'];
        }

        // -- Associando as informações do crachá, com as informações de ingestão
        $dadosCandidato = [];
        foreach ($ibsCandidato as $ibCand) {
            $dadosCandidato[$tibsCandidato[$ibCand['id_tib']]] = $ibCand['valor'];
        }
        $imagemCandidato = $dadosCandidato['imagem'];
        $dadosCracha = [
            $tibsCracha['NOME'] => $dadosCandidato['nomeGuerra'],
            $tibsCracha['CARGO'] => $dadosCandidato['cargo'],
            $tibsCracha['PARTIDO'] => $dadosCandidato['partido'],
            $tibsCracha['ESTADO'] => $dadosCandidato['uf'],
            $tibsCracha['CIDADE'] => $dadosCandidato['cidade'],
            $tibsCracha['NUMERO'] => $dadosCandidato['numero'],
            $tibsCracha['IMGTOPO'] => 'IMGTOPO',
            $tibsCracha['IMGLOGO'] => 'IMGLOGO',
            $tibsCracha['FOTO'] => $dadosCandidato['imagem'],
            $tibsCracha['SIGLA'] => $dadosCandidato['partidoSigla'],
            $tibsCracha['NOMECOMPLETO'] => $dadosCandidato['nome'],
            $tibsCracha['CPF'] => $dadosCandidato['cpf'],
            $tibsCracha['SITUACAO'] => $dadosCandidato['situacao'],
            $tibsCracha['PARTCOLIG'] => $dadosCandidato['coligacaoPartidos'],
            $tibsCracha['NOMECOLIG'] => $dadosCandidato['coligacaoNome'],
            $tibsCracha['EMAIL'] => $dadosCandidato['email'],
            $tibsCracha['PARTIDONUM'] => $dadosCandidato['partidoNumero'],
            $tibsCracha['PARCANDIDATO'] => (!is_null($parCandidato)?$parCandidato:$dadosCandidato['parCandidato'])
        ];

        unset($dadosCracha[null]);
        return $dadosCracha;
    }

    public function perfilAction()
    {
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
        $idPessoa = $this->_request->getParam('codigo');
        $target = NULL;

        if (empty($idPessoa) &&  empty($id)) {
            $this->view->file = 'invalido.html.twig';
            return;
        } elseif (!empty($idPessoa) && (!(new Legacy_Model_Bo_Pessoa())->pessoaExiste($idPessoa))) {
            $this->view->file = 'invalido.html.twig';
            return;
        }

        $BoGrupo = new Legacy_Model_Bo_Grupo();
        $BoItem = new Content_Model_Bo_ItemBiblioteca();

        if (!empty($idPessoa)) {

            $rowset  = $BoGrupo->getSiteByCriador($idPessoa);

            foreach ($rowset as $row) {

                $idMetadata = (new Config_Model_Bo_GrupoMetadata)
                    ->listMetaByMetanome($row['id'], Config_Model_Bo_GrupoMetadata::META_CRACHA)
                    ->toArray();

                if ($idMetadata){
                    $idIbPai = current($idMetadata)['valor'];
                }
            }
        }

        $dados = $BoItem->getItemBibliotecaById($idIbPai);
        $dadosTratados = array();
        if($dados->count() > 0) {
            $dados->toArray();
            foreach ($dados as $item) {
                $tib = current((new Config_Model_Bo_Tib)->getById($item['id_tib']));

                if ('FOTO' == $tib['metanome']) {
                    $item['valor'] = "/uploaded/{$item['valor']}";
                }

                $dadosTratados[$tib['metanome']] = $item['valor'];
                $idPessoa = (!empty($idPessoa)) ? $item['id_criador'] : $idPessoa;
            }
        } else {
            $dadosTratados['NOME'] = 'Barack Obama';
            $dadosTratados['CARGO'] = 'Presidente';
            $dadosTratados['PARTIDO'] = 'Democrata';
            $dadosTratados['FOTO'] = '/images/obama_picture_profile.jpg';
        }

        $this->view->data = ['codigo' => $idPessoa, 'dadosCracha' => $dadosTratados, 'url' => $filedir->url , 'target' => $target];
        $this->view->file = 'perfil.html.twig';
    }

    public function finalizadoAction()
    {
        $this->view->data = [];
        $this->view->file = 'finalizado.html.twig';
    }

    /**
     *
     * @param type $texto
     * @return type
     * @todo Transformar em helper
     */
    protected function limparString($texto)
    {
        $texto = str_replace(
            ['á', 'à', 'ã', 'â', 'é', 'ẽ', 'ê', 'í', 'ó', 'õ', 'ô', 'ú', 'ç', ' ', '-'],
            ['a', 'a', 'a', 'a', 'e', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'c', '', ''],
            $texto
        );

        return str_replace(
            ['Á', 'À', 'Ã', 'Â', 'É', 'Ẽ', 'Ê', 'Í', 'Ó', 'Õ', 'Ô', 'Ú', 'Ç', ' ', '-'],
            ['A', 'A', 'A', 'A', 'E', 'E', 'E', 'I', 'O', 'O', 'O', 'U', 'C', '', ''],
            $texto
        );
    }

    protected function emailAtivacao()
    {
        $siteUrl = $this->_helper->configuracao('filedir', 'site');

        $baseUrl = <<<HTML
<base href="{$siteUrl}/transacional/001/">
HTML;

        $conteudoEmail = str_replace(
            ['%%TIME_HASH%%', '<!-- %%BASE_URL%% -->', '%%HASH_URL%%'],
            [$this->session->time, $baseUrl, $siteUrl],
            $this->_helper->conteudo('transacional/001/ativacao.html')
        );

        $this->_helper->email->sendEmailMailer(
            $this->session->email,
            'Seu HASH foi ativado!',
            $conteudoEmail,
            'HASH Team'
        );
    }
}
