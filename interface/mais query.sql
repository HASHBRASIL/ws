 WITH RECURSIVE tb_menu (id) AS
(
    SELECT * FROM tb_servico WHERE id_pai IS NULL
    UNION ALL
    SELECT * FROM tb_servico WHERE id_pai in ( SELECT id FROM tb_servico )
)
SELECT tb_menu.*, tsm.valor as arquivo,
    CASE WHEN (id_pai IS NULL) THEN 'PAPAI' END AS raiz,
    CASE WHEN EXISTS ( SELECT 1 FROM tb_servico WHERE tb_servico.id_pai = tb_menu.id ) THEN 1 ELSE 0 END AS tem_filho
FROM tb_menu LEFT OUTER JOIN tb_servico_metadata AS tsm ON tb_menu.id = tsm.id_servico

SELECT * FROM rl_inscricao_grupo

select * from tb_servico

select * from tb_grupo

select * from tb_usuario where id = 'd5e27a49-a3e0-4a3a-9aa0-dd0226899cb5'

select * from tb_pessoa where id = 'd5e27a49-a3e0-4a3a-9aa0-dd0226899cb5'

eb66d4f2-f0d8-41a2-b203-f837bec4bf10

SELECT * FROM tb_servico WHERE id_pai IS NULL

"6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6"
"eb66d4f2-f0d8-41a2-b203-f837bec4bf10"
"26a206ae-4acd-4de7-9a9d-29ba6fbe0ff4"
-- QUERY GET GEM!
with recursive getGEM as (
select * from tb_grupo where id in ( select id_grupo from rl_grupo_pessoa where id_pessoa = 'd5e27a49-a3e0-4a3a-9aa0-dd0226899cb5' ) 
UNION
select g.* from tb_grupo g JOIN getGEM gg ON ( gg.id_pai = g.id )
) select * from getGEM where id_representacao is not null

select * from tb_servico where id_pai is null

select * from tb_servico where id_pai = '9b09577b-cf6a-4edd-899a-b506912b1189'

select * from tb_grupo where id = '39f15e29-52b0-4948-8fbc-d5f7169f65a5'

select * from rl_inscricao_grupo


SELECT * FROM tb_servico WHERE id_pai = '9b09577b-cf6a-4edd-899a-b506912b1189'

 WITH RECURSIVE tb_menu ( id, descricao, fluxo, metanome, nome, id_grupo, id_pai, id_tib, visivel ) AS
(
    SELECT id, descricao, fluxo, metanome, nome, id_grupo, id_pai, id_tib, visivel FROM tb_servico WHERE id = '9b09577b-cf6a-4edd-899a-b506912b1189'
    UNION ALL
    SELECT sv.id, sv.descricao, sv.fluxo, sv.metanome, sv.nome, sv.id_grupo, sv.id_pai, sv.id_tib, sv.visivel FROM tb_servico sv JOIN tb_menu mn on ( sv.id_pai = mn.id )
)
SELECT tb_menu.* , tsm.valor as arquivo,
    CASE WHEN (id_pai IS NULL) THEN 'PAPAI' ELSE 'FILHO' END AS raiz,
    CASE WHEN EXISTS ( SELECT 1 FROM tb_servico WHERE tb_servico.id_pai = tb_menu.id ) THEN 1 ELSE 0 END AS tem_filho
FROM tb_menu LEFT OUTER JOIN tb_servico_metadata AS tsm ON tb_menu.id = tsm.id_servico


select * from tb_servico_metadata where id_servico = '5f611c49-c063-43c2-bbc8-e957839a2159'

delete from tb_servico_metadata where id = 'd8ba3dbf-39df-47d3-a90e-6a405c8ea0e4'

--select * from rl_perfil_grupo
--select * from rl_vinculo_pessoa
--select * from tb_classificacao

select * from tb_pessoa where id = 'd5e27a49-a3e0-4a3a-9aa0-dd0226899cb5'

select * from tb_informacao where id_pessoa = 'd5e27a49-a3e0-4a3a-9aa0-dd0226899cb5'

