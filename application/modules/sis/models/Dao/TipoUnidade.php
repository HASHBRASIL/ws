<?php
class Sis_Model_Dao_TipoUnidade extends App_Model_Dao_Abstract
{

    protected $_name         = "mat_tb_tipo_unidade";
    protected $_primary      = "id_tipo_unidade";
    protected $_namePairs    = "nome";

    protected $_dependentTables = array('Material_Model_Dao_Estoque', 'Material_Model_Dao_Item');

		protected $_rowClass = 'Sis_Model_Vo_TipoUnidade';

    protected $_referenceMap    = array(

    		'TipoCliente' => array(
    				'columns'           => 'id_criacao_usuario',
    				'refTableClass'     => 'Auth_Model_Dao_Usuario',
    				'refColumns'        => 'usu_id'
    		));

}