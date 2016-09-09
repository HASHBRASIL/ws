<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_Tib extends App_Model_Dao_Abstract
{
	protected $_name          = "tp_itembiblioteca";
	protected $_primary       = "id";
	protected $_namePairs	  = "valor";
	
	protected $_rowClass = 'Config_Model_Vo_Tib';

        
    public function getById($tib) {
        $select = $this->select()
                       ->from(array($this->_name))
                       ->where('id = ?',$tib);
        return $this->fetchAll($select)->toArray();
    }

    public function getTipoItemBibliotecaGrid($idPai = NULL) {
        
        $select = $this->select()
                ->from(array($this->_name));
        if ($idPai == NULL) {
            $select->where('id_tib_pai is NULL')
            ->where('tipo = ?','Master');
        } else {
            $select->where('id_tib_pai = ?', $idPai);
        }
                
        $select->order(array('metanome'));
//       echo $select; die;
        return $select;
    }
    
    
    public function getTipoItemBibliotecaByMetanome($metanome) {
        
        $select = $this->select()
                ->from(array($this->_name))       
                ->where('tipo = ?','Master')
                ->where('metanome = ?', $metanome);
        
        return $this->fetchAll($select)->toArray();
    }
    
       public function getTemplateByIdTibPai($id_tib_pai)
    {
        $stmt = $this->db->prepare(
                "SELECT item.*, json_object (array_agg( meta.metanome ), array_agg( meta.valor) ) as metadatas, ordem.valor as ordem
                FROM    tp_itembiblioteca       AS item
                JOIN    tp_itembiblioteca_metadata AS meta
                    ON  item.id = meta.id_tib
                JOIN    (select * from tp_itembiblioteca_metadata where metanome = 'ws_ordem') AS ordem
                        ON item.id = ordem.id_tib
                WHERE   item.id_tib_pai = :id_tib_pai
                GROUP BY item.id, ordem.valor
                ORDER BY ordem.valor::INT");
        $stmt->bindValue('id_tib_pai', $id_tib_pai);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
    }
    
     /**
     * @param $idTibPai
     * @return array
     */

    public function getTemplateById($id)
    {
        $stmt = $this->db->prepare(
                "SELECT item.*
                FROM    tp_itembiblioteca   AS item
                INNER   JOIN    tp_itembiblioteca_metadata AS meta
                    ON  item.id = meta.id_tib
                WHERE   item.id = :id and meta.metanome = 'ws_ordem'
                GROUP   BY  item.id, meta.valor::int
                ORDER   BY  meta.valor::int");
        $stmt->bindValue('id', $id);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
    }

    public function getByMetanome($metanome) {
        $select = $this->select()
                       ->from(array($this->_name))
                       ->where('metanome = ?',$metanome);
        return $this->fetchAll($select)->toArray();
    }

    public function getByIdPaiByMetanome($id_pai,$metanome) {
        $select = $this->select()
                       ->from(array($this->_name))
                       ->where('id_tib_pai = ?',$id_pai)
                       ->where('metanome = ?',$metanome);
        return $this->fetchAll($select)->toArray();
    }
    
}