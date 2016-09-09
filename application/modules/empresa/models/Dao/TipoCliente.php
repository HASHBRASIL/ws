<?php
/**
 * @author Ellyson de Jesus Silva
* @since  23/04/2013
*/
class Empresa_Model_Dao_TipoCliente extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_tipo_cliente";
    protected $_primary       = "tic_id";
    protected $_namePairs     = "tic_descricao";

    protected $_dependentTables = array('Empresa_Model_Dao_Empresa');
}