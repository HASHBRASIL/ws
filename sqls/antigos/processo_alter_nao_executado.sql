
-- NAO FORAM EXECUTADOS

ALTER TABLE "pro_tb_gp_processo_servico" ADD CONSTRAINT "fk_tb_processo_has_tb_gs_servico_tb_gs_servico1" FOREIGN KEY ("id_servico") REFERENCES "tb_gs_servico" ("id_servico") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "pro_tb_processo" ADD CONSTRAINT "fk_tb_processo_tb_empresas_grupo1" FOREIGN KEY ("empresas_grupo_id") REFERENCES "tb_empresas_grupo" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "pro_tb_processo" ADD CONSTRAINT "fk_tb_processo_tb_pessoal1" FOREIGN KEY ("pes_id") REFERENCES "tb_pessoal" ("pes_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "pro_tb_processo" ADD CONSTRAINT "tb_processo_ibfk_3" FOREIGN KEY ("enderecos_id") REFERENCES "tb_enderecos" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "th_gp_processo" ADD CONSTRAINT "fk_tb_historico_processo_tb_empresas_grupo1" FOREIGN KEY ("empresas_grupo_id") REFERENCES "tb_empresas_grupo" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "th_gp_processo" ADD CONSTRAINT "fk_tb_historico_processo_tb_enderecos1" FOREIGN KEY ("enderecos_id") REFERENCES "tb_enderecos" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE "th_gp_processo" ADD CONSTRAINT "fk_th_processo_tb_pessoal1" FOREIGN KEY ("pes_id") REFERENCES "tb_pessoal" ("pes_id") ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE;



