<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/04/2013
 */
class Sis_Model_Dao_Endereco extends App_Model_Dao_Abstract
{
    protected $_name  = "tb_enderecos";
    protected $_id    = "id";

    protected $_rowClass = 'Sis_Model_Vo_Endereco';

    protected $_dependentTables = array('Sis_Model_Dao_TipoEnderecoRef', 'Material_Model_Dao_Nfe');

    protected $_referenceMap    = array(
            'Empresa' => array(
                    'columns'           => 'id_empresas',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            ),
            'Cidade' => array(
                    'columns'           => 'cid_id',
                    'refTableClass'     => 'Sis_Model_Dao_Cidade',
                    'refColumns'        => 'cid_id'
            ),
            'Estado' => array(
                    'columns'           => 'ufs_id',
                    'refTableClass'     => 'Sis_Model_Dao_Estado',
                    'refColumns'        => 'ufs_id'
            )

    );
}