--
--tb_cargos
--tb_cidades
--tb_contatos
--tb_contato_departamento
--tb_contato_referenciado
--tb_controle
--tb_empresas_grupo
--tb_enderecos
--tb_ufs
--tb_sis_feed
--tb_empresas
--tb_grupo_geografico
--ta_grupo_geografico_x_empresas
--tb_indicacao
--tb_segmento_atividade
--tb_sis_proprietario
--tb_sis_status_feed
--tb_tp_controle
--tb_tipo_endereco
--tb_tp_endereco_ref
--tb_sis_tipo_feed
--tb_tipo_pessoa
--tb_tipo_segmento
--mat_tb_tipo_unidade -- @todo já foi migrado no material.


-- ----------------------------
--  Table structure for tb_segmento_atividade
-- ----------------------------
DROP TABLE IF EXISTS "tb_segmento_atividade";
CREATE TABLE "tb_segmento_atividade" (
	"seg_id" int4 NOT NULL,
	"seg_descricacao" varchar(100) NOT NULL,
	"seg_ativo" int2,
	"tis_id" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_segmento_atividade" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_enderecos
-- ----------------------------
DROP TABLE IF EXISTS "tb_enderecos";
CREATE TABLE "tb_enderecos" (
	"id" int8 NOT NULL,
	"id_empresas_grupo" int4,
	"id_empresas" int4,
	"ope_id" int4,
	"pes_id" int4,
	"cep" varchar(8),
	"ativo" int2,
	"pais_id" int4,
	"ufs_id" int4,
	"cid_id" int4,
	"tipo_logradouro" varchar(50),
	"nome_logradouro" varchar(50),
	"numero" varchar(15),
	"complemento" varchar(45),
	"bairro" varchar(45),
	"temp_pais" varchar(100),
	"temp_estado" varchar(100),
	"temp_cidade" varchar(100),
	"tie_id" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_enderecos" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_tp_controle
-- ----------------------------
DROP TABLE IF EXISTS "tb_tp_controle";
CREATE TABLE "tb_tp_controle" (
	"id_tp_controle" int4 NOT NULL,
	"nome" varchar(50) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_tp_controle" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_contatos
-- ----------------------------
DROP TABLE IF EXISTS "tb_contatos";
CREATE TABLE "tb_contatos" (
	"id" int8 NOT NULL,
	"id_empresas" int4,
	"nome" varchar(100),
	"email1" varchar(100),
	"email2" varchar(100),
	"telefone1" char(12),
	"telefone2" char(12),
	"telefone3" char(12),
	"radio" varchar(45),
	"aniversario" date,
	"ativo" int2,
	"cdp_id" int4,
	"cre_id" int4,
	"smk_id" int4,
	"car_id" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_contatos" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_cidades
-- ----------------------------
DROP TABLE IF EXISTS "tb_cidades";
CREATE TABLE "tb_cidades" (
	"cid_id" int4 NOT NULL,
	"ufs_id" int4 NOT NULL,
	"cid_nome" varchar(50) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_cidades" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_sis_proprietario
-- ----------------------------
DROP TABLE IF EXISTS "tb_sis_proprietario";
CREATE TABLE "tb_sis_proprietario" (
	"id_proprietario" int4 NOT NULL,
	"id_workspace" int4,
	"nome_empresa" varchar(100),
	"logo" varchar(200),
	"logo_report" varchar(200),
	"cpf_cnpj" varchar(20),
	"telefone" varchar(100),
	"cep" varchar(8),
	"end_proprietario" varchar(200),
	"email" varchar(45),
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL,
	"ativo" int2 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_sis_proprietario" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_sis_feed
-- ----------------------------
DROP TABLE IF EXISTS "tb_sis_feed";
CREATE TABLE "tb_sis_feed" (
	"id_feed" int4 NOT NULL,
	"id_tipo_feed" int4 NOT NULL,
	"id_status_feed" int4,
	"feedback" text,
	"id_criacao_usuario" int2,
	"dt_criacao" timestamp(6) NULL,
	"ativo" int2 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_sis_feed" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_grupo_geografico
-- ----------------------------
DROP TABLE IF EXISTS "tb_grupo_geografico";
CREATE TABLE "tb_grupo_geografico" (
	"id_grupo_geografico" int8 NOT NULL,
	"nome" varchar(45),
	"descricao" varchar(500),
	"ativo" int2 NOT NULL,
	"id_criacao_usuario" int4 NOT NULL,
	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_grupo_geografico" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_empresas
-- ----------------------------
DROP TABLE IF EXISTS "tb_empresas";
CREATE TABLE "tb_empresas" (
	"id" int4 NOT NULL,
	"empresas_id_pai" int4,
	"nome_razao" varchar(100),
	"fantasia" varchar(100),
	"cnpj_cpf" varchar(14),
	"estadual" varchar(45),
	"municipal" varchar(45),
	"dt_cadastro" date NOT NULL,
	"dt_nasc_fundacao" date,
	"site" varchar(100),
	"telefone1" char(12),
	"telefone2" char(12),
	"telefone3" char(12),
	"email_corporativo" varchar(200),
	"ativo" int2,
	"observacoes" text,
	"transportador" int2 NOT NULL,
	"grupo" int2 NOT NULL,
	"funcionario" int2 NOT NULL,
	"tic_id" int4,
	"tif_id" int4,
	"grt_id" int4,
	"seg_id" int4,
	"ind_id" int4,
	"id_empresa_indicacao" int4,
	"poc_id" int4,
	"smk_id" int4,
	"tps_id" int2,
	"codigo_antigo" int4,
	"pessoa_fic" int4,
	"uasg" varchar(80),
	"pes_id_atendido_por" int4,
	"dt_nascimento" date,
	"nome_pai" varchar(45),
	"nome_mae" varchar(45),
	"id_criacao_usuario" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_empresas" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_sis_tipo_feed
-- ----------------------------
DROP TABLE IF EXISTS "tb_sis_tipo_feed";
CREATE TABLE "tb_sis_tipo_feed" (
	"id_tipo_feed" int4 NOT NULL,
	"nome" varchar(50),
	"ativo" int2 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_sis_tipo_feed" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_grupo
-- ----------------------------
DROP TABLE IF EXISTS "tb_grupo";
CREATE TABLE "tb_grupo" (
	"id" int4 NOT NULL,
	"nome" varchar(60),
	"cpf" varchar(60)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_grupo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_indicacao
-- ----------------------------
DROP TABLE IF EXISTS "tb_indicacao";
CREATE TABLE "tb_indicacao" (
	"ind_id" int4 NOT NULL,
	"ind_descricao" varchar(100) NOT NULL,
	"ind_ativo" int2
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_indicacao" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_tp_endereco_ref
-- ----------------------------
DROP TABLE IF EXISTS "tb_tp_endereco_ref";
CREATE TABLE "tb_tp_endereco_ref" (
	"id_endereco" int8 NOT NULL,
	"tie_id" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_tp_endereco_ref" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_sis_status_feed
-- ----------------------------
DROP TABLE IF EXISTS "tb_sis_status_feed";
CREATE TABLE "tb_sis_status_feed" (
	"id_status_feed" int4 NOT NULL,
	"nome" varchar(45) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_sis_status_feed" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_cargos
-- ----------------------------
DROP TABLE IF EXISTS "tb_cargos";
CREATE TABLE "tb_cargos" (
	"car_id" int4 NOT NULL,
	"car_descricao" varchar(200) NOT NULL,
	"car_ativo" int2
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_cargos" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_contato_referenciado
-- ----------------------------
DROP TABLE IF EXISTS "tb_contato_referenciado";
CREATE TABLE "tb_contato_referenciado" (
	"cre_id" int4 NOT NULL,
	"cre_descricao" varchar(100) NOT NULL,
	"cre_ativo" int2
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_contato_referenciado" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_tipo_pessoa
-- ----------------------------
DROP TABLE IF EXISTS "tb_tipo_pessoa";
CREATE TABLE "tb_tipo_pessoa" (
	"tps_id" int2 NOT NULL,
	"tps_descricao" varchar(45) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_tipo_pessoa" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_controle
-- ----------------------------
DROP TABLE IF EXISTS "tb_controle";
CREATE TABLE "tb_controle" (
	"id_controle" int8 NOT NULL,
	"id_gm_protocolo" int8,
	"id_gs_protocolo" int8,
	"id_tp_controle" int4,
	"codigo" varchar(50),
	"ativo" int2 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_controle" OWNER TO "hash";



-- @todo já foi migrado no mat
---- ----------------------------
----  Table structure for tb_tipo_unidade
---- ----------------------------
--DROP TABLE IF EXISTS "tb_tipo_unidade";
--CREATE TABLE "tb_tipo_unidade" (
--	"id_tipo_unidade" int4 NOT NULL,
--	"nome" varchar(100) NOT NULL,
--	"id_criacao_usuario" int4,
--	"dt_criacao" timestamp(6) NULL,
--	"ativo" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_tipo_unidade" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_tipo_segmento
-- ----------------------------
DROP TABLE IF EXISTS "tb_tipo_segmento";
CREATE TABLE "tb_tipo_segmento" (
	"tis_id" int4 NOT NULL,
	"tis_descricao" varchar(50) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_tipo_segmento" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_empresas_grupo
-- ----------------------------
DROP TABLE IF EXISTS "tb_empresas_grupo";
CREATE TABLE "tb_empresas_grupo" (
	"id" int4 NOT NULL,
	"razao" varchar(100) NOT NULL,
	"fantasia" varchar(100) NOT NULL,
	"cnpj" char(14),
	"estadual" varchar(45),
	"municipal" varchar(45),
	"uasg" varchar(45),
	"ativo" int2 NOT NULL,
	"id_pai" int4,
	"logomarca" bytea,
	"id_novo" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_empresas_grupo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_contato_departamento
-- ----------------------------
DROP TABLE IF EXISTS "tb_contato_departamento";
CREATE TABLE "tb_contato_departamento" (
	"cdp_id" int4 NOT NULL,
	"cdp_descricao" varchar(100),
	"cdp_ativo" int2
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_contato_departamento" OWNER TO "hash";

-- ----------------------------
--  Table structure for ta_grupo_geografico_x_empresas
-- ----------------------------
DROP TABLE IF EXISTS "ta_grupo_geografico_x_empresas";
CREATE TABLE "ta_grupo_geografico_x_empresas" (
	"id_grupo_geografico" int8 NOT NULL,
	"id_empresa" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_grupo_geografico_x_empresas" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_ufs
-- ----------------------------
DROP TABLE IF EXISTS "tb_ufs";
CREATE TABLE "tb_ufs" (
	"ufs_id" int4 NOT NULL,
	"ufs_sigla" varchar(10) NOT NULL,
	"ufs_nome" varchar(20) NOT NULL,
	"pais_id" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_ufs" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_tipo_endereco
-- ----------------------------
DROP TABLE IF EXISTS "tb_tipo_endereco";
CREATE TABLE "tb_tipo_endereco" (
	"tie_id" int4 NOT NULL,
	"tie_descricao" varchar(70) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_tipo_endereco" OWNER TO "hash";
