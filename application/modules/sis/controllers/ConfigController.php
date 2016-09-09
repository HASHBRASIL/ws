<?php
class Sis_ConfigController extends Zend_Controller_Action
{
    public function indexAction()
    {
    	if (Zend_Auth::getInstance()->getStorage()->read()->root == true){
    		$this->_helper->layout->disableLayout();
    		$this->view->config = Zend_Registry::get('config');
    	}else{
    		exit("Access Denied");
    	}
    }

    public function gerarBarcodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $qtd_inicial   = 8311;
        $qtd_final     = 9210;
        echo "<style type='text/css'>
                body{
                    margin:0;
                    padding:0;
                }
                .quebra{
                    page-break-before: always;
                }
               </style>";
        echo "<div style='margin:0; width:215mm; max-height:278mm; padding:0 2mm 10mm 5mm;'>";
        for ($qtd_inicial; $qtd_inicial <= $qtd_final; $qtd_inicial++) {
            echo "<div style='float:left; width:67mm; height: 25mm; margin-right:3mm; text-align:center; padding-top: 3mm;'>";
            echo "<img  src='http://kilimanjaro.local/sis/config/barcode/text/".str_pad($qtd_inicial, 9, "0", STR_PAD_LEFT) ."/above' >";
            echo "</div>";
            if($qtd_inicial%30 == 0)
                echo "<span class='quebra'></span>";
        }
        echo "</div>";
    }

/*
 * São jorge = #E6C3A1
 * Titanium = #F2BF7C
 * Agcom    = #A6D9ED
 * Dorneles = #D7F7E4
 * Pix = #EDCAEA
 * FM = #F5B3B3
 * next = #D4CCED
 * Primes = #F7F488
 * Cozinha = #CBF27E
 * São Jorge = #EDB993
 */
    public function barcodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $text = $this->getParam('text');

        $barcodeOptions = array('text' => $text, 'backgroundColor'=>'#EDB993');
        $rendererOptions = array('imageType' => 'png');
        $image = Zend_Barcode::render(
        'code39', 'image', $barcodeOptions, $rendererOptions
        );
    }
}