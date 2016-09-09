<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/07/2013
 */
class Sis_Model_Dao_ContatoReferenciado extends App_Model_Dao_Abstract
{
    protected $_name = "tb_contato_referenciado";
    protected $_primary = "cre_id";

    protected $_namePairs = 'cre_descricao';
}