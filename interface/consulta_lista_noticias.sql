
--ENCONTRA TIPO NOTICIA BIBLIOTECA
select * from tp_itembiblioteca where metanome = 'TPNOTICIA'
--RETORNA TODAS AS NOTICIAS
select * from tb_itembiblioteca where id_tib = '0caf8e3b-59bb-4f71-8c37-afd4bbee7b77'
-- NOTICIA
select * from tb_itembiblioteca where id_ib_pai = '2823ef57-7c24-49f1-971b-082310b3d966'
-- DESCRICAO DO ITEM
select * from tp_itembiblioteca where id = 'a50ff349-051b-4942-a536-6c123679bf54'

"384a2419-a310-4962-a5f2-e2f9ae1b910c"
"a50ff349-051b-4942-a536-6c123679bf54"
"f56e5935-d8a0-410c-9b5b-d4f22cbb68e3"
"2823ef57-7c24-49f1-971b-082310b3d966"

"0caf8e3b-59bb-4f71-8c37-afd4bbee7b77"

-- TEMPLATE DOS ITENS
select * from tp_itembiblioteca where id_tib_pai = '0caf8e3b-59bb-4f71-8c37-afd4bbee7b77'