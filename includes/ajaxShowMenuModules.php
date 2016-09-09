<?php

    require_once 'connect.php';

    $idModulo = $_POST['idModulo'];

    $queryMenus = $dbh->prepare("WITH RECURSIVE tb_menu AS
                                (
                                SELECT id, descricao, fluxo, metanome, nome, id_grupo, id_pai, id_tib, visivel, ordem FROM tb_servico WHERE id = :idModulo
                                UNION
                                SELECT sv.id, sv.descricao, sv.fluxo, sv.metanome, sv.nome, sv.id_grupo, sv.id_pai, sv.id_tib, sv.visivel, sv.ordem FROM tb_servico sv JOIN tb_menu mn on ( sv.id_pai = mn.id )
                                )
                                SELECT tb_menu.*, tsm.valor as arquivo, tsi.valor as icone, ts_tab.count as tab,
                                CASE WHEN (tb_menu.id_pai IS NULL) THEN 'PAPAI' ELSE 'FILHO' END AS raiz,
                                CASE WHEN EXISTS ( SELECT 1 FROM tb_servico WHERE tb_servico.id_pai = tb_menu.id AND tb_servico.visivel = TRUE) THEN 1 ELSE 0 END AS tem_filho,
                                CASE WHEN EXISTS ( SELECT 1 FROM tb_servico_metadata WHERE id_servico = tb_menu.id AND metanome = 'ws_comportamento' AND valor = 'tab' ) THEN 1 ELSE 0 END AS aba
                                FROM tb_menu LEFT OUTER JOIN (SELECT * FROM tb_servico_metadata WHERE metanome = 'ws_arquivo' ) AS tsm ON (tb_menu.id = tsm.id_servico)
                                LEFT OUTER JOIN ( select ts.id_pai, count(ts.id) from tb_servico ts join tb_servico_metadata tsm on ( tsm.id_servico = ts.id ) where tsm.metanome = 'ws_comportamento' and tsm.valor = 'tab' group by ts.id_pai ) AS ts_tab ON ( tb_menu.id = ts_tab.id_pai )
                                LEFT OUTER JOIN (SELECT * FROM tb_servico_metadata WHERE metanome = 'ws_icone') as tsi ON ( tb_menu.id = tsi.id_servico )
                                WHERE tb_menu.visivel = TRUE ORDER BY tb_menu.ordem ASC");

    $queryMenus->bindParam(':idModulo', $idModulo);
    $queryMenus->execute();
    $menus = $queryMenus->fetchAll(PDO::FETCH_ASSOC);
    $arOrganizado = array();
    foreach($menus as $key => $value){
        if(empty( $value['id_pai'] ) )
            $arOrganizado['modulo'] = $value;
        else
            $arOrganizado[$value['id_pai']][] = $value;
    }

    echo json_encode($arOrganizado);

?>
