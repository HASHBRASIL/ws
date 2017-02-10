<?php
/**
 * HashWS
 */

/**
 * Exibição e gestão de perfis.
 *
 * @author Maykel S. Braz
 */
class Config_Perfil2Controller extends App_Controller_Action_Twig
{
    public function init() {
        $this->_translate = Zend_Registry::get('Zend_Translate');
        parent::init();
    }

    public function indexAction()
    {
        $this->view->data = ['id' => $this->identity->id];
    }

    public function editarFotoAction()
    {
        $idPessoa = $this->identity->id;
        $avatar = current((new Config_Model_Bo_Informacao)
            ->getInfoPessoaByMetanome($idPessoa, 'AVATAR'));

        if (empty($avatar)) {
            $this->view->data = ['avatar' => 'img/img-list.jpg'];
        } else {
            $this->view->data = [
                'avatar' => $this->_helper->configuracao('filedir', 'path') . $avatar['valor'],
                'infoid' => $avatar['id']
            ];
        }
    }

    public function salvarFotoAction()
    {

        $idPessoa = $this->identity->id;
        $filepath = $this->_helper->configuracao('filedir', 'path');
        $fileurl = $this->_helper->configuracao('filedir', 'url');
        $grupoBo = new Config_Model_Bo_Grupo();

        $time = current($grupoBo->getGrupoByMetanome(Config_Model_Bo_Grupo::META_GRUPO))['id'];

        $newFolder  =   $filepath . $time . '/';
        $retorno    =   $time . '/';
        if ( !file_exists($newFolder) ){
            mkdir( $newFolder, 0755 );
        }

        // -- grupo de usuario - @
        $grupo = current($grupoBo->getGrupoGeralByCriador($idPessoa))['id'];

        $newFolder  =   $newFolder . $grupo . '/';
        $retorno    =   $retorno . $grupo . '/';

        if ( !file_exists($newFolder) ){
            mkdir( $newFolder, 0755 );
        }

        $file = explode(",", $this->getRequest()->getParam('imagem'));
        $lixo = explode (";", $file[0]);
        $extensao = explode('/', $lixo[0]);

        $imagem = $retorno . UUID::v4() .  '.' . $extensao[1];

        $ifp = fopen( $filepath . $imagem, "wb" );
        fwrite( $ifp, base64_decode( $file[1]) );
        fclose( $ifp );
        $simpleimage = new abeautifulsite\SimpleImage($filepath . $imagem);

        $x1 = $this->getRequest()->getParam('dataCrop')['x'];
        $y1 = $this->getRequest()->getParam('dataCrop')['y'];
        $x2 = $this->getRequest()->getParam('dataCrop')['x'] + $this->getRequest()->getParam('dataCrop')['width'];
        $y2 = $this->getRequest()->getParam('dataCrop')['y'] + $this->getRequest()->getParam('dataCrop')['height'];

         $simpleimage->crop( $x1, $y1, $x2, $y2 )
                    ->fit_to_width(100)
                    ->save($filepath . $imagem);

         $informacao = (new Config_Model_Bo_Informacao())->getInfoPessoaByMetanome($idPessoa, 'AVATAR');
         if(count($informacao) > 0) {
             $informacao = current($informacao);
             $idInfo = (new Config_Model_Bo_Informacao())->persiste($informacao['id'], $informacao['id_tinfo'], $idPessoa, $informacao['id_pai'], $imagem);
         } else {
            $idInfo = (new Config_Model_Bo_Informacao())->addInformacao(
                $idPessoa,
                'AVATAR',
                $imagem,
                null,
                null,
                $this->getParam('infoid')
            );
         }

        $this->_helper->json([
            'msg' => '',
            'path' => $fileurl . "/" . $imagem,
            'id' => $idInfo
        ]);
    }

    public function editarFotoTimeAction()
    {
        $idTime = $this->identity->time['id'];
        $bgtime = current((new Config_Model_Bo_GrupoMetadata())
            ->listMetaByMetanome($idTime, 'ws_avatar')->toArray());

        if (empty($bgtime)) {
            $this->view->data = ['bgtime' => 'img/logo-bg-cinza.png'];
        } else {
            $this->view->data = [
                'bgtime' => $this->_helper->configuracao('filedir', 'path') . $bgtime['valor'],
                'idgrupometadata' => $bgtime['id']
            ];
        }
    }

    public function salvarFotoTimeAction()
    {
        $idTime = $this->identity->time['id'];
        $idGrupoMetadata = $this->getParam('idgrupometadata');
        $filepath = $this->_helper->configuracao('filedir', 'path');

        $pasta = $filepath . $idTime . DIRECTORY_SEPARATOR;
        if (!file_exists($pasta)) {
            mkdir($pasta, 0755, true);
        }

        $file = explode(",", $this->getRequest()->getParam('imagem'));
        $lixo = explode (";", $file[0]);
        $extensao = explode('/', $lixo[0]);

        $imagem = $idTime . DIRECTORY_SEPARATOR . UUID::v4() .  '.' . $extensao[1];

        $ifp = fopen( $filepath . $imagem, "wb" );
        fwrite($ifp, base64_decode($file[1]));
        fclose($ifp);
        $simpleimage = new abeautifulsite\SimpleImage($filepath . $imagem);
        $x1 = $this->getRequest()->getParam('dataCrop')['x'];
        $y1 = $this->getRequest()->getParam('dataCrop')['y'];
        $x2 = $this->getRequest()->getParam('dataCrop')['x'] + $this->getRequest()->getParam('dataCrop')['width'];
        $y2 = $this->getRequest()->getParam('dataCrop')['y'] + $this->getRequest()->getParam('dataCrop')['height'];

        $simpleimage->crop($x1, $y1, $x2, $y2)
            ->fit_to_width(595)
            ->save($filepath . $imagem);

        // -- salva em grupo metadata
        $grupoMetaBo = new Config_Model_Bo_GrupoMetadata();
        if (empty($idGrupoMetadata)) {
            $idGrupoMetadata = $grupoMetaBo->insere($idTime, 'ws_avatar', $imagem);
        } else {
            $grupoMetaBo->updateMeta($idTime, 'ws_avatar', $imagem);
        }

        $this->_helper->json([
            'msg' => '',
            'path' => $filepath . $imagem,
            'id' => $idGrupoMetadata
        ]);
    }
}

