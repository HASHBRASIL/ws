<?php
class Auth_Model_Dao_Workspace extends App_Model_Dao_Abstract
{

    protected $_name         = "tb_workspace";
    protected $_primary      = "id_workspace";

    protected $_rowClass = 'Auth_Model_Vo_Workspace';

    protected $_dependentTables = array('Financial_Model_Dao_AgrupadorFinanceiro', 'Financial_Model_Dao_Contas', 'Financial_Model_Dao_PlanoContas','Financial_Model_Dao_CentroCusto','Service_Model_Dao_CentroCusto', 'Sis_Model_Dao_Sis');

    protected $_referenceMap    = array(
            'Empresa' => array(
                    'columns'           => 'id_empresa',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )

    );
}