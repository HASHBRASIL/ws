


-- ----------------------------
--  Table structure for ta_gs_servico_empresas
-- ----------------------------
DROP TABLE IF EXISTS "ta_gs_servico_empresas";
CREATE TABLE "ta_gs_servico_empresas" (
	"id_servico" int8 NOT NULL,
	"id_empresas" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_gs_servico_empresas" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_servico
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_servico";
CREATE TABLE "tb_gs_servico" (
	"id_servico" int8 NOT NULL,
	"id_grupo" int8,
	"id_subgrupo" int8,
	"id_classe" int8,
	"nome" varchar(100) NOT NULL COLLATE "default",
	"descricao" varchar(255) COLLATE "default",
	"unidade" float4 NOT NULL,
	"id_tipo_unidade" int4 NOT NULL,
	"tipo_servico_interno" int4 NOT NULL,
	"tipo_servico_externo" int2 NOT NULL,
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_servico" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_gs_tarefa
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_tarefa";
CREATE TABLE "tb_gs_tarefa" (
	"id_tarefa" int4 NOT NULL,
	"id_servico" int8 NOT NULL,
	"nome" varchar(45) COLLATE "default",
	"tempo_extimado" int4 NOT NULL,
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_tarefa" OWNER TO "hash";


-- ----------------------------
--  Table structure for ta_gs_servico_centro_custo
-- ----------------------------
DROP TABLE IF EXISTS "ta_gs_servico_centro_custo";
CREATE TABLE "ta_gs_servico_centro_custo" (
	"id_servico" int8 NOT NULL,
	"cec_id" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_gs_servico_centro_custo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_tipo_unidade
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_tipo_unidade";
CREATE TABLE "tb_gs_tipo_unidade" (
	"id_tipo_unidade" int4 NOT NULL,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_tipo_unidade" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_componente
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_componente";
CREATE TABLE "tb_gs_componente" (
	"id_componente" int8 NOT NULL,
	"id_orcamento" int8 NOT NULL,
	"id_tp_servico" int4 NOT NULL,
	"id_tp_componente" int4 NOT NULL,
	"id_servico" int8 NOT NULL,
	"id_valor_servico" int4 NOT NULL,
	"caracteristica" varchar(255) COLLATE "default",
	"quantidade" int4,
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_componente" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_protocolo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_protocolo";
CREATE TABLE "tb_gs_protocolo" (
	"id_protocolo" int8 NOT NULL,
	"id_tp_entrada" int4 NOT NULL,
	"id_empresa_receptora" int4,
	"id_empresa_fornecedor" int4,
	"id_operacao_requisitante" int4,
	"id_operacao_requisitado" int4,
	"id_centro_custo" int4,
	"id_processo" int4,
	"dt_entrada" date,
	"hr_entrada" time(6),
	"observacao" varchar(255) COLLATE "default",
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_protocolo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_tp_servico
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_tp_servico";
CREATE TABLE "tb_gs_tp_servico" (
	"id_tp_servico" int4 NOT NULL,
	"nome" varchar(45) NOT NULL COLLATE "default"
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_tp_servico" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_tp_entrada
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_tp_entrada";
CREATE TABLE "tb_gs_tp_entrada" (
	"id_tp_entrada" int4 NOT NULL,
	"nome" varchar(50) COLLATE "default",
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_tp_entrada" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_gs_grupo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_grupo";
CREATE TABLE "tb_gs_grupo" (
	"id_grupo" int8 NOT NULL,
	"nome" varchar(100) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL,
	"id_atualizacao_usuario" int4,
	"dt_atualizacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_grupo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_subgrupo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_subgrupo";
CREATE TABLE "tb_gs_subgrupo" (
	"id_subgrupo" int8 NOT NULL,
	"id_grupo" int8 NOT NULL,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_subgrupo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_tp_componente
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_tp_componente";
CREATE TABLE "tb_gs_tp_componente" (
	"id_tp_componente" int4 NOT NULL,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_tp_componente" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_classe
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_classe";
CREATE TABLE "tb_gs_classe" (
	"id_classe" int8 NOT NULL,
	"id_subgrupo" int8 NOT NULL,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_classe" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_tp_orcamento
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_tp_orcamento";
CREATE TABLE "tb_gs_tp_orcamento" (
	"id_tp_orcamento" int4 NOT NULL,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_tp_orcamento" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gs_orcamento
-- ----------------------------
DROP TABLE IF EXISTS "tb_gs_orcamento";
CREATE TABLE "tb_gs_orcamento" (
	"id_orcamento" int8 NOT NULL,
	"id_tp_orcamento" int4 NOT NULL,
	"id_empresa_cliente" int4 NOT NULL,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"especificacao" varchar(255) COLLATE "default",
	"quantidade" int4,
	"modelo" int2 NOT NULL,
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gs_orcamento" OWNER TO "hash";
