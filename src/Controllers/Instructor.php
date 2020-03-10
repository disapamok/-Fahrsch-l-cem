<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;
use Simcify\Mail;
use Simcify\ActivityTimeline;

class Instructor {
    /**
     * Get instructors view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {

        $user    = Auth::user();
        $courses = Database::table('courses')->where('school', $user->school)->get();

        $query = "SELECT * FROM `users` WHERE `role` = 'instructor' AND `school` = $user->school AND `branch` = $user->branch";
        
        $instructors = Database::table('users')->getResults($query);

        foreach ($instructors as $instructor) {
            $instructor->courses = Database::table('courseinstructor')->where('instructor', $instructor->id)->count("id", "total")[0]->total;
            $instructor->completed = Database::table('schedules')->where('instructor', $instructor->id)->where('status', "Complete")->count("id", "total")[0]->total;
        }

        $query = "select * from (select * from users where role = 'student') as `users` LEFT JOIN userinfo on userinfo.`user` = users.id WHERE userinfo.id IS NOT NULL AND users.status = 'Active'";
        $query.="AND users.school =".$user->school;
        $tbl = Database::table('userinfo');
        $students = $tbl->getResults($query);

        return view('instructors', compact("user", "instructors", "courses", "students"));
    }


    public function searchInstructor()
    {
        $user    = Auth::user();
        $search  = $gender = '';

        $query = "SELECT * FROM `users` WHERE `role` = 'instructor' AND `school` = $user->school AND `branch` = $user->branch";

        if (isset($_POST['search']) || isset($_POST['gender'])) {

            $search  = strtolower($_POST['search']);
            $gender  = $_POST['gender'];

            if($gender != 'All' && $gender != 'all'){
                $query .= " AND `gender` = '".$gender."'"; 
            }

            if($search){
                $query .= " AND (LOWER(`fname`) LIKE LOWER('%".$search."%') OR LOWER(`lname`) LIKE LOWER('%".$search."%') OR CONCAT(LOWER(`fname`), ' ',LOWER(`lname`)) LIKE LOWER('%".$search."%'))";
            }

        }
        
        $instructors = Database::table('users')->getResults($query);
        // var_dump($instructors);
        // die;
        foreach ($instructors as $instructor) {
            $instructor->courses = Database::table('courseinstructor')->where('instructor', $instructor->id)->count("id", "total")[0]->total;
            $instructor->completed = Database::table('schedules')->where('instructor', $instructor->id)->where('status', "Complete")->count("id", "total")[0]->total;
        }

        $html = '';

        if(!empty($instructors)){

            foreach($instructors as $instructor){
            // <!-- user grid -->
            $html .= "<div class='col-md-4 col-lg-3'>
                <div class='w-100 bg-grey p-1 rounded-top'></div>
                <div class='user-grid card p-0 shadow-sm'>
                  <div class='p-0 p-3'>
                      <div class='user-grid-pic'>";
                        if( !empty($instructor->avatar) ){
                            $html .= "<img src='".url('')."uploads/avatar/".$instructor->avatar."' class='student-image mb-2'>";
                        }
                        else{
                            $html .= "<img src='".url('')."assets/images/avatar.png' class='student-image mb-2'>";
                        }
                $html .= "</div>
                      <div class='user-grid-info'>
                        <h5 class='text-gray text-uppercase font-14 mb-0'>".$instructor->fname." ".$instructor->lname."</h5>
                        <p>".sch_translate('instructor_of_course', [$instructor->courses])."</p>
                      </div>
                  </div>
                  <div class='p-3 px-4'>
                      <div class='row user-grid-buttons'>
                        <div class='col-md-12 px-0 ml-1 mr-2 mb-2'>
                            <span class='bg-dark p-2 text-white float-left w-25 rounded-left'><img src='".url('')."assets/images/icons/telephone.png'></span>
                            <div class='pr-2 d-inline w-75 float-right'>
                              <div class='text-center p-2 bg-grey rounded-right letter_spacing_phone'>".$instructor->phone."</div>
                            </div>
                        </div>
                        <div class='col-md-6 p-5'>
                            <a class='btn btn-red btn-block' href='".url('Profile@get',['userid'=>"$instructor->id"])."'>
                            <i class='fa fa-user mr-2'></i>".sch_translate('profile')."</a>
                        </div>
                        <div class='col-md-6 p-5'>
                            <a class='btn btn-default btn-block' href='".url('Schedule@get')."?filter=instructor&filterid=".$instructor->id."'><img src='".url('')."assets/images/icons/calendar-alt-solid.png' class='img-icon mr-2'>".sch_translate('Kalender')."</a>
                        </div>
                      </div>
                  </div>
                </div>
            </div>";
            }
        }else{
            $html .= "<div class='col-md-12'>
              <div class='empty'>
                <i class='mdi mdi-alert-circle-outline'></i>
                <h3>".sch_translate('empty_here')."</h3>
              </div>
          </div>";
        }

        echo $html;
    }
    
    
    /**
     * Create Instructor Account
     * 
     * @return Json
     */
    public function create() {
        $user = Database::table("users")->where("email", trim(input('email')))->first();
 
        if (!empty($user)) {
            return response()->json(array(
                "status" => "error",
                "title" => sch_translate("email_already_exist"),
                "message" => sch_translate("email_already_exist")
            ));
        }

        $_ort = escape(input("ort"));
        $_street_nr = escape(input("street_nr"));
        $_plz = escape(input("plz"));
        $_address = $_street_nr.", ".$_plz." ".$_ort;

        $_title = escape(preg_replace('/\s+/', ' ', input("title")));
        $_street_nr = escape(preg_replace('/\s+/', ' ', input("street_nr")));
        $_student = input("student");

        $students = '';

        // Store in array
        foreach($_student as $s){
            $students .= $s.",";
        }

        // Remove last string
        $students = substr_replace($students ,"",-1);
        
        
        $_fuehrerschein_class_1 = escape(input("fuehrerschein_class_1"));
        $_fuehrerschein_class_2 = escape(input("fuehrerschein_class_2"));
        $_fuehrerschein_class_3 = escape(input("fuehrerschein_class_3"));

        $_fuehrerschein_class_1 = empty($_fuehrerschein_class_1) ? "" : $_fuehrerschein_class_1.", ";
        $_fuehrerschein_class_2 = empty($_fuehrerschein_class_2) ? "" : $_fuehrerschein_class_2.", ";
        $_fuehrerschein_class_3 = empty($_fuehrerschein_class_3) ? "" : $_fuehrerschein_class_3;

        // Vehicle reg no.
        $_vreg_1 = escape(input("vreg_1"));
        $_vreg_2 = escape(input("vreg_2"));
        $_vreg_3 = escape(input("vreg_3"));

        $_vreg_1 = empty($_vreg_1) ? "" : $_vreg_1.", ";
        $_vreg_2 = empty($_vreg_2) ? "" : $_vreg_2.", ";
        $_vreg_3 = empty($_vreg_3) ? "" : $_vreg_3;


        $user     = Auth::user();
        $password = rand(111111, 999999);
        $signup   = Auth::signup(array(
            "fname" => escape(preg_replace('/\s+/', ' ', input('fname'))),
            "lname" => escape(preg_replace('/\s+/', ' ', input('lname'))),
            "email" => escape(preg_replace('/\s+/', ' ', input('email'))),
            "phone" => "+".escape(preg_replace('/\s+/', ' ', input('phone'))),
            "gender" => escape(preg_replace('/\s+/', ' ', input('gender'))),
            "password" => Auth::password($password),
            "school" => $user->school,
            "branch" => $user->branch,
            "role" => 'instructor',
            "address"=> preg_replace('/\s+/', ' ', $_address),
            "date_of_birth" => date('Y-m-d', input("date_of_birth")),
        ), array(
            "authenticate" => false,
            "uniqueEmail" => escape(preg_replace('/\s+/', ' ', input('email')))
        ));

        if ($signup["status"] == "success") {
            
            $data = array(
                'user'=>$signup["id"],
                "licensetype" => $_fuehrerschein_class_1.$_fuehrerschein_class_2.$_fuehrerschein_class_3,
                "title" => $_title,
                "plz"=>$_plz,
                "city"=>$_ort,
                "street"=>$_street_nr,
                "vehicle_reg_no" => $_vreg_1.$_vreg_2.$_vreg_3,
                "student" => $students
            );
            Database::table('userinfo')->insert($data); 

            Mail::send(preg_replace('/\s+/', ' ', input('email')), sch_translate("welcome_to_app"), array(
                "title" => sch_translate("welcome_to_app"),
                "subtitle" => sch_translate("your_school_has_created_an_instructor_acct_for_you"),
                "buttonText" => sch_translate("login_now"),
                "buttonLink" => env("APP_URL"),
                "message" => sch_translate("these_are_your_login_credentials", [preg_replace('/\s+/', ' ', input('email')), $password])
            ), "withbutton");
            return response()->json(responder("success", sch_translate("account_created"), sch_translate("instructor_account_successfully_created"), "reload()"));
        }
        
    }

    
    public function addAssingStudent(){

        $instructor = input('instructor');
        $assignedStudent = input('assignstudent');

        $ids = array();
        $students = Database::table('instructor_students')->where('instructor_id',$instructor)->get();
        foreach ($students as $student) {
            array_push($ids, $student->student_id);
        }

        $student = Database::table('user')->where('id', input('assignstudent'))->first();
        $key = in_array($assignedStudent, $ids);
        if($key){
            return response()->json(array(
                "status" => "error",
                "title" => sch_translate("learner_already_exist"),
                "message" => sch_translate("learner_already_exist")
            ));
        }else{
            $data = array(
                'instructor_id' => $instructor,
                'student_id'    => $assignedStudent,
            );
            Database::table('instructor_students')->insert($data);
        }

        array_push($ids, $assignedStudent);
        $assigned_students = array();
        // Get students base on instructor

        foreach($ids as $id){
            $assigned_students[] = Database::table('users')->where('id', $id)->first();
        }

        $index = 0;
        $hmtl = "";
        if(!empty($assigned_students)){
            foreach($assigned_students as $student){
                if($student->fname){
                    $alert_color = ($index++ % 2 == 0) ? 'alert-primary' : 'alert-danger';
                    $link = url('')."profile/".$student->id;
                    $name = $student->fname." ".$student->lname;

                    $hmtl .= "<div id='std_".$student->id."' class='p-2 alert ".$alert_color." ' role='alert'>
                                <a href='".$link."' class='alert-link'>".$name."</a>
                                <i id='".$student->id."' instructor='".$instructor."' class='fa fa-trash text-danger float-right mt-1 mr-1 delete_std' style='cursor: pointer;'></i>
                            </div>";
                }
            }

        }
        
        return response()->json(array( "status" => "success", "html" => $hmtl));
    }

