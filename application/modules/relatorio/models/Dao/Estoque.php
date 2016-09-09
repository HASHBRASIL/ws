<?php
class Relatorio_Model_Dao_Estoque extends App_Model_Dao_Abstract{
    protected $_name = 'tb_gm_estoque';


    public function getQtdRegistros($dataInicial, $dataFinal, $dataEntreExato){
        if ($dataEntreExato == 'entre'){

            $data1 = explode("/", $dataInicial);
            $data2 = explode("/", $dataFinal);

            $dataCondicao = "date(est.dt_criacao) >= '".$data1[2]."-".$data1[1]."-".$data1[0]."' and date(est.dt_criacao)<='".$data2[2]."-".$data2[1]."-".$data2[0]."'";
        }
        if ($dataEntreExato == 'exato'){

            $data1 = explode("/", $dataInicial);

            $dataCondicao = "date(est.dt_criacao) = '".$data1[2]."-".$data1[1]."-".$data1[0]."'";
        }

       $query = "select
           est.cod_lote,
           est.codigo,
           item.nome,
           if(item.materia_prima = 1, 'sim', 'não') as materiaPrima,
           m.nome as marca,
           tpMov.nome as movimento,
           est.quantidade as quantidade,
           un.nome as tipoUnidade,
           est.vl_unitario as vl_unitario,
           (est.quantidade * est.vl_unitario) as total,
           est.dt_criacao,
           mov.id_movimento
           from tb_gm_estoque est
           inner join tb_gm_estoque_gm_movimento estMov on ( estMov.id_estoque = est.id_estoque)
           inner join tb_gm_movimento mov on (mov.id_movimento = estMov.id_movimento)
           inner join tb_gm_item item on (item.id_item = est.id_item )
           inner join tb_gm_tp_movimento tpMov on (mov.id_tp_movimento = tpMov.id_tp_movimento)
           left join tb_gm_marca m on (m.id_marca = est.id_marca)
           inner join tb_tipo_unidade un on (un.id_tipo_unidade = item.id_tipo_unidade_compra)
           where est.ativo = 1 and  ".$dataCondicao."
           order by(est.cod_lote) asc";

       return array('qtd'=>count($this->_db->fetchAll($query)), 'sql'=>$query );
    }
}