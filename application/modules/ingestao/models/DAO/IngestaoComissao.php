<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Ingestao_Model_Dao_IngestaoComissao extends App_Model_Dao_Abstract
{
    protected $_name          = "ing_camara_comissao";
    protected $_primary       = "id_comissao";
    protected $_namePairs     = "nome";
    
    protected $_rowClass = 'Ingestao_Model_Vo_IngestaoComissao';
    
    //protected $_dependentTables = array('Financial_Model_Dao_Financial');

    
}