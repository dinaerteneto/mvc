<?php

namespace controllers;

use classes\AbstractController;
use models\Pessoa;

class PessoaController extends AbstractController {

	public function index($sParam) {
		$oPessoa = new Pessoa();
		$aPessoa = $oPessoa->select([]);
		$this->render('index', ['aData' => $aPessoa]);
	}

}