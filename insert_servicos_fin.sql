


insert into tb_servico VALUES ('7a406a06-7548-49a9-9037-bc8556489f16', null, 'Módulo Financeiro', null, 'Financeiro', 'Financeiro', null, null, null, true, null, null);

insert into tb_servico VALUES ('da82e4da-1348-44df-a864-27e0ff816dc8', null, 'Pesquisar Tickets Financeiros', null, 'Pesquisar Tickets Financeiros', 'Pesquisar Tickets Financeiros', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, true, 1, 'financial/financial/index');
insert into tb_servico VALUES ('596c5c9c-7e82-4d0d-969e-8b24d29deeee', null, 'Registros de transações', null, 'Registros de transações', 'Registros de transações', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, true, 2, 'financial/agrupador-financeiro/grid');
insert into tb_servico VALUES ('e25d3608-2df3-4d6e-8259-2df96b707673', null, 'Extrato de Contas', null, 'Extrato de Contas', 'Extrato de Contas', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, true, 3, 'financial/contas/extrato-form');
insert into tb_servico VALUES ('3d019666-f419-430b-a024-896bb60cc823', null, 'Plano de Contas', null, 'Plano de Contas', 'Plano de Contas', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, true, 4, 'financial/plano-contas/grid');
insert into tb_servico VALUES ('411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, 'Contas', null, 'Contas', 'Contas', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, true, 5, 'financial/contas/grid');
insert into tb_servico VALUES ('0e86b641-85bf-43e9-8c1c-3dbc6878a26b', null, 'Crédito para Corporativos', null, 'Crédito para Corporativos', 'Crédito para Corporativos', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, true, 6, 'financial/credito/grid');
insert into tb_servico VALUES ('def80dcd-dcf8-485f-88e7-6d344385a200', null, 'Gerenciador Financeiro', null, 'Gerenciador Financeiro', 'Gerenciador Financeiro', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, true, 7, 'financial/gerenciador-financeiro/index');
insert into tb_servico VALUES ('35592689-c21e-41b5-a14f-4a4a82fba7ce', null, 'Centro de Custo', null, 'Centro de Custo', 'Centro de Custo', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, true, 8, 'service/centro-custo/index');


insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/agrupador-financeiro/grid-financial-processo-ajax', null, 'financial/agrupador-financeiro/grid-financial-processo-ajax', 'financial/agrupador-financeiro/grid-financial-processo-ajax', null, '596c5c9c-7e82-4d0d-969e-8b24d29deeee', null, false, null, 'financial/agrupador-financeiro/grid-financial-processo-ajax');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/agrupador-financeiro/save-transfer', null, 'financial/agrupador-financeiro/save-transfer', 'financial/agrupador-financeiro/save-transfer', null, '596c5c9c-7e82-4d0d-969e-8b24d29deeee', null, false, null, 'financial/agrupador-financeiro/save-transfer');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/agrupador-financeiro/delete', null, 'financial/agrupador-financeiro/delete', 'financial/agrupador-financeiro/delete', null, '596c5c9c-7e82-4d0d-969e-8b24d29deeee', null, false, null, 'financial/agrupador-financeiro/delete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/agrupador-financeiro/duplicar-transacao', null, 'financial/agrupador-financeiro/duplicar-transacao', 'financial/agrupador-financeiro/duplicar-transacao', null, '596c5c9c-7e82-4d0d-969e-8b24d29deeee', null, false, null, 'financial/agrupador-financeiro/duplicar-transacao');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/agrupador-financeiro/historico', null, 'financial/agrupador-financeiro/historico', 'financial/agrupador-financeiro/historico', null, '596c5c9c-7e82-4d0d-969e-8b24d29deeee', null, false, null, 'financial/agrupador-financeiro/historico');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/agrupador-financeiro/autocomplete', null, 'financial/agrupador-financeiro/autocomplete', 'financial/agrupador-financeiro/autocomplete', null, '596c5c9c-7e82-4d0d-969e-8b24d29deeee', null, false, null, 'financial/agrupador-financeiro/autocomplete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/agrupador-financeiro/form', null, 'financial/agrupador-financeiro/form', 'financial/agrupador-financeiro/form', null, '596c5c9c-7e82-4d0d-969e-8b24d29deeee', null, false, null, 'financial/agrupador-financeiro/form');


insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/contas/extrato-form', null, 'financial/contas/extrato-form', 'financial/contas/extrato-form', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/extrato-form');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/contas/extrato-view', null, 'financial/contas/extrato-view', 'financial/contas/extrato-view', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/extrato-view');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/contas/extrato-pdf', null, 'financial/contas/extrato-pdf', 'financial/contas/extrato-pdf', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/extrato-pdf');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/contas/autocomplete', null, 'financial/contas/autocomplete', 'financial/contas/autocomplete', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/autocomplete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/contas/get', null, 'financial/contas/get', 'financial/contas/get', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/get');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/contas/delete', null, 'financial/contas/delete', 'financial/contas/delete', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/delete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/contas/form', null, 'financial/contas/form', 'financial/contas/form', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/form');


insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/credito/quick-search-ajax', null, 'financial/credito/quick-search-ajax', 'financial/credito/quick-search-ajax', null, '0e86b641-85bf-43e9-8c1c-3dbc6878a26b', null, false, null, 'financial/credito/quick-search-ajax');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/credito/limite-by-empresa-ajax', null, 'financial/credito/limite-by-empresa-ajax', 'financial/credito/limite-by-empresa-ajax', null, '0e86b641-85bf-43e9-8c1c-3dbc6878a26b', null, false, null, 'financial/credito/limite-by-empresa-ajax');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/credito/delete', null, 'financial/credito/delete', 'financial/credito/delete', null, '0e86b641-85bf-43e9-8c1c-3dbc6878a26b', null, false, null, 'financial/credito/delete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/credito/autocomplete', null, 'financial/credito/autocomplete', 'financial/credito/autocomplete', null, '0e86b641-85bf-43e9-8c1c-3dbc6878a26b', null, false, null, 'financial/credito/autocomplete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/credito/form', null, 'financial/credito/form', 'financial/credito/form', null, '0e86b641-85bf-43e9-8c1c-3dbc6878a26b', null, false, null, 'financial/credito/form');


insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/grid', null, 'financial/financial/grid', 'financial/financial/grid', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/grid');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/recibo', null, 'financial/financial/recibo', 'financial/financial/recibo', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/recibo');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/save-financial-from-processo-ajax', null, 'financial/financial/save-financial-from-processo-ajax', 'financial/financial/save-financial-from-processo-ajax', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/save-financial-from-processo-ajax');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/print', null, 'financial/financial/print', 'financial/financial/print', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/print');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/get', null, 'financial/financial/get', 'financial/financial/get', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/get');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/get-financial-per-models', null, 'financial/financial/get-financial-per-models', 'financial/financial/get-financial-per-models', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/get-financial-per-models');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/get-financial-with-processo-incompatible-per-models', null, 'financial/financial/get-financial-with-processo-incompatible-per-models', 'financial/financial/get-financial-with-processo-incompatible-per-models', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/get-financial-with-processo-incompatible-per-models');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/grid-pdf', null, 'financial/financial/grid-pdf', 'financial/financial/grid-pdf', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/grid-pdf');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/duplicar-tks-ajax', null, 'financial/financial/duplicar-tks-ajax', 'financial/financial/duplicar-tks-ajax', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/duplicar-tks-ajax');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/historico', null, 'financial/financial/historico', 'financial/financial/historico', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/historico');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/grid-editable', null, 'financial/financial/grid-editable', 'financial/financial/grid-editable', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/grid-editable');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/grid-ajax', null, 'financial/financial/grid-ajax', 'financial/financial/grid-ajax', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/grid-ajax');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/delete', null, 'financial/financial/delete', 'financial/financial/delete', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/delete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/autocomplete', null, 'financial/financial/autocomplete', 'financial/financial/autocomplete', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/autocomplete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/financial/form', null, 'financial/financial/form', 'financial/financial/form', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/form');


insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/gerenciador-financeiro/grid', null, 'financial/gerenciador-financeiro/grid', 'financial/gerenciador-financeiro/grid', null, 'def80dcd-dcf8-485f-88e7-6d344385a200', null, false, null, 'financial/gerenciador-financeiro/grid');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/gerenciador-financeiro/consolidated-an-account-ajax', null, 'financial/gerenciador-financeiro/consolidated-an-account-ajax', 'financial/gerenciador-financeiro/consolidated-an-account-ajax', null, 'def80dcd-dcf8-485f-88e7-6d344385a200', null, false, null, 'financial/gerenciador-financeiro/consolidated-an-account-ajax');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/gerenciador-financeiro/delete', null, 'financial/gerenciador-financeiro/delete', 'financial/gerenciador-financeiro/delete', null, 'def80dcd-dcf8-485f-88e7-6d344385a200', null, false, null, 'financial/gerenciador-financeiro/delete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/gerenciador-financeiro/autocomplete', null, 'financial/gerenciador-financeiro/autocomplete', 'financial/gerenciador-financeiro/autocomplete', null, 'def80dcd-dcf8-485f-88e7-6d344385a200', null, false, null, 'financial/gerenciador-financeiro/autocomplete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/gerenciador-financeiro/form', null, 'financial/gerenciador-financeiro/form', 'financial/gerenciador-financeiro/form', null, 'def80dcd-dcf8-485f-88e7-6d344385a200', null, false, null, 'financial/gerenciador-financeiro/form');

