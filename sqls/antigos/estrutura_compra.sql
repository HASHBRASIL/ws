--    tb_co_campanha
--    tb_co_campanha_corporativa
--    tb_co_campanha_item
--    tb_co_compra
--    tb_co_compra_item
--    ta_co_compra_item_x_gm_opcao
--    tb_co_tp_comissao

-- tabelas verificadas


-- ----------------------------
--  Table structure for ta_co_compra_item_x_gm_opcao
-- ----------------------------
DROP TABLE IF EXISTS "ta_co_compra_item_x_gm_opcao";
CREATE TABLE "ta_co_compra_item_x_gm_opcao" (
    "id_compra_item" int8 NOT NULL,
    "id_opcao" int8 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_co_compra_item_x_gm_opcao" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_co_campanha
-- ----------------------------
DROP TABLE IF EXISTS "tb_co_campanha";
CREATE TABLE "tb_co_campanha" (
    "id_campanha" bigserial primary key,
    "nome" varchar(50) NOT NULL,
    "id_tp_comissao" int4,
    "porcent_comissao" int4,
    "porcent_multa" int4,
    "dt_inicio" timestamp(6) NULL,
    "dt_fim" timestamp(6) NULL,
    "vl_max_compra" numeric(10,2),
    "vl_min_compra" numeric(10,2),
    "qtd_compra" int4,
    "vl_adicional" numeric(10,2),
    "descricao" varchar(500),
    "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
    "dt_criacao" TIMESTAMP default now(),
    "ativo" int2 NOT NULL DEFAULT 1,
    "id_moeda" int2
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_co_campanha" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_co_campanha_item
-- ----------------------------
DROP TABLE IF EXISTS "tb_co_campanha_item";
CREATE TABLE "tb_co_campanha_item" (
    "id_campanha_item" bigserial primary key,
    "id_campanha" int8 NOT NULL,
    "id_item" int8 NOT NULL,
    "vl_unitario" varchar(45),
    "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
    "dt_criacao" TIMESTAMP default now(),
    "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_co_campanha_item" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_co_compra_item
-- ----------------------------
DROP TABLE IF EXISTS "tb_co_compra_item";
CREATE TABLE "tb_co_compra_item" (
    "id_compra_item" bigserial primary key,
    "id_compra" int8 NOT NULL,
    "id_item" int8 NOT NULL,
    "quantidade" int4,
    "vl_unitario" numeric(10,2),
    "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
    "dt_criacao" TIMESTAMP default now(),
    "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_co_compra_item" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_co_tp_comissao
-- ----------------------------
DROP TABLE IF EXISTS "tb_co_tp_comissao";
CREATE TABLE "tb_co_tp_comissao" (
    "id_tp_comissao" serial primary key,
    "nome" varchar(50) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_co_tp_comissao" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_co_compra
-- ----------------------------
DROP TABLE IF EXISTS "tb_co_compra";
CREATE TABLE "tb_co_compra" (
    "id_compra"  bigserial primary key,
    "id_campanha" int8 NOT NULL,
    "id_consultor" int4 NOT NULL,
    "finalizado" int2 NOT NULL,
    "total" numeric(10,2),
    "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
    "dt_criacao" TIMESTAMP default now(),
    "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_co_compra" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_co_campanha_corporativa
-- ----------------------------
DROP TABLE IF EXISTS "tb_co_campanha_corporativa";
CREATE TABLE "tb_co_campanha_corporativa" (
    "id_campanha_corporativa" bigserial primary key,
    "id_campanha" int8 NOT NULL,
    "id_corporativa" uuid REFERENCES tb_pessoa (id),
    "id_tp_comissao" int4,
    "porcent_comissao" int4,
    "vl_max_compra" numeric(10,2),
    "qtd_compra" int4,
    "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
    "dt_criacao" TIMESTAMP default now(),
    "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_co_campanha_corporativa" OWNER TO "hash";




ALTER TABLE "ta_co_compra_item_x_gm_opcao" ADD CONSTRAINT "fk_tb_co_compra_item_has_tb_gm_opcao_tb_co_compra_item1" FOREIGN KEY ("id_compra_item") REFERENCES "tb_co_compra_item" ("id_compra_item") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "ta_co_compra_item_x_gm_opcao" ADD CONSTRAINT "fk_tb_co_compra_item_has_tb_gm_opcao_tb_gm_opcao1" FOREIGN KEY ("id_opcao") REFERENCES "tb_gm_opcao" ("id_opcao") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_co_campanha" ADD CONSTRAINT "fk_tb_co_campanha_tb_co_tp_comissao1" FOREIGN KEY ("id_tp_comissao") REFERENCES "tb_co_tp_comissao" ("id_tp_comissao") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_co_campanha" ADD CONSTRAINT "fk_tb_co_campanha_tb_moedas1" FOREIGN KEY ("id_moeda") REFERENCES "tb_moedas" ("moe_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_co_campanha" ADD CONSTRAINT "fk_tb_co_campanha_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_co_campanha_item" ADD CONSTRAINT "fk_tb_co_campanha_item_tb_co_campanha1" FOREIGN KEY ("id_campanha") REFERENCES "tb_co_campanha" ("id_campanha") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_co_campanha_item" ADD CONSTRAINT "fk_tb_co_campanha_item_tb_gm_item1" FOREIGN KEY ("id_item") REFERENCES "tb_gm_item" ("id_item") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_co_campanha_item" ADD CONSTRAINT "fk_tb_co_campanha_item_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_co_compra_item" ADD CONSTRAINT "fk_tb_compra_item_tb_co_compra1" FOREIGN KEY ("id_compra") REFERENCES "tb_co_compra" ("id_compra") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_co_compra_item" ADD CONSTRAINT "fk_tb_compra_item_tb_gm_item1" FOREIGN KEY ("id_item") REFERENCES "tb_gm_item" ("id_item") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_co_compra_item" ADD CONSTRAINT "fk_tb_co_compra_item_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_co_compra" ADD CONSTRAINT "fk_tb_co_compra_tb_co_campanha1" FOREIGN KEY ("id_campanha") REFERENCES "tb_co_campanha" ("id_campanha") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_co_compra" ADD CONSTRAINT "fk_tb_co_compra_tb_empresas1" FOREIGN KEY ("id_consultor") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_co_compra" ADD CONSTRAINT "fk_tb_co_compra_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_co_campanha_corporativa" ADD CONSTRAINT "fk_tb_co_campanha_coorporativa_tb_co_campanha1" FOREIGN KEY ("id_campanha") REFERENCES "tb_co_campanha" ("id_campanha") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_co_campanha_corporativa" ADD CONSTRAINT "fk_tb_co_campanha_coorporativa_tb_empresas1" FOREIGN KEY ("id_corporativa") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_co_campanha_corporativa" ADD CONSTRAINT "fk_tb_co_campanha_corporativa_tb_co_tp_comissao1" FOREIGN KEY ("id_tp_comissao") REFERENCES "tb_co_tp_comissao" ("id_tp_comissao") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_co_campanha_corporativa" ADD CONSTRAINT "fk_tb_co_campanha_corporativa_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
