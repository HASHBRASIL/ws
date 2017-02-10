<?php
/**
 * HashWS
 */

/**
 * Esse helper carrega conteúdos do public.
 *
 * Basta indicar o arquivo, que o conteúdo dele é retornado em forma de string.
 *
 * @author Maykel S. Braz
 */
class Controller_Conteudo extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Retorna o conteúdo de um arquivo que esteja no public.
     *
     * Valida alguns padrões de alteração de caminho e, se estiver tudo certo,
     * lê o arquivo indicado e retorna seu conteúdo no formato string.
     *
     * @param string $path Caminho do arquivo para leitura.
     * @return string Conteúdo do arquivo indicado em $path.
     * @throws Exception Caminhos inválidos ou arquivo inexistente.
     * @example
     * <code>
     * // -- Chamando de uma controller
     * $this->_helper->conteudo('transacional/001/ativacao.html')
     * </code>
     */
    public function getConteudo($path)
    {
        if (strstr($path, '..') || strstr($path, '&#46;&#46;')) {
            throw new Exception('Caminho inválido.');
        }
        if (0 === strpos($path, '/')) {
            throw new Exception('O caminho do conteúdo deve ser relativo.');
        }
        if (!is_file($path)) {
            throw new Exception('Não foi encontrado um arquivo no caminho informado.');
        }

        return file_get_contents($path);
    }

    /**
     * Retorna o conteúdo de um arquivo. Para maiores informações verifique getConteudo().
     *
     * @param string $path Caminho do arquivo para leitura.
     * @return string Conteúdo do arquivo indicado em $path.
     * @return type
     */
    public function direct($path)
    {
        return $this->getConteudo($path);
    }
}
