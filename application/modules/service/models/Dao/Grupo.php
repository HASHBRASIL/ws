<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Service_Model_Dao_Grupo extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gs_grupo";
    protected $_primary  = "id_grupo";

    protected $_rowClass = 'Service_Model_Vo_Grupo';
    protected $_dependentTables = array( 'Service_Model_Dao_SubGrupo', 'Service_Model_Dao_Servico');

}