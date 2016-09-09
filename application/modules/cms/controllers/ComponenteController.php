<?php
class Cms_CmsController extends App_Controller_Action_Twig
{
	function testeAction () {
		$this->view->file = 'componente-capa.html.twig';
	    $this->view->data = array('teste' => 'ronaldo');
	}
}