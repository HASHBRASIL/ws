<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Content_Model_Bo_RlGrupoItem extends App_Model_Bo_Abstract
{
	/**
	 * @var Content_Model_Dao_ItemBiblioteca
	 */
	protected $_dao;

	/**
	 * @var integer
	 */
	public function __construct()
	{
		$this->_dao = new Content_Model_Dao_RlGrupoItem();
		parent::__construct();
	}

	public function relacionaItem($idGrupo, $idItem) {

		$uuid = UUID::v4();

		$row = $this->_dao->createRow();
		$row->id       = $uuid;
		$row->id_grupo = $idGrupo;
		$row->id_item  = $idItem;
		$row->save();

		return $row;
	}

}