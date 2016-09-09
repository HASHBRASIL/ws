<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_Grupo extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_grupo";
	protected $_primary       = "id";

	protected $_rowClass = 'Config_Model_Vo_Grupo';

    public $searchFields = array(
        'pcpnome'     => 'g.nome',
        'pcptime'     => 'gpai.nome',
        'pcprep'   => 'p.nome'
    );

    public function listGruposAbaixo( $uuid )
    {
        $select = $this->_db->prepare(
                'WITH RECURSIVE grupos as (
                        SELECT * FROM tb_grupo g1 WHERE g1.id = ?
                        UNION ALL
                        select  g2.* from tb_grupo as g2 INNER JOIN grupos ON g2.id_pai = grupos.id
                )
                select * from grupos g');

        $select->bindParam( 1 , $uuid );
        $select->execute();
        return $select->fetchAll();
    }

    public function getGrupo($id)
    {
        $select = $this->_db->prepare('SELECT * FROM tb_grupo WHERE id = ? ');
        $select->bindParam(1, $id);

        $select->execute();
        return $select->fetch();
    }

    public function getGruposByIDPaiByMetanome( $idPai, $metanome )
    {
        $select = $this->_db->prepare( "with recursive getgrupos as (select * from {$this->_name} where id = :idPai union select g.* from tb_grupo g join getgrupos gg on (g.id_pai = gg.id)) select * from getgrupos where metanome = :metanome" );
        $select->bindParam( ':idPai', $idPai );
        $select->bindParam( ':metanome', $metanome );

        $select->execute();
        return $select->fetchAll();
    }

    public function getGrupoByMetanome( $metanome )
    {
        $select = $this->select()
                       ->from(array('g' => $this->_name))
                       ->where('g.metanome = ?',$metanome);
        return $this->fetchAll($select)->toArray();
    }


    public function getGrupoByIDPaiByCanal ($idPai, $canal)
    {
        $select = $this->select()
                       ->from(array('g' => $this->_name))
                       ->where('g.id_pai = ?',$idPai)
                       ->where('g.id_canal = ?',$canal);
        $ret = $this->fetchAll($select)->toArray();

        return $ret;
    }

    public function delGrupo($uuid)
    {
        $condicao = $this->getAdapter()->quoteInto ( 'id = ?', $uuid );

        return $this->delete($condicao);
    }
    public function listGruposOrfaos()
    {
        $select = $this->select()
                        ->from(array('g' => $this->_name))
                        ->where('g.id_pai is null');

        return $this->fetchAll($select)->toArray();
    }

    public function getGrupoByPaiByMetadado($idPai,$metanome,$valor)
    {

        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('g' => $this->_name))
                        ->join(array('tgm'=> 'tb_grupo_metadata'), 'g.id = tgm.id_grupo')
                        ->where('g.id_pai = ?',$idPai)
                        ->where('tgm.metanome = ? ', $metanome)
                        ->where('tgm.valor = ?', $valor);
        $ret = $this->fetchAll($select)->toArray();

        return $ret;
    }

    /**
     * Retorna o grupo pessoal associado a uma pessoa.
     *
     * @param string $uuid Id de tb_pessoa.
     * @return null|integer
     */
    public function getGrupoPessoalByPessoa($idPessoa)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['g' => $this->_name], 'g.id')
            ->join(['rgi' => 'rl_grupo_informacao'], 'g.id = rgi.id_grupo', [])
            ->where('rgi.id_pessoa = ?', $idPessoa);
