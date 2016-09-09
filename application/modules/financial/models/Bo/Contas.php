<?php

    /**
     * @author Carlos Vinicius Bonfim da Silva
     * @since  14/06/2013
     */
    class Financial_Model_Bo_Contas extends App_Model_Bo_Abstract
    {
        /**
         * @var Financial_Model_Dao_Contas
         */
        protected $_dao;

        protected $_metanomeImportacao;

        const APAGAR = 1;
        const ARECEBER = 2;

        public $fields = array(
//        'con_id' => 'Código',
            'con_codnome' => 'Codinome',
            'con_agencia' => 'Agência',
            //'con_age_digito' => 'Dígito da Agência',
            'con_numero' => 'Número da Conta',
            //'con_digito' => 'Dígito da Conta',
            'bco_nome' => 'Banco',
            'tcb_descricao' => 'Tipo Conta',
            //'nome' => 'Time'
        );

        /**
         * @var integer
         */
        public function __construct()
        {
            $this->_metanomeImportacao = 'OFX';
            $this->_grupoVinculo = true;
            $this->_getRegistersWithoutWorkspace = true;
            $this->_dao = new Financial_Model_Dao_Contas();
            parent::__construct();
        }

        protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
        {

            if (empty($object->con_agencia)) {
                throw new App_Validate_Exception($this->_translate->translate('O campo de agência está vazio.'));
            }
            if (empty($object->con_numero)) {
                throw new App_Validate_Exception($this->_translate->translate('O campo de número da conta está vazio.'));
            }

            if (empty($object->con_digito) && $object->con_digito != '0') {
                throw new App_Validate_Exception($this->_translate->translate('O campo de dígito da conta está vazio.'));
            }
            if (empty($object->bco_id)) {
                throw new App_Validate_Exception($this->_translate->translate('Selecione um banco.'));
            }

            return true;
        }

        public function getContasPerWorkspace($date = null, $type = null)
        {

            $contasList = $this->_dao->getContasPerWorkspace($date, $type);

            foreach ($contasList as $key => $conta) {


                if ($conta->total_financeiro == null) {

                    unset($contasList[$key]);
                    continue;
                }

                $conta->total_financeiro = number_format($conta->total_financeiro, 2, ',', '.');
            }
            return $contasList;
        }

        public function getListContaWithFinanceiroAndWorkspacePerTicket($conId = null, $workspace = null)
        {

            return $this->_dao->getListContaWithFinanceiroAndWorkspacePerTicket($conId, $workspace);
        }

        public function findByAccount($conta)
        {
            return $this->_dao->findByAccount($conta);
        }


        public function uploadContas(Zend_File_Transfer_Adapter_Http $upload, $fileContent, $fileInfo)
        {
            $upload->addValidator('Extension', false, array('ofx'));

            if (!$upload->isValid()) {
                throw new Exception($this->_translate->translate('Arquivo Inválido'));
            }

            $OfxParser = new \OfxParser\Parser;
            $ofx = $OfxParser->loadFromString($fileContent);

            $conta = $ofx->BankAccount;

            $rowConta = $this->findByAccount($conta);

            if (!$rowConta) {
                // @todo 2 regras - adiciona conta ou da exception!
                throw new Exception($this->_translate->translate('Conta não encontrada no time!'));
            }

            $boTransacao = new Financial_Model_Bo_TransacaoConta();
            // salva cada 1 das transações
            $contadorImportacao = $boTransacao->processaTransacoesOfx($rowConta->con_id, $conta->Statement->transactions);


            $identity   = Zend_Auth::getInstance()->getIdentity();
            parent::upload($fileInfo, $identity->time['id'], $identity->grupo['id']);

            return $contadorImportacao;

        }

    }
