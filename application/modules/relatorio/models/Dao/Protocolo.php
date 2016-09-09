<?php
class Relatorio_Model_Dao_Protocolo extends App_Model_Dao_Abstract{
     protected  $_name = 'tb_gm_movimento';

     public function getRegistros($idProtocolo){
        $sql = "SELECT upper(ttp.nome) tpprot,emprec.nome_razao emprecebe,empfor.nome_razao empfornece,
                if(LENGTH(emprec.cnpj_cpf) = 14,
                            if (emprec.cnpj_cpf is null or emprec.cnpj_cpf = '',
                              '',
                              CONCAT(SUBSTRING(emprec.cnpj_cpf, 1, 2),'.',SUBSTRING(emprec.cnpj_cpf, 3, 3),'.',SUBSTRING(emprec.cnpj_cpf, 6, 3),'/',SUBSTRING(emprec.cnpj_cpf, 9, 4),'-',SUBSTRING(emprec.cnpj_cpf, 13, 2))),
                            if(emprec.cnpj_cpf is null or emprec.cnpj_cpf = '',
                              '',
                              CONCAT(SUBSTRING(emprec.cnpj_cpf, 1, 3),'.',SUBSTRING(emprec.cnpj_cpf, 4, 3),'.',SUBSTRING(emprec.cnpj_cpf, 7, 3),'-',SUBSTRING(emprec.cnpj_cpf, 10, 2)))) cnpj_cpfrecebe,
                if(LENGTH(empfor.cnpj_cpf) = 14,
                            if (empfor.cnpj_cpf is null or empfor.cnpj_cpf = '',
                              '',
                              CONCAT(SUBSTRING(empfor.cnpj_cpf, 1, 2),'.',SUBSTRING(empfor.cnpj_cpf, 3, 3),'.',SUBSTRING(empfor.cnpj_cpf, 6, 3),'/',SUBSTRING(empfor.cnpj_cpf, 9, 4),'-',SUBSTRING(empfor.cnpj_cpf, 13, 2))),
                            if(empfor.cnpj_cpf is null or empfor.cnpj_cpf = '',
                              '',
                              CONCAT(SUBSTRING(empfor.cnpj_cpf, 1, 3),'.',SUBSTRING(empfor.cnpj_cpf, 4, 3),'.',SUBSTRING(empfor.cnpj_cpf, 7, 3),'-',SUBSTRING(empfor.cnpj_cpf, 10, 2)))) cnpj_cpffornece,
                IF(pro.pro_desc_produto = '' OR pro.pro_desc_produto IS NULL,'não definido',pro.pro_desc_produto) processo,
                IF(tcc.cec_descricao = '' OR tcc.cec_descricao IS NULL,'não definido',tcc.cec_descricao) cencusto,
                (select date_format(tp.dt_entrada,'%d/%m/%y')) as dt_entrada,
                tp.hr_entrada,
                if(id_tp_transportador = 1,emptrans.nome_razao,if(id_tp_transportador = 2,pes.pes_nome,'---')) transportador,
                if(id_tp_transportador = 1,
                  if(LENGTH(emptrans.cnpj_cpf) = 14,
                            if (emptrans.cnpj_cpf is null or emptrans.cnpj_cpf = '',
                              '',
                              CONCAT(SUBSTRING(emptrans.cnpj_cpf, 1, 2),'.',SUBSTRING(emptrans.cnpj_cpf, 3, 3),'.',SUBSTRING(emptrans.cnpj_cpf, 6, 3),'/',SUBSTRING(emptrans.cnpj_cpf, 9, 4),'-',SUBSTRING(emptrans.cnpj_cpf, 13, 2))),
                            if(pes.pes_cpf_cnpj is null or pes.pes_cpf_cnpj = '',
                              '',
                              CONCAT(SUBSTRING(emptrans.cnpj_cpf, 1, 3),'.',SUBSTRING(emptrans.cnpj_cpf, 4, 3),'.',SUBSTRING(emptrans.cnpj_cpf, 7, 3),'-',SUBSTRING(emptrans.cnpj_cpf, 10, 2)))),
                  if(id_tp_transportador = 2,
                    if(LENGTH(pes_cpf_cnpj) = 14,
                            if (pes.pes_cpf_cnpj is null or pes.pes_cpf_cnpj = '',
                              '',
                              CONCAT(SUBSTRING(pes.pes_cpf_cnpj, 1, 2),'.',SUBSTRING(pes.pes_cpf_cnpj, 3, 3),'.',SUBSTRING(pes.pes_cpf_cnpj, 6, 3),'/',SUBSTRING(pes.pes_cpf_cnpj, 9, 4),'-',SUBSTRING(pes.pes_cpf_cnpj, 13, 2))),
                            if(pes.pes_cpf_cnpj is null or pes.pes_cpf_cnpj = '',
                              '',
                              CONCAT(SUBSTRING(pes.pes_cpf_cnpj, 1, 3),'.',SUBSTRING(pes.pes_cpf_cnpj, 4, 3),'.',SUBSTRING(pes.pes_cpf_cnpj, 7, 3),'-',SUBSTRING(pes.pes_cpf_cnpj, 10, 2)))),
                    '---')) cnpj_cpf_trans,
                tm.id_movimento,ti.nome item,ttm.nome movimento, CAST(tm.quantidade AS   SIGNED INT) quantlote,te.cod_lote codigo, te.quantidade quantuni,ttu.nome unidade,
                (select sum(quantidade) from tb_gm_estoque ite where ite.id_item = ti.id_item) as quantestoque ,
                (select date_format(sysdate(),'%d/%m/%y - %h:%m:%s')) as dataAtual
                FROM tb_gm_movimento tm
                inner join tb_gm_protocolo tp on tp.id_protocolo = tm.id_protocolo
                inner join tb_gm_tp_protocolo ttp on ttp.id_tp_protocolo = tp.id_tp_protocolo
                inner join tb_empresas emprec on emprec.id = tp.id_empresa_receptora
                inner join tb_empresas empfor on empfor.id = tp.id_empresa_fornecedor
                left join tb_empresas emptrans on emptrans.id = tp.id_transportador
                left join tb_pessoal pes on pes.pes_id = tp.id_funcionario_transportador
                left join tb_processo pro on pro.pro_id = tp.id_processo
                left join tb_centro_custo tcc on tcc.cec_id = tp.id_centro_custo
                inner join tb_gm_estoque_gm_movimento ta on tm.id_movimento = ta.id_movimento
                inner join tb_gm_estoque te on ta.id_estoque = te.id_estoque
                inner join tb_gm_item ti on ti.id_item = te.id_item
                inner join tb_gm_tp_movimento ttm on ttm.id_tp_movimento = tm.id_tp_movimento
                inner join tb_tipo_unidade ttu on ttu.id_tipo_unidade = te.id_tipo_unidade
                where tm.id_protocolo =".$idProtocolo.";
          ";
        return $this->_db->fetchAll($sql, null, Zend_Db::FETCH_OBJ);

     }
}