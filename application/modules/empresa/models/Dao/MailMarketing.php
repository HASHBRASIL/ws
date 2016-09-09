<?php
/**
 * @author Ellyson de Jesus Silva
* @since  23/04/2013
*/
class Empresa_Model_Dao_MailMarketing extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_status_mailmkt";
    protected $_primary       = "smk_id";
    protected $_namePairs     = "smk_descricao";

    protected $_dependentTables = array('Empresa_Model_Dao_Empresa');
}