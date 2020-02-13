<?php
namespace Simcify\Controllers;

use Simcify\Auth as Authenticate;
use Simcify\Database;

class Auth {
    
    /**
     * Get login view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        if (!isset($_GET['secure'])) {
            redirect(url("Auth@get") . "?secure=true");
        }
        return view('login');
    }
    
    
    /**
     * Sign In a user
     * 
     * @return Json
     */
    public function signin() {

        $signin = Authenticate::login(input('email'), input('password'), array(
            "rememberme" => true,
            "redirect" => url('Dashboard@get'),
            "status" => "Active"
        ));
        return response()->json($signin);
    }
    
    /**
     * Create an account
     * 
     * @return Json
     */
    public function signup() {
        $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'), input('email'))->first();
        if (!empty($user)) {
            return response()->json(array(
                "status" => "error",
                "title" => sch_translate("email_already_exist"),
                "message" => sch_translate("email_already_exist")
            ));
        }
        
        $schoolData = array(
            "name" => input('school'),
            "email" => input('email')
        );
        Database::table("schools")->insert($schoolData);
        $schoolId = Database::table("schools")->insertId();
        
        $branchData = array(
            "name" => "Headquarters",
            "school" => $schoolId,
            "email" => input('email')
        );
        Database::table("branches")->insert($branchData);
        $branchId = Database::table("branches")->insertId();
        
        
        $signup = Authenticate::signup(array(
            "fname" => input('fname'),
            "lname" => input('lname'),
            "username" => input('lname').'_'.Str::random(4),
            "email" => input('email'),
            "password" => Authenticate::password(input('password')),
            "school" => $schoolId,
            "branch" => $branchId,
            "role" => 'admin'
        ), array(
            "authenticate" => true,
            "redirect" => url('Dashboard@get'),
            "uniqueEmail" => input('email')
        ));
        
        return response()->json($signup);
        
    }
    
    /**
     * Forgot password - send reset password email
     * 
     * @return Json
     */
    public function forgot() {
        $forgot = Authenticate::forgot(input('email'), env('APP_URL') . "/reset/[token]");
        return response()->json($forgot);
    }
    
    /**
     * Get reset password view
     * 
     * @return \Pecee\Http\Response
     */
    public function resetview($token) {
        return view('reset', array(
            "token" => $token
        ));
    }
    
    /**
     * Reset password
     * 
     * @return Json
     */
    public function reset() {
        $reset = Authenticate::reset(input('token'), input('password'));
        
        return response()->json($reset);
    }
    
    
    /**
     * Sign Out a logged in user
     *
     */
    public function signout() {
        Authenticate::deauthenticate();
        redirect(url("Auth@get"));
        
    }
    
}