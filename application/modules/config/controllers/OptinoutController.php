<?php
/**
 * HashWS
 */
class Config_OptinoutController extends App_Controller_Action_Twig
{

    public function init()
    {
        parent::init();

        $this->_translate = Zend_Registry::get('Zend_Translate');

        // $this->_helper->layout()->setLayout('publico');
        $this->session = new Zend_Session_Namespace('validacao');
    }


    public function cancelmailAction() {
        $pes = $_GET['p'];
        $time = $_GET['t'];

        $mdlIb = new Content_Model_Bo_ItemBiblioteca();
        $mdlTib = new Config_Model_Bo_Tib();
        $mdlPes = new Legacy_Model_Bo_Pessoa();
        $mdlGrp = new Config_Model_Bo_Grupo();
        $mdlInf = new Config_Model_Bo_Informacao();
        $mdlTinf = new Config_Model_Bo_TipoInformacao();

        $verifOptOut = current($mdlTinf->getByMetanome('OPTOUTEMAIL'));
        $lstOut = $mdlInf->getInfoPessoaByMetanome($pes,$verifOptOut['metanome']);

        if(!$lstOut) {
            $mdlInf->addInformacao($pes, 'OPTOUTEMAIL', 'S', $time);
        }

        // echo $this->_translate->translate('CANCELAMENTO_EMAIL');

        // exit;
    }

    public function cancelcandAction() {
        $pes = $_GET['c'];

        $ibBo = new Content_Model_Bo_ItemBiblioteca();
        $ibBo->atualizastatuscand($idIbPai,'C');

        // $mdlIb = new Content_Model_Bo_ItemBiblioteca();
        // $mdlTib = new Config_Model_Bo_Tib();
        // $mdlPes = new Legacy_Model_Bo_Pessoa();
        // $mdlGrp = new Config_Model_Bo_Grupo();
        // $mdlInf = new Config_Model_Bo_Informacao();
        // $mdlTinf = new Config_Model_Bo_TipoInformacao();

        // $verifOptOut = current($mdlTinf->getByMetanome('OPTOUTEMAIL'));
        // $lstOut = $mdlInf->getInfoPessoaByMetanome($pes,$verifOptOut['metanome']);

        // if(!$lstOut) {
        //     $mdlInf->addInformacao($pes, 'OPTOUTEMAIL', 'S', $time);
        // }

        // echo $this->_translate->translate('CANCELAMENTO_EMAIL');

        // exit;
    }

}
