
update tb_servico set descricao = 'Split', nome = 'Split', rota = 'financial/agrupador/split', id_pai = '332f8169-1250-11e6-856b-eb4cd6ecab5b' where id = '91fc55ef-124d-11e6-8567-bfbc96870584';



insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'erp_tipomov', '1', 'b2a57f5a-85ab-47d2-b636-ce303e3905d5');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'erp_tipomov', '2', 'a434e1e5-eb3e-4a4d-a5b5-8da8b340d306');


insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'erp_tipomov', '1', 'b2a57f5a-85ab-47d2-b636-ce303e3905d5');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'erp_tipomov', '1', 'b2a57f5a-85ab-47d2-b636-ce303e3905d5');

-- regras para definicação de tibs do tipo comportamento, paramentros e historico.

-- sendo que comportamento seriam as opções caso algum dos parametros sejam verdadeiros.
-- os parametros seriam Inicialmente OU entre eles. que seja possivel que multiplos parametros (opcoes)
-- historico seria para cada comportamento aplicado em uma selecao de transacoes. podendo ser revertido caso necessário.
-- transferencia entre contas


--insert INTO tb_servico values ('61a506f3-58c5-46cd-bb2d-443a8b5eb9d0', null, 'Regras', null, 'Regras', 'Regras', null, '627e1bac-3986-11e6-b95b-8f633a2a7c75', null, false, null, 'financial/regra/grid');
--
---- edit d37f96fa-e457-4db9-83f0-90fb33bf8ba7
--insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '1ed1cbc0-d628-4390-9bab-354165cf8cf5');
--insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', '1ed1cbc0-d628-4390-9bab-354165cf8cf5');
----update tb_servico set descricao = 'editar plano de conta', nome = 'editar plano de conta' where id = '1ed1cbc0-d628-4390-9bab-354165cf8cf5';
--
---- novo
--insert into tb_servico values ('1608ba0c-ba75-45b7-b517-ba96c8dd8f16', null, 'novo plano de contas', null, 'novo plano de contas', 'novo', null, '3d019666-f419-430b-a024-896bb60cc823', null, false, null, 'financial/plano-contas/form');
--insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', 'c1c527bd-79ec-447f-970f-a563e84aa4cd');
--insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', 'c1c527bd-79ec-447f-970f-a563e84aa4cd');
--
--
---- delete
----update tb_servico set descricao = 'Deletar Centro de Custo', nome = 'Deletar Centro de Custo', rota = 'financial/centro-custo/delete' where id = '34131224-3cbd-4557-9531-288f00bde114';
--insert into tb_servico values ('373a9245-85df-44d7-89f2-5dc2d093ed27', null, 'Deletar', null, '', 'novo', null, '3d019666-f419-430b-a024-896bb60cc823', null, false, null, 'financial/plano-contas/form');
--insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '373a9245-85df-44d7-89f2-5dc2d093ed27');
--insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', '373a9245-85df-44d7-89f2-5dc2d093ed27');
--insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_confirm', 'você tem certeza que deseja excluir?', '373a9245-85df-44d7-89f2-5dc2d093ed27');
--insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'fa fa-trash', '373a9245-85df-44d7-89f2-5dc2d093ed27');




