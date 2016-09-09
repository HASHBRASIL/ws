
--tb_rh_admissao
--tb_rh_caged
--tb_rh_calculo_ponto
--tb_rh_categoria
--tb_rh_cbo
--tb_rh_certidao_civil
--tb_rh_ci
--tb_rh_config_extra
--tb_rh_config_horario
--tb_rh_configuracao
--ta_rh_configuracao_x_usuario
--tb_rh_contrato
--tb_rh_dados_funcionais
--tb_rh_dados_ponto
--tb_rh_deficiencia
--tb_rh_documento_identidade
--tb_rh_entrada_sintetico
--tb_rh_escala
--tb_rh_extra
--tb_rh_falta
--tb_rh_feriados
--tb_rh_fgts
--tb_rh_folha_de_pagamento
--tb_rh_funcionario
--tb_rh_horario - @todo tabelas não compativeis com postgresql - ajustar
--tb_rh_horario_funcionario -  @todo tabelas não compativeis com postgresql - ajustar
--tb_rh_instrucao
--tb_rh_justificacao_ponto
--tb_rh_local
--tb_rh_modelo_sintetico
--tb_rh_nacionalidade
--tb_rh_natureza_sintetico
--tb_rh_ocorrencia
--tb_rh_outro
--tb_rh_passagem
--tb_rh_registro_ponto
--tb_rh_raca
--rel_rh_financeiro
--tb_rh_servico_militar
--tb_rh_tipo_admissao
--tb_rh_tp_pagamento
--tb_rh_vinculo



