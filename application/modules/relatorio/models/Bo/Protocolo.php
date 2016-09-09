<?php
class Relatorio_Model_Bo_Protocolo extends App_Model_Bo_Abstract{
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Relatorio_Model_Dao_Protocolo();
        parent::__construct();
    }

    public function getRegistros($idProtocolo){
        $protocoloRegistros = $this->_dao->getRegistros($idProtocolo);
        $count = 0;
        $arrayListaRegistro = array();
        if (!isset($protocoloRegistros)){
            App_Validate_MessageBroker::addErrorMessage("ESTA CONSULTA NÃO POSSUI REGISTROS");
            return null;
        }
        else {
                //transforma tudo  num array unico
            foreach ($protocoloRegistros as $key => $registro){
                $arrayListaRegistro[$count] = $registro->id_movimento ;
                $arrayListaRegistro[$count+1] = $registro->item ;
                $arrayListaRegistro[$count+2] = $registro->codigo ;
                $arrayListaRegistro[$count+3] = $registro->quantuni ;
                $arrayListaRegistro[$count+4] = $registro->unidade ;

                $arrayListaRegistro[$count+5] = $registro->quantestoque;
                $arrayListaRegistro[$count+6] = $registro->quantlote;
                $count +=7;
            }
        }
        //pega apenas o id_movimento
        $arrayIdMovimento = array();
        for ($i = 0 ; $i <count($arrayListaRegistro) ; $i++){
            if (!in_array($arrayListaRegistro[$i], $arrayIdMovimento)){
                $arrayIdMovimento['movimento'.$arrayListaRegistro[$i]] = $arrayListaRegistro[$i];
            }
            $i += 6;
        }
        return array($arrayIdMovimento, $arrayListaRegistro);
    }
}