<?php
class Content_Model_Bo_Crawler extends App_Model_Bo_Abstract
{
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Content_Model_Dao_Crawler();
        $this->_daoToken = new Content_Model_Dao_CrawlerToken();
        parent::__construct();
    }

    public function getCrawlerGridSelect($time) {
        return $this->_dao->getCrawlerGridSelect($time);
    }

}