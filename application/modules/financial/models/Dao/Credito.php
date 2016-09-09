<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  04/07/2013
 */
class Financial_Model_Dao_Credito extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_credito";
    protected $_primary       = "id_credito";

    protected $_rowClass = 'Financial_Model_Vo_Credito';

    protected $_referenceMap    = array(
    		'Empresa' => array(
                    'columns'           => 'empresas_id',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )

    );


}

