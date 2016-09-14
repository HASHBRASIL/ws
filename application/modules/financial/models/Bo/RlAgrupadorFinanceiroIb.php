<?php

    class Financial_Model_Bo_RlAgrupadorFinanceiroIb extends App_Model_Bo_Abstract
    {
        /**
         * @var Financial_Model_Dao_RlAgrupadorFinanceiroIb
         */
        protected $_dao;

        public $fields = array(
//        'con_id' => 'Código',
//            'con_codnome' => 'Codinome',
            'ds_transacao_conta' => 'Descrição',
            'dt_transacao_conta' => 'Data',
//            'con_digito' => 'Dígito da Conta',
            'tp_transacao_conta' => 'Tipo',
            'st_transacao_conta' => 'Situação',
            'vl_transacao_conta' => 'Valor',
            'vl_transacao_saldo' => 'A consolidar',
//            'nome' => 'Time'
        );

        public $fieldsFilter = array(
            'ds_transacao_conta' => '',
            'vl_transacao_conta' => 'number_format',
            'vl_transacao_saldo' => 'number_format',
            'dt_transacao_conta' => 'date',
            'tp_transacao_conta' => array('2' => 'Receita', '1' => 'Débito'),
            'st_transacao_conta' => array('' => 'Pendente', '1' => 'Quitado', '2' => 'Não sei')
        );

        /**
         * @var integer
         */
        public function __construct()
        {
            $this->_grupoVinculo = true;
            $this->_getRegistersWithoutWorkspace = true;
            $this->_dao = new Financial_Model_Dao_RlAgrupadorFinanceiroIb();
            parent::__construct();
        }

        public function adicionarVinculo($retorno, $options)
        {
            $data['id_itembiblioteca'] = $retorno['ib'];

            $data['id_agrupador_financeiro'] =
                (isset($options['id_agrupador_financeiro']) && $options['id_agrupador_financeiro'])
                ? $options['id_agrupador_financeiro']
                : null;

            $this->_dao->insert($data);
        }


    }