<?php

class Config_Model_Dao_Convite extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_convite";
    protected $_primary       = "id";
    
    protected $_rowClass = 'Config_Model_Vo_Convite';

    public $searchFields = array(
        'convidada'     => "convid.nome",
        'responsavel'   => "resp.nome",
        'aceitegrupo'   => "conv.aceitogrupo",
        'aceitepessoa'  => "conv.aceitopessoa"
    );
    
    public function getConvitesAprovacaoTime($time) {
        $select = $this->select()
                       ->from(array('cvt' => $this->_name))
                       ->join(array('rlgp'=> 'rl_grupo_pessoa'), 'cvt.id = rlgp.id')
                       ->where('rlgp.id_grupo = ?',$time);
        $ret = $this->fetchAll($select)->toArray();
    }
    
    public function getConvitesAprovacaoTimeGrid($time) {

        $select = $this->_db->prepare( "with recursive getgrupos as (
                                            select id,nome from tb_grupo where id = :idTime
                                            union
                                            select g.id,g.nome from tb_grupo g join getgrupos gg on gg.id = g.id_pai
                                        ) select distinct(gg.id) from getgrupos gg " );
        $select->bindParam( ':idTime', $time );
        $select->execute();
        $grupos = $select->fetchAll(PDO::FETCH_ASSOC);
        $arrayGrupo = array();
        foreach ($grupos as $grupo) {
         $arrayGrupo[] = $grupo['id']; 
        }
        $idsGrupos = implode(', ',$arrayGrupo);
        $select = $this ->select()->setIntegrityCheck(false)
                         ->from(array('conv'      => 'tb_convite'), array('*', 'aceitegrupo'=> new Zend_Db_Expr("CASE WHEN aceitogrupo = TRUE THEN 'Sim' WHEN aceitogrupo = FALSE THEN 'Não' ELSE 'Pendente' END"), 'aceitepessoa'=> new Zend_Db_Expr("CASE WHEN aceitopessoa = TRUE THEN 'Sim' WHEN aceitopessoa = FALSE THEN 'Não' ELSE 'Penderente' END") ))
                         ->join(array('rlgp' => 'rl_grupo_pessoa'), 'rlgp.id = conv.id', array('*', 'rastrogrupo'=> new Zend_Db_Expr('rastrogrupo(rlgp.id_grupo)')))
                         ->join(array('convid' => 'tb_pessoa'), 'rlgp.id_pessoa = convid.id', array('nome AS convidada'))
                         ->join(array('resp' => 'tb_pessoa'), 'rlgp.id_responsavel = resp.id', array('nome AS responsavel'))
                         ->join(array('gru' => 'tb_grupo'), 'rlgp.id_grupo = gru.id', array('grupo'=> new Zend_Db_Expr("CASE WHEN gru.metanome = 'Geral' THEN 'Membro' ELSE 'Seguir' END")))
                         ->where('rlgp.id_grupo in (?)', $arrayGrupo);
        
        return $select;
    }
    
    
    public function mudaStatusConvite($id, $status) {
        $stmt = $this->_db->prepare("UPDATE tb_convite SET aceitogrupo = :aceitogrupo WHERE id = :id");

        $stmt->bindValue(':aceitogrupo', $status);
        $stmt->bindValue(':id',$id);
        return $stmt->execute();
    }
}