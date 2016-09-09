<?php



class Relatorio_EmpresaController extends App_Controller_Action_AbstractCrud{
    private $usuario;
    private    $estadoBo;
    private    $cidadeBo;
    private    $tipoEndBo;
    private     $tipoSegBo;
    private     $tipoFornBo;
    private     $empresaBo;
    private $arrayCidade;


    public function init(){
        $this->usuario = Zend_Auth::getInstance()->getIdentity()->nome_razao;
        $this->_helper->layout()->setLayout('metronic');
        parent::init();


        $this->estadoBo     = new Sis_Model_Bo_Estado();
        $this->cidadeBo     = new Sis_Model_Bo_Cidade();
        $this->tipoEndBo    = new Sis_Model_Bo_TipoEndereco();
        $this->tipoSegBo    = new Sis_Model_Bo_TipoSegmento();
        $this->tipoFornBo   = new Empresa_Model_Bo_TipoFornecedor();
        $this->empresaBo    = new Empresa_Model_Bo_Empresa();

        $this->view->estado         = array(null => 'Selecione')+$this->estadoBo->getPairs(false);
        $this->view->tipoEnd        = array(null => 'Selecione')+$this->tipoEndBo->getPairs(false);
        $this->view->tipoSeg        = array(null => 'Selecione')+$this->tipoSegBo->getPairs(false);
        $this->view->tipoForn       = array(null => 'Selecione')+$this->tipoFornBo->getPairs(false);
        $this->arrayCidade                          = $this->cidadeBo->getPairs(false);

    }
    public function empresaAction(){
        $this->view->relatirio = 1;
        if($this->getRequest()->isPost() )
        {
            //CAPTURA DOS DADOS DO FORMULÁRIO
            $estado         = $this->getParam('estado');
            $cidade         = $this->getParam('cidade');
            $tipoEnd        = $this->getParam('tipoEnd');
            $tipoEmp        = $this->getParam('transportadora');
            $tipoSeg        = $this->getParam('tipoSeg');
            $tipoForn       = $this->getParam('tipoForn');
            $quantTrans1    = $this->getParam('quantTrans1');
            $quantTrans2    = $this->getParam('quantTrans2');
            $tp_relatirio   = $this->getParam('tp_relatirio');
            $transReal1     = $this->_conversorData($this->getParam('transReal1'));
            $transReal2     = $this->_conversorData($this->getParam('transReal2'));
            $transData1     = $this->_conversorData($this->getParam('transData1'));
            $transData2     = $this->_conversorData($this->getParam('transData2'));

            //VALIDAÇÃO
            $this->view->relatirio = $tp_relatirio;
            if($transReal1 > $transReal2 || $transData1 > $transData2)
            {
                App_Validate_MessageBroker::addErrorMessage("ATENÇÃO: DATA INÍCIAL MAIOR QUE A DATA FINAL!");
            }elseif($quantTrans1 > $quantTrans2)
            {
                App_Validate_MessageBroker::addErrorMessage("ATENÇÃO: QUANTIDADE DE TRANSAÇÃO INICIAL MAIOR QUE QUANTIDADE DE TRANSAÇÃO FINAL!");
            }else{
                $cond = array (1=>$estado,
                        2=>$cidade,
                        3=>$tipoEnd,
                        4=>$tipoEmp,
                        5=>$tipoSeg,
                        6=>$tipoForn,
                        7=>$quantTrans1,
                        8=>$quantTrans2,
                        9=>$transReal1,
                        10=>$transReal2,
                        11=>$tp_relatirio,
                        12=>$transData1,
                        13=>$transData2);
                //STRING SQL
                $res = $this->empresaBo->getEmpresaRelatorio($cond);
                if ($res['numRes'] == '0'){
                    App_Validate_MessageBroker::addErrorMessage("ESTA CONSULTA NÃO POSSUI REGISTROS");
                }else{

                    //FORMATAÇÃO DA EXIBIÇÃO DOS FILTROS UTILIZADOS
                    $params = array ();
                    $params['filtros'] = '';
                    $params['filtros'] .= $this->_filtroSelect($estado,$this->view->estado,'Estado:');
                    $params['filtros'] .= $this->_filtroSelect($cidade,$this->arrayCidade,'Cidade:');
                    $params['filtros'] .= $this->_filtroSelect($tipoEnd,$this->view->tipoEnd,'Tipo de Endereço:');
                    $params['filtros'] .= $this->_filtroSelect($tipoSeg,$this->view->tipoSeg,'Tipo de Seguimento:');
                    $params['filtros'] .= $this->_filtroSelect($tipoForn,$this->view->tipoForn,'Tipo de Fornecedor:');
                    if($cond[4] == 1){
                        $tipoEmp = 'Tipo de Empresa: TRANSPORTADOR    ';
                        $params['filtros'] .=  $tipoEmp;
                    }
                    if(!empty($cond[7]) && !empty($cond[8])){
                        $quantTrans = 'Quantidade de Transações de '.$quantTrans1.' a '.$quantTrans2.'    ';
                        $params['filtros'] .=  $quantTrans;
                    }
                    if(!empty($cond[9]) && !empty($cond[10])){
                        $transReal = 'Transações Realizadas de '.$this->_conversorData($transReal1).' a '. $this->_conversorData($transReal2).'    ';
                        $params['filtros'] .=  $transReal;
                    }
                    if(!empty($cond[12]) && !empty($cond[13])){
                        $transData1 = 'Cadastro realizado entre '.$this->_conversorData($transData1).' e '.$this->_conversorData($transData2).'    ';
                        $params['filtros'] .=  $transData1;
                    }

                    //USUARIO DO SISTEMA
                    $params['usuario'] =$this->usuario;

                    //SQL DO ARQUIVO JRXML
                    $params["sql"] = $res['sql'];

                    $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioEmpresa.jrxml";



                    $jasper = new App_Util_Jasper($caminhoXml, $params);
                    $jasper->abrir();

                }

            }

        }
    }
    private function _conversorData($data)
    {
        if(!empty($data)){
            return implode(preg_match("~\/~", $data) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));
        }
        return null;
    }

    private function _filtroSelect($id,$arraydesc,$string){
        if($id == 'Selecione' || empty($id))
        {
            return '';
        }
        else{
            $filtro = $string.' '.$arraydesc[$id].'    ';
            return $filtro;
        }

    }

}