//        echo $select; die;
        $dadosGrupo = $this->fetchOne($select)->toArray();
        if ($dadosGrupo) {
            return $dadosGrupo['id'];
        }

        return null;
    }

    /**
     *
     * @param type $idCriador
     * @return type
     * @todo Refatorar com self::getGrupoByRepresentacao()
     */
    public function getTimeByCriador($idCriador)
    {
        $select = $this->select()
            ->from($this->_name, 'id')
            ->where('id_criador = ?', $idCriador)
            ->where('id_representacao is NOT NULL');

        $dadosGrupo = $this->fetchOne($select)->toArray();

        if ($dadosGrupo) {
            return $dadosGrupo['id'];
        }

        return null;
    }

    public function getTimesPermissao($time) {
        $select = $this->_db->prepare( "select gs.id from
                        (select id,id_pai,id_representacao from
                        (with recursive gettimes as (
                        select id,id_pai,id_representacao from tb_grupo where id = :time1
                        union
                        select g.id,g.id_pai,g.id_representacao from tb_grupo g join gettimes gt on (gt.id_pai = g.id)
                        )select gs.id, gs.id_pai, gs.id_representacao from gettimes gs
                        join tb_grupo_metadata tgm on (gs.id = tgm.id_grupo)
                        where tgm.metanome = 'ws_infopublica'
                        ) cima
                        union
                        select id,id_pai,id_representacao from
                        (
                        with recursive getfilhos as (
                        select id,id_pai,id_representacao from tb_grupo where id = :time2
                        union
                        select g.id,g.id_pai,g.id_representacao from tb_grupo g join getfilhos gf on (gf.id = g.id_pai)
                        ) select id,id_pai,id_representacao from getfilhos
                        ) baixo) gs
                        where gs.id_representacao is not null" );
        $select->bindParam( ':time1', $time );
        $select->bindParam( ':time2', $time );
        $select->execute();
        return $select->fetchAll();
    }

    public function getGrupoByTime($idTime) {
        $select = $this->_db->prepare( "with recursive getgrupos as (
                                            select id,nome from tb_grupo where id = :idTime
                                            union
                                            select g.id,g.nome from tb_grupo g join getgrupos gg on gg.id = g.id_pai
                                        ) select gg.id,gg.nome as valor from getgrupos gg join tb_grupo_metadata tgm on gg.id = tgm.id_grupo where tgm.metanome = 'cms_alias'" );
        $select->bindParam( ':idTime', $idTime );
        $select->execute();
        return $select->fetchAll();
    }

    public function getGrupoByRepresentacao($idTime)
    {
        $select = $this->select()
            ->where('id_representacao = ?', $idTime);

        return $this->fetchAll($select)->toArray();
    }

    public function getTimeByGrupo($grp) {
        $select = $this->_db->prepare( "with recursive getgrupos as (
            select id,id_pai,id_representacao from {$this->_name} where id = :grp
            union
            select g.id,g.id_pai,g.id_representacao from tb_grupo g join getgrupos gg on (g.id = gg.id_pai) where gg.id_representacao is null
            ) select g.id from getgrupos g where g.id_representacao is not null" );
        $select->bindParam( ':grp', $grp );

        $select->execute();
        $ret = $select->fetchAll();
        //return $ret;
        return current($ret)['id'];
    }

    public function getGrupoByTimeEMetanome($idTime, $metanome)
    {
        $sql = <<<DML
SELECT tgf.*
  FROM tb_grupo tgf
    INNER JOIN tb_grupo tgp ON(tgf.id_pai = tgp.id)
  WHERE tgp.id_representacao = :idTime
    AND tgf.metanome = :metaNome
DML;

        $select = $this->_db->prepare($sql);
        $select->bindParam(':idTime', $idTime );
        $select->bindParam(':metaNome', $metanome);
        $select->execute();
        return $select->fetchAll();
    }

    public function getGrupoByCanal ($canal)
    {
        $select = $this->select()
                       ->from(array('g' => $this->_name))
                       ->where('g.id_canal = ?',$canal)
                       ->order(array('g.nome'));
        $ret = $this->fetchAll($select)->toArray();

        return $ret;
    }

    public function gridGrupoCReprByCanal($canal) {
        $select = $this->select()->setIntegrityCheck(false)
                        ->from(array('g' => $this->_name),array('g.id','g.nome'))
                        ->join(array('gpai' => $this->_name), 'gpai.id = g.id_pai',array('pcptime'=>'gpai.nome'))
                        ->join(array('p'=>'tb_pessoa'),'gpai.id_representacao = p.id',array('pcprep'=>'p.nome'))
                        ->join(array('c'=>'tb_canal'),'g.id_canal = c.id',array())
                        ->where('c.metanome = ?',$canal)
                        ->order(array('g.nome', 'gpai.nome'));

        return $select;
    }

    public function getGrupoGeralByCriador($idCriador)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['g' => $this->_name])
            ->join(['gpai' => $this->_name], 'gpai.id = g.id_pai', [])
            ->where('g.id_criador = ?', $idCriador)
            ->where('LOWER(g.nome) = ?', strtolower(Config_Model_Bo_Grupo::NOME_GRUPO_GERAL))
            ->where('gpai.metanome = ?', Config_Model_Bo_Grupo::META_GRUPO);
        return $this->fetchAll($select)->toArray();
    }


    public function getTimesImportados(){
        $select = $this->_db->prepare( "SELECT grtime.nome, grtime.dt_inclusao, ibestado.valor FROM tb_grupo AS grtime
                                        JOIN tb_grupo AS grfilho ON grfilho.id_pai = grtime.id
                                        JOIN tb_grupo_metadata AS grmeta ON grmeta.id_grupo = grfilho.id
                                        JOIN tb_itembiblioteca AS ibmaster ON CAST(ibmaster.id AS TEXT) = grmeta.valor
                                        JOIN tb_itembiblioteca AS ibestado ON ibestado.id_ib_pai = ibmaster.id
                                        join tp_itembiblioteca AS tibestado ON ibestado.id_tib = tibestado.id
                                        WHERE grtime.id_representacao IS NOT NULL
                                        AND grtime.id_pai = 'ecc82072-2cf3-11e6-a307-a3ed3250a164'
                                        AND grfilho.metanome = 'SITE'
                                        AND grmeta.metanome = 'cms_cracha'
                                        AND tibestado.metanome = 'ESTADO'
                                        ORDER BY grtime.dt_inclusao DESC LIMIT 30" );
        $select->execute();
        return $select->fetchAll();
    }

    public function criar_time(
        $time,
        $idRepresentacao,
        $idGrupoPai,
        $idUsuario,
        $idCanal,
        $publico,
        $metanome,
        $descricao,
        $cmsAlias,
        $idGrupoModelo,
        $itembiblioteca,
        $params
    ) {
        $query = <<<QUERY
SELECT criar_time(
    :time,
    :idRepresentacao,
    :idGrupoPai,
    :idUsuario,
    :idCanal,
    :publico,
    :metanome,
    :descricao,
    :cmsAlias,
    :idGrupoModelo,
    :itembiblioteca,
    :params
);
QUERY;

        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query($query, [
            'time' => $time,
            'idRepresentacao' => $idRepresentacao,
            'idGrupoPai' => $idGrupoPai,
            'idUsuario' => $idUsuario,
            'idCanal' => $idCanal,
            'publico' => $publico,
            'metanome' => $metanome,
            'descricao' => $descricao,
            'cmsAlias' => $cmsAlias,
            'idGrupoModelo' => $idGrupoModelo,
            'itembiblioteca' => $itembiblioteca,
            'params' => $params
        ])->fetchAll();
    }
}

