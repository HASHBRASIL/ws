
-- servicos para licensa
INSERT into tb_servico ("id", "id_pai", "descricao","metanome", "nome", "rota", "dtype", "fluxo", "id_grupo", "id_tib", "ordem", "visivel")
VALUES ('ee6110d7-5267-4954-9822-87a4d01e189a', 'f24fe46f-76fe-4d2c-87de-ea1425f705a2', 'config/license/paginacao', 'config/license/paginacao', 'config/license/paginacao', 'config/license/paginacao',  null,  null, null, null, null, false);

INSERT into tb_servico ("id", "id_pai", "descricao","metanome", "nome", "rota", "dtype", "fluxo", "id_grupo", "id_tib", "ordem", "visivel")
VALUES ('0c979d59-4349-4acd-b802-5fe1bccfa382', 'f24fe46f-76fe-4d2c-87de-ea1425f705a2', 'config/license/form', 'config/license/form', 'config/license/form', 'config/license/form',  null,  null, null, null, null, false);

INSERT into tb_servico ("id", "id_pai", "descricao","metanome", "nome", "rota", "dtype", "fluxo", "id_grupo", "id_tib", "ordem", "visivel")
VALUES ('b7ca71f0-5a62-4c81-9cec-219a3820aa2c', 'f24fe46f-76fe-4d2c-87de-ea1425f705a2', 'config/license/save', 'config/license/save', 'config/license/save', 'config/license/save',  null,  null, null, null, null, false);


INSERT into tb_servico_metadata VALUES ('6adbe57a-2fcd-490e-ac6d-42e0dc6f9a4b', null, 'ws_comportamento', 'paginacao', 'ee6110d7-5267-4954-9822-87a4d01e189a');

INSERT into tb_servico_metadata VALUES ('fda25fcb-9deb-4d85-8e83-0f1b3db6adb0', null, 'ws_comportamento', 'listaction', '0c979d59-4349-4acd-b802-5fe1bccfa382');
INSERT into tb_servico_metadata VALUES ('d7e23ed9-8cbe-4208-9f10-0f12b936d630', null, 'ws_show', 'reload', '0c979d59-4349-4acd-b802-5fe1bccfa382');


INSERT into tb_servico ("id", "id_pai", "descricao","metanome", "nome", "rota", "dtype", "fluxo", "id_grupo", "id_tib", "ordem", "visivel")
VALUES ('c8709db8-2365-442a-acd1-13ab6931359b', 'f24fe46f-76fe-4d2c-87de-ea1425f705a2', 'config/permission/usuario', 'config/permission/usuario', 'config/permission/usuario', 'config/permission/usuario',  null,  null, null, null, null, false);

INSERT into tb_servico_metadata VALUES ('cc9e0dbd-2024-4816-9383-f0a04d0d5e31', null, 'ws_comportamento', 'listaction', 'c8709db8-2365-442a-acd1-13ab6931359b');
INSERT into tb_servico_metadata VALUES ('c42a6257-3531-4b96-a3c5-74bb127b725c', null, 'ws_show', 'reload', 'c8709db8-2365-442a-acd1-13ab6931359b');
INSERT into tb_servico_metadata VALUES ('ba93b814-92d9-48f8-8f33-15842c3680ae', null, 'ws_icon', 'glyphicon glyphicon-user', 'c8709db8-2365-442a-acd1-13ab6931359b');

INSERT into tb_servico ("id", "id_pai", "descricao","metanome", "nome", "rota", "dtype", "fluxo", "id_grupo", "id_tib", "ordem", "visivel")
VALUES ('c4fb07f9-5f88-4868-8940-ccc9ae9d0e49', 'c8709db8-2365-442a-acd1-13ab6931359b', 'config/permission/autocomplete', 'config/permission/autocomplete', 'config/permission/autocomplete', 'config/permission/autocomplete',  null,  null, null, null, null, false);

INSERT into tb_servico ("id", "id_pai", "descricao","metanome", "nome", "rota", "dtype", "fluxo", "id_grupo", "id_tib", "ordem", "visivel")
VALUES ('5e93482d-fa86-4a80-a5f3-7b2d7f0a898c', 'c8709db8-2365-442a-acd1-13ab6931359b', 'config/permission/form', 'config/permission/form', 'config/permission/form', 'config/permission/form',  null,  null, null, null, null, false);

INSERT into tb_servico ("id", "id_pai", "descricao","metanome", "nome", "rota", "dtype", "fluxo", "id_grupo", "id_tib", "ordem", "visivel")
VALUES ('52fb5738-1181-48cf-a91f-afadfae0e98a', '5e93482d-fa86-4a80-a5f3-7b2d7f0a898c', 'config/permission/form', 'config/permission/save', 'config/permission/save', 'config/permission/save',  null,  null, null, null, null, false);


insert into rl_permissao_pessoa values (uuid_generate_v4(), '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6', '962d0e69-383a-4b1f-9531-6a612cc1f124', 'ee6110d7-5267-4954-9822-87a4d01e189a', '2020-01-01');
insert into rl_permissao_pessoa values (uuid_generate_v4(), '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6', '962d0e69-383a-4b1f-9531-6a612cc1f124', '0c979d59-4349-4acd-b802-5fe1bccfa382', '2020-01-01');
insert into rl_permissao_pessoa values (uuid_generate_v4(), '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6', '962d0e69-383a-4b1f-9531-6a612cc1f124', 'b7ca71f0-5a62-4c81-9cec-219a3820aa2c', '2020-01-01');


insert into rl_permissao_pessoa values (uuid_generate_v4(), '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6', '962d0e69-383a-4b1f-9531-6a612cc1f124', 'c8709db8-2365-442a-acd1-13ab6931359b', '2020-01-01');
insert into rl_permissao_pessoa values (uuid_generate_v4(), '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6', '962d0e69-383a-4b1f-9531-6a612cc1f124', 'c4fb07f9-5f88-4868-8940-ccc9ae9d0e49', '2020-01-01');
insert into rl_permissao_pessoa values (uuid_generate_v4(), '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6', '962d0e69-383a-4b1f-9531-6a612cc1f124', '5e93482d-fa86-4a80-a5f3-7b2d7f0a898c', '2020-01-01');
insert into rl_permissao_pessoa values (uuid_generate_v4(), '6d4bbdb1-ee37-4453-bc2e-c7ff58ad58f6', '962d0e69-383a-4b1f-9531-6a612cc1f124', '52fb5738-1181-48cf-a91f-afadfae0e98a', '2020-01-01');







