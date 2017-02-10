
insert INTO tb_servico values ('ebff78fe-2ea9-4a05-8a38-6c40c047aa0c', null, 'Listar Conciliado', null, 'Listar Conciliado', 'Listar Conciliado', null, '332f8169-1250-11e6-856b-eb4cd6ecab5b', null, false, null, 'financial/agrupador/grid');

insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', 'ebff78fe-2ea9-4a05-8a38-6c40c047aa0c');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-plus', 'ebff78fe-2ea9-4a05-8a38-6c40c047aa0c');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'transacao_conta_id', 'ebff78fe-2ea9-4a05-8a38-6c40c047aa0c');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', 'ebff78fe-2ea9-4a05-8a38-6c40c047aa0c');

update tb_servico_metadata set valor = 'glyphicon glyphicon-transfer' where id_servico = '12a21781-79f2-4c6f-b4c7-ff2c38776195' and metanome = 'ws_icon';


begin;
insert into tb_grupo (id, dt_inclusao, metanome, nome, publico, id_criador, id_pai, descricao)
select uuid_generate_v4(), now(), 'FINANCEIRO', 'FINANCEIRO', false, 'c7876e15-ede7-4177-aa33-2018fd139c33', id, 'grupo para imagens do financeiro' from tb_grupo where id_representacao is not null;
commit;


insert INTO tb_servico values ('4f869c93-7401-4718-96ec-25bf3360551a', null, 'Inserir Imagens de Media', null, 'Inserir Imagens de Media ', 'Inserir Imagens de Media', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'content/itembiblioteca/dragndrop');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'id_agrupador_financeiro', '4f869c93-7401-4718-96ec-25bf3360551a');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '4f869c93-7401-4718-96ec-25bf3360551a');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '4f869c93-7401-4718-96ec-25bf3360551a');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-file', '4f869c93-7401-4718-96ec-25bf3360551a');


insert INTO tb_servico values ('6709c273-895d-4ccb-883b-25f699417fc5', null, 'Processar Imagens de Media', null, 'Processar Imagens de Media ', 'Processar Imagens de Media', null, '4f869c93-7401-4718-96ec-25bf3360551a', null, false, null, 'financial/agrupador/savednd');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'id_agrupador_financeiro', '6709c273-895d-4ccb-883b-25f699417fc5');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'ajax', '6709c273-895d-4ccb-883b-25f699417fc5');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'filter', '6709c273-895d-4ccb-883b-25f699417fc5');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_arqcampo', '2ccd1deb-f022-4c06-918a-87182f7e5a66', '6709c273-895d-4ccb-883b-25f699417fc5');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_grupo', 'FINANCEIRO', '6709c273-895d-4ccb-883b-25f699417fc5');



CREATE TABLE "titaniumtech"."fin_rl_agrupador_financeiro_ib" (
	"id_itembiblioteca" uuid NOT NULL,
	"id_agrupador_financeiro" bigint
);



insert INTO tb_servico values ('99205ab1-01f8-4b17-b6e3-26a29eea08ab', null, 'Listar Imagens de Media', null, 'Listar Imagens de Media ', 'Listar Imagens de Media', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/agrupador/grid-upload');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'id_agrupador_financeiro', '99205ab1-01f8-4b17-b6e3-26a29eea08ab');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '99205ab1-01f8-4b17-b6e3-26a29eea08ab');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction   ', '99205ab1-01f8-4b17-b6e3-26a29eea08ab');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-picture', '99205ab1-01f8-4b17-b6e3-26a29eea08ab');

insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_grupo', 'FINANCEIRO', '99205ab1-01f8-4b17-b6e3-26a29eea08ab');



insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_arqcampo', '2ccd1deb-f022-4c06-918a-87182f7e5a66', '825695ce-7822-4334-975e-089e1d700963');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_grupo', 'FINANCEIRO', '825695ce-7822-4334-975e-089e1d700963');



insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_arqcampo', '2ccd1deb-f022-4c06-918a-87182f7e5a66', '3e0e7dab-1c2c-4b2d-adfa-52f2b0e3f87e');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_grupo', 'FINANCEIRO', '3e0e7dab-1c2c-4b2d-adfa-52f2b0e3f87e');

