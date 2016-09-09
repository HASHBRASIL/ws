
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '3c106668-2857-421f-bcd3-4d956fbd5d33');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', '3c106668-2857-421f-bcd3-4d956fbd5d33');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_confirm', 'você tem certeza que deseja excluir?', '3c106668-2857-421f-bcd3-4d956fbd5d33');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'fa fa-trash', '3c106668-2857-421f-bcd3-4d956fbd5d33');
update tb_servico set descricao = 'deletar plano de contas', nome = 'deletar' where id = '1ed1cbc0-d628-4390-9bab-354165cf8cf5';

-- edit
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '1ed1cbc0-d628-4390-9bab-354165cf8cf5');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', '1ed1cbc0-d628-4390-9bab-354165cf8cf5');
update tb_servico set descricao = 'editar plano de conta', nome = 'editar plano de conta' where id = '1ed1cbc0-d628-4390-9bab-354165cf8cf5';

-- novo
insert into tb_servico values ('c1c527bd-79ec-447f-970f-a563e84aa4cd', null, 'novo plano de contas', null, 'novo plano de contas', 'novo', null, '3d019666-f419-430b-a024-896bb60cc823', null, false, null, 'financial/plano-contas/form');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', 'c1c527bd-79ec-447f-970f-a563e84aa4cd');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', 'c1c527bd-79ec-447f-970f-a563e84aa4cd');



-- grupo de contas
insert into fin_tb_grupo_contas values (1, 'receita', 1);
insert into fin_tb_grupo_contas values (2, 'despesa', 1);

--3d019666-f419-430b-a024-896bb60cc823   plano de contas
--1aee6791-9802-4af9-b65a-50937236a5e6   financial/plano-contas/get-pairs-per-type
--a5af8b44-271e-4db4-b6a8-dc79709d0e32   financial/plano-contas/quick-search-ajax
--
--   financial/plano-contas/delete
--32760e34-ea1e-4f17-965a-1da82e77d0c2   financial/plano-contas/autocomplete
--1ed1cbc0-d628-4390-9bab-354165cf8cf5   financial/plano-contas/form
--
--
--
--35592689-c21e-41b5-a14f-4a4a82fba7ce
--21054d01-52fa-47b1-b9b4-64b1d9e4da2a
--34131224-3cbd-4557-9531-288f00bde114
--ba198384-0ce4-4efd-ac8c-4b397a5087f1


-- centor de custo
insert INTO tb_servico values ('665999f1-718f-4573-82e0-cc3151ad7986', null, 'novo centro de custo', null, 'novo centro de custo', 'novo', null, '35592689-c21e-41b5-a14f-4a4a82fba7ce', null, false, null, 'financial/centro-custo/form');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', '665999f1-718f-4573-82e0-cc3151ad7986');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', '665999f1-718f-4573-82e0-cc3151ad7986');


update tb_servico set descricao = 'Centro de Custo', nome = ' Centro de Custo', rota = 'financial/centro-custo/grid' where id = '35592689-c21e-41b5-a14f-4a4a82fba7ce';
update tb_servico set descricao = 'Editar Centro de Custo', nome = 'Editar Centro de Custo', rota = 'financial/centro-custo/form' where id = '21054d01-52fa-47b1-b9b4-64b1d9e4da2a';
update tb_servico set descricao = 'Deletar Centro de Custo', nome = 'Deletar Centro de Custo', rota = 'financial/centro-custo/delete' where id = '34131224-3cbd-4557-9531-288f00bde114';
update tb_servico set descricao = 'Autocomplete Centro de Custo', nome = 'Autocomplete Centro de Custo', rota = 'financial/centro-custo/autocomplete' where id = 'ba198384-0ce4-4efd-ac8c-4b397a5087f1';


insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '34131224-3cbd-4557-9531-288f00bde114');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', '34131224-3cbd-4557-9531-288f00bde114');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_confirm', 'você tem certeza que deseja excluir?', '34131224-3cbd-4557-9531-288f00bde114');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'fa fa-trash', '34131224-3cbd-4557-9531-288f00bde114');

insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'reload', '21054d01-52fa-47b1-b9b4-64b1d9e4da2a');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '21054d01-52fa-47b1-b9b4-64b1d9e4da2a');





--novos
--dd3d638a-4e19-459b-96ef-18aa1c9457d6
--665999f1-718f-4573-82e0-cc3151ad7986
--a41fec88-4568-4415-a19e-05d41a93ba0d
--52bb6782-c364-4356-a35f-0034a1e26a8b

