<?php
class Auth_Model_Bo_Usuario extends App_Model_Bo_Abstract
{
    /**
     * @var Auth_Model_Dao_Usuario
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Auth_Model_Dao_Usuario();
        parent::__construct();
    }

    /**
     * @desc autentica o usuário
     * @param string $nome
     * @param string $senha
     * @param array  $data
     * @return boolean true se o usuario tiver autenticado e false
     */
    public function authenticate($nome, $senha)
    {
        $auth       = Zend_Auth::getInstance();

        $ns = new Zend_Session_Namespace('salt');

        if ($ns->senhadbsalt != $senha) {
            // senha incorreta!
            return false;
//            throw new Exception('Senha Incorreta!');
        }

        $adapter = new  Zend_Auth_Adapter_DbTable();
        $adapter->setTableName('tb_usuario')
                ->setIdentityColumn('nomeusuario')
                ->setCredentialColumn('password_encrypted')
                ->setIdentity($nome)
                ->setCredential($ns->senha);

        // @todo verificar onde colocar isso em adição quando efetua login
//        Zend_Session::namespaceUnset('salt');

        $result = $auth->authenticate($adapter);
        //verifica se a autenticação foi válida
        if ($result->isValid()) {
            $usuarioObj = $adapter->getResultRowObject(null, 'password_encrypted');
            $storage = $auth->getStorage();

            // pega outros valores para colocar na sessão.
            $pessoaBo = new Legacy_Model_Bo_Pessoa();
            $pessoa = $pessoaBo->findOne(array('id = ?' => $usuarioObj->id));

            $usuarioObj->pessoa = $pessoa;

            $rlPermissaoPessoaBo = new Legacy_Model_Bo_RlPermissaoPessoa();
            $rsPermissoes = $rlPermissaoPessoaBo->getByIdPessoa($usuarioObj->id);

            if ($rsPermissoes) {
                // usuário tem permissão em algum lugar.
                $grupoBo = new Legacy_Model_Bo_Grupo();

                $permissoes = array();
                foreach ($rsPermissoes as $row) {
                    // dt_expiracao adicionado para fazer verificação direta na ação.
                    $permissoes[$row->id_grupo][$row->id_servico] = $row->dt_expiracao;
                }

                // pega o primeiro time q veio na query e coloca para o usuário

                $rsTimes = $grupoBo->getTimesId($usuarioObj->id);
                $rlGrupoServicoBo = new Legacy_Model_Bo_RlGrupoServico();
                $times = array();
                foreach($rsTimes as $time) {
//                    $rowTime = $grupoBo->findOne(array('id = ?' => $time['id']));
                    $times[$time['id']] = $time;

                    $modulos = $rlGrupoServicoBo->find(array('id_grupo = ?' => $time['id']));

                    $modulos = count($modulos) > 0 ? $modulos->toArray() : null;

                    //@todo verifica se ta tudo vazio e joga o cara para foda
                    $times[$time['id']]['modulos'] = $modulos;
                }

//                $usuarioObj->modulos = $modulos;

                $usuarioObj->times = $times;

                $time = current($usuarioObj->times);
                $usuarioObj->time = $time;

                $rsGrupos = $grupoBo->getGruposId($usuarioObj->id, $time['id']);

//                echo "<PRE>";
//                var_dump($rsGrupos);
//                exit;

                $grupos = array();
                foreach ($rsGrupos as $k => $rowGrupo) {
                    $grupos[$rowGrupo['id']] = $rowGrupo;
                }

                $usuarioObj->grupos = $grupos;
                $usuarioObj->permission = $permissoes;
                $usuarioObj->grupo = current($usuarioObj->grupos);

            }

            if (!$permissoes) {
                // @todo tratar caso: usuário não tem acesso a nenhum grupo - nunca deveria acontecer
            }

            // pega todos os servicos do sistema
            $servicoBo = new Legacy_Model_Bo_Servico();
            $servicos = $servicoBo->getAllServicesAsIterator();
            $usuarioObj->servicos = $servicos['data'];
            $usuarioObj->arvoreServicos = $servicos['iterator'];

            // coloca primeiro modulo como selecionado
            $modulo = $time['modulos'][0]['id_servico'];

            $usuarioObj->modulo = $usuarioObj->servicos[$modulo];

            $servicosAtual = array();

            foreach ($usuarioObj->servicos as $key => $servico) {

                if (array_key_exists($key, $usuarioObj->permission[$time['id']]) && ($usuarioObj->permission[$time['id']][$key] > date('Y-m-d'))) {
                    $servicosAtual[$key] = $servico;
                } else {
                    unset($servicosAtual[$servico['id_pai']]['filhos'][$key]);
                }
            }

            $usuarioObj->servicosAtual = $servicosAtual;

            $storage->write($usuarioObj);

            return true;
        }

        return false;
    }


