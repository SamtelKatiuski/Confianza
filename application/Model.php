<?php

class Model
{
	//Argumento protegido, que solo podran acceder sus hijos
	protected $_db;

	public function __construct()
	{
		//Se crea una instancia de la base de datos		
		$this->_db = new Database(); 
	}
}