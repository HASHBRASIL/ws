<?php

class Relatorio_Model_Dao_Financeiro extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_financeiro";
    protected $_primary       = "fin_id";
    protected $_namePairs     = "fin_descricao";
    protected $_colsSearch    = array('stf_id','con_id','tid_id','tie_id','fin_vencimento','fin_compensacao','fin_competencia','fin_valor_unitario','fin_valor','fin_emissao','pro_id','fin_observacao','usu_id','fin_nota_fiscal','ope_id','grupo_id','grupo_id_ope','plc_id','tmv_id','fin_inclusao','moe_id','grupo_id','fin_numero_doc','fin_qnt_recorrencia','fin_id_origem','rcf_id','fin_complemento_desc','fin_num_doc_os','fin_quantidade','tmp_sacador','tmp_cod_proc','tmp_operacao','tmp_empresa','tmp_faturado_contra','tmp_id_pc','tmp_plano_contas','tmp_contas','fin_revisado','fin_data_revisado','fin_revisado_por','fin_conc_sacador','fin_origem_transf','fin_destino_transf','tcr_id','fin_id_rateio','pes_id','pes_id_contato_eg','tis_id','cec_id','fin_codigo_barras','fin_excluido','fin_id_osi','fin_compensacao');

    public function getFinanceiroRelatorio($cond,$resSql = false)
    {
        $select = $this->_db->select();
        $select->from(array('fin' => 'tb_financeiro'),array("date_format( fin.fin_vencimento , '%d/%m/%y') as fin_vencimento",
                "date_format( fin_compensacao , '%d/%m/%y') as fin_compensacao",
                "date_format( fin_emissao , '%d/%m/%y') as fin_emissao",
                "(select date_format(sysdate(),'%d/%m/%y - %h:%m:%s'))as dataAtual",
                'fin_descricao',
                'fin_valor',
                'fin_nota_fiscal',
                'fin_id',
                'fin.stf_id',
                'emp.nome_razao'
        ))
        ->joinLeft(array('stf' => 'tb_status_financeiro'),'stf.stf_id = fin.stf_id',array('stf.stf_descricao'))
        ->joinInner(array('rsf' => 'rel_sacado_financeiro'),'rsf.tb_financeiro_fin_id = fin.fin_id',null)
        ->joinLeft(array('emp' => 'tb_empresas'),'rsf.empresas_id = emp.id',null)
        ->joinLeft(array('plc' => 'tb_plano_contas'),'plc.plc_id = fin.plc_id',array('plc.plc_descricao','plc.plc_id'));

        if(!empty($cond[1])){
            $select->where('fin.stf_id in ('.$cond[1].')');
        }

        if($cond[4] == 'entre'){
            if(!empty($cond[2]) && !empty($cond[3])){
                $select->where('fin_emissao >= ?',$cond[2]);
                $select->where('fin_emissao <= ?',$cond[3]);
            }elseif(!empty($cond[2])){
                $select->where('fin_emissao = ?',$cond[2]);
            }
        }else{
            if(!empty($cond[2])){
                $select->where('fin_emissao = ?',$cond[2]);
            }
        }

        if($cond[7] == 'entre'){
            if(!empty($cond[5]) && !empty($cond[6])){
                $select->where('fin_vencimento >= ?',$cond[5]);
                $select->where('fin_vencimento <= ?',$cond[6]);
            }elseif(!empty($cond[5])){
                $select->where('fin_vencimento = ?',$cond[5]);
            }
        }else{
            if(!empty($cond[5])){
                $select->where('fin_vencimento = ?',$cond[5]);
            }
        }
        if($cond[10] == 'entre'){
            if(!empty($cond[8]) && !empty($cond[9])){
                $select->where('fin_compensacao >= ?',$cond[8]);
                $select->where('fin_compensacao <= ?',$cond[9]);
            }elseif(!empty($cond[8])){
                $select->where('fin_compensacao = ?',$cond[8]);
            }

        }else{
            if(!empty($cond[8])){
                $select->where('fin_compensacao = ?',$cond[8]);
            }
        }

        if(!empty($cond[11]) ||$cond[11] <> '' ){
            $select->where('rsf.empresas_id in ('.$cond[11].')');
        }

        if(!empty($cond[12]) ||$cond[12] <> '' ){
            $select->where('fin.plc_id in ('.$cond[12].')');
        }

        if($cond[13] == 0 ){
            $select->order('stf.stf_descricao');
        }
        if($cond[13] == 1 ){
            $select->order('nome_razao');
        }
        if($cond[13] == 2 ){
            $select->order('plc.plc_descricao');
        }

        //Retorna os registros ou a string SQL
        if($resSql){
            return $this->_db->fetchAll($select);
        }else{
            $nregistro = count($this->_db->fetchAll($select));
            $res['numRes'] =$nregistro;
            if($nregistro)
            {
                $sql = $select->__toString();
                $res['sql'] = str_replace("`", " ", $sql);
            }
            $res['consulta'] = $this->_db->fetchAll($select);

            return $res;
        }
    }

    public function getFinanceiroRecibo($cond,$resSql = false)
    {
        $select = $this->_db->select();
        $select->from(array('fin' => 'tb_financeiro'),array("date_format( fin.fin_vencimento , '%d/%m/%y') as fin_vencimento",
                "date_format( fin_competencia , '%d/%m/%y') as fin_competencia",
                "date_format( fin_emissao , '%d/%m/%y') as fin_emissao",
                "(select date_format(sysdate(),'%d/%m/%y - %h:%m:%s'))as dataAtual",
                'fin_descricao',
                'fin_valor',
                'fin_nota_fiscal',
                'fin_id'))
                ->joinLeft(array('stf' => 'tb_status_financeiro'),'stf.stf_id = fin.stf_id',array('stf.stf_descricao'))
                ->joinLeft(array('rsf' => 'rel_sacado_financeiro'),'rsf.tb_financeiro_fin_id = fin.fin_id')
                ->joinLeft(array('emp' => 'tb_empresas'),'rsf.empresas_id = emp.id',array('nome_razao'));


        if($resSql){
            return $this->_db->fetchAll($select);
        }else{
            $nregistro = count($this->_db->fetchAll($select));
            $res['numRes'] =$nregistro;
            if($nregistro)
            {
                $sql = $select->__toString();
                $res['sql'] = str_replace("`", " ", $sql);
            }
            return $res;
        }
    }

    public function processaTransacao($rowAgrupador, $data, $tipo)
    {
        $valorFin = $rowAgrupador->fin_valor;

        $arrayImpostos = array('vississqntot' => 'ISS', 'vpisissqntot' => 'PIS', 'vcofinsissqntot' => 'Cofins');

        foreach ($arrayImpostos as $imposto => $desc) {
            if ($data[$imposto]) {
                $valorFin -= $data[$imposto];

                $row = $this->createRow();
                $row->id_agrupador_financeiro = $rowAgrupador->id_agrupador_financeiro;
                $row->fin_valor = $data[$imposto];
                $row->fin_descricao = $desc;
                $row->save();
            }
        }

        if ($valorFin != $rowAgrupador->fin_valor) {

            $row = $this->createRow();
            $row->id_agrupador_financeiro = $rowAgrupador->id_agrupador_financeiro;
            $row->fin_valor = $data[$imposto];
            $row->fin_descricao = 'Valor Liquido';
            $row->save();
        }
    }

}