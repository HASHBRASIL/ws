<?php
/**
 * @author Ellyson de Jesus Silva
* @since  23/04/2013
*/
class Empresa_Model_Dao_Portal extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_portal_cadastrado";
    protected $_primary       = "poc_id";
    protected $_namePairs     = "poc_descricacao";

    protected $_dependentTables = array('Empresa_Model_Dao_Empresa');
}