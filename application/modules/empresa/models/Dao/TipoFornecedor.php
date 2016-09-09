<?php
/**
 * @author Ellyson de Jesus Silva
* @since  23/04/2013
*/
class Empresa_Model_Dao_TipoFornecedor extends App_Model_Dao_Abstract
{
    protected $_name          = "tipo_fornecedor";
    protected $_primary       = "tif_id";
    protected $_namePairs     = "tif_descricao";

    protected $_dependentTables = array('Empresa_Model_Dao_Empresa');
}