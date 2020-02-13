<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;
use Simcify\Sms;
use Simcify\Mail;
use Simcify\File;

class School {
    
    
    /**
     * Get schools view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->role != 'superadmin') {
            return view('errors/404');
        }
        $schools = Database::table('schools')->where('id', '>', 1)->orderBy("id", false)->get();
        foreach ($schools as $school) {
            $school->branches    = Database::table('branches')->where('school', $school->id)->count("id", "total")[0]->total;
            $school->instructors = Database::table('users')->where(array(
                'school' => $school->id,
                'role' => 'instructor'
            ))->count("id", "total")[0]->total;
            $school->students    = Database::table('users')->where(array(
                'school' => $school->id,
                'role' => 'student'
            ))->count("id", "total")[0]->total;
        }
        return view('schools', compact("user", "schools"));
    }
    
    /**
     * Create School Account
     * 
     * @return Json
     */
    public function create() {
        $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'), input('email'))->first();
        if (!empty($user)) {
            return response()->json(array(
                "status" => "error",
                "title" => sch_translate("email_already_exist"),
                "message" => sch_translate("email_already_exist")
            ));
        }
        
        $schoolData = array(
            "name" => escape(input('schoolname')),
            "phone" => escape(input('phone')),
            "address" => escape(input('address')),
            "email" => escape(input('email'))
        );
        Database::table("schools")->insert($schoolData);
        $schoolId = Database::table("schools")->insertId();
        
        $branchData = array(
            "name" => sch_translate("headquarters"),
            "school" => $schoolId,
            "phone" => escape(input('phone')),
            "email" => escape(input('email'))
        );
        Database::table("branches")->insert($branchData);
        $branchId = Database::table("branches")->insertId();
        
        $password = rand(111111, 999999);
        $signup   = Auth::signup(array(
            "fname" => escape(input('fname')),
            "lname" => escape(input('lname')),
            "email" => escape(input('email')),
            "phone" => escape(input('phone')),
            "password" => Auth::password($password),
            "school" => $schoolId,
            "branch" => $branchId,
            "role" => 'admin'
        ), array(
            "authenticate" => false,
            "uniqueEmail" => escape(input('email'))
        ));
        
        
        if ($signup["status"] == "success") {
            Mail::send(input('email'), sch_translate("welcome_to_app"), array(
                "title" => sch_translate("welcome_to_app"),
                "subtitle" => sch_translate("new_acct_created_for_you"),
                "buttonText" => sch_translate("login_now"),
                "buttonLink" => env("APP_URL"),
                "message" => sch_translate("these_are_your_login_credentials", [input('email'), $password])
            ), "withbutton");
            return response()->json(responder("success", sch_translate("school_created"), sch_translate("school_account_successfully_created"), "reload()"));
        }
        
    }
    
    /**
     * Delete school account
     * 
     * @return Json
     */
    public function delete() {
        $users = Database::table("users")->where("school", input("schoolid"))->get();
        foreach ($users as $user) {
            if (!empty($user->avatar)) {
                File::delete($user->avatar, "avatar");
            }
        }
        Database::table("schools")->where("id", input("schoolid"))->delete();
        return response()->json(responder("success", sch_translate("school_deleted"), sch_translate("school_account_successfully_deleted"), "reload()"));
    }
    
    /**
     * School update view
     * 
     * @return Json
     */
    public function updateview() {
        $school = Database::table("schools")->where("id", input("schoolid"))->first();
        return view('extras/updateschool', compact("school"));
    }
    
    /**
     * Update School
     * 
     * @return Json
     */
    public function update() {
        $data = array(
            "name" => escape(input("schoolname")),
            "phone" => escape(input("phone")),
            "email" => escape(input("email")),
            "address" => escape(input("address")),
            "status" => escape(input("status"))
        );
        Database::table("schools")->where("id", input("schoolid"))->update($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("school_successfully_updated"), "reload()"));
    }
    
    
    /**
     * Send Email to School
     * 
     * @return Json
     */
    public function sendemail() {
        $user = Auth::user();
        $school = Database::table("schools")->where("id", input("schoolid"))->first();
        $send   = Mail::send($school->email, input("subject"), array(
            "message" => input("message")
        ), "basic");
        
        
        if ($send) {
            $status = "Sent";
        } else {
            $status = "Failed";
        }
        Database::table("schoolmessages")->insert(array(
            "receiver" => $school->id,
            "type" => "email",
            "contact" => $school->email,
            "subject" => escape(input("subject")),
            "message" => escape(input("message")),
            "school" => $user->school,
            "branch" => $user->branch,
            "status" => $status
        ));
        
        if ($send) {
            return response()->json(responder("success", sch_translate("alright"), sch_translate("email_successfully_sent"), "reload()"));
        } else {
            return response()->json(responder("error", sch_translate("hmm"),$send->ErrorInfo));
        }
    }
    
    /**
     * Send SMS to School
     * 
     * @return Json
     */
    public function sendsms() {
        $user = Auth::user();
        $school = Database::table("schools")->where("id", input("schoolid"))->first();
        if (empty($school->phone)) {
            return response()->json(responder("error", sch_translate("hmm"), sch_translate("this_school_has_not_set_its_phone_number")));
        }
        
        if (env("DEFAULT_SMS_GATEWAY") == "africastalking") {
            if (empty(env("AFRICASTALKING_USERNAME"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_africas_talking_username_is_not_set")));
            }
            if (empty(env("AFRICASTALKING_KEY"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_africas_talking_api_key_is_not_set")));
            }
            
            $send = Sms::africastalking($school->phone, input("message"));
            
            if ($send) {
                $status = "Sent";
            } else {
                $status = "Failed";
            }
            Database::table("schoolmessages")->insert(array(
                "receiver" => $school->id,
                "type" => "sms",
                "contact" => $school->phone,
                "message" => escape(input("message")),
                "school" => $user->school,
                "branch" => $user->branch,
                "status" => $status
            ));
            
            if ($send) {
                return response()->json(responder("success", sch_translate("alright"), sch_translate("sms_successfully_sent"), "reload()"));
            } else {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("failed_to_send_sms_please_try_again")));
            }
            
        } elseif (env("DEFAULT_SMS_GATEWAY") == "twilio") {
            if (empty(env("TWILIO_SID"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_twilio_sid_is_not_set")));
            }
            if (empty(env("TWILIO_AUTHTOKEN"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_twilio_auth_token_is_not_set")));
            }
            if (empty(env("TWILIO_PHONENUMBER"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_twilio_phone_number_is_not_set")));
            }
            
            $send = Sms::twilio($school->phone, input("message"));
            
            
            if ($send) {
                $status = "Sent";
            } else {
                $status = "Failed";
            }
            Database::table("schoolmessages")->insert(array(
                "receiver" => $school->id,
                "type" => "sms",
                "contact" => $school->phone,
                "message" => escape(input("message")),
                "school" => $user->school,
                "branch" => $user->branch,
                "status" => $status
            ));
            
            if ($send) {
                return response()->json(responder("success", sch_translate("alright"), sch_translate("sms_successfully_sent"), "reload()"));
            } else {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("failed_to_send_sms_please_try_again")));
            }
        }
        
    }
    
}
 