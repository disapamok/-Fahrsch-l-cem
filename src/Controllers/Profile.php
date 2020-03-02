<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;
use Simcify\File;
use Simcify\Sms;
use Simcify\Mail;
use Simcify\FS;

class Profile{

    /**
     * Get profile view
     * 
     * @return \Pecee\Http\Response
     */
    public function get($userid) {

        $user = Auth::user();
        $enrollments = $invoices = $payments = array();
        $profile = Database::table('users')->where('id',$userid)->first();
        $profile2 = Database::table('userinfo')->where('user',$userid)->first();
        $branches =  Database::table('branches')->where('school',$profile->school)->get(); //$user->school
        $user_branch =  Database::table('branches')->where('id', $profile->branch)->get(); //$user->branch
        $courses = Database::table('courses')->where('school',$profile->school)->where('status',"Available")->get(); //$user->school
        $instructors = Database::table('users')->where(['role'=>'instructor','branch'=>$profile->branch,'school'=>$profile->school])->get(); //$user->branch , $user->school
        $fleets = Database::table('fleet')->where('branch',$profile->branch)->get(); //$user->branch
        $timeline = Database::table('timeline')->where('user',$userid)->get();
        // $students = Database::table('users')->where(['role'=>'student','branch'=>$user->branch,'school'=>$user->school])->get();
        $theory_lessons = Database::table('theorylessons')->where('user', $userid)->get();

        $driving_lessons = Database::table('drivinglessons')->where('dl_user', $userid)->get();
        $driving_lessons_arr = array();

        foreach($driving_lessons as $dl){
            $ins = Database::table('users')->where('id',$dl->dl_instructor)->first();
            $dl->dl_instructor_id = $dl->dl_instructor;
            $dl->dl_instructor = $ins->fname . " ". $ins->lname;
            
            $driving_lessons_arr[] = $dl;

        }
        // New data
        $driving_lessons = $driving_lessons_arr;

        $assigned_students_id = Database::table('userinfo')->where('user',$userid)->first()->student;
        // convert to array

        $assigned_students_id = ($assigned_students_id == '') ? null : explode(',', $assigned_students_id);

        $assigned_students = array();
        // Get students base on instructor
        foreach($assigned_students_id as $id){
            $assigned_students[] = Database::table('users')->where('id', $id)->first();
        }
        // var_dump($assigned_students);
        // die;
        // fix for dropdown students, use of $profile not $user [task: other students are missing in dropdown] 
        $query = "select * from (select * from users where role = 'student') as `users` LEFT JOIN userinfo on userinfo.`user` = users.id WHERE userinfo.id IS NOT NULL ";
        $query.="AND users.school =".$profile->school." AND users.branch =".$profile->branch;
        $tbl = Database::table('userinfo');
        $students = $tbl->getResultsArray($query);
        // end fix

        if ($profile->role == "student") {
            
            $enrollments = Database::table('coursesenrolled')->leftJoin('courses','coursesenrolled.course','courses.id')->where('student',$profile->id)->orderBy('coursesenrolled.id', false)->get("`courses.name`", "`courses.duration`", "`courses.period`", "`coursesenrolled.created_at`", "`coursesenrolled.id`", "`coursesenrolled.course`", "`coursesenrolled.total_practical`", "`coursesenrolled.total_theory`", "`coursesenrolled.completed_theory`", "`coursesenrolled.completed_practical`");
            $payments = Database::table('payments')->leftJoin('invoices','payments.invoice','invoices.id')->where('payments`.`student',$profile->id)->orderBy('payments.id', false)->get("`payments.id`", "`payments.created_at`", "`payments.amount`", "`payments.method`", "`payments.invoice`", "`invoices.reference`");
            $invoices = Database::table("invoices")->where("student", $profile->id)->orderBy('id', false)->get();
        }
        $notes = Database::table('notes')->leftJoin('users','notes.note_by','users.id')->where('note_for',$profile->id)->orderBy('notes.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`notes.created_at`", "`notes.note`", "`notes.id`", "`notes.note_by`");
        $attachments = Database::table('attachments')->leftJoin('users','attachments.uploaded_by','users.id')->where('attachment_for',$profile->id)->orderBy('attachments.id', false)->get("`users.fname`", "`users.lname`", "`users.avatar`", "`attachments.created_at`", "`attachments.name`", "`attachments.attachment`", "`attachments.id`", "`attachments.uploaded_by`");

        $userinfo = Database::table('userinfo')->where('user',$userid)->get();

        $all_students = Database::table('users')->where('role', 'student')->get();

        $instructors = Database::table('users')->where('role','instructor')->where('status','active')->orderBy('id', true)->get();
        $choosenInstructor = Database::table('instructor_students')->where('student_id',$userid)->first();

        $choosenInstructor = ($choosenInstructor != null ? $choosenInstructor->instructor_id : null);
        
        return view('profile', compact("driving_lessons","theory_lessons","profile2","all_students","assigned_students","user", "profile","branches","enrollments","courses","notes","attachments","invoices","payments","instructors","fleets","students","timeline", "user_branch", "userinfo","instructors","choosenInstructor"));
    }

    /**
     * Update profile
     * 
     * @return Json
     */
    public function update() {

        // By default
        $studentStatus = 'Active';

        // Check if student
        if(input('is_schuler') == 'yes'){
            $studentStatus = escape(preg_replace('/\s+/', ' ', input('student_status')));

            if(strtolower($studentStatus) != 'active'){
                $studentStatus = 'Inactive';
            }
        }
        

        if (!empty(input("date_of_birth"))) {
            $date_of_birth = date('Y-m-d', strtotime(input("date_of_birth")));
        }else{
            $date_of_birth = '';
        }

        $data = array(
            "fname" => escape(preg_replace('/\s+/', ' ', input('fname'))),
            "lname" => escape(preg_replace('/\s+/', ' ', input('lname'))),
            "phone" => escape(input("phone")),
            "email" => escape(preg_replace('/\s+/', ' ', input('email'))),
            "address" => escape(input("address")),
            "date_of_birth" => $date_of_birth,
            "status" => $studentStatus,
            "gender" => escape(input("gender"))
        );

        if (!empty(input("permissions"))) {
            $data['permissions'] = escape(input("permissions"));
            $data['branch'] =  escape(input("branch"));
        }
        Database::table("users")->where("id", input("userid"))->update($data);

        if (escape((input("is_schuler"))) == "yes") {
            //user info              
            // $_karteikennung = escape(input("karteikennung")); removed from student form
            $_fuehrerschein_class_1 = escape(input("fuehrerschein_class_1"));
            $_fuehrerschein_class_2 = escape(input("fuehrerschein_class_2"));
            $_fuehrerschein_class_3 = escape(input("fuehrerschein_class_3"));

            $_fuehrerschein_class_1 = empty($_fuehrerschein_class_1)? "": $_fuehrerschein_class_1.", ";
            $_fuehrerschein_class_2 = empty($_fuehrerschein_class_2)? "": $_fuehrerschein_class_2.", ";
            $_fuehrerschein_class_3 = empty($_fuehrerschein_class_3)? "": $_fuehrerschein_class_3;

            $_BF17 = escape(input("bF17"));
            $_anmeldedatum = escape(input("anmeldedatum"));
            $_branch = escape(input("branch"));
            $_ort = escape(input("ort"));
            $_title = escape(input("title"));
            $_street_nr = escape(input("street_nr"));
            $_plz = escape(input("plz"));
            $_nationality = escape(input("nationality"));
            $_ausweis_nr = escape(input("ausweis_nr"));
            $_language = escape(input("language"));
            $_erteilungsart = escape(input("erteilungsart"));
            $_job = escape(input("job"));
            $_address = $_street_nr.", ".$_plz." ".$_ort;

            $studentID = input("userid");

            $instructor_student = array(
                'instructor_id' => input("instructor"),
                'student_id'    => input("userid")
            );
            $resp = Database::table('instructor_students')->where('student_id',$studentID)->first();
            

            if($resp == null){
                Database::table('instructor_students')->insert($instructor_student);
            }else{
                Database::table('instructor_students')->where('student_id',$studentID)->update($instructor_student);
            }

            $data = array(
                'user'=>input("userid"),
                // "cardnumber"=>$_karteikennung, removed from student form
                "licensetype" => $_fuehrerschein_class_1.$_fuehrerschein_class_2.$_fuehrerschein_class_3,
                "erteilungsart"=>$_erteilungsart,
                "pricelist" => "none",
                "title" => $_title,
                "bf17"=>$_BF17,
                "plz"=>$_plz,
                "city"=>$_ort,
                "street"=>$_street_nr,
                "job"=>$_job,
                "nationality"=>$_nationality,
                "id_number" => $_ausweis_nr,
                "language" => $_language,
                "register_date"=>$_anmeldedatum
            );
            Database::table('userinfo')->where("user", input("userid"))->update($data);

            $_address = $_street_nr.", ".$_plz." ".$_ort;
            $data = array(
                "address" => $_address
            );
            Database::table("users")->where("id", input("userid"))->update($data); 
        }

        return response()->json(responder("success", sch_translate("alright"), sch_translate("profile_successfully_updated"), "reload()"));
    }
    
    /**
     * Delete user account
     * 
     * @return Json
     */
    public function delete() {
        $account = Database::table("users")->where("school", input("userid"))->get();
        if (!empty($account->avatar)) {
            File::delete($account->avatar, "avatar");
        }
        Database::table("users")->where("id", input("userid"))->delete();
        return response()->json(responder("success", sch_translate("user_deleted"), sch_translate("user_account_successfully_deleted"), "redirect('".url('')."', true)"));
    }
    
    
    /**
     * Send Email to user
     * 
     * @return Json
     */
    public function sendemail() {
        $user = Database::table("users")->where("id", input("userid"))->first();
        $send   = Mail::send($user->email, input("subject"), array(
            "message" => input("message")
        ), "basic");
        
        if ($send) {
            return response()->json(responder("success", sch_translate("alright"), sch_translate("email_successfully_sent"), "reload()"));
        } else {
            return response()->json(responder("error", sch_translate("hmm"),$send->ErrorInfo));
        }
    }
    
    /**
     * Send SMS to user
     * 
     * @return Json
     */
    public function sendsms() {
        $user = Database::table("users")->where("id", input("userid"))->first();
        if (empty($user->phone)) {
            return response()->json(responder("error", sch_translate("hmm"), sch_translate("this_user_has_not_set_its_phone_number")));
        }
        
        if (env("DEFAULT_SMS_GATEWAY") == "africastalking") {
            if (empty(env("AFRICASTALKING_USERNAME"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_africas_talking_username_is_not_set")));
            }
            if (empty(env("AFRICASTALKING_KEY"))) {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("your_africas_talking_api_key_is_not_set")));
            }
            
            $send = Sms::africastalking($user->phone, input("message"));
            
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
            
            $send = Sms::twilio($user->phone, input("message"));
            
            if ($send) {
                return response()->json(responder("success", sch_translate("alright"), sch_translate("sms_successfully_sent"), "reload()"));
            } else {
                return response()->json(responder("error", sch_translate("hmm"), sch_translate("failed_to_send_sms_please_try_again")));
            }
        }
        
    }


    /**
     * Add note to profile
     * 
     * @return Json
     */
    public function addnote() {
        $user = Auth::user();
        $data = array(
                'note_by'=>$user->id,
                'note_for'=>input('userid'),
                'note'=> escape(input('note'))
                );
        Database::table('notes')->insert($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("note_successfully_published"), "reload()"));
    }
    
