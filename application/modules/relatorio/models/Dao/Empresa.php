<?php
/**
 * @author Alexandre Nascimetnto Barbosa
 * @since  11/06/2013
 */
class Relatorio_Model_Dao_Empresa extends App_Model_Dao_Abstract
{
    protected $_name = "tb_empresas";
    protected $_primary = "id";
    protected $_namePairs = 'nome_razao';

    protected $_dependentTables = array('Sis_Model_Dao_EmpresaGrupo');


    public function getPairsEmpresa()
    {


        $select = "select
                    if(pros.empresas_id is not null,emp.nome_razao,if(pros.empresas_grupo_id is not null,empg.razao,pes.pes_nome)) id,
                    if(pros.empresas_id is not null,concat('emp-',emp.id),if(pros.empresas_grupo_id is not null,concat('empg-',empg.id),concat('pes-',pes.pes_id))) nome_razao  from tb_processo pros
                    left join tb_empresas emp on emp.id = pros.empresas_id
                    left join tb_empresas_grupo empg on empg.id = pros.empresas_grupo_id
                    left join tb_pessoal pes on pes.pes_id = pros.pes_id

                    ;";


        return $this->_db->fetchPairs($select);
    }

    public function getProId()
    {


        $select = "select `tb_processo`.`pro_id` AS `pro_id`,
                    (case when (`rel_sacado_financeiro`.`pes_id` is not null) then (select `tb_pessoal`.`pes_nome` from `tb_pessoal` where (`tb_pessoal`.`pes_id` = `rel_sacado_financeiro`.`pes_id`))
                  when (`rel_sacado_financeiro`.`empresas_id` is not null) then (select `tb_empresas`.`nome_razao` from `tb_empresas` where (`tb_empresas`.`id` = `rel_sacado_financeiro`.`empresas_id`))
                  when (`rel_sacado_financeiro`.`empresas_grupo_id` is not null) then (select `tb_empresas_grupo`.`razao` from `tb_empresas_grupo` where (`tb_empresas_grupo`.`id` = `rel_sacado_financeiro`.`empresas_grupo_id`)) end) AS `Name_exp_2`
                  from (((`tb_processo`
                  join `rel_processos_master_conta_financeiro` on((`rel_processos_master_conta_financeiro`.`pro_id` = `tb_processo`.`pro_id`)))
                  join `tb_financeiro` on((`tb_financeiro`.`pro_id` = `tb_processo`.`pro_id`)))
                  join `rel_sacado_financeiro` on((`rel_sacado_financeiro`.`tb_financeiro_fin_id` = `tb_financeiro`.`fin_id`)))
                  where (`rel_sacado_financeiro`.`tb_financeiro_fin_id` = `rel_processos_master_conta_financeiro`.`fin_id`)
                order by Name_exp_2";

        return $this->_db->fetchPairs($select);
    }
}