-- ----------------------------
--  Table structure for tb_rh_ci
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_ci";
CREATE TABLE "tb_rh_ci" (
	"id_rh_ci" serial PRIMARY KEY,
	"id_rh_funcionario" int4 NOT NULL,
	"inscricao" numeric(10,0),
	"dt_cadastro" varchar(45),
	"gera_gps" int2,
	"deducao_gps" numeric(10,0),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_ci" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_rh_falta
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_falta";
CREATE TABLE "tb_rh_falta" (
	"id_falta" serial PRIMARY KEY,
	"id_calculo_ponto" int4 NOT NULL,
	"hora" time(6),
	"banco_horas" int2 NOT NULL,
	"dsr" int2 NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "id_atualizacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_atualizacao" TIMESTAMP default null
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_falta" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_config_extra
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_config_extra";
CREATE TABLE "tb_rh_config_extra" (
	"id_config_extra" serial PRIMARY KEY,
	"id_horario" int4 NOT NULL,
	"tipo_dia" int2,
	"hora_inicio" time(6),
	"hora_fim" time(6),
	"porcentagem_desconto" int4,
	"banco_horas" int2 NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "id_atualizacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_atualizacao" TIMESTAMP default null
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_config_extra" OWNER TO "hash";

COMMENT ON COLUMN "tb_rh_config_extra"."tipo_dia" IS '1 - dia trabalhados, 2 - folga, 3 - feriado';



-- ----------------------------
--  Table structure for tb_rh_escala
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_escala";
CREATE TABLE "tb_rh_escala" (
	"id_rh_escala" serial PRIMARY KEY,
	"descricao" varchar(255),
	"revezamento" varchar(150),
	"dias_trabalhado" int4,
	"dias_trabalho" int4,
	"dias_folga" int4,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_escala" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_tp_pagamento
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_tp_pagamento";
CREATE TABLE "tb_rh_tp_pagamento" (
	"id_tp_pagamento" serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_tp_pagamento" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_fgts
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_fgts";
CREATE TABLE "tb_rh_fgts" (
	"id_rh_fgts" serial PRIMARY KEY,
	"id_rh_funcionario" int4 NOT NULL,
	"opta" int2,
	"dt_opcao" date,
	"dt_retratacao" date,
	"codigo_sefip" varchar(50),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_fgts" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_dados_funcionais
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_dados_funcionais";
CREATE TABLE "tb_rh_dados_funcionais" (
	"id_rh_dados_funcionais" serial PRIMARY KEY,
	"id_rh_funcionario" int4 NOT NULL,
	"id_rh_cbo" int4,
	"vencto_atestado" date,
	"funcao_ppp" varchar(50),
	"local" int4,
	"codigo_municipio" int4,
	"id_rh_escala" int4,
	"dt_inicio_escala" date,
	"id_rh_passagem" int4,
	"id_sindicato" int4,
	"hora_semanal" int4,
	"hora_mensal" int4,
	"salario" numeric(10,2),
	"tipo_de_salario" int4,
	"entrada" time(6),
	"saida" time(6),
	"refeicao_entrada" time(6),
	"refeicao_saida" time(6),
	"descanso" varchar(50),
	"horario_especial" time(6),
	"faixa_reajuste" varchar(3),
	"adiantamento" int2,
	"vale_alimentacao" int2,
	"banco" varchar(10),
	"agencia" varchar(50),
	"conta_bancaria" varchar(50),
	"operacao_bancaria" varchar(50),
	"dt_ultima_ferias" date,
	"dt_previcao_ferias" date,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
	"id_rh_local" int4,
	"id_rh_sindicado" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_dados_funcionais" OWNER TO "hash";

COMMENT ON COLUMN "tb_rh_dados_funcionais"."hora_semanal" IS '	';


-- ----------------------------
--  Table structure for tb_rh_horario_funcionario
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_horario_funcionario";
CREATE TABLE "tb_rh_horario_funcionario" (
	"id_horario_funcionario" serial PRIMARY KEY,
	"id_horario" int4 NOT NULL,
	"id_rh_funcionario" int4 NOT NULL,
	"data" date,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_horario_funcionario" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_deficiencia
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_deficiencia";
CREATE TABLE "tb_rh_deficiencia" (
	"id_rh_deficiencia" serial PRIMARY KEY,
	"nome" varchar(300)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_deficiencia" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_natureza_sintetico
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_natureza_sintetico";
CREATE TABLE "tb_rh_natureza_sintetico" (
	"id_rh_natureza_sintetico" serial PRIMARY KEY,
	"nome" varchar(45),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_natureza_sintetico" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_documento_identidade
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_documento_identidade";
CREATE TABLE "tb_rh_documento_identidade" (
	"id_rh_documento_identidade" serial PRIMARY KEY,
	"id_rh_funcionario" int4 NOT NULL,
	"tipo_identidade" int2,
	"identidade" varchar(8),
	"expedida" date,
	"orgao_expididor" varchar(45),
	"municipio_expedida" varchar(100),
	"ctps" int4,
	"serie" int4,
	"uf" int4,
	"ctps_expedida" date,
	"opta_pis" int2,
	"pis" varchar(45),
	"dt_opta_pis" date,
	"titulo_eleitoral" int4,
	"zona" int4,
	"secao" int4,
	"cnh" int4,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_documento_identidade" OWNER TO "hash";


-- @todo tabela existe na base MAS não é utilizada
---- ----------------------------
----  Table structure for tb_rh_sindicado
---- ----------------------------
--DROP TABLE IF EXISTS "tb_rh_sindicado";
--CREATE TABLE "tb_rh_sindicado" (
--	"id_rh_sindicado" int4 NOT NULL,
--	"entidade" varchar(255),
--	"telefone" numeric(11,0),
--	"endereco" varchar(255),
--	"bairro" varchar(255),
--	"cidade" varchar(255),
--	"uf" int4,
--	"cep" numeric(10,0),
--	"cnpj" numeric(10,0),
--	"codigo" varchar(45),
--	"contato" varchar(255),
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL,
--	"ativo" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_rh_sindicado" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_nacionalidade
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_nacionalidade";
CREATE TABLE "tb_rh_nacionalidade" (
	"id_rh_nacionalidade" serial PRIMARY KEY,
	"nome" varchar(50)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_nacionalidade" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_folha_de_pagamento
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_folha_de_pagamento";
CREATE TABLE "tb_rh_folha_de_pagamento" (
	"id_rh_folha_de_pagamento" serial PRIMARY KEY,
	"tss_id" int8 NOT NULL,
	"tse_id" int8 NOT NULL,
	"id_empresa" int4 NOT NULL,
	"valor" numeric(10,2) NOT NULL,
	"descricao" text NOT NULL,
	"dt_competencia" date NOT NULL,
	"observacao" text,
	"plc_id" int4,
	"moe_id" int2 NOT NULL,
	"cec_id" int4,
	"ope_id" int4,
	"grupo_id" int4,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1,


  "id_grupo" uuid REFERENCES tb_grupo (id),
--	"id_workspace" int4,
	"id_tp_pagamento" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_folha_de_pagamento" OWNER TO "hash";

COMMENT ON COLUMN "tb_rh_folha_de_pagamento"."tss_id" IS 'transação de saida';
COMMENT ON COLUMN "tb_rh_folha_de_pagamento"."tse_id" IS 'transação de entrada';


-- ----------------------------
--  Table structure for tb_rh_local
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_local";
CREATE TABLE "tb_rh_local" (
	"id_rh_local" serial PRIMARY KEY,
	"descricao" varchar(255),
	"endereco" varchar(255),
	"bairro" varchar(255),
	"cidade" varchar(255),
	"uf" int4,
	"cep" numeric(10,0),
	"cnpj" numeric(10,0),
	"cei" varchar(255),
	"tipo" varchar(255),
	"vales" varchar(255),
	"responsavel" varchar(255),
	"centro_custo" varchar(255),
	"retencao" varchar(255),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_local" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_feriados
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_feriados";
CREATE TABLE "tb_rh_feriados" (
	"id_rh_feriados" serial PRIMARY KEY,
	"data" date,
	"descricao" varchar(255),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1

)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_feriados" OWNER TO "hash";




-- ----------------------------
--  Table structure for tb_rh_contrato
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_contrato";
CREATE TABLE "tb_rh_contrato" (
	"id_rh_contrato" serial PRIMARY KEY,
	"nome" varchar(300)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_contrato" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_certidao_civil
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_certidao_civil";
CREATE TABLE "tb_rh_certidao_civil" (
	"id_rh_certidao_civil" serial PRIMARY KEY,
	"id_rh_funcionario" int4 NOT NULL,
	"tipo_certidao_civil" varchar(60),
	"termo_matricula" varchar(50),
	"livro" varchar(50),
	"folha" varchar(50),
	"cartorio" varchar(150),
	"uf_certidao" int4,
	"municipio" varchar(100),
	"dt_emissao" date,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1

)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_certidao_civil" OWNER TO "hash";

-- ----------------------------
--  Table structure for ta_rh_configuracao_x_usuario
-- ----------------------------
DROP TABLE IF EXISTS "ta_rh_configuracao_x_usuario";
CREATE TABLE "ta_rh_configuracao_x_usuario" (
	"id_configuracao" int4 NOT NULL,
	"id_usuario" int4 NOT NULL,
	"nivel" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_rh_configuracao_x_usuario" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_entrada_sintetico
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_entrada_sintetico";
CREATE TABLE "tb_rh_entrada_sintetico" (
	"id_rh_entrada_sintetico" serial PRIMARY KEY,
	"nome" varchar(45),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_entrada_sintetico" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_rh_instrucao
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_instrucao";
CREATE TABLE "tb_rh_instrucao" (
	"id_rh_instrucao" serial PRIMARY KEY,
	"nome" varchar(300)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_instrucao" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_caged
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_caged";
CREATE TABLE "tb_rh_caged" (
	"id_rh_caged" serial PRIMARY KEY,
	"nome" varchar(300)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_caged" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_funcionario
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_funcionario";
CREATE TABLE "tb_rh_funcionario" (
	"id_rh_funcionario" serial PRIMARY KEY,
  "id_grupo" uuid REFERENCES tb_grupo (id),
--	"id_workspace" int4 NOT NULL,
	"id_empresa" int4 NOT NULL,
	"sexo" int2,
	"estado_civil" varchar(45),
	"dt_nascimento" date,
	"nome_conjuge" varchar(200),
	"naturalidade" varchar(50),
	"nacionalidade_mae" int4,
	"nacionalidade_pai" int4,
	"dt_demissao" date,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_funcionario" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_admissao
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_admissao";
CREATE TABLE "tb_rh_admissao" (
	"id_rh_admissao" serial PRIMARY KEY,
	"id_rh_funcionario" int4 NOT NULL,
	"dt_admissao" date,
	"id_rh_tipo_admissao" int4,
	"dt_vencto_experiencia" date,
	"dt_prorrog_experiencia" date,
	"dt_vencto_prazo" date,
	"alteracao_sefip" date,
	"registro_emprego" varchar(50),
	"folha_admissao" varchar(50),
	"id_rh_categoria" int4,
	"id_rh_ocorrencia" int4,
	"id_rh_caged" int4,
	"id_rh_vinculo" int4,
	"id_rh_instrucao" int4,
	"id_rh_nacionalidade" int4,
	"id_rh_raca" int4,
	"id_rh_deficiencia" int4,
	"dt_transferencia" date,
	"mes_database" int4,
	"prof_drt" varchar(50),
	"id_rh_contrato" int4,
	"dt_formacao_prof" date,
	"dt_ultima_reciclagem" date,
	"n_registro" int4,
	"n_livro" int4,
	"n_folha" int4,
	"tipo_sangue_rh" varchar(5),
	"sindicalizado" int2,
	"desconta_sindica" int2,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_admissao" OWNER TO "hash";






-- ----------------------------
--  Table structure for tb_rh_ocorrencia
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_ocorrencia";
CREATE TABLE "tb_rh_ocorrencia" (
	"id_rh_ocorrencia" serial PRIMARY KEY,
	"nome" varchar(300)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_ocorrencia" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_raca
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_raca";
CREATE TABLE "tb_rh_raca" (
	"id_rh_raca" serial PRIMARY KEY,
	"nome" varchar(45)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_raca" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_servico_militar
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_servico_militar";
CREATE TABLE "tb_rh_servico_militar" (
	"id_rh_servico_militar" serial PRIMARY KEY,
	"id_rh_funcionario" int4 NOT NULL,
	"reservista" int4,
	"serie_militar" varchar(50),
	"categoria" varchar(50),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_servico_militar" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_horario
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_horario";
CREATE TABLE "tb_rh_horario" (
	"id_horario" serial PRIMARY KEY,
	"nome" varchar(100) NOT NULL,
	"tolerancia_extra" int4,

  "id_grupo" uuid REFERENCES tb_grupo (id),
--	"id_workspace" int4,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_horario" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_rh_passagem
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_passagem";
CREATE TABLE "tb_rh_passagem" (
	"id_rh_passagem" serial PRIMARY KEY,
	"valor1" numeric(10,2),
	"valor2" numeric(10,2),
	"valor3" numeric(10,2),
	"quantidade1" int4,
	"quantidade2" int4,
	"quantidade3" int4,
	"linhas1" varchar(150),
	"linhas2" varchar(150),
	"linhas3" varchar(150),
	"descricao" varchar(255),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_passagem" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_configuracao
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_configuracao";
CREATE TABLE "tb_rh_configuracao" (
	"id_configuracao" serial PRIMARY KEY,
  "id_grupo" uuid REFERENCES tb_grupo (id),
--	"id_workspace" int4,
	"dia_inicio_folha" int4 NOT NULL,
	"dsr" int2,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),

  "id_atualizacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_atualizacao" TIMESTAMP default null
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_configuracao" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_config_horario
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_config_horario";
CREATE TABLE "tb_rh_config_horario" (
	"id_config_horario" serial PRIMARY KEY,
	"id_horario" int4 NOT NULL,
	"entrada1" time(6),
	"saida1" time(6),
	"entrada2" time(6),
	"saida2" time(6),
	"tolerancia_extra" int4,
	"tolerancia_falta" int4,
	"fechamento" time(6),
	"almoco_livre" int2 NOT NULL,
	"compensado" int2 NOT NULL,
	"semana" int2 NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_config_horario" OWNER TO "hash";

-- @todo ISO não pode ser utilizado.
COMMENT ON COLUMN "tb_rh_config_horario"."semana" IS 'Dia da semana de acordo com a ISO 8601 (1 = segunda-feira, 7 = Domingo)';




-- ----------------------------
--  Table structure for tb_rh_calculo_ponto
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_calculo_ponto";
CREATE TABLE "tb_rh_calculo_ponto" (
	"id_calculo_ponto" serial PRIMARY KEY,
	"id_rh_funcionario" int4 NOT NULL,
	"id_horario" int4,
	"data" date NOT NULL,
	"hora_extra" time(6),
	"hora_falta" time(6),

  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),

  "id_atualizacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_atualizacao" TIMESTAMP default null
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_calculo_ponto" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_outro
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_outro";
CREATE TABLE "tb_rh_outro" (
	"id_rh_outro" serial PRIMARY KEY,
	"cartao_facil" int4,
	"alvara_judicial" int2,
	"id_rh_funcionario" int4 NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_outro" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_extra
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_extra";
CREATE TABLE "tb_rh_extra" (
	"id_extra" serial PRIMARY KEY,
	"id_calculo_ponto" int4 NOT NULL,
	"hora" time(6),
	"porcentagem" int4,
	"banco_horas" int2 NOT NULL,
	"id_aprovacao_gerente" uuid REFERENCES tb_pessoa (id),
	"id_aprovacao_diretor" uuid REFERENCES tb_pessoa (id),
	"aprovado" int2,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "id_atualizacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_atualizacao" TIMESTAMP default null
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_extra" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_cbo
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_cbo";
CREATE TABLE "tb_rh_cbo" (
	"id_rh_cbo" serial PRIMARY KEY,
	"codigo" int4,
	"nome" varchar(150),
	"tipo" varchar(45),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_cbo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_vinculo
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_vinculo";
CREATE TABLE "tb_rh_vinculo" (
	"id_rh_vinculo" serial PRIMARY KEY,
	"nome" varchar(300)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_vinculo" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_registro_ponto
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_registro_ponto";
CREATE TABLE "tb_rh_registro_ponto" (
	"id_rh_registro_ponto" serial PRIMARY KEY,
	"local" varchar(150),
	"descricao" varchar(255),
	"opcao" varchar(255),
	"arquivo" varchar(255),
	"arquivo_original" varchar(255),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_registro_ponto" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_categoria
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_categoria";
CREATE TABLE "tb_rh_categoria" (
	"id_rh_categoria" serial PRIMARY KEY,
	"nome" varchar(300)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_categoria" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_justificacao_ponto
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_justificacao_ponto";
CREATE TABLE "tb_rh_justificacao_ponto" (
	"id_rh_justificacao_ponto" serial PRIMARY KEY,
	"descricao" varchar(255),
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_justificacao_ponto" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_tipo_admissao
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_tipo_admissao";
CREATE TABLE "tb_rh_tipo_admissao" (
	"id_rh_tipo_admissao" serial PRIMARY KEY,
	"nome" varchar(300)
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_tipo_admissao" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_rh_dados_ponto
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_dados_ponto";
CREATE TABLE "tb_rh_dados_ponto" (
	"id_rh_dados_ponto" serial PRIMARY KEY,
	"id_calculo_ponto" int4,
	"nsr" numeric(9,0),
	"data" date,
	"hora" time(6),
	"pis" numeric(11,0),
	"tipo" int2 NOT NULL,
	"descricao" varchar(255),
	"id_rh_registro_ponto" int4,
	"id_rh_funcionario" int4,
	"id_rh_justificacao_ponto" int4,
	"duplicado" int2 NOT NULL,
	"posicao" int4,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_dados_ponto" OWNER TO "hash";

-- ----------------------------
--  Table structure for rel_rh_financeiro
-- ----------------------------
DROP TABLE IF EXISTS "rel_rh_financeiro";
CREATE TABLE "rel_rh_financeiro" (
	"fin_id" int8 NOT NULL,
	"id_rh_modelo_sintetico" int4 NOT NULL,
	"referencia" varchar(50),
	"vl_base" numeric(10,2)
)
WITH (OIDS=FALSE);
ALTER TABLE "rel_rh_financeiro" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_rh_modelo_sintetico
-- ----------------------------
DROP TABLE IF EXISTS "tb_rh_modelo_sintetico";
CREATE TABLE "tb_rh_modelo_sintetico" (
	"id_rh_modelo_sintetico" serial PRIMARY KEY,
	"codigo" int4 NOT NULL,
	"descricao" varchar(100) NOT NULL,
	"id_rh_entrada_sintetico" int4 NOT NULL,
	"inss" int2,
	"irrf" int2,
	"fgts" int2,
	"sindical" int2,
	"exibir" int2 NOT NULL,
	"id_rh_natureza_sintetico" int4 NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_rh_modelo_sintetico" OWNER TO "hash";




-- @todo tabelas MYSQL - não compativeis com postgreSQL
--
-- Table structure for table "tb_rh_horario"
--

DROP TABLE IF EXISTS "tb_rh_horario";
CREATE TABLE "tb_rh_horario" (
  "id_horario" bigserial primary key,
  "nome" varchar(100) NOT NULL,
  "tolerancia_extra" bigint,
--  "ativo" tinyint(4) NOT NULL DEFAULT '1',

  "id_grupo" uuid REFERENCES tb_grupo (id),

--  "id_workspace" int(11) DEFAULT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1
--  PRIMARY KEY ("id_horario"),
--  KEY "fk_tb_rh_horario_tb_usuarios1_idx" ("id_criacao_usuario"),
--  KEY "fk_tb_rh_horario_tb_workspace1_idx" ("id_workspace"),
--  CONSTRAINT "fk_tb_rh_horario_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON DELETE NO ACTION ON UPDATE NO ACTION,
--  CONSTRAINT "fk_tb_rh_horario_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON DELETE NO ACTION ON UPDATE NO ACTION
);

----
---- Table structure for table "tb_rh_horario_funcionario"
----
--
DROP TABLE IF EXISTS "tb_rh_horario_funcionario";
CREATE TABLE "tb_rh_horario_funcionario" (
  "id_horario_funcionario" bigserial primary key,
  "id_horario" bigint,
  "id_rh_funcionario" bitint NOT NULL,
  "data" date,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "ativo" int2 NOT NULL DEFAULT 1

--  PRIMARY KEY ("id_horario_funcionario"),
--  KEY "fk_tb_rh_horario_funcionario_tb_rh_horario1_idx" ("id_horario"),
--  KEY "fk_tb_rh_horario_funcionario_tb_rh_funcionario1_idx" ("id_rh_funcionario"),
--  KEY "fk_tb_rh_horario_funcionario_tb_usuarios1_idx" ("id_criacao_usuario"),
--  CONSTRAINT "fk_tb_rh_horario_funcionario_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON DELETE NO ACTION ON UPDATE NO ACTION,
--  CONSTRAINT "fk_tb_rh_horario_funcionario_tb_rh_horario1" FOREIGN KEY ("id_horario") REFERENCES "tb_rh_horario" ("id_horario") ON DELETE NO ACTION ON UPDATE NO ACTION,
--  CONSTRAINT "fk_tb_rh_horario_funcionario_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON DELETE NO ACTION ON UPDATE NO ACTION
);


ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_caged1" FOREIGN KEY ("id_rh_caged") REFERENCES "tb_rh_caged" ("id_rh_caged") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_categoria1" FOREIGN KEY ("id_rh_categoria") REFERENCES "tb_rh_categoria" ("id_rh_categoria") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_contrato1" FOREIGN KEY ("id_rh_contrato") REFERENCES "tb_rh_contrato" ("id_rh_contrato") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_deficiencia1" FOREIGN KEY ("id_rh_deficiencia") REFERENCES "tb_rh_deficiencia" ("id_rh_deficiencia") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_instrucao1" FOREIGN KEY ("id_rh_instrucao") REFERENCES "tb_rh_instrucao" ("id_rh_instrucao") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_nacionalidade1" FOREIGN KEY ("id_rh_nacionalidade") REFERENCES "tb_rh_nacionalidade" ("id_rh_nacionalidade") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_ocorrencia1" FOREIGN KEY ("id_rh_ocorrencia") REFERENCES "tb_rh_ocorrencia" ("id_rh_ocorrencia") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_raca1" FOREIGN KEY ("id_rh_raca") REFERENCES "tb_rh_raca" ("id_rh_raca") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_tipo_admissao1" FOREIGN KEY ("id_rh_tipo_admissao") REFERENCES "tb_rh_tipo_admissao" ("id_rh_tipo_admissao") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_admissao" ADD CONSTRAINT "fk_tb_rh_admissao_tb_rh_vinculo1" FOREIGN KEY ("id_rh_vinculo") REFERENCES "tb_rh_vinculo" ("id_rh_vinculo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_rh_ci" ADD CONSTRAINT "fk_tb_rh_ci_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;



ALTER TABLE "tb_rh_calculo_ponto" ADD CONSTRAINT "fk_tb_rh_calculo_ponto_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_calculo_ponto" ADD CONSTRAINT "fk_tb_rh_calculo_ponto_tb_rh_horario1" FOREIGN KEY ("id_horario") REFERENCES "tb_rh_horario" ("id_horario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_calculo_ponto" ADD CONSTRAINT "fk_tb_rh_calculo_ponto_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_calculo_ponto" ADD CONSTRAINT "fk_tb_rh_calculo_ponto_tb_usuarios2" FOREIGN KEY ("id_atualizacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;



ALTER TABLE "tb_rh_certidao_civil" ADD CONSTRAINT "fk_tb_rh_certidao_civil_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_rh_ci" ADD CONSTRAINT "fk_tb_rh_ci_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_rh_config_extra" ADD CONSTRAINT "fk_tb_rh_config_extra_tb_rh_horario1" FOREIGN KEY ("id_horario") REFERENCES "tb_rh_horario" ("id_horario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_config_extra" ADD CONSTRAINT "fk_tb_rh_config_extra_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_config_extra" ADD CONSTRAINT "fk_tb_rh_config_extra_tb_usuarios2" FOREIGN KEY ("id_atualizacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_rh_config_horario" ADD CONSTRAINT "fk_tb_rh_config_horario_tb_rh_horario1" FOREIGN KEY ("id_horario") REFERENCES "tb_rh_horario" ("id_horario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_config_horario" ADD CONSTRAINT "fk_tb_rh_config_horario_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

--ALTER TABLE "tb_rh_configuracao" ADD CONSTRAINT "fk_tb_rh_configuracao_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_configuracao" ADD CONSTRAINT "fk_tb_rh_configuracao_tb_usuarios2" FOREIGN KEY ("id_atualizacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_configuracao" ADD CONSTRAINT "fk_tb_rh_configuracao_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "ta_rh_configuracao_x_usuario" ADD CONSTRAINT "fk_tb_rh_configuracao_has_tb_usuarios_tb_rh_configuracao1" FOREIGN KEY ("id_configuracao") REFERENCES "tb_rh_configuracao" ("id_configuracao") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "ta_rh_configuracao_x_usuario" ADD CONSTRAINT "fk_tb_rh_configuracao_has_tb_usuarios_tb_usuarios1" FOREIGN KEY ("id_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


-- @todo ajustar para tabela pessoa
-- ALTER TABLE "tb_rh_dados_funcionais" ADD CONSTRAINT "fk_tb_rh_dados_funcionais_tb_empresas1" FOREIGN KEY ("id_sindicato") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_rh_dados_funcionais" ADD CONSTRAINT "fk_tb_rh_dados_funcionais_tb_rh_cbo1" FOREIGN KEY ("id_rh_cbo") REFERENCES "tb_rh_cbo" ("id_rh_cbo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_dados_funcionais" ADD CONSTRAINT "fk_tb_rh_dados_funcionais_tb_rh_escala1" FOREIGN KEY ("id_rh_escala") REFERENCES "tb_rh_escala" ("id_rh_escala") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_dados_funcionais" ADD CONSTRAINT "fk_tb_rh_dados_funcionais_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_dados_funcionais" ADD CONSTRAINT "fk_tb_rh_dados_funcionais_tb_rh_local1" FOREIGN KEY ("id_rh_local") REFERENCES "tb_rh_local" ("id_rh_local") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_dados_funcionais" ADD CONSTRAINT "fk_tb_rh_dados_funcionais_tb_rh_passagem1" FOREIGN KEY ("id_rh_passagem") REFERENCES "tb_rh_passagem" ("id_rh_passagem") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_dados_funcionais" ADD CONSTRAINT "fk_tb_rh_dados_funcionais_tb_rh_sindicado1" FOREIGN KEY ("id_rh_sindicado") REFERENCES "tb_rh_sindicado" ("id_rh_sindicado") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_rh_dados_ponto" ADD CONSTRAINT "fk_tb_rh_dados_ponto_tb_rh_calculo_ponto1" FOREIGN KEY ("id_calculo_ponto") REFERENCES "tb_rh_calculo_ponto" ("id_calculo_ponto") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_dados_ponto" ADD CONSTRAINT "fk_tb_rh_dados_ponto_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_dados_ponto" ADD CONSTRAINT "fk_tb_rh_dados_ponto_tb_rh_justificacao_ponto1" FOREIGN KEY ("id_rh_justificacao_ponto") REFERENCES "tb_rh_justificacao_ponto" ("id_rh_justificacao_ponto") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_dados_ponto" ADD CONSTRAINT "fk_tb_rh_dados_ponto_tb_rh_registro_ponto1" FOREIGN KEY ("id_rh_registro_ponto") REFERENCES "tb_rh_registro_ponto" ("id_rh_registro_ponto") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_rh_documento_identidade" ADD CONSTRAINT "fk_tb_rh_documento_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_rh_extra" ADD CONSTRAINT "fk_tb_rh_extra_tb_rh_calculo_ponto1" FOREIGN KEY ("id_calculo_ponto") REFERENCES "tb_rh_calculo_ponto" ("id_calculo_ponto") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_extra" ADD CONSTRAINT "fk_tb_rh_extra_tb_usuarios1" FOREIGN KEY ("id_aprovacao_gerente") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_extra" ADD CONSTRAINT "fk_tb_rh_extra_tb_usuarios2" FOREIGN KEY ("id_aprovacao_diretor") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_extra" ADD CONSTRAINT "fk_tb_rh_extra_tb_usuarios3" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_extra" ADD CONSTRAINT "fk_tb_rh_extra_tb_usuarios4" FOREIGN KEY ("id_atualizacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_rh_falta" ADD CONSTRAINT "fk_tb_rh_falta_tb_rh_calculo_ponto1" FOREIGN KEY ("id_calculo_ponto") REFERENCES "tb_rh_calculo_ponto" ("id_calculo_ponto") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_falta" ADD CONSTRAINT "fk_tb_rh_falta_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_falta" ADD CONSTRAINT "fk_tb_rh_falta_tb_usuarios2" FOREIGN KEY ("id_atualizacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

-- @todo tabela fora do modulo!
ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_agrupador_financeiro1" FOREIGN KEY ("tss_id") REFERENCES "tb_agrupador_financeiro" ("id_agrupador_financeiro") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_agrupador_financeiro2" FOREIGN KEY ("tse_id") REFERENCES "tb_agrupador_financeiro" ("id_agrupador_financeiro") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_centro_custo1" FOREIGN KEY ("cec_id") REFERENCES "tb_centro_custo" ("cec_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
-- @todo ajustar empresa.
--ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_empresas1" FOREIGN KEY ("id_empresa") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_empresas2" FOREIGN KEY ("grupo_id") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

-- @todo tabelas do financeiro. ajustar
ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_moedas1" FOREIGN KEY ("moe_id") REFERENCES "tb_moedas" ("moe_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_operacoes1" FOREIGN KEY ("ope_id") REFERENCES "tb_operacoes" ("ope_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_plano_contas1" FOREIGN KEY ("plc_id") REFERENCES "tb_plano_contas" ("plc_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

--ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_de_pagamento_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_folha_de_pagamento" ADD CONSTRAINT "fk_tb_rh_folha_pagamento_tb_tppagamento1" FOREIGN KEY ("id_tp_pagamento") REFERENCES "tb_rh_tp_pagamento" ("id_tp_pagamento") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_rh_funcionario" ADD CONSTRAINT "fk_tb_rh_funcionario_tb_empresas1" FOREIGN KEY ("id_empresa") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_funcionario" ADD CONSTRAINT "fk_tb_rh_funcionario_tb_rh_nacionalidade1" FOREIGN KEY ("nacionalidade_mae") REFERENCES "tb_rh_nacionalidade" ("id_rh_nacionalidade") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_funcionario" ADD CONSTRAINT "fk_tb_rh_funcionario_tb_rh_nacionalidade2" FOREIGN KEY ("nacionalidade_pai") REFERENCES "tb_rh_nacionalidade" ("id_rh_nacionalidade") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_funcionario" ADD CONSTRAINT "fk_tb_rh_funcionario_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


--ALTER TABLE "tb_rh_horario" ADD CONSTRAINT "fk_tb_rh_horario_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_horario" ADD CONSTRAINT "fk_tb_rh_horario_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_rh_horario_funcionario" ADD CONSTRAINT "fk_tb_rh_horario_funcionario_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_horario_funcionario" ADD CONSTRAINT "fk_tb_rh_horario_funcionario_tb_rh_horario1" FOREIGN KEY ("id_horario") REFERENCES "tb_rh_horario" ("id_horario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_rh_horario_funcionario" ADD CONSTRAINT "fk_tb_rh_horario_funcionario_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_rh_modelo_sintetico" ADD CONSTRAINT "fk_tb_rh_modelo_sinitro_tb_rh_entrada_sinistro1" FOREIGN KEY ("id_rh_entrada_sintetico") REFERENCES "tb_rh_entrada_sintetico" ("id_rh_entrada_sintetico") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_rh_modelo_sintetico" ADD CONSTRAINT "fk_tb_rh_modelo_sinitro_tb_rh_natureza_sinistro1" FOREIGN KEY ("id_rh_natureza_sintetico") REFERENCES "tb_rh_natureza_sintetico" ("id_rh_natureza_sintetico") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

-- @todo tabelas do financeiro
ALTER TABLE "rel_rh_financeiro" ADD CONSTRAINT "fk_table1_tb_financeiro1" FOREIGN KEY ("fin_id") REFERENCES "tb_financeiro" ("fin_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "rel_rh_financeiro" ADD CONSTRAINT "fk_table1_tb_rh_modelo_sinitro1" FOREIGN KEY ("id_rh_modelo_sintetico") REFERENCES "tb_rh_modelo_sintetico" ("id_rh_modelo_sintetico") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_rh_servico_militar" ADD CONSTRAINT "fk_tb_rh_servico_militar_tb_rh_funcionario1" FOREIGN KEY ("id_rh_funcionario") REFERENCES "tb_rh_funcionario" ("id_rh_funcionario") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


