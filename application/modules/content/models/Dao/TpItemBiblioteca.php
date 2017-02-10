<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Content_Model_Dao_TpItemBiblioteca extends App_Model_Dao_Abstract
{
	protected $_name          = "tp_itembiblioteca";
	protected $_primary       = "id";

	// protected $_rowClass = 'Content_Model_Vo_TpItemBiblioteca';

	public function getTipoById ($id_tib) {

		$db = Zend_Db_Table::getDefaultAdapter();

		$query = <<<QUERY
				SELECT tipo.*, json_object( array_agg(meta.metanome), array_agg(meta.valor) ) as metadata
				FROM tp_itembiblioteca tipo
				JOIN tp_itembiblioteca_metadata meta ON tipo.id = meta.id_tib
				WHERE tipo.id_tib_pai = ?
				GROUP BY tipo.id
QUERY;

		$db = $db->query($query, array($id_tib) );
  		$rowset = $db->fetchAll();

        return $rowset;
	}

	public function getTipoByIdSelect ($idTib) {
		
	}

	public function getHeaderGrid ($id_tib) {

		$select  = $this->select()->setIntegrityCheck(false);
		$select2 = $this->select()->setIntegrityCheck(false);

		$select2->from(array('tp' => 'tp_itembiblioteca'), array('id' => 'tp.id', 'metas' => new zend_db_expr('json_object(array_agg(meta.metanome), array_agg(meta.valor))')))
                        ->join(array('meta' => 'tp_itembiblioteca_metadata'), 'tp.id = meta.id_tib', array() )
                        ->where('tp.id_tib_pai = ?', $id_tib )
                        ->group(array('tp.id'));

		$select ->from(array('tpib' => $this->_name), array('*', 'metadata' => 'metas.metas' ))
                        ->joinLeft(array('metas' => new zend_db_expr( "( $select2 )" ) ), 'tpib.id = metas.id', array())
                        ->where('tpib.id_tib_pai = ?', $id_tib)
                        ->order(array('tpib.id'));

		$rowset = $this->fetchAll($select);

		return $rowset;

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
    
	public function teste($id_tib)
	{
		$select = $this->select();

		$select->from(array('tpib' => $this->_name), array('*', 'metadata' => new zend_db_expr('json_object( array_agg(meta.metanome), array_agg(meta.valor))') ))
		->join(array('meta' =>  'tp_itembiblioteca_metadata'), 'tpib.id = meta.id_tib', array())
		->where('tpib.id_tib_pai = ?', $id_tib)
		->group('tpib.id');

		return $select;
	}

	public function getTipoConteudo ($time) {

		$db     = Zend_Db_Table::getDefaultAdapter();
		$params = array($time);

		$query = <<<QUERY
				SELECT tipo_master.*
				FROM rl_grupo_item relacao
				JOIN (
				WITH RECURSIVE grupos AS (
					SELECT * FROM tb_grupo WHERE id_pai = ? AND metanome = 'SITE'
					UNION
					SELECT filhos.* FROM grupos pai
					JOIN tb_grupo filhos ON filhos.id_pai = pai.id

				)SELECT * FROM grupos ) AS grupos ON relacao.id_grupo = grupos.id
				JOIN tb_itembiblioteca itens ON relacao.id_item = itens.id
				JOIN tb_itembiblioteca itens_filho ON itens.id = itens_filho.id_ib_pai
				JOIN tp_itembiblioteca tipo_master ON itens.id_tib = tipo_master.id
				JOIN tp_itembiblioteca tipo_filho ON itens_filho.id_tib = tipo_filho.id
				JOIN ( SELECT * FROM tp_itembiblioteca_metadata WHERE metanome = 'cms_twig') meta ON tipo_filho.id = meta.id_tib
				GROUP BY tipo_master.metanome, tipo_master.id
QUERY;

		$db = $db->query($query, array($time) );

        $rowset = $db->fetchAll();
        return $rowset;
	}

	public function getTipo() {
		return 0;
	}
}