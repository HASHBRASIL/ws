<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_RlPermissaoPessoa extends App_Model_Dao_Abstract
{
	protected $_name          = "rl_permissao_pessoa";
	protected $_primary       = "id";

	protected $_rowClass = 'Config_Model_Vo_RlPermissaoPessoa';


    public function findPermissao($idPessoa, $idGrupo, $idServico)
    {
        $select = $this->select()
            ->where('id_pessoa =?', $idPessoa)
            ->where('id_grupo = ?', $idGrupo)
            ->where('id_servico = ?', $idServico);

        return $this->fetchOne($select);
    }

    public function apagaPermissoesDoServico($idServico)
    {
        $qry = $this->_db->prepare('DELETE FROM rl_permissao_pessoa WHERE id_servico = :id_servico');
        $qry->bindParam(':id_servico', $idServico);

        return $qry->execute();
    }

}