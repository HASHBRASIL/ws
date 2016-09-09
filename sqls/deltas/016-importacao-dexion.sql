BEGIN;

insert into fin_tb_agrupador_financeiro
(ativo, moe_id, tmv_id, id_grupo, fin_valor, fin_descricao, fin_observacao, dt_financeiro)

select -- uuid_generate_v4(),
1,
1,
CASE WHEN valordebito != '' THEN 1 WHEN valorcredito != '' THEN 2 END,
 '63f6b0c1-6b49-4d8a-8ec7-6061b7342257',
 CAST(replace(replace(coalesce(NULLIF(valorcredito, ''),valordebito,'0'), '.', ''), ',', '.') AS float),
concat(lr.texto1, 'contade: ', "lr"."contade", ' contapara: ', "lr"."contapara", ' sequenciade: ', "lr"."sequenciade",
' descricaode: ' ,"lr"."descricaode", ' lancamento: ' , "lr"."lancamento", ' segundoid: ' , "lr"."segundoid")
, ' IMPORTADO' ,
to_date(lr.datalivro, 'DD/MM/YYYY')
 from ing_dexion_livrorazao lr

-- rollback;

commit;

begin;

	update fin_tb_agrupador_financeiro set transacao_conta_id = (select transacao_conta_id from fin_tb_transacao_conta
	where -- arquivo ilike '%bb%'
	 dt_financeiro = dt_transacao_conta
	 AND @vl_transacao_conta = fin_valor
and tp_transacao_conta::SMALLINT = tmv_id
and con_id = 5 limit 1)

