<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  29/09/2013
 */
class Financial_Model_Dao_AgrupadorFinanceiro extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_agrupador_financeiro";
    protected $_primary       = "id_agrupador_financeiro";
    protected $_rowClass	  = "Financial_Model_Vo_AgrupadorFinanceiro";
    protected $_namePairs	  = 'id_agrupador_financeiro';


    public $searchFields = array(
        'id_agrupador_financeiro'     => "agpf.id_agrupador_financeiro",
        'fin_descricao'   	=> 'agpf.fin_descricao',
        'tmv_descricao'   => 'tmv_descricao',
        'nome_razao'      		=> 'emp.nome',
        'fin_valor'      	=> 'agpf.fin_valor',
        'nome'      	=> 'wk.nome'
    );


    const DEBITO = 1;
    const CREDITO = 2;

    protected $_referenceMap    = array(

    		'PlanoContas' => array(
    				'columns'           => 'plc_id',
    				'refTableClass'     => 'Financial_Model_Dao_PlanoContas',
    				'refColumns'        => 'plc_id'
    		),
            'Moeda' => array(
                				'columns'           => 'moe_id',
                				'refTableClass'     => 'Financial_Model_Dao_Moeda',
                				'refColumns'        => 'moe_id'
            ),
            'CentroCusto' => array(
                    'columns'           => 'cec_id',
                    'refTableClass'     => 'Financial_Model_Dao_CentroCusto',
                    'refColumns'        => 'cec_id'
            ),
            'TipoMovimento' => array(
                    'columns'           => 'tmv_id',
                    'refTableClass'     => 'Financial_Model_Dao_TipoMovimento',
                    'refColumns'        => 'tmv_id'
            ),
    		'EmpresaCliente' => array(
    				'columns'           => 'id_pessoa_cliente',
    				'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
    				'refColumns'        => 'id'
            ),'Grupo' => array(
            'columns'           => 'id_grupo',
            'refTableClass'     => 'Legacy_Model_Dao_Grupo',
            'refColumns'        => 'id'
            ),
    		'Processo' => array(
    				'columns'           => 'pro_id',
    				'refTableClass'     => 'Processo_Model_Dao_Processo',
    				'refColumns'        => 'pro_id'
    		),
            'Usuario' => array(
                    'columns'           => 'id_criacao_usuario',
                    'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
                    'refColumns'        => 'id'
            ),
            'Correlato' => array(
                    'columns'           => 'id_agrupador_financeiro_correlato',
                    'refTableClass'     => 'Financial_Model_Dao_AgrupadorFinanceiro',
                    'refColumns'        => 'id_agrupador_financeiro'
            )
    );

    protected $_dependentTables = array('Financial_Model_Dao_Financial');

    public function selectPaginator(array $options = null)
    {
        $select = $this->_db->select();
        $select->from(array('agpf'  => $this->_name), array("agpf.id_agrupador_financeiro",
                 "agpf.fin_descricao",
                 "agpf.fin_observacao",
                 "agpf.fin_nota_fiscal",
                 "agpf.fin_valor",
                 "agpf.id_agrupador_financeiro_correlato",
                 "tmv.tmv_descricao",
                 "emp.tipopessoa",
                 "emp.usuariotime"
        //            'agpf.id_pessoa_cliente'
            ))
            ->joinLeft(array('tmv' => 'fin_tb_tipo_movimento'), ' tmv.tmv_id= agpf.tmv_id', array())
//            ->joinLeft(array('emp' => 'tb_pessoa'), 'agpf.id_pessoa_cliente = emp.id', array("nome_razao" => "agpf.id_pessoa_cliente"))

            ->joinLeft(array('emp' => 'tb_pessoa'), 'agpf.id_pessoa_cliente = emp.id', array("nome_razao" => new Zend_Db_Expr("COALESCE(emp.nome, emp.nome2)")))

            ->joinLeft(array('agpf2'  => $this->_name), 'agpf.id_agrupador_financeiro_correlato = agpf2.id_agrupador_financeiro', array(""))
            ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agpf.id_grupo', array("nome"));

        $identity = Zend_Auth::getInstance()->getIdentity();
        $select->where("agpf.id_grupo = ?", $identity->time['id']);

        $select->where('agpf.ativo = ?', App_Model_Dao_Abstract::ATIVO);

        if ($options['id_agrupador_list']) {
            $select->where("agpf.id_agrupador_financeiro_correlato = ?", $options['id_agrupador_list']);
        } else {
            // @todo regra para nao listar coisas desnecessárias
            $select->where("(agpf2.id_grupo <> ?", $identity->time['id']);
            $select->orWhere("agpf.id_agrupador_financeiro_correlato is null)");
        }

        if ($options['transacao_conta_id']) {
            $select->where('agpf.transacao_conta_id = ?', $options['transacao_conta_id']);
        }

    	return $select;
    }

    protected function _condPaginator(Zend_Db_Select $select)
    {
    }

    public function gridFinancialProcessoAjax($id){

    	$select = $this->_db->select();
    	$select->from(array('agpf'  => $this->_name))
    	->where('agpf.pro_id = ?', $id)
    	->where('agpf.ativo = ?', App_Model_Dao_Abstract::ATIVO);
    	return $this->_db->fetchAll($select);

    }

    public function inativar($id)
    {
    	$row = $this->find($id)->current();

    	if(empty($row)){
    		App_Validate_MessageBroker::addErrorMessage('Este dado não pode ser excluído.');
    		return false;
    	}
    	if(isset($row->ativo)){
    		$row->ativo = self::INATIVO;
    		if($row->getCorrelato()){
    			$correlato = $row->getCorrelato();
    			$correlato->ativo = self::INATIVO;
    			$correlato->save();
    		}
    		$row->save();
    		return true;
    	} else {
    		App_Validate_MessageBroker::addErrorMessage('Este dado nao pode ser inativado.');
    	}
    }


    public function getNota($idMaster)
    {


    $query = <<<QUERY


WITH RECURSIVE ib_temp AS (
	(

	SELECT ib.*, lower(tib.metanome) metanome FROM tb_itembiblioteca ib join tp_itembiblioteca tib on (ib.id_tib = tib.id)

WHERE ib."id" = ?

)
UNION
	(
		select ib.*, lower(tib.metanome) metanome
		from tb_itembiblioteca ib join ib_temp ibt on (ib.id_ib_pai = ibt.id)
		join tp_itembiblioteca tib on (ib.id_tib = tib.id)
	)
)
SELECT * from ib_temp where metanome in ('vnf', 'cnpjdest', 'cnpjemit', 'demiide', 'idinfnfe', 'natopide', 'nnfide', 'serieide', 'vississqntot', 'vpisissqntot', 'vcofinsissqntot', 'infadfisco', 'vnficmstot')
QUERY;


        $db = Zend_Db_Table::getDefaultAdapter();

        $stmt = $db->query($query, array($idMaster));
        $result = $stmt->fetchAll();

        return $result;

    }



    public function saveSplit($request, $idGrupo = null)
    {


        $rowCorrelato = $this->get($request['id']);
        $identity = Zend_Auth::getInstance()->getIdentity();

        if(substr_count($request['fin_valor'], ",")){
            $request['fin_valor'] = str_replace(".", "", $request['fin_valor']);
            $request['fin_valor'] = str_replace(",", ".", $request['fin_valor']);
        }

        if ($idGrupo && ($idGrupo != $rowCorrelato->id_grupo)) {

            $row = $this->createRow();

            $row->fin_descricao  =  $request['fin_descricao'] ? $request['fin_descricao'] : $rowCorrelato['fin_descricao'];
            $row->fin_valor      = $request['fin_valor'];
            $row->tmv_id  = $rowCorrelato['tmv_id'] == self::DEBITO ? self::DEBITO : self::CREDITO;
            $row->moe_id = $rowCorrelato['moe_id'];

            $row->cec_id = $request['cec_id'] ? $request['cec_id'] : $rowCorrelato['cec_id'];
            $row->plc_id = $request['plc_id'] ? $request['plc_id'] : $rowCorrelato['plc_id'];

            $row->id_grupo = $idGrupo;
            $row->id_criacao_usuario = $identity->id;
            $row->id_agrupador_financeiro_correlato = $request['id'];
            $row->save();

            $row2 = $this->createRow();
            $row2->fin_descricao  =  $request['fin_descricao'] ? $request['fin_descricao'] : $rowCorrelato['fin_descricao'];
            $row2->fin_valor      = $request['fin_valor'];
            $row2->tmv_id  = $rowCorrelato['tmv_id'] == self::DEBITO ? self::CREDITO : self::DEBITO;
            $row2->moe_id = $rowCorrelato->moe_id;
            $row2->id_grupo = $rowCorrelato->id_grupo;

            $row->cec_id = $request['cec_id'] ? $request['cec_id'] : $rowCorrelato['cec_id'];
            $row->plc_id = $request['plc_id'] ? $request['plc_id'] : $rowCorrelato['plc_id'];

            $row2->id_criacao_usuario = $identity->id;
            $row2->id_agrupador_financeiro_correlato = $request['id'];
            $row2->save();

        } else {

            $row = $this->fetchRow(array('id_agrupador_financeiro_correlato = ?' => $rowCorrelato->id_agrupador_financeiro), 'dt_criacao asc');

            if (!$row) {
                $row = $this->createRow();
                $row->setFromArray($rowCorrelato->toArray());
                $row->id_agrupador_financeiro = null;
                $row->id_agrupador_financeiro_correlato = $request['id'];

                $row->fin_valor = $request['keepvalue']
                                ? $rowCorrelato->fin_valor - $request['fin_valor']
                                : $rowCorrelato->fin_valor;

            } else {
                $row->fin_valor = $request['keepvalue']
                                ? $row->fin_valor - $request['fin_valor']
                                : $row->fin_valor;
            }

            $row->save();

            $row2 = $this->createRow();
            $row2->fin_descricao  =  $request['fin_descricao'] ? $request['fin_descricao'] : $rowCorrelato['fin_descricao'];
            $row2->fin_valor      = $request['fin_valor'];
            $row2->tmv_id  = $rowCorrelato['tmv_id'];
            $row2->moe_id = $rowCorrelato->moe_id;
            $row2->id_grupo = $rowCorrelato->id_grupo;

            $row2->cec_id = $request['cec_id'] ? $request['cec_id'] : $rowCorrelato['cec_id'];
            $row2->plc_id = $request['plc_id'] ? $request['plc_id'] : $rowCorrelato['plc_id'];

            $row2->id_criacao_usuario = $identity->id;
            $row2->id_agrupador_financeiro_correlato = $request['id'];
            $row2->save();

        }

        if ($request['keepvalue'] != 1) {
            $rowCorrelato->fin_valor += $request['fin_valor'];
            $rowCorrelato->save();
        }



    }

    public function salvarTransacao($data, $tipo)
    {
//        $arrayConversao = array('vprod' => 'fin_valor', 'cnpjdest', 'cnpjemit', 'demiide' => 'dt_financeiro', 'idinfnfe', 'natopide', 'nnfide', 'serieide', 'vississqntot', 'vpisissqntot', 'vcofinsissqntot');

        $identity = Zend_Auth::getInstance()->getIdentity();

        // @todo ver com o fernando qual a regra para mostrar as contas (times filhos e outros configurações)

        $row = $this->createRow();

        $row->dt_financeiro  = $data['demiide'];
        $row->fin_descricao  = $data['natopide'] .  ' - '  . $data['nnfide'] . ' - ' . $data['serieide'];
        $row->fin_observacao = $data['infadfisco'];
        $row->fin_valor      = $data['vnficmstot'];

        $row->id_pessoa_cliente  = $tipo == 2 ? $data['cnpjdest'] :  $data['cnpjemit'];
        $row->id_pessoa_faturado = $tipo == 1 ? $data['cnpjdest'] :  $data['cnpjemit'];
        $row->tmv_id = $tipo;
        $row->moe_id = 1;
        $row->id_grupo = $identity->time['id'];
        $row->id_criacao_usuario = $identity->id;

        $row->save();

//        vississqntot
//        vpisissqntot
//        vcofinsissqntot
    }


    public function getTransacaoAberta()
    {

        $select = $this->select();
        $select->from(array('agpf'  => $this->_name), array("id_agrupador_financeiro",
            "fin_descricao",
            "fin_observacao",
            "fin_nota_fiscal",
            "fin_valor",
            "id_agrupador_financeiro_correlato"
        ));

        $identity = Zend_Auth::getInstance()->getIdentity();

        $select->where("agpf.id_grupo = ?", $identity->time['id'])
        ->where('agpf.transacao_conta_id is null');

        $select->where('agpf.ativo = ?', App_Model_Dao_Abstract::ATIVO);

        $rowset = $this->fetchAll($select);

        $data = array();
        foreach ($rowset as $row) {
            $data[$row['id_agrupador_financeiro']] = $row['fin_descricao'] . ' - R$ ' . number_format($row['fin_valor'], 2,',','.');
        }

        return $data;
    }

}