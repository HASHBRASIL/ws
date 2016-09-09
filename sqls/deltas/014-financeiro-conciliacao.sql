


 ALTER TABLE "fin_tb_transacao_conta" ADD COLUMN "vl_transacao_saldo" numeric(15,2);


insert INTO tb_servico values ('12a21781-79f2-4c6f-b4c7-ff2c38776195', null, 'Adicionar Transacao Financeira e vincular', null, 'Adicionar Transacao Financeira e vincular', 'Adicionar Transacao Financeira e vincular', null, '332f8169-1250-11e6-856b-eb4cd6ecab5b', null, false, null, 'financial/agrupador/conciliacao-conta-add');

insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'listaction', '12a21781-79f2-4c6f-b4c7-ff2c38776195');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_icon', 'glyphicon glyphicon-plus', '12a21781-79f2-4c6f-b4c7-ff2c38776195');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'transacao_conta_id', '12a21781-79f2-4c6f-b4c7-ff2c38776195');
insert into tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', '12a21781-79f2-4c6f-b4c7-ff2c38776195');