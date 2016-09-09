<?php
class Auxiliar_CnaeController extends App_Controller_Action
{

 	public function init()
    {
        parent::init();
        $this->_helper->layout()->setLayout('novo_hash');
    }

    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }

    public function indexAction()
    {
    	set_time_limit('1200');
    	$tibDao = new Config_Model_Dao_Tib();
    	$tibBo = new Config_Model_Bo_Tib();
    	$rowsetTib = $tibBo->find("metanome = 'TPCNAE'");
    	$CNAECODSEC = $tibDao->fetchRow("metanome = 'CNAECODSEC'");
    	$CNAEDESCSEC = $tibDao->fetchRow("metanome = 'CNAEDESCSEC'");
    	$CNAECODSUBCLAS = $tibDao->fetchRow("metanome = 'CNAECODSUBCLAS'");
    	$CNAEDESCSUBCLAS = $tibDao->fetchRow("metanome = 'CNAEDESCSUBCLAS'");

    	$ibBo = new Content_Model_Bo_ItemBiblioteca();
        $rowset = $ibBo->find("id_tib = '".$rowsetTib[0]->id."'");//, 'valor', 30, 0);

        $data = array();
        $i = 0;
        foreach ($rowset as $k => $v){
        	$ibBo2 = new Content_Model_Bo_ItemBiblioteca();
        	$rowset2 = $ibBo2->find("id_ib_pai = '".$v->id."'");

        	$data[$i]['id'] = $v->id;
        	foreach ($rowset2 as $k2 => $v2){
        		switch ($v2->id_tib) {
        			case $CNAECODSEC->id;
        				$data[$i]['CNAECODSEC'] = $v2->valor;
        			break;
        			case $CNAEDESCSEC->id;
        				$data[$i]['CNAEDESCSEC'] = $v2->valor;
        			break;
        			case $CNAECODSUBCLAS->id;
        				$data[$i]['CNAECODSUBCLAS'] = $v2->valor;
        			break;
        			case $CNAEDESCSUBCLAS->id;
        				$data[$i]['CNAEDESCSUBCLAS'] = $v2->valor;
        			break;
        		}
        	}
        	$i++;
        }
        $header = array();
        $header[] = array('campo' => 'CNAECODSEC', 'label' => $CNAECODSEC->nome);
        $header[] = array('campo' => 'CNAEDESCSEC', 'label' => $CNAEDESCSEC->nome);
        $header[] = array('campo' => 'CNAECODSUBCLAS', 'label' => $CNAECODSUBCLAS->nome);
        $header[] = array('campo' => 'CNAEDESCSUBCLAS', 'label' => $CNAEDESCSUBCLAS->nome);

        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $data, 'header' => $header );
    }

}