<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;
use Simcify\Mail;

class Instructor {
    /**
     * Get instructors view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {

        $user    = Auth::user();
        $search  = $gender = '';
        $courses = Database::table('courses')->where('school', $user->school)->get();

        if (isset($_POST['search']) OR isset($_POST['gender'])) {

            $search  = $_POST['search'];
            $gender  = $_POST['gender'];

            $search_s = explode(" ", $search);
            $str_fname_temp = '';
            $final_fname = '';
            $final_lname = '';
            
            $ctr = 0;
            if($search){
                
                for ($i=0; $i < count($search_s); $i++) {
                    $search_s_temp = '';
                    for ($ii=0; $ii < count($search_s) - $ctr; $ii++) { 
                        $search_s_temp .= $search_s[$ii]." ";
                    }
                    $ctr++;
                    // echo $search_s_temp;
                    // echo "<br/>";
                    // die;
                    $return = Database::table('users')->where(array(
                        'role' => 'instructor',
                        'school' => $user->school,
                        'branch' => $user->branch
                    ))->where('fname', trim($search_s_temp))->first();

                    if($return){
                        $str_fname_temp = $search_s_temp; // Store string as fname
                        break;
                    }

                }
                $final_fname = trim($str_fname_temp);
                $final_lname = trim(substr_replace($search,"",0,strlen($final_fname)));
                // echo "Firstname: ". $final_fname;
                // echo "<br>";
                // echo "Lastname: ". $final_lname;
            }
            // die;

            if($final_fname !== '' && $final_lname !=='' && $gender == 'all'){
                
                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch
                ))->where("fname", "LIKE", "%" . $final_fname . "%")
                ->where("lname", "LIKE", "%" . $final_lname . "%")
                ->get();
            }
            elseif($final_fname !== '' && $final_lname !== '' && $gender != 'all'){
                
                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch,
                    'gender' => $gender,
                ))->where("fname", "LIKE", "%" . $final_fname . "%")
                ->where("lname", "LIKE", "%" . $final_lname . "%")
                ->get();
            }
            elseif($final_fname !== '' && $gender == 'all'){
                
                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch
                ))->where("fname", "LIKE", "%" . $final_fname . "%")
                ->get();
            }
            elseif($final_lname !== '' && $gender == 'all'){

                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch
                ))->where("lname", "LIKE", "%" . $final_lname . "%")
                ->get();
            }
            elseif($final_fname !== '' && $gender != 'all'){
                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch,
                    'gender' => $gender,
                ))->where("fname", "LIKE", "%" . $final_fname . "%")
                ->get();
            }
            elseif($final_lname !== '' && $gender != 'all'){
                
                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch,
                    'gender' => $gender,
                ))->where("lname", "LIKE", "%" . $final_lname . "%")
                ->get();
            }
            elseif($final_lname == '' && $final_fname == '' && $gender != 'all'){
                
                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch,
                    'gender' => $gender,
                ))
                ->get();
            }
            elseif($search !== ''){
                
                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch
                ))->where("fname", "LIKE", "%" . $search . "%")
                ->orWhere("lname", "LIKE", "%" . $search . "%")
                ->get();
            }
            else{

                $instructors = Database::table('users')->where(array(
                    'role' => 'instructor',
                    'school' => $user->school,
                    'branch' => $user->branch
                ))->get();
            }

            // if (!empty($_POST['gender']) && !empty($_POST['search'])) {
                
            //     $instructors = Database::table('users')->where(array(
            //         'role' => 'instructor',
            //         'school' => $user->school,
            //         'branch' => $user->branch,
            //         'gender' => $_POST['gender']
            //     ))->orWhere("fname", "LIKE", "%" . $_POST['search'] . "%")->where(array(
            //         'role' => 'instructor',
            //         'school' => $user->school,
            //         'branch' => $user->branch
            //     ))->get();

            // }elseif (!empty($_POST['gender']) && $_POST['gender'] == 'all') {
            //     $instructors = Database::table('users')->where(array(
            //         'role' => 'instructor',
            //         'school' => $user->school,
            //         'branch' => $user->branch
            //     ))->get();
            // } elseif (!empty($_POST['gender'])) {
            //     $instructors = Database::table('users')->where(array(
            //         'role' => 'instructor',
            //         'school' => $user->school,
            //         'branch' => $user->branch,
            //         'gender' => $_POST['gender']
            //     ))->get();
            // } elseif (!empty($_POST['search'])) {
            //     $instructors = Database::table('users')->where(array(
            //         'role' => 'instructor',
            //         'school' => $user->school,
            //         'branch' => $user->branch
            //     ))->where("fname", "LIKE", "%" . $_POST['search'] . "%")->get();
            // } else {
               
            //     $instructors = Database::table('users')->where(array(
            //         'role' => 'instructor',
            //         'school' => $user->school,
            //         'branch' => $user->branch
            //     ))->get();
            // }

        } else {

            // get user if empty request
            $instructors = Database::table('users')->where(array(
                'role' => 'instructor',
                'school' => $user->school,
                'branch' => $user->branch
            ))->get();
            
        }
        
        foreach ($instructors as $instructor) {
            $instructor->courses = Database::table('courseinstructor')->where('instructor', $instructor->id)->count("id", "total")[0]->total;
            $instructor->completed = Database::table('schedules')->where('instructor', $instructor->id)->where('status', "Complete")->count("id", "total")[0]->total;
        }

        return view('instructors', compact("user", "instructors", "courses", "search", "gender"));
    }
    
    
    /**
     * Create Instructor Account
     * 
     * @return Json
     */
    public function create() {
        $user = Database::table("users")->where("email", input('email'))->first();
 
        if (!empty($user)) {
            return response()->json(array(
                "status" => "error",
                "title" => sch_translate("email_already_exist"),
                "message" => sch_translate("email_already_exist")
            ));
        }
        $user     = Auth::user();
        $password = rand(111111, 999999);
        $signup   = Auth::signup(array(
            "fname" => escape(input('fname')),
            "lname" => escape(input('lname')),
            "email" => escape(input('email')),
            "phone" => "+".escape(input('phone')),
            "gender" => escape(input('gender')),
            "password" => Auth::password($password),
            "school" => $user->school,
            "branch" => $user->branch,
            "role" => 'instructor'
        ), array(
            "authenticate" => false,
            "uniqueEmail" => escape(input('email'))
        ));

        if ($signup["status"] == "success") {
            Mail::send(input('email'), sch_translate("welcome_to_app"), array(
                "title" => sch_translate("welcome_to_app"),
                "subtitle" => sch_translate("your_school_has_created_an_instructor_acct_for_you"),
                "buttonText" => sch_translate("login_now"),
                "buttonLink" => env("APP_URL"),
                "message" => sch_translate("these_are_your_login_credentials", [input('email'), $password])
            ), "withbutton");
            return response()->json(responder("success", sch_translate("account_created"), sch_translate("instructor_account_successfully_created"), "reload()"));
        }
        
    }
    
}

$query = "SELECT * FROM `users` WHERE `role` = 'instructor' AND `school` = $user->school AND `branch` = $user->branch";

        if (isset($_POST['search']) || isset($_POST['gender'])) {

            $search  = strtolower($_POST['search']);
            $gender  = strtolower($_POST['gender']);

            if($gender != 'all' ){
                $query .= "AND LOWER(`gender`) = LOWER($gender)"; 
            }

            // if($search){
            //     $query .= " AND (LOWER(`fname`) LIKE LOWER('%".$search."%') OR LOWER(`lname`) LIKE LOWER('%".$search."%') OR CONCAT(LOWER(`fname`), ' ',LOWER(`lname`)) LIKE LOWER('%".$search."%'))";
            // }

        }
        
        $instructors = Database::table('users')->getResults($query);