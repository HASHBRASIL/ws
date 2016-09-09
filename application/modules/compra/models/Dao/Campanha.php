<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  17/10/2013
 */
class Compra_Model_Dao_Campanha extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_co_campanha";
    protected $_primary       = "id_campanha";
    protected $_namePairs     = "nome";

    protected $_dependentTables = array('Compra_Model_Dao_CampanhaCorporativo');

}