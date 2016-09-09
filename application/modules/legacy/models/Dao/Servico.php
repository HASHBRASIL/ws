<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 14/12/15
 * Time: 21:29
 */
class Legacy_Model_Dao_Servico extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_servico";
    protected $_primary       = "id";
//    protected $_namePairs	  = "bco_nome";

    protected $_rowClass = "Legacy_Model_Vo_Servico";

        public function getAllServices()
        {

            $db = Zend_Db_Table::getDefaultAdapter();

            $query = <<<QUERY

         WITH RECURSIVE tb_pai AS
        (
            SELECT id, descricao, nome, id_pai, visivel, 1 AS depth, array[coalesce(ordem,0)] as ordem_array, array[id] AS path, fluxo, rota, ordem, dtype, metanome, id_grupo, id_tib
            FROM tb_servico where id_pai IS NULL
        UNION
            SELECT sv.id, sv.descricao, sv.nome, sv.id_pai, sv.visivel, pai.depth + 1 AS depth, pai.ordem_array || coalesce(sv.ordem,0) as ordem_array, pai.path || sv.id, sv.fluxo, sv.rota, sv.ordem, sv.dtype, sv.metanome, sv.id_grupo, sv.id_tib
            FROM tb_servico sv JOIN tb_pai pai ON ( sv.id_pai = pai.id )
        )

            SELECT srv.*, array_to_json(srv.ordem_array) as ordem_array, array_to_json(srv.path) as path,
                json_agg(srvmd.metanome) AS metadatas, json_agg(srvmd.valor) AS valor
            FROM tb_pai srv
            LEFT JOIN tb_servico_metadata srvmd ON srvmd.id_servico = srv.id
            GROUP by srv.id, srv.descricao, srv.nome, srv.id_pai, srv.visivel, depth, srv.path, srv.fluxo, srv.rota, srv.ordem, srv.ordem_array, srv.dtype, srv.metanome, srv.id_grupo, srv.id_tib
            ORDER by srv.ordem_array;
QUERY;

            $stmt = $db->query($query);
            $rowset = $stmt->fetchAll();

            return $rowset;
        }
}

