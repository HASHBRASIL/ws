<?php
class App_Module_Model_Modulo extends App_Model_Bo_Abstract
{
    protected $_name = "tb_modulos";
    protected $_primary = "mod_id";
    protected $_namePairs = "mod_nome";

    const GER_SERVICO    = 10;
    const GER_MATERIAL   = 11;
    const GER_SIS        = 12;
}