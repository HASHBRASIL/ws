<?php
class Content_CrawlerController extends App_Controller_Action_Twig
{
    /**
     * @var Content_Model_Bo_Ib
     */
    protected $_bo;

    public function init()
    {
        parent::init();
        $this->_bo = new Content_Model_Bo_Crawler();
    }

    public function gridAction() {
        //x($this->identity->time['id']);

        $header = array();
        $header[] = array('campo' => 'crawling_date', 'label' => 'Data de Captura');
        $header[] = array('campo' => 'token', 'label' => 'Termo');
        $header[] = array('campo' => 'page_link', 'label' => 'Página');
        
        $this->header = $header;
        
        $select = $this->_bo->getCrawlerGridSelect($this->identity->time['id']);
//        x($select);
        $this->_gridSelect = $select;
        
        parent::gridAction();
    }

}