<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;
use Simcify\Sms;
use Simcify\Mail;

class Branch {
    
    /**
     * Get branch view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user     = Auth::user();
        $branches = Database::table('branches')->where('school', $user->school)->orderBy('id', true)->get();
        foreach ($branches as $branch) {
            $branch->instructors = Database::table('users')->where(array(
                'branch' => $branch->id,
                'role' => 'instructor'
            ))->count("id", "total")[0]->total;
            $branch->students    = Database::table('users')->where(array(
                'branch' => $branch->id,
                'role' => 'student'
            ))->count("id", "total")[0]->total;
            $branch->vehicles    = Database::table('fleet')->where('branch', $branch->id)->count("id", "total")[0]->total;
        }
        return view('branches', compact("branches", "user"));
    }
    
    
    /**
     * Create a Branch 
     * 
     * @return Json
     */
    public function create() {
        $user       = Auth::user();
        $branchData = array(
            "name" => escape(input('name')),
            "school" => $user->school,
            "phone" => escape(input('phone')),
            "email" => escape(input('email')),
            "address" => escape(input('address'))
        );
        Database::table("branches")->insert($branchData);
        return response()->json(responder("success", sch_translate('branch_created'), sch_translate('branch_successfully_created'), "reload()"));
    }
    
    
    /**
     * Delete branch
     * 
     * @return Json
     */
    public function delete() {
        $users = Database::table("users")->where("branch", input("branchid"))->get();
        foreach ($users as $user) {
            if (!empty($user->avatar)) {
                File::delete($user->avatar, "avatar");
            }
        }
        Database::table("branches")->where("id", input("branchid"))->delete();
        return response()->json(responder("success", sch_translate('branch_deleted'), sch_translate('branch_successfully_deleted'), "reload()"));
    }
    
    /**
     * Branch update view
     * 
     * @return Json
     */
    public function updateview() {
        $branch = Database::table("branches")->where("id", input("branchid"))->first();
        return view('extras/updatebranch', compact("branch"));
    }
    
    /**
     * Update branch
     * 
     * @return Json
     */
    public function update() {
        $data = array(
            "name" => escape(input("name")),
            "phone" => escape(input("phone")),
            "email" => escape(input("email")),
            "address" => escape(input("address"))
        );
        Database::table("branches")->where("id", input("branchid"))->update($data);
        return response()->json(responder("success", sch_translate('alright'), sch_translate('branch_successfully_updated'), "reload()"));
    }
    
    
    /**
     * Send Email to Branch
     * 
     * @return Json
     */
    public function sendemail() {
        $user = Auth::user();
        $branch = Database::table("branches")->where("id", input("branchid"))->first();
        $send   = Mail::send($branch->email, input("subject"), array(
            "message" => input("message")
        ), "basic");
        
        
        if ($send) {
            $status = "Sent";
        } else {
            $status = "Failed";
        }
        Database::table("branchmessages")->insert(array(
            "receiver" => $branch->id,
            "type" => "email",
            "contact" => $branch->email,
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
     * Send SMS to branch
     * 
     * @return Json
     */
    public function sendsms() {
        $user = Auth::user();
        $branch = Database::table("branches")->where("id", input("branchid"))->first();
        if (empty($branch->phone)) {
            return response()->json(responder("error", sch_translate("hmm"), sch_translate("this_branch_has_not_set_its_phone_number")));
        }
        
        if (env("DEFAULT_SMS_GATEWAY") == "africastalking") {
            if (empty(env("AFRICASTALKING_USERNAME"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_africas_talking_username_is_not_set")));
            }
            if (empty(env("AFRICASTALKING_KEY"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_africas_talking_api_key_is_not_set")));
            }
            
            $send = Sms::africastalking($branch->phone, input("message"));
            
            
            if ($send) {
                $status = "Sent";
            } else {
                $status = "Failed";
            }
            Database::table("branchmessages")->insert(array(
                "receiver" => $branch->id,
                "type" => "sms",
                "contact" => $branch->phone,
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
            
            $send = Sms::twilio($branch->phone, input("message"));
            
            
            if ($send) {
                $status = "Sent";
            } else {
                $status = "Failed";
            }
            Database::table("branchmessages")->insert(array(
                "receiver" => $branch->id,
                "type" => "sms",
                "contact" => $branch->phone,
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
    
    /**
     * Switch between branches
     * 
     * @return Json
     */
    public function switcher() {
        $user = Auth::user();
        Database::table('users')->where('id', $user->id)->update(array(
            'branch' => input("branchid")
        ));
        return response()->json(responder("success", sch_translate("alright"), sch_translate("branch_switch_successfull"), "reload()"));
    }
    
}