<?php

class Content_Model_Dao_CrawlerToken extends App_Model_Dao_Abstract
{
    protected $_name          = "app_crawler_token";
    protected $_primary       = "id";

    protected $_rowClass = 'Content_Model_Vo_CrawlerToken';

}