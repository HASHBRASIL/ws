<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class App_Model_Vo_Row extends Zend_Db_Table_Row_Abstract
{
    public function __set($columnName, $value)
    {
        if(is_string($value)) {
            $value = trim($value);
            if(strlen($value) == 0) {
                $value = null;
            }
        }

        parent::__set($columnName, $value);
    }
}