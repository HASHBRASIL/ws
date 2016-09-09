<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/11/2013
 */
class Processo_Model_Dao_Comentario extends App_Model_Dao_Abstract
{
    protected $_name         = "pro_tb_gp_comentario";
    protected $_primary      = "id_comentario";
    protected $_namePairs    = "descricao";

    protected $_rowClass = 'Processo_Model_Vo_Comentario';

    protected $_referenceMap    = array(
            'Empresa' => array(
                    'columns'           => 'id_corporativa',
                    'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
                    'refColumns'        => 'id'
            ),
            'Usuario' => array(
                    'columns'           => 'id_criacao_usuario',
                    'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
                    'refColumns'        => 'id'
            )
    );
}