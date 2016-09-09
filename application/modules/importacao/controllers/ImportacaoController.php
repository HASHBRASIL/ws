<?php

class Importacao_ImportacaoController extends App_Controller_Action_AbstractCrud
{

    protected $_aclActionAnonymous = array("index");

    public function init()
    {
        parent::init();
    }

    public function catchDataAction()
    {
        //echo 'bloqueado';exit;
        
        set_time_limit(259200); //3 dias
        $tipo = $this->_request->getParam('tipo','');
        $importador = new Controller_Importador();
        //$tipo = $this->_request->getParam('tipo','');
        $tipo = 'catchComissoesProposicoes';
        
        switch ($tipo) {
            case 'catchContentFromEditorias':
                    $model = new CamaraNoticia();
                    $importador->catchContentFromEditorias($model);
                break;
            case 'generateUrlFromDeputados':
                    //$importador->generateUrlFromDeputados();
                break;
            case 'catchComissoesPermanentes':
                    $camaraComissao     = new CamaraComissao();
                    $camaraComissaoMeta = new CamaraComissaoMeta();

                    $comissoes = $importador->catchComissoesPermanentes();
                    
                    foreach($comissoes as $sigla => $comissao)
                    {
                        $camaraComissao->addComissao(array( 'id_status' => 1,
                                                            'tx_nome' => $comissao['tx_nome'],
                                                            'tx_url' => $comissao['tx_url'],
                                                            'dt_check' => date('Y-m-d H:i:s'),
                                                            'dt_created' => date('Y-m-d H:i:s'),
                                                            'tx_sigla' => $sigla));
                        if(is_array($comissao['menu'])){
                            foreach($comissao['menu'] as $menu)
                            {
                                $value = array('dados' => $menu['dados'], 'url' => $menu['url']);
                                $camaraComissaoMeta->addMeta(array('tx_comissao' => $sigla, 'tx_meta' => $menu['item'], 'tx_value' => json_encode($value)));
                            }
                        }
                    }
                break;
            case 'catchComissoesPermanentesNoticias':
                    $camaraComissao     = new CamaraComissao();
                    $camaraComissaoMeta = new CamaraComissaoMeta();
                    $comissaoNoticia    = new ComissaoNoticia();

                    $listaComissoes = $camaraComissaoMeta->listaComissaoMetaByMeta('Notícias', '2016-01-30 12:02:21');

                    foreach($listaComissoes as $comissao)
                    {
                        $dados = json_decode($comissao['tx_value']);
                        $pagina = $comissaoNoticia->getUltimaPaginaNoticia($comissao['tx_sigla']);
                        
                        if(empty($pagina)){
                            $pagina = 1;
                        }

                        $importador->catchMateriasComissaoRecursive($comissaoNoticia, $comissao['tx_sigla'], $dados->url, $pagina);
                        $camaraComissao->updComissao(array('id_comissao' => $comissao['id_comissao'], 'dt_check' => date('Y-m-d H:i:s')));
                    }
                break;
            case 'catchProposicoes':
                    $comissaoNoticia    = new Proposicoes();
                    $importador->catchProposicoes($comissaoNoticia);
                break;
            case 'catchComissoesProposicoes':
                    $camaraComissaoMeta     = new CamaraComissaoMeta();
                    $camaraComissaoProposicao = new CamaraComissaoProposicao();
                
                    $lista = $camaraComissaoMeta->listaComissaoMetaByMeta('Em tramitação na Comissão');
                    var_dump($lista);
                    foreach($lista as $comissao)
                    {
                        echo '1- ';
                        continue;
                        $dados = json_decode($comissao['tx_value']);
                        $id_comissao = $importador->catchIdComissao($dados->url);
                        
                        if(empty($id_comissao)){
                            continue;
                        }
                        
                        $url = 'http://www.camara.gov.br/sileg/Prop_lista.asp?OrgaoOrigem=todos&Comissao='.$id_comissao.'&Situacao=-1';
                        $importador->catchComissoesProposicoes($camaraComissaoProposicao, $id_comissao, $url, 1);
                    }

                break;
                
            case 'teste':
                    echo 1;
                break;
        }
    }
    
    public function routineAction()
    {
        exit;
        $tipo = $this->_request->getParam('tipo','');
        
        $routine = new Controller_Routine();
        
        switch ($tipo) {
            case 'catchContentFromEditorias':
                    $routine->catchContentFromEditorias();
                break;
            case 'generateUrlFromDeputados':
                    $routine->generateUrlFromDeputados();
                break;
            case 'teste':
                    echo 2;
                break;
        }
    }
}