<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  20/05/2013
 */
class Processo_Model_Dao_Historico extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_th_gp_processo";
    protected $_primary  = "id_th_processo";

    protected $_rowClass = 'Processo_Model_Vo_Historico';


    protected $_referenceMap    = array(

            'Empresa' => array(
                    'columns'           => 'empresas_id',
                    'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
                    'refColumns'        => 'id'
            ),

            'EmpresaGrupo' => array(
                    'columns'           => 'empresas_grupo_id',
                    'refTableClass'     => 'Empresa_Model_Dao_EmpresaGrupo',
                    'refColumns'        => 'id'
            ),
            'Pessoal' => array(
                    'columns'           => 'pes_id',
                    'refTableClass'     => 'Auth_Model_Dao_Pessoal',
                    'refColumns'        => 'pes_id'
            ),
            'Status' => array(
                    'columns'           => 'sta_id',
                    'refTableClass'     => 'Processo_Model_Dao_Status',
                    'refColumns'        => 'sta_id'
            ),
            'Usuario' => array(
                    'columns'           => 'id_criacao_usuario',
                    'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
                    'refColumns'        => 'id'
            )
    );


    public function getIdAlteradoNow()
    {
        $select = $this->_db->select();
        $select->from($this->_name, array('pro_codigo', 'pro_id'))
               ->where('dt_criacao LIKE ? ', date('Y-m-d').'%')
               ->group('pro_id');

        return $this->_db->fetchPairs($select);
    }
}