
update tb_servico set  rota = 'financial/transacao-conta/form' where id = 'a41fec88-4568-4415-a19e-05d41a93ba0d'; -- Adicionar Crédito
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'erp_tipomov', '2', 'a41fec88-4568-4415-a19e-05d41a93ba0d');


update tb_servico set rota = 'financial/transacao-conta/form' where id = '52bb6782-c364-4356-a35f-0034a1e26a8b'; -- Adicionar Débito
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'erp_tipomov', '1', '52bb6782-c364-4356-a35f-0034a1e26a8b');

update tb_servico_metadata set valor = 'listaction' where id_servico = 'a41fec88-4568-4415-a19e-05d41a93ba0d' and metanome = 'ws_comportamento'; -- update Crédito
update tb_servico_metadata set valor = 'listaction' where id_servico = '52bb6782-c364-4356-a35f-0034a1e26a8b' and metanome = 'ws_comportamento'; -- update Débito


insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-arrow-down fontVerde', '52bb6782-c364-4356-a35f-0034a1e26a8b');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-arrow-up fontVermelha', 'a41fec88-4568-4415-a19e-05d41a93ba0d');


insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-remove', '91fc55ed-124d-11e6-8565-07be5ded5709');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-remove', '91fc55ee-124d-11e6-8566-af4b00d1ba65');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-remove', '91fc55ef-124d-11e6-8567-bfbc96870584');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-remove', '91fc55f0-124d-11e6-8568-4fe716064a5a');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-remove', '91fc55f1-124d-11e6-8569-6f1639000a12');



insert INTO tb_servico values ('2ec37814-6302-42bb-b069-80ee3ee999b2', null, 'Editar', null, 'Editar', 'Editar', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/agrupador/form');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '2ec37814-6302-42bb-b069-80ee3ee999b2');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '2ec37814-6302-42bb-b069-80ee3ee999b2');

-- @todo

insert INTO tb_servico values ('b4e180ba-b61f-420c-b4fc-692b330fda00', null, 'Importar Nota Fiscal', null, 'Importar Nota Fiscal', 'Importar Nota Fiscal', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/agrupador/import');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', 'b4e180ba-b61f-420c-b4fc-692b330fda00');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', 'b4e180ba-b61f-420c-b4fc-692b330fda00');


insert INTO tb_servico values ('b8f87004-5919-44e2-8c83-6cbd6a1fde9c', null, 'Autocomplete Pessoa', null, 'Autocomplete Pessoa', 'Autocomplete Pessoa', null, '2ec37814-6302-42bb-b069-80ee3ee999b2', null, false, null, 'content/pessoa/autocomplete');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'filter', 'b8f87004-5919-44e2-8c83-6cbd6a1fde9c');


insert into tb_metadata VALUES ('ws_field', 'ws_field', 'serve para indica qual nome da variavel de id passado', 'text', 't', null, now());
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'con_id', 'a41fec88-4568-4415-a19e-05d41a93ba0d');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'con_id', '52bb6782-c364-4356-a35f-0034a1e26a8b');


update tb_servico set descricao = 'Conciliação', nome = 'Conciliação', rota = 'financial/transacao-conta/grid' where id = '332f8169-1250-11e6-856b-eb4cd6ecab5b';
update tb_servico_metadata set valor = 'listaction' where id_servico = '332f8169-1250-11e6-856b-eb4cd6ecab5b' and metanome = 'ws_comportamento';
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-transfer', '332f8169-1250-11e6-856b-eb4cd6ecab5b');


