<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/07/2013
 */
class Sis_Model_Dao_Contato extends App_Model_Dao_Abstract
{
    protected $_name = "tb_contatos";
    protected $_primary = "id";
    protected $_namePairs = 'nome';

    protected $_rowClass = 'Sis_Model_Vo_Contato';

    protected $_referenceMap    = array(
            'Empresa' => array(
                    'columns'           => 'id_empresas',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            ),
            'Contato Referenciado' => array(
                    'columns'           => 'cre_id',
                    'refTableClass'     => 'Sis_Model_Dao_ContatoReferenciado',
                    'refColumns'        => 'cre_id'
            ),
            'Contato Departamento' => array(
                    'columns'           => 'cdp_id',
                    'refTableClass'     => 'Sis_Model_Dao_ContatoDepartamento',
                    'refColumns'        => 'cdp_id'
            ),
            'Cargo' => array(
                    'columns'           => 'car_id',
                    'refTableClass'     => 'Sis_Model_Dao_Cargo',
                    'refColumns'        => 'car_id'
            ),
            'Marketing' => array(
                    'columns'           => 'smk_id',
                    'refTableClass'     => 'Empresa_Model_Dao_MailMarketing',
                    'refColumns'        => 'smk_id'
            )
    );

}