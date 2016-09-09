<?php
class Empresa_Model_Vo_Empresa extends App_Model_Vo_Row
{

    public function getListEndereco()
    {
        if(!empty($this->id)){
            $enderecoDao = new Sis_Model_Dao_Endereco();
            $select = $enderecoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
            return $this->findDependentRowset('Sis_Model_Dao_Endereco', 'Empresa', $select);
        }
        return;
    }

    public function getTipoCliente()
    {
        return $this->findParentRow('Empresa_Model_Dao_TipoCliente', 'Tipo cliente');
    }

    public function getTipoFornecedor()
    {
        return $this->findParentRow('Empresa_Model_Dao_TipoFornecedor', 'Tipo fornecedor');
    }

    public function getSegmento()
    {
        return $this->findParentRow('Sis_Model_Dao_Segmento', 'Segmento');
    }

    public function getIndicacao()
    {
        return $this->findParentRow('Sis_Model_Dao_Indicacao', 'Indicacao');
    }

    public function getPortal()
    {
        return $this->findParentRow('Empresa_Model_Dao_Portal', 'Portal');
    }

    public function getMailMarketing()
    {
        return $this->findParentRow('Empresa_Model_Dao_MailMarketing', 'Mail marketing');
    }

    public function getTipoPessoa()
    {
        return $this->findParentRow('Sis_Model_Dao_TipoPessoa', 'Tipo pessoa');
    }

    public function getResponsavel()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Responsavel');
    }

    public function __toString()
    {
        return $this->fantasia? $this->fantasia : $this->nome_razao;
    }

    public function getListContato()
    {
        if(!empty($this->id)){
            $contatoDao = new Sis_Model_Dao_Contato();
            $select = $contatoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
            return $this->findDependentRowset('Sis_Model_Dao_Contato', 'Empresa', $select);
        }
        return;
    }

    public function getListEmpresa($option = null)
    {
        if(!empty($this->id)){
            $empresaDao = new Empresa_Model_Dao_Empresa();
            $select = $empresaDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
            if($option){
                foreach ($option as $key => $value){
                    $select->where($key, $value);
                }
            }
            return $this->findDependentRowset('Empresa_Model_Dao_Empresa', 'Responsavel', $select);
        }
        return;
    }

}