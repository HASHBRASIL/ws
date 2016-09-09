<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/04/2013
 */
class Sis_Model_Dao_TipoEnderecoRef extends App_Model_Dao_Abstract
{
    protected $_name         = "tb_tp_endereco_ref";
    protected $_primary      = array("id_endereco","tie_id");

    protected $_rowClass = 'Sis_Model_Vo_TipoEnderecoRef';

    protected $_referenceMap    = array(
            'Tipo Endereco' => array(
                    'columns'           => 'tie_id',
                    'refTableClass'     => 'Sis_Model_Dao_TipoEndereco',
                    'refColumns'        => 'tie_id'
            ),
            'Endereco' => array(
                    'columns'           => 'id_endereco',
                    'refTableClass'     => 'Sis_Model_Dao_Endereco',
                    'refColumns'        => 'id'
            )
    );

    public function deleteByEndereco($idEndereco)
    {
        $where = array("id_endereco = ?" => $idEndereco);
        return $this->delete($where);
    }
}