<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_RlGrupoPessoa extends App_Model_Dao_Abstract
{
    protected $_name          = "rl_grupo_pessoa";
    protected $_primary       = "id";

    protected $_rowClass = 'Config_Model_Vo_RlGrupoPessoa';

    public function listGrupoPessoa($id_grupo = null, $id_pessoa = null)
    {
        $select = $this->select()
                       ->from(array('c' => $this->_name));

        if(!empty($id_pessoa)){
            $select->where('c.id_pessoa = ?', $id_pessoa);
        }
        if(!empty($id_grupo)){
            $select->where('c.id_grupo = ?', $id_grupo);
        }
        return $this->fetchAll($select)->toArray();
    }

    public function getGrupoPessoalNaInstalacao($idPessoa)
    {
        $query = <<<QUERY
SELECT id_grupo
  FROM rl_grupo_pessoa
  WHERE id_pessoa = :idPessoa
    AND nomehash <> :geral
QUERY;
        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query($query, [
            'idPessoa' => $idPessoa,
            'geral' => 'GERAL',
        ])->fetchAll();
    }
}
