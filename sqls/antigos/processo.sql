


CREATE TABLE "mat_tb_gm_item" (
	"id_item" bigserial primary key,
	"id_grupo" int8,
	"id_subgrupo" int8,
	"id_classe" int8,
	"nome" varchar(200) NOT NULL COLLATE "default",
	"qtd_compra" numeric(10,2) NOT NULL,
	"id_tipo_unidade_compra" int4 NOT NULL,
	"qtd_consumo" numeric(10,2),
	"id_tipo_unidade_consumo" int4 NOT NULL,
	"descricao" varchar(255) COLLATE "default",
	"materia_prima" int2 NOT NULL,
	"revenda" int2 NOT NULL,
	"produto_finalizado" int2 NOT NULL,
	"ncm_sh" int4,
	"id_unidade_rastreabilidade" int4,
	"referencia" varchar(45) COLLATE "default",
	"valor_revenda" numeric(10,2),
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario"  uuid REFERENCES tb_pessoa (id),
	"dt_criacao" TIMESTAMP default now()
);

ALTER TABLE "mat_tb_gm_item" OWNER TO "hash";


CREATE TABLE "mat_tb_gm_opcao" (
	"id_opcao" bigserial primary key,
	"id_atributo" int8 NOT NULL,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario"  uuid REFERENCES tb_pessoa (id),
	"dt_criacao" TIMESTAMP default now()
);
ALTER TABLE "mat_tb_gm_opcao" OWNER TO "hash";



CREATE TABLE "mat_tb_tipo_unidade" (
	"id_tipo_unidade" serial primary key,
	"nome" varchar(100) NOT NULL COLLATE "default",
	"id_criacao_usuario"  uuid REFERENCES tb_pessoa (id),
	"dt_criacao" TIMESTAMP default now(),
	"ativo" int2 NOT NULL DEFAULT 1
);
ALTER TABLE "mat_tb_tipo_unidade" OWNER TO "hash";


CREATE TABLE "mat_tb_gm_marca" (
	"id_marca" serial primary key,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
	"dt_criacao" TIMESTAMP default now()
);
ALTER TABLE "mat_tb_gm_marca" OWNER TO "hash";


-- ------------------------------ ------------------------------ ------------------------------ ------------------------------ ------------------------------
-- ------------------------------ ------------------------------ ------------------------------ ------------------------------ ------------------------------
-- ------------------------------ ------------------------------ ------------------------------ ------------------------------ ------------------------------


CREATE TABLE "pro_tb_gp_abertura" (
	"id_abertura" serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario"  uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
);
ALTER TABLE "pro_tb_gp_abertura" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_acabamento" (
	"id_acabamento" serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
);
ALTER TABLE "pro_tb_gp_acabamento" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_categoria" (
	"id_categoria" bigserial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_categoria" OWNER TO "hash";

CREATE TABLE "pro_tb_status" (
	"sta_id" serial PRIMARY KEY,
	"sta_hexadecimal" varchar(45) COLLATE "default",
	"sta_descricao" varchar(100) COLLATE "default",
	"sta_cor_fonte" varchar(45) COLLATE "default",
	"sta_numero" varchar(45) COLLATE "default",
  "id_grupo" uuid REFERENCES tb_grupo (id),
	"sta_finalizado" int2 NOT NULL
)
;
ALTER TABLE "pro_tb_status" OWNER TO "hash";

CREATE TABLE "pro_tb_processo" (
	"pro_id" serial PRIMARY KEY,
	"pro_codigo" varchar(50) COLLATE "default",
	"pro_id_pedido" int4,
	"pro_cliente" varchar(255) COLLATE "default",
	"pro_contato" varchar(255) COLLATE "default",
	"pro_desc_produto" varchar(1000) COLLATE "default",
	"pro_quantidade" varchar(255) COLLATE "default",
	"pro_vlr_unt" numeric(15,2),
	"pro_vlr_pedido" numeric(15,2),
	"pro_prazo_entrega" varchar(255) COLLATE "default",
	"sta_id" int4 references pro_tb_status(sta_id),
	"pro_data_inc" timestamp(6) NULL,
	"empresas_id"  uuid REFERENCES tb_pessoa(id),
	"pro_data_entrega" timestamp(6) NULL,
	"pro_lote_numero" int4,
	"enderecos_id" int8,
	"empresas_grupo_id" int4,
	"pes_id" int4,
  "id_grupo" uuid REFERENCES tb_grupo (id),
	"id_processo_pai"  int4 REFERENCES pro_tb_processo (pro_id)
)
;
ALTER TABLE "pro_tb_processo" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_costura_caderno" (
	"id_costura_caderno" serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_costura_caderno" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_form_impressao" (
	"id_form_impressao" serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
  "dt_criacao" TIMESTAMP default now(),
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id)
)
;
ALTER TABLE "pro_tb_gp_form_impressao" OWNER TO "hash";


