<?php
class Content_GrupoController extends App_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->_helper->layout()->setLayout('novo_hash');
    }

    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }

    public function updGrupoPosicaoAction()
    {
        if($this->_request->isPost()){

            $postado = $this->_request->getPost();
            $grupo   = new Config_Model_Bo_GrupoMetadata();
            $pos     = 0;

            foreach($postado['bloco'] as $pai => $filhos){
                foreach($filhos as $filho){
                    $grupo->updPosicao($filho, ++$pos);
                }
            }
        }

        $this->_helper->json($postado);
    }

    /*
    private function array_place($key, $value, &$array, $type = 'create')
    {
        foreach($array as $k => $v)
        {
            if($key == $k){
                if($type == 'create'){
                    $array[$k][$value] = array();
                }else{
                    $array[$k] = $value;
                }
                return true;
            }else if(is_array($v)){
                return $this->array_place($key, $value, $array[$k], $type);
            }
        }
        return false;
    }*/

    private function logTree($msg, $type = 'grupos')
    {
        $dir = APPLICATION_PATH.'/../public/log/';
        if(!is_dir($dir)){
            mkdir($dir, 0755);
        }

        $handle = fopen($dir.'/log-'.$type.'-'.date('Y-m-d').'.txt', 'a+');
        fwrite($handle, $msg."\n\r");
        fclose($handle);
    }


    public function listaGruposTreeAction()
    {
        $this->identity  = Zend_Auth::getInstance()->getIdentity();

        $idTimeEscolhido = $this->_request->getParam('idTimeEscolhido', $this->identity->time['id']);

        $modelGrupo = new Config_Model_Bo_Grupo();
        $novaArvore = array();

        if($idTimeEscolhido == 'todos'){

            $orfaos = $modelGrupo->listGruposOrfaos();

            $novaArvore[] = (object) array(
                    'id'     => 'pai',
                    'parent' => '#',
                    'text'   => 'TODOS',
                    'type'   => 'pai',
                );

            foreach( $orfaos as $grupo )
            {
                $novaArvore[] = (object) array(
                    'id'     => $grupo['id'],
                    'parent' => 'pai',
                    'text'   => $nome,
                    'type'   => $grupo['id_pai'],
                );

                $grupos = $modelGrupo->listGruposAbaixo($grupo['id']);

                foreach( $grupos as $subGrupo )
                {
                    $parent = empty($subGrupo['id_pai']) ? '#' : $subGrupo['id_pai'];
                    $nome = empty($subGrupo['nome']) ? 'S/N' : $subGrupo['nome'];

                    $novaArvore[] = (object) array(
                        'id'     => $subGrupo['id'],
                        'parent' => $parent,
                        'text'   => $nome,
                        'type'   => $subGrupo['id_pai'],
                    );
                }
            }

        }else{
            $grupos = $modelGrupo->listGruposAbaixo($idTimeEscolhido);

            foreach( $grupos as $grupo )
            {
                $parent = empty($grupo['id_pai']) ? '#' : $grupo['id_pai'];
                $nome = empty($grupo['nome']) ? 'S/N' : $grupo['nome'];

                $novaArvore[] = (object) array(
                    'id'     => $grupo['id'],
                    'parent' => $parent,
                    'text'   => $nome,
                    'type'   => $grupo['id_pai'],
                );
            }
        }



        $novaArvore[0]->parent = '#';

        $this->_helper->json($novaArvore);
    }

    public function copiaGruposTreeAction()
    {
        $modelGrupo     = new Config_Model_Bo_Grupo();

        $copiado  = $this->_request->getParam('copiado', null);
        $colado   = $this->_request->getParam('colado', null);
        $msg      = '';

        if(empty($copiado)){ $this->_helper->json(array('msg' => 'Copiado não indentificavel'));}
        if(empty($colado)){  $this->_helper->json(array('msg' => 'Destino não indentificavel'));}

        $modelGrupo->copiaGrupo($copiado, $colado, $this->identity->pessoa->id);

        $this->_helper->json(array('msg' => $msg));
    }

    public function excluiGrupoTreeAction()
    {
        $modelGrupo     = new Config_Model_Bo_Grupo();

        $uuid  = $this->_request->getParam('uuid', null);

        if(empty($uuid)){ $this->_helper->json(array('msg' => 'Grupo não indentificavel'));}

        $msg = $modelGrupo->delGrupoSemRelacao($uuid);

        if($msg === true){
            $this->_helper->json(array('msg' => ''));
        }else{
            $this->_helper->json(array('msg' => $msg));
        }
    }

    public function salvaGrupoTreeAction()
    {
        $this->identity  = Zend_Auth::getInstance()->getIdentity();

        $modelGrupo = new Config_Model_Bo_Grupo();
        $modelGrupoMetadata = new Config_Model_Bo_GrupoMetadata();

        $retorno = array('msg' => '');
        $nome  = $this->_request->getParam('nome', '');
        $idpai = $this->_request->getParam('idPai', '');
        $uuid  = $this->_request->getParam('uuid', null);

        if(empty($idpai)){  $this->_helper->json(array('msg' => 'Grupo pai deve ser preenchido'));}

        if(empty($uuid)){

            if(empty($nome)){ $this->_helper->json(array('msg' => 'nome precisa ser preenchido')); }

            $novoId = $modelGrupo->insere(  $nome, preg_replace('[^a-zA-Z0-9]', '', $nome), $idpai,
                                            null, null, $this->identity->pessoa->id, null, null,'f');

            //$this->logTree('Criou grupo '.$novoId);

            $retorno['uuid'] = $novoId;
        }else{
            $dataToUpdate = array('id_pai' => $idpai);
            if(!empty($nome)){
                $dataToUpdate['nome'] = $nome;
                //$grupo = $modelGrupo->getGrupo($uuid);
                //$this->logTree('Atualizou grupo '.$uuid.' (nome, id_pai) ('.$grupo['nome'].' '.$grupo['id_pai'].') -> ('.$nome.' - '.$idpai.')');
            }

            $modelGrupo->update( $uuid, $dataToUpdate );

            $reordenarGrupos = $this->_request->getParam('reordenarGrupos', array());

            foreach($reordenarGrupos as $lista)
            {
                if(is_array($lista['lista'])){
                    foreach($lista['lista'] as $grupo)
                    {
                        $modelGrupoMetadata->updPosicao($grupo['id'],  $grupo['pos']);
                    }
                }
            }
        }

        $this->_helper->json($retorno);
    }

    public function gerenciarGruposAction()
    {
    	$this->identity  = Zend_Auth::getInstance()->getIdentity();

        $idTimeEscolhido = $this->_request->getParam('idTimeEscolhido', $this->identity->time['id']);
        $idTipoEscolhido = $this->_request->getParam('idTipoEscolhido', 'grupos');
        $times = $this->identity->times;

        array_push($times, array('id' => 'todos', 'nome' => 'TODOS'));

        $this->view->data = array(
                'idTimeEscolhido'   => $idTimeEscolhido,
                'idTipoEscolhido'   => $idTipoEscolhido,
                'times'             => $times,
                'arvore'            => $novaArvore
        );
    }

    public function delMetaGrupoAction()
    {
        $retorno = array('msg' => '');

        try{
            $modelGrupoMeta = new Config_Model_Bo_GrupoMetadata();

            $id_grupo  = $this->_request->getParam('id_grupo', null);
            $metanome  = $this->_request->getParam('metanome', null);

            if(empty($id_grupo) || empty($metanome)){
                throw new Exception('Dados incompletos');
            }

            if(!$modelGrupoMeta->remove($id_grupo, $metanome)){
                throw new Exception('erro');
            }

        } catch (Exception $ex) {
            $retorno['msg'] = $ex->getMessage();
        }

        $this->_helper->json($retorno);
    }

    public function updGrupoAction()
    {
        $retorno = array('msg' => '');

        try{
            $modelGrupo     = new Config_Model_Bo_Grupo();

            $id_grupo  = $this->_request->getParam('id_grupo', null);
            $coluna     = $this->_request->getParam('coluna', null);
            $valor     = $this->_request->getParam('valor', null);

            if(empty($id_grupo) || empty($coluna)){
                throw new Exception('Dados incompletos');
            }

            if(!$modelGrupo->update($id_grupo, array($coluna => $valor))){
                throw new Exception('erro');
            }

        } catch (Exception $ex) {
            $retorno['msg'] = $ex->getMessage();
        }

        $this->_helper->json($retorno);
    }

    public function updMetaGrupoAction()
    {
        $retorno = array('msg' => '');

        try{

            $modelGrupoMeta = new Config_Model_Bo_GrupoMetadata();

            $id_grupo  = $this->_request->getParam('id_grupo', null);
            $metanome  = $this->_request->getParam('metanome', null);
            $valor     = $this->_request->getParam('valor', null);

            if(empty($id_grupo) || empty($metanome)){
                throw new Exception('Dados incompletos');
            }

            if(!$modelGrupoMeta->updateMeta($id_grupo, $metanome, $valor)){
                throw new Exception('erro');
            }

        } catch (Exception $ex) {
            $retorno['msg'] = $ex->getMessage();
        }

        $this->_helper->json($retorno);
    }

    public function addMetaGrupoAction()
    {
        $retorno = array('msg' => '');

        try{
            $modelGrupo     = new Config_Model_Bo_Grupo();
            $modelGrupoMeta = new Config_Model_Bo_GrupoMetadata();

            $id_grupo  = $this->_request->getParam('id_grupo', null);
            $metanome  = $this->_request->getParam('metanome', null);
            $valor     = $this->_request->getParam('valor', null);

            if(empty($id_grupo) || empty($metanome) || empty($valor)){
                throw new Exception('Dados incompletos');
            }

            $grupo = $modelGrupo->getGrupo($id_grupo);

            if(empty($grupo)){
                throw new Exception('Grupo inexistente');
            }

            if(!$modelGrupoMeta->insere($id_grupo, $metanome, $valor)){
                throw new Exception('erro');
            }
        } catch (Exception $ex) {
            $retorno['msg'] = $ex->getMessage();
        }

        $this->_helper->json($retorno);
    }

    public function grupoPropriedadesAction()
    {
        $this->_helper->layout->disableLayout();
        $retorno = array();

        try{
            $modelGrupo     = new Config_Model_Bo_Grupo();
            $modelGrupoMeta = new Config_Model_Bo_GrupoMetadata();

            $id_grupo  = $this->_request->getParam('id_grupo', null);
            //$id_grupo  = '4762bf42-3253-11e6-a44e-271bf079f4e3';

            $retorno['idGrupo'] = $id_grupo;

            if(empty($id_grupo)){ throw new Exception('Grupo não enconrado'); }

            $grupo = $modelGrupo->getGrupo($id_grupo);
            $metas = $modelGrupoMeta->listMeta($id_grupo);

            $camposGrupo = ['dtype', 'metanome', 'nome', 'publico', 'id_canal', 'descricao', 'id_representacao'];
            $camposGrupoReadOnly = ['id', 'id_pai', 'dt_inclusao'];

            foreach($camposGrupo as $campo){
                $retorno['grupo'][$campo] = $grupo[$campo];
            }

            foreach($camposGrupoReadOnly as $campo){
                $retorno['grupoReadOnly'][$campo] = $grupo[$campo];
            }

            $retorno['metas'] = $metas;

        } catch (Exception $ex) {
            $retorno['msg'] = $ex->getMessage();
        }

        //$this->view->file = 'mensagem.twig';
        $this->view->data = $retorno;
    }
}
