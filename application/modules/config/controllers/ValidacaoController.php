<?php
/**
 * HashWS
 */

/**
 * Gerencia validacao de inscricao.
 */
class Config_ValidacaoController extends App_Controller_Action
{
    /**
     * @var Zend_Session_Namespace Armazena dados da validação da inscrição
     */
    protected $session;
    const CRACHA_METANOME = "TPCRACHACOMITE";
    const META_CRACHA = "cms_cracha";

     	public function init()
    {
        parent::init();

        $this->_translate = Zend_Registry::get('Zend_Translate');

        $this->_helper->layout()->setLayout('publico');
        $this->session = new Zend_Session_Namespace('validacao');
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

    public function cleanAction()
    {
        $this->session->unsetAll();
        $this->redirect('/config/validacao');
    }
    /**
     * As actions abaixo estão apenas para acessar pela rota.
     */
    public function invalidoAction()
    {
        $this->view->data = [];
        $this->view->file = 'invalido.html.twig';
    }

    public function termosAction()
    {
        $this->view->data = [
            "tipoinstalacao" => ($this->getRequest()->getParam('tipoinstacacao') == 'ativacao'
                ?'ativacao'
                :'instalacao')
        ];
        $this->view->file = 'termos.html.twig';
    }

    public function perfilAction()
    {
        $id = $this->getRequest()->getParam('id');
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
                $idMetadata = (new Config_Model_Bo_GrupoMetadata)->listMetaByMetanome($row['id'], self::META_CRACHA)->toArray();
                if ($idMetadata){
                    $idIbPai = current($idMetadata)['valor'];
                }
            }
        }

        if (!empty($id)) {
            $idServico = $this->getRequest()->getParam('servico');
            $servico = (new Config_Model_Bo_Servico())->getServico($idServico);
            $target = $servico['id_pai'];
            $idIbPai = $id;
        }

        $dados = $BoItem->getItemBibliotecaById($idIbPai);

