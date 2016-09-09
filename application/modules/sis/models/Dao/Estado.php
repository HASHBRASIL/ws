<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_Model_Dao_Estado extends App_Model_Dao_Abstract
{
    protected $_name = "tb_ufs";
    protected $_primary = "ufs_id";
    protected $_namePairs = 'ufs_sigla';

    protected $_dependentTables = array('Sis_Model_Dao_Endereco');
}