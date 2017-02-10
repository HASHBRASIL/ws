<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_GrupoMetadata extends App_Model_Bo_Abstract
{
    /**
     * Identificação do metadado de alias.
     */
    const META_ALIAS = 'cms_alias';
    /**
     * Identificação do metadado de alias.
     */
    const META_CRACHA = 'cms_cracha';
    /**
     * Identificação do metadado de alias.
     */
    const META_INSTALL = 'ws_install';

    /**
     * @var Config_Model_Dao_GrupoMetadata
     */
    public $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_GrupoMetadata();
        parent::__construct();
    }

    public function listMeta($id_grupo)
    {
        return $this->_dao->listMeta($id_grupo);
    }
    
    public function listMetaByMetanome($id_grupo, $metanome)
    {
        return $this->_dao->listMetaByMetanome($id_grupo, $metanome);
    }
    
    public function updPosicao($id_grupo, $pos)
    {
        $meta = $this->_dao->fetchOne(array('id_grupo = ?' => $id_grupo, 'metanome = ?' => 'cms_ordem'));

        if(empty($meta)){
            $this->_dao->insert(array('id' => UUID::v4(), 'id_grupo' => $id_grupo, 'valor' => $pos, 'metanome' => 'cms_ordem'));
        }else{
            $meta->valor = $pos;
            $meta->save();
        }
    }

    public function updateMeta($idGrupo,$metanome,$valor){
        return $this->_dao->updateMeta($idGrupo,$metanome,$valor);
    }

    public function remove($idGrupo, $metanome){
        return $this->_dao->remove($idGrupo, $metanome);
    }

    public function delGrupoMetadatas($idGrupo){
        return $this->_dao->delGrupoMetadatas($idGrupo);
    }

    public function insere($idGrupo,$metanome,$valor)
    {
        return $this->_dao->insere($idGrupo,$metanome,$valor);
    }

    /**
     * Encontra um alias de time na tabela tb_grupo_metadata.
     *
     * @param string $alias Alias para o time.
     * @return array
     */
    public function findByAlias($alias)
    {
        return $this->findOne([
            'metanome = ?' => self::META_ALIAS,
            'valor = ?' => $alias
        ]);
    }

    /**
     * Modifica os alias de um grupo copiado, adicionando ao valor do alias
     * um pósfixo igual ao alias do time.
     *
     * @param type $idTime
     * @param type $dnsId
     */
    public function atualizaAliasECriaDns($idTime, $dnsId = null)
    {
        $aliasTime = current($this->listMeta($idTime, self::META_ALIAS)
            ->toArray())['valor'];

        $metas = $this->_dao->listMetaRecursivo($idTime);
        
        $arrayMetas = array();
        foreach ($metas as $item) {
            $arrayMeta = explode('__',$item['metanome']);
            if ( count($arrayMeta) == 2 ) {
                if(!key_exists($item['id_grupo'], $arrayMetas)){
                    $arrayMetas[$item['id_grupo']][$arrayMeta[0]] = str_replace("%alias%", $aliasTime, $item['valor']);
                }
            }
        }
        
        $siteBo = new Config_Model_Bo_Site();
        foreach ($metas as $meta) {
            if ($aliasTime == $meta['valor']) {
                continue;
            }
            
            if ($meta['metanome'] == self::META_ALIAS) {
                
                $valor = $arrayMetas[$meta['id_grupo']]['cms_alias'];
                $this->_dao->updateMetaById(
                    $meta['id'],
                    $valor
                    );
                
                $siteBo->geraDns($dnsId, $valor);
            }
            
            if ($meta['metanome'] == self::META_CRACHA) {
                 $this->_dao->delGrupoMetadatasByMetanome($meta['id_grupo'], $meta['metanome']);
            }
        }
    }
}
