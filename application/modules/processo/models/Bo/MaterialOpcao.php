<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  20/01/2014
 */
class Processo_Model_Bo_MaterialOpcao extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_MaterialOpcao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_MaterialOpcao();
        parent::__construct();
    }

    public function deleteByMaterial($idMaterialProcesso)
    {
        return $this->_dao->delete(array('id_material_processo = ?' => $idMaterialProcesso));
    }
}