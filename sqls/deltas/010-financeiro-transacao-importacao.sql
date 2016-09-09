
INSERT INTO tb_servico values ('f67cd71e-b133-4fef-a943-82e188f36dd5', null, 'importar receita', null, 'importar receita', 'importar receita', null, 'da82e4da-1348-44df-a864-27e0ff816dc8', null, false, null, 'financial/agrupador/import-receita');

INSERT INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'action', 'f67cd71e-b133-4fef-a943-82e188f36dd5');
INSERT INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_show', 'dropdown', 'f67cd71e-b133-4fef-a943-82e188f36dd5');

UPDATE tb_servico set descricao = 'Importar Despesa', nome = 'Importar Despesa', rota = 'financial/agrupador/import-despesa' where id = 'b4e180ba-b61f-420c-b4fc-692b330fda00';

insert INTO tb_servico values ('3e0e7dab-1c2c-4b2d-adfa-52f2b0e3f87e', null, 'upload receita', null, 'upload receita', 'upload receita', null, 'f67cd71e-b133-4fef-a943-82e188f36dd5', null, false, null, 'financial/agrupador/upload-despesa');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'upload', '3e0e7dab-1c2c-4b2d-adfa-52f2b0e3f87e');

insert INTO tb_servico values ('825695ce-7822-4334-975e-089e1d700963', null, 'upload despesa', null, 'upload despesa', 'upload despesa', null, 'b4e180ba-b61f-420c-b4fc-692b330fda00', null, false, null, 'financial/agrupador/upload-receita');
insert INTO tb_servico_metadata values (uuid_generate_v4(), null, 'ws_comportamento', 'upload', '825695ce-7822-4334-975e-089e1d700963');





-- 6d7297e0-51f8-4f95-83df-552050095f7a




-- @todo rodar scripts de acoes da tela de consolidacao

-- @todo criar list consolidada (Botao apenas)
-- @todo criar lista geral de transacoes bancarias  - movimento (juntar as 2 acimas)

-- tipo era para ser mascarado - entrada / saida

-- formatar numero e alinhar direita -- ver se twig tem formatador de numeros
-- data formato padrao twig


-- imagem da transacao  - colocar na listagem de transacoes por transacao bancaria e para padrao



-- @todo criar campo chave nota fiscal