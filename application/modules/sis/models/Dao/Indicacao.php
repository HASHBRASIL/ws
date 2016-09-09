<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_Model_Dao_Indicacao extends App_Model_Dao_Abstract
{
    protected $_name = "tb_indicacao";
    protected $_primary = "ind_id";

    protected $_namePairs = 'ind_descricao';

    protected $_dependentTables = array('Empresa_Model_Dao_Empresa');
}