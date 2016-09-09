<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  30/04/2013
 */
class Material_Model_Bo_Nfe extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Nfe
     */
    protected $_dao;

    // tipo de frete
    const SEM_FRETE       = 0;
    const EMITENTE        = 1;
    const DESTINATARIO    = 2;
    const EMIT_DESTIN     = 3;
    const DEST_REMETE     = 4;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Nfe();
        parent::__construct();
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->natureza_operacao)){
            App_Validate_MessageBroker::addErrorMessage("O campo natureza da operação está vazio.");
            return false;
        }

        if(empty($object->num_danfe)){
            App_Validate_MessageBroker::addErrorMessage("O campo N° DANFE está vazio.");
            return false;
        }

        if(empty($object->num_serie) && $object->num_serie != 0){
            App_Validate_MessageBroker::addErrorMessage("O campo N° Série está vazio.");
            return false;
        }

        if(empty($object->dt_emissao)){
            App_Validate_MessageBroker::addErrorMessage("O campo data da emissão está vazio.");
            return false;
        }
        if(!empty($object->num_danfe)){
            $criteria = array(
                    'num_danfe = ?'       => $object->num_danfe,
                    'ativo = ? '          => App_Model_Dao_Abstract::ATIVO,
                    'id_nfe <> ?'         => $object->id_nfe ? $object->id_nfe : 0,
                    'num_serie <> ?'      => $object->num_serie ? $object->num_serie : 0,
                    'id_fornecedor <> ?'  => $object->id_fornecedor ? $object->id_fornecedor : 0,
            );
            $nfe = $this->find($criteria);
            if(count($nfe)){
               App_Validate_MessageBroker::addErrorMessage("O número DANFE já existe.");
               return false;
            }
        }

        $validator = new Zend_Validate_Date(array('format'=>'dd/MM/yyyy'));
        if(!$validator->isValid($object->dt_emissao)){
            $object->dt_emissao = null;
            App_Validate_MessageBroker::addErrorMessage("A data de emissão está incorreta.");
            return false;
        }
        if(!empty($object->dt_saida) && !$validator->isValid($object->dt_saida)){
            $object->dt_saida = null;
            App_Validate_MessageBroker::addErrorMessage("A data de saída está incorreta.");
            return false;
        }
        if(!empty($object->dt_entrada) && !$validator->isValid($object->dt_entrada)){
            $object->dt_entrada = null;
            App_Validate_MessageBroker::addErrorMessage("A data de entrada está incorreta.");
            return false;
        }

        $validator->isValid('2000-10-10');
        return true;
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(!empty($object->dt_emissao)){
            $dt_emissao         = new Zend_Date($object->dt_emissao);
            $object->dt_emissao = $dt_emissao->toString('yyyy-MM-dd');
        }

        if(!empty($object->dt_saida)){
            $dt_saida         = new Zend_Date($object->dt_saida);
            $object->dt_saida = $dt_saida->toString('yyyy-MM-dd');
        }

        if(!empty($object->dt_entrada)){
            $dt_entrada = new Zend_Date($object->dt_entrada);
            $object->dt_entrada = $dt_entrada->toString('yyyy-MM-dd');
        }

        $object->tl_produto      = $this->_formatDecimal($object->tl_produto);
        $object->tl_nota         = $this->_formatDecimal($object->tl_nota);

    }

    public function getIdFailTotal(){
        $criteria = array('ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        $listNfe  = $this->find($criteria);
        $listIdFail = array();
        if(count($listNfe)){
            foreach ($listNfe as $nfe){
                $totalItem = 0;
                if(count($nfe->getItemList())){
                    foreach ($nfe->getItemList() as $item){
                        $totalItem += $item->vl_total;
                    }
                }
                if(!(bccomp(floatval($nfe->tl_produto), $totalItem) == 0)){
                    $listIdFail[] = $nfe->id_nfe;
                }
            }
        }

        return $listIdFail;

    }

    public function equalsTlProduto($tlProduto, $idNfe)
    {
        $movBo = new Material_Model_Bo_Movimento();
        $movList  = $movBo->find(array(
                'id_nfe = ?' => $idNfe
        ));
        $vlTotal = 0;
        foreach ($movList as $mov){
            if(count($mov->getListMovimento())){
                $vlTotal += $mov->getListMovimento()->current()->getEstoque()->vl_total;
            }
        }
        if(!(bccomp(floatval($tlProduto), $vlTotal) == 0)){
            return false;
        }
        return true;
    }

}