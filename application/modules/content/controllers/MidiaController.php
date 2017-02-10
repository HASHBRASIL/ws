<?php
class Content_MidiaController extends App_Controller_Action_Twig
{
    /**
     * @var Content_Model_Bo_Ib
     */
    protected $_bo;

    public function init()
    {
        parent::init();
        $this->_bo = new Content_Model_Bo_ItemBiblioteca();
    }

    public function gridAction() {

        $filedir   = Zend_Registry::getInstance()->get('config')->get('filedir');
        $modelTPIB = new Content_Model_Bo_TpItemBiblioteca();

        $this->header = $modelTPIB->getBasicConfigHeader($this->servico);

        if(empty($this->header)) {
            $this->header = array( /*array('campo' => 'id',        'label' => 'id'),*/
                            array('campo' => 'imglocal',  'label' => 'imglocal', 'tipo' => 'image' ),
                            array('campo' => 'credito',   'label' => 'credito'));
        }

        $objGrupo = new Config_Model_Bo_Grupo();

        //$grupo = $this->identity->grupo['id'];
        if (isset($this->servico['id_grupo'])){
            $this->_grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])){
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'],$this->servico['metadata']['ws_grupo']);
            if(!empty($grupos)){
                $this->_grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $this->_grupo = $this->identity->grupo['id'];
        }

        $select = $this->_bo->getItemBibliotecaGrid($this->servico['id_tib'], $this->_grupo);

        $this->_gridSelect = $select;

        parent::gridAction();

        $this->view->filedir = $filedir;
        $this->view->file    = 'midiaGrid.html.twig';
    }

    public function cropAction() {

        $boIb    = new Content_Model_Bo_ItemBiblioteca();
        $boTib   = new Content_Model_Bo_TpItemBiblioteca();
        $objGrupo = new Config_Model_Bo_Grupo();
        
        if (isset($this->servico['id_grupo'])){
            $this->_grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])){
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'],$this->servico['metadata']['ws_grupo']);
            if(!empty($grupos)){
                $this->_grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $this->_grupo = $this->identity->grupo['id'];
        }
        $grupo   = $this->_grupo;
        $time    = $this->identity->time['id'];
        

        $idTib   = $this->servico['id_tib'];
        $config  = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $filedir = $config->getOption('filedir');
        $data    = $this->getallParams();

        $uuidLocal = UUID::v4() . '.jpg';
        $uuidGrd   = UUID::v4() . '.jpg';
        $uuidMed   = UUID::v4() . '.jpg';
        $uuidPeq   = UUID::v4() . '.jpg';

        $directory = $time . '/' . $grupo . '/';

        $simpleimage = new abeautifulsite\SimpleImage( $filedir['path'] . str_replace($filedir['url'], '', $data['image']) );

        $imgLocal = $directory . $uuidLocal;
        $imgMed   = $directory . $uuidMed;
        $imgGrd   = $directory . $uuidGrd;
        $imgPeq   = $directory . $uuidPeq;

        $x1 = $data['data']['x'];
        $y1 = $data['data']['y'];
        $x2 = $data['data']['x'] + $data['data']['width'];
        $y2 = $data['data']['y'] + $data['data']['height'];

        if(!file_exists($filedir['path'] . $directory)){
            mkdir($filedir['path'] . $directory, 0755, true);
        }

        copy($data['image'], $filedir['path'] . $imgLocal);

        $simpleimage->crop( $x1, $y1, $x2, $y2 )
                    ->fit_to_width(848)
                    ->save( $filedir['path'] . $imgGrd)
                    ->fit_to_width(393)
                    ->save( $filedir['path'] . $imgMed)
                    ->fit_to_width(292)
                    ->save( $filedir['path'] . $imgPeq);

        $imgTemplate = $boTib->getTipoByIdSelect($idTib);
        $currentIMG  = $boIb->getItemBibliotecaById($data['id_ib_img']);

        foreach($imgTemplate as $row){
            $arCampos[$row['id'] . '_' . $row['metanome']] = null;
        }
        
        foreach($arCampos as $key => $campo){
            $chave = explode('_', $key)[0];
            foreach($currentIMG as $row) {
                if($chave == $row['id_tib']) {
                    $arCampos[$key] = $row['valor'];
                }
            }
        }

        foreach($arCampos as $key => $campos){
            $campoName = explode('_', $key)[1];
            if( $campoName == 'imggrd' ) {
                $arCampos[$key] = $imgGrd;
            } else if($campoName == 'imgmed') {
                $arCampos[$key] = $imgMed;
            } else if($campoName == 'imgpeq') {
                $arCampos[$key] = $imgPeq;
            } else if($campoName == 'imglocal') {
                $arCampos[$key] = $imgLocal;
            }
        }

        $arData = array( 'config' => array('id_tib'   => $idTib,
                                           'data'     => $data,
                                           'config'   => $config,
                                           'filedir'  => $filedir,
                                           'data'     => $data,
                                           'pessoa'   => $this->identity->id,
                                           'time'     => $time,
                                           'grupo'    => $grupo),

                         'campos' => $arCampos);

        $feedback = $boIb->save($arData);

        return $this->_helper->json( array('imgGrd' => $filedir['url'] . $imgGrd, 
                                           'imgMed' => $filedir['url'] . $imgMed, 
                                           'imgPeq' => $filedir['url'] . $imgPeq,
                                           'msg'    => $feedback ));
    }
}