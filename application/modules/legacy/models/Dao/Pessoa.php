<?php

    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 18/12/15
     * Time: 12:58
     */
    class Legacy_Model_Dao_Pessoa extends App_Model_Dao_Abstract
    {
        protected $_name         = "tb_pessoa";
        protected $_primary      = "id";

        public $searchFields = array(
            'nome'         => 'pes.nome',
            'nome2'          => 'pes.nome2'
        );

        public function getHeaderGrid ($id_tib) {

            $select = $this->select();

            $select->from(array('tpib' => $this->_name), array('*', 'metadata' => new zend_db_expr('json_object( array_agg(meta.metanome), array_agg(meta.valor))') ))
                ->join(array('meta' =>  'tp_itembiblioteca_metadata'), 'tpib.id = meta.id_tib', array())
                ->where('tpib.id_tib_pai = ?', $id_tib)
                ->group('tpib.id');

            $rowset = $this->fetchAll($select);

            return $rowset;

        }

        public function getVisibleFields($perfis)
        {
            $select = $this->getAllFields($perfis);

            $select->columns(array('tinf.nome'))
                ->join(array('tpifm' => 'tp_informacao_metadata'),
                    "tpifm.id_tpinfo = tinf.id and tpifm.metanome = 'ws_ordemLista'", array())
                ->reset(Zend_Db_Select::ORDER)
                ->order('tpifm.valor');

            $rs = $this->fetchAll($select);

            return $rs;
        }

        public function getAllFields($perfis)
        {
            $select = $this->select()->setIntegrityCheck(false);

            $select->from(array('rpi' => 'rl_perfil_informacao'), array('tinf.metanome'))
                ->join(array('prf' => 'tb_perfil'), 'rpi.id_perfil = prf.id', array())
                ->join(array('tinf' => 'tp_informacao'), 'rpi.id_informacao = tinf.id', array())
                ->where("prf.metanome = ANY ( string_to_array(? , ','))", $perfis)
                ->order(array('tinf.metanome'));

            return $select;
        }

        public function selectGrid($time, $classificacao, $perfis, $gridHeader) {

            // $visiveis = $this->getVisibleFields($perfis);
            // $arrVis = array();
            // foreach($visiveis as $vis){
            //     $arrVis[] = "(tinf.metanome = '" . $vis['metanome'] . "')";
            // }
            // $strVis = implode(' or ', $arrVis);

            if($classificacao) {
                $arrCls = explode(',',$classificacao);
                $newArrCls = array();
                foreach($arrCls as $cls) {
                    $newArrCls[] = "(cls.metanome = '" . $cls . "')";
                }
                $strCls = implode(' or ', $newArrCls);
            }

            // $arrPrf = explode(',',$perfis);
            // $newArrPrf = array();
            // foreach($arrPrf as $prf) {
            //     $newArrPrf[] = "(prf.metanome = '" . $prf . "')";
            // }
            // $strPrf = implode(' or ', $newArrPrf);

            $arrTimes = explode(',',$time);
            $newArrTimes = array();
            foreach($arrTimes as $tm) {
                $newArrTimes[] = "(rvp.id_grupo = '" . $tm . "')";
            }
            $strTimes = implode(' or ', $newArrTimes);

            // $select1 = $this->select()->setIntegrityCheck(false);
            // $select2 = $this->getAllFields($perfis);
            // $camposVarchar = implode($gridHeader['fields'], ' varchar, ');

            // $select3 = $this->select()->setIntegrityCheck(false);

            // $select3->from(array('g' => 'tb_grupo', array('g.id', 'g.id_pai')))
            //     ->where('id = ?', $time);

            // $select4 = $this->select()->setIntegrityCheck(false);
            // $select4->from(array('g' => 'tb_grupo', array('g.id', 'g.id_pai')))
            //     ->join(array('gt' => 'gettimes'), 'gt.id_pai = g.id', array());

            // $select5 = $this->select()->setIntegrityCheck(false)
            //     ->union(array($select3, $select4));


            // $select1->from(array('rgi' => 'rl_grupo_informacao'),
            //     array('pes.id', 'tinf.metanome', new zend_db_expr('coalesce(refpes.nome,ib.valor,inf.valor)')))
            //     ->join(array('pes' => 'tb_pessoa'), 'rgi.id_pessoa = pes.id', array())
            //     ->join(array('grp' => 'tb_grupo'), 'rgi.id_grupo = grp.id', array())
            //     ->joinLeft(array('inf' => 'tb_informacao'), 'rgi.id_info = inf.id', array())
            //     ->joinLeft(array('tinf' => 'tp_informacao'), 'inf.id_tinfo = tinf.id', array())
            //     ->joinLeft(array('timtib' => new Zend_Db_Expr("(select * from tp_informacao_metadata where metanome = 'ws_tib')")),
            //         'tinf.id = timtib.id_tpinfo', array())
            //     ->joinLeft(array('timcombo' => new Zend_Db_Expr("(select * from tp_informacao_metadata where metanome = 'ws_comboform')")),
            //         'tinf.id = timcombo.id_tpinfo', array())
            //     ->joinLeft(array('tibcampo' => 'tp_itembiblioteca'),
            //         'tibcampo.id_tib_pai::varchar = timtib.valor::varchar and tibcampo.metanome = timcombo.valor',
            //         array())
            //     ->joinLeft(array('ib' => 'tb_itembiblioteca'),
            //         'ib.id_ib_pai::varchar = inf.valor::varchar and id_tib = tibcampo.id', array())
            //     ->join(array('rpi' => 'rl_perfil_informacao'), 'inf.id_tinfo = rpi.id_informacao', array())
            //     ->join(array('prf' => 'tb_perfil'), 'rpi.id_perfil  = prf.id', array())
            //     ->join(array('rvp' => 'rl_vinculo_pessoa'), 'rgi.id_grupo = rvp.id_grupo and pes.id = rvp.id_pessoa',array())
            //     ->join(array('cls' => 'tb_classificacao'), 'rvp.id_classificacao = cls.id', array())
            //     ->joinLeft(array('refpes' => 'tb_pessoa'), 'refpes.id::varchar = inf.valor::varchar', array())

            //     ->where(" ($strVis) ")
            //     ->where(" ($strPrf) ")
            //     ->where(" ($strTimes) ");
            //     if (isset($strCls)){
            //         $select1->where(" ($strCls) ");
            //     }
            //     $select1->order(array('pes.id', 'tinf.metanome'));

            // $select = $this->select()->setIntegrityCheck(false);
            // $select->from(array('subselect' => new zend_db_expr("( SELECT * FROM crosstab( $$ $select1 $$, $$ $select2 $$ ) AS ( id uuid, $camposVarchar varchar ) )")),
            //     new Zend_Db_Expr(implode(array_merge(array("id"), array_column($gridHeader['header'], 'campo')), " ,")));

            $select = $this->select()->setIntegrityCheck(false);
            $select->from(array('pes' => 'tb_pessoa'), array('pes.id', 'pes.nome', 'pes.nome2'))
                    ->join(array('rvp' => 'rl_vinculo_pessoa'), 'rvp.id_pessoa = pes.id', array())
                    ->join(array('cls' => 'tb_classificacao'), 'rvp.id_classificacao = cls.id', array())
                    ->where(" ($strTimes) ");
                    if (isset($strCls)){
                        $select->where(" ($strCls) ");
                    }

            return $select;
        }

        public function selectGrid2($time, $tipopessoa, $informacao, $classificacao, $filtros)
        {
            $arrTimes       = explode(',', $time);
            $newArrTimes    = array();

            foreach($arrTimes as $tm) {
                $newArrTimes[] = "(rvp.id_grupo = '" . $tm . "')";
            }

            $strTimes = implode(' or ', $newArrTimes);

            $lstInf = explode(',',$informacao);
            //array_unshift($lstInf,'');
            $arrSelect = array();
            $arrCampos = ['id' => 'pes.id','nome'=>'pes.nome','nome2'=>'pes.nome2'];
            $arrCamposMasters = ['id','nome','nome2'];
            $arrCamposMain = $arrCampos;
            $arrCamposCmp = array();
            //foreach($lstInf as $inf) {

            //     $arrCamposMain[strtolower($inf)] = new Zend_Db_Expr("''");
            //     $linf = strtolower($inf);
            //     $arrCamposMasters[] = new Zend_Db_Expr("string_agg( distinct t.{$linf},',')");
            //     foreach($lstInf as $infcmp) {
            //         if($inf == $infcmp) {
            //             $arrCampoCmp[$inf]['campos'][strtolower($infcmp)] = new Zend_Db_Expr("string_agg( distinct inf.valor,',')");
            //         } else {
            //             $arrCampoCmp[$inf]['campos'][strtolower($infcmp)] = new Zend_Db_Expr("''");
            //         }

            //     }
            // }

            $select1 = $this->_db->select()
                            ->from(['pes'=>'tb_pessoa'],$arrCamposMain)
                            ->join(['rvp'=>'rl_vinculo_pessoa'],'rvp.id_pessoa = pes.id',[])
                            ->where(" ($strTimes) ");
            if($classificacao) {
                $select1->join(['cls'=>'tb_classificacao'],'rvp.id_classificacao = cls.id',[]);
                $select1->where('cls.metanome = ? ', $classificacao );
            }

            if($tipopessoa) {
                $select1->where('pes.tipopessoa = ?', $tipopessoa);
            }

            if((count($lstInf)>1) || ((count($lstInf)==1) && (strlen($lstInf[0])>0)) ) {
                foreach($lstInf as $cnt=>$inf){
                    $select1->join(['inf'.$cnt=>'tb_informacao'],"inf{$cnt}.id_pessoa = pes.id",["info{$cnt}" => "inf{$cnt}.valor"]);
                    $select1->join(['tinf'.$cnt=>'tp_informacao'],"inf{$cnt}.id_tinfo = tinf{$cnt}.id",[]);
                    $select1->where("tinf{$cnt}.metanome = ?", $inf);
                }
            }

            if($filtros) {
                foreach($filtros as $cnt=>$fil) {
                    $select1->join(['fil'.$cnt=>'tb_informacao'],"fil{$cnt}.id_pessoa = pes.id",[]);
                    $select1->where("fil{$cnt}.idx_valor @@ to_tsquery('{$fil->valor}')");
                }
            }
            
            $select1->order('pes.dt_criacao DESC');
            
            //$arrSelect[] = $select1;

            // foreach($arrCampoCmp as $campo => $data) {
            //     $arrCamposCampo = $arrCampos;
            //     foreach($data['campos'] as $cmp => $expr) {
            //         $arrCamposCampo[$cmp] = $expr;
            //     }
            //     $tmpSql = $this->_db->select()
            //                 ->from(['pes'=>'tb_pessoa'],$arrCamposCampo)
            //                 ->join(['rvp'=>'rl_vinculo_pessoa'],'rvp.id_pessoa = pes.id',[])
            //                 ->join(['cls'=>'tb_classificacao'],'rvp.id_classificacao = cls.id',[])
            //                 ->joinLeft(['inf'=>'tb_informacao'],'inf.id_pessoa = pes.id',[])
            //                 ->join(['tinf'=>'tp_informacao'],'inf.id_tinfo = tinf.id',[])
            //                 ->where('rvp.id_grupo = ? ', $time)
            //                 ->where('cls.metanome = ? ', $classificacao)
            //                 ->where('pes.tipopessoa = ? ', $tipopessoa)
            //                 ->where('tinf.metanome = ? ', $campo)
            //                 ->group(['pes.id','pes.nome','pes.nome2']);
            //     // if($filtros) {
            //     //     foreach($filtros as $cnt=>$fil) {
            //     //         $tmpSql->join(['fil'.$cnt=>'tb_informacao'],"fil{$cnt}.id_pessoa = pes.id",[]);
            //     //         $tmpSql->join(['tfil'.$cnt=>'tp_informacao'],"fil{$cnt}.id_tinfo = tfil{$cnt}.id",[]);
            //     //         $tmpSql->where("tfil{$cnt}.metanome = '{$fil->campo}'");
            //     //         $tmpSql->where("fil{$cnt}.valor ${$fil->condicao} '{$fil->valor}'");
            //     //     }
            //     // }
            //     $arrSelect[] = $tmpSql;
            // }

            return $select1;
        }

        public function countGrid2($time,$tipopessoa,$classificacao,$filtros) {
            $arrTimes = explode(',',$time);
            $newArrTimes = array();
            foreach($arrTimes as $tm) {
                $newArrTimes[] = "(rvp.id_grupo = '" . $tm . "')";
            }
            $strTimes = implode(' or ', $newArrTimes);

            //array_unshift($lstInf,'');
            $arrSelect = array();
            $arrCampos = [Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'count(1)'];
            $arrCamposMain = $arrCampos;

            $select1 = $this->_db->select()
                            ->from(['pes'=>'tb_pessoa'],$arrCamposMain)
                            ->join(['rvp'=>'rl_vinculo_pessoa'],'rvp.id_pessoa = pes.id',[])
                            ->where(" ($strTimes) ");
            if($classificacao) {
                $select1->join(['cls'=>'tb_classificacao'],'rvp.id_classificacao = cls.id',[]);
                $select1->where('cls.metanome = ? ', $classificacao );
            }

            if($tipopessoa) {
                $select1->where('pes.tipopessoa = ?', $tipopessoa);
            }

            if($filtros) {
                foreach($filtros as $cnt=>$fil) {
                    $select1->join(['fil'.$cnt=>'tb_informacao'],"fil{$cnt}.id_pessoa = pes.id",[]);
                    $select1->where("fil{$cnt}.idx_valor @@ to_tsquery('{$fil->valor}')");
                }
            }

            //x($select1->__toString());

            return $select1;
        }

        /**
         * @param string $chave o campo que será usado como chave.
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_primary
         * @param string $valor o campo que deve ser retornado no valor
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_namePairs
         * @param string $where
         * @param string $ordem
         * @param string $limit
         * @return Ambigous <multitype:, multitype:mixed >
         */
        public function fetchPairsGrupo($chave = null, $valor = null, $where = null, $ordem = null, $limit = null)
        {
            if(empty($chave)){
                if(is_array($this->_primary)){
                    $chave = $this->_primary[1];
                }else{
                    $chave = $this->_primary;
                }
            }

            if(empty($valor)){
                $valor = $this->_namePairs;
            }

            $select = $this->_db
                ->select()
                ->from(array('te' => $this->_name), array($chave, $valor))
//                ->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = te.id')
//                ->where('ta.id_caracteristica = ?', Empresa_Model_Bo_Caracteristica::GRUPO)
                ->order($ordem ? $ordem : $valor);

            if( is_numeric( $limit) ){
                $select->limit( $limit );
            }

            if($where){
                if (is_array($where)){
                    foreach ($where as $key => $value){
                        $select->where($key, $value);
                    }
                }else{
                    $select->where($where);
                }
            }

//            $select->where("te.cnpj_cpf is not null");
            return $this->_db->fetchPairs($select);
        }

        public function getPessoaByGrupo()
        {

            $db = Zend_Db_Table::getDefaultAdapter();

            $query = <<<QUERY

                WITH RECURSIVE getGEM AS (
                SELECT * FROM tb_grupo WHERE id IN ( SELECT id_grupo FROM rl_grupo_pessoa WHERE id_pessoa = ? )
            UNION
                SELECT g.* FROM tb_grupo g JOIN getGEM gg ON ( gg.id_pai = g.id )
            ) SELECT * FROM getGEM WHERE id_representacao IS NOT NULL and id_pai IS NOT NULL ORDER BY nome
QUERY;

            $identity = Zend_Auth::getInstance()->getIdentity();

            $stmt = $db->query($query, array($identity->id));
            $rowset = $stmt->fetchAll();

            return $rowset;
        }




        public function getClientes($termo)
        {


            $db = Zend_Db_Table::getDefaultAdapter();

            $query = <<<QUERY

                SELECT
                tbpessoa.id, tbpessoa.nome
                , json_agg(tpinfo.nome) as nomes
                , json_agg(tpinfo.metanome) as metanomes, json_agg(tpinfo.id) as tpinfo_id, json_agg(tbinfo.valor) as valores, json_agg(tpinfo.ordem) as ordem

                from tb_pessoa tbpessoa

                    inner join rl_vinculo_pessoa rlvp on rlvp.id_pessoa = tbpessoa.id
                    inner join tb_classificacao tbc on tbc.id = rlvp.id_classificacao
                    inner join tb_perfil tbperfil on tbperfil.metanome = ANY (string_to_array(?, ','))
                    inner join rl_perfil_informacao rlpi on rlpi.id_perfil = tbperfil.id
                    inner join tp_informacao tpinfo on tpinfo.id = rlpi.id_informacao AND tpinfo.visivel = true
                    left join tb_informacao tbinfo on tbinfo.id_tinfo = tpinfo.id and tbinfo.id_pessoa = tbpessoa.id
                    and tbinfo.id in (select id_info from rl_grupo_informacao where id_pessoa = tbpessoa.id and id_grupo  = rlvp.id_grupo)

                    WHERE rlvp.id_grupo = ANY ((string_to_array(?, ','))::uuid[])
                    AND tbc.nome = ?
                    and tbpessoa.nome ilike ?

                    GROUP BY tbpessoa.id, tbpessoa.nome
QUERY;

            $identity = Zend_Auth::getInstance()->getIdentity();

            $stmt = $db->query($query, array('cliente', $identity->time['id'],'cliente', "%$termo%"));
            $rowset = $stmt->fetchAll();

            return $rowset;
        }

        public function getById($id)
        {

            $identity = Zend_Auth::getInstance()->getIdentity();
            $where = $this->getDefaultAdapter()->quoteInto("'?'", $id);
            $time = $this->getDefaultAdapter()->quoteInto("'?'", $identity->time['id']);

            $query = <<<QUERY

select id, COALESCE(nomefantasia, nomepessoa, razaosocial) as nome from crosstab(

                'select id_pessoa,metanome,valor from (select inf.id_pessoa,tinf.metanome,coalesce(pes.nome,inf.valor) as valor from tb_informacao inf
join tp_informacao tinf on (inf.id_tinfo = tinf.id)
join tp_informacao_metadata tim on (tinf.id = tim.id_tpinfo)
left outer join tb_pessoa pes on (inf.valor::varchar = pes.id::varchar)
where tim.metanome = ''ws_pesquisavel''
and inf.id_pessoa in (
WITH recursive gettimes AS
(
SELECT g.id,g.id_pai FROM   tb_grupo g  WHERE  g.id = $time
UNION
select g.id,g.id_pai from tb_grupo g join gettimes gt on (g.id = gt.id_pai)
)
SELECT   distinct inf.id_pessoa
FROM     tb_informacao inf
JOIN     tp_informacao tinf on (inf.id_tinfo = tinf.id)
JOIN     rl_grupo_informacao rgi ON (inf.id = rgi.id_info)
JOIN     tp_informacao_metadata tim ON (inf.id_tinfo = tim.id_tpinfo)
JOIN     gettimes gp ON (rgi.id_grupo = gp.id)
left outer join tb_pessoa pes on (pes.id::varchar = inf.valor::varchar)
WHERE
(
gp.id = $time
OR  EXISTS
(SELECT 1 FROM   tb_grupo_metadata gm WHERE  id_grupo = gp.id  AND    gm.metanome = ''ws_infopublica'')
)
AND    tim.metanome = ''ws_pesquisavel'') order by inf.id_pessoa, tinf.metanome) t
where t.id_pessoa = $where
', 'select tinf.metanome from tp_informacao tinf join tp_informacao_metadata tim on (tinf.id = tim.id_tpinfo) where tim.metanome = ''ws_pesquisavel'' order by tinf.metanome') as ct(id uuid, NOMEFANTASIA varchar, NOMEPESSOA varchar, RAZAOSOCIAL varchar)
QUERY;

            $db = Zend_Db_Table::getDefaultAdapter();

            $stmt = $db->query($query);

            $row = $stmt->fetch();

            return $row;
        }


         public function getByIdIgnoreTime($id)
        {

            $identity = Zend_Auth::getInstance()->getIdentity();
            $where = $this->getDefaultAdapter()->quoteInto("'?'", $id);

            $query = <<<QUERY

select id, COALESCE(nomefantasia, nomepessoa, razaosocial) as nome from crosstab(

                'select id_pessoa,metanome,valor from (select inf.id_pessoa,tinf.metanome,coalesce(pes.nome,inf.valor) as valor from tb_informacao inf
join tp_informacao tinf on (inf.id_tinfo = tinf.id)
join tp_informacao_metadata tim on (tinf.id = tim.id_tpinfo)
left outer join tb_pessoa pes on (inf.valor::varchar = pes.id::varchar)
where tim.metanome = ''ws_pesquisavel''
and inf.id_pessoa in (
WITH recursive gettimes AS
(
SELECT g.id,g.id_pai FROM   tb_grupo g
UNION
select g.id,g.id_pai from tb_grupo g join gettimes gt on (g.id = gt.id_pai)
)
SELECT   distinct inf.id_pessoa
FROM     tb_informacao inf
JOIN     tp_informacao tinf on (inf.id_tinfo = tinf.id)
JOIN     rl_grupo_informacao rgi ON (inf.id = rgi.id_info)
JOIN     tp_informacao_metadata tim ON (inf.id_tinfo = tim.id_tpinfo)
JOIN     gettimes gp ON (rgi.id_grupo = gp.id)
left outer join tb_pessoa pes on (pes.id::varchar = inf.valor::varchar)
WHERE
(
EXISTS
(SELECT 1 FROM   tb_grupo_metadata gm WHERE  id_grupo = gp.id  AND    gm.metanome = ''ws_infopublica'')
)
AND    tim.metanome = ''ws_pesquisavel'') order by inf.id_pessoa, tinf.metanome) t
where t.id_pessoa = $where
', 'select tinf.metanome from tp_informacao tinf join tp_informacao_metadata tim on (tinf.id = tim.id_tpinfo) where tim.metanome = ''ws_pesquisavel'' order by tinf.metanome') as ct(id uuid, NOMEFANTASIA varchar, NOMEPESSOA varchar, RAZAOSOCIAL varchar)
QUERY;

            $db = Zend_Db_Table::getDefaultAdapter();

            $stmt = $db->query($query);

            $row = $stmt->fetch();

            return $row;
        }

        public function getAutocomplete($term, $limit = 10, $page = 0, $chave = null, $valor = null, $ordem = null, $ativo = false) {

            $identity = Zend_Auth::getInstance()->getIdentity();

            $sqlLimit = 'limit ?';
            
            if (!is_numeric($limit)) {
                $limit = 1000;
            }

            if($page >= 1){
                $sqlLimit = 'limit ? OFFSET ?';
                $limit = array($limit, ($page - 1 ) * $limit);
            }
                
            $query="SELECT id, case when ts_rank_cd(idx_nome, plainto_tsquery(?)) >= ts_rank_cd(idx_nome2, plainto_tsquery(?)) then nome
                    when ts_rank_cd(idx_nome, plainto_tsquery(?)) < ts_rank_cd(idx_nome2, plainto_tsquery(?)) then nome2
                    end as text
                    FROM (
                    SELECT id, nome, nome2, idx_nome, idx_nome2
                      FROM tb_pessoa_teste_indice
                      WHERE ((idx_nome @@ plainto_tsquery(?)) or (idx_nome2 @@ plainto_tsquery(?)))
                    ) AS t1 ORDER BY ts_rank_cd(idx_nome, plainto_tsquery(?)) DESC, ts_rank_cd(idx_nome, plainto_tsquery(?)) DESC $sqlLimit;";

            $db = Zend_Db_Table::getDefaultAdapter();

            $stmt = $db->prepare($query);

            $stmt->bindValue(1,$term);
            $stmt->bindValue(2,$term);
            $stmt->bindValue(3,$term);
            $stmt->bindValue(4,$term);
            $stmt->bindValue(5,$term);
            $stmt->bindValue(6,$term);
            $stmt->bindValue(7,$term);
            $stmt->bindValue(8,$term);
            
            if(is_array($limit)){
                $stmt->bindValue(9,$limit[0]);
                $stmt->bindValue(10,$limit[1]);
            }else{
                $stmt->bindValue(9,$limit);
            }
            
            
            $stmt->execute();
            $rows = $stmt->fetchAll();

            // x($rows);

            return $rows;
        }



        public function getListFaturadoWithAgrupadorAndWorkspacePerTransacao($idPessoaFaturado = null, $idGrupo = null){

            $select = $this->_db->select()->from(array('emp' => $this->_name),array("id","nome_razao" => "nome", new Zend_Db_Expr('sum(agf.fin_valor) AS fin_valor')))
//                ->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = emp.id', array(null))
                ->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'agf.id_pessoa_faturado = emp.id', array("id_agrupador_financeiro"))
                ->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
                ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));

