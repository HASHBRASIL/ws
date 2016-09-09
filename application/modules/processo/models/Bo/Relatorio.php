<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  02/05/2014
 */
class Processo_Model_Bo_Relatorio extends App_Model_Bo_Abstract
{

    public function getRelatorioProcesso($request)
    {
        if(isset($request['de_pro_data_inc'])){
            $request['de_pro_data_inc'] = $this->dateYmd($request['de_pro_data_inc']);
        }
        if(isset($request['para_pro_data_inc'])){
            $request['para_pro_data_inc'] = $this->dateYmd($request['para_pro_data_inc']);
        }
        $processoBo = new Processo_Model_Dao_Processo();
        $AgrupadorFinanceiroBo = new Financial_Model_Bo_AgrupadorFinanceiro(); 
        $processoList = $processoBo->getRelatorioProcesso($request);
       
        return $processoList;
    }
    
    public function getRelatorioAnalitico($request)
    {
        if(isset($request['de_pro_data_inc'])){
            $request['de_pro_data_inc'] = $this->dateYmd($request['de_pro_data_inc']);
        }
        if(isset($request['para_pro_data_inc'])){
            $request['para_pro_data_inc'] = $this->dateYmd($request['para_pro_data_inc']);
        }
        if(isset($request['empresa_sacado']) && !empty($request['empresa_sacado'])){
        	$request['empresaList'] = explode(',', $request['empresa_sacado']);
        }
        
        $request['tmv_id'] = Financial_Model_Bo_TipoMovimento::CREDITO;
        $processoBo 			= new Processo_Model_Dao_Processo();
        $processoList = $processoBo->getRelatorioAnalitico($request);
         
        return $processoList;
    	
    }
}