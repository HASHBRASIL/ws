<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_ServicoMetadata extends App_Model_Dao_Abstract
{
	protected $_name          = "tb_servico_metadata";
	protected $_primary       = "id";
	protected $_namePairs	  = "metanome";

	protected $_rowClass = 'Config_Model_Vo_ServicoMetadata';

	protected $_dependentTables = array('Config_Model_Dao_Servico');

    public function getByMetanome($idservico, $metanome)
    {
        $select = $this->select();
        $select->where('id_servico = ?', $idservico)
            ->where('metanome = ?', $metanome);

        return $this->fetchAll($select)->toArray();
    }

    public function getByServico($idservico)
    {
        $select = $this->select();
        $select->where('id_servico = ?', $idservico);

        return $this->fetchAll($select)->toArray();
    }

    public function apagaMetadadosDoServico($idServico)
    {
        $qry = $this->_db->prepare('DELETE FROM tb_servico_metadata WHERE id_servico = :id_servico');
        $qry->bindParam(':id_servico', $idServico);

        return $qry->execute();
    }

    public function apagaMetadado($idMetadado){
        $qry = $this->_db->prepare('DELETE FROM tb_servico_metadata WHERE id = :id_metadado');
        $qry->bindParam(':id_metadado', $idMetadado);

        return $qry->execute();
    }
}
