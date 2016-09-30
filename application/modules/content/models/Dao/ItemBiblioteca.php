<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Content_Model_Dao_ItemBiblioteca extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_itembiblioteca";
    protected $_primary       = "id";

    protected $_rowClass = 'Content_Model_Vo_ItemBiblioteca';

    public $searchFields = array(
        'arquivo' => "ib3.valor",
        'titulo' => 'ib2.valor',
        'nome_grupo' => 'g.nome',
        'dt_publicacao' => 'ib4.valor',
        'status' => 'ib5.valor',
        'ocr' => 'ib6.valor'
    );




    public function getItemBiblioteca ($time) {

        $db = Zend_Db_Table::getDefaultAdapter();

        $query = <<<QUERY
        SELECT itens_filho.id_ib_pai, json_object( array_agg(meta.valor), array_agg(itens_filho.valor) ) conteudo, itens.dt_criacao, tipo_master.metanome tipo
        FROM rl_grupo_item relacao
        JOIN (
        WITH RECURSIVE grupos AS (
        SELECT * FROM tb_grupo WHERE id_pai = ? AND metanome = 'SITE'
        UNION
        SELECT filhos.* FROM grupos pai
        JOIN tb_grupo filhos ON filhos.id_pai = pai.id

        )SELECT * FROM grupos ) AS grupos ON relacao.id_grupo = grupos.id
        JOIN tb_itembiblioteca itens ON relacao.id_item = itens.id
        JOIN tb_itembiblioteca itens_filho ON itens.id = itens_filho.id_ib_pai
        JOIN tp_itembiblioteca tipo_master ON itens.id_tib = tipo_master.id
        JOIN tp_itembiblioteca tipo_filho ON itens_filho.id_tib = tipo_filho.id
        JOIN ( SELECT * FROM tp_itembiblioteca_metadata WHERE metanome = 'cms_twig') meta ON tipo_filho.id = meta.id_tib
        GROUP BY itens_filho.id_ib_pai, itens.dt_criacao, tipo_master.metanome
        ORDER BY itens.dt_criacao DESC
        LIMIT 4
QUERY;

        $db = $db->query($query, array($time) );
        $rowset = $db->fetchAll();

        return $rowset;
    }

    public function getItemBibliotecaGrid($id_tib, $id_grupo, $options = null) {

        $select2 = $this ->select()->setIntegrityCheck(false)
                         ->from(array('tib'      => 'tp_itembiblioteca'), array('tib.metanome'))->distinct()
                         ->joinLeft(array('meta' => 'tp_itembiblioteca_metadata'), 'tib.id = meta.id_tib', array())
                         ->where('tib.id_tib_pai = ?', $id_tib)
                         ->order(array('tib.metanome'));

        $rows = $this->fetchAll($select2)->toArray();

        $fields = array();

        foreach ($rows as $row) {
            $fields[] = $row['metanome'];
        }

        $camposVarchar = implode($fields, ' varchar, ');

        $campos = new Zend_Db_Expr(implode(array_merge(array("id"), $fields), ', '));

        $db = Zend_Db_Table::getDefaultAdapter();

        $select1 = $this->select()->setIntegrityCheck(false);

        $select1->from(array('ib' => 'tb_itembiblioteca'), array('ib.id_ib_pai', 'tib.metanome',  new zend_db_expr('coalesce(ibcombo.valor,ib.valor)')))
                ->join(array('tib' => 'tp_itembiblioteca'), 'ib.id_tib = tib.id', array())
                ->join(array('rlgi' => 'rl_grupo_item'), 'rlgi.id_item = ib.id_ib_pai', array())
                ->joinLeft(array('tim' => new Zend_Db_Expr("(select * from tp_itembiblioteca_metadata where metanome = 'ws_tib')")),'tib.id = tim.id_tib', array())
                ->joinLeft(array('timcombo' => new Zend_Db_Expr("(select * from tp_itembiblioteca_metadata where metanome = 'ws_comboform')")),'tib.id = timcombo.id_tib', array())
                ->joinLeft(array('tibcampo' => 'tp_itembiblioteca'),'tibcampo.id_tib_pai::varchar = tim.valor::varchar and tibcampo.metanome = timcombo.valor',array())
                ->joinLeft(array('ibcombo' => 'tb_itembiblioteca'),'ibcombo.id_ib_pai::varchar = ib.valor::varchar and ibcombo.id_tib = tibcampo.id', array())
                ->where('tib.id_tib_pai = ?', $id_tib)
                ->where('rlgi.id_grupo = ?', $id_grupo)
                ->order(array('ib.id_ib_pai', 'tib.metanome'));

        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('subselect' => new zend_db_expr("( SELECT * FROM crosstab( $$ $select1 $$, $$ $select2 $$ ) AS ( id uuid, $camposVarchar varchar ) )")),
        $campos );

        return $select;
    }

     public function getIbByTibAndGrupo($idTib, $idGrupo) {

        $stmt = $this->_db->prepare("SELECT ibpai.id, ibfilhos.valor  FROM tb_itembiblioteca AS ibpai
                            JOIN tb_itembiblioteca AS ibfilhos ON ibfilhos.id_ib_pai = ibpai.id
                            JOIN tp_itembiblioteca AS tip ON tip.id = ibfilhos.id_tib
                            JOIN rl_grupo_item AS rlgi on rlgi.id_item = ibpai.id
                            WHERE ibpai.id_tib = :tib
                            AND rlgi.id_grupo = :grupo
                            AND tip.metanome = 'titulo'");

        $stmt->bindValue(':tib', $idTib);
        $stmt->bindValue(':grupo',$idGrupo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getAllItensByTibByOrdem($tib,$ordem) {
        $ret = array();
        $stmt = $this->_db->prepare("select fil.id_ib_pai, fil.valor,tpfil.metanome as tpmeta, ord.valor as ordem
                        from tb_itembiblioteca fil join tb_itembiblioteca pai on (fil.id_ib_pai = pai.id)
                        join tp_itembiblioteca tpfil on (fil.id_tib = tpfil.id)
                        join tb_itembiblioteca ord on (ord.id_ib_pai = pai.id)
                        join tp_itembiblioteca tpord on (ord.id_tib = tpord.id)
                        where pai.id_tib = :tib
                        and tpord.metanome = :ordem
                        order by ord.valor");

        $stmt->bindValue(':tib', $tib);
        $stmt->bindValue(':ordem',$ordem);
        $stmt->execute();
        $rsItem = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rsItem as $linha) {
            $ret[$linha['id_ib_pai']][$linha['tpmeta']] = $linha['valor'];
        }

        return $ret;
    }

    public function getAllIbByTSE($metanome, $cargo, $estados, $limit, $offset) {
        $ret = array();
        $this->_db->getProfiler()->setEnabled(true);

        $stmt = $this->_db->prepare("SELECT ibfilho.id_ib_pai as id FROM tp_itembiblioteca AS tibmaster
                                    INNER JOIN tb_itembiblioteca AS ibmaster ON ibmaster.id_tib = tibmaster.id
                                    INNER JOIN tb_itembiblioteca AS ibfilho ON ibfilho.id_ib_pai = ibmaster.id
                                    INNER JOIN tb_itembiblioteca AS ibfilhocargo ON ibfilhocargo.id_ib_pai = ibmaster.id
                                    INNER JOIN tb_itembiblioteca AS ibfilhoestado ON ibfilhoestado.id_ib_pai = ibmaster.id
                                    INNER JOIN tp_itembiblioteca AS tibcargo ON tibcargo.id = ibfilho.id_tib
                                    WHERE
                                        tibmaster.metanome = :metanome
                                        AND tibcargo.metanome = 'cargo'
                                        AND ibfilhocargo.idx_valor @@ to_tsquery(:cargo)
                                        AND ibfilhoestado.idx_valor @@ to_tsquery(:estados)
                                    LIMIT :limit
                                    OFFSET :offset
                                    ");

        $stmt->bindValue(':metanome', $metanome);
        $stmt->bindValue(':cargo',$cargo);
        $stmt->bindValue(':estados',$estados);
        $stmt->bindValue(':limit',$limit);
        $stmt->bindValue(':offset',$offset);
        $stmt->execute();
        Zend_Debug::dump($this->_db->getProfiler()->getLastQueryProfile()->getQuery());
        Zend_Debug::dump($this->_db->getProfiler()->getLastQueryProfile()->getQueryParams());
        $this->_db->getProfiler()->setEnabled(false);
//        x($stmt->debugDumpParams());
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkIbValor($idTib, $valor)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $query = <<<QUERY
        SELECT valor FROM tb_itembiblioteca WHERE id_tib = ? AND valor = ? limit 1
QUERY;

        $db = $db->query($query, array($idTib, $valor) );
        $rowset = $db->fetchAll();

        return $rowset;
    }


    public function getCamposByTipo ($tipo) {

        $db    = Zend_Db_Table::getDefaultAdapter();

        $query = <<<QUERY
        SELECT * FROM tp_itembiblioteca where id_tib_pai = ?
QUERY;

        $db = $db->query($query, array($tipo));
        $rowset = $db->fechtAll();

        return $rowset;
    }

    public function create($data) {
        $rowPai  = $this->createRow();
        if(isset($data['id'])) {

        } else {

        }
        $uuidPai = UUID::v4();
        $rowPai->id         = $uuidPai;
        $rowPai->dt_criacao = new zend_db_expr('now()');
        $rowPai->id_criador = $data['config']['pessoa'];
        $rowPai->id_tib     = $data['config']['id_tib'];
        $rowPai->id_time    = $data['config']['time'];
        $rowPai->save();
    }

    public function addRelGrupoItem($id_grupo, $id_item)
    {
//        x($id_item, false); //53546938-5179-4a75-d64a-3365396b4e37
        $qry = $this->_db->prepare('INSERT INTO rl_grupo_item VALUES (uuid_generate_v1mc(), :id_grupo, :id_item)');

        $qry->bindParam( ':id_grupo', $id_grupo );
        $qry->bindParam( ':id_item', $id_item );

        return $qry->execute();
    }

    public function delRelGrupoItem($id_grupo, $id_item)
    {
//        x($id_item, false); //53546938-5179-4a75-d64a-3365396b4e37

        $qry = $this->_db->prepare('DELETE FROM rl_grupo_item WHERE id_item = :id_item AND id_grupo = :id_grupo');
        $qry->bindParam( ':id_grupo', $id_grupo );
        $qry->bindParam( ':id_item', $id_item );

        return $qry->execute();
    }


    public function deletar($uuid) {

        return $this->_db->delete($this->_name, array('id = ?', $uuid));

    }

    function getIbById($id){

        $stmt = $this->_db->prepare("SELECT * FROM tb_itembiblioteca WHERE id = :id");
        $stmt->bindValue('id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getFilhosByIdPai($id){

        $stmt = $this->_db->prepare("SELECT * FROM tb_itembiblioteca WHERE id = :id");
        $stmt->bindValue('id', $id);
        $stmt->execute();

        $rsItemBibliotecaMaster = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $this->_db->prepare("SELECT * FROM tb_itembiblioteca WHERE id_ib_pai = :id_ib_pai");
        $stmt2->bindValue('id_ib_pai', $rsItemBibliotecaMaster[0]['id']);
        $stmt2->execute();

        $rsItemBibliotecaFilhos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return $rsItemBibliotecaFilhos;
    }

    public function getAllByTib($tib,$ordem) {
        $ret = array();
        $stmt = $this->db->prepare("select fil.id_ib_pai, fil.valor,tpfil.metanome as tpmeta, ord.valor as ordem
                        from tb_itembiblioteca fil join tb_itembiblioteca pai on (fil.id_ib_pai = pai.id)
                        join tp_itembiblioteca tpfil on (fil.id_tib = tpfil.id)
                        join tb_itembiblioteca ord on (ord.id_ib_pai = pai.id)
                        join tp_itembiblioteca tpord on (ord.id_tib = tpord.id)
                        where pai.id_tib = :tib
                        and tpord.metanome = :ordem
                        order by ord.valor");
            $stmt->bindValue(':tib', $tib);
        $stmt->bindValue(':ordem',$ordem);
            $stmt->execute();
            $rsItem = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rsItem as $linha) {
            $ret[$linha['id_ib_pai']][$linha['tpmeta']] = $linha['valor'];
        }
        //new dBug($ret);

        return $ret;
    }

    /*
    public function save($idTib, $idIb){


        $daoTpItemBiblioteca = new Content_Model_Dao_TpItemBiblioteca();

        // $this->_sequence = false;
        // para gerar UUID

        if ( isset($idIb) ) {
            $rsDadossalvosanteriormente = $this->fetchAll(array('id_ib_pai = ?' => $idIb));

            foreach($rsDadossalvosanteriormente as $row) {
                $dadosparaupdate[$row['id_tib']] = $row;
            }
        }

        $rowsTibsreceitadebolo = $daoTpItemBiblioteca->fetchAll(array('id_tib_pai = ?' => $idTib));

        foreach ($rowsTibsreceitadebolo => $dadosparasalvar) {

            if (!$dadosparaupdate['id_tib']){
                $row = $this->createRow();
                $row->id = uuid;
            } else {
                $row = $dadosparaupdate[$dadosparasalvar['id_tib']]
            }

            $row->valor = $data[$dadosparasalvar['id'] . '_' . $dadosparasalvar['METANOME']];
            $row->seilaaoqtemmaisdecamposnesselugar = $data['xpto'];

            //$row->delete();
            $row->save();


        }
    }
    */

    public function getIbByPaiByTib($idpai,$idtib){
        $select = $this->select()
                ->from(array($this->_name))
                ->where('id_ib_pai = ?',$idpai)
                ->where('id_tib = ?',$idtib);
        $rowset = $this->fetchAll($select);
        return $rowset;
    }

    public function getTpItembiblioteca($id = null, $metanome = null, $idTibPai = false)
    {
        $select2 = $this ->select()->setIntegrityCheck(false)
                         ->from(array('tib' => 'tp_itembiblioteca'), array('*'))
                         ->limit(1);

        if(!is_null($metanome)){
            $select2->where('tib.metanome = ?', $metanome);
        }else if(!is_null($id)){
            $select2->where('tib.id = ?', $id);
        }
        if(!empty($idTibPai)){
            $select2->where('tib.id_tib_pai = ?', $idTibPai);
        }

        return $this->fetchRow($select2);
    }

    public function getIbByRlGrupoItem($idGrupo) {
        $qry = $this->_db->prepare('SELECT * FROM rl_grupo_item WHERE id_grupo = :id_grupo');

        $qry->bindParam( ':id_grupo', $idGrupo );
        $qry->execute();
        return $qry->fetchAll();
    }

//    public function listItemBiblioteca($id_tib, $id_grupo) {
//
//        if ($options !== null) {
//            // regras extras especificas - só mexa se souber o que está fazendo
//
//            var_dump($options['id_agrupador_financeiro']);
//
//            if ($options['id_agrupador_financeiro']) {
//
//                $select1->join(array('rlfinanceiroib' => 'fin_rl_agrupador_financeiro_ib'), 'rlfinanceiroib.id_itembiblioteca = ib.id', array())
//                    ->where('rlfinanceiroib.id_agrupador_financeiro = ?', $options['id_agrupador_financeiro']);
//
////                var_dump($select1->__toString());
////                exit;
//            }
//        }
//
//        $select = $this->select()->setIntegrityCheck(false);
//        $select->from(array('subselect' => new zend_db_expr("( SELECT * FROM crosstab( $$ $select1 $$, $$ $select2 $$ ) AS ( id uuid, $camposVarchar varchar ) )")),
//        $campos );
//
//        return $select;
//    }


    public function listItemBiblioteca($id_tib, $id_grupo) {

        $select2 = $this ->select()->setIntegrityCheck(false)
                         ->from(array('tib'      => 'tp_itembiblioteca'), array('tib.metanome'))->distinct()
                         ->joinLeft(array('meta' => 'tp_itembiblioteca_metadata'), 'tib.id = meta.id_tib', array())
                         ->where('tib.id_tib_pai = ?', $id_tib)
                         ->order(array('tib.metanome'));

        $rows = $this->fetchAll($select2)->toArray();

        $fields = array();

        foreach ($rows as $row) {
            $fields[] = $row['metanome'];
        }

        $camposVarchar = implode($fields, ' varchar, ');

        $campos = new Zend_Db_Expr(implode(array_merge(array("id"), $fields), ', '));

        $db = Zend_Db_Table::getDefaultAdapter();

        $select1 = $this->select()->setIntegrityCheck(false);

        $select1->from(array('ib' => 'tb_itembiblioteca'), array('ib.id_ib_pai', 'tib.metanome',  new zend_db_expr('coalesce(ibcombo.valor,ib.valor)')))
                ->join(array('tib' => 'tp_itembiblioteca'), 'ib.id_tib = tib.id', array())
                ->join(array('rlgi' => 'rl_grupo_item'), 'rlgi.id_item = ib.id_ib_pai', array())
                ->joinLeft(array('tim' => new Zend_Db_Expr("(select * from tp_itembiblioteca_metadata where metanome = 'ws_tib')")),'tib.id = tim.id_tib', array())
                ->joinLeft(array('timcombo' => new Zend_Db_Expr("(select * from tp_itembiblioteca_metadata where metanome = 'ws_comboform')")),'tib.id = timcombo.id_tib', array())
                ->joinLeft(array('tibcampo' => 'tp_itembiblioteca'),'tibcampo.id_tib_pai::varchar = tim.valor::varchar and tibcampo.metanome = timcombo.valor',array())
                ->joinLeft(array('ibcombo' => 'tb_itembiblioteca'),'ibcombo.id_ib_pai::varchar = ib.valor::varchar and ibcombo.id_tib = tibcampo.id', array())
                ->where('tib.id_tib_pai = ?', $id_tib)
                ->where('rlgi.id_grupo = ?', $id_grupo)
                ->order(array('ib.id_ib_pai', 'tib.metanome'));

        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('subselect' => new zend_db_expr("( SELECT * FROM crosstab( $$ $select1 $$, $$ $select2 $$ ) AS ( id uuid, $camposVarchar varchar ) )")),
        $campos );

        return $this->fetchAll($select)->toArray();
    }

    public function getValorByCriadorEMetanome($idcriador, $metanome, $metanomepai)
    {
        $query = <<<DML
SELECT ibfilha.*
  FROM tb_itembiblioteca AS ibpai
    INNER JOIN tp_itembiblioteca AS tibpai ON (tibpai.id = ibpai.id_tib)
    INNER JOIN tb_itembiblioteca AS ibfilha ON (ibfilha.id_ib_pai = ibpai.id)
    INNER JOIN tp_itembiblioteca AS tibfilha ON (tibfilha.id = ibfilha.id_tib)

  WHERE ibpai.id_criador = :idcriador
    AND tibfilha.metanome = :metanome
    AND tibpai.metanome = :metanomepai

DML;
        $stmt = $this->_db->prepare($query);
        $stmt->bindValue(':idcriador', $idcriador);
        $stmt->bindValue(':metanome', $metanome);
        $stmt->bindValue(':metanomepai', $metanomepai);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getValoresFilhosNomeados($idIbPai)
    {
        $sql = <<<DML
SELECT tib.valor,
       tip.metanome,
       tip.id
  FROM tb_itembiblioteca tib
    INNER JOIN tp_itembiblioteca tip ON (tib.id_tib = tip.id)
  WHERE tib.id_ib_pai = :idIbPai
DML;
        $stmt = $this->_db->prepare($sql);
        $stmt->bindValue(':idIbPai', $idIbPai);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProximoCandidatoSemPar() {
        $query = <<<DML
select ib.id, ibcidade.valor as cidade, ibestado.valor as uf, ibcolig.valor as colig, ibpart.valor as part, ibnome.valor as nome
from (select ib.id, ib.id_tib from tb_itembiblioteca ib join tp_itembiblioteca tib on ib.id_tib = tib.id
join rl_grupo_item rgi on rgi.id_item = ib.id where tib.metanome = 'TPINGCANDTSE' and rgi.id_grupo = '363a27b3-95e2-4e46-ea44-f4be5070dcb6') ib
join tb_itembiblioteca ibcidade on ibcidade.id_ib_pai = ib.id
join tp_itembiblioteca tibcidade on ibcidade.id_tib = tibcidade.id and tibcidade.id_tib_pai = ib.id_tib
join tb_itembiblioteca ibnome on ibnome.id_ib_pai = ib.id
join tp_itembiblioteca tibnome on ibnome.id_tib = tibnome.id and tibnome.id_tib_pai = ib.id_tib
join tb_itembiblioteca ibestado on ibestado.id_ib_pai = ib.id
join tp_itembiblioteca tibestado on ibestado.id_tib = tibestado.id and tibestado.id_tib_pai = ib.id_tib
join tb_itembiblioteca ibcolig on ibcolig.id_ib_pai = ib.id
join tp_itembiblioteca tibcolig on ibcolig.id_tib = tibcolig.id and tibcolig.id_tib_pai = ib.id_tib
join tb_itembiblioteca ibcargo on ibcargo.id_ib_pai = ib.id
join tp_itembiblioteca tibcargo on ibcargo.id_tib = tibcargo.id and tibcargo.id_tib_pai = ib.id_tib
join tb_itembiblioteca ibpart on ibpart.id_ib_pai = ib.id
join tp_itembiblioteca tibpart on ibpart.id_tib = tibpart.id and tibpart.id_tib_pai = ib.id_tib
where tibcidade.metanome = 'cidade'
and tibestado.metanome = 'uf'
and tibcargo.metanome = 'cargo'
and tibcolig.metanome = 'coligacaoNome'
and tibpart.metanome = 'partidoSigla'
and tibnome.metanome = 'nomeGuerra'
and ibcargo.valor = 'PREFEITO'
and ibcolig.valor <> 'PARTIDO ISOLADO'
and not exists (select 1 from tb_itembiblioteca ibex join tp_itembiblioteca tib on ibex.id_tib = tib.id
join tb_itembiblioteca ibpar on ibpar.id_ib_pai = ibex.id
join tp_itembiblioteca tibpar on ibpar.id_tib = tibpar.id and tibpar.id_tib_pai = tib.id
where tib.metanome = 'TPINGCANDTSE'
and tibpar.metanome = 'parCandidato'
and ibex.id = ib.id) limit 1
DML;
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getCandidadoPorColig($cidade,$estado,$colig,$cargo) {
        $query = <<<DML
select ib.id, ibnome.valor as nome, ibpart.valor as partido
from tb_itembiblioteca ib
join tp_itembiblioteca tib on ib.id_tib = tib.id
join tb_itembiblioteca ibcidade on ibcidade.id_ib_pai = ib.id
join tp_itembiblioteca tibcidade on ibcidade.id_tib = tibcidade.id and tibcidade.id_tib_pai = tib.id
join tb_itembiblioteca ibuf on ibuf.id_ib_pai = ib.id
join tp_itembiblioteca tibuf on ibuf.id_tib = tibuf.id and tibuf.id_tib_pai = tib.id
join tb_itembiblioteca ibcolig on ibcolig.id_ib_pai = ib.id
join tp_itembiblioteca tibcolig on ibcolig.id_tib = tibcolig.id and tibcolig.id_tib_pai = tib.id
join tb_itembiblioteca ibcargo on ibcargo.id_ib_pai = ib.id
join tp_itembiblioteca tibcargo on ibcargo.id_tib = tibcargo.id and tibcargo.id_tib_pai = tib.id
join tb_itembiblioteca ibnome on ibnome.id_ib_pai = ib.id
join tp_itembiblioteca tibnome on ibnome.id_tib = tibnome.id and tibnome.id_tib_pai = tib.id
join tb_itembiblioteca ibpart on ibpart.id_ib_pai = ib.id
join tp_itembiblioteca tibpart on ibpart.id_tib = tibpart.id and tibpart.id_tib_pai = tib.id
join rl_grupo_item rgi on rgi.id_item = ib.id
where tib.metanome = 'TPINGCANDTSE'
and tibcidade.metanome = 'cidade'
and tibuf.metanome = 'uf'
and tibcolig.metanome = 'coligacaoNome'
and tibcargo.metanome = 'cargo'
and tibnome.metanome = 'nomeGuerra'
and tibpart.metanome = 'partidoSigla'
and ibcidade.idx_valor @@ plainto_tsquery(?)
and ibuf.idx_valor @@ plainto_tsquery(?)
and ibcolig.idx_valor @@ plainto_tsquery(?)
and ibcargo.valor = ?
and rgi.id_grupo = '363a27b3-95e2-4e46-ea44-f4be5070dcb6'
DML;
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(1, $cidade);
        $stmt->bindParam(2, $estado);
        $stmt->bindParam(3, $colig);
        $stmt->bindParam(4, $cargo);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function geratmpemailinfocand() {

        $query = <<<DML
select ib.id, ibnome.valor as nome, ibemail.valor as email, ibnumero.valor as numero, unaccent_string(lower(replace(ibnome.valor,' ',''))) || ibnumero.valor as nometime
from (select ib.id, ib.id_tib as tib from tb_itembiblioteca ib join tp_itembiblioteca tib on ib.id_tib = tib.id
    join rl_grupo_item rgi on rgi.id_item = ib.id
    left join tmp_email_campanha tec on ib.id = tec.idcand
    where tib.metanome = 'TPINGCANDTSE'
    and rgi.id_grupo = '363a27b3-95e2-4e46-ea44-f4be5070dcb6'
    and tec.idcand is null limit 1) ib
join tb_itembiblioteca ibnome on ibnome.id_ib_pai = ib.id
join tp_itembiblioteca tibnome on ibnome.id_tib = tibnome.id and tibnome.id_tib_pai = ib.tib
join tb_itembiblioteca ibemail on ibemail.id_ib_pai = ib.id
join tp_itembiblioteca tibemail on ibemail.id_tib = tibemail.id and tibemail.id_tib_pai = ib.tib
join tb_itembiblioteca ibnumero on ibnumero.id_ib_pai = ib.id
join tp_itembiblioteca tibnumero on ibnumero.id_tib = tibnumero.id and tibnumero.id_tib_pai = ib.tib
where tibnome.metanome = 'nomeGuerra'
and tibemail.metanome = 'email'
and tibnumero.metanome = 'numero'
DML;
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function inseretmpemailcand($idcand,$nomecand,$emailcand,$usercand,$txtemail,$status) {
        $query = "insert into tmp_email_campanha (idcand,nomecand,emailcand,usercand,txtemail,statusemail) values (:idcand,:nomecand,:emailcand,:usercand,:txtemail,:status)";

        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(':idcand', $idcand);
        $stmt->bindParam(':nomecand', $nomecand);
        $stmt->bindParam(':emailcand', $emailcand);
        $stmt->bindParam(':usercand', $usercand);
        $stmt->bindParam(':txtemail', $txtemail);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
    }

    public function atualizastatuscand($idcand,$status){
        $query = "update tmp_email_campanha set statusemail = :status where idcand = :idcand";
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(':idcand', $idcand);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
    }

    public function geratmppessoainfocand($uf) {

        $query = <<<DML
select ib.id,tibf.metanome,ibf.valor
from (select ib.id, ib.id_tib
from tb_itembiblioteca ib
join tp_itembiblioteca tib on ib.id_tib = tib.id
join rl_grupo_item rgi on rgi.id_item = ib.id
join tb_itembiblioteca ibuf on ibuf.id_ib_pai = ib.id
join tp_itembiblioteca tibuf on ibuf.id_tib = tibuf.id and tibuf.id_tib_pai = tib.id
where tib.metanome = 'TPINGCANDTSE'
and tibuf.metanome = 'uf'
and ibuf.idx_valor @@ :uf
and rgi.id_grupo = '363a27b3-95e2-4e46-ea44-f4be5070dcb6'
and not exists (select 1 from tb_itembiblioteca pes join tp_itembiblioteca tibpes on pes.id_tib = tibpes.id and tibpes.id_tib_pai = tib.id where tibpes.metanome = 'idpessoa' and pes.id_ib_pai = ib.id) limit 1) ib
join tb_itembiblioteca ibf on ibf.id_ib_pai = ib.id
join tp_itembiblioteca tibf on ibf.id_tib = tibf.id and tibf.id_tib_pai = ib.id_tib;
DML;
        $stmt = $this->_db->prepare($query);
        $stmt->bindParam(":uf", $uf);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


    public function getFolderByGrupoByTib($idGrupo, $servico, $idTib = null) {

        $select = $this->select()->setIntegrityCheck(false);

        $select->from(array('ib' => 'tb_itembiblioteca'), array(
            'ib2.valor as titulo',
            'ib3.valor as arquivo',
            'g.nome as nome_grupo',
            'ib4.valor as dt_publicacao',
            'ib5.valor as status',
            'ib6.valor as ocr',
            'ib.id_tib',
            'ib.id',
            'rlvi.id_ib_vinculado'
        ))
            ->join(array('tib' => 'tp_itembiblioteca'), 'ib.id_tib = tib.id', array())
            ->join(array('ib2' => 'tb_itembiblioteca'), 'ib.id = ib2.id_ib_pai', array())
            ->join(array('tib2' => 'tp_itembiblioteca'), 'ib2.id_tib = tib2.id', array())
            ->join(array('ib3' => 'tb_itembiblioteca'), 'ib.id = ib3.id_ib_pai', array())
            ->join(array('tib3' => 'tp_itembiblioteca'), 'ib3.id_tib = tib3.id', array())
            ->join(array('ib4' => 'tb_itembiblioteca'), 'ib.id = ib4.id_ib_pai', array())
            ->join(array('tib4' => 'tp_itembiblioteca'), 'ib4.id_tib = tib4.id', array())
            ->join(array('ib5' => 'tb_itembiblioteca'), 'ib.id = ib5.id_ib_pai', array())
            ->join(array('tib5' => 'tp_itembiblioteca'), 'ib5.id_tib = tib5.id', array())
            ->joinLeft(array('ib6' => 'tb_itembiblioteca'), "ib.id = ib6.id_ib_pai", array())
            ->joinLeft(array('tib6' => 'tp_itembiblioteca'), "ib6.id_tib = tib6.id and tib6.id = '{$servico['metadata']['ws_arqstatus']}'", array())
            ->join(array('rlgi' => 'rl_grupo_item'), 'rlgi.id_item = ib.id', array())
            ->join(array('g' => 'tb_grupo'), 'rlgi.id_grupo = g.id', array())
            ->joinLeft(array('rlvi' => 'rl_vinculo_item'), 'rlvi.id_ib_vinculado = ib.id', array())

//            ->joinLeft(array('tim' => new Zend_Db_Expr("(select * from tp_itembiblioteca_metadata where metanome = 'ws_tib')")),'tib.id = tim.id_tib', array())
//            ->joinLeft(array('timcombo' => new Zend_Db_Expr("(select * fro    m tp_itembiblioteca_metadata where metanome = 'ws_comboform')")),'tib.id = timcombo.id_tib', array())
//            ->joinLeft(array('tibcampo' => 'tp_itembiblioteca'),'tibcampo.id_tib_pai::varchar = tim.valor::varchar and tibcampo.metanome = timcombo.valor',array())
//            ->joinLeft(array('ibcombo' => 'tb_itembiblioteca'),'ibcombo.id_ib_pai::varchar = ib.valor::varchar and ibcombo.id_tib = tibcampo.id', array())

            // pegando os dados do arquivo
            ->where('tib2.id = ?', $servico['metadata']['ws_arqnome']) // ws_arqnome
            ->where('tib3.id = ?', $servico['metadata']['ws_arqcampo']) // ws_arqcampo
            ->where('tib4.id = ?', $servico['metadata']['ws_arqdata']) // ws_arqdata
            ->where('tib5.id = ?', $servico['metadata']['ws_arqstatus']); // ws_arqstatus

//            ->where('tib6.id = ?', '1607fb2c-e28f-4af9-87d2-e98f0f10eb9d'); //$servico['metadata']['ws_arqocr']); // ws_arqstatus

//            ->where('tib4.id = ?', $servico['metadata']['ws_arqdata']);

//            ->orWhere('tib2.metanome = ?', 'imglocal')
//            ->orWhere('tib2.metanome = ?)', 'titulo');

//            ->order(array('ib.id_ib_pai', 'tib.metanome'));

        if ($idTib) {
            $select->where('rlvi.id_ib_principal = ?', $idTib);
        } else {
            $select->where('rlvi.id_ib_vinculado is null')
                   ->where('rlgi.id_grupo = ?', $idGrupo);
        }

//        echo $select->__toString();
//        exit;
        return $select;

//        exit;
//        $rows = $this->fetchAll($select);
//        return $rows;
    }

}
//
//    ->where('tib2.id = ?', '99696bec-ca4a-484c-a68e-78dbc19c1ce1') // ws_arqnome
//->where('tib3.id = ?', '3633182d-368d-47fd-a9c7-172c28f4f3b2') // ws_arqcampo
//->where('tib3.id = ?', '3633182d-368d-47fd-a9c7-172c28f4f3b2') // ws_arqsattu
//->where('tib4.id = ?', 'e2204ca0-3dfe-11e6-847b-0ff2a4830130'); // ws_arqdata
