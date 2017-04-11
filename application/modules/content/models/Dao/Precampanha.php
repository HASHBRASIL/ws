<?php

class Content_Model_Dao_Precampanha extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_pessoa";
    protected $_primary       = "id";
    
    protected $_rowClass = 'Content_Model_Vo_Precampanha';

    public $searchFields = array(
        'indicacao'     => "ind.nome",
        'info'          => 'inf.valor',
        'nome'          => 'pes.nome'
    );
    
    public function getSelectGrid($time,$campo) {

        $select = $this ->select()->setIntegrityCheck(false)
                         ->from(array('pes' => 'tb_pessoa'), array('pes.nome'))
                         ->join(array('rvp' => 'rl_vinculo_pessoa'), 'rvp.id_vinculado = pes.id', array('rvp.id'))
                         ->join(array('cls' => 'tb_classificacao'), 'rvp.id_classificacao = cls.id', array())
                         ->join(array('ind' => 'tb_pessoa'), 'rvp.id_pessoa = ind.id', array('indicacao'=>'ind.nome'))
                         ->joinLeft(array('inf' => 'tb_informacao'), 'inf.id_pessoa = pes.id', array('info'=>new Zend_Db_Expr("string_agg( distinct inf.valor,',')")))
                         ->join(array('tinf' => 'tp_informacao'), 'inf.id_tinfo = tinf.id', array())
                         ->where('rvp.id_grupo = ?', $time)
                         ->where("cls.metanome = 'INDICACAO'")
                         ->where("tinf.metanome = ? ", $campo)
                         ->group(array('rvp.id','pes.nome','indicacao'))
                         ->order(array('pes.nome','ind.nome'));

        return $select;

    }

    public function getCountSelectGrid($time,$campo) {

        $select = $this ->select()->setIntegrityCheck(false)
                         ->from(array('pes' => 'tb_pessoa'), array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => new Zend_Db_Expr("count(distinct pes.id)")))
                         ->join(array('rvp' => 'rl_vinculo_pessoa'), 'rvp.id_vinculado = pes.id', array())
                         ->join(array('cls' => 'tb_classificacao'), 'rvp.id_classificacao = cls.id', array())
                         ->join(array('ind' => 'tb_pessoa'), 'rvp.id_pessoa = ind.id', array())
                         ->joinLeft(array('inf' => 'tb_informacao'), 'inf.id_pessoa = pes.id', array())
                         ->join(array('tinf' => 'tp_informacao'), 'inf.id_tinfo = tinf.id', array())
                         ->where('rvp.id_grupo = ?', $time)
                         ->where("cls.metanome = 'INDICACAO'")
                         ->where("tinf.metanome = ? ", $campo);

        return $select;

    }

    public function getParCandidatoColigacao ($ibPai, $cargo) {

        $db = Zend_Db_Table::getDefaultAdapter();

        $query = <<<QUERY
        select ibcoligado.id,ibnome.valor as nome, ibcargo.valor, as cargo, ibcolig.valor, as coligacao, ibestado.valor, as estado from
        tb_itembiblioteca ibcoligado 
        join tb_itembiblioteca ibcargo on ibcargo.id_ib_pai = ibcoligado.id
        join tb_itembiblioteca ibcolig on ibcolig.id_ib_pai = ibcoligado.id
        join tb_itembiblioteca ibestado on ibestado.id_ib_pai = ibcoligado.id
        join tb_itembiblioteca ibnome on ibnome.id_ib_pai = ibcoligado.id
        join tp_itembiblioteca tibcolig on ibcolig.id_tib = tibcolig.id
        join tp_itembiblioteca tibcargo on ibcargo.id_tib = tibcargo.id
        join tp_itembiblioteca tibestado on ibestado.id_tib = tibestado.id
        join tp_itembiblioteca tibnome on ibnome.id_tib = tibnome.id
        join (select ibmaster.id, ibcolig.valor as colig, ibestado.valor as uf
        from tb_itembiblioteca ibmaster
        join tb_itembiblioteca ibcolig on ibcolig.id_ib_pai = ibmaster.id
        join tb_itembiblioteca ibestado on ibestado.id_ib_pai = ibmaster.id
        join tp_itembiblioteca tibcolig on ibcolig.id_tib = tibcolig.id
        join tp_itembiblioteca tibestado on ibestado.id_tib = tibestado.id
        where ibmaster.id = ?
        and tibcolig.metanome = 'coligacaoNome'
        and tibestado.metanome = 'uf') original on original.colig = ibcolig.valor and original.uf = ibestado.valor
        where ibcargo.valor = ?
        and tibcargo.metanome = 'cargo'
        and tibcolig.metanome = 'coligacaoNome'
        and tibestado.metanome = 'uf'
        and tibnome.metanome = 'nome'
QUERY;

        $db = $db->query($query, array($ibPai, $cargo) );
        $rowset = $db->fetchAll();

        return $rowset;
    }
    
     public function getParCandidatoSemColigacao ($ibPai, $cargo) {

        $db = Zend_Db_Table::getDefaultAdapter();

        $query = <<<QUERY
        select idvice.id, ibnome.valor as nome, ibnomeguerra.valor as nomeguerra, ibpartid.valor as partido, ibcidade.valor as cidade, ibestado.valor as estado
        from tb_itembiblioteca idvice
        join tb_itembiblioteca ibcidade on ibcidade.id_ib_pai = idvice.id
        join tb_itembiblioteca ibestado on ibestado.id_ib_pai = idvice.id
        join tb_itembiblioteca ibpartid on ibpartid.id_ib_pai = idvice.id
        join tb_itembiblioteca ibcargo on ibcargo.id_ib_pai = idvice.id
        join tb_itembiblioteca ibnome on ibnome.id_ib_pai = idvice.id
        join tb_itembiblioteca ibnomeguerra on ibnomeguerra.id_ib_pai = idvice.id
        join tp_itembiblioteca tibcidade on ibcidade.id_tib = tibcidade.id
        join tp_itembiblioteca tibestado on ibestado.id_tib = tibestado.id
        join tp_itembiblioteca tibpartid on ibpartid.id_tib = tibpartid.id
        join tp_itembiblioteca tibnome on ibnome.id_tib = tibnome.id
        join tp_itembiblioteca tibnomeguerra on ibnomeguerra.id_tib = tibnomeguerra.id
        join tp_itembiblioteca tibcargo on ibcargo.id_tib = tibcargo.id
        join (select ibmaster.id, ibcidade.valor as cidade, ibestado.valor as uf, ibpartid.valor as partid
        from tb_itembiblioteca ibmaster
        join tb_itembiblioteca ibcidade on ibcidade.id_ib_pai = ibmaster.id
        join tb_itembiblioteca ibestado on ibestado.id_ib_pai = ibmaster.id
        join tb_itembiblioteca ibpartid on ibpartid.id_ib_pai = ibmaster.id
        join tp_itembiblioteca tibcidade on ibcidade.id_tib = tibcidade.id
        join tp_itembiblioteca tibestado on ibestado.id_tib = tibestado.id
        join tp_itembiblioteca tibpartid on ibpartid.id_tib = tibpartid.id
        where ibmaster.id = ?
        and tibcidade.metanome = 'cidade'
        and tibestado.metanome = 'uf'
        and tibpartid.metanome = 'partidoSigla') pref on pref.cidade = ibcidade.valor and pref.uf = ibestado.valor and pref.partid = ibpartid.valor
        where ibcargo.valor = ?
        and tibcidade.metanome = 'cidade'
        and tibestado.metanome = 'uf'
        and tibpartid.metanome = 'partidoSigla'
        and tibcargo.metanome = 'cargo'
        and tibnome.metanome = 'nome'
        and tibnomeguerra.metanome = 'nomeGuerra'
        group by idvice.id, ibnome.valor, ibpartid.valor, ibcidade.valor, ibestado.valor, ibnomeguerra.valor
QUERY;

        $db = $db->query($query, array($ibPai, $cargo) );
        $rowset = $db->fetchAll();

        return $rowset;
    }
}