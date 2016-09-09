
insert into tb_servico VALUES ('9b555927-6d3e-4b0f-a0b0-152765345714', null, 'financial/contas/form', null, 'financial/contas/form', 'financial/contas/form', null, '411dc5e4-6ebd-4814-9e12-f24f9288aeed', null, false, null, 'financial/contas/form');

INSERT into tb_servico_metadata VALUES (uuid_generate_v4(), null, 'ws_comportamento', 'action', '9b555927-6d3e-4b0f-a0b0-152765345714');
INSERT into tb_servico_metadata VALUES (uuid_generate_v4(), null, 'ws_show', 'reload', '9b555927-6d3e-4b0f-a0b0-152765345714');

INSERT into tb_servico_metadata VALUES (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '2d78cf1e-45bf-4486-8d81-c64e165d161e');
INSERT into tb_servico_metadata VALUES (uuid_generate_v4(), null, 'ws_show', 'reload', '2d78cf1e-45bf-4486-8d81-c64e165d161e');
INSERT into tb_servico_metadata VALUES (uuid_generate_v4(), null, 'ws_confirm', 'Você tem certeza que deseja excluir?', '2d78cf1e-45bf-4486-8d81-c64e165d161e');

INSERT into tb_servico_metadata VALUES (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', 'd9d71c8c-361e-4f7f-bae2-9b74a43a0f5c');
INSERT into tb_servico_metadata VALUES (uuid_generate_v4(), null, 'ws_show', 'reload', 'd9d71c8c-361e-4f7f-bae2-9b74a43a0f5c');

INSERT into tb_servico_metadata VALUES (uuid_generate_v4(), null, 'ws_icon', 'fa fa-trash', '2d78cf1e-45bf-4486-8d81-c64e165d161e');

update tb_servico set descricao = 'Nova Conta', metanome = 'Nova Conta', nome = 'Nova Conta' where id = '9b555927-6d3e-4b0f-a0b0-152765345714'