CREATE TABLE "pro_th_gp_processo" (
	"id_th_processo" serial PRIMARY KEY,
	"pro_id"  int4 REFERENCES pro_tb_processo (pro_id),
	"empresas_grupo_id" int4,
	"empresas_id" uuid REFERENCES tb_pessoa(id),
	"pes_id" int4,
	"pro_codigo" varchar(50) COLLATE "default",
	"pro_id_pedido" int4,
	"pro_cliente" varchar(255) COLLATE "default",
	"pro_contato" varchar(255) COLLATE "default",
	"pro_desc_produto" varchar(500) COLLATE "default",
	"pro_quantidade" varchar(255) COLLATE "default",
	"pro_vlr_unt" numeric(15,6),
	"pro_vlr_pedido" numeric(15,6),
	"pro_data_entrega" timestamp(6) NULL,
	"pro_prazo_entrega" varchar(255) COLLATE "default",
	"sta_id" int4 references pro_tb_status(sta_id),
	"pro_data_inc" timestamp(6) NULL,
	"pro_lote_numero" int4,
	"enderecos_id" int8,
  "dt_criacao" TIMESTAMP default now(),
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "id_grupo" uuid REFERENCES tb_grupo (id),
	"id_processo_pai"  int4 REFERENCES pro_tb_processo (pro_id)
)
;
ALTER TABLE "pro_th_gp_processo" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_lote_producao" (
	"id_lote_producao" serial PRIMARY KEY,
	"id_processo"  int4 REFERENCES pro_tb_processo (pro_id),
	"id_empresa"  uuid REFERENCES tb_pessoa(id),
	"cod_lote" varchar(45) NOT NULL COLLATE "default",
	"quantidade" int4,
	"dt_entrega" date,
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_lote_producao" OWNER TO "hash";

CREATE TABLE "pro_tb_gp_tp_material" (
	"id_tp_material" serial PRIMARY KEY,
	"nome" varchar(50) COLLATE "default"
)
;
ALTER TABLE "pro_tb_gp_tp_material" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_montagem" (
	"id_montagem" serial PRIMARY KEY,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_montagem" OWNER TO "hash";


CREATE TABLE "pro_tb_gp_posicao" (
	"id_posicao" serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default"
)
;
ALTER TABLE "pro_tb_gp_posicao" OWNER TO "hash";


CREATE TABLE "pro_tb_gp_prioridade" (
	"id_prioridade" bigserial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"cor" varchar(45) NOT NULL COLLATE "default"
)
;
ALTER TABLE "pro_tb_gp_prioridade" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_comentario" (
	"id_comentario"  bigserial PRIMARY KEY,
	"id_corporativa" uuid REFERENCES tb_pessoa(id),
	"id_processo" int4 REFERENCES pro_tb_processo (pro_id),
	"descricao" varchar(255) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_comentario" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_status_material" (
	"id_status_material" serial PRIMARY KEY,
	"nome" varchar(50) NOT NULL COLLATE "default"
)
;
ALTER TABLE "pro_tb_gp_status_material" OWNER TO "hash";


