<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 14/12/15
 * Time: 21:52
 */
class Legacy_Model_Vo_Servico extends App_Model_Vo_Row
{

    public function getDefaults() {

        $servicoMetadata = new Legacy_Model_Bo_ServicoMetadata();

        $rs = $servicoMetadata->find(array('id_servico = ?' => $this->id));

        foreach ($rs as $row) {
            $defaults[substr($row->metanome, 3)] = $row->valor;
        }

        if ($this->{Legacy_Model_Bo_Servico::$routeColumn}) {
            $routePath = explode('/', $this->{Legacy_Model_Bo_Servico::$routeColumn});

            if ($routePath) {
                $defaults['module'] = $routePath[0];
                $defaults['controller'] = $routePath[1];
                $defaults['action'] = $routePath[2];
            }
        }

        $defaults['route'] = Legacy_Model_Bo_Servico::$routeColumn;

        $defaults['servico'] = $this->id;


        return $defaults;
    }

    public function getReqs() {
        $reqs = $this->{Legacy_Model_Bo_Servico::$reqsColumnn};

        if($reqs) {
            $reqs = Zend_Json::decode($reqs);
        } else {
            $reqs = array();
        }
        return $reqs;
    }

    public function getModule() {
        if(empty($this->{Legacy_Model_Bo_Servico::$moduleColumn})) {
            return "default";
        } else {
            return $this->{Legacy_Model_Bo_Servico::$moduleColumn};
        }
    }
    public function getController() {
        if(empty($this->{Legacy_Model_Bo_Servico::$controllerColumnn})) {
            return "default";
        } else {
            return $this->{Legacy_Model_Bo_Servico::$controllerColumnn};
        }
    }
    public function getAction() {
        if(empty($this->{Legacy_Model_Bo_Servico::$actionColumnn})) {
            return "default";
        } else {
            return $this->{Legacy_Model_Bo_Servico::$actionColumnn};
        }
    }
    public function getRouteObject() {

//        $reqs = array('servico' => $this->id);

        $defaults = $this->getDefaults();
        $route = new Zend_Controller_Router_Route($this->rota, $defaults);
        return $route;
    }
}
