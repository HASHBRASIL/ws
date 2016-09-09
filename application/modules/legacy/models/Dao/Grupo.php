<?php

    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 18/12/15
     * Time: 0:14
     */
    class Legacy_Model_Dao_Grupo extends App_Model_Dao_Abstract
    {
        protected $_name         = "tb_grupo";
        protected $_primary      = "id";


        public function getTimesId($idPessoa)
        {

            $db = Zend_Db_Table::getDefaultAdapter();

            $query = <<<QUERY

            WITH RECURSIVE gettimes as (
            SELECT * FROM tb_grupo WHERE id IN (SELECT DISTINCT id_grupo FROM rl_grupo_pessoa WHERE id_pessoa = (select id FROM tb_usuario WHERE id = ?))
            UNION
            SELECT g.* FROM tb_grupo g JOIN gettimes gg on (g.id = gg.id_pai) where gg.id_representacao is null
            ) SELECT * FROM gettimes WHERE (id_representacao IS NOT NULL AND id_pai IS NOT NULL) or (metanome = 'HASH') order by nome;
QUERY;

            $stmt = $db->query($query, array($idPessoa));

            $rowset = $stmt->fetchAll();

            return $rowset;
        }

        public function getGruposId($idPessoa, $idGrupoTime)
        {
            $identity = Zend_Auth::getInstance()->getIdentity();

            $db = Zend_Db_Table::getDefaultAdapter();

            $query = <<<QUERY


            with recursive getgrupos as (
            select * from tb_grupo where id = ?
            union
            select g.* from tb_grupo g join getgrupos gg on (g.id_pai = gg.id) where g.id_representacao is null
            ) select gg.* from getgrupos gg join rl_grupo_pessoa rgp on (gg.id = rgp.id_grupo) where rgp.id_pessoa = ? and gg.id_representacao is null order by nome;


QUERY;

            $stmt = $db->query($query, array($idGrupoTime, $idPessoa));

            $rowset = $stmt->fetchAll();

            return $rowset;
        }

        public function getGroupList($idTime)
        {

            $identity = Zend_Auth::getInstance()->getIdentity();

            $db = Zend_Db_Table::getDefaultAdapter();

            $query = <<<QUERY


            select distinct id from (with recursive gettimes as (
        select g.id from tb_grupo g join tb_grupo_metadata tgm on (g.id = tgm.id_grupo) where g.id = ? and tgm.metanome = 'erp_consultafilhos'
union
select g.id from tb_grupo g join gettimes gg on (g.id_pai = gg.id) join tb_grupo_metadata tgm on (g.id = tgm.id_grupo) where g.id_representacao is not null and tgm.metanome = 'erp_visivelmatriz'
) select id from gettimes union
select irmao.id from tb_grupo eu join tb_grupo irmao on (eu.id_pai = irmao.id_pai) join tb_grupo_metadata tgmirmao on (irmao.id = tgmirmao.id_grupo) join tb_grupo_metadata tgmeu on (eu.id = tgmeu.id_grupo) where tgmeu.metanome = 'erp_consultagrupo'
    and tgmirmao.metanome = 'erp_visivelcoligada' and eu.id = ? union select ? as id) foo


QUERY;

            $stmt = $db->query($query, array($idTime, $idTime, $idTime));

            $rowset = $stmt->fetchAll();

            return $rowset;
        }


        public function getGroups($idTimes)
        {
            $rowset = $this->fetchAll(array('id in (?)' => $idTimes));

            return $rowset;
        }

        public function getSiteByCriador($idCriador)
        {
            $db = Zend_Db_Table::getDefaultAdapter();

            $query = "with recursive site as (
                          select * from tb_grupo where id_criador = ? and id_representacao is not null
                          union all
                          select grupo.* from tb_grupo grupo
                          join site on grupo.id_pai = site.id
                      ) select site.* from site
                      join tb_grupo_metadata meta on site.id = meta.id_grupo
                      where meta.metanome = 'cms_alias'
                      and id_representacao is null";

            $stmt = $db->query($query, array($idCriador));

            $rowset = $stmt->fetchAll();

            return $rowset;
        }

        public function createCracha()
        {

        }

        public function getCrachaBySite($idSite)
        {
            $db = Zend_Db_Table::getDefaultAdapter();

            $query = "select meta.* from tb_grupo grupo
                      join tb_grupo_metadata meta on ( grupo.id = meta.id_grupo )
                      where grupo.id_pai = ( select id from tb_grupo where id_criador = ? and id_representacao is not null )
                      and grupo.metanome = 'SITE' and meta.metanome = 'cms_cracha'";

            $stmt = $db->query($query, array($idSite));

            $rowset = $stmt->fecthAll();

            return $rowset;
        }


        public function getGrupoByMetanome($metanome, $idCriador)
        {
            $db = Zend_Db_Table::getDefaultAdapter();

            $query = "SELECT * FROM tb_grupo WHERE id_criador = ? AND metanome = ?";
            $stmt = $db->query($query, array($idCriador, $metanome));
            $rowset = $stmt->fetchAll();

            return $rowset;
        }


        public function getTimeByCriador($idCriador)
        {
            $db = Zend_Db_Table::getDefaultAdapter();

            $query = "SELECT * FROM tb_grupo WHERE id_criador = ? AND id_representacao IS NOT NULL";
            $stmt = $db->query($query, array($idCriador));
            $rowset = $stmt->fetchAll();

            return $rowset;
        }

        public function getAlcadaAcima($time) {
            $db = Zend_Db_Table::getDefaultAdapter();

            $query = "with recursive getAcima as (
                        select id, id_representacao, id_pai from tb_grupo where id = ?
                        union
                        select g.id, g.id_representacao, g.id_pai from tb_grupo g join getAcima ga on ga.id_pai = g.id
                        ) select ga.id from getAcima ga join tb_grupo_metadata tgm on
                        ga.id = tgm.id_grupo where tgm.metanome = 'ws_visivelgrupo' and ga.id_representacao is not null";
            $stmt = $db->query($query, array($time));
            $rowset = $stmt->fetchAll();

            return $rowset;
        }

        public function getAlcadaAbaixo($time) {
            $db = Zend_Db_Table::getDefaultAdapter();

            $query = "with recursive getAbaixo as (
                        select id, id_representacao, id_pai from tb_grupo where id = ?
                        union
                        select g.id, g.id_representacao, g.id_pai from tb_grupo g join getAbaixo ga on ga.id = g.id_pai
                        ) select ga.id from getAbaixo ga join tb_grupo_metadata tgm on ga.id = tgm.id_grupo where ga.id_representacao is not null and tgm.metanome = 'ws_visivelmatriz'";
            $stmt = $db->query($query, array($time));
            $rowset = $stmt->fetchAll();

            return $rowset;
        }

        public function getAlcadaLado($time) {
            $db = Zend_Db_Table::getDefaultAdapter();

            $query = "select g.id
                    from tb_grupo g
                    join tb_grupo gfiltro on g.id_pai = gfiltro.id_pai
                    join tb_grupo_metadata tgm on g.id = tgm.id_grupo
                    where gfiltro.id = ?
                    and g.id_representacao is not null
                    and tgm.metanome = 'ws_visivelcoligada'";
            $stmt = $db->query($query, array($time));
            $rowset = $stmt->fetchAll();

            return $rowset;
        }


        public function getLicense()
        {
            return $this->select()->where('id_representacao is not null');
        }



    }