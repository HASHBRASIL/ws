<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/09/2013
 */
class Material_Model_Dao_Opcao extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_opcao";
    protected $_primary  = "id_opcao";
    protected $_namePairs = "nome";

    protected $_rowClass = 'Material_Model_Vo_Opcao';

    protected $_referenceMap    = array(
            'Atributo' => array(
                    'columns'           => 'id_atributo',
                    'refTableClass'     => 'Material_Model_Dao_Atributo',
                    'refColumns'        => 'id_atributo'
            ),
    );
}