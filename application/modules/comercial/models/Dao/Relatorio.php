<?php 
/**
 * @author Vinicius S P Leônidas
 * @since 24/03/04
 */
class Comercial_Model_Dao_Relatorio extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_empresas";
    protected $_primary       = "id";
    protected $_namePairs     = "nome_razao";
    protected $_colsSearch = array('nome_razao', 'fantasia', 'cnpj_cpf', 'estadual', 'municipal', 'site', 'telefone1', 'telefone2', 'telefone3', 'email_corporativo', 'observacoes', 'uasg', 'transacao');

    protected $_rowClass = 'Empresa_Model_Vo_Empresa';
    protected $_dependentTables = array('Sis_Model_Dao_Endereco', 'Material_Model_Dao_Nfe', 'Material_Model_Dao_Transportador', 'Material_Model_Dao_Protocolo', 'Service_Model_Dao_Protocolo', 'Financial_Model_Dao_SacadoFinanceiro', 'Financial_Model_Dao_Credito','Auth_Model_Dao_Usuario', 'Financial_Model_Dao_AgrupadorFinanceiro', 'Compra_Model_Dao_Compra','Rh_Model_Dao_Local');

    protected $_referenceMap    = array(
    		'Tipo funcionario' => array(
    				'columns'           => 'id_tp_funcionario',
    				'refTableClass'     => 'Empresa_Model_Dao_TipoFuncionario',
    				'refColumns'        => 'id_tp_funcionario'
    		),
            'Tipo cliente' => array(
                    'columns'           => 'tic_id',
                    'refTableClass'     => 'Empresa_Model_Dao_TipoCliente',
                    'refColumns'        => 'tic_id'
            ),
            'Tipo fornecedor' => array(
                    'columns'           => 'tif_id',
                    'refTableClass'     => 'Empresa_Model_Dao_TipoFornecedor',
                    'refColumns'        => 'tif_id'
            ),
            'Segmento' => array(
                    'columns'           => 'seg_id',
                    'refTableClass'     => 'Sis_Model_Dao_Segmento',
                    'refColumns'        => 'seg_id'
            ),
            'Indicacao' => array(
                    'columns'           => 'ind_id',
                    'refTableClass'     => 'Sis_Model_Dao_Indicacao',
                    'refColumns'        => 'ind_id'
            ),
            'Portal' => array(
                    'columns'           => 'poc_id',
                    'refTableClass'     => 'Empresa_Model_Dao_Portal',
                    'refColumns'        => 'poc_id'
            ),
            'Mail marketing' => array(
                    'columns'           => 'smk_id',
                    'refTableClass'     => 'Empresa_Model_Dao_MailMarketing',
                    'refColumns'        => 'smk_id'
            ),
            'Tipo pessoa' => array(
                    'columns'           => 'tps_id',
                    'refTableClass'     => 'Sis_Model_Dao_TipoPessoa',
                    'refColumns'        => 'tps_id'
            ),
            'Responsavel' => array(
                    'columns'           => 'empresas_id_pai',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )
    );
    
    public function buscarRelatorio($dados){
    	
    	$responsavel = array_search('empresas_id_pai', $dados['coluna']);
    	if (!empty($responsavel)) {
    		;
    	}
    	
    	$select = $this->select();
    	$select->from(array('te' => $this->_name), $dados['coluna']);
    	
    	if (!empty($dados['grupoGeografico'])) {
    		$select->joinLeft(array('ta'=>'ta_grupo_geografico_x_empresas'), 'te.id = ta.id_empresa', null);
    		$select->where('ta.id_grupo_geografico IN ('.$dados['grupoGeografico'].')');
    	}
    	
    	if (!empty($dados['tipoPessoa'])) {
    		$select->where('te.tps_id = ?', $dados['tipoPessoa']);
    	}
    	
    	if ($dados['empresas_id_pai']) {
    		$select->where('te.empresas_id_pai = ?', $dados['empresas_id_pai']);
    	}
    	
    	if (!empty($dados['ordenar']))
    		$select->order($dados['ordenar']);
    	
    	$select->where("te.ativo = ?", App_Model_Dao_Abstract::ATIVO);
    	
    	return $this->_db->fetchAll($select);
    }
    
    public function agruparResponsavel(){
    	
    	$select = $this->select();
    	$select->from(array('te' => $this->_name), array('id_responsavel' => 'te.empresas_id_pai'));
    	$select->joinLeft(array('tep' => $this->_name), 'te.empresas_id_pai = tep.id', array('titulo' => 'tep.nome_razao'));
    	$select->where("te.ativo = ?", App_Model_Dao_Abstract::ATIVO);
    	$select->where('te.empresas_id_pai IS NOT NULL');
    	$select->group('te.empresas_id_pai');
    	$select->order('te.nome_razao');

    	return $this->_db->fetchAll($select);
    }
}