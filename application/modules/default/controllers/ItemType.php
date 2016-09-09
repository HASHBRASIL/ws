<?php

class Controller_Item_Type extends Zend_Controller_Action_Helper_Abstract
{
	public function createServiceCampo($id, $ordem, $obrigatorio, $multiplo, $nome, $descricao, $metanome, $tipo, $perfil, $metadatas){
		$campos = array(
		'id' 			=> $id,
		'ordem' 		=> $ordem,
		'obrigatorio' 	=> $obrigatorio,
		'multiplo' 		=> $multiplo,
		'nome' 			=> $nome,
		'descricao' 	=> $descricao,
		'metanome'		=> $metanome,
		'tipo'			=> $tipo,
		'perfil'		=> $perfil);
		
		foreach ($metadatas as $chave => $valor){
			$campos['metadatas'][$chave]	= $valor;
		}
		
		return $campos;		
	}
}

?>