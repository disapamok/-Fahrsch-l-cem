<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Landa;
use Simcify\Auth;
use Simcify\Mail;

class Student{

    /**
     * Get students view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {

        $r = $_REQUEST;
        $status         = isset($r['status']) ? $r['status'] : '';
        $payment_stat   = isset($r['istatus']) ? $r['istatus'] : '';
        $search         = isset($r['search']) ? $r['search'] : '';
        $gender         = isset($r['gender']) ? $r['gender'] : '';
        $class          = isset($r['fuehrerschein_class']) ? $r['fuehrerschein_class'] : '' ;
        $branch         = isset($r['location']) ? $r['location'] : '';

        $user = Auth::user();

        if(count($r) > 0){
           
            $query_string = !empty($search) ? " AND users.fname LIKE '%".$search."%' OR users.email LIKE '%".$search."%' OR users.phone LIKE '%".$search."%'" : '';
            $query_string.= !empty($class) ? " AND userinfo.licensetype LIKE '%".$class."%'" : '';
            $query_string.=" AND users.school =".$user->school;
            $query_string.= !empty($branch) ? " AND users.branch=".$branch : '';


            $query ="select * from (select * from users where role = 'student')as `users` LEFT JOIN userinfo on userinfo.`user` = users.id WHERE 1=1 ".$query_string;
            $tbl = Database::table('userinfo');
            $students = $tbl->getResultsArray($query);

        }else{

            $query = "select * from (select * from users where role = 'student') as `users` LEFT JOIN userinfo on userinfo.`user` = users.id WHERE userinfo.id IS NOT NULL AND users.status = 'Active'";
            $query.="AND users.school =".$user->school;
            $tbl = Database::table('userinfo');
            $students = $tbl->getResultsArray($query);
        }

        $courses = Database::table('courses')->where('school',$user->school)->where('status',"Available")->get();
        $branches = Database::table('branches')->where('school', $user->school)->orderBy('id', true)->get();
        $instructors = Database::table('users')->where('role','instructor')->where('status','active')->orderBy('id', true)->get();

        return view('students', compact("user", "students", "courses", "branches","instructors"));
    }

    public function searchStudent(){

        $r = $_REQUEST;
        $status         = isset($r['status']) ? $r['status'] : '';
        $payment_stat   = isset($r['istatus']) ? $r['istatus'] : '';
        $search         = isset($r['search']) ? preg_replace('/\s+/', ' ', $r['search']) : '';
        $gender         = isset($r['gender']) ? $r['gender'] : '';
        $class          = ($r['fuehrerschein_class'] != 'All') ? "qry2.licensetype REGEXP '[[:<:]]".$r['fuehrerschein_class']."[[:>:]]'" : "INSTR(qry2.licensetype, '' ) <> 0 " ;
        $branch         = isset($r['location']) ? $r['location'] : '';

        $user = Auth::user();
        
        $ctr = 0;

        if(!empty($search) || $payment_stat!= 'All' || $branch!='All' || $r['fuehrerschein_class'] !='All' || $r['istatus'] != 'All'  ){
            $query_school=" AND qry2.user_school =".$user->school;
            $query_string= $branch!='All' ? " AND qry2.user_branch=".$branch : '';
            $query_istatus = $r['istatus'] !='All' ? "AND qry2.istatus='".$r['istatus']."'" : '' ;
            $query = "SELECT
        * 
        FROM
            (
        SELECT
            userid,
            fullname,
            email,
            gender,
            date_of_birth,
            phone,
            address,
            user_school,
            user_branch,
            course,
            role,
            `status`,
            amount,
            amountpaid,
            licensetype,
            ( CASE WHEN amountpaid < amount && amountpaid > 0 THEN 'Partially' WHEN amountpaid = 0 THEN 'Unpaid' WHEN amountpaid >= amount THEN 'Paid' END ) AS istatus 
        FROM
            (
        SELECT
            * 
        FROM
            (
        SELECT
            id AS userid,
            CONCAT( fname, ' ', lname ) AS fullname,
            email,
            fname,
            lname,
            gender,
            date_of_birth,
            phone,
            address,
            school AS user_school,
            branch AS user_branch,
            course,
            role,
            `status` 
        FROM
            users 
        WHERE
            role = 'student' 
            AND school = ".$user->school."  
            ) AS `users`
            INNER JOIN userinfo ON users.userid = userinfo.`user` 
            ) AS userdetails
            LEFT JOIN invoices ON userdetails.userid = invoices.student 
        WHERE
            userdetails.userid IS NOT NULL 
            AND 
            (
                userdetails.fname LIKE '%".$search."%' OR 
                userdetails.lname LIKE '%".$search."%' OR 
                userdetails.email LIKE '%".$search."%' OR 
                userdetails.phone LIKE '%".$search."%' OR
                CONCAT(userdetails.fname, ' ',userdetails.lname) LIKE '%".$search."%'
            )
            
            ) AS qry2 
        WHERE
            ".$class." ".$query_school." ".$query_string." ".$query_istatus." GROUP BY userid";

                   // echo $query;
            $tbl = Database::table('userinfo');
            $students = $tbl->getResultsArray($query);
        }else{
    
            $query = "select *, users.id AS userid from (select *, CONCAT( fname, ' ', lname ) AS fullname from users where role = 'student')as `users` LEFT JOIN userinfo on userinfo.`user` = users.id WHERE userinfo.id IS NOT NULL ";
            $query.="AND users.school =".$user->school." GROUP BY users.id";

            $tbl = Database::table('userinfo');
            $students = $tbl->getResultsArray($query);
        }
        
        $newStudents = array();
        // temp
        // Filter statudents base on status
        foreach($students as $s){
            if($s['status'] == $status){
                $newStudents[] = $s;
            }
        }
        
        return response()->json($newStudents);
    }
  
    
    /**
     * Create student Account
     * 
     * @return Json
     */
    public function create() {
        $amountpaid = floatval(str_replace(',','.',input('amountpaid')));

        //user info 
        $studentStatus = escape(preg_replace('/\s+/', ' ', input('student_status')));

        if(strtolower($studentStatus) != 'active'){
            $studentStatus = 'Inactive';
        }
 
        $_ort = escape(input("ort"));
        $_street_nr = escape(input("street_nr"));
        $_plz = escape(input("plz"));
        $_branch = escape(input("branch"));
        $_instructor = escape(input("instructor"));
        $_address = $_street_nr.", ".$_plz." ".$_ort;

        $user = Database::table("users")->where("email", input('email'))->first();
        if (!empty($user)) {
            return response()->json(array(
                "status" => "error",
                "title" => sch_translate("email_already_exist"),
                "message" => sch_translate("email_already_exist")
            ));
        }
        $user = Auth::user();
        $school = Database::table("schools")->where("id", $user->school)->first();
        $password = rand(111111, 999999);
        $signup   = Auth::signup(array(
            "fname" => escape(preg_replace('/\s+/', ' ', input('fname'))),
            "lname" => escape(preg_replace('/\s+/', ' ', input('lname'))),
            "email" => escape(preg_replace('/\s+/', ' ', input('email'))),
            "phone" => "+".escape(preg_replace('/\s+/', ' ', input('phone'))),
            "gender" => escape(preg_replace('/\s+/', ' ', input('gender'))),
            "permissions" => escape(input('permissions')),
            "branch" => $_branch,
            "password" => Auth::password($password),
            "school" => $user->school,
            "role" => 'student',
            "status" => $studentStatus,
            "address"=> preg_replace('/\s+/', ' ', $_address)
        ), array(
            "authenticate" => false,
            "uniqueEmail" => escape(preg_replace('/\s+/', ' ', input('email')))
        ));

        if ($signup["status"] == "success") {
            //user info              
            // $_karteikennung = escape(input("karteikennung"));
            $_fuehrerschein_class_1 = escape(input("fuehrerschein_class_1"));
            $_fuehrerschein_class_2 = escape(input("fuehrerschein_class_2"));
            $_fuehrerschein_class_3 = escape(input("fuehrerschein_class_3"));

            $_fuehrerschein_class_1 = empty($_fuehrerschein_class_1) ? "" : $_fuehrerschein_class_1.", ";
            $_fuehrerschein_class_2 = empty($_fuehrerschein_class_2) ? "" : $_fuehrerschein_class_2.", ";
            $_fuehrerschein_class_3 = empty($_fuehrerschein_class_3) ? "" : $_fuehrerschein_class_3;

            $_BF17 = escape(preg_replace('/\s+/', ' ', input("bF17")));
            $_anmeldedatum = escape(preg_replace('/\s+/', ' ', input("anmeldedatum")));
            $_branch = escape(preg_replace('/\s+/', ' ', input("branch")));
            $_ort = escape(preg_replace('/\s+/', ' ', input("ort")));
            $_title = escape(preg_replace('/\s+/', ' ', input("title")));
            $_street_nr = escape(preg_replace('/\s+/', ' ', input("street_nr")));
            $_plz = escape(preg_replace('/\s+/', ' ', input("plz")));
            $_nationality = escape(preg_replace('/\s+/', ' ', input("nationality")));
            $_ausweis_nr = escape(preg_replace('/\s+/', ' ', input("ausweis_nr")));
            $_language = escape(input("language"));
            $_erteilungsart = escape(preg_replace('/\s+/', ' ', input("erteilungsart")));
            $_job = escape(preg_replace('/\s+/', ' ', input("job")));


            $instructor_student = array(
                'instructor_id' => $_instructor,
                'student_id'    => $newStudents
            );

            $resp = Database::table('instructor_students')->insert($instructor_student);

            $data = array(
                'user'=>$signup["id"],
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
            Database::table('userinfo')->insert($data);   
        }
        
        if ($signup["status"] == "success") {

            $timeline = sch_translate_notification("new_student_acct_created_by", [$user->fname, $user->lname]);
            Landa::timeline($signup["id"], $timeline);  
            Landa::notify($timeline, $signup["id"], "newaccount", "personal");
            
            $notification = sch_translate_notification("new_student_acct_created_for", [input('fname'), input('lname')]);
            Landa::notify($notification, $user->id, "newaccount");
          // send welcome email
            Mail::send(input('email'), sch_translate("welcome_to_school", [$school->name]), array(
                "title" => sch_translate("welcome_to_school", [$school->name]),
                "subtitle" => sch_translate("your_school_has_created_an_acct_for_you", [$school->name]),
                "buttonText" => sch_translate("login_now"),
                "buttonLink" => env("APP_URL"),
                "message" => sch_translate("these_are_your_login_credentials", [input('email'), $password])
            ), "withbutton");

            if (!empty(input("newcourse"))) {
              self::enroll($signup["id"], input("newcourse"));
              self::createinvoice($signup["id"], input("newcourse"), $amountpaid, input("method"));
            }
 
            return response()->json(responder("success", sch_translate("account_created"), sch_translate("student_account_successfully_created"), "reload()"));
        }else{
          return response()->json(responder("error", sch_translate("hmm"), sch_translate("something_went_wrong_please_try_again")));
        }
        
    }


    /**
     * Add course
    *
    */
    public function addcourse(){
          self::enroll(input("studentid"), input("newcourse"));
          self::createinvoice(input("studentid"), input("newcourse"), input("amountpaid"), input("method"));
          return response()->json(responder("success", sch_translate("alright"), sch_translate("student_successfully_enrolled_to_the_course"), "reload()"));
    }

    /**
     * Enroll student to a course
    *
     * @return true
    */
    private function enroll($student, $course){
        $user = Auth::user();
        $school = Database::table('schools')->where('id',$user->school)->first();
        $course = Database::table('courses')->where('id',$course)->first();
        $student = Database::table('users')->where('id',$student)->first();

        $data = array(
            'school'=>$user->school,
            'branch'=>$user->branch,
            'student'=>$student->id,
            'course'=>$course->id,
            'total_theory'=>$course->practical_classes,
            'total_practical'=>$course->theory_classes
        );
        Database::table('coursesenrolled')->insert($data);   
          $timeline = sch_translate("enrolled_to_course", [$course->name]);
          Landa::timeline($student->id, $timeline);  
        // send enrollment email
          Mail::send($student->email, sch_translate("enrolled_to_course", [$school->name]), array(
              "message" => sch_translate("successfully_enrolled_to_course", [$student->fname, $course->name, $school->name, $course->practical_classes, $course->theory_classes, $school->name]) 
          ), "basic");
          return true;
    }

    /**
     * Delete student Enrollment student to a course
    *
     * @return true
    */
    public function deleteenrollment(){
        Database::table('coursesenrolled')->where("id", input("enrollmentid"))->delete();
        return response()->json(responder("success", sch_translate("alright"), sch_translate("student_enrollment_successfully_deleted"), "reload()"));
    }

    /**
     * Create an invoice
    *
     * @return true
    */
    private function createinvoice($student, $course, $amountpaid = 0, $paymentmethod = "Other"){
        $user = Auth::user();
        $school = Database::table('schools')->where('id',$user->school)->first();
        $course = Database::table('courses')->where('id',$course)->first();
        $student = Database::table('users')->where('id',$student)->first();

        $reference = rand(111111,999999);
        $data = array(
            'school'=>$user->school,
            'branch'=>$user->branch,
            'student'=>$student->id,
            'reference'=> $reference,
            'item'=>$course->name,
            'amount'=>$course->price,
            'amountpaid'=>$amountpaid
          );  
          Database::table('invoices')->insert($data); 
          $invoiceId = Database::table('invoices')->insertId();  

          if ($amountpaid > 0) {
            $data = array(
                'invoice'=>$invoiceId,
                'school'=>$user->school,
                'branch'=>$user->branch,
                'student'=>$student->id,
                'method'=>$paymentmethod,
                'amount'=>$amountpaid
            );   
            Database::table('payments')->insert($data);
            $notification = sch_translate_notification("you_made_a_payment_of", [money($amountpaid)]);
            Landa::notify($notification, $student->id, "payment", "personal");
            $notification = sch_translate_notification("a_payment_of_recieved_from", [money($amountpaid), $student->fname, $student->lname]);
            Landa::notify($notification, $user->id, "payment");
          }  


        // send invoice email
          Mail::send($student->email, $school->name." invoice #".$reference, 
            array(
                "title" => sch_translate("thank_you_for_joining_us"),
                "subtitle" => sch_translate("invoice_for_your_enrollment", [$school->name, $amountpaid]),
                "summary" => array(
                                "currency" => currency(),
                                "subtotal" => $course->price,
                                "tax" => 0,
                                "total" => $course->price,
                            ),
                "items" => array(
                            array(
                                "name" => $course->name,
                                "quantity" => "1",
                                "price" => $course->price,
                            )
                        )
            ), "invoice");

          return true;

    }


}
