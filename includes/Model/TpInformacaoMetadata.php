<?php

    class TpInformacaoMetadata extends Base {
        function getMetadatasByTpinf ($id) {
            $stmt = $this->dbh->prepare(
                "SELECT *
                FROM    tp_informacao_metadata
                WHERE   id_tpinfo = :id");
        $stmt->bindValue('id', $id);
        $stmt->execute();
    
        $rsTemplate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $rsTemplate;
        }
    }

