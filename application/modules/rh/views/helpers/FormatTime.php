<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  07/08/2014
 */
class Zend_View_Helper_FormatTime extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function formatTime($hora = null, $format = "H:i:s")
    {
    	if(empty($hora)){
    		return;
    	}
    	$horaFormat = DateTime::createFromFormat('H:i:s', $hora);
		return $horaFormat->format($format);
    }
}

