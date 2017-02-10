tb_co_campanha
tb_co_campanha_corporativa
tb_co_campanha_item
tb_co_compra
tb_co_compra_item
ta_co_compra_item_x_gm_opcao
tb_co_tp_comissao

tb_free_tarefa

tb_gm_arquivo
tb_gm_atributo
tb_gm_classe
tb_mat_compra
tb_gm_entrega
tb_gm_estoque
tb_gm_estoque_gm_movimento
ta_gm_estoque_x_opcao
tb_gm_grupo
tb_gm_imposto
mat_tb_gm_item
tb_gm_item_entrega
ta_gm_item_x_opcao
mat_tb_gm_marca
tb_gm_movimento
tb_gm_nfe
tb_gm_opcao
tb_gm_protocolo
tb_gm_status
tb_gm_subgrupo
tb_gm_tp_protocolo
tb_gm_tp_movimento
tb_gm_tp_transportador
tb_gm_transportador


tb_mob_device


tb_pcp_config_empresa
tb_pcp_timer

-- verificar este.
tb_au_profile
ta_profile_x_user
ta_resource_x_profile

-- relatorio
tb_centro_custo
tb_gm_estoque
tb_processo
tb_gm_movimento
tb_status_financeiro
vw_status


tb_au_resource

-- rh
tb_rh_admissao
tb_rh_caged
tb_rh_calculo_ponto
tb_rh_categoria
tb_rh_cbo
tb_rh_certidao_civil
tb_rh_ci
tb_rh_config_extra
tb_rh_config_horario
tb_rh_configuracao
ta_rh_configuracao_x_usuario
tb_rh_contrato
tb_rh_dados_funcionais
tb_rh_dados_ponto
tb_rh_deficiencia
tb_rh_documento_identidade
tb_rh_entrada_sintetico
tb_rh_escala
tb_rh_extra
tb_rh_falta
tb_rh_feriados
tb_rh_fgts
tb_rh_folha_de_pagamento
tb_rh_funcionario
tb_rh_horario
tb_rh_horario_funcionario
tb_rh_instrucao
tb_rh_justificacao_ponto
tb_rh_local
tb_rh_modelo_sintetico
tb_rh_nacionalidade
tb_rh_natureza_sintetico
tb_rh_ocorrencia
tb_rh_outro
tb_rh_passagem
tb_rh_registro_ponto
tb_rh_raca
rel_rh_financeiro
tb_rh_servico_militar
tb_rh_tipo_admissao
tb_rh_tp_pagamento
tb_rh_vinculo


-- service
tb_gs_classe
tb_gs_componente
tb_gs_grupo
tb_gs_orcamento
tb_gs_protocolo
tb_gs_servico
ta_gs_servico_centro_custo
ta_gs_servico_empresas
tb_gs_subgrupo
tb_gs_tarefa
tb_gs_tp_componente
tb_gs_tp_entrada
tb_gs_tp_orcamento
tb_gs_tp_servico
tb_gs_tipo_unidade
tb_gs_valor_servico


-- sis
tb_cargos
tb_cidades
tb_contatos
tb_contato_departamento
tb_contato_referenciado
tb_controle
tb_empresas_grupo
tb_enderecos
tb_ufs
tb_sis_feed
tb_empresas
tb_grupo_geografico
ta_grupo_geografico_x_empresas
tb_indicacao
tb_segmento_atividade
tb_sis_proprietario
tb_sis_status_feed
tb_tp_controle
tb_tipo_endereco
tb_tp_endereco_ref
tb_sis_tipo_feed
tb_tipo_pessoa
tb_tipo_segmento
mat_tb_tipo_unidade