    /**
     * Delete note
     * 
     * @return Json
     */
    public function deletenote() {
        Database::table("notes")->where("id", input("noteid"))->delete();
        return response()->json(responder("success", sch_translate("note_deleted"), sch_translate("note_successfully_deleted"), "reload()"));
    }
    
    /**
     * Note details view
     * 
     * @return Json
     */
    public function readnote() {
        $note = Database::table("notes")->where("id", input("noteid"))->first();
        return view('extras/readnote', compact("note"));
    }
    
    /**
     * Note update view
     * 
     * @return Json
     */
    public function updatenoteview() {
        $note = Database::table("notes")->where("id", input("noteid"))->first();
        return view('extras/updatenote', compact("note"));
    }
    
    /**
     * Update Note
     * 
     * @return Json
     */
    public function updatenote() {
        $data = array(
            "note" => escape(input("note"))
        );
        Database::table("notes")->where("id", input("noteid"))->update($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("note_successfully_updated"), "reload()"));
    }

    /**
    *Upload attachment
    * 
    * @return Json
    */
    public function uploadattachment() {
        $user = Auth::user();
        $upload = File::upload(
            $_FILES['attachment'], 
            "attachments"
            ,array(
                "source" => "form",
                "allowedExtesions" => "pdf, png, gif, jpg, jpeg",
                 )
        );

        if($upload['status'] == 'success'){
            $data = array(
                'name'=>escape(input('name')),
                'attachment'=>$upload['info']['name'],
                'uploaded_by'=>$user->id,
                'attachment_for'=>escape(input('userid'))
            );
            Database::table('attachments')->insert($data);
            return response()->json(responder("success", sch_translate("alright"), sch_translate("attachment_successfully_uploaded"), "reload()"));
        }else{
            return response()->json(responder("error", sch_translate("hmm"), sch_translate("something_went_wrong_please_try_again")));
        }
    }
    
    /**
     * Delete attachment
     * 
     * @return Json
     */
    public function deleteattachment() {
        $attachment = Database::table("attachments")->where("id", input("attachmentid"))->first();
        File::delete($attachment->attachment, "attachments");
        Database::table("attachments")->where("id", input("attachmentid"))->delete();
        return response()->json(responder("success", sch_translate("attachment_deleted"), sch_translate("attachment_successfully_deleted"), "reload()"));
    }
    
    /**
     * disconnect google calendar
     * 
     * @return Json
     */
    public function disconnectgoogle() {
        Database::table("users")->where("id", input("userid"))->update(array("google_access_token" => ""));
        return response()->json(responder("success", sch_translate("disconnected"), sch_translate("your_google_calendar_has_been_disconnected"), "reload()"));
    }

}
