<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/04/2013
 */
class Sis_EnderecoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_Endereco
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Sis_Model_Bo_Endereco();
        $this->_aclActionAnonymous = array('get', 'get-by-empresa');
        parent::init();
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $endereco = $this->_bo->get($id);
        $enderecoJson = array(
                'id'              => $endereco->id,
                'cep'             => $endereco->cep,
                'tipo_logradouro' => $endereco->tipo_logradouro,
                'endereco'        => $endereco->nome_logradouro,
                'numero'          => $endereco->numero,
                'complemento'     => $endereco->complemento,
                'bairro'          => $endereco->bairro,
                'id_empresa'      => $endereco->id_empresas,
                'ufs_id'          => $endereco->ufs_id,
                'cid_id'          => $endereco->cid_id
        );

        if(count($endereco->getListTipoEndereco()) > 0){
            foreach ($endereco->getListTipoEndereco() as $ref){
                $enderecoJson['id_tp_ref'][] = $ref->tie_id;
            }
        }

        $this->_helper->json($enderecoJson);
    }

    public function getByEmpresaAction()
    {
        $id_empresa = $this->_getParam('id_empresa');
        $criteria = array(
                'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
                'id_empresas = ?' => $id_empresa
            );
        $enderecoList = $this->_bo->find($criteria);
        $enderecoJson = array();
        if(count($enderecoList) == 0){
            $this->_helper->json(array('success'=> false));
        }
        foreach ($enderecoList as $keyArray => $enderecos){
            foreach ($enderecos as $key => $endereco){
                $enderecoJson[$keyArray][$key] = $endereco;
            }
            $enderecoJson[$keyArray]['uf_sigla'] = $enderecos->getEstado() ? $enderecos->getEstado()->ufs_sigla: null;
            $enderecoJson[$keyArray]['cidade_nome'] = $enderecos->getCidade()? $enderecos->getCidade()->cid_nome : null;
            //verifica se possui tipo de endereço se tem retorna todas as descrição concatenado
            if( count($enderecos->getListTipoEndereco()) > 0 ){
                $tpDescricao = "";
                foreach ($enderecos->getListTipoEndereco() as $countTipo => $tipoEndereco){
                    if($countTipo > 0)
                        $tpDescricao .= ", ";
                    $tpDescricao .= $tipoEndereco->getTipoEndereco()->tie_descricao;
                }
                $enderecoJson[$keyArray]['tipo_endereco'] = $tpDescricao;
            }
        }

        $this->_helper->json($enderecoJson);
    }


    public function getByGrupoAction()
    {
        $id_empresa = $this->_getParam('id_empresa');
        $criteria = array(
                'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
                'id_empresas_grupo = ?' => $id_empresa
        );
        $enderecoList = $this->_bo->find($criteria);
        $enderecoJson = array();
        if(count($enderecoList) == 0){
            $this->_helper->json(array('success'=> false));
        }
        foreach ($enderecoList as $keyArray => $enderecos){
            foreach ($enderecos as $key => $endereco){
                $enderecoJson[$keyArray][$key] = $endereco;
            }
            $enderecoJson[$keyArray]['uf_sigla'] = $enderecos->getEstado() ? $enderecos->getEstado()->ufs_sigla: null;
            $enderecoJson[$keyArray]['cidade_nome'] = $enderecos->getCidade()? $enderecos->getCidade()->cid_nome : null;
            //verifica se possui tipo de endereço se tem retorna todas as descrição concatenado
            if( count($enderecos->getListTipoEndereco()) > 0 ){
                $tpDescricao = "";
                foreach ($enderecos->getListTipoEndereco() as $tipoEndereco){
                    $tpDescricao .= $tipoEndereco->getTipoEndereco()->tie_descricao.' ';
                }
                $enderecoJson[$keyArray]['tipo_endereco'] = $tpDescricao;
            }
        }

        $this->_helper->json($enderecoJson);
    }

}