    public function getUsuarioLoginData($usuario)
    {
        $usuarioObj = $this->find(array("nomeusuario = ?" => $usuario))->current();
        require_once "../includes/Random.php";
        $ns = new Zend_Session_Namespace('salt');
        $ns->unsetAll();

        if (!$usuarioObj) {
            $response = array('dbsalt' => Random::random_str(8), 'sessionsalt' => Random::random_str(8));
        } else {
            $ns->salt = Random::random_str(8);
            $ns->senha = $usuarioObj->password_encrypted;
            $ns->senhadbsalt = hash_pbkdf2('sha1', $usuarioObj->password_encrypted, $ns->salt,10000,40);

            $response = array('dbsalt' => $usuarioObj->salt, 'sessionsalt' => $ns->salt);
        }

        return $response;
    }

    /**
     * Cria um novo usuário na base à partir do id de uma pessoa.
     *
     * Após criar o usuário, retorna a senha inicial atribuída a esse usuário.
     *
     * @param string $uuid ID de um registro de tb_pessoa.
     * @param string $nomeusuario O Nome do usuário.
     * @return Auth_Model_Vo_Usuario
     */
    public function criaUsuario($uuid, $nomeusuario, $senha = null)
    {
        list($senha, $salt, $encryptedpass) = $this->criaPassword($senha);

        $this->_dao->insert([
            'id' => $uuid,
            'nomeusuario' => $nomeusuario,
            'completar_cadastro' => true,
            'salt' => $salt,
            'password_encrypted' => $encryptedpass
        ]);

        return $senha;
    }

    /**
     * Gera uma nova senha e retorna seus componentes.
     *
     * @param string|null $senha Senha do usuário, caso seja nulo, uma senha de 8 caracteres é gerada.
     * @return type
     */
    public function criaPassword($senha = null)
    {
        if (is_null($senha)) {
            $senha = Random::random_str(8);
        }

        $salt = Random::random_str(8);

        return [
            $senha,
            $salt,
            hash_pbkdf2('sha1', $senha, $salt, 10000, 40)
        ];
    }

    public function autoComplete($term)
    {
        return $this->find(array('nomeusuario ilike ?' => "%$term%"), null, 10);
    }

    public function geraTicketSenha($idPessoa)
    {
        $ticket = Random::random_str(8);
        $this->_dao->update(
            ['ticket_senha' => $ticket],
            $idPessoa
        );

        return $ticket;
    }

    public function validaTicketSenha($ticketSenha)
    {
        return $this->_dao->validaTicketSenha($ticketSenha);
    }

    /**
     *
     * @param array $dados Par de chave e valor representando campo e valor.
     * @param uuid $idUsuario Id
     */
    public function update(array $dados, $idUsuario)
    {
        $this->_dao->update($dados, $idUsuario);
    }

    public function getUserByNomeUsuario($nome) {
        return $this->_dao->getUserByNomeUsuario($nome);
    }
}