CREATE TABLE "pro_tb_gp_tam_chapa" (
	"id_tam_chapa" serial PRIMARY KEY,
	"nome" varchar(50) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_tam_chapa" OWNER TO "hash";


CREATE TABLE "pro_tb_gp_tam_papel" (
	"id_tam_papel" serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_tam_papel" OWNER TO "hash";




CREATE TABLE "pro_tb_gp_tp_produto" (
	"id_tp_produto" serial PRIMARY KEY,
	"nome" varchar(45) NOT NULL COLLATE "default",
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
);
ALTER TABLE "pro_tb_gp_tp_produto" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_material_processo" (
	"id_material_processo" bigserial PRIMARY KEY,
	"id_processo"  int4 REFERENCES pro_tb_processo (pro_id),
	"id_tp_material" int4 references pro_tb_gp_tp_material(id_tp_material),
	"id_status_material" int4 references pro_tb_gp_status_material(id_status_material),
	"id_tipo_unidade" int4 references mat_tb_tipo_unidade(id_tipo_unidade),
	"id_marca" int4 references mat_tb_gm_marca(id_marca),
	"id_item" int8 references mat_tb_gm_item(id_item),
	"nome" varchar(50) COLLATE "default",
	"observacao" varchar(300) COLLATE "default",
	"quantidade" numeric(10,2) NOT NULL,
	"qtd_baixado" numeric(10,2),
	"vl_unitario" numeric(10,2),
	"total" numeric(10,2),
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_material_processo" OWNER TO "hash";


CREATE TABLE "pro_ta_gp_material_processo_x_opcao" (
	"id_material_processo" int8 references pro_tb_gp_material_processo(id_material_processo),
	"id_opcao" int8 references mat_tb_gm_opcao(id_opcao),
	primary key (id_material_processo, id_opcao)
)
;
ALTER TABLE "pro_ta_gp_material_processo_x_opcao" OWNER TO "hash";




CREATE TABLE "pro_th_gp_material_processo" (
	"id_th_material_processo" bigserial PRIMARY KEY,
	"id_material_processo" int8 references pro_tb_gp_material_processo(id_material_processo),
	"id_processo"  int4 REFERENCES pro_tb_processo (pro_id),
	"id_tp_material" int4 references pro_tb_gp_tp_material(id_tp_material),
	"id_status_material" int4 references pro_tb_gp_status_material(id_status_material),
	"id_tipo_unidade" int4 references mat_tb_tipo_unidade(id_tipo_unidade),
	"id_marca" int4 references mat_tb_gm_marca(id_marca),
	"id_item" int8 references mat_tb_gm_item(id_item),
	"nome" varchar(50) COLLATE "default",
	"observacao" varchar(300) COLLATE "default",
	"quantidade" numeric(10,2) NOT NULL,
	"qtd_baixado" numeric(10,2),
	"vl_unitario" numeric(10,2),
	"total" numeric(10,2),
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_th_gp_material_processo" OWNER TO "hash";


CREATE TABLE "pro_tb_gp_desc_producao" (
	"id_desc_producao" bigserial PRIMARY KEY,
	"id_categoria" int8 REFERENCES pro_tb_gp_categoria (id_categoria),
	"id_tp_produto" int4 REFERENCES pro_tb_gp_tp_produto (id_tp_produto),
	"id_form_impressao" int4 REFERENCES pro_tb_gp_form_impressao (id_form_impressao),
	"id_tam_papel" int4 REFERENCES pro_tb_gp_tam_papel (id_tam_papel),
	"id_tam_chapa" int4 REFERENCES pro_tb_gp_tam_chapa (id_tam_chapa),
	"id_costura_caderno" int4 REFERENCES pro_tb_gp_costura_caderno (id_costura_caderno),
	"id_acabamento" int4 REFERENCES pro_tb_gp_acabamento (id_acabamento),
	"id_posicao" int4 REFERENCES pro_tb_gp_posicao (id_posicao),
	"id_abertura" int4 REFERENCES pro_tb_gp_abertura (id_abertura),
	"id_montagem" int4 REFERENCES pro_tb_gp_montagem (id_montagem),
	"qtd_pagina" int4,
	"tam_pagina" varchar(50) COLLATE "default",
	"formato_pagina" varchar(50) COLLATE "default",
	"cores" varchar(50) COLLATE "default",
	"pinca" int4,
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_desc_producao" OWNER TO "hash";


CREATE TABLE "pro_tb_gp_arquivo" (
	"id_arquivo"  bigserial PRIMARY KEY,
	"pro_id" int4 REFERENCES pro_tb_processo (pro_id),
	"nome" varchar(160) NOT NULL COLLATE "default",
	"nome_md5" varchar(160) COLLATE "default",
	"extensao" varchar(45) COLLATE "default",
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_arquivo" OWNER TO "hash";


CREATE TABLE "pro_tb_gp_processo_servico" (
	"id_processo_servico" serial PRIMARY KEY,
	"id_processo"  int4 REFERENCES pro_tb_processo (pro_id),
	"id_servico" int8 NOT NULL,
	"quantidade" int4,
	"vl_unitario" numeric(10,2),
	"total" numeric(10,2),
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now()
)
;
ALTER TABLE "pro_tb_gp_processo_servico" OWNER TO "hash";



CREATE TABLE "pro_tb_gp_planejamento" (
	"id_planejamento" bigserial PRIMARY KEY,
	"id_processo"  int4 REFERENCES pro_tb_processo (pro_id),
	"id_prioridade" int8 REFERENCES pro_tb_gp_prioridade(id_prioridade),
	"data" date,
	"ordem" int4,
	"ativo" int2 NOT NULL DEFAULT 1,
	"id_criacao_usuario" uuid REFERENCES tb_pessoa (id),
  "dt_criacao" TIMESTAMP default now(),
  "id_grupo" uuid REFERENCES tb_grupo (id)
)
;
ALTER TABLE "pro_tb_gp_planejamento" OWNER TO "hash";


CREATE TABLE "pro_tb_pcp_timer" (
	"id_timer" serial PRIMARY KEY,
	"empresas_id" uuid REFERENCES tb_pessoa(id),
	"latitude" varchar(45) COLLATE "default",
	"longitude" varchar(45) COLLATE "default",
	"inicio_work" timestamp(6) NOT NULL,
	"fim_work" timestamp(6) NULL,
	"pro_id"  int4 REFERENCES pro_tb_processo (pro_id),
  "dt_criacao" TIMESTAMP default now(),
	"ativo" int2 NOT NULL DEFAULT 1
)
;
ALTER TABLE "pro_tb_pcp_timer" OWNER TO "hash";
