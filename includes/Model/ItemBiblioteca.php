<?php

/**
 * User: ericcomcmudo
 * Date: 14/12/15
 * Time: 17:21
 */

class ItemBiblioteca extends Base
{
    /**
     * @param $grupo
     * @param $id_tib_pai
     * @param $filtros
     * @param null $offset
     * @return array
     */
    function getItemBibliotecaByGrupo($grupo, $id_tib_pai, $filtros = null, $offset = null) {

        if (!$offset) {
            $offset = 0;
        }

        //Montando filtros din�micos
        $strFiltros = "";
        $where = " 1=1 ";
        $where .= " AND RLGI.id_grupo = ? ";
        $where .= " AND IB.id_tib = ?";
        if ($filtros){
            foreach ($filtros as $kf){
                $where .= " AND EXISTS (SELECT 1 FROM tp_itembiblioteca tibEx JOIN tb_itembiblioteca ibEx ON (tibEx.id = ibEx.id_tib) WHERE tibEx.metanome = ? AND ibEx.valor = ? AND ibEx.id_ib_pai = ib.id)";
            }
        }

        $stmt = $this->dbh->prepare(
            "WITH RECURSIVE itensBiblioteca as
            (
                (
                SELECT
                    IB.*,
                    '' as ordem,
                    tib.nome as nomeCampo
                FROM    tb_itembiblioteca   as  IB
                JOIN    rl_grupo_item as RLGI
                    ON  (ib.id = RLGI.id_item)
                JOIN    tp_itembiblioteca   as  tib
                    ON  (ib.id_tib = tib.id)
                WHERE   $where
                LIMIT   30  OFFSET  ?
                )
                UNION
                (
                SELECT
                    IB2.*,
                    tib_ordem.valor as  ordem,
                    tib2.metanome   as  nomeCampo
                FROM    tb_itembiblioteca   IB2
                JOIN    itensBiblioteca as  itensBiblioteca
                    ON  (IB2.id_ib_pai = itensBiblioteca.id)
                JOIN tp_itembiblioteca  as  tib2
                    ON  (IB2.id_tib = tib2.id)
                JOIN
                    (
                    SELECT  *
                    FROM    tp_itembiblioteca_metadata
                    WHERE   metanome = 'ws_ordemLista'
                    )   as  tib_ordem
                    ON  (IB2.id_tib = tib_ordem.id_tib)
                JOIN
                    (
                    SELECT  *
                    FROM    tp_itembiblioteca_metadata
                    WHERE   metanome = 'ws_visivel'
                    )   as tib_visivel
                    ON (IB2.id_tib = tib_visivel.id_tib)
                )
            )

            SELECT
                itensBiblioteca.id_ib_pai,
                json_agg(itensBiblioteca.nomeCampo order by itensBiblioteca.ordem)  as  campos,
                json_agg(itensBiblioteca.valor ORDER BY itensBiblioteca.ordem)      as  valores
            FROM    itensBiblioteca
            WHERE   itensBiblioteca.id_ib_pai is not null
            GROUP   BY  itensBiblioteca.id_ib_pai");
        //x($stmt);
        $cnt = 3;
        $stmt->bindValue(1, $grupo);
        $stmt->bindValue(2, $id_tib_pai);

        if ($filtros){
            foreach ($filtros as $kbind => $rbind){
                $stmt->bindValue($cnt++, $kbind);
                $stmt->bindValue($cnt++, $rbind);
            }
        }
        $stmt->bindValue($cnt++, $offset);
        $stmt->execute();

        $rsItemBiblioteca = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rsItemBiblioteca;
        }

    function getFilhosByIdPai($id){

        $stmt = $this->dbh->prepare("SELECT * FROM tb_itembiblioteca WHERE id = :id");
        $stmt->bindValue('id', $id);
        $stmt->execute();

        $rsItemBibliotecaMaster = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $this->dbh->prepare("SELECT * FROM tb_itembiblioteca WHERE id_ib_pai = :id_ib_pai");
        $stmt2->bindValue('id_ib_pai', $rsItemBibliotecaMaster[0]['id']);
        $stmt2->execute();

        $rsItemBibliotecaFilhos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return $rsItemBibliotecaFilhos;
    }

    /**
     * @param $grupo
     * @param $arrTipo
     * @param null $offset
     * @return array
     */
    function getArquivosByGrupo($grupo, $arrTipo, $offset = null)   {
        if (!$offset) {
            $offset = 0;
        }
        $rsDocs =   array();
        foreach ( $arrTipo as $key => $tipo){

            $stmt = $this->dbh->prepare(
                "
                SELECT  *
                FROM    tb_itembiblioteca AS item
                INNER   JOIN    rl_grupo_item relacao   ON item.id_ib_pai = relacao.id_item
                INNER   JOIN    tp_itembiblioteca meta  ON meta.id  = item.id_tib
                WHERE   meta.tipo = ?
                ");

            switch ( $tipo ){
                case 'image';
                $stmt->bindValue(1, $tipo);
                break;

                case 'audio';
                $stmt->bindValue(1, $tipo);
                break;

                case 'doc';
                $stmt->bindValue(1, $tipo);
                break;

                case 'file';
                $stmt->bindValue(1, $tipo);
                break;
            }
            $stmt->execute();
            $rsDocs[ $key ] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $rsDocs;
    }

    /**
     * @param $id
     * @return array
     */
    function getById($id)   {
        $rsItemBiblio   =   array();
        $stmt = $this->dbh->prepare("SELECT *   FROM    tb_itembiblioteca   WHERE   id  =   ?");

        $stmt->bindValue(1, $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param $id
     * @return array
     */
    function getByValor($master, $ordem, $termo, $limit = 10, $page = 1)   {
        $page--;
        $stmt = $this->dbh->prepare("
		select ibfilho.id_ib_pai,tib.metanome, ibfilho.valor, ibordem.valor as ordem
		from tb_itembiblioteca ibfilho join
			(select distinct inf.id_ib_pai as id, ibordem.valor as ordem
			from tb_itembiblioteca inf join tp_itembiblioteca tinf on (inf.id_tib = tinf.id)
			join tb_itembiblioteca ibordem on (inf.id_ib_pai = ibordem.id_ib_pai)
			join tp_itembiblioteca tpordem on (ibordem.id_tib = tpordem.id)
			where tinf.id_tib_pai = :master
			and tpordem.metanome = :ordem
			and inf.valor ilike :termo
			order by ibordem.valor
			limit :limit offset :offset)
		ibpai on (ibfilho.id_ib_pai = ibpai.id) join tb_itembiblioteca ibordem on (ibpai.id = ibordem.id_ib_pai)
		join tp_itembiblioteca tib on (ibfilho.id_tib = tib.id)
		join tp_itembiblioteca tpordem on (ibordem.id_tib = tpordem.id)
		where tpordem.metanome = :ordem order by ibordem.valor");
//            SELECT  ib.*
//            FROM    tb_itembiblioteca ib
//            INNER   JOIN    tp_itembiblioteca   AS tib
//                ON  tib.id = ib.id_tib
//            WHERE   ib.id_tib in
//                (
//                select  tp.id
//                from    tp_itembiblioteca tp
//                inner   join tp_itembiblioteca as tp2
//                    on  tp.id_tib_pai = tp2.id
//                where   tp2.id = :master and tp.metanome = :campo
//                )
//                AND ib.valor ilike :termo
//            LIMIT :limit ");
//
	$offset = $page * 10;
        $termo = "%" . $termo . "%";
        $stmt->bindParam(':master', $master);
        $stmt->bindParam(':ordem',  $ordem);
        $stmt->bindParam(':termo',  $termo);
        $stmt->bindParam(':limit',  $limit);
	$stmt->bindParam(':offset', $offset);

        $stmt->execute();
        $rsItens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rsItens;
    }

    /**
     * @param $id
     * @param $valor
     * @param $id_tib
     * @param null  $id_ib_pai
     * @return array
     */
    function criarItem( $id, $valor, $id_tib, $id_ib_pai = NULL)
    {
        $i  =   1;
        if ( !is_null( $id_ib_pai)){
            $queryTbItemBilbioteca = $stmt = $this->dbh->prepare("INSERT INTO tb_itembiblioteca ( id_ib_pai, id, valor, id_criador, id_tib )
                                                                   VALUES ( ?, ?, ?, ?, ?  )");
            $queryTbItemBilbioteca->bindValue($i,       $id_ib_pai);

            $i++;
        }else{
            $queryTbItemBilbioteca = $stmt = $this->dbh->prepare("INSERT INTO tb_itembiblioteca ( id, valor, id_criador, id_tib )
                                                                   VALUES ( ?, ?, ?, ? )");
        }

        $stmt->bindValue($i++,  $id);
        $stmt->bindValue($i++,  $valor);
        $stmt->bindValue($i++,  $_SESSION['USUARIO']['ID']);
        $stmt->bindValue($i++,  $id_tib);
        $stmt->execute();

        return $id;
    }

    function euExisto($id)  {
        $rsItemBiblio   =   array();
        $stmt = $this->dbh->prepare("SELECT *   FROM    tb_itembiblioteca   WHERE   id  =   ?");

        $stmt->bindValue(1, $id);
        $stmt->execute();

        $rs =    $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( isset( $rs[0] ) ){
            return true;
        } else {
            return false;
        }
    }

    function apagarPaiEFilhos( $id_pai ){
        $stmt = $this->dbh->prepare("DELETE FROM tb_itembiblioteca  WHERE   id_ib_pai   =   ?");
        $stmt->bindValue(1, $id_pai);
        $stmt->execute();

        $stmt2 = $this->dbh->prepare("DELETE    FROM tb_itembiblioteca  WHERE   id  =   ?");
        $stmt2->bindValue(1, $id_pai);
        $stmt2->execute();
    }

    function apagarPorTIB($arrTIB) {

        foreach ( $arrTIB as $tib){
            $stmt = $this->dbh->prepare("DELETE FROM tb_itembiblioteca  WHERE   id_tib  =   ?");
            $stmt->bindValue(1, $tib);
            $stmt->execute();
        }
    }

    function getByValorETIB( $valor, $tib){
        $rsItemBiblio   =   array();
        $stmt = $this->dbh->prepare("select * from  tb_itembiblioteca where valor = ? and id_tib = ? ");
        $stmt->bindValue(1, $valor);
        $stmt->bindValue(2, $tib);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getByPaiETIB( $ibpai, $tib){
        $rsItemBiblio   =   array();
        $stmt = $this->dbh->prepare("select * from  tb_itembiblioteca where id_ib_pai = ? and id_tib = ? ");
        $stmt->bindValue(1, $ibpai);
        $stmt->bindValue(2, $tib);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllByTib($tib,$ordem) {
	$ret = array();
	$stmt = $this->dbh->prepare("select fil.id_ib_pai, fil.valor,tpfil.metanome as tpmeta, ord.valor as ordem
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

    function pegarTodasPorTib($id_tib, $offset = null) {

    	if (!$offset) {
    		$offset = 0;
    	}

    	//Montando filtros din�micos
    	$stmt = $this->dbh->prepare(
    			"WITH RECURSIVE itensBiblioteca as
				(
				(
				SELECT
				IB.*,
				'' as ordem,
				tib.nome as nomeCampo
				FROM    tb_itembiblioteca   as  IB
				JOIN    tp_itembiblioteca   as  tib
				ON  (ib.id_tib = tib.id)
				WHERE   1=1 AND IB.id_tib = ?
				LIMIT   1000  OFFSET  ?
				)
				UNION
				(
				SELECT
				IB2.*,
				tib_ordem.valor as  ordem,
				tib2.metanome   as  nomeCampo
				FROM    tb_itembiblioteca   IB2
				JOIN    itensBiblioteca as  itensBiblioteca
				ON  (IB2.id_ib_pai = itensBiblioteca.id)
				JOIN tp_itembiblioteca  as  tib2
				ON  (IB2.id_tib = tib2.id)
				JOIN
				(
				SELECT  *
				FROM    tp_itembiblioteca_metadata
				WHERE   metanome = 'ws_ordemLista'
				)   as  tib_ordem
				ON  (IB2.id_tib = tib_ordem.id_tib)
				JOIN
				(
				SELECT  *
				FROM    tp_itembiblioteca_metadata
				WHERE   metanome = 'ws_visivel'
				)   as tib_visivel
				ON (IB2.id_tib = tib_visivel.id_tib)
				)
				)

				SELECT
				itensBiblioteca.id_ib_pai,
				json_agg(itensBiblioteca.nomeCampo order by itensBiblioteca.ordem)  as  campos,
				json_agg(itensBiblioteca.valor ORDER BY itensBiblioteca.ordem)      as  valores
				FROM    itensBiblioteca
				WHERE   itensBiblioteca.id_ib_pai is not null
				GROUP   BY  itensBiblioteca.id_ib_pai");

    	$stmt->bindValue(1, $id_tib);
    	$stmt->bindValue(2, $offset);
    	$stmt->execute();

    	$rsItemBiblioteca = $stmt->fetchAll(PDO::FETCH_ASSOC);

    	return $rsItemBiblioteca;
    }
}
?>
