<?php

    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 17/12/15
     * Time: 23:53
     */
    class Legacy_Model_Dao_RlPermissaoPessoa extends App_Model_Dao_Abstract
    {
        protected $_name         = "rl_permissao_pessoa";
        protected $_primary      = array("id_pessoa", "id_servico", "id_grupo");


        public function getServicosByUsuarioByTime($idUsuario, $idTime)
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
                json_agg(srvmd.metanome) AS metadatas, json_agg(srvmd.valor) AS valor, rlpp.id_grupo, rlpp.id_pessoa, rlpp.dt_expiracao
            FROM tb_pai srv
            LEFT JOIN tb_servico_metadata srvmd ON srvmd.id_servico = srv.id
            LEFT JOIN "rl_permissao_pessoa" AS "rlpp"
                ON srv.id = rlpp.id_servico
                AND rlpp.id_pessoa = ?
                AND rlpp.id_grupo = ?

            GROUP by srv.id, srv.descricao, srv.nome, srv.id_pai, srv.visivel, depth, srv.path, srv.fluxo, srv.rota, srv.ordem, srv.ordem_array, srv.dtype, srv.metanome, srv.id_grupo, srv.id_tib, rlpp.id_grupo, rlpp.id_pessoa, rlpp.dt_expiracao
            ORDER by srv.path
QUERY;

            $stmt = $db->query($query, array($idUsuario, $idTime));
            $rowset = $stmt->fetchAll();

            return $rowset;
        }


        /**
         * deve salvar apenas os servicos novos e deletar os que não vieram na array
         * E não se deve mexer nos modulos corretos
         * @param $idTime
         * @param $modulos
         * @throws Zend_Db_Select_Exception
         */
        public function salvarPermissao($idUsuario, $idTime, $servicos, $dtExpiracao)
        {

            // remove vazios
            $servicos = array_filter($servicos);

            // remove todos os servicos não selecionados
            $this->delete(
                array('id_grupo = ?' => $idTime,
                    'id_servico not in (?)' => $servicos,
                    'id_pessoa = ?' => $idUsuario
                )
            );

            // pega os servicos já salvos para atualizar a data
            $select = $this
                ->select()
                ->from($this, array('*'))
                ->where('id_servico in (?)', $servicos)
                ->where('id_pessoa = ?', $idUsuario)
                ->where('id_grupo = ?', $idTime);

            $rowset = $this->FetchAll($select);

            foreach ($rowset as $row) {
                $data = new Zend_Date($dtExpiracao[$row->id_servico], 'dd/MM/yyyy');

                if ($row->dt_expiracao != $data->toString('yyyy-MM-dd')) {
                    $row->dt_expiracao = $data->toString('yyyy-MM-dd');
                    $row->save();
                }
            }

            // pega os servicos já salvos
            $select = $this
                ->select()
                ->from($this, array('id', 'id_servico'))
                ->where('id_servico in (?)', $servicos)
                ->where('id_pessoa = ?', $idUsuario)
                ->where('id_grupo = ?', $idTime);

            $rowset = $this->_db->fetchPairs($select);

            // remove da lista os servicos salvos em banco
            $arrayServicosNovos = array_diff($servicos, $rowset);

            // salva cada 1 dos servicos que não estão ainda em banco
            foreach ($arrayServicosNovos as $servico) {

                $data = new Zend_Date($dtExpiracao[$servico], 'dd/MM/yyyy');

                $row = $this->createRow();
                $row->id = new Zend_Db_Expr('uuid_generate_v4()');
                $row->id_grupo = $idTime;
                $row->id_pessoa = $idUsuario;
                $row->dt_expiracao = $data->toString('yyyy-MM-dd');
                $row->id_servico = $servico;
                $row->save();
            }
        }




    }