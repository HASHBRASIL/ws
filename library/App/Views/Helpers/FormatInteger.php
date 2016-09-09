<?php

class Zend_View_Helper_FormatInteger extends Zend_View_Helper_Abstract
{

    /**
     *
     * Format values.
     * value for apply format ex: 12345678900
     * format output ex: 99.999.999.999
     * return 12.345.678.900
     *
     * @param $str string
     * @return string
     */

    public function formatInteger($str)
    {
        return number_format($str,0, ',', '.');
    }

}