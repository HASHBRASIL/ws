<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 14/12/15
 * Time: 21:26
 */
class Legacy_Model_Bo_Servico extends App_Model_Bo_Abstract
{

    protected $_name = 'tb_servico';
    protected $_rowClass = 'Legacy_Model_Vo_Servico';

    public static $primaryColumn = 'id';
    public static $routeColumn = 'rota';

    protected $_iterator = '';



    /**
     * Legacy_Model_Bo_Servico constructor.
     */
    public function __construct()
    {
        $this->_dao = new Legacy_Model_Dao_Servico();
        parent::__construct();
    }

    public function getRouterRoutes() {
        $routes = array();
        $rowset = $this->_dao->fetchAll();

        foreach($rowset as $row) {
            $routes[$row->id] = $row->getRouteObject();
        }
        return $routes;
    }

    public function getNamedRoute($name) {
        $rowset = $this->find(array(self::$routeColumn . ' = ?' => trim($name, '/')));

        foreach($rowset as $row) {
            return $row->getRouteObject();
        }
    }

    public function getRouteByServico($idServico)
    {
        $rowset = $this->find(array(self::$primaryColumn . ' = ?' => $idServico));

        foreach($rowset as $row) {
            return $row->getRouteObject();
        }

        return false;
    }

    function recursiveArrayCreate(&$array, $pai, $id) {
        foreach($array as $key => $value) {
            if ($key == $pai) {
                $array[$key][$id] = array();
            } elseif (is_array($value) && (count($value) > 0)) {
                $this->recursiveArrayCreate($array[$key], $pai, $id);
            }
        }
    }

    // @todo verificar se a query funciona => titanic de noé
    // pega todos os servicos do sistema
    public function getAllServicesAsIterator()
    {
        $rs = $this->_dao->getAllServices();

        $this->_iterator = array();

        $result = array();
        foreach ($rs as $key => $row) {
            $metadatas = json_decode($row['metadatas']);
            $valores = json_decode($row['valor']);
            $row['tab'] = false;

            foreach ($metadatas as $key => $metadata) {
                $row[$metadata] = $valores[$key];
                $row['metadata'][$metadata] = $valores[$key];
            }

            if (empty($row['metadata']['ws_arquivo'])) {
                switch ($row['fluxo']) {
                    case "editar":
                        $row['metadata']['ws_arquivo'] = "editDataMaster.php";
                        break;

                    case "criar":
                        $row['metadata']['ws_arquivo'] = "createDataMaster.php";
                        break;

                    default:
                        $row['metadata']['ws_arquivo'] = "master.php";
                }
            }

            $result['data'][$row['id']] = $row;

            if (!$row['id_pai']) {
                $this->_iterator[$row['id']] = array();
            } else {
                $this->recursiveArrayCreate($this->_iterator, $row['id_pai'], $row['id']);
                $result['data'][$row['id_pai']]['filhos'][$row['id']] = $row;

                if (isset($row['metadata']['ws_comportamento']) && ($row['metadata']['ws_comportamento'] == 'tab')) {
                    $result['data'][$row['id_pai']]['tab'] = true;
                }

            }
        }

        $result['iterator'] = $this->_iterator;

        return $result;
    }


}

