<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  20/05/2013
 */
class Processo_Model_Dao_Processo extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_tb_processo";
    protected $_primary  = "pro_id";
    protected $_namePairs = 'pro_codigo';

    protected $_rowClass = 'Processo_Model_Vo_Processo';


    protected $_referenceMap    = array(
            'Empresa' => array(
                    'columns'           => 'empresas_id',
                    'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
                    'refColumns'        => 'id'
            ),
            'Status' => array(
                    'columns'           => 'sta_id',
                    'refTableClass'     => 'Processo_Model_Dao_Status',
                    'refColumns'        => 'sta_id'
            ),
            'Usuario' => array(
                    'columns'           => 'sta_id',
                    'refTableClass'     => 'Processo_Model_Dao_Status',
                    'refColumns'        => 'sta_id'
            ),
            'ProcessoPai' => array(
                    'columns'           => 'id_processo_pai',
                    'refTableClass'     => 'Processo_Model_Dao_Processo',
                    'refColumns'        => 'pro_id'
            ),
            'Workspace' => array(
                    'columns'           => 'id_workspace',
                    'refTableClass'     => 'Auth_Model_Dao_Workspace',
                    'refColumns'        => 'id_workspace'
                    )
    );

    protected $_dependentTables = array('Financial_Model_Dao_Financial','Processo_Model_Dao_Arquivo');


    /**
     * @param string $chave o campo que será usado como chave.
     * Caso String vazia ou null, pega a chave primaria definida no atributo $_primary
     * @param string $valor o campo que deve ser retornado no valor
     * Caso String vazia ou null, pega a chave primaria definida no atributo $_namePairs
     * @param string $where
     * @param string $ordem
     * @param string $limit
     * @return Ambigous <multitype:, multitype:mixed >
     */
    public function fetchPairs($chave = null, $valor = null, $where = null, $ordem = null, $limit = null)
    {
        if(empty($chave)){
            if(is_array($this->_primary)){
                $chave = $this->_primary[1];
            }else{
                $chave = $this->_primary;
            }
        }

        if(empty($valor)){
            $valor = "CONCAT_WS(' - ',cast(pro_codigo as CHAR CHARACTER SET utf8), pro_desc_produto )";
        }

        $select = $this->_db
        ->select()
        ->from($this->_name, array($chave, new Zend_Db_Expr($valor) ))
        ->order($ordem ? $ordem : 'pro_codigo DESC');

        if( is_numeric( $limit) ){
            $select->limit( $limit );
        }

        if($where){
            if (is_array($where)){
                foreach ($where as $key => $value){
                    $select->where($key, $value);
                }
            }else{
                $select->where($where);
            }
        }
        return $this->_db->fetchPairs($select);
    }

    public function maxCodigo ($idGrupo){

        $select = $this->_db->select()
            ->from($this->_name,array( 'max' => new Zend_Db_Expr("max(split_part(pro_codigo, '-', 2))")))
            ->where('id_grupo = ?', $idGrupo)
            ->where('EXTRACT(YEAR FROM pro_data_inc) = EXTRACT(YEAR FROM TIMESTAMP ?)', date('Y-m-d'));

        return $this->_db->fetchOne($select);
    }

    protected function _condPaginator(Zend_Db_Select $select)
    {
    }
    public function selectPaginator(array $options = null)
    {

        $selectFinancial = $this->_db->select();
        $selectFinancial->from(array('tf' => 'fin_tb_agrupador_financeiro'), new Zend_Db_Expr('sum(fin_valor)'))
        ->where('p.pro_id  = tf.pro_id')
        ->where('tf.tmv_id  = 2'/*a receber*/)
        ->where('tf.ativo  = ?', App_Model_Dao_Abstract::ATIVO);

        $select = $this->_db->select()->from(array('p'=>$this->_name),array(
                'total_financeiro' => new Zend_Db_Expr("(".$selectFinancial.")"),
                'pro_id','pro_codigo', 'pro_desc_produto', 'pro_quantidade', 'pro_vlr_unt', 'pro_vlr_pedido',
                'cliente' => 'emp.nome', 'pro_data_inc'
        ))
        ->joinLeft(array('emp' => 'tb_pessoa'), 'p.empresas_id = emp.id', array("nome"))
//        ->joinLeft(array('emp' => 'tb_empresas'), 'p.empresas_id = emp.id', array("nome_razao"))

        ->joinInner(array('ts' => 'pro_tb_status'), 'p.sta_id = ts.sta_id', array('sta_descricao'));

//        if(isset($options['empresas_id']) && !empty($options['empresas_id'])){
//            $select->where('p.empresas_id = ?', $options['empresas_id']);
//        }


        if(isset($options['empresas_id'])){

            if (count($options['empresas_id']) > 0){

                $select->where('emp.id IN (?)', $options['empresas_id'] );
            }
        }


        if(isset($options['statusList'])){
            $select->where('p.sta_id in(?)', $options['statusList']);
        }


        $identity = Zend_Auth::getInstance()->getIdentity();

        $select->where("p.id_grupo = ?", $identity->time['id']);

//    	$workspaceSession = new Zend_Session_Namespace('workspace');
//    	$workspaceBo = new Auth_Model_Bo_Workspace();
//
//    	$workspaceObj = $workspaceBo->get($workspaceSession->id_workspace);
//
//    	if ($workspaceObj->free_access != true){
//    	    $select->where("p.id_workspace = ?",$workspaceObj->id_workspace);
//
//    	}else{
//    	    $select->joinLeft(array('tw' => 'tb_workspace'), 'tw.id_workspace = p.id_workspace', array('name_workspace' =>'nome'));
//    	}

        $select->order("p.pro_data_inc DESC");

        $this->_searchPaginator($select, $options);
        $this->_condPaginator($select);

        return $select;
    }

    public function getProcessoByStatus($idStatus)
    {

        $selectFinancial = $this->_db->select();
        $selectFinancial->from(array('tf' => 'fin_tb_financeiro'), new Zend_Db_Expr('sum(tf.fin_valor)'))
        ->joinInner(array('agf' => 'fin_tb_agrupador_financeiro'), 'tf.id_agrupador_financeiro = agf.id_agrupador_financeiro', null)
        ->where('agf.pro_id  = p.pro_id')
        ->where('agf.ativo = ?', App_Model_Dao_Abstract::ATIVO)
        ->where('tf.ativo = ?', App_Model_Dao_Abstract::ATIVO);

        $select = $this->_db->select()->from(array('p'=>$this->_name),array(
                'total_financeiro' => new Zend_Db_Expr("(".$selectFinancial.")"),
                'pro_id','pro_codigo', 'pro_desc_produto', 'pro_quantidade', 'pro_vlr_unt', 'pro_vlr_pedido',
            'cliente' => 'emp.nome',
        ))->order("pro_codigo DESC")
            ->joinLeft(array('emp' => 'tb_pessoa'), 'p.empresas_id = emp.id', array("nome"))
//        ->joinLeft(array('emp' => 'tb_empresas'), 'p.empresas_id = emp.id', array("nome"))
        ->joinInner(array('ts' => 'pro_tb_status'), 'p.sta_id = ts.sta_id', array('sta_descricao'))
        ->where('p.sta_id = ?', $idStatus);

//        $workspaceSession = new Zend_Session_Namespace('workspace');
//        $workspaceBo = new Auth_Model_Bo_Workspace();
//        $workspaceObj = $workspaceBo->get($workspaceSession->id_workspace);
//
//        if ($workspaceObj->free_access != true){
//            $select->where("p.id_workspace = ?",$workspaceObj->id_workspace);
//
//        }


        $identity = Zend_Auth::getInstance()->getIdentity();

        $select->where("p.id_grupo = ?", $identity->time['id']);

        return $this->_db->fetchAll($select);
    }

    public function selectPaginatorByPendencia(array $options = null)
    {
        $selectFinancial = $this->_db->select();
        $selectFinancial->from(array('agf' => 'fin_tb_agrupador_financeiro'), new Zend_Db_Expr('coalesce(sum(agf.fin_valor),0) as fin_valor'))
        ->where('agf.pro_id  = p.pro_id')
        ->where('agf.tmv_id  = 2'/*a receber*/)
        ->where('agf.ativo = ?', App_Model_Dao_Abstract::ATIVO);

        $select = $this->_db->select()->from(array('p'=>$this->_name),array(
                'total_financeiro' => new Zend_Db_Expr("(".$selectFinancial.")"),
                'pro_id','pro_codigo', 'pro_desc_produto', 'pro_quantidade', 'pro_vlr_unt', 'pro_vlr_pedido',
            'cliente' => 'emp.nome',
        ))->order("pro_codigo DESC")
            ->joinLeft(array('emp' => 'tb_pessoa'), 'p.empresas_id = emp.id', array("nome"))
        ->joinInner(array('ts' => 'pro_tb_status'), 'p.sta_id = ts.sta_id', array('sta_descricao'))
        ->where("pro_vlr_pedido <> ($selectFinancial)");

        if(isset($options['empresas_id']) && !empty($options['empresas_id'])){
            $select->where('p.empresas_id = ?', $options['empresas_id']);
        }

        if(isset($options['statusList'])){
            $select->where('p.sta_id in(?)', $options['statusList']);
        }

        $identity = Zend_Auth::getInstance()->getIdentity();

        $select->where("p.id_grupo = ?", $identity->time['id']);


        $this->_searchPaginator($select, $options);
        $this->_condPaginator($select);
        return $select;
    }

    public function processoPai($idProcesso)
    {
        $select = $this->_db->select();
        $select->from(array('tp' => $this->_name), array('pro_codigo', 'pro_desc_produto', 'pro_quantidade','pro_vlr_unt', 'pro_vlr_pedido', 'pro_id','id_processo_pai', 'pro_id'))
                ->joinInner(array('ts' => 'tb_status'), 'ts.sta_id = tp.sta_id', 'sta_descricao')
            ->joinLeft(array('emp' => 'tb_pessoa'), 'p.empresas_id = emp.id', array("nome_razao" => "nome"))
                ->where('tp.id_processo_pai = ?', $idProcesso);

        return $this->_db->fetchAll($select);
    }

    public function getRelatorioProcesso($options)
    {
        $select = $this->_db->select()->from(array('p'=>$this->_name),array(
                'pro_id','pro_codigo', 'pro_desc_produto', 'pro_quantidade', 'pro_vlr_unt', 'pro_vlr_pedido',
                'cliente' => 'emp.nome', 'pro_data_inc'
        ))
            ->joinLeft(array('emp' => 'tb_pessoa'), 'p.empresas_id = emp.id', array("nome"))
        ->joinInner(array('ts' => 'pro_tb_status'), 'p.sta_id = ts.sta_id', array('sta_descricao'));

        if(isset($options['empresaList'])){
            $select->where('p.empresas_id in(?)', $options['empresaList']);
        }

        if ($options['de_pro_data_inc']!=""){
            $select->where('date(p.pro_data_inc) >= ?', $options['de_pro_data_inc']);
        }

        if ($options['para_pro_data_inc']!=""){
            $select->where('date(p.pro_data_inc) <= ?', $options['para_pro_data_inc']);
        }

        if(isset($options['statusList'])){
            $select->where('p.sta_id in(?)', $options['statusList']);
        }

        $select->joinLeft(array('tw' => 'tb_grupo'), 'tw.id = p.id_grupo', array('name_workspace' =>'nome'));
        if (isset($options['id_grupo']) && !empty($options['id_grupo'])){
            $select->where("p.id_grupo = ?", $options['id_grupo']);
        }

        $select->order("p.pro_data_inc DESC ");
        return $this->_db->fetchAll($select);
    }

    public function getRelatorioAnalitico($options)
    {
        $select = $this->_db->select();
        $select->from(array('tp' => $this->_name), array('pro_id', 'pro_codigo', 'pro_desc_produto', 'pro_quantidade', 'pro_vlr_unt', 'pro_vlr_pedido', 'pro_data_inc','id_cliente'=> 'empresas_id'))
               ->joinInner(array('taf' => 'fin_tb_agrupador_financeiro'), 'tp.pro_id = taf.pro_id', array('taf.id_agrupador_financeiro'))
               ->joinInner(array('tm' => 'fin_tb_tipo_movimento'), ' taf.tmv_id = tm.tmv_id', array('tipo_transacao'=>'tmv_descricao2', 'tmv_id'))
               ->joinInner(array('tf' => 'fin_tb_financeiro'), 'taf.id_agrupador_financeiro = tf.id_agrupador_financeiro', array('fin_id','valor_tk'=>'fin_valor','fin_vencimento', 'fin_compensacao', 'fin_competencia'))

            ->joinLeft(array('emp' => 'tb_pessoa'), 'tp.empresas_id = emp.id', array("nome"))

//               ->joinLeft(array('emp'=> 'tb_empresas'), 'tp.empresas_id = emp.id', array('cliente' => 'nome_razao', 'cnpj_cpf'))
               ->joinInner(array('ts'=>'pro_tb_status'), 'tp.sta_id = ts.sta_id', array('status' =>'sta_descricao'))
//               ->joinLeft(array('tw' => 'tb_workspace'), 'tw.id_workspace = tp.id_workspace',array('name_workspace' =>'nome'))
        ->joinLeft(array('tw' => 'tb_grupo'), 'tw.id = tp.id_grupo', array('name_workspace' =>'nome'))
               ->joinLeft(array('tc'=>'fin_tb_contas'), 'tf.con_id = tc.con_id', array('con_codnome'))
               ->where('taf.ativo = ?', App_Model_Dao_Abstract::ATIVO)
               ->where('tf.ativo = ?', App_Model_Dao_Abstract::ATIVO)
               ->order('emp.nome');

        if(isset($options['empresaList'])){
            $select->where('tp.empresas_id in(?)', $options['empresaList']);
        }

        if ($options['de_pro_data_inc']!=""){
            $select->where('date(tp.pro_data_inc) >= ?', $options['de_pro_data_inc']);
        }

        if ($options['para_pro_data_inc']!=""){
            $select->where('date(tp.pro_data_inc) <= ?', $options['para_pro_data_inc']);
        }

        if(isset($options['statusList'])){
            $select->where('tp.sta_id in(?)', $options['statusList']);
        }

        if (isset($options['id_grupo']) && !empty($options['id_grupo'])){
            $select->where("tp.id_grupo = ?", $options['id_grupo']);
        }

        if((int)$options['compensado'] === 1){
            $select->where('tf.fin_compensacao is not null')
                    ->where('tf.con_id is not null');
        }elseif ((int)$options['compensado'] === 2){
            $select->where('tf.fin_compensacao is null or tf.con_id is null');
        }

        if(isset($option['tmv_id']) && !empty($options['tmv_id'])){
            $select->where('tm.tmv_id = ?', $options['tmv_id']);
        }

        return $this->_db->fetchAll($select);
    }
}