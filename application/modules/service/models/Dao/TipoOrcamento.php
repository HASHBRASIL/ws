<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 01/07/2013
 */
class Service_Model_Dao_TipoOrcamento extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gs_tp_orcamento";
    protected $_primary  = "id_tp_orcamento";

    protected $_dependentTables = array( 'Service_Model_Vo_Orcamento' );
}