    public function removeAssignStudent(){

        $instructor = input('instructor');
        $student    = input('student');
        $row = Database::table('instructor_students')->where('instructor_id',$instructor)->where('student_id',$student)->delete();

        $student_ids = Database::table('userinfo')->where('user', input('instructor'))->first();
        $students = $student_ids->student;

        return response()->json(array( "status" => "success"));
    }

    public function addTheoryLesson()
    {
        $user = preg_replace('/\s+/', ' ', input('user'));
        $type = input('type');
        $name = preg_replace('/\s+/', ' ', input('name'));
        $date = date('Y-m-d', strtotime(input('date')));
        $time = date('H:i:s', strtotime(input('time')));
        $min = input('min');

        $data = array(
            "user" => $user,
            "type" => $type,
            "name" => $name,
            "date" => $date,
            "time" => $time,
            "min" => $min,
        );

        Database::table('theorylessons')->insert($data);
        $theoryid = Database::table('theorylessons')->insertId();
        
        self::setScheduleToCalendar($theoryid, null, $date." ".$time, $date." ".$time, $user, null);
        ActivityTimeline::logActivity($user,ActivityTimeline::$ADD_THEORY_LESSON);
        
        return response()->json(responder("success", sch_translate("alright"), sch_translate("theory_lesson_successfully_added"), "reload()"));
    }

