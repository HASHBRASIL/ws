
insert into tb_metadata VALUES ('erp_tipomov', 'erp_tipomov', '', 'text', 't', null, now());

update tb_servico set descricao = 'Transações', nome = 'Transações', rota = 'financial/agrupador/grid' where id = 'da82e4da-1348-44df-a864-27e0ff816dc8'; -- antigo index

update tb_servico set descricao = 'Adicionar Despesa', nome = 'Adicionar Despesa', rota = 'financial/agrupador/form/despesa' where id = 'b2a57f5a-85ab-47d2-b636-ce303e3905d5'; -- antigo grid
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', 'b2a57f5a-85ab-47d2-b636-ce303e3905d5');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', 'b2a57f5a-85ab-47d2-b636-ce303e3905d5');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'erp_tipomov', '1', 'b2a57f5a-85ab-47d2-b636-ce303e3905d5');

update tb_servico set descricao = 'Adicionar Receita', nome = 'Adicionar Receita', rota = 'financial/agrupador/form/receita' where id = 'a434e1e5-eb3e-4a4d-a5b5-8da8b340d306'; -- antigo form
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', 'a434e1e5-eb3e-4a4d-a5b5-8da8b340d306');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', 'a434e1e5-eb3e-4a4d-a5b5-8da8b340d306');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'erp_tipomov', '2', 'a434e1e5-eb3e-4a4d-a5b5-8da8b340d306');


insert INTO tb_servico values ('76d2e14e-908a-478c-9987-96abe3a289a2', null, 'Autocomplete Pessoa', null, 'Autocomplete Pessoa', 'Autocomplete Pessoa', null, 'b2a57f5a-85ab-47d2-b636-ce303e3905d5', null, false, null, 'content/pessoa/autocomplete');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'filter', '76d2e14e-908a-478c-9987-96abe3a289a2');

insert INTO tb_servico values ('7401ad56-9c37-4692-a602-c5f6a9c86371', null, 'Autocomplete Pessoa', null, 'Autocomplete Pessoa', 'Autocomplete Pessoa', null, 'a434e1e5-eb3e-4a4d-a5b5-8da8b340d306', null, false, null, 'content/pessoa/autocomplete');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'filter', '7401ad56-9c37-4692-a602-c5f6a9c86371');


insert INTO tb_servico values ('a41fec88-4568-4415-a19e-05d41a93ba0d', null, 'Adicionar Crédito', null, 'Adicionar Crédito', 'Adicionar Crédito', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/credito');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', 'a41fec88-4568-4415-a19e-05d41a93ba0d');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', 'a41fec88-4568-4415-a19e-05d41a93ba0d');

insert INTO tb_servico values ('52bb6782-c364-4356-a35f-0034a1e26a8b', null, 'Adicionar Débito', null, 'Adicionar Débito', 'Adicionar Débito', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/debito');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', '52bb6782-c364-4356-a35f-0034a1e26a8b');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '52bb6782-c364-4356-a35f-0034a1e26a8b');

insert INTO tb_servico values ('332f816a-1250-11e6-856c-8fca70b64598', null, 'Adicionar Transferência entre Contas', null, 'Adicionar Transferência entre Contas', 'Adicionar Transferência entre Contas', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/transferencia');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', '332f816a-1250-11e6-856c-8fca70b64598');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '332f816a-1250-11e6-856c-8fca70b64598');

insert INTO tb_servico values ('e24f7e50-1250-11e6-856d-1b1959ce10bb', null, 'Importação de Arquivo', null, 'Importação de Arquivo', 'Importação de Arquivo', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/importacao');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', 'e24f7e50-1250-11e6-856d-1b1959ce10bb');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', 'e24f7e50-1250-11e6-856d-1b1959ce10bb');


