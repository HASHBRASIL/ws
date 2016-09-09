<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  12/02/2015
 */
class Freelancer_Model_Dao_Tarefa extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_free_tarefa";
    protected $_primary       = "id_tarefa";

    protected $_rowClass = 'Freelancer_Model_Vo_Tarefa';

    protected $_referenceMap    = array(
    		'Empresa' => array(
                    'columns'           => 'id_empresa',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )

    );


}
