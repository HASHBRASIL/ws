
-- ----------------------------
--  Table structure for fin_tb_transacao_conta
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_transacao_conta";

CREATE TABLE "fin_tb_transacao_conta" (
  "transacao_conta_id" bigserial PRIMARY KEY,
  "con_id" int4 REFERENCES fin_tb_contas(con_id),
  "ds_transacao_conta" varchar(100),
  "dt_transacao_conta" date default now(),
  "tp_transacao_conta" char(1),
  "vl_transacao_conta" numeric(15,2) NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" timestamp(6) NULL,
  "id_grupo" uuid REFERENCES tb_grupo (id),
  "st_transacao_conta" int2,
  "ativo" int2 NOT NULL DEFAULT 1
);

ALTER TABLE "fin_tb_transacao_conta" OWNER TO "hash";
