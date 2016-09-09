<?php
/**
 * User: ericcomcmudo
 * Date: 17/12/15
 * Time: 14:36
 */

class TpItemBiblioteca extends Base
{
    
    function getById($id) {
        $stmt = $this->dbh->prepare(
                "SELECT *
                FROM    tp_itembiblioteca
                WHERE   id = :id");
        $stmt->bindValue('id', $id);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
    }


    /**
     * @param $idTibPai
     * @return array
     */

    function getTemplateById($id)
    {
        $stmt = $this->dbh->prepare(
                "SELECT item.*
                FROM    tp_itembiblioteca   AS item
                INNER   JOIN    tp_itembiblioteca_metadata AS meta
                    ON  item.id = meta.id_tib
                WHERE   item.id = :id and meta.metanome = 'ws_ordem'
                GROUP   BY  item.id, meta.valor::int
                ORDER   BY  meta.valor::int");
        $stmt->bindValue('id', $id);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
    }
    
    function getTemplateByIdTibPai($id_tib_pai)
    {
        $stmt = $this->dbh->prepare(
                "SELECT item.*, json_object (array_agg( meta.metanome ), array_agg( meta.valor) ) as metadatas, ordem.valor as ordem
                FROM    tp_itembiblioteca       AS item
                JOIN    tp_itembiblioteca_metadata AS meta
                    ON  item.id = meta.id_tib
                JOIN    (select * from tp_itembiblioteca_metadata where metanome = 'ws_ordem') AS ordem
                        ON item.id = ordem.id_tib
                WHERE   item.id_tib_pai = :id_tib_pai
                GROUP BY item.id, ordem.valor
                ORDER BY ordem.valor::INT");
        $stmt->bindValue('id_tib_pai', $id_tib_pai);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
    }

    function getTemplateCabecalho($id_tib_pai)
    {
        $stmt = $this->dbh->prepare(
                "SELECT item.*, json_object (array_agg( meta.metanome ), array_agg( meta.valor) ) as metadatas, ordem.valor as ordem
                FROM    tp_itembiblioteca       AS item
                JOIN    tp_itembiblioteca_metadata AS meta
                    ON  item.id = meta.id_tib
                JOIN    (select * from tp_itembiblioteca_metadata where metanome = 'ws_ordemLista') AS ordem
                        ON item.id = ordem.id_tib
                WHERE   item.id_tib_pai = :id_tib_pai
                GROUP BY item.id, ordem.valor
                ORDER BY ordem.valor::INT");
        $stmt->bindValue('id_tib_pai', $id_tib_pai);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
    }
    
    function getTemplateByIdTibIrmao($id_tib)
    {
        //pegando pai
        $stmt = $this->dbh->prepare(
                "SELECT id_tib_pai
                FROM    tp_itembiblioteca
                WHERE   id = :id");
        $stmt->bindValue('id', $id_tib);
        $stmt->execute();
                
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
    }
    
    function  getTpItemBibliotecaBy( $arrCampo ){
        
        $where      =   "";
        $contador   =   0;
        $arrValores =   array();
        foreach ( $arrCampo as $campo => $valor){
            $contador++;
            if ( $contador == 1 ){
                $where  .= " " . $campo . " = ?";
                $arrValores[ $contador ]    =   $valor;
            } else {
                $where  .= " AND " . $campo . " = ?";
                $arrValores[ $contador ]    =   $valor;             
            }           
        }
        
        $stmt = $this->dbh->prepare(
                "SELECT *
                FROM    tp_itembiblioteca
                WHERE   " . $where);
        
        foreach ( $arrValores as $chave => $valor ){
            $stmt->bindValue( $chave , $valor);
        }
        
        $stmt->execute();
        
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $rsTemplate;
    }
    
    function getIdTibByMetanome($metanome)
    {
    	
        $stmt = $this->dbh->prepare(
                "SELECT id
                FROM    tp_itembiblioteca   
                WHERE   lower(trim(metanome)) = lower(:meta)");
        $stmt->bindValue('meta', $metanome);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
        if (!isset( $rsTemplate[0])){
        	x($metanome);
        }
        return $rsTemplate[0]['id'];
    }

    function getCampoPadrao($id) {
        $stmt = $this->dbh->prepare(
                "SELECT *
                FROM    tp_itembiblioteca
                WHERE   id_tib_pai = :id
                AND padrao = true");
        $stmt->bindValue('id', $id);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
    }

    function getMetanomesByTIB($tib){
        $stmt = $this->dbh->prepare("select * from tp_itembiblioteca_metadata where id_tib = :tib");
        $stmt->bindParam(':tib',$tib);
        $stmt->execute();

        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }
    
}

?>