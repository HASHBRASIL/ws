<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  23/04/2013
 */
class Sis_Model_Dao_TipoPessoa extends App_Model_Dao_Abstract
{
    protected $_name = "tb_tipo_pessoa";
    protected $_primary = "tps_id";
    protected $_namePairs = "tps_descricao";

    protected $_dependentTables = array('Empresa_Model_Dao_Empresa');
}