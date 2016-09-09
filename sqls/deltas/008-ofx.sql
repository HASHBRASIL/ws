

insert INTO tb_servico values ('62a3d0b0-b186-4f01-8a0c-7f0187574e0a', null, 'importação ofx', null, 'importação ofx', 'importação ofx', null, 'e24f7e50-1250-11e6-856d-1b1959ce10bb', null, false, null, 'financial/contas/upload');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'upload', '62a3d0b0-b186-4f01-8a0c-7f0187574e0a');

alter table fin_tb_transacao_conta ALTER COLUMN tp_transacao_conta TYPE char(1);

alter table fin_tb_transacao_conta ADD COLUMN ds_idunico_transacao_conta char(40);

alter table fin_tb_transacao_conta ADD COLUMN tp_transacao_conta_extra char(20);



insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'con_id', '332f8169-1250-11e6-856b-eb4cd6ecab5b');

update tb_servico_metadata set valor = 'reload' where id_servico = '332f8169-1250-11e6-856b-eb4cd6ecab5b' and metanome = 'ws_show';