---- ----------------------------
----  Table structure for tb_grupo_contas
---- ----------------------------
--DROP TABLE IF EXISTS "tb_grupo_contas";
--CREATE TABLE "tb_grupo_contas" (
--	"grc_id" int2 NOT NULL,
--	"grc_descricao" varchar(45) NOT NULL COLLATE "default",
--	"grc_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_grupo_contas" OWNER TO "hash";

---- ----------------------------
----  Table structure for rel_operacoes_pessoal
---- ----------------------------
--DROP TABLE IF EXISTS "rel_operacoes_pessoal";
--CREATE TABLE "rel_operacoes_pessoal" (
--	"ope_id" int4 NOT NULL,
--	"pes_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_operacoes_pessoal" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gs_valor_servico
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gs_valor_servico";
--CREATE TABLE "tb_gs_valor_servico" (
--	"id_valor_servico" int4 NOT NULL,
--	"id_servico" int8 NOT NULL,
--	"id_empresa" int4,
--	"vl_unitario" numeric(10,2) NOT NULL,
--	"fixo" int2 NOT NULL,
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gs_valor_servico" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_tipo_servico
---- ----------------------------
--DROP TABLE IF EXISTS "tb_tipo_servico";
--CREATE TABLE "tb_tipo_servico" (
--	"tis_id" int4 NOT NULL,
--	"tis_descricao" varchar(100) COLLATE "default",
--	"tis_ativo" int2,
--	"tis_interno" int2,
--	"tis_externo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_tipo_servico" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_gp_comentario
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_comentario";
--CREATE TABLE "tb_gp_comentario" (
--	"id_comentario" int8 NOT NULL,
--	"id_corporativa" int4,
--	"id_processo" int4,
--	"descricao" varchar(255) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" varchar(45) COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_comentario" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_au_resource
---- ----------------------------
--DROP TABLE IF EXISTS "tb_au_resource";
--CREATE TABLE "tb_au_resource" (
--	"id_au_resource" int4 NOT NULL,
--	"name_resource" varchar(75) NOT NULL COLLATE "default",
--	"module_resource" varchar(75) NOT NULL COLLATE "default",
--	"controller_resource" varchar(75) NOT NULL COLLATE "default",
--	"action_resource" varchar(75) NOT NULL COLLATE "default",
--	"description_resource" text COLLATE "default",
--	"dt_criacao" timestamp(6) NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"ativo" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_au_resource" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_prioridade
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_prioridade";
--CREATE TABLE "tb_gp_prioridade" (
--	"id_prioridade" int8 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default",
--	"cor" varchar(45) NOT NULL COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_prioridade" OWNER TO "hash";

---- ----------------------------
----  Table structure for pcp_tb_timer
---- ----------------------------
--DROP TABLE IF EXISTS "pcp_tb_timer";
--CREATE TABLE "pcp_tb_timer" (
--	"idtb_timer" int4 NOT NULL,
--	"inicio" timestamp(6) NOT NULL,
--	"fim" timestamp(6) NOT NULL,
--	"pro_id" int4 NOT NULL,
--	"pes_id" int4 NOT NULL,
--	"pes_cpf_criador" varchar(25) COLLATE "default",
--	"dt_criacao" timestamp(6) NULL,
--	"vl_hora_pes" numeric(10,2),
--	"vl_hora_data_ref" date
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "pcp_tb_timer" OWNER TO "hash";

---- ----------------------------
----  Table structure for pcp_tb_timer_maquina
---- ----------------------------
--DROP TABLE IF EXISTS "pcp_tb_timer_maquina";
--CREATE TABLE "pcp_tb_timer_maquina" (
--	"id" int4 NOT NULL,
--	"dt_trabalho" date,
--	"id_processo" int4,
--	"hr_inicio" time(6),
--	"hr_fim" time(6),
--	"tempo_producao" time(6),
--	"desc_atividade" varchar(255) COLLATE "default",
--	"obs" varchar(255) COLLATE "default",
--	"cons" varchar(255) COLLATE "default",
--	"cip" int2,
--	"tipo" int4,
--	"tiragem" numeric(10,2),
--	"pes_id" int4,
--	"pes_cpf_criador" varchar(30) COLLATE "default",
--	"dt_criacao" timestamp(6) NULL,
--	"id_centro_custo" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "pcp_tb_timer_maquina" OWNER TO "hash";





---- ----------------------------
----  Table structure for tb_portal_cadastrado
---- ----------------------------
--DROP TABLE IF EXISTS "tb_portal_cadastrado";
--CREATE TABLE "tb_portal_cadastrado" (
--	"poc_id" int4 NOT NULL,
--	"poc_descricacao" varchar(100) NOT NULL COLLATE "default",
--	"poc_ativo" int2,
--	"poc_endereco_chamada" varchar(255) COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_portal_cadastrado" OWNER TO "hash";
--
---- ----------------------------
----  Table structure for ta_caracteristica_x_empresa
---- ----------------------------
--DROP TABLE IF EXISTS "ta_caracteristica_x_empresa";
--CREATE TABLE "ta_caracteristica_x_empresa" (
--	"id_caracteristica" int8 NOT NULL,
--	"id_empresa" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "ta_caracteristica_x_empresa" OWNER TO "hash";

--
---- ----------------------------
----  Table structure for tb_tipo_cliente
---- ----------------------------
--DROP TABLE IF EXISTS "tb_tipo_cliente";
--CREATE TABLE "tb_tipo_cliente" (
--	"tic_id" int4 NOT NULL,
--	"tic_descricao" varchar(45) NOT NULL COLLATE "default",
--	"tic_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_tipo_cliente" OWNER TO "hash";

---- ----------------------------
----  Table structure for ta_workspace_x_usuario
---- ----------------------------
--DROP TABLE IF EXISTS "ta_workspace_x_usuario";
--CREATE TABLE "ta_workspace_x_usuario" (
--	"id_workspace" int4 NOT NULL,
--	"usu_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "ta_workspace_x_usuario" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_usuarios
---- ----------------------------
--DROP TABLE IF EXISTS "tb_usuarios";
--CREATE TABLE "tb_usuarios" (
--	"usu_id" int4 NOT NULL,
--	"usu_senha" char(32) COLLATE "default",
--	"usu_primeiro_acesso" int2,
--	"pes_id" int4,
--	"id_empresa" int4,
--	"root" int2 NOT NULL,
--	"dt_criacao" timestamp(6) NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"ativo" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_usuarios" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_processo
---- ----------------------------
--DROP TABLE IF EXISTS "tb_processo";
--CREATE TABLE "tb_processo" (
--	"pro_id" int4 NOT NULL,
--	"pro_codigo" varchar(50) COLLATE "default",
--	"pro_id_pedido" int4,
--	"pro_cliente" varchar(255) COLLATE "default",
--	"pro_contato" varchar(255) COLLATE "default",
--	"pro_desc_produto" varchar(1000) COLLATE "default",
--	"pro_quantidade" varchar(255) COLLATE "default",
--	"pro_vlr_unt" numeric(15,2),
--	"pro_vlr_pedido" numeric(15,2),
--	"pro_prazo_entrega" varchar(255) COLLATE "default",
--	"sta_id" int4 NOT NULL,
--	"pro_data_inc" timestamp(6) NULL,
--	"empresas_id" int4,
--	"pro_data_entrega" timestamp(6) NULL,
--	"pro_lote_numero" int4,
--	"enderecos_id" int8,
--	"empresas_grupo_id" int4,
--	"pes_id" int4,
--	"id_workspace" int4,
--	"id_processo_pai" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_processo" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_status
---- ----------------------------
--DROP TABLE IF EXISTS "tb_status";
--CREATE TABLE "tb_status" (
--	"sta_id" int4 NOT NULL,
--	"sta_hexadecimal" varchar(45) COLLATE "default",
--	"sta_descricao" varchar(100) COLLATE "default",
--	"sta_cor_fonte" varchar(45) COLLATE "default",
--	"sta_numero" varchar(45) COLLATE "default",
--	"id_workspace" int4,
--	"sta_finalizado" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_status" OWNER TO "hash";




---- ----------------------------
----  Table structure for tb_aplicacoes
---- ----------------------------
--DROP TABLE IF EXISTS "tb_aplicacoes";
--CREATE TABLE "tb_aplicacoes" (
--	"apl_id" int4 NOT NULL,
--	"apl_nome" varchar(200) NOT NULL COLLATE "default",
--	"apl_descricao" varchar(255) NOT NULL COLLATE "default",
--	"apl_nome_menu" varchar(20) COLLATE "default",
--	"apl_parent" int4,
--	"apl_lista_menu" int2,
--	"mod_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_aplicacoes" OWNER TO "hash";


---- ----------------------------
----  Table structure for rel_grupo_trabalho_empresas
---- ----------------------------
--DROP TABLE IF EXISTS "rel_grupo_trabalho_empresas";
--CREATE TABLE "rel_grupo_trabalho_empresas" (
--	"grt_id" int4 NOT NULL,
--	"empresas_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_grupo_trabalho_empresas" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_tam_chapa
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_tam_chapa";
--CREATE TABLE "tb_gp_tam_chapa" (
--	"id_tam_chapa" int4 NOT NULL,
--	"nome" varchar(50) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_tam_chapa" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_categoria
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_categoria";
--CREATE TABLE "tb_gp_categoria" (
--	"id_categoria" int8 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_categoria" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gc_caracteristica
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gc_caracteristica";
--CREATE TABLE "tb_gc_caracteristica" (
--	"id_caracteristica" int8 NOT NULL,
--	"nome" varchar(50) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gc_caracteristica" OWNER TO "hash";

---- ----------------------------
----  Table structure for rel_aplicacoes_aplicacoes
---- ----------------------------
--DROP TABLE IF EXISTS "rel_aplicacoes_aplicacoes";
--CREATE TABLE "rel_aplicacoes_aplicacoes" (
--	"apl_id" int4 NOT NULL,
--	"apl_id_pai" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_aplicacoes_aplicacoes" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_gp_status_material
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_status_material";
--CREATE TABLE "tb_gp_status_material" (
--	"id_status_material" int4 NOT NULL,
--	"nome" varchar(50) NOT NULL COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_status_material" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_au_menu
---- ----------------------------
--DROP TABLE IF EXISTS "tb_au_menu";
--CREATE TABLE "tb_au_menu" (
--	"id_au_menu" int4 NOT NULL,
--	"id_au_resource" int4,
--	"id_au_parent_menu" int4,
--	"name_menu" varchar(75) NOT NULL COLLATE "default",
--	"title_menu" varchar(75) NOT NULL COLLATE "default",
--	"description_menu" text COLLATE "default",
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NOT NULL,
--	"icon_menu" varchar(50) COLLATE "default",
--	"ativo" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_au_menu" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_gp_tp_produto
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_tp_produto";
--CREATE TABLE "tb_gp_tp_produto" (
--	"id_tp_produto" int4 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_tp_produto" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_contatos
---- ----------------------------
--DROP TABLE IF EXISTS "tb_contatos";
--CREATE TABLE "tb_contatos" (
--	"id" int8 NOT NULL,
--	"id_empresas" int4,
--	"nome" varchar(100) COLLATE "default",
--	"email1" varchar(100) COLLATE "default",
--	"email2" varchar(100) COLLATE "default",
--	"telefone1" char(12) COLLATE "default",
--	"telefone2" char(12) COLLATE "default",
--	"telefone3" char(12) COLLATE "default",
--	"radio" varchar(45) COLLATE "default",
--	"aniversario" date,
--	"ativo" int2,
--	"cdp_id" int4,
--	"cre_id" int4,
--	"smk_id" int4,
--	"car_id" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_contatos" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_plano_contas
---- ----------------------------
--DROP TABLE IF EXISTS "tb_plano_contas";
--CREATE TABLE "tb_plano_contas" (
--	"plc_id" int4 NOT NULL,
--	"plc_cod_contabil" varchar(45) COLLATE "default",
--	"plc_cod_reduzido" varchar(20) COLLATE "default",
--	"plc_descricao" varchar(255) NOT NULL COLLATE "default",
--	"plc_conta_redutora" int2,
--	"grc_id" int2,
--	"plc_id_pai" int4,
--	"plc_oculta" int2,
--	"plc_contabil" int2 NOT NULL,
--	"plc_resultado" int2 NOT NULL,
--	"plc_transferencia" int2 NOT NULL,
--	"id_workspace" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_plano_contas" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_recorrencia_fin
---- ----------------------------
--DROP TABLE IF EXISTS "tb_recorrencia_fin";
--CREATE TABLE "tb_recorrencia_fin" (
--	"rcf_id" int2 NOT NULL,
--	"rcf_descricao" varchar(45) COLLATE "default",
--	"rcf_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_recorrencia_fin" OWNER TO "hash";



---- ----------------------------
----  Table structure for rel_empresas_modulos
---- ----------------------------
--DROP TABLE IF EXISTS "rel_empresas_modulos";
--CREATE TABLE "rel_empresas_modulos" (
--	"empresas_id" int4 NOT NULL,
--	"mod_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_empresas_modulos" OWNER TO "hash";


---- ----------------------------
----  Table structure for rl_usuarios_grupos
---- ----------------------------
--DROP TABLE IF EXISTS "rl_usuarios_grupos";
--CREATE TABLE "rl_usuarios_grupos" (
--	"gru_id" int4 NOT NULL,
--	"usu_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rl_usuarios_grupos" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_processo_servico
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_processo_servico";
--CREATE TABLE "tb_gp_processo_servico" (
--	"id_processo_servico" int4 NOT NULL,
--	"id_processo" int4 NOT NULL,
--	"id_servico" int8 NOT NULL,
--	"quantidade" int4,
--	"vl_unitario" numeric(10,2),
--	"total" numeric(10,2),
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_processo_servico" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_tipo_contabanco
---- ----------------------------
--DROP TABLE IF EXISTS "tb_tipo_contabanco";
--CREATE TABLE "tb_tipo_contabanco" (
--	"tcb_id" int2 NOT NULL,
--	"tcb_descricao" varchar(45) NOT NULL COLLATE "default",
--	"tcb_sigla" varchar(4) COLLATE "default",
--	"tcb_ativo" varchar(45) COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_tipo_contabanco" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_pessoal
---- ----------------------------
--DROP TABLE IF EXISTS "tb_pessoal";
--CREATE TABLE "tb_pessoal" (
--	"pes_id" int4 NOT NULL,
--	"pes_nome" varchar(100) NOT NULL COLLATE "default",
--	"pes_cpf_cnpj" varchar(14) NOT NULL COLLATE "default",
--	"pes_data_nasc" date,
--	"pes_email1" varchar(200) NOT NULL COLLATE "default",
--	"pes_email2" varchar(200) COLLATE "default",
--	"pes_rg_insc_est" varchar(45) COLLATE "default",
--	"id_novo" int4,
--	"usu_senha2" varchar(90) COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_pessoal" OWNER TO "hash";




















---- ----------------------------
----  Table structure for tb_historico_empresas
---- ----------------------------
--DROP TABLE IF EXISTS "tb_historico_empresas";
--CREATE TABLE "tb_historico_empresas" (
--	"hem_id" int4 NOT NULL,
--	"hem_descricao" text COLLATE "default",
--	"hem_data_hora" timestamp(6) NULL,
--	"empresas_id" int4 NOT NULL,
--	"usu_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_historico_empresas" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_workspace
---- ----------------------------
--DROP TABLE IF EXISTS "tb_workspace";
--CREATE TABLE "tb_workspace" (
--	"id_workspace" int4 NOT NULL,
--	"nome" varchar(200) NOT NULL COLLATE "default",
--	"dt_criacao" timestamp(6) NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"id_empresa" int4,
--	"acronym" varchar(3) NOT NULL COLLATE "default",
--	"free_access" int2 NOT NULL,
--	"ativo" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_workspace" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_modulos
---- ----------------------------
--DROP TABLE IF EXISTS "tb_modulos";
--CREATE TABLE "tb_modulos" (
--	"mod_id" int4 NOT NULL,
--	"mod_nome" varchar(100) NOT NULL COLLATE "default",
--	"mod_descricao" varchar(150) COLLATE "default",
--	"mod_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_modulos" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_permissoes
---- ----------------------------
--DROP TABLE IF EXISTS "tb_permissoes";
--CREATE TABLE "tb_permissoes" (
--	"per_id" int2 NOT NULL,
--	"per_descricao" varchar(20) NOT NULL COLLATE "default",
--	"per_sigla" char(2) NOT NULL COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_permissoes" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_gp_planejamento
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_planejamento";
--CREATE TABLE "tb_gp_planejamento" (
--	"id_planejamento" int8 NOT NULL,
--	"id_processo" int4 NOT NULL,
--	"id_prioridade" int8,
--	"data" date,
--	"ordem" int4,
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL,
--	"id_workspace" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_planejamento" OWNER TO "hash";

---- ----------------------------
----  Table structure for pcp_tb_tp_processo_maquina
---- ----------------------------
--DROP TABLE IF EXISTS "pcp_tb_tp_processo_maquina";
--CREATE TABLE "pcp_tb_tp_processo_maquina" (
--	"id" int4 NOT NULL,
--	"nome" varchar(50) COLLATE "default",
--	"descricao" varchar(255) COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "pcp_tb_tp_processo_maquina" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_gp_material_cliente
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_material_cliente";
--CREATE TABLE "tb_gp_material_cliente" (
--	"id_material_cliente" int8 NOT NULL,
--	"id_processo" int4 NOT NULL,
--	"id_tipo_unidade" int4 NOT NULL,
--	"id_marca" int4,
--	"nome" varchar(50) NOT NULL COLLATE "default",
--	"observacao" varchar(300) COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"quantidade" numeric(10,2) NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_material_cliente" OWNER TO "hash";

---- ----------------------------
----  Table structure for rel_financeiro_financeiro_impostos_recuperar
---- ----------------------------
--DROP TABLE IF EXISTS "rel_financeiro_financeiro_impostos_recuperar";
--CREATE TABLE "rel_financeiro_financeiro_impostos_recuperar" (
--	"fin_id_master" int8 NOT NULL,
--	"fin_id_imposto" int8 NOT NULL,
--	"fin_id_conta" int8 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_financeiro_financeiro_impostos_recuperar" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_status_mailmkt
---- ----------------------------
--DROP TABLE IF EXISTS "tb_status_mailmkt";
--CREATE TABLE "tb_status_mailmkt" (
--	"smk_id" int4 NOT NULL,
--	"smk_descricao" varchar(45) NOT NULL COLLATE "default",
--	"smk_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_status_mailmkt" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_gp_posicao
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_posicao";
--CREATE TABLE "tb_gp_posicao" (
--	"id_posicao" int4 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_posicao" OWNER TO "hash";

---- ----------------------------
----  Table structure for tipo_fornecedor
---- ----------------------------
--DROP TABLE IF EXISTS "tipo_fornecedor";
--CREATE TABLE "tipo_fornecedor" (
--	"tif_id" int4 NOT NULL,
--	"tif_descricao" varchar(45) COLLATE "default",
--	"tif_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tipo_fornecedor" OWNER TO "hash";


-- ----------------------------
----  Table structure for tb_tipo_documento
---- ----------------------------
--DROP TABLE IF EXISTS "tb_tipo_documento";
--CREATE TABLE "tb_tipo_documento" (
--	"tid_id" int2 NOT NULL,
--	"tid_descricao" varchar(100) NOT NULL COLLATE "default",
--	"tid_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_tipo_documento" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_abertura
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_abertura";
--CREATE TABLE "tb_gp_abertura" (
--	"id_abertura" int4 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_abertura" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_gp_material_processo
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_material_processo";
--CREATE TABLE "tb_gp_material_processo" (
--	"id_material_processo" int8 NOT NULL,
--	"id_processo" int4 NOT NULL,
--	"id_tp_material" int4 NOT NULL,
--	"id_status_material" int4,
--	"id_tipo_unidade" int4 NOT NULL,
--	"id_marca" int4,
--	"id_item" int8,
--	"nome" varchar(50) COLLATE "default",
--	"observacao" varchar(300) COLLATE "default",
--	"quantidade" numeric(10,2) NOT NULL,
--	"qtd_baixado" numeric(10,2),
--	"vl_unitario" numeric(10,2),
--	"total" numeric(10,2),
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_material_processo" OWNER TO "hash";

--
---- ----------------------------
----  Table structure for th_gp_processo
---- ----------------------------
--DROP TABLE IF EXISTS "th_gp_processo";
--CREATE TABLE "th_gp_processo" (
--	"id_th_processo" int4 NOT NULL,
--	"pro_id" int4 NOT NULL,
--	"empresas_grupo_id" int4,
--	"empresas_id" int4,
--	"pes_id" int4,
--	"pro_codigo" varchar(50) COLLATE "default",
--	"pro_id_pedido" int4,
--	"pro_cliente" varchar(255) COLLATE "default",
--	"pro_contato" varchar(255) COLLATE "default",
--	"pro_desc_produto" varchar(500) COLLATE "default",
--	"pro_quantidade" varchar(255) COLLATE "default",
--	"pro_vlr_unt" numeric(15,6),
--	"pro_vlr_pedido" numeric(15,6),
--	"pro_data_entrega" timestamp(6) NULL,
--	"pro_prazo_entrega" varchar(255) COLLATE "default",
--	"sta_id" int4 NOT NULL,
--	"pro_data_inc" timestamp(6) NULL,
--	"pro_lote_numero" int4,
--	"enderecos_id" int8,
--	"dt_criacao" timestamp(6) NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"id_workspace" int4,
--	"id_processo_pai" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "th_gp_processo" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_grupos
---- ----------------------------
--DROP TABLE IF EXISTS "tb_grupos";
--CREATE TABLE "tb_grupos" (
--	"gru_id" int4 NOT NULL,
--	"gru_nome" varchar(45) COLLATE "default",
--	"gru_ativo" int2,
--	"apl_id_inicial" int4,
--	"gru_permite_valores_processo" int2,
--	"gru_ativa_empresa_lote" int2,
--	"gru_exporta_processo_excel" int2,
--	"gru_edita_liq_fin" int2,
--	"gru_imprimir_financeiro" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_grupos" OWNER TO "hash";

---- ----------------------------
----  Table structure for th_financeiro
---- ----------------------------
--DROP TABLE IF EXISTS "th_financeiro";
--CREATE TABLE "th_financeiro" (
--	"id_th_financeiro" int4 NOT NULL,
--	"fin_id" int8 NOT NULL,
--	"id_agrupador_financeiro" int8 NOT NULL,
--	"con_id" int2,
--	"tid_id" int2,
--	"tie_id" int2,
--	"cec_id" int4,
--	"ope_id" int4,
--	"plc_id" int4,
--	"grupo_id" int4,
--	"fin_compensacao" date,
--	"fin_competencia" date,
--	"fin_descricao" text NOT NULL COLLATE "default",
--	"fin_valor" numeric(10,2) NOT NULL,
--	"fin_vencimento" date,
--	"fin_emissao" date,
--	"fin_observacao" text COLLATE "default",
--	"fin_numero_doc" numeric(20,0),
--	"fin_num_doc_os" int8,
--	"fin_excluido" int2,
--	"pago" int2,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL,
--	"ativo" int2 NOT NULL,
--	"id_financeiro_correlato" int8
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "th_financeiro" OWNER TO "hash";
--
--COMMENT ON COLUMN "th_financeiro"."fin_competencia" IS 'mmaaaa';
--COMMENT ON COLUMN "th_financeiro"."fin_emissao" IS 'quando a operação foi cadastrada';
--COMMENT ON COLUMN "th_financeiro"."fin_numero_doc" IS 'Numero de documento interno';
--COMMENT ON COLUMN "th_financeiro"."fin_num_doc_os" IS 'Numero de documento externo';
--COMMENT ON COLUMN "th_financeiro"."fin_excluido" IS 'Este campo serve para dizer se um registro errado esta excluido, seja el vindo da importação ou não.';

---- ----------------------------
----  Table structure for tb_agrupador_financeiro
---- ----------------------------
--DROP TABLE IF EXISTS "tb_agrupador_financeiro";
--CREATE TABLE "tb_agrupador_financeiro" (
--	"id_agrupador_financeiro" int8 NOT NULL,
--	"id_empresa" int4,
--	"fin_descricao" text NOT NULL COLLATE "default",
--	"fin_valor" numeric(15,2) NOT NULL,
--	"pro_id" int4,
--	"fin_observacao" text COLLATE "default",
--	"fin_nota_fiscal" int8,
--	"plc_id" int4,
--	"tmv_id" int2 NOT NULL,
--	"moe_id" int2 NOT NULL,
--	"cec_id" int4,
--	"ope_id" int4,
--	"grupo_id" int4,
--	"transferencia" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL,
--	"ativo" int2 NOT NULL,
--	"id_agrupador_financeiro_correlato" int8,
--	"id_workspace" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_agrupador_financeiro" OWNER TO "hash";
--
--COMMENT ON COLUMN "tb_agrupador_financeiro"."grupo_id" IS 'faturado contra';



---- ----------------------------
----  Table structure for rel_processos_master_conta_financeiro
---- ----------------------------
--DROP TABLE IF EXISTS "rel_processos_master_conta_financeiro";
--CREATE TABLE "rel_processos_master_conta_financeiro" (
--	"fin_id" int8 NOT NULL,
--	"pro_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_processos_master_conta_financeiro" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_bancos
---- ----------------------------
--DROP TABLE IF EXISTS "tb_bancos";
--CREATE TABLE "tb_bancos" (
--	"bco_id" int4 NOT NULL,
--	"bco_comp" varchar(10) COLLATE "default",
--	"bco_nome" varchar(255) NOT NULL COLLATE "default",
--	"bco_site" varchar(255) COLLATE "default",
--	"bco_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_bancos" OWNER TO "hash";

---- ----------------------------
----  Table structure for th_gp_material_processo
---- ----------------------------
--DROP TABLE IF EXISTS "th_gp_material_processo";
--CREATE TABLE "th_gp_material_processo" (
--	"id_th_material_processo" int8 NOT NULL,
--	"id_material_processo" int8 NOT NULL,
--	"id_processo" int4 NOT NULL,
--	"id_tp_material" int4 NOT NULL,
--	"id_status_material" int4,
--	"id_tipo_unidade" int4 NOT NULL,
--	"id_marca" int4,
--	"id_item" int8,
--	"nome" varchar(50) COLLATE "default",
--	"observacao" varchar(300) COLLATE "default",
--	"quantidade" numeric(10,2) NOT NULL,
--	"qtd_baixado" numeric(10,2),
--	"vl_unitario" numeric(10,2),
--	"total" numeric(10,2),
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "th_gp_material_processo" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_gp_tam_papel
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_tam_papel";
--CREATE TABLE "tb_gp_tam_papel" (
--	"id_tam_papel" int4 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_tam_papel" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_tp_material
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_tp_material";
--CREATE TABLE "tb_gp_tp_material" (
--	"id_tp_material" int4 NOT NULL,
--	"nome" varchar(50) COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_tp_material" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_acabamento
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_acabamento";
--CREATE TABLE "tb_gp_acabamento" (
--	"id_acabamento" int4 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_acabamento" OWNER TO "hash";


---- ----------------------------
----  Table structure for rl_grupos_permissoes_aplicacoes
---- ----------------------------
--DROP TABLE IF EXISTS "rl_grupos_permissoes_aplicacoes";
--CREATE TABLE "rl_grupos_permissoes_aplicacoes" (
--	"per_id" int2 NOT NULL,
--	"apl_id" int4 NOT NULL,
--	"gru_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rl_grupos_permissoes_aplicacoes" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_desc_producao
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_desc_producao";
--CREATE TABLE "tb_gp_desc_producao" (
--	"id_desc_producao" int8 NOT NULL,
--	"id_categoria" int8 NOT NULL,
--	"id_tp_produto" int4,
--	"id_form_impressao" int4,
--	"id_tam_papel" int4,
--	"id_tam_chapa" int4,
--	"id_costura_caderno" int4,
--	"id_acabamento" int4,
--	"id_posicao" int4,
--	"id_abertura" int4,
--	"id_montagem" int4,
--	"qtd_pagina" int4,
--	"tam_pagina" varchar(50) COLLATE "default",
--	"formato_pagina" varchar(50) COLLATE "default",
--	"cores" varchar(50) COLLATE "default",
--	"pinca" int4,
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_desc_producao" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_moedas
---- ----------------------------
--DROP TABLE IF EXISTS "tb_moedas";
--CREATE TABLE "tb_moedas" (
--	"moe_id" int2 NOT NULL,
--	"moe_descricao" varchar(45) COLLATE "default",
--	"moe_sigla" varchar(3) COLLATE "default",
--	"moe_defaut" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_moedas" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_montagem
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_montagem";
--CREATE TABLE "tb_gp_montagem" (
--	"id_montagem" int4 NOT NULL,
--	"nome" varchar(50) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_montagem" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_grupo_trabalho
---- ----------------------------
--DROP TABLE IF EXISTS "tb_grupo_trabalho";
--CREATE TABLE "tb_grupo_trabalho" (
--	"grt_id" int4 NOT NULL,
--	"grt_descricacao" varchar(45) COLLATE "default",
--	"grt_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_grupo_trabalho" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_tipo_conta_rateio
---- ----------------------------
--DROP TABLE IF EXISTS "tb_tipo_conta_rateio";
--CREATE TABLE "tb_tipo_conta_rateio" (
--	"tcr_id" int2 NOT NULL,
--	"tcr_descricao" varchar(45) COLLATE "default",
--	"tcr_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_tipo_conta_rateio" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_atividade_tipo_interna
---- ----------------------------
--DROP TABLE IF EXISTS "tb_atividade_tipo_interna";
--CREATE TABLE "tb_atividade_tipo_interna" (
--	"ati_id" int2 NOT NULL,
--	"ati_nome" varchar(45) COLLATE "default",
--	"ati_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_atividade_tipo_interna" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_orientacao_tipo_servico
---- ----------------------------
--DROP TABLE IF EXISTS "tb_orientacao_tipo_servico";
--CREATE TABLE "tb_orientacao_tipo_servico" (
--	"ots_id" int4 NOT NULL,
--	"ots_descricao" varchar(200) NOT NULL COLLATE "default",
--	"tis_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_orientacao_tipo_servico" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_atividade_pessoa
---- ----------------------------
--DROP TABLE IF EXISTS "tb_atividade_pessoa";
--CREATE TABLE "tb_atividade_pessoa" (
--	"atp_id" int4 NOT NULL,
--	"atp_data_hora_inicio" timestamp(6) NOT NULL,
--	"atp_data_hora_fim" timestamp(6) NOT NULL,
--	"atp_inclusao" timestamp(6) NULL,
--	"atp_observacao" varchar(255) COLLATE "default",
--	"cec_id" int4,
--	"pro_id" int4,
--	"atv_id" int4 NOT NULL,
--	"atp_controle" varchar(10) COLLATE "default",
--	"ati_id" int2,
--	"fin_id_osi" int8
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_atividade_pessoa" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_pais
---- ----------------------------
--DROP TABLE IF EXISTS "tb_pais";
--CREATE TABLE "tb_pais" (
--	"pais_id" int4 NOT NULL,
--	"pais_nome" varchar(45) NOT NULL COLLATE "default",
--	"pais_ativo" varchar(45) COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_pais" OWNER TO "hash";



---- ----------------------------
----  Table structure for th_agrupador_financeiro
---- ----------------------------
--DROP TABLE IF EXISTS "th_agrupador_financeiro";
--CREATE TABLE "th_agrupador_financeiro" (
--	"id_th_agrupador_financeiro" int4 NOT NULL,
--	"id_agrupador_financeiro" int8 NOT NULL,
--	"id_workspace" int4,
--	"id_empresa" int4,
--	"fin_valor" numeric(15,2) NOT NULL,
--	"fin_descricao" text NOT NULL COLLATE "default",
--	"pro_id" int4,
--	"fin_observacao" text COLLATE "default",
--	"fin_nota_fiscal" int8,
--	"plc_id" int4,
--	"tmv_id" int2 NOT NULL,
--	"moe_id" int2 NOT NULL,
--	"cec_id" int4,
--	"ope_id" int4,
--	"grupo_id" int4,
--	"transferencia" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL,
--	"ativo" int2 NOT NULL,
--	"id_agrupador_financeiro_correlato" int8
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "th_agrupador_financeiro" OWNER TO "hash";

---- ----------------------------
----  Table structure for rel_empresas_gupoempresas
---- ----------------------------
--DROP TABLE IF EXISTS "rel_empresas_gupoempresas";
--CREATE TABLE "rel_empresas_gupoempresas" (
--	"id_empresas_grupo" int4 NOT NULL,
--	"id_empresas" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_empresas_gupoempresas" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_tipo_movimento
---- ----------------------------
--DROP TABLE IF EXISTS "tb_tipo_movimento";
--CREATE TABLE "tb_tipo_movimento" (
--	"tmv_id" int2 NOT NULL,
--	"tmv_descricao" varchar(45) NOT NULL COLLATE "default",
--	"tmv_descricao2" varchar(45) COLLATE "default",
--	"tmv_sigla" varchar(45) COLLATE "default",
--	"tmv_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_tipo_movimento" OWNER TO "hash";

---- ----------------------------
----  Table structure for ta_gp_material_processo_x_opcao
---- ----------------------------
--DROP TABLE IF EXISTS "ta_gp_material_processo_x_opcao";
--CREATE TABLE "ta_gp_material_processo_x_opcao" (
--	"id_material_processo" int8 NOT NULL,
--	"id_opcao" int8 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "ta_gp_material_processo_x_opcao" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_operacoes
---- ----------------------------
--DROP TABLE IF EXISTS "tb_operacoes";
--CREATE TABLE "tb_operacoes" (
--	"ope_id" int4 NOT NULL,
--	"ope_nome" varchar(100) NOT NULL COLLATE "default",
--	"ope_cpf_cnpj" varchar(14) COLLATE "default",
--	"ope_telefone1" varchar(45) COLLATE "default",
--	"ope_telefone2" varchar(45) COLLATE "default",
--	"ope_telefone3" varchar(45) COLLATE "default",
--	"ope_email1" varchar(200) COLLATE "default",
--	"ope_email2" varchar(200) COLLATE "default",
--	"ope_ativo" int2,
--	"empresas_grupo_id" int4,
--	"ope_emite_osi" int2,
--	"ope_recebe_osi" int2,
--	"id_workspace" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_operacoes" OWNER TO "hash";


---- ----------------------------
----  Table structure for pcp_tb_config_funcionarios
---- ----------------------------
--DROP TABLE IF EXISTS "pcp_tb_config_funcionarios";
--CREATE TABLE "pcp_tb_config_funcionarios" (
--	"id" int4 NOT NULL,
--	"id_func" int4,
--	"carga_horaria_diaria" time(6),
--	"almoco" time(6),
--	"inicio" time(6),
--	"fim" time(6),
--	"vl_hora" numeric(10,2)
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "pcp_tb_config_funcionarios" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_costura_caderno
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_costura_caderno";
--CREATE TABLE "tb_gp_costura_caderno" (
--	"id_costura_caderno" int4 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_costura_caderno" OWNER TO "hash";



---- ----------------------------
----  Table structure for pcp_tb_grupo
---- ----------------------------
--DROP TABLE IF EXISTS "pcp_tb_grupo";
--CREATE TABLE "pcp_tb_grupo" (
--	"id" int4 NOT NULL,
--	"nome" varchar(60) COLLATE "default",
--	"cpf" varchar(60) COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "pcp_tb_grupo" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_tipo_documento_externo
---- ----------------------------
--DROP TABLE IF EXISTS "tb_tipo_documento_externo";
--CREATE TABLE "tb_tipo_documento_externo" (
--	"tie_id" int2 NOT NULL,
--	"tie_descricao" varchar(100) NOT NULL COLLATE "default",
--	"tie_ativo" int2
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_tipo_documento_externo" OWNER TO "hash";



---- ----------------------------
----  Table structure for rel_status_processos_status_financeiro
---- ----------------------------
--DROP TABLE IF EXISTS "rel_status_processos_status_financeiro";
--CREATE TABLE "rel_status_processos_status_financeiro" (
--	"sta_id" int4 NOT NULL,
--	"stf_id" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_status_processos_status_financeiro" OWNER TO "hash";



---- ----------------------------
----  Table structure for rel_pessoal_cargos_departamento
---- ----------------------------
--DROP TABLE IF EXISTS "rel_pessoal_cargos_departamento";
--CREATE TABLE "rel_pessoal_cargos_departamento" (
--	"rel_id" int4 NOT NULL,
--	"pes_id" int4 NOT NULL,
--	"car_id" int4 NOT NULL,
--	"cdp_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_pessoal_cargos_departamento" OWNER TO "hash";






---- ----------------------------
----  Table structure for tb_financeiro
---- ----------------------------
--DROP TABLE IF EXISTS "tb_financeiro";
--CREATE TABLE "tb_financeiro" (
--	"fin_id" int8 NOT NULL,
--	"con_id" int2,
--	"tid_id" int2,
--	"tie_id" int2,
--	"fin_vencimento" date,
--	"fin_compensacao" date,
--	"fin_competencia" date,
--	"fin_descricao" text NOT NULL COLLATE "default",
--	"fin_valor" numeric(10,2),
--	"fin_emissao" date,
--	"fin_observacao" text COLLATE "default",
--	"ope_id" int4,
--	"plc_id" int4,
--	"grupo_id" int4,
--	"fin_numero_doc" varchar(45) COLLATE "default",
--	"fin_num_doc_os" varchar(45) COLLATE "default",
--	"cec_id" int4,
--	"id_criacao_usuario" int4,
--	"dt_criacao" timestamp(6) NULL,
--	"ativo" int2 NOT NULL,
--	"id_financeiro_correlato" int8,
--	"id_agrupador_financeiro" int8 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_financeiro" OWNER TO "hash";
--
--COMMENT ON COLUMN "tb_financeiro"."tid_id" IS 'Tipo de documento interno';
--COMMENT ON COLUMN "tb_financeiro"."tie_id" IS 'Tipo de documento externo';
--COMMENT ON COLUMN "tb_financeiro"."fin_vencimento" IS 'tanto para pagamento quanto para recebimento';
--COMMENT ON COLUMN "tb_financeiro"."fin_competencia" IS 'mmaaaa';
--COMMENT ON COLUMN "tb_financeiro"."fin_emissao" IS 'quando a operação foi cadastrada';
--COMMENT ON COLUMN "tb_financeiro"."grupo_id" IS 'faturado contra';
--COMMENT ON COLUMN "tb_financeiro"."fin_numero_doc" IS 'Numero de documento interno';
--COMMENT ON COLUMN "tb_financeiro"."fin_num_doc_os" IS 'Numero de documento externo';

---- ----------------------------
----  Table structure for rel_sacado_financeiro
---- ----------------------------
--DROP TABLE IF EXISTS "rel_sacado_financeiro";
--CREATE TABLE "rel_sacado_financeiro" (
--	"tb_financeiro_fin_id" int8 NOT NULL,
--	"empresas_id" int4,
--	"empresas_grupo_id" int4,
--	"pes_id" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "rel_sacado_financeiro" OWNER TO "hash";




---- ----------------------------
----  Table structure for tb_saldos_contas
---- ----------------------------
--DROP TABLE IF EXISTS "tb_saldos_contas";
--CREATE TABLE "tb_saldos_contas" (
--	"sac_id" int4 NOT NULL,
--	"sac_date_inicio" date NOT NULL,
--	"sac_date_fim" date NOT NULL,
--	"sac_date_inclusao" timestamp(6) NOT NULL,
--	"sac_valor" numeric(10,2) NOT NULL,
--	"sac_observacao" varchar(255) COLLATE "default",
--	"con_id" int2 NOT NULL,
--	"moe_id" int2 NOT NULL,
--	"usu_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_saldos_contas" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_atividade
---- ----------------------------
--DROP TABLE IF EXISTS "tb_atividade";
--CREATE TABLE "tb_atividade" (
--	"atv_id" int4 NOT NULL,
--	"pes_id" int4 NOT NULL,
--	"atv_inclusao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_atividade" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_gp_form_impressao
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_form_impressao";
--CREATE TABLE "tb_gp_form_impressao" (
--	"id_form_impressao" int4 NOT NULL,
--	"nome" varchar(45) NOT NULL COLLATE "default",
--	"ativo" int2 NOT NULL,
--	"dt_criacao" timestamp(6) NULL,
--	"id_criacao_usuario" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_form_impressao" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_arquivo
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_arquivo";
--CREATE TABLE "tb_gp_arquivo" (
--	"id_arquivo" int8 NOT NULL,
--	"pro_id" int4,
--	"nome" varchar(60) NOT NULL COLLATE "default",
--	"nome_md5" varchar(60) COLLATE "default",
--	"extensao" varchar(45) COLLATE "default",
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_arquivo" OWNER TO "hash";


---- ----------------------------
----  Table structure for tb_contas
---- ----------------------------
--DROP TABLE IF EXISTS "tb_contas";
--CREATE TABLE "tb_contas" (
--	"con_id" int2 NOT NULL,
--	"con_agencia" varchar(10) NOT NULL COLLATE "default",
--	"con_age_digito" varchar(3) COLLATE "default",
--	"con_numero" varchar(45) NOT NULL COLLATE "default",
--	"con_digito" varchar(5) NOT NULL COLLATE "default",
--	"tcb_id" int2,
--	"bco_id" int4 NOT NULL,
--	"con_codnome" varchar(50) COLLATE "default",
--	"con_ordem" varchar(2) COLLATE "default",
--	"id_workspace" int4,
--	"ativo" int2 NOT NULL,
--	"dt_criacao" timestamp(6) NULL,
--	"id_criacao_usuario" int4
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_contas" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_co_antecipacao
---- ----------------------------
--DROP TABLE IF EXISTS "tb_co_antecipacao";
--CREATE TABLE "tb_co_antecipacao" (
--	"id_antecipacao" int8 NOT NULL,
--	"id_campanha" int8,
--	"vl_inicio" numeric(10,2),
--	"vl_final" numeric(10,2),
--	"porcentagem" numeric(10,2),
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_co_antecipacao" OWNER TO "hash";



---- ----------------------------
----  Table structure for tb_credito
---- ----------------------------
--DROP TABLE IF EXISTS "tb_credito";
--CREATE TABLE "tb_credito" (
--	"id_credito" int4 NOT NULL,
--	"empresas_id" int4 NOT NULL,
--	"limite_credito" varchar(45) NOT NULL COLLATE "default",
--	"posicao_serasa" varchar(45) COLLATE "default",
--	"numero_serasa" varchar(45) COLLATE "default",
--	"data_consulta_serasa" date,
--	"consultado_por" int2 NOT NULL,
--	"situacao_serasa" int2,
--	"analise_risco" varchar(200) NOT NULL COLLATE "default",
--	"dt_criacao" timestamp(6) NOT NULL,
--	"ativo" int2 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_credito" OWNER TO "hash";




---- ----------------------------
----  Table structure for tb_historico
---- ----------------------------
--DROP TABLE IF EXISTS "tb_historico";
--CREATE TABLE "tb_historico" (
--	"his_id" int4 NOT NULL,
--	"his_descricao" text COLLATE "default",
--	"pro_id" int4 NOT NULL,
--	"his_data" timestamp(6) NULL,
--	"sta_id" int4 NOT NULL,
--	"usu_id" int4 NOT NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_historico" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_gp_lote_producao
---- ----------------------------
--DROP TABLE IF EXISTS "tb_gp_lote_producao";
--CREATE TABLE "tb_gp_lote_producao" (
--	"id_lote_producao" int4 NOT NULL,
--	"id_processo" int4 NOT NULL,
--	"id_empresa" int4,
--	"cod_lote" varchar(45) NOT NULL COLLATE "default",
--	"quantidade" int4,
--	"dt_entrega" date,
--	"ativo" int2 NOT NULL,
--	"id_criacao_usuario" int4 NOT NULL,
--	"dt_criacao" timestamp(6) NULL
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_gp_lote_producao" OWNER TO "hash";

---- ----------------------------
----  Table structure for tb_log
---- ----------------------------
--DROP TABLE IF EXISTS "tb_log";
--CREATE TABLE "tb_log" (
--	"id" int4 NOT NULL,
--	"inserted_date" timestamp(6) NULL,
--	"username" int4,
--	"application" varchar(200) NOT NULL COLLATE "default",
--	"creator" varchar(30) NOT NULL COLLATE "default",
--	"ip_user" varchar(32) NOT NULL COLLATE "default",
--	"action" varchar(30) NOT NULL COLLATE "default",
--	"description" text COLLATE "default"
--)
--WITH (OIDS=FALSE);
--ALTER TABLE "tb_log" OWNER TO "hash";
