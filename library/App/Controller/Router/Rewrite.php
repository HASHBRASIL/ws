<?php

/**
 * Class App_Controller_Router_Rewrite
 */
class App_Controller_Router_Rewrite extends Zend_Controller_Router_Rewrite
{

    /**
     * Retrieve a named route
     *
     * @param string $name Name of the route
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Router_Route_Interface Route object
     */
    public function getRoute($name)
    {

        if (!isset($this->_routes[$name])) {
            /* BEGIN - DB routes */
            $routes = new Legacy_Model_Bo_Servico();
            $route = $routes->getNamedRoute($name);
            if($route instanceof Zend_Controller_Router_Route_Abstract) {
                $this->addRoute($name, $route);
            }
            /* END - DB routes */
            if (!isset($this->_routes[$name])) {
                require_once 'Zend/Controller/Router/Exception.php';
                throw new Zend_Controller_Router_Exception("Route $name is not defined");
            }
        }

        return $this->_routes[$name];
    }


    /**
     * Find a matching route to the current PATH_INFO and inject
     * returning values to the Request object.
     *
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Request_Abstract Request object
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception('Zend_Controller_Router_Rewrite requires a Zend_Controller_Request_Http-based request object');
        }


//        $this->getRoute($request->getPathInfo());



        if ($this->_useDefaultRoutes) {
            $this->addDefaultRoutes();
        }

        // Find the matching route
        $routeMatched = false;
//        var_dump($this->_routes);

        foreach (array_reverse($this->_routes, true) as $name => $route) {
            // TODO: Should be an interface method. Hack for 1.0 BC
            if (method_exists($route, 'isAbstract') && $route->isAbstract()) {
                continue;
            }

            // TODO: Should be an interface method. Hack for 1.0 BC
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $request->getPathInfo();
            } else {
                $match = $request;
            }

            if ($params = $route->match($match)) {
                $this->_setRequestParams($request, $params);
                $this->_currentRoute = $name;
                $routeMatched        = true;
                break;
            }
        }

        if ($this->_currentRoute == 'home_servico') {
            $routes = new Legacy_Model_Bo_Servico();
            $idServico = $request->getParam('servico');

//            $path = $request->getModuleName()."/".$request->getControllerName()."/".$request->getActionName();
            $rota = $routes->getRouteByServico($idServico);

            if ($rota && $rota->getDefaults('route')) {
                $this->_currentRoute = $rota;
                $routeMatched        = true;
                $this->_setRequestParams($request, $rota->getDefaults());
            } else {
                $request->setPathInfo($request->getPathInfo() . '?servico=' . $idServico);

                $this->_setRequestParams($request, array('servico' => $idServico));
            }

        } else {
            // caso padrão vai cair aqui.
            $routes = new Legacy_Model_Bo_Servico();
            $path = $request->getModuleName()."/".$request->getControllerName()."/".$request->getActionName();
            $rota = $routes->getNamedRoute($path);

            if (!$rota) {
                $rota = $routes->getNamedRoute($request->getPathInfo());
                $this->_currentRoute = $rota;
            }

            if ($rota) {
                $this->_setRequestParams($request, array('servico' => $rota->getDefault('servico')));
                $routeMatched        = true;
            }
        }


        /* BEGIN - DB routes */
//        $front = Zend_Controller_Front::getInstance();
        if (!$routeMatched || ($routeMatched && !Zend_Controller_Front::getInstance()->getDispatcher()->isDispatchable($request))) {
            $routes = new Legacy_Model_Bo_Servico();

//            $this->getRoute($request->getPathInfo());
//
//            $rota = $routes->getNamedRoute($request->getPathInfo());
//
//            if ($rota) {
//                $this->_setRequestParams($request, $params);
//                $this->_currentRoute = $name;
//                $routeMatched        = true;
//            }

//            $dbRoutes = $routes->getRouterRoutes();
//
//            foreach ($dbRoutes as $name => $route) {
//                // TODO: Should be an interface method. Hack for 1.0 BC
//                    if (method_exists($route, 'isAbstract') && $route->isAbstract()) {
//                    continue;
//                }
//
//                // TODO: Should be an interface method. Hack for 1.0 BC
//                if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
//                    $match = $request->getPathInfo();
//                } else {
//                    $match = $request;
//                }
//
//                if ($params = $route->match($match)) {
//                    $this->_setRequestParams($request, $params);
//                    $this->_currentRoute = $name;
//                    $routeMatched        = true;
//                    break;
//                }
//            }
        }
        /* END - DB routes */

        if(!$routeMatched) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception('No route matched the request', 404);
        }

        if($this->_useCurrentParamsAsGlobal) {
            $params = $request->getParams();
            foreach($params as $param => $value) {
                $this->setGlobalParam($param, $value);
            }
        }

        return $request;

    }

}