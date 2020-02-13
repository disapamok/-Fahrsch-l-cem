<?php 
namespace Simcify\Controllers;


class Extensions{

	public function get(){

	}

	public function error404(){
		response()->httpCode(404);
		echo view('errors/404');		
	}

	public function error405(){
		response()->httpCode(405);
		echo view('errors/405');
	}

	// public function 


}