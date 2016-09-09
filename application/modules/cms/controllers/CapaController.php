<?php

    // CONTROLLER PARA TRABALHAR O MODULO CAPA DOS CMS's

class Cms_CapaController extends App_Controller_Action_Twig
{
    /**
     * @var Content_Model_Bo_TpItemBiblioteca
     */
    protected $_bo;

    public function init()
    {
        parent::init();
        $this->_bo = new Content_Model_Bo_TpItemBiblioteca();
        //$this->_id = $this->getParam("id");
    }

    function indexAction () {

        // JEITO TOSCO DE CHAMAR A CLASSE, O SOL VAI CONSERTAR ISSO AQUI
        //include('../vendor/abeautifulsite/simpleimage/src/abeautifulsite/SimpleImage.php');

        $teste = new abeautifulsite\SimpleImage('teste.jpg');
        die();

        // TA COMENTADO MAS NÃO É PRA RANCAR PORQUE VAI VOLTAR PORQUE ISSO É MOH LEGAL
        $this->view->headLink()->appendStylesheet( $this->view->baseUrl(). '/css/componentes/capa/style.css');
        $this->view->headLink()->appendStylesheet( $this->view->baseUrl() . '/library/cropimg/css/cropper.min.css');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/library/cropimg/js/cropper.min.js');

        foreach($this->servico['filhos'] as $filho) {
            $botoes[$filho['metanome']] = $this->identity->servicos[$filho['id']];
        }

        $bo        = new Content_Model_Bo_ItemBiblioteca();
        $botp      = new Content_Model_Bo_TpItemBiblioteca();
        $data      = $bo->getConteudo($this->identity->time['id']);
        $data_tipo = $botp->getTipoConteudo($this->identity->time['id']);

        foreach($data as $key => $value){
            $data[$key]['conteudo'] = json_decode($value['conteudo']);
        }        

        $tipo_conteudo = array();
        foreach( $data_tipo as $key => $value) {
            array_push($tipo_conteudo, array( 'text' => $value['nome'], 'id' => $value['id'] ) );
        }

        $this->view->data = array('botoes' => $botoes, 'itens' => $data, 'tipo_conteudo' => json_encode($tipo_conteudo) );
    }

    function pinAction () {

        $post = $this->getAllParams();
        $botp = new Content_Model_Bo_TpItemBiblioteca();
        $boib = new Content_Model_Bo_ItemBiblioteca();
        $data = $botp->getHeaderGrid($post['id_tib']);

        $header  = array();
        foreach($data->toArray() as $key => $value) {
            $meta = json_decode($value['metadata'], false);

            if(isset($meta->ws_ordemLista) ){
                $header[] = array('campo' => $value['metanome'], 'label' => $value['nome']);
            }

            $varchar[] = $value['metanome'];
        }

        $this->header = $header;
        $cont = $boib->getItemBibliotecaGrid($post['id_tib'], $header, $varchar);

        $this->_gridSelect = $cont;
        parent::gridAction();
        
    }

    function callbackpinAction () {

        var_dump($this->getAllParams());
        die();

        $dado = $this->getParam('data');
        foreach ($dado as $key => $value) {
            $response[$key]         = $value;
            $response[$key]['attr'] = 'value';
        }
        $this->_helper->json(array( 'dataModal' => $response ));
    }

    function galeriaAction () {

        // REMOVER PORQUE ESSA ACTION JA VIROU OUTRA

        //$this->view->file = 'DELETAR_GALERIA.html.twig';
        //$this->view->data = array('teste' => 'ronaldo', 'nome_servico' => 'Nome do servico');
    }

    function callbackgaleriaAction () {

        $dado = $this->getParam('data');
        foreach ($dado as $key => $value) {
            $response[$key]         = $value;
            $response[$key]['attr'] = 'src';
        }
        $this->_helper->json(array( 'dataModal' => $response ));
    }

    function campoAction () {

        // $post = $this->getAllParams();

        // $bo        = new Content_Model_Bo_ItemBiblioteca();
        // $data      = $bo->getCamposByTipo($post);

        new dBug($data);
        die();

        $objTeste = array(array("text" => "Test item no. 1", "id" => '1'),
                          array("text" => "Test item no. 2", "id" => '2'));

        $this->_helper->json( $objTeste );
    }
}