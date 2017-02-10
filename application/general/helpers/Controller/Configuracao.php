<?php
/**
 * HashWS
 */

/**
 * Esse helper consulta o application.ini e retorna o valor das configurações solicitadas.
 *
 * A quantidade de parâmetros recebidos é variável, mas o primeiro é obrigatório.
 * Cada um dos parâmetros avança um nível dentro daquela configuração.
 *
 * @author Maykel S. Braz
 */
class Controller_Configuracao extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Retorna o valor de uma configuração do application.ini.
     *
     * Percorre uma árvore de configuração retornando o valor da última opção
     * informada. Para cada nível adicional, informe o nome da configuração
     * filha desejada.
     *
     * @param string $opcao Primeiro nível de configurações é obrigatório.
     * @return string|string[]
     * @example
     * <code>
     * // -- Retorna o valor da configuração "site" filha de "filedir"
     * $this->_helper->configuracao('filedir', 'site');
     * </code>
     * @example
     * <code>
     * // -- Retorna o todas as configurações filhas de "filedir"
     * $this->_helper->configuracao('filedir');
     * </code>
     */
    public function getValorConfiguracao($opcao)
    {
        $opcoes = func_get_args();
        $configuracoes = Zend_Controller_Front::getInstance()->getParam('bootstrap')
            ->getOption(array_shift($opcoes));

        foreach ($opcoes as $opc) {
            $configuracoes = $configuracoes[$opc];
        }

        return $configuracoes;
    }

    /**
     * Atalho de chamada.
     *
     * Veja a documentação do método Controller_Configuracao::getValorConfiguracao().
     *
     * @param string $opcao Primeiro nível de configurações é obrigatório.
     * @return string|string[]
     */
    public function direct($opcao)
    {
        return call_user_func_array(
            [$this, 'getValorConfiguracao'],
            func_get_args()
        );
    }
}
