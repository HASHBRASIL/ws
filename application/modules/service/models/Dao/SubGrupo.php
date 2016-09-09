<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/04/2013
 */
class Service_Model_Dao_SubGrupo extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gs_subgrupo";
    protected $_primary  = "id_subgrupo";


    protected $_rowClass        = 'Service_Model_Vo_SubGrupo';
    protected $_dependentTables = array('Service_Model_Dao_Classe', 'Service_Model_Dao_Servico');

    protected $_referenceMap    = array(
        'Grupo' => array(
            'columns'           => 'id_grupo',
            'refTableClass'     => 'Service_Model_Dao_Grupo',
            'refColumns'        => 'id_grupo'
        )
    );
}