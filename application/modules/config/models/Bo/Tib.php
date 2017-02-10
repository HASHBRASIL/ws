<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_Tib extends App_Model_Bo_Abstract
{
    /**
     * @var Config_Model_Dao_Servico
     */
    protected $_dao;
    
    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_Tib();
        parent::__construct();
    }
	
    public function getTipoItemBibliotecaGrid($idPai = NULL) {
        return $this->_dao->getTipoItemBibliotecaGrid($idPai);
    }
    
    public function getTipoItemBibliotecaByMetanome($metanome) {
        return $this->_dao->getTipoItemBibliotecaByMetanome($metanome);
    }
    
    public function getById($tib) {
        return $this->_dao->getById($tib);
    }

    public function getFilhosById($id) {
        $data = $this->_dao->fetchAll(array('id_tib_pai = ?' => $id));

        return $data;
    }
    
    public function getTemplateByIdTibPai($id_tib_pai) {
        return $this->_dao->getTemplateByIdTibPai($id_tib_pai);
    }
    
    public function getTemplateById($id)
    {
        return $this->_dao->getTemplateById($id);
    }

    public function getByMetanome($metanome) {
        return $this->_dao->getBymetanome($metanome);
    }

    public function getByIdPaiByMetanome($id_pai,$metanome) {
        return $this->_dao->getByIdPaiByMetanome($id_pai,$metanome);
    }
}