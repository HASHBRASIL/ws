<?php

class Content_Model_Dao_Crawler extends App_Model_Dao_Abstract
{
    protected $_name          = "app_crawler";
    protected $_primary       = "id";

    protected $_rowClass = 'Content_Model_Vo_Crawler';

    public function getCrawlerGridSelect($time){
        
        $select = $this->select()
                ->from(array($this->_name),array('id',"to_char(crawling_date,'DD/MM/YYYY HH24:MI:SS') as crawling_date",'token','page_link'))
                ->where('time = ?',$time)
                ->order(array('crawling_date DESC'));
        return $select;
    }

}