    public function updateTheoryLesson()
    {

        $theoryid = input('theoryid');
        // $user = preg_replace('/\s+/', ' ', input('user'));
        $type = input('type');
        $name = preg_replace('/\s+/', ' ', input('name'));
        $date = date('Y-m-d', strtotime(input('date')));
        $time = date('H:i:s', strtotime(input('time')));
        $min = input('min');

        $data = array(
            // "user" => $user,
            "type" => $type,
            "name" => $name,
            "date" => $date,
            "time" => $time,
            "min" => $min,
        );

        Database::table('theorylessons')->where('id', $theoryid)->update($data);

        self::updateScheduleToCalendar($theoryid, 'theory_id', $date." ".$time, $date." ".$time);
        
        return response()->json(responder("success", sch_translate("alright"), sch_translate("theory_lesson_successfully_updated"), "reload()"));
    }

    public function deleteTheoryLesson()
    {
        Database::table('theorylessons')->where("id", input("theoryid"))->delete();

        self::deleteScheduleFromCalendar(input("theoryid"), 'theory_id');

        return response()->json(responder("success", sch_translate("alright"), sch_translate("student_enrollment_successfully_deleted"), "reload()"));
    }

    public function addDrivingLesson()
    {
        $user = input('user');
        $type = input('type');
        $date = date('Y-m-d', strtotime(input('date')));
        $from = date('H:i:s', strtotime(input('from')));
        $to = date('H:i:s', strtotime(input('to')));
        $duration = input('duration');
        $instructor = input('instructor');

        $data = array(
            "dl_type" => $type,
            "dl_date" => $date,
            "dl_from" => $from,
            "dl_to" => $to,
            "dl_duration" => $duration,
            "dl_user" => $user,
            "dl_instructor" => $instructor
        );
        Database::table('drivinglessons')->insert($data);
        $drivingid = Database::table('drivinglessons')->insertId();  

        ActivityTimeline::logActivity($user,ActivityTimeline::$ADD_PRACTICAL_LESSON);
       
        self::setScheduleToCalendar(null, $drivingid, $date." ".$from, $date." ".$to, $user, $instructor);

        return response()->json(responder("success", sch_translate("alright"), sch_translate("driving_lesson_successfully_added"), "reload()"));
    }