        $dadosTratados = array();
        if($dados->count() > 0) {
            $dados->toArray();
            foreach ($dados as $item) {
                $tib = current((new Config_Model_Bo_Tib)->getById($item['id_tib']));
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

    public function updateimageAction()
    {
            $insert = true;
            $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

            $params   = $this->_request->getParams();

            $idPessoa = $this->_request->getParam('codigo');
            $this->session->codigo = $idPessoa;
            $BoGrupo = new Legacy_Model_Bo_Grupo();


            $time = current($BoGrupo->getTimeByCriador($idPessoa))['id'];

            $rowset  = $BoGrupo->getSiteByCriador($idPessoa);

            $arrayDados = array();
            if (isset($params['email'])) {
                $arrayDados['EMAIL'] = $params['email'];
            }
            if (isset($params['nome'])) {
                $arrayDados['NOME'] = $params['nome'];
            }
            if (isset($params['cargo'])) {
                $arrayDados['CARGO'] = $params['cargo'];
            }
            if (isset($params['partido'])) {
                $arrayDados['PARTIDO'] = $params['partido'];
            }
            if (isset($params['estado'])) {
                $arrayDados['ESTADO'] = $params['estado'];
            }
            if (isset($params['cidade'])) {
                $arrayDados['CIDADE'] = $params['cidade'];
            }
            if (isset($params['numero'])) {
                $arrayDados['NUMERO'] = $params['numero'];
            }
            if (isset($params['sigla'])) {
                $arrayDados['SIGLA'] = $params['sigla'];
            }
            if (isset($params['nome_completo'])) {
                $arrayDados['NOMECOMPLETO'] = $params['nome_completo'];
            }
            if (isset($params['cpf'])) {
                $arrayDados['CPF'] = $params['cpf'];
            }
            if (isset($params['partidos_coligacao'])) {
                $arrayDados['PARTCOLIG'] = $params['partidos_coligacao'];
            }
            if (isset($params['nome_coligacao'])) {
                $arrayDados['NOMECOLIG'] = $params['nome_coligacao'];
            }
            if (isset($params['numero_partido'])) {
                $arrayDados['PARTIDONUM'] = $params['numero_partido'];
            }
            if (isset($params['par_candidato'])) {
                $arrayDados['PARCANDIDATO'] = $params['par_candidato'];
            }

            if (isset($params['imagem'])) {
                $grupo = current($BoGrupo->getGrupoByMetanome('MEDIA',$idPessoa))['id'];

                $newFolder  =   $filedir->path . $time . '/';
                $retorno    =   $time . '/';
                if ( !file_exists($newFolder) ){
                    mkdir( $newFolder, 0755 );
                }

                $newFolder  =   $newFolder . $grupo . '/';
                $retorno    =   $retorno . $grupo . '/';
                if ( !file_exists($newFolder) ){
                    mkdir( $newFolder, 0755 );
                }

                $file = explode(",",$params['imagem']);
                $lixo = explode (";",$file[0]);
                $extensao = explode('/',$lixo[0]);

                $imagem = $retorno . UUID::v4() .  '.' . $extensao[1];

                $ifp = fopen( $filedir->path . $imagem, "wb" );
                fwrite( $ifp, base64_decode( $file[1]) );
                fclose( $ifp );
                $arrayDados['FOTO'] = $imagem;
            }

            $idTib = current((new Config_Model_Bo_Tib)->getByMetanome(self::CRACHA_METANOME))['id'];

            $ib_bo   = new Content_Model_Bo_ItemBiblioteca();

            foreach ($rowset as $row) {
                $idMetadata = (new Config_Model_Bo_GrupoMetadata)->listMetaByMetanome($row['id'], self::META_CRACHA)->toArray();
                if ($idMetadata){
                    $idIbPai = current($idMetadata)['valor'];
                    $insert = false;
                }
            }

            $idGrupo = current($BoGrupo->getGrupoByMetanome('CMSGRUPOCRACHA',$idPessoa))['id'];

            if ($insert == true) {
                $cracha  = $ib_bo->insere( $idTib, $idPessoa,$arrayDados );
                $ib_bo->addRelGrupoItem($idGrupo, $cracha);

                foreach ($rowset as $row) {
                    $idMetadata = (new Config_Model_Bo_GrupoMetadata)->listMetaByMetanome($row['id'], self::META_CRACHA)->toArray();
                    if ($idMetadata) {
                        (new Config_Model_Bo_GrupoMetadata)->updateMeta($row['id'], self::META_CRACHA, $cracha);
                    } else {
                        (new Config_Model_Bo_GrupoMetadata)->insere($row['id'], self::META_CRACHA, $cracha);
                    }
                }

            } else {
                $cracha  = $ib_bo->atualiza($idIbPai, $idTib, $idPessoa,$arrayDados );
            }
          die;
    }

    public function finalizadoAction(){
        $this->view->data = [];
        $this->view->file = 'finalizado.html.twig';
    }

    public function configurandoAction()
    {
        $this->view->data = [];
        $this->view->file = 'configurando.html.twig';
    }

    public function indexAction()
    {
        $idPessoa = $this->_request->getParam('codigo');
        $this->view->data = [
            'codigo' => $idPessoa,
            'nome' => ucfirst(strtolower(current((new Content_Model_Bo_ItemBiblioteca())
                ->getValorByCriadorEMetanome($idPessoa, 'NOME', 'TPCRACHACOMITE'))['valor']))
        ];

        if (empty($idPessoa)
            || (!(new Legacy_Model_Bo_Pessoa())->pessoaExiste($idPessoa))
        ) {
            $this->view->file = 'invalido.html.twig';
            return;
        }

        if ($this->_request->isPost()){

            if ($this->getParam('servico') && $this->getParam('privacidade')) {
                if ($this->_request->getParam('tipoinstalacao') == 'instalacao') {
                    $filedir = Zend_Registry::getInstance()
                        ->get('config')
                        ->get('filedir');

                    $url = $filedir->remoto . $this->view->url([
                        'module'     => 'content',
                        'controller' => 'robo',
                        'action'     => 'configura-workspace',
                    ]);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

                    $res = curl_exec($ch);
                    curl_close($ch);

                    $this->view->file = 'configurando.html.twig';
                    return;
                } else {

                    $senha = $this->getParam('senha');
                    $senha2 = $this->getParam('senha2');

                    if (($senha != $senha2) || empty($senha)) {
                        $this->_addMessageError('As senhas informadas não conferem.');
                        $this->view->file = 'termos.html.twig';
                        return;
                    }
                    // -- Gerando e armazenando a nova senha
                    $usuarioBo = new Auth_Model_Bo_Usuario();
                    $dados = [];
                    list(, $dados['salt'], $dados['password_encrypted'])
                        = $usuarioBo->criaPassword($senha);
                    $usuarioBo->update($dados, $idPessoa);

                    // -- Armazenando também o lembrete da senha
                    (new Config_Model_Bo_Informacao())
                        ->addInformacao(
                            $idPessoa,
                            'LEMBRETESENHA',
                            $this->getParam('lembrete')
                        );

                    // -- Consultando o e-mail da pessoa para notificação
                    $emailPessoa = current((new Config_Model_Bo_Informacao)
                        ->getInfoPessoaByMetanome(
                            $idPessoa,
                            Config_Model_Bo_TipoInformacao::META_EMAIL
                        ))['valor'];

                    $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
                    $opcoes = $config->getOption('filedir');
                    $url = $opcoes['site'];

                    $urlValidacao = $url . $this->view->url([
                        'module' => 'config',
                        'controller' => 'validacao',
                        'action' => 'perfil',
                        'codigo' => $idPessoa
                    ]);
                $destinatarios = ['diretoria@titaniumtech.com.br', 'felipe@titaniumtech.com.br'];
        $assuntoEmail = 'Ativação da Imagem do Perfil';
        $conteudoEmail = <<<TEXT
<h1>Já validamos seu aceite!</h1>
Acesse agora e mude suas Informações: {$urlValidacao}
TEXT;
                    $this->_helper->campanha([
                            'assunto' => $this->_translate->translate($assuntoEmail),
                            'mensagem' => $conteudoEmail
                        ], $destinatarios
                    );
                }

                $this->redirect("/config/validacao/perfil/codigo/{$idPessoa}");
                die();
            }
            $this->_addMessageError('aceitar_antes_de_continuar');
        }

        $this->view->file = 'termos.html.twig';
    }

    /**
     * Action usada pela primeira versão do instalador.
     *
     * Ele recebe um id pessoa para consultar os dados, um par de senhas e a
     * confirmação por parte do usuário sobre os termos de serviço e compromisso.
     *
     * @return type
     */
    public function index2Action()
    {
        $idPessoa = $this->_request->getParam('codigo');
        $this->view->data = [
            'codigo' => $idPessoa,
        ];

        if (empty($idPessoa)
            || (!(new Legacy_Model_Bo_Pessoa())->pessoaExiste($idPessoa))
        ) {
            $this->view->file = 'invalido.html.twig';
            return;
        }

        if ($this->_request->isPost()){

            if ($this->getParam('servico') && $this->getParam('privacidade')) {

                $senha = $this->getParam('senha');
                $senha2 = $this->getParam('senha2');

                if (($senha != $senha2) || empty($senha)) {
                    $this->_addMessageError('As senhas informadas não conferem.');
                    $this->view->file = 'termos.html.twig';
                    return;
                }
                // -- Gerando e armazenando a nova senha
                $usuarioBo = new Auth_Model_Bo_Usuario();
                $dados = [];
                list(, $dados['salt'], $dados['password_encrypted'])
                    = $usuarioBo->criaPassword($senha);
                $usuarioBo->update($dados, $idPessoa);

                $filedir = Zend_Registry::getInstance()
                    ->get('config')
                    ->get('filedir');

                $url = $filedir->remoto . $this->view->url([
                    'module'     => 'content',
                    'controller' => 'robo',
                    'action'     => 'configura-workspace',
                ]);

                // -- Chamando o robo de instalação de produtos
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 1);

                $res = curl_exec($ch);
                curl_close($ch);

                $this->view->file = 'configurando.html.twig';
                return;
            }
            $this->_addMessageError('aceitar_antes_de_continuar');
        }

        $this->view->file = 'termos.html.twig';
    }

    public function dashboardAction() {
        $BoGrupo = new Config_Model_Bo_Grupo();
        $times = $BoGrupo->getTimesImportados();

        $this->view->data = ['quantidade' => count($times), 'times' => $times];
        $this->view->file = 'dashboard.html.twig';
    }
}
