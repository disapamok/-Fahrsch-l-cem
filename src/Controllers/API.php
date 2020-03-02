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
            "status" => "Active"
        ));

        if($signin['status'] == 'success'){
        	$userRow = Database::table('users')->where('email', $input->email)->orderBy('id', true)->first();
        	
        	$user = array(
                'id' => $userRow->id,
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

	function getStudentInstructor(){
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON);

		//$input->student_id

		//Database::table('userinfo')->where('student','', $instructor)->first();
		return json_encode(array(
			'error' => true,
			'message' => 'This API is still under development. Please contact Sahan.'
		));
	}

    function getUser(){
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON);

        if(property_exists($input,"user_id")){
            $userRow = Database::table("users")->where('id',$input->user_id)->first();
            $user = array(
                'fname' => $userRow->fname,
                'lname' => $userRow->lname,
                'username' => $userRow->username,
                'email' => $userRow->email,
                'gender' => $userRow->gender,
                'phone'  => $userRow->phone,
                'address' => $userRow->address,
                'branch' => $userRow->branch,
                'date_of_birth' => $userRow->date_of_birth,
                'phone' => $userRow->phone,
                'student_type' => $userRow->student_type,
                'avatar' => $userRow->avatar,
                'role' => $userRow->role,
                'status' => $userRow->status,
                'language' => $userRow->lang,
                'created_at' => $userRow->created_at
            );

            if($userRow->role == 'instructor'){
                $instructor_students = array();
                $students = Database::table('instructor_students')->where('instructor_id',$input->user_id)->where('active','1')->get();

                foreach ($students as $student) 
                    array_push($instructor_students, $student->student_id);
                
                $query = "SELECT id,fname,lname,username,email FROM `users` WHERE `id` IN (".implode(",", $instructor_students).")";
                $students = Database::table('users')->getResults($query);
                $user['students'] = $students;

            }else if($userRow->role == 'student'){
                $stdInstructor = Database::table('instructor_students')->where('student_id',$input->user_id)->first()->instructor_id;

                $qry = "SELECT id,fname,lname,username,email FROM `users` WHERE `id`=".$stdInstructor."";
                $user['instructor'] = Database::table('users')->getResults($qry);
            }

            return json_encode(array(
                'error' => fasle,
                'message' => 'User data',
                'data' => $user
            ));
        }else{
            return json_encode(array(
                'error' => true,
                'message' => "Request has no parameter named 'user_id'"
            ));
        }
    }

    function updateUser(){
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON);

        if(property_exists($input,"id")){
            $user = Database::table("users")->where("id", $input->id);
            if (empty($user)) {
                return response()->json(array(
                    "error" => true,
                    "message" => 'No user belongs to this user id:'.$input->id,
                )); 
            }else{
                $data = array(
                    'fname' => $input->fname,
                    'lname' => $input->lname,
                    'username' => $input->username,
                    'gender' => $input->gender,
                    'phone' => $input->phone,
                    'address' => $input->address,
                    'date_of_birth' => $input->date_of_birth
                );
                $user->update($data);

                return response()->json(array(
                    "error" => false,
                    "message" => 'User has been updated.',
                )); 
            }
        }else{
            return response()->json(array(
                "error" => true,
                "message" => 'user id is required',
            )); 
        }
    }
}