select * from tp_informacao where id in ( select id_tinfo from tb_informacao where id_pessoa = 'd5e27a49-a3e0-4a3a-9aa0-dd0226899cb5' )

select * from tp_informacao where

select * from tb_perfil

insert into tb_perfil (id, descricao, metanome, nome) values ('b73b317d-0ab2-47be-a616-12a68d01cbff', 'Dados Pessoais', 'PESSOAIS', 'Dados Pessoais' )

select * from rl_perfil_pessoa

select * from rl_perfil_informacao

select * from tb_pessoa

select * from tb_informacao

select * from tp_informacao

select * from tb_usuario

SELECT * FROM tp_informacao

insert into rl_perfil_informacao (id, id_informacao, id_perfil) values ('2fde1b0a-8718-11e5-af63-feff819cdc9f', '46d799e8-8714-11e5-af63-feff819cdc9f', 'b6854aaa-8701-11e5-af63-feff819cdc9f')

select * from tp_informacao where id = 'f7158235-7a06-475e-9de4-b336496146e5'

select * from tb_perfil

select * from rl_perfil_informacao
select * from tb_pessoa

select * from tp_informacao

select * from tb_informacao

select * from tp_informacao_metadata




SELECT filhas.*, pai.metanome as master FROM tp_informacao filhas 
JOIN tp_informacao pai ON ( filhas.id_pai = pai.id ) 
JOIN rl_perfil_informacao rpi ON ( rpi.id_informacao = pai.id ) 
JOIN tb_perfil p ON ( rpi.id_perfil = p.id )
WHERE p.metanome = 'GERAL'
OR p.metanome = 'PESSOAIS'


SELECT filhas.* FROM tp_informacao filhas
left outer join tp_informacao pai ON ( filhas.id_pai = pai.id )


select filhas.* from tp_informacao filhas
right outer join (
SELECT tinfo.* 
FROM tp_informacao tinfo
JOIN rl_perfil_informacao rpi ON ( tinfo.id = rpi.id_informacao )
JOIN tb_perfil p              ON ( rpi.id_perfil = p.id ) 
where p.metanome              IN ( 'GERAL', 'PESSOAIS' ) 
) as tinf ON ( filhas.id_pai = tinf.id )

WITH RECURSIVE teste AS
(
	SELECT tinfo.*, p.metanome as Master
	FROM tp_informacao tinfo
	JOIN rl_perfil_informacao rpi ON ( tinfo.id = rpi.id_informacao )
	JOIN tb_perfil p              ON ( rpi.id_perfil = p.id ) 
	where p.metanome              IN ( 'GERAL', 'PESSOAIS' ) 
UNION
	SELECT tp_informacao.*, teste.Master from tp_informacao JOIN teste ON ( tp_informacao.id_pai = teste.id )
)
SELECT * from teste WHERE tipo <> 'Master'

select * from tb_pessoa

select * from tb_perfil

select * from rl_perfil_informacao 

select * from tp_informacao where id = 'f7158235-7a06-475e-9de4-b336496146e5'

select * from tp_informacao

select * from tb_informacao

select * from tb_perfil

delete from tb_perfil where metanome = 'PESSOAIS'

select * from rl_perfil_informacao

select * from tp_informacao where id = 'f7158235-7a06-475e-9de4-b336496146e5'

WITH RECURSIVE Form AS
(
	SELECT tinfo.*, p.metanome as Master
	FROM tp_informacao tinfo
	JOIN rl_perfil_informacao rpi ON ( tinfo.id = rpi.id_informacao )
	JOIN tb_perfil p              ON ( rpi.id_perfil = p.id ) 
	where p.metanome              IN ( 'GERAL', 'PESSOAIS' ) 
UNION
	SELECT tp_informacao.*, Form.Master from tp_informacao JOIN Form ON ( tp_informacao.id_pai = Form.id )
)
SELECT *,
CASE WHEN (tipo = 'Master') THEN 'MASTER' ELSE 'NOT MASTER' END AS father
from Form


select * from tb_informacao



insert into tp_informacao (id, descricao, nome, visivel, id_pai, tipo) values ( 'a1a4ac1f-ccac-47d0-bbe5-72858fcef376', 'Nome', 'Nome', true, '46d799e8-8714-11e5-af63-feff819cdc9f', 'text' )