--financial/moeda/get
--financial/moeda/delete
--financial/moeda/autocomplete
--financial/moeda/form

insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/plano-contas/get-pairs-per-type', null, 'financial/plano-contas/get-pairs-per-type', 'financial/plano-contas/get-pairs-per-type', null, '3d019666-f419-430b-a024-896bb60cc823', null, false, null, 'financial/plano-contas/get-pairs-per-type');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/plano-contas/quick-search-ajax', null, 'financial/plano-contas/quick-search-ajax', 'financial/plano-contas/quick-search-ajax', null, '3d019666-f419-430b-a024-896bb60cc823', null, false, null, 'financial/plano-contas/quick-search-ajax');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/plano-contas/delete', null, 'financial/plano-contas/delete', 'financial/plano-contas/delete', null, '3d019666-f419-430b-a024-896bb60cc823', null, false, null, 'financial/plano-contas/delete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/plano-contas/autocomplete', null, 'financial/plano-contas/autocomplete', 'financial/plano-contas/autocomplete', null, '3d019666-f419-430b-a024-896bb60cc823', null, false, null, 'financial/plano-contas/autocomplete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'financial/plano-contas/form', null, 'financial/plano-contas/form', 'financial/plano-contas/form', null, '3d019666-f419-430b-a024-896bb60cc823', null, false, null, 'financial/plano-contas/form');


insert into tb_servico VALUES (uuid_generate_v4(), null, 'service/centro-custo/form', null, 'service/centro-custo/form', 'service/centro-custo/form', null, '35592689-c21e-41b5-a14f-4a4a82fba7ce', null, false, null, 'service/centro-custo/form');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'service/centro-custo/delete', null, 'service/centro-custo/delete', 'service/centro-custo/delete', null, '35592689-c21e-41b5-a14f-4a4a82fba7ce', null, false, null, 'service/centro-custo/delete');
insert into tb_servico VALUES (uuid_generate_v4(), null, 'service/centro-custo/autocomplete', null, 'service/centro-custo/autocomplete', 'service/centro-custo/autocomplete', null, '35592689-c21e-41b5-a14f-4a4a82fba7ce', null, false, null, 'service/centro-custo/autocomplete');


BEGIN
;

INSERT INTO rl_grupo_servico (ID, id_grupo, id_servico)
VALUES
  (
    uuid_generate_v4 (),
    '7d2a002e-308a-42b9-b7ec-a59a6b7b4c50',
    '7a406a06-7548-49a9-9037-bc8556489f16'
  );

INSERT INTO rl_grupo_servico (ID, id_grupo, id_servico)
VALUES
  (
    uuid_generate_v4 (),
    '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6',
    '7a406a06-7548-49a9-9037-bc8556489f16'
  );



INSERT INTO rl_permissao_pessoa (
  ID,
  id_grupo,
  id_pessoa,
  id_servico,
  dt_expiracao
) SELECT
  uuid_generate_v4 (),
  '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6',
  '962d0e69-383a-4b1f-9531-6a612cc1f124',
  id,
  '2020-01-01'
  FROM
    tb_servico
  WHERE
      ID IN (
        'da82e4da-1348-44df-a864-27e0ff816dc8',
        '596c5c9c-7e82-4d0d-969e-8b24d29deeee',
        'e25d3608-2df3-4d6e-8259-2df96b707673',
        '3d019666-f419-430b-a024-896bb60cc823',
        '411dc5e4-6ebd-4814-9e12-f24f9288aeed',
        '0e86b641-85bf-43e9-8c1c-3dbc6878a26b',
        'def80dcd-dcf8-485f-88e7-6d344385a200',
        '35592689-c21e-41b5-a14f-4a4a82fba7ce'
      )
    OR id_pai IN (
      'da82e4da-1348-44df-a864-27e0ff816dc8',
      '596c5c9c-7e82-4d0d-969e-8b24d29deeee',
      'e25d3608-2df3-4d6e-8259-2df96b707673',
      '3d019666-f419-430b-a024-896bb60cc823',
      '411dc5e4-6ebd-4814-9e12-f24f9288aeed',
      '0e86b641-85bf-43e9-8c1c-3dbc6878a26b',
      'def80dcd-dcf8-485f-88e7-6d344385a200',
      '35592689-c21e-41b5-a14f-4a4a82fba7ce'
    );

