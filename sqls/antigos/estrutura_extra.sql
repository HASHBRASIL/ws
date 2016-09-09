
---- tabelas extras
--tb_mob_device -- @todo do que se trata este modulo?
--
--tb_free_tarefa -- @todo verificar qual o uso do modulo freelance
--
--tb_pcp_config_empresa -- @todo tabela não encontrada
--tb_pcp_timer
--
--tb_au_profile -- @todo  verificar modulo se é necessário
--ta_profile_x_user -- @todo  verificar modulo se é necessário
--ta_resource_x_profile -- @todo  verificar modulo se é necessário
--
--tb_centro_custo - @todo foi transformada em fin_tb_centro_custo - ajustar onde for necessário
--tb_gm_estoque   - @todo tabela existe no material - Não existe necessidade de se duplicar o DAO.
--tb_processo  @todo tabela existe no material - Não existe necessidade de se duplicar o DAO.
--tb_gm_movimento  @todo tabela existe no material - Não existe necessidade de se duplicar o DAO.
--tb_status_financeiro  @todo tabela existe no financeiro - Não existe necessidade de se duplicar o DAO.
--vw_status - @todo tabela não existe. - conferir lugares que tem a tabela e ajustar códigos.
--
--tb_au_resource - @todo tabela não existe. verificar modulo se é necessário


-- ----------------------------
--  Table structure for tb_au_profile
-- ----------------------------
DROP TABLE IF EXISTS "tb_au_profile";
CREATE TABLE "tb_au_profile" (
	"id_au_profile" int4 NOT NULL,
	"name_profile" varchar(75) NOT NULL,
	"module_profile" varchar(75) NOT NULL,
	"description_profile" text,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL,
	"ativo" int2 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_au_profile" OWNER TO "hash";

-- ----------------------------
--  Table structure for ta_resource_x_profile
-- ----------------------------
DROP TABLE IF EXISTS "ta_resource_x_profile";
CREATE TABLE "ta_resource_x_profile" (
	"id_au_resource" int4 NOT NULL,
	"id_au_profile" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_resource_x_profile" OWNER TO "hash";




-- ----------------------------
--  Table structure for tb_pcp_timer
-- ----------------------------
DROP TABLE IF EXISTS "tb_pcp_timer";
CREATE TABLE "tb_pcp_timer" (
	"id_timer" int4 NOT NULL,
	"empresas_id" int4 NOT NULL,
	"latitude" varchar(45),
	"longitude" varchar(45),
	"inicio_work" timestamp(6) NOT NULL,
	"fim_work" timestamp(6) NULL,
	"pro_id" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL,
	"ativo" int2 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_pcp_timer" OWNER TO "hash";

-- ----------------------------
--  Table structure for ta_profile_x_user
-- ----------------------------
DROP TABLE IF EXISTS "ta_profile_x_user";
CREATE TABLE "ta_profile_x_user" (
	"id_au_profile" int4 NOT NULL,
	"usu_id" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_profile_x_user" OWNER TO "hash";


-- @todo relatorio
-- ----------------------------
--  Table structure for tb_status_financeiro
-- ----------------------------
DROP TABLE IF EXISTS "tb_status_financeiro";
CREATE TABLE "tb_status_financeiro" (
	"stf_id" int2 NOT NULL,
	"stf_descricao" varchar(45) NOT NULL,
	"stf_ativo" int2,
	"stf_pagar" int2,
	"stf_receber" int2
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_status_financeiro" OWNER TO "hash";

COMMENT ON COLUMN "tb_status_financeiro"."stf_descricao" IS 'receita e despesa';
COMMENT ON COLUMN "tb_status_financeiro"."stf_pagar" IS 'Se 1, faz parte de contas a pagar se zero não faz parte ';
COMMENT ON COLUMN "tb_status_financeiro"."stf_receber" IS 'Se 1, faz parte de contas a receber, se zero não';

-- ----------------------------
--  Table structure for tb_centro_custo
-- ----------------------------
DROP TABLE IF EXISTS "tb_centro_custo";
CREATE TABLE "tb_centro_custo" (
	"cec_id" int4 NOT NULL,
	"cec_descricao" varchar(255),
	"cec_codigo" varchar(45),
	"cec_id_pai" int4,
	"cec_oculta" int2,
	"cec_operacional" int2 NOT NULL,
	"ativo" int2 NOT NULL,
	"vl_hora" numeric(10,2),
	"id_workspace" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_centro_custo" OWNER TO "hash";



-- @todo mobile
-- ----------------------------
--  Table structure for tb_mob_device
-- ----------------------------
DROP TABLE IF EXISTS "tb_mob_device";
CREATE TABLE "tb_mob_device" (
	"id_device" int4 NOT NULL,
	"nome" varchar(200) NOT NULL,
	"marca" varchar(200),
	"modelo" varchar(200),
	"uuid" varchar(20) NOT NULL,
	"mac" varchar(20),
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL,
	"ativo" int2 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_mob_device" OWNER TO "hash";



-- @todo freelance

-- ----------------------------
--  Table structure for tb_free_tarefa
-- ----------------------------
DROP TABLE IF EXISTS "tb_free_tarefa";
CREATE TABLE "tb_free_tarefa" (
	"id_tarefa" int4 NOT NULL,
	"dt_inicio" timestamp(6) NOT NULL,
	"dt_fim" timestamp(6) NULL,
	"percentual_completado" int4,
	"descricao" text NOT NULL,
	"horas_trabalhadas" int2,
	"concluido" int2 NOT NULL,
	"id_empresa" int4 NOT NULL,
	"id_workspace" int4,
	"dt_criacao" timestamp(6) NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"ativo" int2 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_free_tarefa" OWNER TO "hash";
