<?php

abstract class App_Model_Dao_Abstract extends Zend_Db_Table_Abstract
{
    protected $db;
    protected $transaction = false;
    protected $_namePairs = 'nome';
    protected $_rowClass = "App_Model_Vo_Row";
    protected $_colsSearch;

    const ATIVO = 1;
    const INATIVO = 0;
    const STRING_SEARCH = "all";


    /**
     *
     */
    public function init() {
        $this->db = $this->getDefaultAdapter();
    }


    public function get($id = null)
    {
        if (is_numeric($id)) {
            $row = $this->find($id)->current();

            if (!empty($row)) {
                return $row;
            }
        }

        return $this->createRow();
    }

    /**
     * @desc pegar toda a lista
     * @param boolean $ativo default = true
     * @return array|object Zend_Db_Table_Rowset_Abstract
     */
    public function getList($ativo = true)
    {
        $where = "";
        if ($ativo) {
            $where = array('ativo = ?' => self::ATIVO);
        }
        return $this->fetchAll($where);
    }

    public function getFalseCount()
    {
        $select = $this ->select()->setIntegrityCheck(false)    
                        ->from(array('pg_catalog.pg_tables'),
                        array( Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'tablename' ))
                        ->limit(1);

        return $select;
    }
        
    public function fetch($criteria = null, $order = null, $count = null, $offset = null)
    {
        return $this->fetchAll($criteria, $order, $count, $offset);
    }

    public function fetchOne($criteria = null, $order = null, $count = null, $offset = null)
    {
        $rs = $this->fetch($criteria, $order, $count, $offset);

        if ($rs) {
            return $rs->current();
        } else {
            return null;
        }
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
    public function fetchPairs($chave = null, $valor = null, $where = null, $ordem = null, $limit = null)
    {
        if (empty($chave)) {
            if (is_array($this->_primary)) {
                $chave = $this->_primary[1];
            } else {
                $chave = $this->_primary;
            }
        }

        if (empty($valor)) {
            $valor = $this->_namePairs;
        }

        $select = $this->_db
            ->select()
            ->from($this->_name, array($chave, $valor))
            ->order($ordem ? $ordem : $valor);

        if (is_numeric($limit)) {
            $select->limit($limit);
        }

        if ($where) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $select->where($key, $value);
                }
            } else {
                $select->where($where);
            }
        }

        return $this->_db->fetchPairs($select);
    }

    public function inativar($id)
    {
        $row = $this->find($id)->current();

        if (empty($row)) {
            App_Validate_MessageBroker::addErrorMessage('Este dado não pode ser excluído.');
            return false;
        }

        if (isset($row->ativo)) {
            $row->ativo = self::INATIVO;
            $row->save();
            return true;
        } else {
            App_Validate_MessageBroker::addErrorMessage('Este dado nao pode ser inativado.');
        }
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
    public function getAutocomplete($term, $limit = 10, $page = 0, $chave = null, $valor = null,
        $ordem = null, $ativo = false
    ) {
        if (empty($chave)) {
            if (is_array($this->_primary)) {
                $chave = $this->_primary[1];
            } else {
                $chave = $this->_primary;
            }
        }

        if (empty($valor)) {
            $valor = $this->_namePairs;
        }

        $select = $this->_db
            ->select()
            ->from($this->_name, array('value' => $valor, 'id' => $chave, 'label' => $valor))
            ->order($ordem ? $ordem : $valor);

        if (is_numeric($limit)) {
            $select->limit($limit);
        } else {
            $select->limit(1000);
        }

        if ($where) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $select->where($key, $value);
                }
            } else {
                $select->where($where);
            }
        }
        $select->where($valor . ' like "%' . $term . '%"');

        return $this->_db->fetchAll($select);

    }

    /**
     * @param string $campo
     * @param string $value
     * @return boolean true se exister dado repitido na tabela.
     */
    public function stringEquals($campo, $value, $valuePrimary = null)
    {
        $select = $this->_db->select();
        $select->from($this->_name)
            ->where($campo . ' = ?', trim($value));

        if (!empty($valuePrimary)) {
            $select->where($this->getPrimaryName() . " <> ?", $valuePrimary);
        }

        $row = $this->_db->fetchRow($select);

        if (!$row) {
            return false;
        }
        return true;
    }

    /**
     * @desc corrigindo um erro do zend framework aonde nao retorna o nome do primary key
     * @return string
     */
    public function getPrimaryName()
    {
        if (is_array($this->_primary)) {
            return $this->_primary[1];
        }
        return $chave = $this->_primary;
    }

    public function selectPaginator(array $options = null)
    {

        $select = $this->_db->select()->from($this->_name);
//        $this->_searchPaginator($select, $options);
//        $this->_condPaginator($select);
//        var_dump($select->__toString());

        return $select;
    }

    public function fetchPaginator(Zend_Db_Select $select, $options = null)
    {
        $this->_searchPaginator($select, $options);
    }

    protected function _condPaginator(Zend_Db_Select $select)
    {
        $select->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
    }


    public function _searchPaginator(Zend_Db_Select $select, $options = null)
    {
        $translate = "replace(replace(lower(translate(?::text, 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ', 'aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC')), '-', ''), ' ', '')";

        if ((isset($options['search']) && ($options['search']))) {
            $i = 0;
            $after = "";
            foreach ($options['fields'] as $field => $label) {
                $i++;
                if (isset($this->searchFields) && $this->searchFields[$field]) {
                    $field = $this->searchFields[$field];
                }
                if ($i == 1) {
                    $select->where("("  .$this->_translate($field) . " like {$translate}" . $after,
                        "%{$options['search']}%");
                } else {
                    if ($i == count($options['fields'])) {
                        $after = ")";
                    }
                    $select->orWhere($this->_translate($field) . " like {$translate}" . $after,
                        "%{$options['search']}%");
                }
            }
        }

        if ((isset($options['searchId']) && ($options['searchId']))) {
            if ((isset($options['id']) && ($options['id']))) {
                $select->where($this->_primary . " in (?)", $options['id']);
            }
        }

        if (isset($options['searchFields'])) {
            foreach ($options['searchFields'] as $field => $param) {

                if (($param) && (isset($options['fields'][$field]) && isset($options['searchFields'][$field]))) {
                    if (!empty($options['searchFields']) && !empty($options['searchFields'])) {
                        if (isset($this->searchFields) && $this->searchFields[$field]) {
                            $field = $this->searchFields[$field];
                        }
                        $select->where($this->_translate($field) . " like {$translate}", "%{$param}%");
                    }
                }
            }
        }

    }


    /**
     * @param $text
     * @return string
     */
    public static function _translate($text)
    {
        static $from = 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ';
        static $to = 'aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC';
        return sprintf("replace(replace(lower(translate(%s::text, '{$from}', '{$to}')), '-', ''), ' ', '')", $text);
    }


    /**
     *
     */
    public function __destruct() {
        if ($this->transaction == true) {
            $this->db->commit();
        }
    }

    /**
     *
     */
    public function beginTransaction ()
    {
        if ($this->transaction == false) {
            $this->db->beginTransaction();
            $this->transaction = true;
        }
    }

    /**
     *
     */
    public function commit ()
    {
        if ($this->transaction == true) {
            $this->db->commit();
            $this->transaction = false;
        }
    }

    /**
     *
     */
    public function rollBack ()
    {
        if ($this->transaction == true) {
            $this->db->rollback();
            $this->transaction = false;
        }
    }


}
