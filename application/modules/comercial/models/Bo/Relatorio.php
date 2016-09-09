<?php
/**
 * @author Vinícius S P Leônidas
 * @since 20/03/2014
 */
class Comercial_Model_Bo_Relatorio extends App_Model_Bo_Abstract
{
	
	protected $_dao;
	
	public function __construct()
	{
		$this->_dao = new Comercial_Model_Dao_Relatorio();
		parent::__construct();
	}
	
	public function buscarRelatorio($dados){
		
		$dados['coluna'][] = 'id';
		
		$key = array_search('tel', $dados['coluna']);
		
		if (!empty($key)) {
			unset($dados['coluna'][$key]);
			$dados['coluna'][] = 'telefone1';
			$dados['coluna'][] = 'telefone2';
			$dados['coluna'][] = 'telefone3';
		}
		
		$keyTs = array_search('transacao', $dados['coluna']);
		if (!empty($keyTs)) {
			unset($dados['coluna'][$keyTs]);
		}
		
		$corporativo = $this->_dao->buscarRelatorio($dados);

		$id = array_search('id', $dados['coluna']);
		
		if (!empty($keyTs)) {
		
			$relSacadoBo = new Financial_Model_Bo_SacadoFinanceiro();

			foreach ($corporativo as $keys => $value){

				$teste = $relSacadoBo->find(array('empresas_id = ?' => $value['id']))->count();
				$corporativo[$keys]['transacao'] = $teste; 
				
			}
				
		}
		
		if (!empty($dados['agrupar']) == 'empresas_id_pai') {
			
			$corporativo = $this->agruparResponsavel($corporativo);
			
		} else if (!empty($dados['agrupar']) == 'grupoGeo') {
			echo '';
		} else if (!empty($dados['agrupar']) == 'tps_id') {
			echo '';
		} else {
			
			$corporativo = $this->noAgrupador($corporativo);
			
		}
		
		return $corporativo;
	}
	
	public function noAgrupador($dados){

		$responsavelArray[0]['id_responsavel'] = 0;
		$responsavelArray[0]['titulo'] = '';
		$responsavelArray[0]['dados'] = $dados;
		
		return $responsavelArray;
	}
	
	public function agruparGeografico($dados){
		
		$grupoGeografico = new Sis_Model_Bo_GrupoGeograficoEmpresa();
		
	}
	
	public function agruparResponsavel($dados){
		
		$responsavelArray = $this->_dao->agruparResponsavel();
		
		foreach ($responsavelArray as $key => $resp){
				
 			foreach ($dados as $keyDados => $dado){
 				
					$verifica = $this->find(array('id = ?' => $dado['id'], 'empresas_id_pai = ?' => $resp['id_responsavel']));
	 				
	 				if(count($verifica) > 0 ){
	 					
	 					$responsavelArray[$key]['dados'][] = $dados[$keyDados];
	 					unset($dados[$keyDados]);
	 				
	  				}
 			}
		}
		$keyNew = count($responsavelArray)+1;
		$responsavelArray[$keyNew]['id_responsavel'] = 0;
		$responsavelArray[$keyNew]['titulo'] = 'Sem responsável';
		$responsavelArray[$keyNew]['dados'] = $dados;
		
		return $responsavelArray;
	}
}