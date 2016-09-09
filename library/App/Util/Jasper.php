<?php
class App_Util_Jasper{

    /**
     * @var PHPJasperXML
     */
    private $_jasper;

    public function __construct($caminhoXml, $params = null)
    {
        if($caminhoXml instanceof SimpleXMLElement){
            $xml = $caminhoXml;
        }else{
            $xml = simplexml_load_file($caminhoXml);
        }
        $this->_jasper = new PHPJasperXML();
        if($params){
            $this->_jasper->arrayParameter    = $params;
        }
        $this->removeSubreportXml($xml, $params);
        $this->_jasper->xml_dismantle($xml);

        $this->connect();

    }

    private function connect()
    {
        $config    = Zend_Registry::get('config');
        $server    = $config->resources->db->params->host;
        $user      = $config->resources->db->params->username;
        $pass      = $config->resources->db->params->password;
        $db        = $config->resources->db->params->dbname;

        if($config->resources->db->params->port){
            $server = $server.':'.$config->resources->db->params->port;
        }
        $this->_jasper->transferDBtoArray($server,$user,$pass,$db);
    }

    public function getJasper()
    {
        return $this->_jasper;
    }

    public function abrir($tipo = "I")
    {
        $this->_jasper->outpage($tipo);
        exit();
    }

    /**
     * @desc Remove o nó do XML quando o sub-relatório possuir um printWhenExpression true or false
     * não foi possivel fazer pelo ireport
     * @param SimpleXMLElement  $caminhoXml
     * @param string|array $params
     */
    public function removeSubreportXml($xml, $params)
    {

        $count = 0;
        foreach ( $xml->detail->band as $key=> $band){
            $printWhenExpression = str_replace(array('$P', '{', '}'), '', $band->printWhenExpression->__toString());
            if(!empty($printWhenExpression) && isset($params[$printWhenExpression]) && !$params[$printWhenExpression]){
                $xml->detail->band[$count] = null;
            }
            $count++;
        }
    }
}