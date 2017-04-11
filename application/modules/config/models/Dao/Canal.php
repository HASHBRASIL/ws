<?php
/**
 * @author Fernando Augusto
 * @since  18/05/2016
 */
class Config_Model_Dao_Canal extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_canal";
    protected $_primary       = "id";
    
    protected $_rowClass = 'Config_Model_Vo_Canal';

    public function getCanalByMetanome( $metanome )
    {
        $select = $this->select()
                       ->from(array('c' => $this->_name))
                       ->where('c.metanome = ?',$metanome);
        return $this->fetchAll($select)->toArray();
    }

}