insert INTO tb_servico values ('332f8168-1250-11e6-856a-0f79ed64b8fe', null, 'Deletar', null, 'Deletar', 'Deletar', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/deletar');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'multiaction', '332f8168-1250-11e6-856a-0f79ed64b8fe');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '332f8168-1250-11e6-856a-0f79ed64b8fe');

insert INTO tb_servico values ('332f8169-1250-11e6-856b-eb4cd6ecab5b', null, 'Transferir', null, 'Transferir', 'Transferir', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/transferir');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'multiaction', '332f8169-1250-11e6-856b-eb4cd6ecab5b');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '332f8169-1250-11e6-856b-eb4cd6ecab5b');


insert INTO tb_servico values ('91fc55e8-124d-11e6-8560-cb9df3eee3f3', null, 'Agrupar', null, 'Agrupar', 'Agrupar', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/merge');
insert INTO tb_servico values ('91fc55e9-124d-11e6-8561-9f39c8e0c654', null, 'Consolidar', null, 'Consolidar', 'Consolidar', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/verify');
insert INTO tb_servico values ('91fc55ea-124d-11e6-8562-17d3190173bb', null, 'Transferir', null, 'Transferir', 'Transferir', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/transfer');
insert INTO tb_servico values ('91fc55eb-124d-11e6-8563-cfa75dd2995b', null, 'Deletar', null, 'Deletar', 'Deletar', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/delete');
insert INTO tb_servico values ('91fc55ec-124d-11e6-8564-eba1208a7fed', null, 'Classificar', null, 'Classificar', 'Classificar', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/categorize');
insert INTO tb_servico values ('91fc55ed-124d-11e6-8565-07be5ded5709', null, 'Consolidar', null, 'Consolidar', 'Consolidar', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/verify');
insert INTO tb_servico values ('91fc55ee-124d-11e6-8566-af4b00d1ba65', null, 'Split', null, 'Split', 'Split', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/split');
insert INTO tb_servico values ('91fc55ef-124d-11e6-8567-bfbc96870584', null, 'Recibo', null, 'Recibo', 'Recibo', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/recibo');
insert INTO tb_servico values ('91fc55f0-124d-11e6-8568-4fe716064a5a', null, 'Mover para Pessoal', null, 'Mover para Pessoal', 'Mover para Pessoal', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/personal');
insert INTO tb_servico values ('91fc55f1-124d-11e6-8569-6f1639000a12', null, 'Deletar', null, 'Deletar', 'Deletar', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/delete');

insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'multiaction', '91fc55e8-124d-11e6-8560-cb9df3eee3f3');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'multiaction', '91fc55e9-124d-11e6-8561-9f39c8e0c654');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'multiaction', '91fc55ea-124d-11e6-8562-17d3190173bb');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'multiaction', '91fc55eb-124d-11e6-8563-cfa75dd2995b');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'multiaction', '91fc55ec-124d-11e6-8564-eba1208a7fed');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '91fc55ed-124d-11e6-8565-07be5ded5709');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '91fc55ee-124d-11e6-8566-af4b00d1ba65');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '91fc55ef-124d-11e6-8567-bfbc96870584');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '91fc55f0-124d-11e6-8568-4fe716064a5a');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '91fc55f1-124d-11e6-8569-6f1639000a12');

insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55e8-124d-11e6-8560-cb9df3eee3f3');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55e9-124d-11e6-8561-9f39c8e0c654');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55ea-124d-11e6-8562-17d3190173bb');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55eb-124d-11e6-8563-cfa75dd2995b');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55ec-124d-11e6-8564-eba1208a7fed');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55ed-124d-11e6-8565-07be5ded5709');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55ee-124d-11e6-8566-af4b00d1ba65');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55ef-124d-11e6-8567-bfbc96870584');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55f0-124d-11e6-8568-4fe716064a5a');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '91fc55f1-124d-11e6-8569-6f1639000a12');




-- insert INTO tb_servico values ('1f53e06e-b09c-4dea-b205-f7ee5e57ebbd', null, 'Agrupar', null, 'Agrupar', 'Agrupar', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/financial/merge');
