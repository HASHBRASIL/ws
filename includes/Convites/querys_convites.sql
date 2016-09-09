select * from tb_servico where nome like '%Convites HASH%' ORDER BY nome
select * from tb_servico where id_pai = 'e8dfefe9-a969-49f8-bf13-517b13efb1a0'

SELECT * from tb_servico_metadata where id_servico = 'e2f99f59-92db-49ce-8058-1d502083033d' -- create
SELECT * from tb_servico_metadata where id_servico = 'a7325cce-55f4-4254-89a4-c219d65ac8ce' -- delete
SELECT * from tb_servico_metadata where id_servico = '62f6521d-0e3f-486c-9fb6-07f571f297df' -- filter
SELECT * from tb_servico_metadata where id_servico = 'e8dfefe9-a969-49f8-bf13-517b13efb1a0' -- index
SELECT * from tb_servico_metadata where id_servico = '01032cf3-77dc-4e5c-ad95-0651d53f57fd' -- insert
SELECT * from tb_servico_metadata where id_servico = '5a77b5d4-5d5a-44bc-b923-4696c99a20cb' -- list
SELECT * from tb_servico_metadata where id_servico = '13fd6ac9-ef1f-422d-a7ae-f42a8d16637e' -- retrieve
SELECT * from tb_servico_metadata where id_servico = '6579c69e-df7d-49a8-82c8-6f7242bb7bae' -- update