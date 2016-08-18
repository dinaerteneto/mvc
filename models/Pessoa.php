<?php

namespace models;

use classes\AbstractEntity;

class Pessoa extends AbstractEntity {

	public $table = 'Pessoa';

	public $attributes = [
		'id',
		'nome',
		'data_nascimento',
		'rg',
		'cpf'
	];

}