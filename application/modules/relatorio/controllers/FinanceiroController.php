<?php
//desabilitando o limite de memória de processamento do servidor
ini_set( "memory_limit", -1 );
//desabilitando o limite do tempo de execução para geração de PDF`s grandes
ini_set( "max_execution_time", 0 );
class Relatorio_FinanceiroController extends App_Controller_Action_AbstractCrud
{
    private $statusBo;
    private $financeiroBo;
    private $usuario;

    public function init(){
        $this->_helper->layout()->setLayout("metronic");
        $this->statusBo                 = new Relatorio_Model_Bo_StatusFinanceiro();
        $this->financeiroBo             = new Relatorio_Model_Bo_Financeiro();
        $this->usuario = Zend_Auth::getInstance()->getIdentity()->nome_razao;
        parent::init();
    }

    public function indexAction()
    {

        $this->view->statusCheck  = $this->statusBo->getPairs(false);
        $this->view->linha        = round(count($this->statusBo->getPairs(false)) / 2);

    }
    public function statusAction()
    {

        $this->view->statusCheck  = $this->statusBo->getPairs(false);
        $this->view->linha        = round(count($this->statusBo->getPairs(false)) / 2);

        if($this->getRequest()->isPost() )
        {
            //CAPTURA DOS DADOS DO FORMULÁRIO
            $cond = $this->_capturaForm(0);

            //STRING SQL
            $res = $this->financeiroBo->getFinanceiroRelatorio($cond);

            //VALIDAÇÃO
            if (isset( $res['consulta']->fin_id) || $res['numRes'] == '0' || $res['numRes'] == 0 ){
                App_Validate_MessageBroker::addErrorMessage("ESTA CONSULTA NÃO POSSUI REGISTROS");
                $this->_redirect("relatorio/financeiro/index");
            }else{
                $params = array ();
                $params['usuario'] =$this->usuario;
                $params["sql"] = $res['sql'];
                $params["total"] = $res['numRes'];
                $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioFinanceiro.jrxml";
                $jasper = new App_Util_Jasper($caminhoXml, $params);
                $jasper->abrir();

            }
        }
    }

    public function empresaAction()
    {


        $this->view->statusCheck  = $this->statusBo->getPairs(false);
        $this->view->linha        = round(count($this->statusBo->getPairs(false)) / 2);

        if($this->getRequest()->isPost() )
        {
            //CAPTURA DOS DADOS DO FORMULÁRIO
           $cond = $this->_capturaForm(1);

            //STRING SQL
            $res = $this->financeiroBo->getFinanceiroRelatorio($cond);

            //VALIDAÇÃO
            if (isset( $res['consulta']->fin_id) || $res['numRes'] == '0' || $res['numRes'] == 0 ){
                App_Validate_MessageBroker::addErrorMessage("ESTA CONSULTA NÃO POSSUI REGISTROS");
                $this->_redirect("relatorio/financeiro/index");
            } else{
                $params = array ();
                $params['usuario'] = $this->usuario;;
                $params["sql"] = $res['sql'];
                $params["total"] = $res['numRes'];
                $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioFinanceiroEmpresa.jrxml";
                $jasper = new App_Util_Jasper($caminhoXml, $params);
                $jasper->abrir();

            }
        }
    }
    public function plcAction()
    {


        $this->view->statusCheck  = $this->statusBo->getPairs(false);
        $this->view->linha        = round(count($this->statusBo->getPairs(false)) / 2);

        if($this->getRequest()->isPost() )
        {
            //CAPTURA DOS DADOS DO FORMULÁRIO
            $cond = $this->_capturaForm(2);

            //STRING SQL
            $res = $this->financeiroBo->getFinanceiroRelatorio($cond);

            //VALIDAÇÃO
            if (isset( $res['consulta']->fin_id) || $res['numRes'] == '0' || $res['numRes'] == 0 ){
                App_Validate_MessageBroker::addErrorMessage("ESTA CONSULTA NÃO POSSUI REGISTROS");
                $this->_redirect("relatorio/financeiro/index");
            }else{
                $params = array ();
                $params['usuario'] = $this->usuario;
                $params["sql"] = $res['sql'];
                $params["total"] = $res['numRes'];
                $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioFinanceiroPlc.jrxml";
                $jasper = new App_Util_Jasper($caminhoXml, $params);
                $jasper->abrir();

            }
        }
    }


    public function _capturaForm($agrupamento){
        //CAPTURA DOS DADOS DO FORMULÁRIO
        $empresas = $this->getParam("empresaList");
        $emp = $this->_separaVirgula($empresas);

        $planocontas = $this->getParam("plcList");
        $plc = $this->_separaVirgula($planocontas);

        $dataEmissao1 = $this->_conversorData($this->getParam('data_emissao'));
        $dataEmissao2 = $this->_conversorData($this->getParam('data_emissao2'));
        $dataEmissaoType = $this->getParam('data_emissaoType');

        $dataVencimento1 = $this->_conversorData($this->getParam('data_vencimento'));
        $dataVencimento2 = $this->_conversorData($this->getParam('data_vencimento2'));
        $dataVencimentoType = $this->getParam('data_vencimentoType');

        $dataCompensacao1 = $this->_conversorData($this->getParam('data_compensacao'));
        $dataCompensacao2 =$this->_conversorData($this->getParam('data_compensacao2'));
        $dataCompensacaoType = $this->getParam('data_compensacaoType');

        $statusCheck = $this->getParam('statusCheck');
        $staCheck = $this->_separaVirgula($statusCheck);

        $cond = array (1=>$staCheck,
                2=>$dataEmissao1,
                3=>$dataEmissao2,
                4=>$dataEmissaoType,
                5=>$dataVencimento1,
                6=>$dataVencimento2,
                7=>$dataVencimentoType,
                8=>$dataCompensacao1,
                9=>$dataCompensacao2,
                10=>$dataCompensacaoType,
                11=>$emp,
                12=>$plc,
                13=>$agrupamento
        );
        return $cond;
    }



    private function _conversorData($data)
    {
        if(!empty($data)){
            return implode(preg_match("~\/~", $data) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));
        }
        return null;
    }

    private function _separaVirgula($array){
        $dt = '';
        $virgula = false;
        if($array)
        {
            foreach ($array as $d){
                if($virgula)
                    $dt .= ','.$d;
                else{
                    $dt .= $d;
                    $virgula = true;
                }
            }
            return $dt;
        }
        return '';
    }

    private function _filtrosRepetitivos($arrayId,$arrayDesc,$string){
        $filtros = '';
        if($arrayId <> '' && !empty($arrayId)){
            foreach ($arrayId as $id)
            {
                $s = $arrayDesc[$id];
                $s = $string.' '.$s.'    ';
                $filtros .=  $s;
            }
        }
        return $filtros;
    }
}