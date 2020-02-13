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

		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON);


		$signin = Authenticate::login($input->email, $input->password, array(
            "rememberme" => true,
            "redirect" => url('Dashboard@get'),
            "status" => "Active"
        ));

        if($signin['status'] == 'success'){
        	$userRow = Database::table('users')->where('email', $input->email)->orderBy('id', true)->first();
        	
        	$user = array(
        		'fname' => $userRow->fname,
        		'lname' => $userRow->lname,
        		'username' => $userRow->username,
        		'email' => $userRow->email,
        		'gender' => $userRow->gender,
        		'date_of_birth' => $userRow->date_of_birth,
        		'phone' => $userRow->phone,
        		'student_type' => $userRow->student_type,
        		'avatar' => $userRow->avatar,
        		'role' => $userRow->role,
        		'created_at' => $userRow->created_at

        	);
        	$signin['user'] = $user;
        }
        return json_encode($signin);
	}

	function test(){
		echo 'test';
	}
}