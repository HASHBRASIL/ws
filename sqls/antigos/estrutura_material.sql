
--    tb_gm_arquivo
--    tb_gm_atributo
--    tb_gm_classe
--
--    tb_gm_entrega
--    tb_gm_estoque
--    tb_gm_estoque_gm_movimento -- @todo NAO ENCONTREI!  - tive que pegar do backup dump completo MYSQL (estrutura da tabela não estava correta)
--    ta_gm_estoque_x_opcao
--    tb_gm_grupo
--    tb_gm_imposto
--    mat_tb_gm_item -- @todo ja foi migrado - ajustar apenas codigos
--    tb_gm_item_entrega
--    ta_gm_item_x_opcao
--    mat_tb_gm_marca -- @todo ja foi migrado - ajustar apenas codigos
--    tb_gm_movimento
--    tb_gm_nfe
--    tb_gm_opcao
--    tb_gm_protocolo
--    tb_gm_status
--    tb_gm_subgrupo
--    tb_gm_tp_protocolo
--    tb_gm_tp_movimento
--    tb_gm_tp_transportador
--    tb_gm_transportador


-- ----------------------------
--  Table structure for tb_gm_subgrupo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_subgrupo";
CREATE TABLE "tb_gm_subgrupo" (
	"id_subgrupo" bigserial PRIMARY KEY,
	"id_gm_grupo" bigint NOT NULL,
	"nome" varchar(50) NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_subgrupo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gm_grupo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_grupo";
CREATE TABLE "tb_gm_grupo" (
	"id_gm_grupo" bigserial PRIMARY KEY,
	"nome" varchar(50) NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_grupo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gm_classe
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_classe";
CREATE TABLE "tb_gm_classe" (
	"id_classe" bigserial PRIMARY KEY,
	"id_subgrupo" int8 NOT NULL,
	"nome" varchar(50) NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_classe" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_gm_item_entrega
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_item_entrega";
CREATE TABLE "tb_gm_item_entrega" (
	"id_estoque_entrega"  bigserial PRIMARY KEY,
	"id_entrega" bigint NOT NULL,
	"id_item" bigint NOT NULL,
	"quantidade" numeric(10,2),
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_item_entrega" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_gm_item
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_item";
CREATE TABLE "tb_gm_item" (
	"id_item"  bigserial PRIMARY KEY,
	"id_gm_grupo" bigint,
	"id_subgrupo" bigint,
	"id_classe" bigint,
	"nome" varchar(200) NOT NULL,
	"qtd_compra" numeric(10,2) NOT NULL,
	"id_tipo_unidade_compra" int4 NOT NULL,
	"qtd_consumo" numeric(10,2),
	"id_tipo_unidade_consumo" int4 NOT NULL,
	"descricao" varchar(255),
	"materia_prima" int2 NOT NULL,
	"revenda" int2 NOT NULL,
	"produto_finalizado" int2 NOT NULL,
	"ncm_sh" int4,
	"id_unidade_rastreabilidade" int4,
	"referencia" varchar(45),
	"valor_revenda" numeric(10,2),
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_item" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_gm_status
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_status";
CREATE TABLE "tb_gm_status" (
	"id_status"  serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_status" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gm_transportador
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_transportador";
CREATE TABLE "tb_gm_transportador" (
	"id_transportador"  bigserial PRIMARY KEY,
	"id_transp_empresa" int NOT NULL,
	"antt" varchar(45),
	"frete" int2,
	"placa" varchar(45),
	"quantidade" numeric(10,2),
	"especie" varchar(45),
	"marca" varchar(45),
	"numeracao" varchar(45),
	"peso_bruto" numeric(10,2),
	"peso_liquido" numeric(10,2),

  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_transportador" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_gm_tp_transportador
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_tp_transportador";
CREATE TABLE "tb_gm_tp_transportador" (
	"id_tp_transportador" serial PRIMARY KEY,
	"nome" varchar(50) NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_tp_transportador" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gm_tp_protocolo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_tp_protocolo";
CREATE TABLE "tb_gm_tp_protocolo" (
	"id_tp_protocolo" serial PRIMARY KEY,
	"id_tp_movimento" int4,
	"nome" varchar(50) NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_tp_protocolo" OWNER TO "hash";



-- ----------------------------
--  Table structure for ta_gm_estoque_x_opcao
-- ----------------------------
DROP TABLE IF EXISTS "ta_gm_estoque_x_opcao";
CREATE TABLE "ta_gm_estoque_x_opcao" (
	"id_estoque" int8 NOT NULL,
	"id_opcao" int8 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_gm_estoque_x_opcao" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gm_nfe
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_nfe";
CREATE TABLE "tb_gm_nfe" (
	"id_nfe" bigserial PRIMARY KEY,
	"id_empresa_destinatario" int4,
	"id_endereco_destinatario" int8,
	"id_fornecedor" int4,
	"id_endereco_fornecedor" int8,
	"id_imposto" int8,
	"id_transportador" int8,
	"id_endereco_transportador" int8,
	"natureza_operacao" varchar(60) NOT NULL,
	"num_danfe" int8,
	"num_serie" int4,
	"dt_emissao" date,
	"dt_saida" date,
	"hr_saida" time(6),
	"dt_entrada" date,
	"hr_entrada" time(6),
	"id_funcionario" int4,
	"tl_produto" numeric(10,2),
	"tl_nota" numeric(10,2),
	"descricao" text,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
	"tp_nfe" int2,

  "id_grupo" uuid REFERENCES tb_grupo (id)
--	"id_workspace" int4 -- @todo
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_nfe" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gm_arquivo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_arquivo";
CREATE TABLE "tb_gm_arquivo" (
	"id_arquivo" serial PRIMARY KEY,
	"id_item" int8,
	"nome" varchar(60) NOT NULL,
	"nome_md5" varchar(60),
	"extensao" varchar(45),
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_arquivo" OWNER TO "hash";



-- ----------------------------
--  Table structure for tb_gm_movimento
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_movimento";
CREATE TABLE "tb_gm_movimento" (
	"id_movimento" bigserial PRIMARY KEY,
	"id_tp_movimento" int4 NOT NULL,
	"id_nfe" int8,
	"id_protocolo" int8,
	"id_processo" int4,
	"id_material_processo" int8,
	"quantidade" numeric(10,2),
	"transferencia" int2 NOT NULL,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_movimento" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gm_entrega
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_entrega";
CREATE TABLE "tb_gm_entrega" (
	"id_entrega" bigserial PRIMARY KEY,
	"id_empresa" int4 NOT NULL,
	"id_status" int4 NOT NULL,
	"email" varchar(50),
	"aos_cuidados" varchar(50),
	"destinatario" varchar(50),
	"contato" varchar(50),
	"cep" varchar(15) NOT NULL,
	"logradouro" varchar(50) NOT NULL,
	"numero" int4 NOT NULL,
	"complemento" varchar(30),
	"bairro" varchar(50),
	"id_cidade" int4 NOT NULL,
	"telefone" varchar(18),
	"celular" varchar(18),
	"fax" varchar(18),
	"observacao" varchar(255),
	"protocolo" varchar(500),
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_entrega" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_gm_imposto
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_imposto";
CREATE TABLE "tb_gm_imposto" (
	"id_imposto" bigserial PRIMARY KEY,
	"bs_calc_icms" numeric(10,2),
	"vl_icms" numeric(10,2),
	"bs_calc_icms_subst" numeric(10,2),
	"vl_icms_subst" numeric(10,2),
	"vl_frete" numeric(10,2),
	"vl_seguro" numeric(10,2),
	"desconto" numeric(10,2),
	"vl_despesa_extra" numeric(10,2),
	"vl_ipi" numeric(10,2),
	"tl_produto" numeric(10,2),
	"tl_nota" numeric(10,2),
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_imposto" OWNER TO "hash";

-- @todo já foi migrado no processo
---- ----------------------------
----  Table structure for tb_gm_marca
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gm_marca";
--CREATE TABLE "tb_gm_marca" (
--	"id_marca" int4 NOT NULL,
--	"nome" varchar(50) NOT NULL,
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gm_marca" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_gm_estoque
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_estoque";
CREATE TABLE "tb_gm_estoque" (
	"id_estoque" bigserial PRIMARY KEY,
	"id_item" int8 NOT NULL,
	"id_tipo_unidade" int4,
	"cod_lote" varchar(50),
	"codigo" varchar(45),
	"ncm_sh" int4,
	"cst" int4,
	"cfop" int4,
	"quantidade" numeric(10,2),
	"vl_unitario" numeric(10,2),
	"vl_total" numeric(10,2),
	"bc_icms" numeric(10,2),
	"vl_icms" numeric(10,2),
	"vl_ipi" numeric(10,2),
	"aliq_icms" numeric(10,2),
	"aliq_ipi" numeric(10,2),
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),

  "id_grupo" uuid REFERENCES tb_grupo (id)
--	"id_workspace" int4 -- @TODO
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_estoque" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_gm_atributo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_atributo";
CREATE TABLE "tb_gm_atributo" (
	"id_atributo" bigserial PRIMARY KEY,
	"nome" varchar(50) NOT NULL,
  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),

  "id_grupo" uuid REFERENCES tb_grupo (id)
--	"id_workspace" int4 -- @TODO
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_atributo" OWNER TO "hash";


-- ----------------------------
--  Table structure for tb_gm_opcao
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_opcao";
CREATE TABLE "tb_gm_opcao" (
	"id_opcao" bigserial PRIMARY KEY,
	"id_atributo" int8 NOT NULL,
	"nome" varchar(50) NOT NULL,

  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_opcao" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_gm_tp_movimento
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_tp_movimento";
CREATE TABLE "tb_gm_tp_movimento" (
	"id_tp_movimento" serial PRIMARY KEY,
	"nome" varchar(90) NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_tp_movimento" OWNER TO "hash";

-- ----------------------------
--  Table structure for ta_gm_item_x_opcao
-- ----------------------------
DROP TABLE IF EXISTS "ta_gm_item_x_opcao";
CREATE TABLE "ta_gm_item_x_opcao" (
	"id_item" int8 NOT NULL,
	"id_opcao" int8 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "ta_gm_item_x_opcao" OWNER TO "hash";

-- ----------------------------
--  Table structure for tb_gm_protocolo
-- ----------------------------
DROP TABLE IF EXISTS "tb_gm_protocolo";
CREATE TABLE "tb_gm_protocolo" (
	"id_protocolo" bigserial PRIMARY KEY,
	"id_tp_protocolo" int4 NOT NULL,
	"id_empresa_receptora" int4,
	"id_empresa_fornecedor" int4,
	"id_operacao_requisitante" int4,
	"id_operacao_requisitado" int4,
	"id_tp_transportador" int4,
	"id_transportador" int8,
	"id_funcionario_transportador" int4,
	"id_empresas_grupo" int4,
	"id_processo" int4,
	"id_centro_custo" int4,
	"dt_entrada" date,
	"hr_entrada" time(6),

  "ativo" int2 NOT NULL DEFAULT 1,
  "id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),

  "id_grupo" uuid REFERENCES tb_grupo (id)
--	"id_workspace" int4 -- @todo
)
WITH (OIDS=FALSE);
ALTER TABLE "tb_gm_protocolo" OWNER TO "hash";



-- tabela extraida do mysql pois no postgres nao foi. @todo ajustar estrutura para funcionar no postgreSQL
DROP TABLE IF EXISTS "tb_gm_estoque_gm_movimento";
CREATE TABLE "tb_gm_estoque_gm_movimento" (
  "id_estoque" serial PRIMARY KEY,
  "id_movimento" int8 not null,
  "quantidade" numeric(10,2)
);


--  PRIMARY KEY ("id_estoque","id_movimento"),
--  KEY "fk_tb_gm_estoque_has_tb_gm_movimento_tb_gm_movimento1_idx" ("id_movimento"),
--  KEY "fk_tb_gm_estoque_has_tb_gm_movimento_tb_gm_estoque1_idx" ("id_estoque"),
--  CONSTRAINT "fk_tb_gm_estoque_has_tb_gm_movimento_tb_gm_estoque1" FOREIGN KEY ("id_estoque") REFERENCES "tb_gm_estoque" ("id_estoque") ON DELETE NO ACTION ON UPDATE NO ACTION,
--  CONSTRAINT "fk_tb_gm_estoque_has_tb_gm_movimento_tb_gm_movimento1" FOREIGN KEY ("id_movimento") REFERENCES "tb_gm_movimento" ("id_movimento") ON DELETE NO ACTION ON UPDATE NO ACTION




--ALTER TABLE "tb_gm_subgrupo" ADD CONSTRAINT "tb_gm_subgrupo_pkey" PRIMARY KEY ("id_subgrupo") NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_gm_subgrupo" ADD CONSTRAINT "fk_tb_gm_subgrupo_tb_gm_grupo1" FOREIGN KEY ("id_gm_grupo") REFERENCES "tb_gm_grupo" ("id_gm_grupo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_subgrupo" ADD CONSTRAINT "fk_tb_gm_subgrupo_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_classe" ADD CONSTRAINT "fk_tb_gm_classe_tb_gm_subgrupo1" FOREIGN KEY ("id_subgrupo") REFERENCES "tb_gm_subgrupo" ("id_subgrupo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_classe" ADD CONSTRAINT "fk_tb_gm_classe_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_item_entrega" ADD CONSTRAINT "fk_tb_gm_estoque_entrega_tb_gm_entrega1" FOREIGN KEY ("id_entrega") REFERENCES "tb_gm_entrega" ("id_entrega") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_item_entrega" ADD CONSTRAINT "fk_tb_gm_estoque_entrega_tb_gm_item1" FOREIGN KEY ("id_item") REFERENCES "tb_gm_item" ("id_item") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_item_entrega" ADD CONSTRAINT "fk_tb_gm_item_entrega_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_item" ADD CONSTRAINT "fk_tb_gm_item_tb_gm_classe1" FOREIGN KEY ("id_classe") REFERENCES "tb_gm_classe" ("id_classe") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_item" ADD CONSTRAINT "fk_tb_gm_item_tb_gm_subgrupo1" FOREIGN KEY ("id_subgrupo") REFERENCES "tb_gm_subgrupo" ("id_subgrupo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_item" ADD CONSTRAINT "fk_tb_gm_item_tb_unidade3" FOREIGN KEY ("id_unidade_rastreabilidade") REFERENCES "tb_tipo_unidade" ("id_tipo_unidade") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_item" ADD CONSTRAINT "fk_tb_mat_item_tb_mat_grupo1" FOREIGN KEY ("id_grupo") REFERENCES "tb_gm_grupo" ("id_grupo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_item" ADD CONSTRAINT "fk_tb_mat_item_tb_tipo_unidade1" FOREIGN KEY ("id_tipo_unidade_consumo") REFERENCES "tb_tipo_unidade" ("id_tipo_unidade") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_item" ADD CONSTRAINT "fk_tb_mat_material_tb_tipo_unidade1" FOREIGN KEY ("id_tipo_unidade_compra") REFERENCES "tb_tipo_unidade" ("id_tipo_unidade") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_item" ADD CONSTRAINT "fk_tb_mat_material_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_item_entrega" ADD CONSTRAINT "fk_tb_gm_estoque_entrega_tb_gm_entrega1" FOREIGN KEY ("id_entrega") REFERENCES "tb_gm_entrega" ("id_entrega") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_item_entrega" ADD CONSTRAINT "fk_tb_gm_estoque_entrega_tb_gm_item1" FOREIGN KEY ("id_item") REFERENCES "tb_gm_item" ("id_item") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_item_entrega" ADD CONSTRAINT "fk_tb_gm_item_entrega_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;



ALTER TABLE "tb_gm_transportador" ADD CONSTRAINT "fk_tb_gm_transportador_tb_empresas1" FOREIGN KEY ("id_transp_empresa") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_transportador" ADD CONSTRAINT "fk_tb_gm_transportador_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;



--ALTER TABLE "tb_gm_tp_protocolo" ADD CONSTRAINT "fk_tb_gm_tp_entrada_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_tp_protocolo" ADD CONSTRAINT "fk_tb_gm_tp_protocolo_tb_gm_tp_movimento1" FOREIGN KEY ("id_tp_movimento") REFERENCES "tb_gm_tp_movimento" ("id_tp_movimento") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


-- @todo esta errada - deve fazer vinculo com estoque.
ALTER TABLE "ta_gm_estoque_x_opcao" ADD CONSTRAINT "fk_tb_gm_estoque_has_tb_gm_opcao_tb_gm_opcao1" FOREIGN KEY ("id_opcao") REFERENCES "tb_gm_opcao" ("id_opcao") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_empresas1" FOREIGN KEY ("id_fornecedor") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_empresas2" FOREIGN KEY ("id_empresa_destinatario") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_empresas3" FOREIGN KEY ("id_funcionario") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

-- @todo endereços
--ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_enderecos1_idx" FOREIGN KEY ("id_endereco_destinatario") REFERENCES "tb_enderecos" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_enderecos2" FOREIGN KEY ("id_endereco_fornecedor") REFERENCES "tb_enderecos" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_enderecos3" FOREIGN KEY ("id_endereco_transportador") REFERENCES "tb_enderecos" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_gm_imposto1" FOREIGN KEY ("id_imposto") REFERENCES "tb_gm_imposto" ("id_imposto") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_gm_transportador1" FOREIGN KEY ("id_transportador") REFERENCES "tb_gm_transportador" ("id_transportador") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_entrada_tb_usuarios2" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
-- @todo
--ALTER TABLE "tb_gm_nfe" ADD CONSTRAINT "fk_tb_gm_nfe_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_arquivo" ADD CONSTRAINT "fk_tb_gm_arquivo_tb_gm_item1" FOREIGN KEY ("id_item") REFERENCES "tb_gm_item" ("id_item") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_arquivo" ADD CONSTRAINT "fk_tb_gm_arquivo_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;



ALTER TABLE "tb_gm_movimento" ADD CONSTRAINT "fk_tb_gm_movimento_tb_gm_nfe1" FOREIGN KEY ("id_nfe") REFERENCES "tb_gm_nfe" ("id_nfe") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_movimento" ADD CONSTRAINT "fk_tb_gm_movimento_tb_gm_protocolo1" FOREIGN KEY ("id_protocolo") REFERENCES "tb_gm_protocolo" ("id_protocolo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_movimento" ADD CONSTRAINT "fk_tb_gm_movimento_tb_gm_tp_movimento1" FOREIGN KEY ("id_tp_movimento") REFERENCES "tb_gm_tp_movimento" ("id_tp_movimento") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_movimento" ADD CONSTRAINT "fk_tb_gm_movimento_tb_gp_material1" FOREIGN KEY ("id_material_processo") REFERENCES "tb_gp_material_processo" ("id_material_processo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_movimento" ADD CONSTRAINT "fk_tb_gm_movimento_tb_processo1" FOREIGN KEY ("id_processo") REFERENCES "tb_processo" ("pro_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_movimento" ADD CONSTRAINT "fk_tb_gm_movimento_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


-- @todo ver como vai ser para fazer isso.
--ALTER TABLE "tb_gm_entrega" ADD CONSTRAINT "fk_tb_gm_entrega_tb_cidades1" FOREIGN KEY ("id_cidade") REFERENCES "tb_cidades" ("cid_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_entrega" ADD CONSTRAINT "fk_tb_gm_entrega_tb_empresas1" FOREIGN KEY ("id_empresa") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_entrega" ADD CONSTRAINT "fk_tb_gm_entrega_tb_gm_status1" FOREIGN KEY ("id_status") REFERENCES "tb_gm_status" ("id_status") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_entrega" ADD CONSTRAINT "fk_tb_gm_entrega_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_imposto" ADD CONSTRAINT "fk_tb_gm_imposto_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_gm_estoque" ADD CONSTRAINT "fk_tb_gm_estoque_tb_gm_item1" FOREIGN KEY ("id_item") REFERENCES "tb_gm_item" ("id_item") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_estoque" ADD CONSTRAINT "fk_tb_gm_estoque_tb_tipo_unidade1" FOREIGN KEY ("id_tipo_unidade") REFERENCES "tb_tipo_unidade" ("id_tipo_unidade") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_estoque" ADD CONSTRAINT "fk_tb_gm_estoque_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
-- @todo
--ALTER TABLE "tb_gm_estoque" ADD CONSTRAINT "fk_tb_gm_estoque_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


--ALTER TABLE "tb_gm_atributo" ADD CONSTRAINT "fk_tb_gm_atributo_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
-- @todo
--ALTER TABLE "tb_gm_atributo" ADD CONSTRAINT "fk_tb_gm_atributo_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_opcao" ADD CONSTRAINT "fk_tb_gm_opcao_tb_gm_atributo1" FOREIGN KEY ("id_atributo") REFERENCES "tb_gm_atributo" ("id_atributo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_opcao" ADD CONSTRAINT "fk_tb_gm_opcao_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_tp_movimento" ADD CONSTRAINT "tb_gm_tp_movimento_pkey" PRIMARY KEY ("id_tp_movimento") NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "ta_gm_item_x_opcao" ADD CONSTRAINT "fk_tb_gm_item_has_tb_gm_opcao_tb_gm_item1" FOREIGN KEY ("id_item") REFERENCES "tb_gm_item" ("id_item") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "ta_gm_item_x_opcao" ADD CONSTRAINT "fk_tb_gm_item_has_tb_gm_opcao_tb_gm_opcao1" FOREIGN KEY ("id_opcao") REFERENCES "tb_gm_opcao" ("id_opcao") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;


ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_centro_custo1" FOREIGN KEY ("id_centro_custo") REFERENCES "tb_centro_custo" ("cec_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_empresas1" FOREIGN KEY ("id_empresa_receptora") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_empresas2" FOREIGN KEY ("id_empresa_fornecedor") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_empresas3" FOREIGN KEY ("id_funcionario_transportador") REFERENCES "tb_empresas" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_empresas_grupo1" FOREIGN KEY ("id_empresas_grupo") REFERENCES "tb_empresas_grupo" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_gm_tp_protocolo1" FOREIGN KEY ("id_tp_protocolo") REFERENCES "tb_gm_tp_protocolo" ("id_tp_protocolo") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_gm_tp_transportador1" FOREIGN KEY ("id_tp_transportador") REFERENCES "tb_gm_tp_transportador" ("id_tp_transportador") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_gm_transportador1" FOREIGN KEY ("id_transportador") REFERENCES "tb_gm_transportador" ("id_transportador") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_operacoes1" FOREIGN KEY ("id_operacao_requisitante") REFERENCES "fin_tb_operacoes" ("ope_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_operacoes2" FOREIGN KEY ("id_operacao_requisitado") REFERENCES "fin_tb_operacoes" ("ope_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_processo1" FOREIGN KEY ("id_processo") REFERENCES "pro_tb_processo" ("pro_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
--ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_usuarios1" FOREIGN KEY ("id_criacao_usuario") REFERENCES "tb_usuarios" ("usu_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
-- @todo
--ALTER TABLE "tb_gm_protocolo" ADD CONSTRAINT "fk_tb_gm_protocolo_tb_workspace1" FOREIGN KEY ("id_workspace") REFERENCES "tb_workspace" ("id_workspace") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "tb_gm_estoque_gm_movimento" ADD CONSTRAINT "fk_tb_gm_estoque_has_tb_gm_movimento_tb_gm_estoque1" FOREIGN KEY ("id_estoque") REFERENCES "tb_gm_estoque" ("id_estoque") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "tb_gm_estoque_gm_movimento" ADD CONSTRAINT "fk_tb_gm_estoque_has_tb_gm_movimento_tb_gm_movimento1" FOREIGN KEY ("id_movimento") REFERENCES "tb_gm_movimento" ("id_movimento") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;

