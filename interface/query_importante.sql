
WITH RECURSIVE ib_temp ( id , id_ib_pai , id_tib , valor , nome, ordem  ) AS (
	(SELECT ib.id, ib.id_ib_pai, ib.id_tib, ib.valor, tib.nome, '-1' FROM tb_itembiblioteca ib join tp_itembiblioteca tib on (ib.id_tib = tib.id) WHERE id_tib = 'ef4fd1ca-7915-4737-83ab-ce900af8e0ec' order by ib.dt_criacao desc offset 0 limit 40)
UNION
	select ib.id, ib.id_ib_pai, ib.id_tib, ib.valor, tib.nome, timb.valor as ordem 
	from tb_itembiblioteca ib join ib_temp ibt on (ib.id_ib_pai = ibt.id) 
	join tp_itembiblioteca tib on(ib.id_tib = tib.id) 
	join tp_itembiblioteca_metadata timb on (ib.id_tib = timb.id_tib) 
	where timb.metanome = 'ws_ordemLista' 
)
SELECT * from ib_temp

WITH RECURSIVE ib_temp ( id , id_ib_pai , id_tib , valor , nome, ordem  ) AS (
	(SELECT ib.id, ib.id_ib_pai, ib.id_tib, ib.valor, tib.nome, '-1' FROM tb_itembiblioteca ib join tp_itembiblioteca tib on (ib.id_tib = tib.id) WHERE id_tib = 'e6785010-9889-423e-9b50-d4d7a6492120' order by ib.dt_criacao desc offset 0 limit 15)
UNION ALL
	select ib.id, ib.id_ib_pai, ib.id_tib, ib.valor, tib.nome, timb.valor as ordem 
	from tb_itembiblioteca ib join ib_temp ibt on (ib.id_ib_pai = ibt.id) 
	join tp_itembiblioteca tib on(ib.id_tib = tib.id) 
	join tp_itembiblioteca_metadata timb on (ib.id_tib = timb.id_tib) 
	where timb.metanome = 'ws_ordemLista' 
)
SELECT * from ib_temp

select * from tp_itembiblioteca where id = 'e6785010-9889-423e-9b50-d4d7a6492120'
select * from tp_itembiblioteca where tipo = 'Master'


------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------- QUERY QUE ALIMENTA O PORTAL WHAT WHAT IN THE *** -------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

select * from tb_grupo where id = '3defea9f-257e-4b20-8120-330e8ec14b4d'

--DESCOBRE O SITE
select * from tb_site where host = 'deputadomodelo'

--ENCONTRA AS ESTRUTURAS DO SITE
select cms_estrutura.*,tb_grupo.nome, CAST(coalesce(tb_grupo_metadata.valor, '-1') AS integer) as ordem 
from cms_estrutura left outer join tb_grupo_metadata 
on ( cms_estrutura.id_grupoarea = tb_grupo_metadata.id_grupo ) join tb_grupo on (cms_estrutura.id_grupoarea = tb_grupo.id)
where cms_estrutura.id_site = 'be8c6487-0995-4d45-b8eb-3dbbdda69116' 
and cms_estrutura.id_grupo = '9dd5f303-220b-4b90-956c-c621b42bc3ff'
order by ordem

--QUERY QUE MONTA OS BOXES -- 
--3faecac6-6b42-4e7f-9333-ffac9792164b   --->  EXEMPLO DE ESTRUTURA
select cms_box.*, cms_tpbox.largura, cms_tpbox.altura, cms_comportamento.arquivo, cms_tpbox.id_tptemplate 
from cms_box left outer join cms_tpbox on (cms_box.id_tpbox = cms_tpbox.id) 
left outer join cms_comportamento on (cms_tpbox.id_comportamento = cms_comportamento.id)
where id_estrutura = '857fe008-7d36-4e10-9c6a-2b1238ebae2d' order by cms_box.ordem




update cms_box set param = '{"total": 5}' where id = '8c95d5f1-b840-49e3-b689-bb9fdbffec3f'

select * from cms_box

select * from cms_tpbox

select * from cms_template

select * from cms_tptemplate

select * from cms_comportamento

-- QUERY MUITO LOUCA DO FERNANDO OUHHH YEAH! 
-- ME DEVOLTE TUDO QUE EU PRECISO PRA MONTAR OS DADOS DE UMA GRID
-- PEGAR ID DO GRUPO_AREA E MANDAR PRA ESSA QUERY


--TITANIC DE NOE
select ib.*, ibpai.id_tib as id_tib_pai, tib.nome as alias from tb_itembiblioteca ib join tp_itembiblioteca tib on (ib.id_tib = tib.id) join    
((select ib.id, ib.id_tib from tb_itembiblioteca ib join rl_grupo_item rgi on (ib.id = rgi.id_item) join
    (with recursive getgrupos as (
        select g.id from tb_grupo g where g.id = '9dd5f303-220b-4b90-956c-c621b42bc3ff'
        union
        (
        with recursive getfilhos as (
     select g.id from tb_grupo g join getgrupos gi on (g.id_pai = gi.id)
     union
     (
     with recursive getinscricoes as (
         select g.id from tb_grupo g join rl_inscricao_grupo rig on (g.id = rig.id_inscricao) join getfilhos gg on (rig.id_publicacao = gg.id)
         union                
          select g.id from tb_grupo g join getinscricoes gf on (g.id_pai = gf.id)
      ) select * from getinscricoes
         )
     ) select * from getfilhos
        )
    ) select * from getgrupos) gg on (rgi.id_grupo = gg.id) order by ib.dt_criacao desc  offset 0 limit 1) 
    ) ibpai on (ib.id_ib_pai = ibpai.id)rr

