begin;


-- ----------------------------
--  Table structure for tb_grupos
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_grupos";

CREATE TABLE "fin_tb_grupos" (
  "gru_id" serial PRIMARY KEY,
  "gru_nome" varchar(45) COLLATE "default",
  "gru_ativo" int2 DEFAULT 1,
  "apl_id_inicial" int4,
  "gru_permite_valores_processo" int2,
  "gru_ativa_empresa_lote" int2,
  "gru_exporta_processo_excel" int2,
  "gru_edita_liq_fin" int2,
  "gru_imprimir_financeiro" int2
);

ALTER TABLE "fin_tb_grupos" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_centro_custo
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_centro_custo";
CREATE TABLE "fin_tb_centro_custo" (
  "cec_id" serial PRIMARY KEY ,
  "cec_descricao" varchar(255) COLLATE "default",
  "cec_codigo" varchar(45) COLLATE "default",
  "cec_id_pai" int4 REFERENCES fin_tb_centro_custo (cec_id),
  "cec_oculta" int2,
  "cec_operacional" int2 NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "vl_hora" numeric(10,2),
  "id_grupo" uuid REFERENCES tb_grupo (id)
);

ALTER TABLE "fin_tb_centro_custo" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_grupo_contas
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_grupo_contas";
CREATE TABLE "fin_tb_grupo_contas" (
  "grc_id" SMALLSERIAL PRIMARY KEY ,
  "grc_descricao" varchar(45) NOT NULL COLLATE "default",
  "grc_ativo" int2 DEFAULT 1
);

ALTER TABLE "fin_tb_grupo_contas" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_plano_contas
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_plano_contas";
CREATE TABLE "fin_tb_plano_contas" (
  "plc_id" SERIAL PRIMARY KEY ,
  "plc_cod_contabil" varchar(45) COLLATE "default",
  "plc_cod_reduzido" varchar(20) COLLATE "default",
  "plc_descricao" varchar(255) NOT NULL COLLATE "default",
  "plc_conta_redutora" int2,
  "grc_id" int2 REFERENCES fin_tb_grupo_contas (grc_id),
  "plc_id_pai" int4 REFERENCES fin_tb_plano_contas (plc_id),
  "plc_oculta" int2,
  "plc_contabil" int2 NOT NULL,
  "plc_resultado" int2 NOT NULL,
  "plc_transferencia" int2 NOT NULL,
  "id_grupo" uuid REFERENCES tb_grupo (id)
);

ALTER TABLE "fin_tb_plano_contas" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_tipo_movimento
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_tipo_movimento";
CREATE TABLE "fin_tb_tipo_movimento" (
  "tmv_id" SMALLSERIAL PRIMARY KEY,
  "tmv_descricao" varchar(45) NOT NULL COLLATE "default",
  "tmv_descricao2" varchar(45) COLLATE "default",
  "tmv_sigla" varchar(45) COLLATE "default",
  "tmv_ativo" int2 DEFAULT 1
)
WITH (OIDS=FALSE);

ALTER TABLE "fin_tb_tipo_movimento" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_moedas
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_moedas";
CREATE TABLE "fin_tb_moedas" (
  "moe_id" smallserial PRIMARY KEY,
  "moe_descricao" varchar(45) COLLATE "default",
  "moe_sigla" varchar(3) COLLATE "default",
  "moe_defaut" int2
)
WITH (OIDS=FALSE);

