<?php
class Relatorio_Model_Dao_Funcionario extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_empresas";
    protected $_primary       = "id";
    protected $_namePairs     = "nome_razao";

    public function getListFuncionarios(){
        $retorno = array();
        $select = $this->_db->select()
            ->from (array('emp'=>$this->_name))
            ->joinInner(array('caracemp'=>'ta_caracteristica_x_empresa'), "caracemp.id_empresa = emp.id")
            ->where('emp.ativo = ?',App_Model_Dao_Abstract::ATIVO)
            ->where('caracemp.id_caracteristica = ?', Empresa_Model_Bo_Caracteristica::FUNCIONARIO);
        $retorno['sql'] = $select->__toString();
        $retorno['total'] = count($this->_db->fetchAll($select));
        return $retorno;
    }

}