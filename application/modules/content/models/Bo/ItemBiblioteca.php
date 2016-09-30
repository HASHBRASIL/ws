<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Content_Model_Bo_ItemBiblioteca extends App_Model_Bo_Abstract
{
    /**
     * @var Content_Model_Dao_ItemBiblioteca
     */
    protected $_dao;

    public $fieldsFilter = array(
        'ocr'      			  => 'hidden',
    );

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Content_Model_Dao_ItemBiblioteca();
        parent::__construct();
    }

    public function addRelGrupoItem($id_grupo, $id_item) {
        return $this->_dao->addRelGrupoItem($id_grupo, $id_item);
    }

    public function delRelGrupoItem($id_grupo, $id_item) {
        return $this->_dao->delRelGrupoItem($id_grupo, $id_item);
    }

    public function getById($id) {
        $data = $this->_dao->find($id);
        return $data;
    }

    public function getConteudo($time) {
        $data = $this->_dao->getItemBiblioteca($time);
        return $data;
    }

    public function getTipoConteudo($time, $term) {
        $data = $this->_dao->getTipoConteudo($time, $term);
        return $data;
    }

    public function getCamposByTipo($tib) {
        $data = $this->_dao->getCamposByTipo($tib);
        return $data;
    }

    public function getItemBibliotecaGrid($id_tib, $id_grupo, $options = null) {
        $retorno = array(
            'query' => $this->_dao->getItemBibliotecaGrid($id_tib, $id_grupo, $options),
            'count' => $this->_dao->getFalseCount()
        );


        return $retorno;
    }

    public function listItemBiblioteca($id_tib, $id_grupo) {
        return $this->_dao->listItemBiblioteca($id_tib, $id_grupo);
    }

    public function getIbByTibAndGrupo($id_tib, $id_grupo) {
        return $this->_dao->getIbByTibAndGrupo($id_tib, $id_grupo);
    }

    public function getItemBibliotecaById ($idIbPai) {
        $data = $this->_dao->fetchAll( array('id_ib_pai = ?' => $idIbPai) );
        return $data;
    }

    public function checkIbValor($idIbPai, $valor) {
        return $this->_dao->checkIbValor($idIbPai, $valor);
    }

    public function getAllIbByTib($tib)
    {
        return $this->_dao->fetchAll( array('id_tib = ?' => $tib) );
    }

    public function getAllIbByTSE($metanome, $cargo, $estados, $limit, $offset) {
        return $this->_dao->getAllIbByTSE($metanome, $cargo, $estados, $limit, $offset);
    }

    public function getAllItensByTibByOrdem($tib,$ordem) {
        return $this->_dao->getAllItensByTibByOrdem($tib,$ordem);
    }

    public function getItemByTibByValor($tib, $valor)
    {
        return $this->_dao->fetchAll( array('id_tib = ?' => $tib, 'valor = ?' => $valor) );
    }

    public function update($uuid,$idPessoa,$valor) {
        $this->persiste($uuid, NULL, $idPessoa, NULL, $valor);
    }

    public function delete($uuid) {
        return $this->_dao->delete("id = '" . $uuid . "'");
    }

    public function getTpItembiblioteca($id = null, $metanome = null, $metonomePai = null) {
        return $this->_dao->getTpItembiblioteca($id, $metanome, $metonomePai);
    }

    public function insere($idTib,$idPessoa,$dados){
        $idItem = $this->persiste(false,$idTib,$idPessoa,null,null);
        foreach($dados as $chave => $valor) {
            if (UUID::is_uuid($chave)) {
                $idTibFilho = $chave;
            } else {
                $idTibFilho = $this->getTpItembiblioteca(null, $chave, $idTib)->id;
            }
            $this->persiste(false,$idTibFilho,$idPessoa,$idItem,$valor);
        }
        return $idItem;
    }

    public function persiste($uuid,$idTib,$idPessoa,$idPai,$valor) {
        $rowPai = null;
        if($uuid){
            $rowPai = $this->_dao->find($uuid);
            if($rowPai->count()==0) {
                throw new Exception("Item não encontrado");
            }
            $rowPai = $rowPai->current();
        } else {
            $rowPai  = $this->_dao->createRow();
            $uuid = UUID::v4();
            $rowPai->id     = $uuid;
        }

        $rowPai->dt_criacao = new zend_db_expr('now()');
        if($idPessoa){
            $rowPai->id_criador = $idPessoa;
        }
        if($idTib){
            $rowPai->id_tib     = $idTib;
        }
        if($idPai){
            $rowPai->id_ib_pai  = $idPai;
        }
        $rowPai->valor = $valor;
        $rowPai->save();
        return $uuid;
    }


    public function atualiza($idIbPai, $idTib,$idPessoa,$dados){
        foreach($dados as $chave => $valor) {
            if (UUID::is_uuid($chave)) {
                $idTibFilho = $chave;
            } else {
                $idTibFilho = $this->getTpItembiblioteca(null, $chave, $idTib)->id;
            }
            $id = current($this->getIbByPaiByTib($idIbPai, $idTibFilho)->toArray())['id'];

            $this->update($id,$idPessoa,$valor);
        }
    }

    public function save($data) {

        $boTib = new Content_Model_Bo_TpItemBiblioteca();
        $rlGI  = new Content_Model_Bo_RlGrupoItem();

        $rowsTib = $boTib->getTipoByIdSelect($data['config']['id_tib']);
        $rowsIb  = $this->getItemBibliotecaById($data['config']['data']['id_ib_img']);

        // EU PRECISO SEMPRE CRIAR UMA NOVA IMAGEM QUANDO O USUÁRIO SELECIONAR UMA IMAGEM PRA UMA NOTICIA
        //
        try {
            $rowPai  = $this->_dao->createRow();
            $uuidPai = UUID::v4();
            $rowPai->id         = $uuidPai;
            $rowPai->dt_criacao = new zend_db_expr('now()');
            $rowPai->id_criador = $data['config']['pessoa'];
            $rowPai->id_tib     = $data['config']['id_tib'];
            $rowPai->id_time    = $data['config']['time'];
            $rowPai->save();

            foreach ($rowsTib as $dados) {

                try {
                    $row = $this->_dao->createRow(); // esse this tem que ser a dao do item biblioteca no caso aqui esta errado
                    $row->id         = UUID::v4();
                    $row->dt_criacao = new zend_db_expr('now()');
                    $row->valor      = $data['campos'][$dados['id'] . '_' . $dados['metanome']];// path + nome da image que vai cair no lugar
                    $row->id_criador = $data['config']['pessoa'];
                    $row->id_ib_pai  = $uuidPai;
                    $row->id_tib     = $dados['id'];
                    $row->id_time    = $data['config']['time'];
                    $row->save();
                } catch (Exception $e) {
                    //var_dump('deu pau ao gerar um filho do pai' . $e);
                    return 'deu pau ao gerar um filho do pai' . $e;
                }
            }
            try {
                $rlGI->relacionaItem( $data['config']['grupo'] ,$uuidPai);
            } catch (Exception $e) {
                return 'deu ruim' . $e;
            }

            return 'SUCESS';

        } catch (Exception $e) {
            var_dump('nao salvou o paapai' . $e);
            return 'nao salvou o paapai' . $e;
        }
    }

    public function getFilhosByIdPai($idPai) {
        return $this->_dao->getFilhosByIdPai($idPai);
    }

    public function getAllByTib($tib,$ordem) {
        return $this->_dao->getAllByTib($tib,$ordem);
	}

    public function getIbByPaiByTib($idpai,$idtib){
        return $this->_dao->getIbByPaiByTib($idpai,$idtib);
    }

    public function copiaIb($idGrupoAntigo, $idGrupoNovo, $idPessoa, $imagemTroca) {

        $itens = $this->_dao->getIbByRlGrupoItem($idGrupoAntigo);
        foreach ($itens as $item) {
            $itemMaster = $this->_dao->getIbById($item['id_item']);

            if (count($itemMaster) > 0) {
                $idIbPai = $this->persiste(false, current($itemMaster)['id_tib'], $idPessoa, NULL, NULL);
                $itensFilhos = $this->_dao->getFilhosByIdPai($item['id_item']);
                foreach ($itensFilhos as $itemfilho) {

                   $tib = current((new Config_Model_Bo_Tib)->getById($itemfilho['id_tib']));
                   if($tib['metanome'] == 'imagem') {
                       $this->persiste(false, $itemfilho['id_tib'], $idPessoa, $idIbPai, $imagemTroca);
                   } else {
                       $this->persiste(false, $itemfilho['id_tib'], $idPessoa, $idIbPai, $itemfilho['valor']);
                   }

                }
                $this->addRelGrupoItem($idGrupoNovo, $idIbPai);
            }
        }
    }

    public function getValorByCriadorEMetanome($idcriador, $metanome, $metanomepai)
    {
        return $this->_dao->getValorByCriadorEMetanome($idcriador, $metanome, $metanomepai);
    }

    public function getValoresFilhosNomeados($idIbPai)
    {
        return $this->_dao->getValoresFilhosNomeados($idIbPai);
    }

    public function verificaDuplicidade($tibId, $valor)
    {
        $dados = $this->_dao->fetchAll([
            'id_tib = ?' => $tibId,
            'valor ilike ?' => $valor
        ])->toArray();

        return count($dados) > 1;
    }

    public function getProximoCandidatoSemPar() {
        return $this->_dao->getProximoCandidatoSemPar();
    }

    public function getCandidadoPorColig($cidade,$estado,$colig,$cargo) {
        return $this->_dao->getCandidadoPorColig($cidade,$estado,$colig,$cargo);
    }

    public function geratmpemailinfocand() {
        return $this->_dao->geratmpemailinfocand();
    }

    public function geratmppessoainfocand($uf) {
        return $this->_dao->geratmppessoainfocand($uf);
    }

    public function inseretmpemailcand($idcand,$nomecand,$emailcand,$usercand,$txtemail,$status){
        return $this->_dao->inseretmpemailcand($idcand,$nomecand,$emailcand,$usercand,$txtemail,$status);
    }

    public function atualizastatuscand($idcand,$status){
        return $this->_dao->atualizastatuscand($idcand,$status);
    }

    public function getFolderByGrupoByTib($idGrupo, $servico, $idTib = null) {
        return $this->_dao->getFolderByGrupoByTib($idGrupo, $servico, $idTib);
    }

    public function processaArquivos($servico)
    {

        $googleVision = new App_Model_Bo_Vision();
        $registro = $this->_dao->getPendente($servico);
        $boTib = new Config_Model_Bo_Tib();

        $this->_dao->beginTransaction();

        // caminho para os arquivos
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        // pega o caminho real
        $file = realpath($filedir->path . $registro['arquivo']);

        var_dump($registro);exit;

        $extensao = substr(strrchr($file, "."),1);

        $data = array();

        switch (strtolower($extensao)) {
            case 'pdf':
                //Fall through to next case;
            case 'tif':
                //Fall through to next case;
            case 'tiff':

                // prepara para transformar o arquivo pdf
                $fileTransformation = new Spatie\PdfToImage\Pdf($file);

                // para cada pagina!
                foreach (range(1, $fileTransformation->getNumberOfPages()) as $pageNumber) {
                    // pega o conteudo jpg
                    $fileContents = $fileTransformation->setPage($pageNumber)->getImageData("xpto.jpg");

                    // salva arquivo no banco de dados
                    $retorno = $this->saveFile($fileContents, $registro['titulo'] . '-' . $pageNumber . ".jpg", $registro['id_pai']);

                    // processa no google vision!
                    $retornoOcr = $googleVision->process($fileContents);

                    // pega dentro da array de retorno do google a parte que importa
                    $textoOcr = $retornoOcr['responses'][0]['textAnnotations'][0]['description'];

                    // pega o id do tib ocr
                    $arrOcr = $boTib->getByMetanome('ocr');

                    // salva o dado do OCR
                    $dadosOcr = $this->persiste(false, $arrOcr[0]['id'], $registro['id_criador'], $registro['id_ib_pai'], $textoOcr);
                }

                break;
            // break omitido intensionalmente
            case 'png':
                //Fall through to next case;
            case 'gif':
                //Fall through to next case;
            case 'jpg':
                //Fall through to next case;
            case 'jpe':
                //Fall through to next case;
            case 'jpeg':

                // processa no google vision!
                $retornoOcr = $googleVision->process(file_get_contents($file));

                // pega dentro da array de retorno do google a parte que importa
                $textoOcr = $retornoOcr['responses'][0]['textAnnotations'][0]['description'];

                // pega o id do tib ocr
                $arrOcr = $boTib->getByMetanome('ocr');

                // salva o dado do OCR
                $dadosOcr = $this->persiste(false, $arrOcr[0]['id'], $registro['id_criador'], $registro['id_ib_pai'], $textoOcr);


                break;
            default:
                throw new Exception("Extensão do arquivo não suportado.");
                break;
        }

        // @todo muda a situacao para outra!

    }
}
