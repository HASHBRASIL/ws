<?php

    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 17/12/15
     * Time: 23:52
     */
    class Legacy_Model_Bo_Pessoa extends App_Model_Bo_Abstract
    {
        /**
         * @var Legacy_Model_Dao_Pessoa
         */
        public $_dao;

        public $fields =  array(
            'nome'              => 'Nome',
            'nome2'         => 'Sobrenome'
        );

        public function __construct()
        {
            $this->_dao = new Legacy_Model_Dao_Pessoa();
            parent::__construct();
        }

        public function get($id = null)
        {
            return $this->_dao->get($id);
        }

        public function getGrupoPairs(
            $ativo = true,
            $chave = null,
            $valor = 'nome',
            $ordem = null,
            $limit = null
        ) {
            $rowset = $this->_dao->getPessoaByGrupo();
            $data = array();
            foreach ($rowset as $row) {
                $data[$row['id_representacao']] = $row['nome'];
            }

            return $data;
//            return $this->_dao->fetchPairsGrupo($chave, $valor, $ordem, $limit);
        }


        public function getAutocompleteEmpresa($term, $limit = 10, $page = 0, $chave = null, $valor = null,
            $ordem = null, $ativo = false
        ) {
            return $this->_dao->getAutocomplete($term, $limit, $page, $chave, $valor, $ordem, $ativo);

        }

        public function getAutocomplete($term, $limit = 10, $page = 0, $chave = null, $valor = null, $ordem = null, $ativo = false) {

            $rowset = $this->_dao->getAutocomplete($term, $limit, $page, $chave, $valor, $ordem, $ativo, $strTime);
            $data['results'] = array();
            foreach ($rowset as $row) {
                $data['results'][] = array('id' => $row['id'], 'text' => $row['text']);
            }

            $data['pagination']['more'] = (count($rowset) >= $limit) ? $page+1 : false;

            return $data;
        }

        public function getListFaturadoWithAgrupadorAndWorkspacePerTransacao($idPessoaFaturado = null, $idGrupo = null)
        {
            return $this->_dao->getListFaturadoWithAgrupadorAndWorkspacePerTransacao($idPessoaFaturado, $idGrupo);
        }

        public function getListFaturadoWithAgrupadorAndWorkspacePerTicket($idPessoaFaturado = null, $idGrupo = null)
        {
            return $this->_dao->getListFaturadoWithAgrupadorAndWorkspacePerTicket($idPessoaFaturado, $idGrupo);
        }

        public function getGridHeader($servico)
        {
            $fields = $this->_dao->getVisibleFields($servico['metadata']['ws_perfil']);
            $header = array();
            foreach ($fields as $value) {
                $header[] = array('campo' => strtolower($value['metanome']), 'label' => $value['nome']);
            }

            $rows = $this->_dao->fetchAll($this->_dao->getAllFields($servico['metadata']['ws_perfil']))->toArray();

            $fields = array();
            foreach ($rows as $row) {
                $fields[] = $row['metanome'];
            }

            return array('header' => $header, 'fields' => $fields);
        }

        public function selectGrid($time, $classificacao, $perfis, $gridHeader)
        {

            $grpBo = new Config_Model_Bo_Grupo();

            $retPrm = $grpBo->getTimesPermissao($time);

            $arrTime = array();
            foreach($retPrm as $item) {
                $arrTime[] = $item['id'];
            }
            $strTime = implode(',',$arrTime);
            //x($strTime);

            return $this->_dao->selectGrid($strTime, $classificacao, $perfis, $gridHeader);
        }

        public function selectGrid2($time,$tipopessoa,$informacao,$classificacao,$filtros) {
            $grpBo = new Config_Model_Bo_Grupo();

            $retPrm = $grpBo->getTimesPermissao($time);

            $arrTime = array();
            foreach($retPrm as $item) {
                $arrTime[] = $item['id'];
            }
            $strTime = implode(',',$arrTime);
            return $this->_dao->selectGrid2($strTime,$tipopessoa,$informacao,$classificacao,$filtros);
        }

        public function countGrid2($time,$tipopessoa,$classificacao,$filtros) {
            $grpBo = new Config_Model_Bo_Grupo();

            $retPrm = $grpBo->getTimesPermissao($time);

            $arrTime = array();
            foreach($retPrm as $item) {
                $arrTime[] = $item['id'];
            }
            $strTime = implode(',',$arrTime);

            return $this->_dao->countGrid2($strTime,$tipopessoa,$classificacao,$filtros);
        }

        public function getPessoaByCpfCnpj($cpfCnpj)
        {
            return $this->_dao->getPessoaByCpfCnpj($cpfCnpj);
        }

        public function getById($id)
        {
            return $this->_dao->getById($id);
        }

        public function getByIdIgnoreTime($id)
        {
            return $this->_dao->getByIdIgnoreTime($id);
        }
        /**
         * Verifica se uma pessoa existe na base, procurando pelo seu UUID.
         *
         * @param string $uuid UUID da pessoa para verificação
         * @return bool
         */
        public function pessoaExiste($uuid)
        {
            return is_null($this->_dao->find($uuid)->id);
        }

        public function getPessoaByMetanome($metanome) {
            return $this->_dao->fetchAll( array('metanome = ?' => $metanome) );
        }

        public function persiste($uuid,$nome,$nome2 = null,$metanome = null) {
            $rowPai = null;
            if($uuid){
                $rowPai = $this->_dao->find($uuid);
                if($rowPai->count()==0) {
                    throw new Exception("Pessoa não encontrada");
                }
                $rowPai = $rowPai->current();
            } else {
                $rowPai  = $this->_dao->createRow();
                $uuid = UUID::v4();
                $rowPai->id     = $uuid;
            }

            $rowPai->dt_inclusao = new zend_db_expr('now()');
            $rowPai->dtype = 'TbPessoa';
            if($nome2){
                $rowPai->nome2 = $nome2;
            }
            if($metanome){
                $rowPai->metanome = $metanome;
            }
            $rowPai->nome = $nome;
            $rowPai->save();
            return $uuid;
        }

        public function findPessoasUsuarioByEmail($email)
        {
            return $this->_dao->getPessoasUsuarioByEmail($email);
        }

        /**
         * Criação de uma nova pessoa e seu time.
         *
         * Inclui a criação dos grupos pessoal e geral, além de criar os relacionamentos
         * entre e-mail, grupo e pessoa.
         *
         * @param string $nomeUsuario Nome do usuário/pessoa que será criado.
         * @param string $emailUsuario E-mail do usuário/pessoa que será criado.
         * @param string $nomeTime Nome do novo time.
         * @param string $aliasTime Alias do novo time.
         * @param string $produto O nome do produto inicial adquirido pelo usuário.
         * @return string[]
         * @throws Exception
         */
        public function criaPessoaETime($nomeUsuario, $emailUsuario, $nomeTime, $aliasTime, $produto = null, $senha = null, $imagemCandidato)
        {
            try {
                if (!Zend_Registry::isRegistered('TRANSACAO_INGESTAO')) {
                    Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                }

                $grupoBo = new Config_Model_Bo_Grupo();
                $dadosGrupoPai = $grupoBo
                    ->getGrupoByMetanome(Config_Model_Bo_Grupo::META_GRUPOELEGIE);

                if (!is_array($dadosGrupoPai) || empty($dadosGrupoPai)) {
                    throw new Exception('grupo_hash_nao_encontrado');
                }
                $idGrupoPai = current($dadosGrupoPai)['id'];

                list($idPessoa, $senhaUsuario) = $this->criaPessoa(
                    $idGrupoPai,
                    $nomeUsuario,
                    $emailUsuario,
                    $produto,
                    $senha,
                    $imagemCandidato
                );

                $idTime = $grupoBo->criaTime($idPessoa, $idGrupoPai, $nomeTime, $aliasTime);

                /**
                * Criava a relação entre a pessoa criada e o time Pai Hash
                * @author Felipe Lino <felipe@titaniumtech.com.br>
                */
//                (new Config_Model_Bo_RlGrupoPessoa())
//                    ->addPessoaAoGrupo($idPessoa, $idGrupoPai);
                /**
                 * Fim do ajuste
                 * @author Felipe Lino <felipe@titaniumtech.com.br>
                 */

                if (!Zend_Registry::isRegistered('TRANSACAO_INGESTAO')) {
                    Zend_Db_Table::getDefaultAdapter()->commit();
                }
            } catch (Exception $ex) {
                if (!Zend_Registry::isRegistered('TRANSACAO_INGESTAO')) {
                    Zend_Db_Table::getDefaultAdapter()->rollBack();
                }
                throw $ex;
            }

            return [$senhaUsuario, $idPessoa, $idTime];
        }

        /**
         * Cria uma pessoa, seu usuário, seu grupo pessoal e sua info de email.
         *
         * @param UUID $idGrupoPai UUID do grupo pai do grupo pessoal - o grupo raiz.
         * @param string $nomeUsuario Nome do usuário/pessoa que será criado.
         * @param string $emailUsuario E-mail do usuário/pessoa que será criado.
         * @param string|array $produto O nome do produto inicialmente criado.
         * @param string $senha Senha inicial do usuário.
         * @return string[]
         */
        protected function criaPessoa($idGrupoPai, $nomeUsuario, $emailUsuario, $produto = null, $senha = null, $imagemCandidato)
        {
            // -- Criando pessoa
            $idPessoa = $this->persiste(null, $nomeUsuario);

            // -- Criando usuário
            $usuarioBo = new Auth_Model_Bo_Usuario();
            $primeiraSenhaUsuario = $usuarioBo->criaUsuario(
                $idPessoa,
                $nomeUsuario,
                $senha
            );

            $tipoInfoBo = new Config_Model_Bo_TipoInformacao();
            $infoBo = new Config_Model_Bo_Informacao();

            // -- Criando a informação de e-mail
            $idInfoEmail = $infoBo->persiste(
                null,
                current($tipoInfoBo->getByMetanome(Config_Model_Bo_TipoInformacao::META_EMAIL))['id'],
                $idPessoa,
                null,
                $emailUsuario,
                $idPessoa
            );

            // -- Armazenando o nome do produto - para instalação na configuração do workspace
            if (!empty($produto)) {
                if (!is_array($produto)) {
                    $produto = [$produto];
                }

                foreach ($produto as $prod) {
                    $infoBo->addInformacao(
                        $idPessoa,
                        Config_Model_Bo_TipoInformacao::META_PRODUTOINICIAL,
                        $prod
                    );
                }

            }

            $infoBo->addInformacao(
                $idPessoa,
                Config_Model_Bo_TipoInformacao::META_AVATAR,
                $imagemCandidato
            );
            // -- Criando grupo pessoal
            (new Config_Model_Bo_Grupo())->criaGrupo(
                "@{$nomeUsuario}",
                $idPessoa,
                $idInfoEmail,
                $idGrupoPai,
                null,
                'f',
                "@{$nomeUsuario}"
            );

            return [$idPessoa, $primeiraSenhaUsuario];
        }

        /**
         *
         * @param type $idPessoa
         * @param type $idTime
         * @throws Exception
         * @todo Refatorar e separar o instala modulos
         */
        public function salvaDeAcordoEInstalaModulos($idPessoa, $idTime = null, $apenasInstala = false, $imagemTroca = NULL)
        {
            $install = true;
            try {
                if (!Zend_Registry::isRegistered('TRANSACAO_INGESTAO')) {
                    Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                }

                $grupoBo = new Config_Model_Bo_Grupo();
                $metasBo = new Config_Model_Bo_GrupoMetadata();
                $rlGrupoServico = new Config_Model_Bo_RlGrupoServico();
                if (!$apenasInstala) {
                    $idGrupoPessoal = $grupoBo
                        ->getGrupoPessoalByPessoa($idPessoa);

                    (new Config_Model_Bo_Informacao())
                        ->salvaDeAcordo($idPessoa, $idGrupoPessoal);
                }

                // -- Instala módulos obrigatórios para o grupo time
                if (is_null($idTime)) {
                    $idGrupoTime = $grupoBo->getTimeByCriador($idPessoa);
                } else {
                    $idGrupoTime = current($grupoBo->getGrupoByRepresentacao($idTime))['id'];
                }

                $servicoBo = new Config_Model_Bo_Servico();
                $modulos = $servicoBo->getModulosObrigatorios();

                // -- Instala módulos obrigatórios para o time HASH
                $dadosGrupoHash = $grupoBo
                        ->getGrupoByMetanome(Config_Model_Bo_Grupo::META_GRUPOELEGIE);
                $dadosGrupoHash = current($dadosGrupoHash);

                $rlGrupoServico
                    ->ativaServicosDoModulo($idPessoa, $dadosGrupoHash['id'], $modulos);

                $this->instalaModulosDoProduto($idPessoa, $idGrupoTime, NULL, $imagemTroca);

                $metas = $metasBo->listMeta($idGrupoTime)->toArray();
                foreach ($metas as $meta) {
                    if ($meta['metanome'] == $metasBo::META_INSTALL) {
                        $install = false;
                    }
                }
                if ($install) {
                    $metasBo->insere($idGrupoTime, $metasBo::META_INSTALL, TRUE);
                    if (!Zend_Registry::isRegistered('TRANSACAO_INGESTAO')) {
                        Zend_Db_Table::getDefaultAdapter()->commit();
                    }

                } else {
                    if (!Zend_Registry::isRegistered('TRANSACAO_INGESTAO')) {
                        Zend_Db_Table::getDefaultAdapter()->rollBack();
                    }
                }
            } catch (Exception $ex) {
                if (!Zend_Registry::isRegistered('TRANSACAO_INGESTAO')) {
                    Zend_Db_Table::getDefaultAdapter()->rollBack();
                }
                throw $ex;
            }
        }

        /**
         * Instala os módulos do produto indicado em $nomeProduto.
         *
         * Caso $nomeProduto não esteja preenchido, é feito uma busca na base de
         * dados pelo meta Config_Model_Bo_TipoInformacao::META_PRODUTOINICIAL,
         * essa informação é criada pelo instalador ao salvar os produtos solicitados
         * durante a inscrição (via url).
         *
         * @param type $idPessoa
         * @param type $idGrupoTime
         * @param string[] $nomeProduto O nome do produto ou produtos para instalação.
         * @param type $imagemTroca
         * @throws App_Validate_Exception
         */

        public function instalaModulosDoProduto($idPessoa, $idGrupoTime, $nomeProduto = null,  $imagemTroca = NULL)
        {
            $produtos = [];

            if (is_string($nomeProduto)) {
                $produtos = [$nomeProduto];
            } else {
                $listaProdutos = (new Config_Model_Bo_Informacao())->getInfoPessoaByMetanome(
                    $idPessoa,
                    Config_Model_Bo_TipoInformacao::META_PRODUTOINICIAL
                );

                foreach ($listaProdutos as $metaProduto) {
                    $produtos[] = $metaProduto['valor'];
                }
            }

            $grupoBo = new Config_Model_Bo_Grupo();
            $grupoMetaBo = new Config_Model_Bo_GrupoMetadata();
            $servicoBo = new Config_Model_Bo_Servico();
            $grupoServBo = new Config_Model_Bo_RlGrupoServico();

            foreach ($produtos as $nomeProduto) {
                switch ($nomeProduto) {
                    case 'hash':
                        $dnsId = Config_Model_Bo_Site::HASH;
                        $timeOrigem = current($grupoBo->getGrupoByMetanome('comitemodelo'));
                    break;
                    case 'hpc':
                        $dnsId = Config_Model_Bo_Site::ELEGIE;
                        $timeOrigem = current($grupoBo->getGrupoByMetanome('comitemodelo'));
                    break;
                    case 'elegie':
                    case 'doacao':
                        $dnsId = Config_Model_Bo_Site::ELEGIE;
                        $timeOrigem = current($grupoBo->getGrupoByMetanome('comitemodelo'));
                        break;
                    default:
                        throw new App_Validate_Exception('produto_inicial_nao_encontrado');
                }

                $grupoBo->copiaGrupo($timeOrigem['id'], $idGrupoTime, $idPessoa, true, true, $imagemTroca);
                $grupoMetaBo->atualizaAliasECriaDns($idGrupoTime, $dnsId);
                $modulos = $servicoBo->getModulosByGrupo($timeOrigem['id']);
                $grupoServBo->ativaServicosDoModulo($idPessoa, $idGrupoTime, $modulos);
            }
        }

        public function getSimpleById($id) {
            return $this->_dao->getSimpleById($id);
        }

        /**
         * Cria um novo usuário gerando a configuração inicial do usuário.
         *
         * Exemplo de conteúdo de $param
         * 1) 'EMAIL=teste@teste.com,CEL={NUMCEL=61996184651},CEL={NUMCEL=6199999999,DDDCEL=61}'
         *
         * @param string $nome Primeiro nom do usuário
         * @param string $nome2 Sobrenome do usuário
         * @param string $username Nome de usuário para autenticação no sistema
         * @param string $password Password não encriptado
         * @param string $param Lista de informações do usuário, consulte a documentação para detalhes
         * @return Array contendo $idPessoa e senha sem criptografia.
         */
        public function criar_usuario($nome, $nome2, $username, $password = null, $param = null)
        {
            list($password, $salt, $encryptedpass) = (new Auth_Model_Bo_Usuario())
                ->criaPassword($password);

            $idPessoa = $this->_dao->criar_usuario(
                $nome,
                $nome2,
                $username,
                $salt,
                $encryptedpass,
                $param
            );

            return [$idPessoa, $password];
        }

        public function criar_entidade($idCriador, $nome, $nome2, $idGrupo, $idVinculado = null, $param = '')
        {
            if (is_null($idVinculado)) {
                $idVinculado = $idCriador;
            }

            return $this->_dao->criar_entidade($idCriador, $nome, $nome2, $idGrupo, $idVinculado, $param);
        }
    }
