<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  01/09/2014
 */
class Zend_View_Helper_CalculoHora extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function calculoHora($hora1, $hora2, $format = "H:i:s")
    {
    	$times = array(
				    $hora1,
				    $hora2
				);

		$seconds = 0;
		
		foreach ( $times as $time )
		{
		    list( $g, $i, $s ) = explode( ':', $time );
		    $seconds += $g * 3600;
		    $seconds += $i * 60;
		    $seconds += $s;
		}
		
		$hours    = floor( $seconds / 3600 );
		$seconds -= $hours * 3600;
		$minutes  = floor( $seconds / 60 );
		$seconds -= $minutes * 60;
		if($minutes == 0){
			$minutes = "00";
		}
		
		return "{$hours}:{$minutes}";  
    }
}