INSERT INTO rl_permissao_pessoa (
  ID,
  id_grupo,
  id_pessoa,
  id_servico,
  dt_expiracao
)
VALUES
  (
    uuid_generate_v4 (),
    '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6',
    '962d0e69-383a-4b1f-9531-6a612cc1f124',
    '7a406a06-7548-49a9-9037-bc8556489f16',
    '2020-01-01'
  );

INSERT INTO rl_permissao_pessoa (
  ID,
  id_grupo,
  id_pessoa,
  id_servico,
  dt_expiracao
) SELECT
  uuid_generate_v4 (),
  '7d2a002e-308a-42b9-b7ec-a59a6b7b4c50',
  '962d0e69-383a-4b1f-9531-6a612cc1f124',
  id,
  '2020-01-01'
FROM
  tb_servico
WHERE
    ID IN (
      'da82e4da-1348-44df-a864-27e0ff816dc8',
      '596c5c9c-7e82-4d0d-969e-8b24d29deeee',
      'e25d3608-2df3-4d6e-8259-2df96b707673',
      '3d019666-f419-430b-a024-896bb60cc823',
      '411dc5e4-6ebd-4814-9e12-f24f9288aeed',
      '0e86b641-85bf-43e9-8c1c-3dbc6878a26b',
      'def80dcd-dcf8-485f-88e7-6d344385a200',
      '35592689-c21e-41b5-a14f-4a4a82fba7ce'
    )
  OR id_pai IN (
    'da82e4da-1348-44df-a864-27e0ff816dc8',
    '596c5c9c-7e82-4d0d-969e-8b24d29deeee',
    'e25d3608-2df3-4d6e-8259-2df96b707673',
    '3d019666-f419-430b-a024-896bb60cc823',
    '411dc5e4-6ebd-4814-9e12-f24f9288aeed',
    '0e86b641-85bf-43e9-8c1c-3dbc6878a26b',
    'def80dcd-dcf8-485f-88e7-6d344385a200',
    '35592689-c21e-41b5-a14f-4a4a82fba7ce'
  );

INSERT INTO rl_permissao_pessoa (
  ID,
  id_grupo,
  id_pessoa,
  id_servico,
  dt_expiracao
)
VALUES
  (
    uuid_generate_v4 (),
    '7d2a002e-308a-42b9-b7ec-a59a6b7b4c50',
    '962d0e69-383a-4b1f-9531-6a612cc1f124',
    '7a406a06-7548-49a9-9037-bc8556489f16',
    '2020-01-01'
  );

update tb_servico set rota = null where rota = 'legacy';




-- auto complete empresa
insert into tb_servico VALUES ('8b98d1fb-72ee-4f25-a767-296f9146ad22', null, 'empresa/empresa/autocomplete', null, 'empresa/empresa/autocomplete', 'empresa/empresa/autocomplete', null, '7a406a06-7548-49a9-9037-bc8556489f16', null, false, null, 'empresa/empresa/autocomplete');
INSERT INTO rl_permissao_pessoa (
  ID,
  id_grupo,
  id_pessoa,
  id_servico,
  dt_expiracao
)
VALUES
  (
    uuid_generate_v4 (),
    '7d2a002e-308a-42b9-b7ec-a59a6b7b4c50',
    '962d0e69-383a-4b1f-9531-6a612cc1f124',
    '8b98d1fb-72ee-4f25-a767-296f9146ad22',
    '2020-01-01'
  );
INSERT INTO rl_permissao_pessoa (
  ID,
  id_grupo,
  id_pessoa,
  id_servico,
  dt_expiracao
)
VALUES
  (
    uuid_generate_v4 (),
    '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6',
    '962d0e69-383a-4b1f-9531-6a612cc1f124',
    '8b98d1fb-72ee-4f25-a767-296f9146ad22',
    '2020-01-01'
  );


update tb_servico set rota = null where rota = 'legacy';
