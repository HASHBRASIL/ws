<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  20/01/2014
 */
class Processo_Model_Dao_MaterialOpcao extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_ta_gp_material_processo_x_opcao";
    protected $_primary  = array('id_material_processo', 'id_opcao');
}