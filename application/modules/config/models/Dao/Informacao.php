<?php

class Config_Model_Dao_Informacao extends App_Model_Dao_Abstract
{
	protected $_name          = "tb_informacao";
	protected $_primary       = "id";

	protected $_rowClass = 'Config_Model_Vo_Informacao';

    public function getInfoPessoaByMetanome($idPessoa, $metanome)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['tbi' => $this->_name])
            ->join(['tpi' => 'tp_informacao'],  'tpi.id = id_tinfo', '')
            ->where('tbi.id_pessoa = ?', $idPessoa)
            ->where('tpi.metanome = ?', $metanome);

        return $this->fetchAll($select)->toArray();
    }

    public function getInfoByMetanomeEValor($metanome, $valor)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['tbi' => $this->_name])
            ->join(['tpi' => 'tp_informacao'], 'tpi.id = id_tinfo', '')
            ->where('tpi.metanome = ?', $metanome)
            ->where('tbi.idx_valor @@ plainto_tsquery(?)', $valor);

        return $this->fetchAll($select)->toArray();
    }
}