union select id, id_tib from tb_itembiblioteca where id in (?,?,?)


select * from tb_itembiblioteca where id_ib_pai = '0056a351-1106-44ae-b0cc-fd54a75b396c'
--e6785010-9889-423e-9b50-d4d7a6492120 - 0c345ffc-e783-4bb9-a520-ba5a1992bdd3
select * from cms_template where id_tib = 'e6785010-9889-423e-9b50-d4d7a6492120' and id_tptemplate = '0c345ffc-e783-4bb9-a520-ba5a1992bdd3';

select * from cms_template

select * from tb_itembiblioteca where id_ib_pai ='016c4ee1-5744-4ec5-9850-3ad0700e3722'
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------FIM DO BLOCO DE QUERY QUE ALIMENTA O SITE-----------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


select * from tb_grupo where id = '9dd5f303-220b-4b90-956c-c621b42bc3ff'

select * from tb_grupo_metadata

insert into tb_grupo_metadata  (id, metanome, valor, id_grupo) values 
('fe6ee462-768e-11e5-8bcf-feff819cdc9f', 'cms_ordem', '3', '6f32bb76-8ba3-4356-b3e6-9e644f07b526'),
('0150bdf4-768f-11e5-8bcf-feff819cdc9f', 'cms_ordem', '4', '2042dcd5-27e9-480e-86b3-281f0af7bc62')


select cms_box.*, cms_tpbox.altura, cms_tpbox.largura, cms_tpbox.id_comportamento, cms_tpbox.id_tptemplate from cms_box join cms_tpbox on ( cms_box.id_tpbox = cms_tpbox.id )




select cms.* from cms_estrutura cms 
join tb_site site on ( cms.id_site = site.id )
where site.host = 'deputadomodelo'
and cms.id_grupo = ''

select * from tb_site


select * from tb_grupo_metadata

select * from tb_grupo


select * from cms_box where id_estrutura in ( select id from cms_estrutura where id_grupoarea = '3defea9f-257e-4b20-8120-330e8ec14b4d' )



select tabela_2.*, comportamento.arquivo from (
select tabela.*, tptmp.nome as tmp_nome from (
select box.*, tpbox.altura, tpbox.largura, tpbox.id_comportamento, tpbox.id_tptemplate from cms_box as box join cms_tpbox as tpbox on ( box.id_tpbox = tpbox.id ) where id_estrutura in ( select id from cms_estrutura where id_grupoarea = '3defea9f-257e-4b20-8120-330e8ec14b4d' )
) as tabela join cms_tptemplate as tptmp on ( tabela.id_tptemplate = tptmp.id )
) as tabela_2 join cms_comportamento as comportamento on (tabela_2.tmp_nome = comportamento.nome)


select * from tp_itembiblioteca where tipo = 'Master'

select * from tb_itembiblioteca where id_tib = 'ef4fd1ca-7915-4737-83ab-ce900af8e0ec'
select * from tb_itembiblioteca where id_ib_pai = 'b119a0ad-5c10-42bf-a2bc-1e5074680820'

select * from tp_itembiblioteca where id = '5a9b1785-bfbd-4a70-bcf3-9adef226d5ee'

select * from tp_itembiblioteca_metadata where id_tib = '7253142b-b6da-4082-8164-a7282ac876f3'


3defea9f-257e-4b20-8120-330e8ec14b4d


select * from cms_tpbox

select * from tp_itembiblioteca id_tib_pai 

select * from cms_tptemplate

--73dd8b2d-7704-4989-965d-82445d09f3ac


select * from cms_estrutura

select * from tb_grupo where id = '3defea9f-257e-4b20-8120-330e8ec14b4d'



CAST(coalesce(<column>, '0') AS integer)

SELECT tib.id, tib.descricao, tib.metanome, tib.nome, tib.tipo,vis.valor AS visivel, ordemLista.valor AS ordemLista, CAST(coalesce(ordem.valor, '-1') AS integer)  AS ordem
FROM tp_itembiblioteca tib
LEFT OUTER JOIN ( SELECT id_tib, valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_visivel' ) vis ON ( tib.id = vis.id_tib )
LEFT OUTER JOIN ( SELECT id_tib, valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordemLista' ) ordemLista ON ( tib.id = ordemLista.id_tib )
LEFT OUTER JOIN ( SELECT id_tib, valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordem' ) ordem ON ( tib.id = ordem.id_tib )
WHERE tib.id_tib_pai = 'd2fac29f-4a99-434e-bde6-cd7fcca06da3'
ORDER BY ordem ASC;


select * from tp_itembiblioteca_metadata where id_tib_pai = 'd2fac29f-4a99-434e-bde6-cd7fcca06da3'


select * from tb_servico_metadata

ws_arquivo

select * from tb_itembiblioteca

select * from tb_itembiblioteca_


select * from tp_itembiblioteca_metadata where id_tib = 'd562e8d7-b805-4499-a6cf-332340b6753c'