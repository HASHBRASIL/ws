<?php
    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 21/12/15
     * Time: 0:03
     */
class Zend_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
    /**
     * @param $idServico
     * @return bool
     */
    public function isAllowed($idServico)
    {
        $identity = Zend_Auth::getInstance()->getIdentity();

        if (array_key_exists($idServico, $identity->permission[$identity->time['id']])) {
            if ($identity->permission[$identity->time['id']][$idServico] > date('Y-m-d')) {
                // @todo colocar verificação de DATA - dt_expiracao
                return true;
            }
        }

        return false;
    }
}