ALTER TABLE "fin_tb_moedas" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_operacoes
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_operacoes";
CREATE TABLE "fin_tb_operacoes" (
  "ope_id" SERIAL PRIMARY KEY,
  "ope_nome" varchar(100) NOT NULL COLLATE "default",
  "ope_cpf_cnpj" varchar(14) COLLATE "default",
  "ope_telefone1" varchar(45) COLLATE "default",
  "ope_telefone2" varchar(45) COLLATE "default",
  "ope_telefone3" varchar(45) COLLATE "default",
  "ope_email1" varchar(200) COLLATE "default",
  "ope_email2" varchar(200) COLLATE "default",
  "ope_ativo" int2 DEFAULT 1,
  "empresas_grupo_id" int4,
  "ope_emite_osi" int2,
  "ope_recebe_osi" int2,
  "id_grupo" uuid REFERENCES tb_grupo (id)
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_operacoes" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_agrupador_financeiro
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_agrupador_financeiro";

CREATE TABLE "fin_tb_agrupador_financeiro" (
  "id_agrupador_financeiro" bigserial PRIMARY KEY ,
  "id_pessoa_cliente" uuid REFERENCES tb_pessoa (id),
  "id_pessoa_faturado" uuid REFERENCES tb_pessoa (id),
  "fin_descricao" text NOT NULL COLLATE "default",
  "fin_valor" numeric(15,2) NOT NULL,
  "pro_id" int4,
  "fin_observacao" text COLLATE "default",
  "fin_nota_fiscal" int8,
  "plc_id" int4 REFERENCES fin_tb_plano_contas (plc_id),
  "tmv_id" int2 NOT NULL REFERENCES fin_tb_tipo_movimento (tmv_id),
  "moe_id" int2 NOT NULL REFERENCES fin_tb_moedas (moe_id),
  "cec_id" int4 REFERENCES fin_tb_centro_custo (cec_id),
  "ope_id" int4 REFERENCES fin_tb_operacoes (ope_id),
  "transferencia" int2 NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_agrupador_financeiro_correlato" int8 REFERENCES fin_tb_agrupador_financeiro (id_agrupador_financeiro),
  "id_grupo" uuid REFERENCES tb_grupo (id)
);


ALTER TABLE "fin_tb_agrupador_financeiro" OWNER TO "hash";

COMMENT ON COLUMN "fin_tb_agrupador_financeiro"."id_pessoa_faturado" IS 'faturado contra';



-- ----------------------------
--  Table structure for tb_bancos
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_bancos";
CREATE TABLE "fin_tb_bancos" (
  "bco_id" SERIAL PRIMARY KEY,
  "bco_comp" varchar(10) COLLATE "default",
  "bco_nome" varchar(255) NOT NULL COLLATE "default",
  "bco_site" varchar(255) COLLATE "default",
  "bco_ativo" int2 DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_bancos" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_status_financeiro
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_status_financeiro";
CREATE TABLE "fin_tb_status_financeiro" (
  "stf_id" SMALLSERIAL PRIMARY KEY ,
  "stf_descricao" varchar(45) NOT NULL COLLATE "default",
  "stf_ativo" int2 DEFAULT 1,
  "stf_pagar" int2,
  "stf_receber" int2
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_status_financeiro" OWNER TO "hash";

COMMENT ON COLUMN "fin_tb_status_financeiro"."stf_descricao" IS 'receita e despesa';
COMMENT ON COLUMN "fin_tb_status_financeiro"."stf_pagar" IS 'Se 1, faz parte de contas a pagar se zero não faz parte ';
COMMENT ON COLUMN "fin_tb_status_financeiro"."stf_receber" IS 'Se 1, faz parte de contas a receber, se zero não';




-- ----------------------------
--  Table structure for tb_tipo_contabanco
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_tipo_contabanco";
CREATE TABLE "fin_tb_tipo_contabanco" (
  "tcb_id" SMALLSERIAL PRIMARY KEY ,
  "tcb_descricao" varchar(45) NOT NULL COLLATE "default",
  "tcb_sigla" varchar(4) COLLATE "default",
  "tcb_ativo" int2 DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_tipo_contabanco" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_contas
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_contas";
CREATE TABLE "fin_tb_contas" (
  "con_id" SMALLSERIAL PRIMARY KEY ,
  "con_agencia" varchar(10) NOT NULL COLLATE "default",
  "con_age_digito" varchar(3) COLLATE "default",
  "con_numero" varchar(45) NOT NULL COLLATE "default",
  "con_digito" varchar(5) NOT NULL COLLATE "default",
  "tcb_id" int2 REFERENCES fin_tb_tipo_contabanco(tcb_id),
  "bco_id" int4 NOT NULL REFERENCES fin_tb_bancos (bco_id),
  "con_codnome" varchar(50) COLLATE "default",
  "con_ordem" varchar(2) COLLATE "default",
  "ativo" int2 NOT NULL DEFAULT 1,
  "dt_criacao" timestamp(6) NULL,
  "id_grupo" uuid REFERENCES tb_grupo(id),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa(id)
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_contas" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_credito
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_credito";
CREATE TABLE "fin_tb_credito" (
  "id_credito" serial PRIMARY KEY,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa(id),
  "limite_credito" varchar(45) NOT NULL COLLATE "default",
  "posicao_serasa" varchar(45) COLLATE "default",
  "numero_serasa" varchar(45) COLLATE "default",
  "data_consulta_serasa" date,
  "consultado_por" int2 NOT NULL,
  "situacao_serasa" int2,
  "analise_risco" varchar(200) NOT NULL COLLATE "default",
  "dt_criacao" timestamp(6) NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_credito" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_tipo_documento_externo
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_tipo_documento_externo";
CREATE TABLE "fin_tb_tipo_documento_externo" (
  "tie_id" SMALLSERIAL PRIMARY KEY,
  "tie_descricao" varchar(100) NOT NULL COLLATE "default",
  "tie_ativo" int2 DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_tipo_documento_externo" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_tipo_documento
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_tipo_documento";
CREATE TABLE "fin_tb_tipo_documento" (
  "tid_id" SMALLSERIAL PRIMARY KEY,
  "tid_descricao" varchar(100) NOT NULL COLLATE "default",
  "tid_ativo" int2 DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_tipo_documento" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_financeiro
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_financeiro";
CREATE TABLE "fin_tb_financeiro" (
  "fin_id" BIGSERIAL PRIMARY KEY ,
  "con_id" int2 REFERENCES fin_tb_contas (con_id),
  "tid_id" int2 REFERENCES fin_tb_tipo_documento (tid_id),
  "tie_id" int2 REFERENCES fin_tb_tipo_documento_externo (tie_id),
  "fin_vencimento" date,
  "fin_compensacao" date,
  "fin_competencia" date,
  "fin_descricao" text NOT NULL COLLATE "default",
  "fin_valor" numeric(10,2),
  "fin_emissao" date,
  "fin_observacao" text COLLATE "default",
  "ope_id" int4 REFERENCES fin_tb_operacoes (ope_id),
  "plc_id" int4 REFERENCES fin_tb_plano_contas (plc_id),
  "id_pessoa_faturado" uuid REFERENCES tb_pessoa (id),
  "fin_numero_doc" varchar(45) COLLATE "default",
  "fin_num_doc_os" varchar(45) COLLATE "default",
  "cec_id" int4 REFERENCES fin_tb_centro_custo (cec_id),
  "id_criacao_usuario"  uuid REFERENCES tb_pessoa (id),
  "dt_criacao" timestamp(6) NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_financeiro_correlato" int8 REFERENCES fin_tb_financeiro (fin_id),
  "id_agrupador_financeiro" int8 NOT NULL REFERENCES fin_tb_agrupador_financeiro (id_agrupador_financeiro)
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_financeiro" OWNER TO "hash";

COMMENT ON COLUMN "fin_tb_financeiro"."tid_id" IS 'Tipo de documento interno';
COMMENT ON COLUMN "fin_tb_financeiro"."tie_id" IS 'Tipo de documento externo';
COMMENT ON COLUMN "fin_tb_financeiro"."fin_vencimento" IS 'tanto para pagamento quanto para recebimento';
COMMENT ON COLUMN "fin_tb_financeiro"."fin_competencia" IS 'mmaaaa';
COMMENT ON COLUMN "fin_tb_financeiro"."fin_emissao" IS 'quando a operação foi cadastrada';
COMMENT ON COLUMN "fin_tb_financeiro"."id_pessoa_faturado" IS 'faturado contra';
COMMENT ON COLUMN "fin_tb_financeiro"."fin_numero_doc" IS 'Numero de documento interno';
COMMENT ON COLUMN "fin_tb_financeiro"."fin_num_doc_os" IS 'Numero de documento externo';



-- ----------------------------
--  Table structure for rel_sacado_financeiro
-- ----------------------------
DROP TABLE IF EXISTS "fin_rel_sacado_financeiro";
CREATE TABLE "fin_rel_sacado_financeiro" (
  "tb_financeiro_fin_id" int8 REFERENCES fin_tb_financeiro (fin_id),
  "id_pessoa_empresa" uuid REFERENCES tb_pessoa(id),
  "empresas_grupo_id" int4,
  "id_pessoa" uuid REFERENCES tb_pessoa(id)
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_rel_sacado_financeiro" OWNER TO "hash";



-- ----------------------------
--  Table structure for th_agrupador_financeiro
-- ----------------------------
DROP TABLE IF EXISTS "fin_th_agrupador_financeiro";
CREATE TABLE "fin_th_agrupador_financeiro" (
  "id_th_agrupador_financeiro" SERIAL PRIMARY KEY ,
  "id_agrupador_financeiro" int8 REFERENCES fin_tb_agrupador_financeiro (id_agrupador_financeiro),
  "id_pessoa_cliente" uuid REFERENCES tb_pessoa (id),
  "id_pessoa_faturado" uuid REFERENCES tb_pessoa (id),
  "fin_valor" numeric(15,2) NOT NULL,
  "fin_descricao" text NOT NULL COLLATE "default",
  "pro_id" int4,
  "fin_observacao" text COLLATE "default",
  "fin_nota_fiscal" int8,
  "plc_id" int4 REFERENCES fin_tb_plano_contas (plc_id),
  "tmv_id" int2 NOT NULL REFERENCES fin_tb_tipo_movimento (tmv_id),
  "moe_id" int2 NOT NULL REFERENCES fin_tb_moedas (moe_id),
  "cec_id" int4 REFERENCES fin_tb_centro_custo (cec_id),
  "ope_id" int4 REFERENCES fin_tb_operacoes (ope_id),
  "transferencia" int2 NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" timestamp(6) NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_grupo" uuid REFERENCES tb_grupo (id),
  "id_agrupador_financeiro_correlato" int8 REFERENCES fin_tb_agrupador_financeiro (id_agrupador_financeiro)
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_th_agrupador_financeiro" OWNER TO "hash";


-- ----------------------------
--  Table structure for th_financeiro
-- ----------------------------
DROP TABLE IF EXISTS "fin_th_financeiro";
CREATE TABLE "fin_th_financeiro" (
  "id_th_financeiro" SERIAL PRIMARY KEY ,
  "con_id" int2 REFERENCES fin_tb_contas (con_id),
  "tid_id" int2 REFERENCES fin_tb_tipo_documento (tid_id),
  "tie_id" int2 REFERENCES fin_tb_tipo_documento_externo (tie_id),
  "fin_id" int8 NOT NULL REFERENCES fin_tb_financeiro(fin_id),
  "cec_id" int4 REFERENCES fin_tb_centro_custo (cec_id),
  "ope_id" int4 REFERENCES fin_tb_operacoes (ope_id),
  "plc_id" int4 REFERENCES fin_tb_plano_contas (plc_id),
  "id_pessoa_faturado" uuid REFERENCES tb_pessoa (id),
  "fin_compensacao" date,
  "fin_competencia" date,
  "fin_descricao" text NOT NULL COLLATE "default",
  "fin_valor" numeric(10,2) NOT NULL,
  "fin_vencimento" date,
  "fin_emissao" date,
  "fin_observacao" text COLLATE "default",
  "fin_numero_doc" numeric(20,0),
  "fin_num_doc_os" int8,
  "fin_excluido" int2,
  "pago" int2,
  "id_criacao_usuario"  uuid REFERENCES tb_pessoa (id),
  "dt_criacao" timestamp(6) NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_financeiro_correlato" int8 REFERENCES fin_tb_financeiro (fin_id),
  "id_agrupador_financeiro" int8 NOT NULL REFERENCES fin_tb_agrupador_financeiro (id_agrupador_financeiro)
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_th_financeiro" OWNER TO "hash";

COMMENT ON COLUMN "fin_th_financeiro"."fin_competencia" IS 'mmaaaa';
COMMENT ON COLUMN "fin_th_financeiro"."fin_emissao" IS 'quando a operação foi cadastrada';
COMMENT ON COLUMN "fin_th_financeiro"."fin_numero_doc" IS 'Numero de documento interno';
COMMENT ON COLUMN "fin_th_financeiro"."fin_num_doc_os" IS 'Numero de documento externo';
COMMENT ON COLUMN "fin_th_financeiro"."fin_excluido" IS 'Este campo serve para dizer se um registro errado esta excluido, seja el vindo da importação ou não.';


-- ----------------------------
--  Table structure for tb_recorrencia_fin
-- ----------------------------
DROP TABLE IF EXISTS "fin_tb_recorrencia_fin";
CREATE TABLE "fin_tb_recorrencia_fin" (
  "rcf_id" SMALLSERIAL PRIMARY KEY ,
  "rcf_descricao" varchar(45) COLLATE "default",
  "rcf_ativo" int2 DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "fin_tb_recorrencia_fin" OWNER TO "hash";



commit;


ALTER TABLE fin_tb_grupos ALTER gru_ativo set DEFAULT 1;
ALTER TABLE fin_tb_centro_custo ALTER ativo set DEFAULT 1;
ALTER TABLE fin_tb_grupo_contas ALTER grc_ativo set DEFAULT 1;
ALTER TABLE fin_tb_tipo_movimento ALTER tmv_ativo set DEFAULT 1;
ALTER TABLE fin_tb_operacoes ALTER ope_ativo set DEFAULT 1;
ALTER TABLE fin_tb_agrupador_financeiro ALTER ativo set DEFAULT 1;
ALTER TABLE fin_tb_bancos ALTER bco_ativo set DEFAULT 1;
ALTER TABLE fin_tb_status_financeiro ALTER stf_ativo set DEFAULT 1;
ALTER TABLE fin_tb_tipo_contabanco ALTER tcb_ativo set DEFAULT 1;
ALTER TABLE fin_tb_contas ALTER ativo set DEFAULT 1;
ALTER TABLE fin_tb_credito ALTER ativo set DEFAULT 1;
ALTER TABLE fin_tb_tipo_documento_externo ALTER tie_ativo set DEFAULT 1;
ALTER TABLE fin_tb_tipo_documento ALTER tid_ativo set DEFAULT 1;
ALTER TABLE fin_tb_financeiro ALTER ativo set DEFAULT 1;
ALTER TABLE fin_th_agrupador_financeiro ALTER ativo set DEFAULT 1;
ALTER TABLE fin_th_financeiro ALTER ativo set DEFAULT 1;
ALTER TABLE fin_tb_recorrencia_fin ALTER rcf_ativo set DEFAULT 1;




ALTER TABLE fin_tb_plano_contas ALTER plc_contabil set DEFAULT 0;
ALTER TABLE fin_tb_plano_contas ALTER plc_resultado set DEFAULT 0;
ALTER TABLE fin_tb_plano_contas ALTER plc_transferencia set DEFAULT 0;








ALTER TABLE fin_th_agrupador_financeiro ALTER transferencia set DEFAULT 0;
ALTER TABLE fin_tb_agrupador_financeiro ALTER transferencia set DEFAULT 0;
ALTER TABLE fin_th_financeiro ALTER pago set DEFAULT 0;

