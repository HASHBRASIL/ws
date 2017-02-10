-- @todo acao para adicionar dados extras ao agrupador

-- @todo acao para colocar consolidacao

-- @todo acao para upload de arquivo


-- consolidar transacao agrupador - 91fc55ed-124d-11e6-8565-07be5ded5709
-- classificar - transacao agrupador - 91fc55ec-124d-11e6-8564-eba1208a7fed
-- deletar - 91fc55f1-124d-11e6-8569-6f1639000a12
update tb_servico set descricao = 'Conciliação', nome = 'Conciliação', rota = 'financial/agrupador/conciliacao' where id = '91fc55ed-124d-11e6-8565-07be5ded5709';
update tb_servico_metadata set valor = 'glyphicon glyphicon-transfer' where id_servico = '91fc55ed-124d-11e6-8565-07be5ded5709' and metanome = 'ws_icon';


--update tb_servico set descricao = 'Classificar', nome = 'Classificar', rota = 'financial/transacao-conta/grid' where id = '91fc55ec-124d-11e6-8564-eba1208a7fed';
--update tb_servico_metadata set valor = 'glyphicon glyphicons-edit' where id_servico = '91fc55ec-124d-11e6-8564-eba1208a7fed' and metanome = 'ws_icon';

update tb_servico set descricao = 'Classificar', nome = 'Classificar', rota = 'financial/agrupador/form-extra' where id = '91fc55ee-124d-11e6-8566-af4b00d1ba65';
update tb_servico_metadata set valor = 'glyphicon glyphicon-edit' where id_servico = '91fc55ee-124d-11e6-8566-af4b00d1ba65' and metanome = 'ws_icon';

update tb_servico set descricao = 'Deletar', nome = 'Deletar', rota = 'financial/transacao-conta/grid' where id = '91fc55f1-124d-11e6-8569-6f1639000a12';
update tb_servico_metadata set valor = 'glyphicon glyphicon-trash' where id_servico = '91fc55f1-124d-11e6-8569-6f1639000a12' and metanome = 'ws_icon';


-- listagem - conciliacao 332f8169-1250-11e6-856b-eb4cd6ecab5b
-- editando 91fc55f0-124d-11e6-8568-4fe716064a5a - para virar conciliar
update tb_servico set descricao = 'Conciliação', nome = 'Conciliação', rota = 'financial/agrupador/conciliacao-conta', id_pai = '332f8169-1250-11e6-856b-eb4cd6ecab5b' where id = '91fc55f0-124d-11e6-8568-4fe716064a5a';
update tb_servico_metadata set valor = 'glyphicon glyphicon-transfer' where id_servico = '91fc55f0-124d-11e6-8568-4fe716064a5a' and metanome = 'ws_icon';
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_field', 'transacao_conta_id', '91fc55f0-124d-11e6-8568-4fe716064a5a');


ALTER TABLE "hash"."fin_tb_agrupador_financeiro" ADD COLUMN "transacao_conta_id" int4;
ALTER TABLE "hash"."fin_tb_agrupador_financeiro" ADD CONSTRAINT "fin_tb_agrupador_financeiro_transacao_conta_fkey" FOREIGN KEY ("transacao_conta_id") REFERENCES "hash"."fin_tb_transacao_conta" ("transacao_conta_id") ON UPDATE NO ACTION ON DELETE NO ACTION;



