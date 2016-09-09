<?php
//desabilitando o limite de memória de processamento do servidor
ini_set( "memory_limit", -1 );
//desabilitando o limite do tempo de execução para geração de PDF`s grandes
ini_set( "max_execution_time", 0 );
class Relatorio_ProcessoController extends App_Controller_Action_AbstractCrud
{

    private $statusBo;
    private $processoBo;
    private $empresaBo;
    private $usuario;


        public function init()
        {

            $this->_helper->layout()->setLayout('metronic');
            parent::init();


            $this->statusBo     = new Relatorio_Model_Bo_StatusProcesso();
            $this->processoBo   = new Relatorio_Model_Bo_Processo();
            $this->empresaBo    = new Relatorio_Model_Bo_Empresa();
            $this->usuario = Zend_Auth::getInstance()->getIdentity()->nome_razao;
            $this->view->usuario = $this->usuario;
        }

    public function indexAction()
    {

    }


    public function visualizarAction(){



        if($this->getRequest()->isPost() )
        {

            //autocomplete status
            $statusCheck = $this->getParam('statusCheck');
            $emp = $this->getParam('empresas_id_pai');

                $staCheck = $this->_separaVirgula($statusCheck);

                $sql = $this->processoBo->getProcessoRelatorio($statusCheck, $emp);

                 if ($sql['qtdRegistros']==0){
                     App_Validate_MessageBroker::addErrorMessage("Esta consulta não possui registros");
                     $this->_redirect('relatorio/processo/index');

                 }
                      $params = array();
                      $params['sql'] = $sql['sql'];
                      $params['usuario'] = $this->usuario;
                      $params['total'] = $sql['qtdRegistros'];

                      $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioProcesso.jrxml";
                     $jasper = new App_Util_Jasper($caminhoXml, $params);
                     $jasper->abrir();
        }
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