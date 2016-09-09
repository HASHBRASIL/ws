<?php

$loader = new Twig_Loader_Filesystem(array('includes/templates', 
	                                       'application/modules/default'));
$twig   = new Twig_Environment($loader, array('debug' => true));

$twig->addExtension(new Twig_Extension_Debug());
$twig->addGlobal('session', $_SESSION);