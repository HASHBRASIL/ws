<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_Model_Dao_Empresa extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_empresas";
    protected $_primary       = "id";
    protected $_namePairs     = "nome_razao";
    protected $_colsSearch = array('nome_razao', 'fantasia', 'cnpj_cpf', 'estadual', 'municipal', 'site', 'telefone1', 'telefone2', 'telefone3', 'email_corporativo', 'observacoes', 'uasg', 'transacao');

    protected $_rowClass = 'Empresa_Model_Vo_Empresa';
    protected $_dependentTables = array('Sis_Model_Dao_Endereco', 'Material_Model_Dao_Nfe', 'Material_Model_Dao_Transportador', 'Material_Model_Dao_Protocolo', 'Service_Model_Dao_Protocolo', 'Financial_Model_Dao_SacadoFinanceiro', 'Financial_Model_Dao_Credito','Auth_Model_Dao_Usuario', 'Financial_Model_Dao_AgrupadorFinanceiro', 'Compra_Model_Dao_Compra','Rh_Model_Dao_Local','Freelancer_Model_Dao_Tarefa');

    protected $_referenceMap    = array(
    		'Tipo funcionario' => array(
    				'columns'           => 'id_tp_funcionario',
    				'refTableClass'     => 'Empresa_Model_Dao_TipoFuncionario',
    				'refColumns'        => 'id_tp_funcionario'
    		),
            'Tipo cliente' => array(
                    'columns'           => 'tic_id',
                    'refTableClass'     => 'Empresa_Model_Dao_TipoCliente',
                    'refColumns'        => 'tic_id'
            ),
            'Tipo fornecedor' => array(
                    'columns'           => 'tif_id',
                    'refTableClass'     => 'Empresa_Model_Dao_TipoFornecedor',
                    'refColumns'        => 'tif_id'
            ),
            'Segmento' => array(
                    'columns'           => 'seg_id',
                    'refTableClass'     => 'Sis_Model_Dao_Segmento',
                    'refColumns'        => 'seg_id'
            ),
            'Indicacao' => array(
                    'columns'           => 'ind_id',
                    'refTableClass'     => 'Sis_Model_Dao_Indicacao',
                    'refColumns'        => 'ind_id'
            ),
            'Portal' => array(
                    'columns'           => 'poc_id',
                    'refTableClass'     => 'Empresa_Model_Dao_Portal',
                    'refColumns'        => 'poc_id'
            ),
            'Mail marketing' => array(
                    'columns'           => 'smk_id',
                    'refTableClass'     => 'Empresa_Model_Dao_MailMarketing',
                    'refColumns'        => 'smk_id'
            ),
            'Tipo pessoa' => array(
                    'columns'           => 'tps_id',
                    'refTableClass'     => 'Sis_Model_Dao_TipoPessoa',
                    'refColumns'        => 'tps_id'
            ),
            'Responsavel' => array(
                    'columns'           => 'empresas_id_pai',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )
    );

    public function getAllEmpresa()
    {
        $select = $this->select();
        $select->from($this->_name)
               ->where("ativo = ?", App_Model_Dao_Abstract::ATIVO);
        return $select;
    }

    public function selectPaginator(array $options = null, $order = null)
    {
        $select = $this->_db->select()->from("vw_tb_empresa");
        if($order){
            $select->order($order);
        }
        $this->_searchPaginator($select, $options);
        $this->_condPaginator($select);

        return $select;
    }

    public function getAutocomplete($term, $chave = null, $valor = null,
            $where = null, $ordem = null, $limit = null){
        if(empty($chave)){
            if(is_array($this->_primary)){
                $chave = $this->_primary[1];
            }else{
                $chave = $this->_primary;
            }
        }

        if(empty($valor)){
            $valor = "nome_razao";
        }

        $select = $this->_db
        ->select()
        ->from('vw_tb_empresa', array('value'      => new Zend_Db_Expr("concat( concat('(', transacao, ')'), ' - ', {$valor} )"),
                                      'id'         => $this->_primary,
                                      'label'      =>  new Zend_Db_Expr("concat( concat('(', transacao, ')'), ' - ', {$valor} )")
                                                  ));
        if( is_numeric( $limit) ){
            $select->limit( $limit );
        }else {
            $select->limit(1000);
        }

        if($where){
            if (is_array($where)){
                foreach ($where as $key => $value){
                    $select->where($key, $value);
                }
            }else{
                $select->where($where);
            }
        }
        $select->where('vw_tb_empresa.cnpj_cpf is not null');
        $select->where($valor.' like "%'.$term.'%"')
        ->order(array($ordem ? $ordem : 'transacao DESC'));

        return $this->_db->fetchAll($select);

    }

    public function getAutocompleteByCnpj($term, $chave = null, $valor = null,
            $where = null, $ordem = null, $limit = null){
        if(empty($chave)){
            if(is_array($this->_primary)){
                $chave = $this->_primary[1];
            }else{
                $chave = $this->_primary;
            }
        }

        if(empty($valor)){
            $valor = "cnpj_cpf";
        }

        $select = $this->_db
        ->select()
        ->from('vw_tb_empresa', array('value'      => $valor,
        'id'         => $this->_primary,
        'label'      => $valor
        ))
        ->order($ordem ? $ordem : $valor);

        if( is_numeric( $limit) ){
            $select->limit( $limit );
        }else {
            $select->limit(1000);
        }

        if($where){
            if (is_array($where)){
                foreach ($where as $key => $value){
                    $select->where($key, $value);
                }
            }else{
                $select->where($where);
            }
        }
        $select->where($valor.' like "%'.$term.'%"');

        return $this->_db->fetchAll($select);

    }

    /**
     * @desc busca todos os dados da pessoa pelo cpf
     * @param int $cpf
     * @return array fetchRow
     */
    public function findEmpresaByCnpj($cnpj)
    {
        if( is_numeric($cnpj)){
            $select = $this->_db->select()->from(array('te' => $this->_name) , array('*', 'isEmpresa' => new Zend_Db_Expr("if(tps_id = 1, 1,0)")) )
            ->joinInner(array('tu'=>'tb_usuarios'), 'tu.id_empresa = te.id', array('usu_id', 'root'))
            ->where('cnpj_cpf = ?', $cnpj)
            ->where('te.ativo = ?', parent::ATIVO)
            ->where('tu.ativo = ?', parent::ATIVO);
            return $this->_db->fetchRow($select, null, Zend_Db::FETCH_OBJ);
        }
    }

    public function countEmpresas(){

    	$select = $this->_db->select()->from(array('te' => $this->_name) ,array( 'num' => new Zend_Db_Expr( 'COUNT(*)' ) ) )
    	->where('te.ativo = ?', parent::ATIVO);
    	return $this->_db->fetchRow($select, null, Zend_Db::FETCH_OBJ);

    }
    public function countEmpresasLastDays(){

    	$select = $this->_db->select()->from(array('te' => $this->_name) ,array( 'days' => new Zend_Db_Expr( 'COUNT(*)' ) ) )
    	->where('te.dt_cadastro > ?',date('Y-m-d', mktime( 0, 0, 0, date('m') - 6 , date('d'), date("Y") ) ) )
    	->where('te.ativo = ?', parent::ATIVO);
    	return $this->_db->fetchRow($select, null, Zend_Db::FETCH_OBJ);

    }

    public function getEmpresaRelatorio($cond,$resSql = false)
    {
        $select = $this->_db->select();


        $select->from(array('vw' => 'vw_tb_empresa') ,array('vw.id',
                'upper(vw.nome_razao) as nome_razao',
                "if(vw . transportador = 1,'TRANSPORTADOR','NÃO TRANSPORTADOR') AS transportador",
                "date_format( vw.dt_cadastro , '%d/%m/%y') as dt_cadastro",
                "vw.transacao as transacao",
                "(select date_format(sysdate(),'%d/%m/%y - %h:%m:%s'))as dataAtual"))
            ->where("vw.ativo = ?",1)
            ->where("vw.nome_razao <> ''")
            ->joinLeft(array('end'=>'tb_enderecos'), 'end.id_empresas = vw.id', null)
            ->joinLeft(array('endref'=>'tb_tp_endereco_ref'), 'endref.id_endereco = end.id')
            ->joinLeft(array('tpend'=>'tb_tipo_endereco'), 'tpend.tie_id = endref.tie_id',"UPPER(tpend.tie_descricao) as tie_descricao")
            ->joinLeft(array('tpsega'=>'tb_segmento_atividade'), 'tpsega.seg_id = vw.seg_id')
            ->joinLeft(array('tpseg'=>'tb_tipo_segmento'), 'tpseg.tis_id = tpsega.tis_id',"UPPER( tpseg.tis_descricao) as tis_descricao")
            ->joinLeft(array('tpforn'=>'tipo_fornecedor'), 'tpforn.tif_id = vw.tif_id',"UPPER( tpforn.tif_descricao) as tif_descricao")
            ->joinLeft(array('ufs'=>'tb_ufs'), 'ufs.ufs_id = end.ufs_id',"UPPER( ufs.ufs_sigla) as ufs_sigla")
            ->joinLeft(array('cid'=>'tb_cidades'), 'cid.cid_id = end.cid_id',"UPPER( cid.cid_nome) as cid_nome")
            ->limit(500);

            if(!empty($cond[1])){
                $select->where('end.ufs_id = ?',$cond[1]);
                $cidade = $cond[2];
            }

            if(!empty($cond[2])){
                $select->where('end.cid_id = ?',$cidade);
            }

            if(!empty($cond[3])){
                $select->where('tpend.tie_id = ?',$cond[3]);
            }

            if(!empty($cond[4])){
                $select->where('vw.transportador = ?',$cond[4]);
            }

            if(!empty($cond[5])){
                $select->where('tpseg.tis_id = ?',$cond[5]);
            }

            if(!empty($cond[6])){
                $select->where('tpforn.tif_id = ?',$cond[6]);
            }

            if(!empty($cond[7]) && !empty($cond[8])){
                $select->where('vw.transacao >= ?',$cond[7]);
                $select->where('vw.transacao <= ?',$cond[8]);
            }

            if(!empty($cond[9]) && !empty($cond[10])){
                $subquery = $this->_db->select();
                $subquery->from(array('tbfin'=>'tb_financeiro'), array('rsfin.empresas_id'))
                ->joinInner(array('rsfin'=>'rel_sacado_financeiro'), 'rsfin.tb_financeiro_fin_id = tbfin.fin_id','')
                ->where("tbfin.fin_compensacao BETWEEN '".$cond[9]."' AND '". $cond[10]."'")
                ->where(' rsfin . empresas_id IS NOT NULL');
                $select->where("vw.id IN (?)", $subquery);
            }

            if(!empty($cond[12]) && !empty($cond[13])){
                $select->where('vw.dt_cadastro >= ?',$cond[12]);
                $select->where('vw.dt_cadastro <= ?',$cond[13]);
            }


            //Retorna os registros ou a string SQL
            if($resSql){
                return $this->_db->fetchAll($select);
            }else{
                $nregistro = count($this->_db->fetchAll($select));
                $res['numRes'] =$nregistro;

                if($nregistro){
                    $sql = $select->__toString();
                    $res['sql'] = str_replace("`", " ", $sql);
                }
                return $res;
            }
        }

        public function selectPaginatorInativos(array $options = null)
        {
        	$select = $this->_db->select()->from("tb_empresas")->where("ativo = ?", App_Model_Dao_Abstract::INATIVO);
        	$this->_searchPaginator($select, $options);
        	return $select;
        }

        /**
         * @param string $chave o campo que será usado como chave.
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_primary
         * @param string $valor o campo que deve ser retornado no valor
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_namePairs
         * @param string $where
         * @param string $ordem
         * @param string $limit
         * @return Ambigous <multitype:, multitype:mixed >
         */
        public function fetchPairsFuncionario($chave = null, $valor = null, $where = null, $ordem = null, $limit = null)
        {
        	if(empty($chave)){
        		if(is_array($this->_primary)){
        			$chave = $this->_primary[1];
        		}else{
        			$chave = $this->_primary;
        		}
        	}

        	if(empty($valor)){
        		$valor = $this->_namePairs;
        	}

        	$select = $this->_db
        	->select()
        	->from(array('te' => $this->_name), array($chave, $valor))
        	->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = te.id')
        	->where('ta.id_caracteristica = ?', Empresa_Model_Bo_Caracteristica::FUNCIONARIO)
        	->order($ordem ? $ordem : $valor);

        	if( is_numeric( $limit) ){
        		$select->limit( $limit );
        	}

        	if($where){
        		if (is_array($where)){
        			foreach ($where as $key => $value){
        				$select->where($key, $value);
        			}
        		}else{
        			$select->where($where);
        		}
        	}
        	$select->where("te.cnpj_cpf is not null");

        	return $this->_db->fetchPairs($select);
        }

        /**
         * @param string $chave o campo que será usado como chave.
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_primary
         * @param string $valor o campo que deve ser retornado no valor
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_namePairs
         * @param string $where
         * @param string $ordem
         * @param string $limit
         * @return Ambigous <multitype:, multitype:mixed >
         */
        public function fetchPairsGrupo($chave = null, $valor = null, $where = null, $ordem = null, $limit = null)
        {
        	if(empty($chave)){
        		if(is_array($this->_primary)){
        			$chave = $this->_primary[1];
        		}else{
        			$chave = $this->_primary;
        		}
        	}

        	if(empty($valor)){
        		$valor = $this->_namePairs;
        	}

        	$select = $this->_db
        	->select()
        	->from(array('te' => $this->_name), array($chave, $valor))
        	->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = te.id')
        	->where('ta.id_caracteristica = ?', Empresa_Model_Bo_Caracteristica::GRUPO)
        	->order($ordem ? $ordem : $valor);

        	if( is_numeric( $limit) ){
        		$select->limit( $limit );
        	}

        	if($where){
        		if (is_array($where)){
        			foreach ($where as $key => $value){
        				$select->where($key, $value);
        			}
        		}else{
        			$select->where($where);
        		}
        	}

        	$select->where("te.cnpj_cpf is not null");
        	return $this->_db->fetchPairs($select);
        }

        /**
         * @param string $chave o campo que será usado como chave.
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_primary
         * @param string $valor o campo que deve ser retornado no valor
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_namePairs
         * @param string $where
         * @param string $ordem
         * @param string $limit
         * @return array(value => $chave, label => valor)
         */
        public function getAutocompleteCaracteristica($idCaracteristica, $term, $chave = null, $valor = null, $where = null, $ordem = null, $limit = null){
        	if(empty($chave)){
        		if(is_array($this->_primary)){
        			$chave = $this->_primary[1];
        		}else{
        			$chave = $this->_primary;
        		}
        	}

        	if(empty($valor)){
        		$valor = $this->_namePairs;
        	}

        	$select = $this->_db
        	->select()
        	->from(array('te' =>$this->_name), array('value' => $valor,'id'=>$chave, 'label' => $valor))
        	->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = te.id')
        	->where('ta.id_caracteristica = ?', $idCaracteristica)
        	->order($ordem ? $ordem : $valor);

        	if( is_numeric( $limit) ){
        		$select->limit( $limit );
        	}else {
        		$select->limit(1000);
        	}

        	if($where){
        		if (is_array($where)){
        			foreach ($where as $key => $value){
        				$select->where($key, $value);
        			}
        		}else{
        			$select->where($where);
        		}
        	}
        	$select->where($valor.' like "%'.$term.'%"');
        	return $this->_db->fetchAll($select);

        }

        public function getListFaturadoWithAgrupadorAndWorkspacePerTransacao($grupoId = null, $workspace = null){

        	$select = $this->_db->select()->from(array('emp' => $this->_name),array("id","nome_razao", new Zend_Db_Expr('sum(agf.fin_valor) AS fin_valor')))
        	->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = emp.id', array(null))
        	->joinInner(array('agf'=>'tb_agrupador_financeiro'), 'agf.grupo_id = emp.id', array("fin_valor", "id_agrupador_financeiro"))
        	->joinInner(array('tmv'=>'tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
        	->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));

        	if (isset($grupoId)){

        		$select->where("emp.id = ?", $grupoId);
        	}

        	if ($workspace){

        		$select->where('agf.id_workspace = ?', $workspace);
        	}

        	$select->group(array("wk.nome", "emp.id", "agf.tmv_id"));

        	$select->where('ta.id_caracteristica = ?', Empresa_Model_Bo_Caracteristica::GRUPO)
        	->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO)
        	->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
        	->where("emp.ativo = ?",App_Model_Dao_Abstract::ATIVO);

        	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

        }

        public function getListFaturadoWithAgrupadorAndWorkspacePerTicket($grupoId = null, $workspace = null){

        	$select = $this->_db->select()->from(array('emp' => $this->_name),array("id","nome_razao", new Zend_Db_Expr('sum(fin.fin_valor) AS fin_valor')))
        	->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = emp.id', array(null))
        	->joinInner(array('fin'=>'tb_financeiro'), 'fin.grupo_id = emp.id', array("fin_id"))
        	->joinInner(array('agf'=>'tb_agrupador_financeiro'), 'agf.id_agrupador_financeiro = fin.id_agrupador_financeiro', array(null))
        	->joinInner(array('tmv'=>'tb_tipo_movimento'), 'tmv.tmv_id = agf.tmv_id', array("tmv_descricao"))
        	->joinInner(array('wk'=>'tb_workspace'), 'agf.id_workspace = wk.id_workspace', array("nome_workspace" => "nome"));

        	if (isset($grupoId)){

        		$select->where("emp.id = ?", $grupoId);
        	}

        	if ($workspace){

        		$select->where('agf.id_workspace = ?', $workspace);
        	}

        	$select->group(array("wk.nome", "emp.id", "agf.tmv_id"));

        	$select->where('ta.id_caracteristica = ?', Empresa_Model_Bo_Caracteristica::GRUPO)
        	->where("agf.ativo = ?",App_Model_Dao_Abstract::ATIVO)
        	->where("fin.ativo = ?",App_Model_Dao_Abstract::ATIVO)
        	->where("wk.ativo = ?",App_Model_Dao_Abstract::ATIVO)
        	->where("emp.ativo = ?",App_Model_Dao_Abstract::ATIVO);

        	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);

        }

        /**
         * @param int id da caracteristica de sua escolha.
         * @param string $chave o campo que será usado como chave.
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_primary
         * @param string $valor o campo que deve ser retornado no valor
         * Caso String vazia ou null, pega a chave primaria definida no atributo $_namePairs
         * @param string $where
         * @param string $ordem
         * @param string $limit
         * @return Ambigous <multitype:, multitype:mixed >
         */

        public function fetchPairsCaracteristica($caracteristica, $where = null, $limit = null)
        {

        	$select = $this->_db
        	->select()
        	->from(array('te' => $this->_name), array('te.nome_razao'))
        	->joinInner(array('ta'=>'ta_caracteristica_x_empresa'), 'ta.id_empresa = te.id')
        	->where('ta.id_caracteristica = ?', $caracteristica);

        	if( is_numeric( $limit) ){
        		$select->limit( $limit );
        	}

        	if($where){
        		if (is_array($where)){
        			foreach ($where as $key => $value){
        				$select->where($key, $value);
        			}
        		}else{
        			$select->where($where);
        		}
        	}
        	return $this->_db->fetchAll($select);
        }
}
