select * from tp_itembiblioteca where id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120'
select * from tp_itembiblioteca where id = 'e6785010-9889-423e-9b50-d4d7a6492120'

select * from tp_itembiblioteca where id = 'e6785010-9889-423e-9b50-d4d7a6492120' or id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120'


select * from tb_itembiblioteca where id_tib = 'e6785010-9889-423e-9b50-d4d7a6492120'

select * from tb_itembiblioteca where id_ib_pai = '62acd7ab-9f29-45b4-b8c8-94b4886806cb'

select * from tp_itembiblioteca where id = 'ab2e4049-d4e1-456c-b586-bba480fdb81c'

select * from tp_itembiblioteca_metadata 

select * from tb_grupo where id_pai = '3defea9f-257e-4b20-8120-330e8ec14b4d'

select * from tp_itembiblioteca where id = '12fd5631-626b-4006-afd6-1bb47d4b5684'

select * from tb_grupo

select * from rl_grupo_item

select * from tp_itembiblioteca where tipo = 'Master'

select * from tp_itembiblioteca where id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120'

select * from tp_itembiblioteca_metadata


select * from tb_itembiblioteca where id_ib_pai in (
select id from tb_itembiblioteca where id_tib = 'e6785010-9889-423e-9b50-d4d7a6492120'
)

select * from tp_itembiblioteca limit 20

select count(*) from tb_itembiblioteca where id_tib = 'e6785010-9889-423e-9b50-d4d7a6492120'

