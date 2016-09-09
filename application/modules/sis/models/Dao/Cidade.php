<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_Model_Dao_Cidade extends App_Model_Dao_Abstract
{
    protected $_name = "tb_cidades";
    protected $_primary = "cid_id";

    protected $_namePairs = 'cid_nome';

    protected $_dependentTables = array('Sis_Model_Dao_Endereco');
}