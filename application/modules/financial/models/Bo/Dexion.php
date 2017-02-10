<?php

class Financial_Model_Bo_Dexion extends App_Model_Bo_Abstract
{

    protected $_dao;

    public $fields =  array(
        'datalivro' => 'datalivro',
        'lancamento' => 'lancamento',
        'valordebito' => 'valordebito',
        'valorcredito' => 'valorcredito',
        'debcred' => 'debcred',
        'contade' => 'contade',
        'contapara' => 'contapara',
        'texto1' => 'texto1'
    );

    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_Dexion();
        parent::__construct();
    }

    public function getSelectDexion($time) {
        return $this->_dao->getSelectDexion($time);
    }

    public function importFiles()
    {

        // @todo para rodar - colocar arquivos em alguma pasta no servidor
        // @todo ajustar array abaixo para os arquivos a serem processado

        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/BB2012.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/BB2013.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/BRB2012.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/BRB2013.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/CAIXA2012.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/CAIXA2013.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/Fernando2012.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/Fernando2013.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/Frederico2012.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/Frederico2013.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/HSBC2012.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/HSBC2013.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/JoseClaudio2012.XML";
        $arquivos[] = "/Users/solbisio/dev/hash-ws-php/public/planilhasfred/JoséClaudio2013.XML";

        foreach ($arquivos as $arquivo) {
            $dados['arquivo'] = $arquivo;
            $contents = file_get_contents($arquivo);
            $this->import(substr(strrchr($arquivo, "/"),1), $contents);
        }
    }

    public function import($arquivo, $contents)
    {

        $dados['arquivo'] = $arquivo;

        $objXml = new SimpleXMLIterator($contents);

        $boDexion = new Financial_Model_Bo_Dexion();

        $objXml->rewind();
        // $objXml header
        $objXml->next();

        while ($objXml->valid()) {

            // $objxml pagina
            $item = $objXml->getChildren();
            $item->rewind();
            $item->next();

            while ($item->valid()) {
                    $desc = array();
                    // caso seja conta
                if ($item->current()->attributes()['XPos'] == '23,32') {
                    $dados['contade'] = $item->current();
                }
                if ($item->current()->attributes()['XPos'] == '73,59') {
                    $dados['sequenciade'] = $item->current();
                }

                if ($item->current()->attributes()['XPos'] == '111,16') {
                    $dados['descricaode'] = $item->current();
                }

                if ($item->current()->attributes()['XPos'] == '14,59') {
                    // campo de data
                    $dataCompare = $item->current()->__toString();
                    if (($dataCompare{0} == '0') ||
                        ($dataCompare{0} == '1') ||
                        ($dataCompare{0} == '2') ||
                        ($dataCompare{0} == '3') )
                    {

                        $dados['datalivro'] = $item->current();
                        $item->next();
                        $dados['lancamento'] = $item->current();
                        $item->next();
                        $dados['valordebito'] = $item->current();
                        $item->next();
                        $dados['valorcredito'] = $item->current();
                        $item->next();
                        $dados['saldo'] = $item->current();
                        $item->next();
                        $dados['contapara'] = $item->current();
                        $item->next();

                        while ($item->current()->attributes()['XPos'] == '71,21') {
                            $desc[] =  $item->current();
                            $item->next();
                        }

                        $dados['texto1'] = implode(" ", $desc);

                        $dados['debcred'] = $item->current();
                        $item->next();

                        $dados['segundoid'] = $item->current();
                        $item->next();
                        // salva aqui
                        $row = $boDexion->_dao->createRow();

                        // falta validação de unicidade
                        $row->setFromArray($dados);
                        $row->id = UUID::v4();
                        $row->save();
                    }

                }
                $item->next();
            }
            $objXml->next();

        }
    }

    public function process()
    {

    }


}