<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_GrupoMetadata extends App_Model_Dao_Abstract
{
	protected $_name          = "tb_grupo_metadata";
	protected $_primary       = "id";

	protected $_rowClass = 'Config_Model_Vo_GrupoMetadata';

    public function insere($idGrupo, $metanome, $valor)
    {
        $uuid = UUID::v4();
        $select = $this->_db->prepare(
            'INSERT INTO tb_grupo_metadata VALUES (?, ?, ?, ?, now())'
        );

        $select->bindParam(1, $uuid);
        $select->bindParam(2, $metanome);
        $select->bindParam(3, $valor);
        $select->bindParam(4, $idGrupo);
        $select->execute();

        return $uuid;
    }

    public function delGrupoMetadatas($idGrupo)
    {
        $select =  $select = $this->_db->prepare(
            'DELETE FROM tb_grupo_metadata WHERE id_grupo = ?'
        );

        $select->bindParam( 1 , $idGrupo );

        return $select->execute();
    }

    public function delGrupoMetadatasByMetanome($idGrupo, $metanome)
    {
        $select =  $select = $this->_db->prepare(
            'DELETE FROM tb_grupo_metadata WHERE id_grupo = ? AND metanome = ? '
        );

        $select->bindParam( 1 , $idGrupo );
        $select->bindParam( 2 , $metanome );

        return $select->execute();
    }


    public function remove($idGrupo, $metanome)
    {
        $select =  $select = $this->_db->prepare(
            'DELETE FROM tb_grupo_metadata WHERE id_grupo = ? AND metanome = ?'
        );

        $select->bindParam( 1 , $idGrupo );
        $select->bindParam( 2 , $metanome );

        return $select->execute();
    }

    public function updateMeta($idGrupo,$metanome,$valor)
    {
        $select =  $select = $this->_db->prepare(
            'update tb_grupo_metadata set valor = ? WHERE id_grupo = ? AND metanome = ?'
        );

        $select->bindParam( 1 , $valor );
        $select->bindParam( 2 , $idGrupo );
        $select->bindParam( 3 , $metanome );

        return $select->execute();
    }


    public function updateMetaById($id,$valor)
    {
        $select =  $select = $this->_db->prepare(
            'update tb_grupo_metadata set valor = ? WHERE id = ?'
        );

        $select->bindParam( 1 , $valor );
        $select->bindParam( 2 , $id );

        return $select->execute();
    }

    public function listMeta($id_grupo, $metanome = null)
    {
        $select =  $this->select()
                    ->where('id_grupo = ?', $id_grupo);

        if (!is_null($metanome)) {
            $select->where('metanome = ?', $metanome);
        }

        return $this->fetchAll($select);
    }


    public function listMetaByMetanome($id_grupo, $metanome = null)
    {
        $select =  $this->select()
                    ->where('id_grupo = ?', $id_grupo);

        if (!is_null($metanome)) {
            $select->where('metanome = ?', $metanome);
        }

        return $this->fetchAll($select);
    }

    public function listMetaRecursivo($idGrupo, $metanome = null)
    {
        $sql = <<<DML
WITH RECURSIVE gg AS(
  SELECT id
    FROM tb_grupo
    WHERE id = ?
  UNION
  SELECT g.id
    FROM tb_grupo g
      INNER JOIN gg ON (gg.id = g.id_pai)
)
SELECT tgm.*
  FROM gg
    INNER JOIN tb_grupo_metadata tgm ON (gg.id = tgm.id_grupo)
DML;
        if (!is_null($metanome)) {
            $sql .= ' WHERE tgm.metanome = ?';
        }

        $select = $this->_db->prepare($sql);
        $select->bindParam(1, $idGrupo);

        if (!is_null($metanome)) {
            $select->bindParam(2, $metanome);
        }

        $select->execute();
        return $select->fetchAll();
    }
}