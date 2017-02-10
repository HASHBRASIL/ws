<?php
/**
 * @author Fernando Augusto
 * @since  17/05/2016
 */
class Emandato_Model_Bo_Robo extends App_Model_Bo_Abstract
{
    /**
     * @var Content_Model_Dao_ItemBiblioteca
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_Grupo();
        parent::__construct();
    }

    public function carregaNoticiasCamara() {

        $boTib = new Content_Model_Bo_TpItemBiblioteca();
        $boIb = new Content_Model_Bo_ItemBiblioteca();

        $tprss = current($boTib->getTipoByMetanome('TPRSS')->toArray());
        $tpnot = current($boTib->getTipoByMetanome('TPNOTICIA')->toArray());
        $tplink = current($boTib->getTipoByIdPaiByMetanome($tprss['id'],'link')->toArray());
        $tpnotlink = current($boTib->getTipoByIdPaiByMetanome($tpnot['id'],'link')->toArray());
        $lstCmpFil = $boTib->getTipoByIdPai($tpnot['id'])->toArray();

        $lstLink = $boIb->getAllIbByTib($tplink['id'])->toArray();
        foreach($lstLink as $rss) {
            $cml = file_get_contents($rss['valor']);
            $xml = simplexml_load_string($cml);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            foreach($array as $linha) {
                foreach($linha as $campo => $valor) {
                    if(is_array($valor)){
                        foreach($valor as $noticia) {
                            $guid = $noticia['guid'];
                            $title = $noticia['title'];
                            $link = $noticia['link'];
                            $description = $noticia['description'];
                            $pubdate = $noticia['pubDate'];
                            $ret = current($boIb->getItemByTibByValor($tpnotlink['id'],$link)->toArray());
                            if(!$ret) {
                                $htmlnot = file_get_contents($link);
                                $editIni = "<meta name='agenciaretranca' content='";
				$editIni2 = "<meta name='radioretranca' content='";
                                $editFim = "' />";
                                $titIni = "<div id=\"tituloNoticia\"><h2>";
                                $titFim = "</h2></div>";
                                $resIni = "<div id=\"resumoNoticia\"><p>";
                                $resFim = "</p></div>";
                                $contIni1 = "<div id=\"conteudoNoticia\">";
                                $contIni2 = "<p>";
                                $contFim = "</p></div>";
                                $contFimAlt = "</p></div>";
                                $contFimAlt2 = "</div></div>";
                                $fontIni = "<div id=\"creditosMateria\"><span>";
                                $fontFim = "</span>";
				

                                $posEditIni = strpos($htmlnot,$editIni) + strlen($editIni);
				if($posEditIni < 100) {
					$posEditIni = strpos($htmlnot,$editIni2) + strlen($editIni2);
				}

                                $posEditFim = strpos($htmlnot,$editFim,$posEditIni);
                                $editoria = substr($htmlnot,$posEditIni,$posEditFim-$posEditIni);

                                $posTitIni = strpos($htmlnot,$titIni,$posEditFim) + strlen($titIni);
                                $posTitFim = strpos($htmlnot,$titFim,$posTitIni);
                                $titulo = substr($htmlnot,$posTitIni,$posTitFim-$posTitIni);

                                $posResIni = strpos($htmlnot,$resIni,$posTitFim) + strlen($resIni);
                                $posResFim = strpos($htmlnot,$resFim,$posResIni);
                                $desc   = substr($htmlnot,$posResIni,$posResFim-$posResIni);
				if(empty($desc)){
					$desc = html_entity_decode($description);
				}

                                $posContIni = strpos($htmlnot,$contIni1,$posResFim);
                                $posContIni = strpos($htmlnot,$contIni2,$posContIni);
                                $posContFim = strpos($htmlnot,$contFim,$posContIni);

                                if($posContFim < 0) {
                                    $posContFim = strpos($htmlnot,$contFimAlt,$posContIni);
                                    if($posContFim < 0) {
                                        $posContFim = strpos($htmlnot,$contFimAlt2,$posContIni);
                                    }
                                }
                                $conteudo = substr($htmlnot,$posContIni,$posContFim-$posContIni);
				$conteudo = html_entity_decode($conteudo);

                                $posFontIni = strpos($htmlnot,$fontIni,$posContFim)+strlen($fontIni);
                                $posFontFim = strpos($htmlnot,$fontFim,$posFontIni);
                                $fonte = substr($htmlnot,$posFontIni,$posFontFim-$posFontIni);
				$fonte = str_replace("\\?","",$fonte);
                                $fonte = str_replace("<br />"," ", $fonte);
                                $havedesc = false;
                                $havechan = false;
                                $arrCmp = array();
                                foreach($lstCmpFil as $fil) {
                                    if($fil['metanome']=="editoria") {
                                        $valor = $editoria;
                                    } else if($fil['metanome']=="description") {
                                        $valor = $desc;
                                    } else if($fil['metanome']=="channel") {
                                        $valor = 'Agência Câmara';
                                    } else if($fil['metanome']=="noticiacredito") {
                                        $valor = $fonte;
                                    } else if($fil['metanome']=="title") {
                                        $valor = $title;
                                    } else if($fil['metanome']=="conteudo") {
                                        $valor = $conteudo;
                                    } else if($fil['metanome']=="link") {
                                        $valor = $link;
                                    }
                                    $arrCmpFil[$fil['id']] = $valor;
                                }
                                $item = $boIb->insere($tpnot['id'],$arrCmpFil);


                            } else {
                                //x($ret);
                                echo "JÁ EXISTE " . $link . "\n";
                            }
                        }
                    } else {
                        echo $campo . " - " . $valor . "\n";
                    }
                }
            }    
        }
    }

    public function gravaImagem($url) {

        $boTib = new Content_Model_Bo_TpItemBiblioteca();
        $boIb = new Content_Model_Bo_ItemBiblioteca();
        $boGrp = new Config_Model_Bo_Grupo();

        $bdimg = current($boTib->getTipoByMetanome('TPBDIMG')->toArray());
        $imglocal = current($boTib->getTipoByIdPaiByMetanome($bdimg['id'],'imglocal')->toArray());
        $imggrd = current($boTib->getTipoByIdPaiByMetanome($bdimg['id'],'imggrd')->toArray());
        $imgmed = current($boTib->getTipoByIdPaiByMetanome($bdimg['id'],'imgmed')->toArray());
        $link = current($boTib->getTipoByIdPaiByMetanome($bdimg['id'],'link')->toArray());

        $config  = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $filedir = $config->getOption('filedir');

        $time = current($boGrp->getGrupoByMetanome('camara'));
        $site = current($boGrp->getGruposByIDPaiByMetanome($time['id'],'SITE'));
        $grupo = current($boGrp->getGruposByIDPaiByMetanome($site['id'],'IMAGENS'));

        $uuidLocal = UUID::v4() . '.jpg';
        $uuidGrd   = UUID::v4() . '.jpg';
        $uuidMed   = UUID::v4() . '.jpg';
        $uuidPeq   = UUID::v4() . '.jpg';

        $directory = $time['id'] . '/' . $grupo['id'] . '/';
        $arquivo = end(explode('/',$url));
        $extensao = end(explode('.',$arquivo));

        if(!file_exists($filedir['path'] . $directory)){
            mkdir($filedir['path'] . $directory, 0755, true);
        }

        $img = file_get_contents($url);
        if($img) {
            file_put_contents($filedir['path'] . $directory . $uuidLocal,$img);
            $simpleimage = new abeautifulsite\SimpleImage($filedir['path'] . $directory . $uuidLocal);
            $simpleimage->fit_to_width(848)
                    ->save($imgGrd)
                    ->fit_to_width(393)
                    ->save($imgMed)
                    ->fit_to_width(292)
                    ->save($imgPeq);

            $dados = array();
            $dados[$imglocal['id']] = $directory . $uuidLocal;
            $dados[$imggrd['id']] = $directory . $uuidGrd;
            $dados[$imgmed['id']] = $directory . $uuidMed;
            $dados[$link['id']] = $url;

            return $boIb->insere($bdimg['id'],$dados);

        } else {
            echo $filedir['path'] . $directory . $uuidLocal . ' inexistente.';

            return false;
        }
        
    }
}
