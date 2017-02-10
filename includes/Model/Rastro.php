<?php
/**
 * @Author: toinsane
 * @Date:   2016-01-29 14:40:26
 * @Last Modified by:   toinsane
 * @Last Modified time: 2016-01-29 15:28:40
 */

 Class Rastro extends Base
 {
    function getPath ( $currentServico ) {
            $stmt = $this->dbh->prepare(
    "WITH RECURSIVE rastro ( id, nome, id_pai ) AS (
          SELECT ts.id, ts.nome, ts.id_pai FROM tb_servico ts WHERE ts.id = :id_servico
    UNION
          SELECT ts1.id, ts1.nome, ts1.id_pai FROM tb_servico ts1 JOIN rastro ras ON ( ts1.id = ras.id_pai )
    )
    SELECT * FROM rastro");

        $stmt->bindParam(':id_servico', $currentServico);
        $stmt->execute();
        $rastro     = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $arrInverso = array_reverse($rastro);

        return $arrInverso;
    }
 }