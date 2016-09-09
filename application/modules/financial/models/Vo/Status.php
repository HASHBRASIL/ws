<?php
class Financial_Model_Vo_Status extends App_Model_Vo_Row
{

    public function getListFinancial()
    {
            $financialDao = new Financial_Model_Dao_Financial();
            return $this->findDependentRowset('Financial_Model_Dao_Financial', 'Status');
    }

}