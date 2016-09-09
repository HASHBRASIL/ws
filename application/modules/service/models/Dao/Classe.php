<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/04/2013
 */
class Service_Model_Dao_Classe extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gs_classe";
    protected $_primary  = "id_classe";

    protected $_rowClass = 'Service_Model_Vo_Classe';
    protected $_dependentTables = array('Service_Model_Dao_Servico');
    
    protected $_referenceMap    = array(
            'SubGrupo' => array(
                    'columns'           => 'id_subgrupo',
                    'refTableClass'     => 'Service_Model_Dao_SubGrupo',
                    'refColumns'        => 'id_subgrupo'
            )
    );
}