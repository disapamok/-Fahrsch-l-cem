<?php
namespace Simcify\Controllers;

use Simcify\Exception;
use Simcify\Database;
use Simcify\Landa;
use Simcify\Auth;
use Simcify\Sms;
use Simcify\Mail;

class Communication {
    
    /**
     * Get communication view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if (!isset($_GET['view'])) {
            $messages = Database::table('usermessages')->where("usermessages`.`school", $user->school)->where("usermessages`.`branch", $user->branch)->leftJoin("users", "users.id", "receiver")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`usermessages.contact`", "`usermessages.status`", "`usermessages.sent_at`", "`usermessages.subject`", "`usermessages.message`", "`usermessages.type`", "`usermessages.id`", "`usermessages.receiver`");
            $type     = "user";
        } elseif (isset($_GET['view']) && $_GET['view'] == "branches") {
            $messages = Database::table('branchmessages')->where("branchmessages`.`school", $user->school)->where("branchmessages`.`branch", $user->branch)->leftJoin("branches", "branches.id", "receiver")->get("`branches.name`", "`branchmessages.contact`", "`branchmessages.status`", "`branchmessages.sent_at`", "`branchmessages.subject`", "`branchmessages.message`", "`branchmessages.type`", "`branchmessages.id`");
            $type     = "branch";
        } elseif (isset($_GET['view']) && $_GET['view'] == "schools" && $user->role == "superadmin") {
            $messages = Database::table('schoolmessages')->leftJoin("schools", "schools.id", "receiver")->get("`schools.name`", "`schoolmessages.contact`", "`schoolmessages.status`", "`schoolmessages.sent_at`", "`schoolmessages.subject`", "`schoolmessages.message`", "`schoolmessages.type`", "`schoolmessages.id`");
            $type     = "school";
        }
        $users = Database::table('users')->where("school", $user->school)->get();
        return view('communication', compact("recipients", "users", "user", "messages", "type"));
    }
    
    /**
     * Send SMS
     * 
     * @return Json
     */
    public function sms() {
        $user         = Auth::user();
        $recipient    = input('recipient');
        $receivers    = array();
        $type         = "user";
        $messageTable = "usermessages";
        if ($recipient == "student" || $recipient == "staff" || $recipient == "instructor" || $recipient == "everyone" || $recipient == "branches" || $recipient == "schools") {
            if ($recipient == "everyone") {
                $recipients   = Database::table('users')->where("school", $user->school)->get();
                $notification = sch_translate_notification("sms_sent_to_everyone_by", [$user->fname, $user->lname]);
            } elseif ($recipient == "branches") {
                $recipients   = Database::table('branches')->where("school", $user->school)->get();
                $type         = "branch";
                $messageTable = "branchmessages";
                $notification = sch_translate_notification("sms_sent_to_all_branches_by", [$user->fname, $user->lname]);
            } elseif ($recipient == "schools") {
                $recipients   = Database::table('schools')->get();
                $type         = "school";
                $messageTable = "schoolmessages";
                $notification = sch_translate_notification("sms_sent_to_all_schools_by", [$user->fname, $user->lname]);
            } else {
                $notification = sch_translate_notification("sms_sent_to_recipient_by", [$recipient, $user->fname, $user->lname]);
                $recipients   = Database::table('users')->where("role", $recipient)->where("school", $user->school)->get();
            }
            Landa::notify($notification, $user->id, "message");
            foreach ($recipients as $account) {
                if (empty($account->phone)) {
                    continue;
                }
                $receivers[] = array(
                    $account->id,
                    $account->phone,
                    $type
                );
            }
        } else {
            $recipients  = Database::table('users')->where("id", $recipient)->first();
            $receivers[] = array(
                $recipients->id,
                $recipients->phone,
                $type
            );
            Database::table("schools")->insert($schoolData);
        }
        if (empty($receivers)) {
            return response()->json(responder("error", sch_translate("hmm"), sch_translate("selected_recipients_have_not_set_numbers")));
        }
        
        if (env("DEFAULT_SMS_GATEWAY") == "africastalking") {
            if (empty(env("AFRICASTALKING_USERNAME"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_africas_talking_username_is_not_set")));
            }
            if (empty(env("AFRICASTALKING_KEY"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_africas_talking_api_key_is_not_set")));
            }
            foreach ($receivers as $receiver) {
                $send = Sms::africastalking($receiver[1], input("message"));
                if ($send) {
                    $status = "Sent";
                } else {
                    $status = "Failed";
                }
                Database::table($messageTable)->insert(array(
                    "receiver" => $receiver[0],
                    "type" => "sms",
                    "contact" => $receiver[1],
                    "message" => escape(input("message")),
                    "school" => $user->school,
                    "branch" => $user->branch,
                    "status" => $status
                ));
            }
            
            return response()->json(responder("success", sch_translate("alright"), sch_translate("message_queued_to_be_sent"), "reload()"));
            
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
            
            foreach ($receivers as $receiver) {
                $send = Sms::twilio($receiver[1], input("message"));
                if ($send) {
                    $status = "Sent";
                } else {
                    $status = "Failed";
                }
                Database::table($messageTable)->insert(array(
                    "receiver" => $receiver[0],
                    "type" => "sms",
                    "contact" => $receiver[1],
                    "message" => escape(input("message")),
                    "school" => $user->school,
                    "branch" => $user->branch,
                    "status" => $status
                ));
            }
            
            return response()->json(responder("success", sch_translate("alright"), sch_translate("message_queued_to_be_sent"), "reload()"));
            
        }
    }
    
    /**
     * Send Email
     * 
     * @return Json
     */
    public function email() {
        $user         = Auth::user();
        $recipient    = input('recipient');
        $receivers    = array();
        $type         = "user";
        $messageTable = "usermessages";
        if ($recipient == "student" || $recipient == "staff" || $recipient == "instructor" || $recipient == "everyone" || $recipient == "branches" || $recipient == "schools") {
            if ($recipient == "everyone") {
                $recipients   = Database::table('users')->where("school", $user->school)->get();
                $notification = sch_translate_notification("email_sent_to_everyone_by", [$user->fname, $user->lname]);
            } elseif ($recipient == "branches") {
                $recipients   = Database::table('branches')->where("school", $user->school)->get();
                $type         = "branch";
                $messageTable = "branchmessages";
                $notification = sch_translate_notification("email_sent_to_all_branches_by", [$user->fname, $user->lname]);
            } elseif ($recipient == "schools") {
                $recipients   = Database::table('schools')->get();
                $type         = "school";
                $messageTable = "schoolmessages";
                $notification = sch_translate_notification("email_sent_to_all_schools_by", [$user->fname, $user->lname]);
            } else {
                $notification = sch_translate_notification("email_sent_to_recipient_by", [$recipient, $user->fname, $user->lname]);
                $recipients   = Database::table('users')->where("role", $recipient)->where("school", $user->school)->get();
            }
            Landa::notify($notification, $user->id, "message");
            foreach ($recipients as $account) {
                if (empty($account->email)) {
                    continue;
                }
                $receivers[] = array(
                    $account->id,
                    $account->email,
                    $type
                );
            }
        } else {
            $recipients  = Database::table('users')->where("id", $recipient)->first();
            $receivers[] = array(
                $recipients->id,
                $recipients->email,
                $type
            );
            // Database::table("schools")->insert($schoolData);
        }
        if (empty($receivers)) {
            return response()->json(responder("error", sch_translate("hmm"), sch_translate("selected_recipients_have_not_set_emails")));
        }
        
        foreach ($receivers as $receiver) {
            $send = Mail::send($receiver[1], input('subject'), array(
                "message" => input('message')
            ), "basic");
            if ($send) {
                $status = "Sent";
            } else {
                $status = "Failed";
            }
            Database::table($messageTable)->insert(array(
                "receiver" => $receiver[0],
                "type" => "email",
                "contact" => $receiver[1],
                "subject" => escape(input("subject")),
                "message" => escape(input("message")),
                "school" => $user->school,
                "branch" => $user->branch,
                "status" => $status
            ));
        }
        
        return response()->json(responder("success", sch_translate("alright"), sch_translate("message_queued_to_be_sent"), "reload()"));
        
    }
    
    /**
     * Read message
     * 
     * @return \Pecee\Http\Response
     */
    public function read() {
        if (input("type") == "user") {
            $message = Database::table("usermessages")->where("id", input("messageid"))->first();
        } elseif (input("type") == "branch") {
            $message = Database::table("branchmessages")->where("id", input("messageid"))->first();
        } elseif (input("type") == "school") {
            $message = Database::table("schoolmessages")->where("id", input("messageid"))->first();
        }
        return view('extras/readmessage', compact("message"));
    }
    
    
    /**
     * Delete message
     * 
     * @return Json
     */
    public function delete() {
        if (input("type") == "user") {
            Database::table("usermessages")->where("id", input("messageid"))->delete();
        } elseif (input("type") == "branch") {
            Database::table("branchmessages")->where("id", input("messageid"))->delete();
        } elseif (input("type") == "school") {
            Database::table("schoolmessages")->where("id", input("messageid"))->delete();
        }
        return response()->json(responder("success", sch_translate("message_deleted"), sch_translate("message_successfully_deleted"), "reload()"));
    }
    
}
 