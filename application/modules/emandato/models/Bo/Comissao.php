<?php
/**
 * @author Fernando Augusto
 * @since  17/05/2016
 */
class Emandato_Model_Bo_Comissao extends App_Model_Bo_Abstract
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

    public function getComissao($id){
        return current($this->_dao->find($id)->toArray());
    }

    public function getBasicConfigHeader($svc){
        $ret = parent::getBasicConfigHeader($svc);

        if(!$ret){
            $ret = array( 
                                array('campo' => 'nome',  'label' => 'Nome', 'tipo' => 'text' ),
                                array('campo' => 'metanome',   'label' => 'Sigla', 'tipo' => 'text')
                            );
        }

        return $ret;
    }

    public function getSelectGridComissoes() 
    {
        $grpBO = new Config_Model_Bo_Grupo();
        $cnlBO = new Config_Model_Bo_Canal();

        $camara = current($grpBO->getGrupoByMetanome('camara'));
        $canal = current($cnlBO->getCanalByMetanome('COMISSAO'));
        $comissoes = $this->_dao->select()
                               ->where('id_pai = ?',$camara['id'])
                               ->where('id_canal = ?',$canal['id']);
        return $comissoes;
    }

    public function importaIngestao() 
    {
        $ingBO = new Ingestao_Model_Bo_IngestaoComissao();
        $grpBO = new Config_Model_Bo_Grupo();
        $cnlBO = new Config_Model_Bo_Canal();
        $tgmBO = new Config_Model_Bo_GrupoMetadata();

        $camara = current($grpBO->getGrupoByMetanome('camara'));
        $canal = current($cnlBO->getCanalByMetanome('COMISSAO'));

        $rsCms = $ingBO->todos()->toArray();
        $cnt = 0;
        $this->_dao->beginTransaction();
        foreach($rsCms as $row){
            if(!$grpBO->getGruposByIDPaiByMetanome( $camara['id'], strtoupper($row['tx_sigla']))){
                $nome = current(explode('-',$row['tx_nome']));
                $grpBO->insere($nome,strtoupper($row['tx_sigla']),$camara['id'],'EU SEI NADAR',$canal['id'],$camara['id_criador'],null);
                $cnt++;
            }
        }
        $this->_dao->commit();
        return $cnt;
    }

    public function gerasite($idcomissao) {
        $grp = current($this->_dao->find($idcomissao)->toArray());
        $grpBO = new Config_Model_Bo_Grupo();
        $cnlBO = new Config_Model_Bo_Canal();
        $tgmBO = new Config_Model_Bo_GrupoMetadata();

        $this->_dao->beginTransaction();

        $idSite = UUID::v4();
        $idMain = UUID::v4();
        $grpBO->insere($grp['metanome'],'SITE',$idcomissao,$grp['metanome'],null,$grp['id_criador'],null,$idSite);
        $tgmBO->insere($idSite,'cms_alias',strtolower($grp['metanome']));
        $tgmBO->insere($idSite,'cms_template','emandato');
        $tgmBO->insere($idSite,'cms_layout','default');
        $tgmBO->insere($idSite,'cms_area_menu','menuPrincipal');
        $tgmBO->insere($idSite,'cms_area_coluna','itensBox');
        $tgmBO->insere($idSite,'cms_area_rodape','rodapePrincipal');
        $tgmBO->insere($idSite,'cms_area_topo','publicidadeTopo,itensDestaques');
        $tgmBO->insere($idSite,'cms_visivel','true');
        $tgmBO->insere($idSite,'cms_menulista','');
        $tgmBO->insere($idSite,'cms_area_conteudo','itensColunas,agendaCalendario,midiaGaleria,fotosGaleria');
        $tgmBO->insere($idSite,'cms_menuperfil',$idMain);

        $grpBO->insere('Comissão','MAINMENU',$idSite,null,null,$grp['id_criador'],null,$idMain);
        $tgmBO->insere($idMain,'cms_menuperfil','true');
        $tgmBO->insere($idMain,'cms_visivel','true');
        $tgmBO->insere($idMain,'cms_ordem',0);

        $idNots = UUID::v4();
        $grpBO->insere('Notícias','NOTICIAS',$idMain,null,null,$grp['id_criador'],null,$idNots);
        $tgmBO->insere($idNots,'cms_menutipo','editoria');
        $tgmBO->insere($idNots,'cms_area_conteudo','itensBlog');
        $tgmBO->insere($idNots,'cms_area_topo','');
        $tgmBO->insere($idNots,'cms_area_menu','menuPrincipal');
        $tgmBO->insere($idNots,'cms_area_coluna','itensBox');
        $tgmBO->insere($idNots,'cms_area_rodape','rodapePrincipal');
        $tgmBO->insere($idNots,'cms_visivel','true');
        $tgmBO->insere($idNots,'cms_ordem','0');
        
        $idAtivs = UUID::v4();
        $grpBO->insere('Atividade Parlamentar','ATIVIDADES',$idMain,null,null,$grp['id_criador'],null,$idAtivs);
        $tgmBO->insere($idAtivs,'cms_menutipo','editoria');
        $tgmBO->insere($idAtivs,'cms_area_conteudo','itensBlog');
        $tgmBO->insere($idAtivs,'cms_area_topo','');
        $tgmBO->insere($idAtivs,'cms_area_menu','menuPrincipal');
        $tgmBO->insere($idAtivs,'cms_area_coluna','itensBox');
        $tgmBO->insere($idAtivs,'cms_area_rodape','rodapePrincipal');
        $tgmBO->insere($idAtivs,'cms_visivel','true');
        $tgmBO->insere($idAtivs,'cms_ordem','1');

        $idGals = UUID::v4();
        $grpBO->insere('Galerias','GALERIAS',$idMain,null,null,$grp['id_criador'],null,$idGals);
        $tgmBO->insere($idGals,'cms_menutipo','editoria');
        $tgmBO->insere($idGals,'cms_area_conteudo','itensBlog');
        $tgmBO->insere($idGals,'cms_area_topo','');
        $tgmBO->insere($idGals,'cms_area_menu','menuPrincipal');
        $tgmBO->insere($idGals,'cms_area_coluna','itensBox');
        $tgmBO->insere($idGals,'cms_area_rodape','rodapePrincipal');
        $tgmBO->insere($idGals,'cms_visivel','true');
        $tgmBO->insere($idGals,'cms_ordem','2');

        $idProjs = UUID::v4();
        $grpBO->insere('Projetos','PROJETOS',$idMain,null,null,$grp['id_criador'],null,$idProjs);
        $tgmBO->insere($idProjs,'cms_menutipo','editoria');
        $tgmBO->insere($idProjs,'cms_area_conteudo','itensBlog');
        $tgmBO->insere($idProjs,'cms_area_topo','');
        $tgmBO->insere($idProjs,'cms_area_menu','menuPrincipal');
        $tgmBO->insere($idProjs,'cms_area_coluna','itensBox');
        $tgmBO->insere($idProjs,'cms_area_rodape','rodapePrincipal');
        $tgmBO->insere($idProjs,'cms_visivel','true');
        $tgmBO->insere($idProjs,'cms_ordem','3');

        $idRadio = UUID::v4();
        $grpBO->insere('Radio','AUDIOS',$idMain,null,null,$grp['id_criador'],null,$idRadio);
        $tgmBO->insere($idRadio,'cms_menutipo','editoria');
        $tgmBO->insere($idRadio,'cms_area_conteudo','itensBlog');
        $tgmBO->insere($idRadio,'cms_area_topo','');
        $tgmBO->insere($idRadio,'cms_area_menu','menuPrincipal');
        $tgmBO->insere($idRadio,'cms_area_coluna','itensBox');
        $tgmBO->insere($idRadio,'cms_area_rodape','rodapePrincipal');
        $tgmBO->insere($idRadio,'cms_visivel','true');
        $tgmBO->insere($idRadio,'cms_ordem','4');

        $idWebTV = UUID::v4();
        $grpBO->insere('WebTV','VIDEOS',$idMain,null,null,$grp['id_criador'],null,$idWebTV);
        $tgmBO->insere($idWebTV,'cms_menutipo','editoria');
        $tgmBO->insere($idWebTV,'cms_area_conteudo','itensBlog');
        $tgmBO->insere($idWebTV,'cms_area_topo','');
        $tgmBO->insere($idWebTV,'cms_area_menu','menuPrincipal');
        $tgmBO->insere($idWebTV,'cms_area_coluna','itensBox');
        $tgmBO->insere($idWebTV,'cms_area_rodape','rodapePrincipal');
        $tgmBO->insere($idWebTV,'cms_visivel','true');
        $tgmBO->insere($idWebTV,'cms_ordem','5');

        $this->_dao->commit();
    }

}