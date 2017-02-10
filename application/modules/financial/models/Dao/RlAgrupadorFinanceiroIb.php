<?php

class Financial_Model_Dao_RlAgrupadorFinanceiroIb extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_rl_agrupador_financeiro_ib";
    protected $_primary       = array("id_itembiblioteca", "agrupador_financeiro_id");
    protected $_sequence = false;


    public function selectPaginator(array $options = null)
    {
        $select = $this->_db->select()->from($this->_name, array(
            'id' => 'transacao_conta_id',
            'ds_transacao_conta',
            'dt_transacao_conta',
            'tp_transacao_conta',
            'vl_transacao_conta',
            'vl_transacao_saldo',
            'st_transacao_conta',
            'ds_idunico_transacao_conta',
            'con_id',
//            'tp_transacao_conta',
            'id_criacao_usuario',
            'dt_criacao',
            'id_grupo'
        ));

        $select->join(array('c' => 'fin_tb_contas'), 'c.con_id = fin_tb_transacao_conta.con_id', array('con_codnome'));
        $select->join(array('b' => 'fin_tb_bancos'), 'c.bco_id = b.bco_id', array('bco_id', 'bco_nome'));
        $select->join(array('tpcb' => 'fin_tb_tipo_contabanco'), 'c.tcb_id = tpcb.tcb_id',
            array('tcb_id', 'tcb_descricao'));
        $select->join(array('g' => 'tb_grupo'), 'g.id = c.id_grupo', array('nome'));

        $identity = Zend_Auth::getInstance()->getIdentity();

        // @todo ver com o fernando qual a regra para mostrar as contas (times filhos e outros configurações)
        $select->where("c.id_grupo = ?", $identity->time['id']);

        if ($options['con_id']) {
            $select->where('c.con_id = ?', $options['con_id']);
        } else if ($options['transacao_conta_id']) {
            $select->where('fin_tb_transacao_conta.transacao_conta_id = ?', $options['transacao_conta_id']);
        }

        return $select;
    }


}

