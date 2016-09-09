<?php

	require_once "Model/ItemBiblioteca.php";

	$ib = new ItemBiblioteca();

	return $ib->getFilhosByIdPai("2d0ccf11-486d-420e-b5a7-7f19f962dd54");