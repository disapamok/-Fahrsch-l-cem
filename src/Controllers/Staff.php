<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;
use Simcify\Mail;

class Staff{
    /**
     * Get staff view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        $branches =  Database::table('branches')->where('school',$user->school)->get();
        if (isset($_GET['search']) OR isset($_GET['gender'])) {
            if (!empty($_GET['gender']) && !empty($_GET['search'])) {
                $staffs = Database::table('users')->where(array(
                    'role' => 'staff',
                    'school' => $user->school,
                    'gender' => $_GET['gender']
                ))->orWhere("fname", "LIKE", "%" . $_GET['search'] . "%")->where(array(
                    'role' => 'staff',
                    'branch' => $user->branch
                ))->get();
            } elseif (!empty($_GET['gender'])) {
                $staffs = Database::table('users')->where(array(
                    'role' => 'staff',
                    'school' => $user->school,
                    'gender' => $_GET['gender']
                ))->get();
            } elseif (!empty($_GET['search'])) {
                $staffs = Database::table('users')->where(array(
                    'role' => 'staff',
                    'school' => $user->school
                ))->where("fname", "LIKE", "%" . $_GET['search'] . "%")->get();
            } else {
                $staffs = Database::table('staffs')->where(array(
                    'role' => 'staff',
                    'school' => $user->school
                ))->get();
            }
        } else {
            $staffs = Database::table('users')->where(array(
                'role' => 'staff',
                'school' => $user->school
            ))->get();
        }
        foreach($staffs as $staff){
            $branch =  Database::table('branches')->where('id',$staff->branch)->first();
            $staff->branchname = $branch->name;
        }

        return view('staff', compact("user", "branches", "staffs"));
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
            "phone" => escape(input('phone')),
            "gender" => escape(input('gender')),
            "permissions" => escape(input('permissions')),
            "branch" => escape(input('branch')),
            "password" => Auth::password($password),
            "school" => $user->school,
            "role" => 'staff'
        ), array(
            "authenticate" => false,
            "uniqueEmail" => escape(input('email'))
        ));
        
        
        if ($signup["status"] == "success") {
            Mail::send(input('email'), sch_translate("welcome_to_app"), array(
                "title" => sch_translate("welcome_to_app"),
                "subtitle" => sch_translate("your_school_has_created_a_staff_acct_for_you"),
                "buttonText" => sch_translate("login_now"),
                "buttonLink" => env("APP_URL"),
                "message" => sch_translate("these_are_your_login_credentials", [input('email'), $password])
            ), "withbutton");
            return response()->json(responder("success", sch_translate("account_created"), sch_translate("instructor_account_successfully_created"), "reload()"));
        }
        
    }
}