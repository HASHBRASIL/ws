<?php

    class Financial_Model_Bo_TransacaoConta extends App_Model_Bo_Abstract
    {
        /**
         * @var Financial_Model_Dao_TransacaoConta
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
            $this->_dao = new Financial_Model_Dao_TransacaoConta();
            parent::__construct();
        }



        protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
        {
            if ($object->vl_transacao_conta){
                $object->vl_transacao_conta = $this->_formatDecimal($object->vl_transacao_conta);
            }
        }

        public function findByUniqueId($uniqueId)
        {
            return $this->_dao->findByUniqueId($uniqueId);
        }

        public function processaTransacoesOfx($conId, $transacoes)
        {
            $i = 0;
            $identity   = Zend_Auth::getInstance()->getIdentity();


            foreach ($transacoes as $transacao) {
                if ($this->findByUniqueId($transacao->uniqueId)) {
                } else {
                    $i++;
                    $data = array();
                    $data['ds_transacao_conta'] = $transacao->memo;
                    $data['dt_transacao_conta'] = $transacao->date->format('Y-m-d h:i:s');
                    $data['tp_transacao_conta_extra'] = $transacao->type;
                    $data['vl_transacao_conta'] = $transacao->amount;
                    $data['ds_idunico_transacao_conta'] = $transacao->uniqueId;
                    $data['con_id'] = $conId;
                    $data['tp_transacao_conta'] = $transacao->amount > 0 ? 2 : 1;
                    $data['id_criacao_usuario'] = $identity->id;
                    $data['dt_criacao'] = new Zend_Db_Expr("now()");
                    $data['id_grupo'] = $identity->time['id'];

                    $this->_dao->insert($data);
                }
            }

            return $i;
        }

    }