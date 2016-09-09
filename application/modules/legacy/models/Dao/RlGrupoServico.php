<?php

    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 19/12/15
     * Time: 10:27
     */
    class Legacy_Model_Dao_RlGrupoServico extends App_Model_Dao_Abstract
    {
        protected $_name         = "rl_grupo_servico";
        protected $_primary      = "id";

        public function getModulosByTime($idTime)
        {
            $select = $this->_db->select()
                ->from(array('s'=>'tb_servico'), array("rlgs.id_servico", "rlgs.id_grupo", "s.nome"))
                ->joinLeft(array('rlgs' => $this->_name), $this->_db->quoteInto('s.id = rlgs.id_servico and rlgs.id_grupo = ?', $idTime, 'string'), array(""))
                ->where('s.id_pai is null')
                ->group(array("rlgs.id_servico", "rlgs.id_grupo", "s.nome"));

            return $this->_db->fetchAll($select);
        }

        /**
         * deve salvar apenas os modulos novos e deletar os que não vieram na array
         * E não se deve mexer nos modulos corretos
         * @param $idTime
         * @param $modulos
         * @throws Zend_Db_Select_Exception
         */
        public function salvarModulos($idTime, $modulos)
        {
            // remove vazios
            $modulos = array_filter($modulos);

            // remove todos os servicos não selecionados
            $this->delete(
                array('id_grupo = ?' => $idTime,
                      'id_servico not in (?)' => $modulos
                )
            );

            // pega os servicos já salvos
            $select = $this
                ->select()
                ->from($this, array('id', 'id_servico'))
                ->where('id_servico in (?)', $modulos)
                ->where('id_grupo = ?', $idTime);

            $rowset = $this->_db->fetchPairs($select);

            // remove da lista os servicos salvos em banco
            $arrayModulosNovos = array_diff($modulos, $rowset);

            // salva cada 1 dos servicos que não estão ainda em banco
            foreach ($arrayModulosNovos as $modulo) {
                $data = array(
                    'id' => new Zend_Db_Expr('uuid_generate_v4()'),
                    'id_grupo' => $idTime,
                    'id_servico' => $modulo
                );

                $row = $this->createRow($data);
                $row->save();
            }
        }

    }