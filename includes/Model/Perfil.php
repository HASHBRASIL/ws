<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 28/11/15
 * Time: 14:49
 */
class Perfil extends Base
{

    /**
     * @param $grupos
     * @param $perfis
     * @param null $offset
     * @return array
     */
    function getPessoasByGrupoByPerfil($grupo, $perfis, $classificacao, $offset = 0, $compacto = false)
    {
    	//desconsiderando classificação de "perfil"
    	$arClas = explode(',', $classificacao);
    	foreach ( $arClas as $k => $v){
    		if ( $v == 'ENTIDADE' || $v == 'PF' ){
    			unset( $arClas[$k]);
    			
    		}
    	}

    	$newClassificacao = implode(',', $arClas);    	
        if (!$offset) {
            $offset = 0;
        }
        
        $stmt = $this->dbh->prepare(
                "select 
				pes.id, 
				pes.nome, 
				json_agg(tinf.nome) as nomes, 
				json_agg(tinf.metanome) as metanomes, 
				json_agg(tinf.id) as tpinfo_id,
				json_agg(inf.valor) as valores,
				json_agg(tinf.ordem) as ordem
                from rl_grupo_informacao rgi
                inner join tb_pessoa            pes   on (rgi.id_pessoa = pes.id)
                inner join tb_informacao        inf   on (rgi.id_info = inf.id)
                inner join tp_informacao        tinf  on (inf.id_tinfo = tinf.id)
                inner join rl_perfil_informacao rpi   on (tinf.id = rpi.id_informacao)
                inner join tb_perfil            prf   on (rpi.id_perfil = prf.id)
                inner join (select * from tp_informacao_metadata where metanome = 'ws_ordemLista') tim on (tim.id_tpinfo = tinf.id) 
				where
                pes.id in (select id_pessoa from rl_vinculo_pessoa 
        			where 
        			id_classificacao in 
                	( 
						select	id 
    	    			from	tb_classificacao
        				where
        					metanome = ANY (string_to_array(:classificacao, ',') )
        			) and 
        			id_perfil in 
        			(
        				select id from tb_perfil where metanome = ANY ( string_to_array(:perfis , ',') )
        			) and
        			id_grupo	in 
                    (
                        WITH recursive gettimes AS 
                        (
                            SELECT g.* FROM   tb_grupo g WHERE  g.id = :grupoa
                            UNION  
                            ( 
                                WITH recursive getfilhos AS 
                                (
                                    SELECT g.* FROM   tb_grupo g JOIN   gettimes gi ON 
                                    (
                                        g.id = gi.id_pai
                                    )
                                    UNION 
                                    (
                                        WITH recursive getinscricoes AS 
                                        (
                                            SELECT g.* FROM   tb_grupo g 
                                            JOIN   rl_inscricao_grupo rig ON 
                                            (
                                                g.id = rig.id_publicacao
                                            )
                                            JOIN   getfilhos gg ON 
                                            (
                                                rig.id_inscricao = gg.id
                                            )
                                            UNION 
                                            (
                                                SELECT g.* FROM   tb_grupo g
                                                JOIN   getinscricoes gf ON 
                                                (
                                                    g.id = gf.id_pai
                                                )
                                            )
                                        ) SELECT * FROM getinscricoes
                                    )
                                ) SELECT * FROM   getfilhos
                            ) 
                        ) SELECT id from gettimes gp
                          WHERE (gp.id = :grupob OR EXISTS (SELECT 1 FROM   tb_grupo_metadata gm WHERE  id_grupo = gp.id AND gm.metanome = 'ws_infopublica'))
                    )
                )
                group by pes.nome, pes.id
                order by pes.nome
                limit 30 offset :offset"
        		); 

        $stmt->bindValue(':classificacao', 	$newClassificacao);
        $stmt->bindValue(':grupoa',			$grupo);
        $stmt->bindValue(':grupob',         $grupo);
        $stmt->bindValue(':perfis',			$perfis);
        $stmt->bindValue(":offset",			$offset);
        

        $stmt->execute();

        $rsPessoa = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rsPessoa;
    }

    //    /**
    //     * @param $grupos
    //     * @param $perfis
    //     * @param $idPessoa
    //     * @return array
    //     */
    //    function getPessoaByGruposByPerfilByPessoa($grupos, $perfis, $idPessoa)
    //    {
    //        $stmt = $this->dbh->prepare(
    //            "select p.* from tb_perfil p
    //            left join rl_perfil_informacao pi on pi.id_perfil = p.id
    //            left join rl_grupo_informacao gi on pi.id_informacao = gi.id_info
    //            left join tb_pessoa pessoa on pessoa.id = gi.id_pessoa
    //            where rlgi.id_grupo = ANY (  (string_to_array(:grupos, ','))::uuid[] )
    //            AND tbperfil.metanome = ANY (string_to_array(:perfis, ','))
    //            AND gi.id_pessoa = :idPessoa"
    //        );
    //
    //        $stmt->bindValue(':grupos', implode(',', $grupos));
    //        $stmt->bindValue(':perfis', $perfis);
    //        $stmt->bindValue(':idPessoa', $idPessoa);
    //
    //        $stmt->execute();
    //
    //        $rsPessoa = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //
    //        return $rsPessoa;
    //    }

    function getCamposLista($perfis) {
         $stmt = $this->dbh->prepare(
                "select tinf.metanome, tinf.nome,tim.valor
                from tb_perfil prf join rl_perfil_informacao rpi on (rpi.id_perfil = prf.id)
                join tp_informacao tinf on (rpi.id_informacao = tinf.id)
                join tp_informacao_metadata tim on (tim.id_tpinfo = tinf.id)
                where prf.metanome::text = ANY(string_to_array(:perfis,',')) and tim.metanome = 'ws_ordemLista' order by tim.valor"
                );

        $stmt->bindValue(':perfis',  $perfis);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getCamposByGridOrdem($gridordem) {
         $stmt = $this->dbh->prepare(
                "select tinf.metanome, tinf.nome,tim.valor
                from tp_informacao tinf
                join tp_informacao_metadata tim on (tim.id_tpinfo = tinf.id)
                where tinf.metanome::text = ANY(string_to_array(:gridordem,','))"
                );

        $stmt->bindValue(':gridordem',  $gridordem);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
