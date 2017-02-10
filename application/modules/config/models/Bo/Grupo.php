<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_Grupo extends App_Model_Bo_Abstract
{
    /**
     * Identificação do metadado de grupo/hash.
     */
    const META_GRUPO = 'HASH';
    const META_GRUPOELEGIE = 'ELEGIE';

    /**
     * Identificação do metanome do grupo de CRACHA.
     */
    const META_CRACHA = 'CMSGRUPOCRACHA';

    /**
     * Nome do grupo geral.
     */
    const NOME_GRUPO_GERAL = 'Geral';

    /**
     * @var Config_Model_Dao_Grupo
     */
    public $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
            $this->_dao = new Config_Model_Dao_Grupo();
            parent::__construct();
    }

    public function delGrupoSemRelacao($uuid)
    {
        $msg = '';
        $grupos = $this->listGruposAbaixo($uuid);

        $modelGrupoMeta = new Config_Model_Bo_GrupoMetadata();
        $modelGrupoItem = new Config_Model_Bo_RlGrupoItem();
        $modelGrupoPess = new Config_Model_Bo_RlGrupoPessoa();
        $modelGrupoServ = new Config_Model_Bo_RlGrupoServico();
        $modelGrupoInfo = new Config_Model_Bo_RlGrupoInformacao();

        foreach($grupos as $grupo)
        {
            $itens   = $modelGrupoItem->listGrupoItem($uuid);

            $pessoa  = $modelGrupoPess->listGrupoPessoa($uuid);

            $servico = $modelGrupoServ->listGrupoServico($uuid);

            $info    = $modelGrupoInfo->listGrupoInfo($uuid);

            if(count($itens) > 0){
                $msg .= ' ( Grupo '.$grupo['nome'].', tem itens relacionados ) ';
            }
            if(count($pessoa) > 0){
                $msg .= ' ( Grupo '.$grupo['nome'].', tem pessoas relacionados ) ';
            }
            if(count($servico) > 0){
                $msg .= ' ( Grupo '.$grupo['nome'].', tem servicos relacionados ) ';
            }
            if(count($info) > 0){
                $msg .= ' ( Grupo '.$grupo['nome'].', tem informacoes relacionados ) ';
            }
        }

        $grupos = array_reverse($grupos);

        if(empty($msg)){
            foreach($grupos as $grupo)
            {
                if($grupo['id'] != $uuid){
                    $modelGrupoMeta->delGrupoMetadatas($grupo['id']);
                    $this->_dao->delGrupo($grupo['id']);
                }
            }
            $modelGrupoMeta->delGrupoMetadatas($uuid);
            $this->_dao->delGrupo($uuid);
        }
        return empty($msg) ? true : $msg;
    }

    public function copiaGrupo($copiado, $colado, $id_pessoa, $soConteudo = false, $copiaIb = false, $imagemTroca = NULL)
    {
        $modelGrupoMeta = new Config_Model_Bo_GrupoMetadata();

        $grupos = $this->listGruposAbaixo($copiado);

        $parentes = array();

        for($i = 0 ; $i < count($grupos); ++$i)
        {
            if($soConteudo === true){
                if($i == 0){ continue; }
                if($grupos[$i]['id_pai'] == $grupos[0]['id'] ){
                    $id_pai = $colado;
                }else{
                    $id_pai = $parentes[$grupos[$i]['id_pai']]['idNovo'];
                }
            }else{
                $id_pai = $i == 0 ? $colado : $parentes[$grupos[$i]['id_pai']]['idNovo'] ;
            }

            $novoId = $this->insere(    $grupos[$i]['nome'],
                                        $grupos[$i]['metanome'],
                                        $id_pai,
                                        $grupos[$i]['descricao'],
                                        $grupos[$i]['id_canal'],
                                        $id_pessoa,
                                        null,
                                        null,
                                        ($grupos[$i]['publico'] == true) ? 'TRUE' : 'FALSE');
             $modelIb = new Content_Model_Bo_ItemBiblioteca();

            $parentes[$grupos[$i]['id']] = array(   'idAntigo'      => $grupos[$i]['id'],
                                                    'idNovo'        => $novoId,
                                                    'idPaiAntigo'   => $grupos[$i]['id_pai']);

            $metas = $modelGrupoMeta->listMeta($grupos[$i]['id']);
            if ($copiaIb) {
                $modelIb->copiaIb($grupos[$i]['id'], $novoId, $id_pessoa, $imagemTroca);
            }
            foreach($metas as $meta){
                $modelGrupoMeta->insere($novoId, $meta['metanome'], $meta['valor']);

            }
        }
    }

    public function delGrupo($uuid)
    {
        return $this->_dao->delGrupo( $uuid );
    }

    public function getGrupo($uuid)
    {
        return $this->_dao->getGrupo( $uuid );
    }


    public function listGruposOrfaos()
    {
        return $this->_dao->listGruposOrfaos();
    }

    public function listGruposAbaixo($uuid, $params = array())
    {
        return $this->_dao->listGruposAbaixo( $uuid, $params);
    }
    public function listGruposFilho($uuid)
    {
        return $this->_dao->listGruposFilho( $uuid );
    }

    public function getGruposByIDPaiByMetanome( $idPai, $metanome)
    {
        return $this->_dao->getGruposByIDPaiByMetanome( $idPai, $metanome);
    }

    public function getGrupoByMetanome($metanome)
    {
        return $this->_dao->getGrupoByMetanome($metanome);
    }

    public function getGrupoByIDPaiByCanal ($idPai, $canal)
    {
        return $this->_dao->getGrupoByIDPaiByCanal($idPai, $canal);
    }

    public function update($id, $dados)
    {
        $condicao = $this->_dao->getAdapter()->quoteInto ( 'id = ?', $id );
        return $this->_dao->update($dados, $condicao);
    }

    public function insere($nome,$metanome = null,$idpai = null,$desc = null,$idcanal = null,$idcriador = null,$idrepresentacao = null,$id = null, $publico = true)
    {
        if(empty($id)){
            $id = UUID::v4();
        }

        $this->_dao->insert(array(
                                'id' => $id,
                                'nome' => $nome,
                                'metanome' => $metanome,
                                'id_pai' => $idpai,
                                'descricao' => $desc,
                                'id_canal' => $idcanal,
                                'id_criador' => $idcriador,
                                'id_representacao' => $idrepresentacao,
                                'publico' => $publico
                                ));
        return $id;
    }

    /**
     * Cria um grupo, o relaciona ao seu proprietário e, opcionalmente, define sua info de e-mail.
     *
     * Se $idInfoEmail for nulo, a associação grupo/infoemail não acontece.
     * Se $idRepresentacao for nulo, a definição de proprietário não acontece.
     *
     * @param string $nomeGrupo Nome do grupo que está sendo criado.
     * @param string $idPessoa UUID de tb_pessoa.
     * @param string $idInfoEmail UUID da informação de e-mail associada ao $idPessoa.
     * @param string $idGrupoPai UUID do grupo pai deste novo grupo.
     * @param string $idRepresentacao UUID do time/pessoa
     * @param bool $publico Indica se é um grupo público ou não.
     * @param string $nomeHash
     * @return string UUID do grupo cadastrado.
     */
    public function criaGrupo($nomeGrupo, $idPessoa, $idInfoEmail, $idGrupoPai, $idRepresentacao, $publico = null, $nomeHash = null)
    {
        // -- Criando grupo
        $idGrupo = $this->insere(
            $nomeGrupo,
            null,
            $idGrupoPai,
            null,
            null,
            $idPessoa,
            $idRepresentacao,
            null,
            $publico
        );

        // -- Proprietário do grupo
        if (is_null($idRepresentacao)) {
            (new Config_Model_Bo_RlGrupoPessoa())->addPessoaAoGrupo(
                $idPessoa,
                $idGrupo,
                Config_Model_Bo_RlGrupoPessoa::PERMISSAO_DONO,
                $nomeHash
            );
        }

        // -- Associando a info de e-mail do proprietário ao grupo
        if (!is_null($idInfoEmail)) {
            (new Config_Model_Bo_RlGrupoInformacao())->persiste(
                null,
                $idGrupo,
                $idPessoa,
                $idInfoEmail
            );
        }

        return $idGrupo;
    }

    /**
     * Cria um time, com seu grupo de time, alias do time e grupo geral.
     *
     * @param string $idPessoa UUID id de tb_pessoa.
     * @param string $idGrupoPai UUID id do grupopai do grupo do time.
     * @param string $nomeTime Nome do novo time.
     * @param string $aliasTime Alias do novo time.
     * @return uuid Id do novo time criado.
     * @todo Lançar uma app_validate_exception quando o alias do time já existir
     */
    public function criaTime($idPessoa, $idGrupoPai, $nomeTime, $aliasTime)
    {
        // -- Criando o time (como pessoa)
        $idTime = (new Legacy_Model_Bo_Pessoa())
            ->persiste(null, $nomeTime);

        // -- Criando o grupo do time
        $idGrupo = $this->criaGrupo(
            $nomeTime,
            $idPessoa,
            null,
            $idGrupoPai,
            $idTime
        );

        /**
         * Adicione para criar o grupo geral vinculado ao Hash
         * @author Felipe Lino <felipe@titaniumtech.com.br>
         */
        // -- Criando o grupo geral
        $this->criaGrupo(
            'Geral',
            $idPessoa,
            null,
            $idGrupoPai,
            null,
            't',
            '#geral'
        );
        /**
         * Fim do ajuste
         * @author Felipe Lino <felipe@titaniumtech.com.br>
         */

        // -- Criando o alias do time
        (new Config_Model_Bo_GrupoMetadata())->insere(
            $idGrupo,
            Config_Model_Bo_GrupoMetadata::META_ALIAS,
            $aliasTime
        );

        // -- Criando o grupo geral
        $this->criaGrupo(
            'Geral',
            $idPessoa,
            null,
            $idGrupo,
            null,
            't',
            '#geral'
        );

        return $idTime;
    }

    public function getGrupoPessoalByPessoa($uuid)
    {
        return $this->_dao->getGrupoPessoalByPessoa($uuid);
    }

    public function getTimeByCriador($idCriador)
    {
        return $this->_dao->getTimeByCriador($idCriador);
    }

    public function getTimesPermissao($idtime) {
        return $this->_dao->getTimesPermissao($idtime);

    }

    public function getGrupoByTime($idTime) {
        return $this->_dao->getGrupoByTime($idTime);
    }

    public function getGrupoByRepresentacao($idTime)
    {
        return $this->_dao->getGrupoByRepresentacao($idTime);
    }

    public function getTimeByGrupo($grp) {
        return $this->_dao->getTimeByGrupo($grp);
    }

    public function getGrupoByTimeEMetanome($idTime, $metanome){
        return $this->_dao->getGrupoByTimeEMetanome($idTime, $metanome);
    }

    public function getGrupoByCanal ($canal)
    {
        return $this->_dao->getGrupoByCanal($canal);
    }

    public function gridGrupoCReprByCanal($canal) {
        return $this->_dao->gridGrupoCReprByCanal($canal);
    }

    public function getGrupoGeralByCriador($idCriador)
    {
        return $this->_dao->getGrupoGeralByCriador($idCriador);
    }

    public function getTimesImportados(){
        return $this->_dao->getTimesImportados();
    }

    public function criar_time(
        $time,
        $idRepresentacao,
        $idGrupoPai,
        $idUsuario,
        $idCanal,
        $publico,
        $metanome,
        $descricao,
        $cmsAlias,
        $idGrupoModelo,
        $itembiblioteca,
        $params
    ) {
        return $this->_dao->criar_time(
            $time,
            $idRepresentacao,
            $idGrupoPai,
            $idUsuario,
            $idCanal,
            $publico,
            $metanome,
            $descricao,
            $cmsAlias,
            $idGrupoModelo,
            $itembiblioteca,
            $params
        );
    }
}
