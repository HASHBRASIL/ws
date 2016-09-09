<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/04/2013
 */
class Sis_Model_Dao_TipoEndereco extends App_Model_Dao_Abstract
{
    protected $_name         = "tb_tipo_endereco";
    protected $_primary      = "tie_id";
    protected $_namePairs    = 'tie_descricao';

    protected $_dependentTables = array('Sis_Model_Dao_Endereco', 'Sis_Model_Dao_TipoEnderecoRef');
}