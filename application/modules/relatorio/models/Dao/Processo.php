<?php

class Relatorio_Model_Dao_Processo extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_processo";
    protected $_primary       = "pro_id";
    protected $_namePairs     = "pro_desc_produto";
    protected $_colsSearch = array('pro_codigo','pro_id_pedido','pro_cliente','pro_contato','pro_desc_produto','pro_quantidade','pro_vlr_unt','pro_vlr_pedido','pro_prazo_entrega','sta_id','pro_data_entrega','pro_lote_numero','enderecos_id');


    public function getProcessoRelatorio($statusCheck, $emp)
    {
        $sql = array();
  $select = $this->_db->select();
  $subQuery = $this->_db->select();

        $subQuery->from(array('fin' => 'tb_financeiro'), array('fin.fin_nota_fiscal'))
                 ->where('pro_id = pros.pro_id ')
                 ->where('fin.fin_nota_fiscal <> 0')
                 ->where('fin.fin_nota_fiscal is not null')
                 ->limit(1);

        $select->from(array('pros' => 'tb_processo'), array("pros.pro_codigo as pro_codigo",
                "upper(pros.pro_cliente) as pro_cliente",
                "upper(pros.pro_contato) as pro_contato",
                "upper(pros.pro_desc_produto) as pro_desc_produto",
                "upper(pros.pro_quantidade)  as pro_quantidade",
                "upper(pros.pro_vlr_unt) as pro_vlr_unt",
                "upper(pros.pro_prazo_entrega) as pro_prazo_entrega",
                "upper(pros.pro_data_entrega) as pro_data_entrega",
                "(pros.pro_quantidade * pros.pro_vlr_unt) as calculo",
                "upper(pros.pro_vlr_pedido)  as subtotal",
                "(".$subQuery.") as fin_nota_fiscal",
                "(select date_format(sysdate(),'%d/%m/%y - %h:%m:%s'))as dataAtual"
        ))
            ->joinLeft(array('emp'=>'tb_empresas'),'emp.id = pros.empresas_id',array('empresa'=>'emp.nome_razao'))
            ->joinLeft(array('sta' => 'vw_status'), 'sta.sta_id = pros.sta_id',array('status'))
            ->order('sta.status');

        $sql['sql'] = $select->__toString();

                if (!empty($statusCheck[0]) && !empty($emp)){
                    $select->where('pros.empresas_id in (?)', $emp);
                    $select->where('pros.sta_id in (?)', $statusCheck);
                    $sql['sql'] = $select->__toString();
                }

                    elseif (!empty($emp) && empty($statusCheck[0])){
                        $select->where('pros.empresas_id in (?)', $emp);
                        $sql['sql'] = $select->__toString();
                    }
                    if (!empty($statusCheck[0]) && empty($emp)){
                        $select->where('pros.sta_id in (?)',$statusCheck);
                        $sql['sql'] = $select->__toString();
                    }

            $sql['qtdRegistros'] = count($this->_db->fetchAll($sql['sql']));

            return $sql;
    }
}