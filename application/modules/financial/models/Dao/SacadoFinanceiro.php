<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  17/06/2013
 *
 */
class Financial_Model_Dao_SacadoFinanceiro extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_rel_sacado_financeiro";
    protected $_primary       = "tb_financeiro_fin_id";

    protected $_rowClass = 'Financial_Model_Vo_SacadoFinanceiro';

    protected $_referenceMap    = array(
    		'Financial' => array(
    				'columns'           => 'tb_financeiro_fin_id',
    				'refTableClass'     => 'Financial_Model_Dao_Financial',
    				'refColumns'        => 'fin_id'
    		),
        'Empresa' => array(
            'columns'           => 'id_pessoa_empresa',
            'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
            'refColumns'        => 'id'
        ),

    );

    public function getFinanceiroRh($idEmpresa, $data){

    	$inicial = new Zend_Date($data, 'yyyy-MM-dd');
    	$inicial = $inicial->toString('yyyy-MM-dd');
    	$final = new Zend_Date($data, 'yyyy-MM-dd');
    	$final->addMonth(1);
    	$final->sub('1', Zend_Date::DAY);
    	$final = $final->toString('yyyy-MM-dd');
    	$workspaceSession = new Zend_Session_Namespace('workspace');

    	$select = $this->_db->select();
    	$select->from(array('rsf' => $this->_name))
    	->where('rsf.empresas_id = ?', $idEmpresa)
    	->where('rsf.tb_financeiro_fin_id not in(SELECT fin_id FROM rel_rh_financeiro)')
    	->joinInner(array('tf' => 'tb_financeiro'), 'tf.fin_id = rsf.tb_financeiro_fin_id')
    	->joinInner(array('taf' => 'tb_agrupador_financeiro'), 'taf.id_agrupador_financeiro = tf.id_agrupador_financeiro')
    	->where('tf.ativo = ?', App_Model_Dao_Abstract::ATIVO)
    	->where("taf.id_workspace = ?", $workspaceSession->id_workspace)
    	->where("tf.fin_emissao BETWEEN '{$inicial}' AND '{$final}'");
    	return $this->_db->fetchAll($select);
    }

}

