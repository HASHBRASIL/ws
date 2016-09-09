<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  24/06/2014
 */
class Zend_View_Helper_SumTime extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function sumTime($hora1 = null, $hora2 = null, $hora3 = null, $hora4 = null, $format = "H:i:s")
    {
	    $sum = null;
		$intervalo1 = null;
		$intervalo2 = null;
		if(!empty($hora1) && !empty($hora2) ){
			$inicio = DateTime::createFromFormat('H:i:s', $hora1);
			$almocoInicio = DateTime::createFromFormat('H:i:s', $hora2);
		
			$intervalo1 = $inicio->diff($almocoInicio);
			$sum = DateTime::createFromFormat('H:i:s', $intervalo1->format('%H:%I:%S'));
		}
			
		if(!empty($hora3) && !empty($hora4)){
			$almocoFim = DateTime::createFromFormat('H:i:s', $hora3);
			$fim = DateTime::createFromFormat('H:i:s', $hora4);
				
			$intervalo2 = $almocoFim->diff($fim);
		}
		if(!empty($intervalo1) && !empty($intervalo2)){
			$sum1 = DateTime::createFromFormat('H:i:s', $intervalo1->format('%H:%I:%S'));
			$sum = $sum1->add($intervalo2);
		}
		return !empty($sum)?$sum->format($format): null;
    }
}

