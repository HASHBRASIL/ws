<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/07/2013
 */
class Sis_ContatoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_Contato
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Sis_Model_Bo_Contato();
        $this->_aclActionAnonymous = array('get');
        parent::init();
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $endereco = $this->_bo->get($id);
        $endereco->telefone1 = $this->formatTelefone($endereco->telefone1);
        $endereco->telefone2 = $this->formatTelefone($endereco->telefone2);
        $endereco->telefone3 = $this->formatTelefone($endereco->telefone3);

        $this->_helper->json($endereco->toArray());
    }
    
    private function formatTelefone($telefone)
    {
    	if(empty($telefone)){
    		return null;
    	}
    	if(strpos($telefone, '0') === 0){
    		$primeiraParte 	= substr( $telefone, 0, 4 );
    		$segundaParte	= substr( $telefone, 4, 3 );
    		$terceiraParte	= substr( $telefone, 7, 4 );
    		return $primeiraParte.' '.$segundaParte.' '.$terceiraParte;
    	}
    	switch (strlen($telefone) ){
    		case 10:
    			$codigoCidade 	= substr( $telefone, 0, 2 );
    			$primeiraParte	= substr( $telefone, 2, 4 );
    			$segundaParte	= substr( $telefone, 6, 4 );
    			return '('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
    			break;
    		case 11:
    			$codigoCidade 	= substr( $telefone, 0, 2 );
    			$primeiraParte	= substr( $telefone, 2, 5 );
    			$segundaParte	= substr( $telefone, 7, 4 );
    			return '('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
    			break;
    		case 12:
    			$codigoPais		= substr( $telefone, 0, 2 );
    			$codigoCidade 	= substr( $telefone, 2, 2 );
    			$primeiraParte	= substr( $telefone, 4, 4 );
    			$segundaParte	= substr( $telefone, 8, 4 );
    			return $codigoPais.'('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
    			break;
    		case 13:
    			$codigoPais		= substr( $telefone, 0, 2 );
    			$codigoCidade 	= substr( $telefone, 2, 2 );
    			$primeiraParte	= substr( $telefone, 4, 5 );
    			$segundaParte	= substr( $telefone, 9, 4 );
    			return $codigoPais.'('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
    			break;
    		default:
    			$codigoCidade 	= substr( $telefone, 0, 2 );
    			$primeiraParte	= substr( $telefone, 2, 4 );
    			$segundaParte	= substr( $telefone, 6, 4 );
    			return '('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
    			break;
    	}
    }

}