    public function deleteDrivingLesson()
    {
        Database::table('drivinglessons')->where("id", input("drivingid"))->delete();

        self::deleteScheduleFromCalendar(input("drivingid"), 'driving_id');

        return response()->json(responder("success", sch_translate("alright"), sch_translate("driving_lesson_successfully_deleted"), "reload()"));
    }

    public function updateDrivingLesson()
    {
        $drivingid = input('drivingid');
        // $user = input('user');
        $type = input('type');
        $date = date('Y-m-d', strtotime(input('date')));
        $from = date('H:i:s', strtotime(input('from')));
        $to = date('H:i:s', strtotime(input('to')));
        $duration = input('duration');
        $instructor = input('instructor');

        $data = array(
            "dl_type" => $type,
            "dl_date" => $date,
            "dl_from" => $from,
            "dl_to" => $to,
            "dl_duration" => $duration,
            // "dl_user" => $user,
            "dl_instructor" => $instructor
        );

        Database::table('drivinglessons')->where('id', $drivingid)->update($data);

        self::updateScheduleToCalendar($drivingid, 'driving_id', $date." ".$from, $date." ".$to);
        
        return response()->json(responder("success", sch_translate("alright"), sch_translate("driving_lesson_successfully_updated"), "reload()"));
    }

    public function setScheduleToCalendar($theory_id, $driving_id, $start, $end, $student, $instructor)
    {
        $user = Auth::user();

        $data = array(
            'image' => "",
            'school' => $user->school,
            'branch' => $user->branch,
            'name' => 'N/A',
            'price' => 0.00,
            'duration' => 0,
            'period' => 'N/A',
            'practical_classes' => 0,
            'theory_classes' => 0,
            'status' => 'Unavailable'
        );
        Database::table('courses')->insert($data);
        $courseId = Database::table('courses')->insertId();

        if($theory_id){
            $data = array(
                'theory_id' => $theory_id,
                // 'driving_id' => $driving_id,
                'school'=>$user->school,
                'branch'=>$user->branch,
                'start'=>$start,
                'end'=>$end,
                'course'=>$courseId,
                'student'=>$student,
                'instructor'=>1, // Temp
                'class_type'=> 'Theory',
                'status'=>'New'
            );
        }
        else{
            $data = array(
                // 'theory_id' => $theory_id,
                'driving_id' => $driving_id,
                'school'=>$user->school,
                'branch'=>$user->branch,
                'start'=>$start,
                'end'=>$end,
                'course'=>$courseId,
                'student'=>$student,
                'instructor'=>$instructor,
                'class_type'=>'Practical',
                'status'=>'New'
            );
        }
        Database::table('schedules')->insert($data);
    }

    public function updateScheduleToCalendar($id, $type, $start, $end)
    {

        if($type == 'theory_id'){
            $data = array(
                'start'=>$start,
                'end'=>$end,
            );
        }
        else{
            $data = array(
                // 'driving_id' => $driving_id,
                // 'school'=>$user->school,
                // 'branch'=>$user->branch,
                'start'=>$start,
                'end'=>$end,
                // 'course'=>$courseId,
                // 'student'=>$student,
                // 'instructor'=>$instructor,
                // 'class_type'=>'Practical',
                // 'status'=>'New'
            );
        }
        Database::table('schedules')->where($type, $id)->update($data);
    }

    public function deleteScheduleFromCalendar($id = null, $type = 'theory_id')
    {
        // TODO
        // Get cousre id
        $schedule = Database::table('schedules')->where($type, $id)->first();
        // Delete schedule
        Database::table('schedules')->where($type, $id)->delete();
        // Delete course
        Database::table('courses')->where('id', $schedule->course)->delete();
    }
    
}