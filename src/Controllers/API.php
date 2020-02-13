<?php
namespace Simcify\Controllers;

use Simcify\Auth as Authenticate;
use Simcify\Database;

/**
 * 
 */
class API{
	
	// function __construct(argument)
	// {
	// 	# code...
	// }

	function fetchUser(){
		echo 'sahan';  exit();

		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON);


		$signin = Authenticate::login($input->email, $input->password, array(
            "rememberme" => true,
            "redirect" => url('Dashboard@get'),
            "status" => "Active"
        ));

        if($signin['status'] == 'error'){
        	echo "error ";
        }

		var_dump($signin);
	}
}