select valor,  from tb_itembiblioteca left outer join tp_itembiblioteca_metadata on (  ) where id_tib in
(select tib.id from tp_itembiblioteca tib left outer join tp_itembiblioteca_metadata timb on ( tib.id = timb.tib_id ) where timb.valor = '0' and tib.id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120')

select valor as (select 1) from tp_itembiblioteca_metadata where tib_id in ( select id from tp_itembiblioteca where id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120' )



select * from tp_itembiblioteca where id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120'



select ib.valor as conteudo, timb.valor, ib.id_ib_pai
from tb_itembiblioteca ib join tp_itembiblioteca tib on (ib.id_tib = tib.id) join tp_itembiblioteca_metadata timb on (timb.tib_id = tib.id)
where tib.id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120' 
and timb.metanome = 'ws_ordemLista'
order by ib.id_ib_pai, timb.valor



select ib.*, tib.* from tb_itembiblioteca ib join tp_itembiblioteca tib on( ib.id_tib = tib.id ) where 

select * from tb_itembiblioteca where id_tib in ( select id from tp_itembiblioteca where tipo = 'Master' )


select giovanna.count, tib.nome, giovanna.id_tib from tp_itembiblioteca tib left outer join 
(select count(*), ib.id_tib from tb_itembiblioteca as ib where ib.id_tib in (select id from tp_itembiblioteca where tipo = 'Master') group by ib.id_tib) as giovanna on ( giovanna.id_tib = tib.id ) where tib.id_tib_pai is null and giovanna.count > 0

--join tp_itembiblioteca as tib on (ib.id_tib = tib.id) 
select * from tb_grupo where id = '765a4317-a09a-0157-3480-138d4876a909'



select * from tp_itembiblioteca where descricao = 'blablau'

select * from tb_servico
select * from tp_itembiblioteca_metadata where tib_id in (select id from tp_itembiblioteca where id_tib_pai = 'f04e9e50-686b-28bc-bf54-f7ef29a0c65d')

select * from tb_servico

select * from tb_site

select * from tb_servico


select ib.valor, timb.valor, ib.id_ib_pai
from tb_itembiblioteca ib join tp_itembiblioteca tib on (ib.id_tib = tib.id) join tp_itembiblioteca_metadata timb on (timb.tib_id = tib.id)
where tib.id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120' 
and timb.metanome = 'ws_ordemLista'
order by ib.id_ib_pai, timb.valor



with recursive tb_todos_grupos (id, nome, id_pai, metanome) as 
( 
select id, nome, id_pai, metanome from tb_grupo where id_pai = '9dd5f303-220b-4b90-956c-c621b42bc3ff'
UNION ALL
select tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai, tb_grupo.metanome from tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
)
select id, nome, id_pai, metanome, 
case when exists ( select 1 from tb_grupo g1 where g1.id_pai = tb_todos_grupos.id ) then 1
else 0
end as pai,
case id_pai when '9dd5f303-220b-4b90-956c-c621b42bc3ff' then 1
else 0
end as principal
from tb_todos_grupos



select * from tb_grupo where id = '9dd5f303-220b-4b90-956c-c621b42bc3ff'


with recursive tb_todos_grupos (id, nome, id_pai, metanome) as 
( 
select id, nome, id_pai, metanome from tb_grupo where id_pai = '9dd5f303-220b-4b90-956c-c621b42bc3ff'
UNION ALL
select tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai, tb_grupo.metanome from tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
)
select id, nome, id_pai, metanome, 
case when exists ( select 1 from tb_grupo g1 where g1.id_pai = tb_todos_grupos.id ) then 1
else 0
end as pai,
case id_pai when '9dd5f303-220b-4b90-956c-c621b42bc3ff' then 1
else 0
end as principal
from tb_todos_grupos


SELECT * from tb_grupo where id = '9dd5f303-220b-4b90-956c-c621b42bc3ff'
select * from tb_grupo where id = '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6'
select * from tb_grupo where id = 'eb66d4f2-f0d8-41a2-b203-f837bec4bf10'
select * from tb_pessoa where id = '76e87056-5f25-4920-8ce5-e22b52b6252c'

select * from tb_itembiblioteca where id = '00f729ea-ec08-4876-90d2-09c9827ac896'

select * from tb_itembiblioteca where id = '00f729ea-ec08-4876-90d2-09c9827ac896'
select * from tb_itembiblioteca where id_ib_pai = '00f729ea-ec08-4876-90d2-09c9827ac896'



select mixtable.count, tib.*, mixtable.id_tib from tp_itembiblioteca tib left outer join 
(select count(*), ib.id_tib from tb_itembiblioteca as ib where ib.id_tib in (select id from tp_itembiblioteca where tipo = 'Master') group by ib.id_tib) as mixtable on ( mixtable.id_tib = tib.id ) where tib.id_tib_pai is null and mixtable.count > 0


select * from tb_grupo

select * from rl_grupo_item

select * from tb_usuario where id = '5ffc8714-22fd-4de2-abb3-f949eea57ead'

select * from tb_grupo

select * from rl_grupo_item

select * from tb_usuarior

select grupo.id, grupo.nome, grupo.id_pai, grupo.metanome, grupo.pai, grupo.principal, string_agg( cast(rl.id as varchar), ' - ' ) from (
with recursive tb_todos_grupos (id, nome, id_pai, metanome) as 
( 
select id, nome, id_pai, metanome from tb_grupo where id_pai = '9dd5f303-220b-4b90-956c-c621b42bc3ff'
UNION ALL
select tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai, tb_grupo.metanome from tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
)
select id, nome, id_pai, metanome, 
case when exists ( select 1 from tb_grupo g1 where g1.id_pai = tb_todos_grupos.id ) then 1
else 0
end as pai,
case id_pai when '9dd5f303-220b-4b90-956c-c621b42bc3ff' then 1
else 0
end as principal
from tb_todos_grupos
) as grupo left outer join rl_grupo_item as rl on ( grupo.id = rl.id_grupo ) group by grupo.id, grupo.nome, grupo.id_pai, grupo.metanome, grupo.pai, grupo.principal


select ib.*, timb.valor as ordem, tib.nome as nomecampo
from tb_itembiblioteca as ib right outer join tp_itembiblioteca as tib on (ib.id_tib = tib.id)
right outer join tp_itembiblioteca_metadata as timb on (tib.id = timb.tib_id) 
where ib.id_ib_pai IN (select id from tb_itembiblioteca where id_tib = 'e6785010-9889-423e-9b50-d4d7a6492120') order by ordem


select string_agg(cast(id_item as varchar), ' - '), id_grupo from rl_grupo_item where id_grupo = 'defd6c29-2c2c-0717-0d5e-dbcd8234e577' group by id_grupo

--TIB.id as tibID, RLIB.id_grupo, RLIB.id_item from
select * from
(
	select 
	*
	from rl_grupo_item as RL
	left outer join 
	tb_itembiblioteca as IB
	on ( RL.id_item = IB.id )
) as RLIB 
left outer join tp_itembiblioteca as TIB
on (RLIB.id_tib = TIB.id)

select * from rl_grupo_item

select * from tp_itembiblioteca limit 10

select * from tb_itembiblioteca limit 10

select * from tb_grupo


select * from tb_itembiblioteca where id= '0056a351-1106-44ae-b0cc-fd54a75b396c'
select * from tb_itembiblioteca where id_ib_pai = '0056a351-1106-44ae-b0cc-fd54a75b396c'



select 
RL.id_grupo,
string_agg(cast(RL.id_item as varchar), ','), 
string_agg(cast(IB.id_tib as varchar), ',')
from rl_grupo_item as RL
left outer join 
tb_itembiblioteca as IB
on ( RL.id_item = IB.id ) where RL.id_grupo = 'defd6c29-2c2c-0717-0d5e-dbcd8234e577' group by RL.id_grupo

select mixtable.count, tib.nome, mixtable.id_tib from tp_itembiblioteca tib left outer join 
(select count(*), ib.id_tib from tb_itembiblioteca as ib where ib.id_tib in (select id from tp_itembiblioteca where tipo = 'Master')
group by ib.id_tib) as mixtable on ( mixtable.id_tib = tib.id ) where tib.id_tib_pai is null and mixtable.count > 0


select 
RL.id,
RL.id_grupo,
RL.id_item,
IB.id_tib
from rl_grupo_item as RL
left outer join 
tb_itembiblioteca as IB
on ( RL.id_item = IB.id ) where RL.id_grupo = '0e27fe2f-20f2-8f8c-5cfa-481201a866db'


select * from tb_grupo

select * from tb_site where id_grupo = 'eb66d4f2-f0d8-41a2-b203-f837bec4bf10'

select * from tb_grupo where id_representacao is not null

select * from tp_itembiblioteca limit 10 where id_tib_pai = '89bf351d-30c5-4702-aa39-9fe1000fac6e'


select * from rl_grupo_item where id = '9f8592fe-a97b-a280-43d7-7a436f6f0476'

delete from tb_itembiblioteca_metadata

select * from tb_itembiblioteca_metadata 

select * from tp_itembiblioteca_metadata

insert into tp_itembiblioteca_metadata ( id, metanome, valor, id_tib, id_tib_pai ) values ( '66c09b5c-6ea9-11e5-9d70-feff819cdc9f', 'ws_ordemLista',  '0',  'd454ed25-ce3a-485b-9256-dd5bfc9c0659', 'aceb6e5a-9a84-4ab1-a17b-efb7445c95cf' )





select * from tp_itembiblioteca where id_tib_pai = 'aceb6e5a-9a84-4ab1-a17b-efb7445c95cf'



select * from tp_itembiblioteca where tipo = 'Master'

select * from tb_grupo where id_representacao is not null

select * from tb_site where id_grupo = '26a206ae-4acd-4de7-9a9d-29ba6fbe0ff4'

select * from tb_grupo where id = '9dd5f303-220b-4b90-956c-c621b42bc3ff'

select * from tb_site where id_grupo in ( select id from tb_grupo where id_representacao = 'd5e27a49-a3e0-4a3a-9aa0-dd0226899cb5'  ) 

select * from tb_grupo where id_pai = '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6'

select * from tb_grupo where id_pai = '9dd5f303-220b-4b90-956c-c621b42bc3ff'



select * from tb_grupo where id_pai in ( select id from tb_grupo where id_representacao is not null ) and metanome = 'SITE'

select * from rl_grupo_item

select * from tb_grupo where metanome = 'SITE'

select * from tb_grupo where id_pai = '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6'


select ib.valor as conteudo, timb.valor, ib.id_ib_pai
from tb_itembiblioteca ib join tp_itembiblioteca tib on (ib.id_tib = tib.id) join tp_itembiblioteca_metadata timb on (timb.id_tib = tib.id)
where tib.id_tib_pai = 'aceb6e5a-9a84-4ab1-a17b-efb7445c95cf'
and timb.metanome = 'ws_ordemLista'
order by ib.id_ib_pai, timb.valor



select * from rl_grupo_item


with recursive tb_todos_grupos (id, nome, id_pai, metanome) as 
( 
select id, nome, id_pai, metanome from tb_grupo where id_pai = '9dd5f303-220b-4b90-956c-c621b42bc3ff'
UNION ALL
select tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai, tb_grupo.metanome from tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
)
select id, nome, id_pai, metanome, 
case when exists ( select 1 from tb_grupo g1 where g1.id_pai = tb_todos_grupos.id ) then 1
else 0
end as pai,
case id_pai when '9dd5f303-220b-4b90-956c-c621b42bc3ff' then 1
else 0
end as principal
from tb_todos_grupos


select * from tb_grupo limit 10

select 
RL.id,
RL.id_grupo,
RL.id_item,
IB.id_tib
from rl_grupo_item as RL
left outer join 
tb_itembiblioteca as IB
on ( RL.id_item = IB.id ) where RL.id_grupo = '47194763-674d-1f39-2ad0-384db33aaf82'

with recursive tb_todos_grupos (id, nome, id_pai, metanome, publico) as 
( 
select id, nome, id_pai, metanome, publico from tb_grupo where id_pai = '9dd5f303-220b-4b90-956c-c621b42bc3ff'
UNION ALL
select tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai, tb_grupo.metanome, tb_grupo.publico from tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
)
select id, nome, id_pai, metanome, publico,
case when exists ( select 1 from tb_grupo g1 where g1.id_pai = tb_todos_grupos.id ) then 1
else 0
end as pai,
case id_pai when '9dd5f303-220b-4b90-956c-c621b42bc3ff' then 1
else 0
end as principal
from tb_todos_grupos

select * from tb_grupo where id = '6ff9c535-6f8b-40b3-88a4-e78064c9dabe'

select * from rl_grupo_item
select * from tp_itembiblioteca where id = 'aceb6e5a-9a84-4ab1-a17b-efb7445c95cf'


with recursive getitems as (
select ib.* from tb_itembiblioteca ib join rl_grupo_item gi on (ib.id = gi.id_item) join (WITH RECURSIVE getFilhos AS (select g.* from tb_grupo g where g.id = 'eb66d4f2-f0d8-41a2-b203-f837bec4bf10' UNION select g.* from getFilhos e join tb_grupo g on (g.id_pai = e.id)) SELECT id from getFilhos) gps on (gps.id = gi.id_grupo) union select ibr.* from tb_itembiblioteca ibr join getitems gr on (gr.id = ibr.id_ib_pai)) select * from getitems


select tib.*, timb.metanome as comportamento, timb.valor, timb.id as timbmetadata from tp_itembiblioteca tib left outer join tp_itembiblioteca_metadata timb on ( tib.id = timb.id_tib ) where tib.id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120';

select * from tp_itembiblioteca_metadata 

select * from tp_itembiblioteca where id = 'c174a4a7-d1ae-45f0-8bfb-ab2f01b5fdf9'

select * from tp_itembiblioteca where id in ( select id_tib from tp_itembiblioteca_metadata where metanome = 'ws_ordemLista' )


select * from tp_itembiblioteca limit 10


select * from tb_site

w


select * from tp_itembiblioteca as tib left outer join 
(
select tb_visivel.id_tib, tb_visivel.visivel, tb_ordem.ordem from 
(
select id_tib, valor as visivel from tp_itembiblioteca_metadata where id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120' and metanome= 'ws_visivel'
) as tb_visivel join
(
select id_tib, valor as ordem from tp_itembiblioteca_metadata where id_tib_pai  = 'e6785010-9889-423e-9b50-d4d7a6492120' and metanome= 'ws_ordemLista'
) as tb_ordem on ( tb_visivel.id_tib = tb_ordem.id_tib )
) as tibplus on (tib.id = tibplus.id_tib) where tib.id_tib_pai = 'e6785010-9889-423e-9b50-d4d7a6492120'

select * from tp_itembiblioteca_metadata


insert into tp_itembiblioteca_metadata (id, metanome, valor, id_tib, id_tib_pai) values ('e525ab36-7048-11e5-9d70-feff819cdc9f', 'ws_ordemLista', '0', '12fd5631-626b-4006-afd6-1bb47d4b5684', 'e6785010-9889-423e-9b50-d4d7a6492120' )