WITH RECURSIVE Form AS
(
	SELECT tinfo.*,  p.metanome as Master, p.descricao as perfil_descricao
	FROM tp_informacao tinfo
	JOIN rl_perfil_informacao rpi ON ( tinfo.id = rpi.id_informacao )
	JOIN tb_perfil p              ON ( rpi.id_perfil = p.id )
	where p.metanome              IN ( 'GERAL' )
UNION
	SELECT tp_informacao.*, Form.Master, Form.perfil_descricao FROM tp_informacao JOIN Form ON ( tp_informacao.id_pai = Form.id )
)
SELECT *,
CASE WHEN (tipo = 'Master') THEN 'MASTER' ELSE 'NOT MASTER' END AS Father
FROM Form

select * from tb_perfil

select * from tb_pessoa

update rl_perfil_informacao set pesquisa = true where id_informacao = '4f7934fc-d87d-4637-8c41-e4d563f0b2ec'

select * from rl_perfil_informacao
select * from tp_informacao where id = '81759d2c-1609-4ba9-a2fa-f6d6833569e9'

select * from tb_informacao where id_pessoa in (
select id from tb_pessoa pessoa where nome ILIKE '%Anto%' 
) and id_tinfo in (
select rpi.id_informacao from rl_perfil_informacao rpi
JOIN tb_perfil p on ( rpi.id_perfil = p.id ) where rpi.pesquisa = TRUE and p.metanome = 'GERAL'
)

select * from tb_informacao
--COISA HORROROSA
select p.*, info.*,rpi.*, pf.* from tb_pessoa p 
join tb_informacao info on (p.id = info.id_pessoa)
join rl_perfil_informacao rpi on (rpi.id_informacao = info.id_tinfo)
join tb_perfil pf on (rpi.id_perfil = pf.id)
where p.nome ilike '%AnToNiO%'
and pf.metanome = 'GERAL'
and rpi.pesquisa = true



delete from tb_pessoa where id = '4b085ab5-2ffe-4265-8fed-2e134ba8397d'

select * from tb_informacao



update tp_informacao set tipo = 'text' where id = 'ba6660ca-7bb2-4bdc-b03f-97dbea36015e'
select * from tp_informacao where id_pai in (select id_informacao from rl_perfil_informacao where id_perfil in ( select id from tb_perfil where metanome = 'GERAL') )

--insert into rl_perfil_informacao (id, id_informacao, id_perfil) values ('6d2fd2fc-8702-11e5-af63-feff819cdc9f', 'f7158235-7a06-475e-9de4-b336496146e5', 'b6854aaa-8701-11e5-af63-feff819cdc9f')
--insert into tb_perfil (id, descricao, metanome, nome) values ('b6854aaa-8701-11e5-af63-feff819cdc9f', 'Dados gerais', 'GERAL', 'Geral' )
insert into tp_informacao (id, descricao, nome, visivel, tipo, id_pai) values ( '9a2acb1a-8714-11e5-af63-feff819cdc9f', 'Nascimento',  'Data de Nascimento', TRUE, 'Date', '46d799e8-8714-11e5-af63-feff819cdc9f' )
--update tp_informacao set pai_id = 'f7158235-7a06-475e-9de4-b336496146e5' where id = 'e814db1b-23b5-4eb9-b008-81cfec7d95c1'
--d--e--l--e---t-e from tp_informacao where id = 'd96b3a9e-8700-11e5-af63-feff819cdc9f'


select filhas.*, pai.nome as 'master' from tp_informacao filhas join tp_informacao pai on ( filhas.id_pai = pai.id ) join rl_perfil_informacao rpi on ( rpi.id_informacao = pai.id ) join tb_perfil p on ( rpi.id_perfil = p.id ) where p.metanome = 'GERAL'
--SELECT * FROM tp_informacao WHERE id_pai IN (SELECT id_informacao FROM rl_perfil_informacao WHERE id_perfil IN ( SELECT id FROM tb_perfil WHERE metanome = 'GERAL') )