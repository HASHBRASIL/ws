<?php
/**
 * @author Vinicius Leônidas
 * @since 30/01/2014
 */
class Rh_Model_Dao_DadosPonto extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_dados_ponto';
	protected $_primary = 'id_rh_dados_ponto';

	public function listaPontoManual($idPonto = null, $data, $idFuncionario, $horarioPadrao)
	{
		
		$select = $this->_db->select()
		->from(array('dp' => $this->_name ))
		->where('dp.id_rh_funcionario = ?', $idFuncionario)
		->where('dp.duplicado  in(?)', array(Rh_Model_Bo_DadosPonto::DUPLICADO_APROVADO,Rh_Model_Bo_DadosPonto::NAO_DUPLICADO ))
		->joinLeft(array('jp' => 'tb_rh_justificacao_ponto'), "dp.id_rh_justificacao_ponto = jp.id_rh_justificacao_ponto", array('justificativa' => 'jp.descricao'))
		->order('dp.data')
		->order(new Zend_Db_Expr('-posicao DESC'))
		->order('hora')
		->where('dp.ativo = ?', App_Model_Dao_Abstract::ATIVO);

		if (!empty($idPonto)) {

			$select->where('dp.id_rh_registro_ponto = ?', $idPonto);

		}
		if($horarioPadrao && !empty($horarioPadrao['fechamento'])){
			$proxData = new Zend_Date($data);
			$proxData->addDay(1);
			$select->where("(dp.data = '{$data}' and dp.hora > ?) or (dp.data = '{$proxData->toString('yyyy-MM-dd')}' and dp.hora < ? )", $horarioPadrao['fechamento']);
		}else{
			$select->where('dp.data = ?', $data);
		}
		return $this->_db->fetchAll($select);
	}

	public function selectPaginator(array $options = null)
	{
	    $select = $this->_db->select();
        $select->from(array('tdp' => $this->_name), array('id_rh_dados_ponto', 'nsr', 'pis', 'data', 'hora', 'duplicado', 'descricao'))
	           ->joinLeft(array('tf' => 'tb_rh_funcionario'), 'tdp.id_rh_funcionario = tf.id_rh_funcionario', null)
               ->joinLeft(array('te' => 'tb_empresas'), 'tf.id_empresa = te.id', 'nome_razao')
               ->joinLeft(array('tregistro' => 'tb_rh_registro_ponto'), 'tregistro.id_rh_registro_ponto = tdp.id_rh_registro_ponto', 'local')
               ->where('tdp.ativo = ?', App_Model_Dao_Abstract::ATIVO)
               ->order('data desc')
               ->order('hora desc');
	    $this->_searchPaginator($select, $options);

	    return $select;
	}
}
