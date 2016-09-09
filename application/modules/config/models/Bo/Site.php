<?php
/**
 * @author Fernando Augusto
 * @since  19/05/2016
 */
class Config_Model_Bo_Site extends App_Model_Bo_Abstract
{
    
    const EMANDATO = 2;
    const EMACOM = 15;
    const ELEGIE = 8;
    const HASH = 29;

    const CMS1 = '54.233.101.41';

    private $_host = 'titaniumtech.com.br';
    private $_port = 8443;
    private $_protocol = 'https';
    private $_login = 'admin';
    private $_password = 'titanium2015#$@';
    private $_secretKey;


    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_Grupo();
        parent::__construct();
    }

    public function geraDns($dominio,$dns){
        $cms = SELF::CMS1;
        $request = <<<EOF
<packet>
<dns>
   <add_rec>
      <site-id>$dominio</site-id>
      <type>A</type>
      <host>$dns</host>
      <value>$cms</value>
   </add_rec>
</dns>
</packet>
EOF;

    $this->request($request);
    }

    public function getSiteByIdPaiByAlias($idPai,$alias)
    {
      return $this->_dao->getGrupoByPaiByMetadado($idPai,'cms_alias',$alias);
    }

    /**
     * Perform API request
     *
     * @param string $request
     * @return string
     */
    private function request($request)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "$this->_protocol://$this->_host:$this->_port/enterprise/control/agent.php");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_getHeaders());
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    /**
     * Retrieve list of headers needed for request
     *
     * @return array
     */
    private function _getHeaders()
    {
        $headers = array(
            "Content-Type: text/xml",
            "HTTP_PRETTY_PRINT: TRUE",
        );
        if ($this->_secretKey) {
            $headers[] = "KEY: $this->_secretKey";
        } else {
            $headers[] = "HTTP_AUTH_LOGIN: $this->_login";
            $headers[] = "HTTP_AUTH_PASSWD: $this->_password";
        }
        return $headers;
    }
}