//                ->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));

            if (isset($idPessoaFaturado)){

                $select->where("emp.id = ?", $idPessoaFaturado);
            }

            if ($idGrupo){

                $select->where('agf.id_grupo = ?', $idGrupo);
            }

            $select->group(array("wk.nome", "emp.id", "agf.tmv_id", "id_agrupador_financeiro", "nome_razao", "emp.id", "tmv_descricao"));

            $select
//                ->where('ta.id_caracteristica = ?', Empresa_Model_Bo_Caracteristica::GRUPO)
                ->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO);

//                ->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
//                ->where("emp.ativo = ?",App_Model_Dao_Abstract::ATIVO);

            return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

        }

        public function getListFaturadoWithAgrupadorAndWorkspacePerTicket($idPessoaFaturado = null, $idGrupo = null){

            $select = $this->_db->select()->from(array('emp' => $this->_name),array("id","nome_razao" => "nome", new Zend_Db_Expr('sum(fin.fin_valor) AS fin_valor')))
                ->joinInner(array('fin'=>'fin_tb_financeiro'), 'fin.id_pessoa_faturado = emp.id', array("fin_id"))
                ->joinInner(array('agf'=>'fin_tb_agrupador_financeiro'), 'agf.id_agrupador_financeiro = fin.id_agrupador_financeiro', array("id_agrupador_financeiro"))
                ->joinInner(array('tmv'=>'fin_tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
                ->joinInner(array('wk' => 'tb_grupo'), 'wk.id = agf.id_grupo', array("nome_workspace" => "nome"));


//            $select = $this->_db->select()->from(array('emp' => $this->_name),array("id","nome_razao", new Zend_Db_Expr('sum(fin.fin_valor) AS fin_valor')))
//                ->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = emp.id', array(null))
//                ->joinInner(array('fin'=>'tb_financeiro'), 'fin.grupo_id = emp.id', array("fin_id"))
//                ->joinInner(array('agf'=>'tb_agrupador_financeiro'), 'agf.id_agrupador_financeiro = fin.id_agrupador_financeiro', array(null))
//                ->joinInner(array('tmv'=>'tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
//                ->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));


            if (isset($idPessoaFaturado)){

                $select->where("emp.id = ?", $idPessoaFaturado);
            }

            if ($idGrupo){

                $select->where('agf.id_grupo = ?', $idGrupo);
            }

            $select->group(array("wk.nome", "emp.id", "agf.tmv_id", "agf.id_agrupador_financeiro", "nome_razao", "emp.id", "tmv_descricao", "fin_id"));

            $select
//                ->where('ta.id_caracteristica = ?', Empresa_Model_Bo_Caracteristica::GRUPO)
                ->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO)
                ->where("fin.ativo = ?",App_Model_Dao_Abstract::ATIVO);
//                ->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
//                ->where("emp.ativo = ?",App_Model_Dao_Abstract::ATIVO);

            return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

        }


        /**
         * query para pegar os dados da pessoa.
         * @param null $id
         * @return mixed
         */
        public function get($id = null)
        {
            $query = <<<QUERY
                        SELECT
                            inf.id_pessoa,
                            tinf.metanome,
                            inf.valor,
                            rgi.id_grupo
                        FROM
                            tb_informacao inf
                        JOIN tp_informacao tinf ON (inf.id_tinfo = tinf. ID)
                        JOIN rl_grupo_informacao rgi ON (inf. ID = rgi.id_info)
                        WHERE
                            inf.id_pessoa = ?
                            AND rgi.id_grupo = ?
                            AND tinf.metanome IN (
                                    'NOME',
                                    'RAZAOSOCIAL',
                                    'CPF',
                                    'CNPJ'
                            )
QUERY;


            $db = Zend_Db_Table::getDefaultAdapter();
            $identity = Zend_Auth::getInstance()->getIdentity();

            $stmt = $db->query($query, array($id, $identity->time['id']));
            $rowset = $stmt->fetchAll();

//            var_dump($rowset);
//            exit;

            $result = new stdClass;
            $result->nome_razao = '';
            $result->cnpj_cpf = '';
            foreach ($rowset as $row) {
                if (($row['metanome'] == 'NOME') || ($row['metanome'] == 'RAZAOSOCIAL')) {
                    $result->nome_razao = $row['valor'];
                } else {
                    $result->cnpj_cpf = $row['valor'];
                }
            }

            return $result;

        }


        /**
         * query para pegar os dados da pessoa.
         * @param null $id
         * @return mixed
         */
        public function getPessoaByCpfCnpj($cpfcnpj)
        {
            $query = <<<QUERY
            select pes.id
from tb_informacao inf
join tb_pessoa pes on (inf.id_pessoa = pes.id)
join rl_grupo_informacao rgi on (rgi.id_info = inf.id)
join tb_grupo grp on (rgi.id_grupo = grp.id)
join tp_informacao tinf on (inf.id_tinfo = tinf.id)
where replace(replace(replace(inf.valor,'.',''),'/',''),'-','') = ?
and tinf.metanome in ('CNPJ','CPF')
and grp.id in (WITH recursive gettimes AS
(
SELECT g.id,g.id_pai FROM   tb_grupo g  WHERE  g.id = ?
UNION
select g.id,g.id_pai from tb_grupo g join gettimes gt on (g.id = gt.id_pai)
) select id from gettimes gp WHERE
(
gp.id = ?
OR  EXISTS
(SELECT 1 FROM   tb_grupo_metadata gm WHERE  id_grupo = gp.id  AND    gm.metanome = 'ws_infopublica')
))

QUERY;


            $db = Zend_Db_Table::getDefaultAdapter();
            $identity = Zend_Auth::getInstance()->getIdentity();

            $stmt = $db->query($query, array($cpfcnpj, $identity->time['id'], $identity->time['id']));
            $rowset = $stmt->fetchAll();

            return $rowset[0]['id'];

        }

        /**
         * Procura pessoas por um endereço de e-mail.
         *
         * @param string $email Endereço de e-mail para busca.
         * @return array
         */
        public function getPessoasUsuarioByEmail($email)
        {
            $query = <<<QUERY
    SELECT tbp.id,
           tbu.nomeusuario
      FROM tb_pessoa tbp
        LEFT JOIN tb_usuario tbu USING(id)
        INNER JOIN tb_informacao tbi ON (tbp.id = tbi.id_pessoa)
        INNER JOIN tp_informacao tpi ON (tbi.id_tinfo = tpi.id)
      WHERE tpi.metanome = :metanome
        AND tbi.valor = :email
QUERY;
            $db = Zend_Db_Table::getDefaultAdapter();

            return $db->query($query, [
                'metanome' => Config_Model_Bo_TipoInformacao::META_EMAIL,
                'email' => $email
            ])->fetchAll();
        }

        public function getSimpleById($id) {
            $select = $this->_db->select()->from(array($this->_name),array('id','nome','nome2'))->where('id = ?',$id);
            return $this->_db->query($select)->fetchAll();
        }

        public function criar_usuario($nome, $nome2, $username, $salt, $password, $param)
        {
            $query = <<<QUERY
SELECT criar_usuario(:nome, :nome2, :username, :salt, :password, :param)
QUERY;
            $db = Zend_Db_Table::getDefaultAdapter();
            return $db->query($query, [
               'nome' => $nome,
               'nome2' => $nome2,
               'username' => $username,
               'salt' => $salt,
               'password' => $password,
               'param' => $param
            ])->fetchAll();
        }

        public function criar_entidade($idCriador, $nome, $nome2, $idGrupo, $idVinculado, $param)
        {
            $query = <<<QUERY
SELECT criar_entidade(:criador, :nome, :nome2, :grupo, :vinculado, :param)
QUERY;
            $db = Zend_Db_Table::getDefaultAdapter();
            return $db->query($query, [
               'criador' => $idCriador,
               'nome' => $nome,
               'nome2' => $nome2,
               'grupo' => $idGrupo,
               'vinculado' => $idVinculado,
               'param' => $param
            ])->fetchAll();
        }
    }
