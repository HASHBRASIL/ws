<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/09/2013
 */
class Material_Model_Dao_Atributo extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_atributo";
    protected $_primary  = "id_atributo";
    protected $_namePairs = "nome";

    protected $_rowClass = 'Material_Model_Vo_Atributo';

    protected $_referenceMap    = array(
            'Opcao' => array(
                    'columns'           => 'id_opcao',
                    'refTableClass'     => 'Material_Model_Dao_Opcao',
                    'refColumns'        => 'id_opcao'
            )
    );
}