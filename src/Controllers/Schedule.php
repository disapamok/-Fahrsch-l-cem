<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Google;
use Simcify\Landa;
use Simcify\Auth;
use Simcify\Sms;

class Schedule{

    /**
     * Get scheduling view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {

        $user = Auth::user();
        if (isset($_GET['filter']) && !empty($_GET['filter']) && isset($_GET['filterid']) && !empty($_GET['filterid'])) {
            $filter = $_GET['filter'];
            $filterid = $_GET['filterid'];
        }else{
            $filterid = 0;
            $filter = "none";
        }
        if(isset($_GET['code'])) {
            try {
                $data = Google::GetAccessToken($_GET['code']);
                Database::table('users')->where('id',$user->id)->update(array("google_access_token" => $data['access_token']));
                self::calendarSync();
                redirect(url("Schedule@get"));
            }
            catch(\Exception $e) {
                //  error
                echo "<h1>oops!</h1><br>";
                echo 'Something went wrong<br><p>Please click <a href="'.url("Schedule@get").'">'.url("Schedule@get").'</a> to go back and try again</p>';
                exit();
            }
        }
        $fleets = Database::table('fleet')->where('branch',$user->branch)->get();
        $courses = Database::table('courses')->where('school',$user->school)->where('status',"Available")->get();
        $instructors = Database::table('users')->where(['role'=>'instructor','branch'=>$user->branch,'school'=>$user->school])->get();
        $students = Database::table('users')->where(['role'=>'student','branch'=>$user->branch,'school'=>$user->school])->get();
        $googleCalendarUrl = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/calendar') . '&redirect_uri=' . urlencode(env("APP_URL")."/scheduling") . '&response_type=code&client_id=' . env("GOOGLE_CLIENT_ID") . '&access_type=online';


        return view('scheduling', compact("courses","instructors","students","fleets","user","filter","filterid","googleCalendarUrl"));
    }

    /**
     * Create Schedule
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        $school = Database::table('schools')->where('id',$user->school)->first();
        $course = Database::table('courses')->where('id',input('course'))->first();
        $startTime = self::landaDate(input('start'));
        $endTime = self::landaDate(input('end'));
        if ($endTime <= $startTime) {
            return response()->json(responder("warning", sch_translate("time_overlap"), sch_translate("class_end_time_must_be_greater_than_start_time")));
        }
        if (input("class_type") == "Practical" && $school->multibooking == "Disabled") {
            $overlap = Database::table('schedules')->where('branch',$user->branch)
                                                    ->where('class_type',"Practical")->where('start','<',$startTime)->where('end','>',$startTime)
                                                    ->orWhere('branch',$user->branch)
                                                    ->where('class_type',"Practical")->where('start','<',$endTime)->where('end','>',$endTime)
                                                    ->orWhere('branch',$user->branch)
                                                    ->where('class_type',"Practical")->where('start',$startTime)->where('end',$endTime)
                                                    ->first();
            if (!empty($overlap)) {
                return response()->json(responder("warning", sch_translate("schedule_overlap"), sch_translate("another_class_is_scheduled_to_start_at___1____and_end_at___2__", [date('j M Y H:i:s', strtotime($overlap->start)), date('j M Y H:i:s', strtotime($overlap->end))])));
            }
        }
        
        if (input('car')) {
            $data = array(
                'school'=>$user->school,
                'branch'=>$user->branch,
                'start'=>$startTime,
                'end'=>$endTime,
                'course'=>input('course'),
                'student'=>input('student'),
                'instructor'=>input('instructor'),
                'class_type'=>input('class_type'),
                'car'=>input('car'),
                'status'=>input('status')
            );
        } else {
            $data = array(
                'school'=>$user->school,
                'branch'=>$user->branch,
                'start'=>$startTime,
                'end'=>$endTime,
                'course'=>input('course'),
                'student'=>input('student'),
                'instructor'=>input('instructor'),
                'class_type'=>input('class_type'),
                'status'=>input('status')
            );
        }
        

        // put on google calendar
        if(!empty(env("GOOGLE_CLIENT_ID")) && !empty(env("GOOGLE_CLIENT_SECRECT"))){

  
            $student = Database::table('users')->where('id', input('student'))->first();
            $instructor = Database::table('users')->where('id', input('instructor'))->first();
            $classTime = array( 
                "start_time" => str_replace(" ", "T", $startTime), 
                "end_time" => str_replace(" ", "T", $endTime), 
                "event_date" => date('Y-m-d', strtotime($startTime)) );
            if(!empty($student->google_access_token)){
                try {
                    // Get event details
                    $calTitle = sch_translate("class_with_instructor", [input('class_type'), $instructor->fname, $instructor->lname]);
                    $userTimezone = Google::GetUserCalendarTimezone($student->google_access_token);
                    $data['student_google_id'] = Google::CreateCalendarEvent('primary', $calTitle, 0 , $classTime, $userTimezone, $student->google_access_token);
                }
                catch(\Exception $e) {
                    //  error
                }
            }
            if(!empty($instructor->google_access_token)){
                try {
                    // Get event details
                    $calTitle = sch_translate("class_with_student", [input('class_type'), $student->fname, $student->lname]);
                    $userTimezone = Google::GetUserCalendarTimezone($instructor->google_access_token);
                    $data['instructor_google_id'] = Google::CreateCalendarEvent('primary', $calTitle, 0 , $classTime, $userTimezone, $instructor->google_access_token);
                }
                catch(\Exception $e) {
                    //  error
                }
            }
        }
        
        // end google calendar
        if (input('status') == "Complete") {

            $timeline = sch_translate("completed_a_class_for", [input('class_type'), $course->name]);

            Landa::timeline(input('student'), $timeline);  
            $enrollment =  Database::table('coursesenrolled')->where('course', input('course'))->where('student', input('student'))->first();
            if (input('class_type') == "Theory" && !empty($enrollment)) {
                Database::table('coursesenrolled')->where('id', $enrollment->id)->update('completed_theory', $enrollment->completed_theory + 1);
            }elseif(input('class_type') == "Practical" && !empty($enrollment)){
                Database::table('coursesenrolled')->where('id', $enrollment->id)->update('completed_practical', $enrollment->completed_practical + 1);
            }
            if (!empty($enrollment)) {
                $total = $enrollment->total_theory + $enrollment->total_practical;
                $completed = $enrollment->completed_theory + $enrollment->completed_practical + 1;
                if ($total == $completed) {
                    $timeline = sch_translate("completed_a_named_course", [$course->name]);
                    Landa::timeline(input('student'), $timeline);  
                    $complete = array("completed_on" => date("Y-m-d"), "status" => "Complete");
                    Database::table('coursesenrolled')->where('id', $enrollment->id)->update($complete);
                }
            }
        }
         
        Database::table('schedules')->insert($data);

        $scheduleId = Database::table('schedules')->insertId();

        if ($school->class_sms_notifications == "Enabled" && input('status') == "New") {
            self::smsnotification($data, $scheduleId);
        }
        return response()->json(responder("success", sch_translate("schedule_set"), sch_translate("class_schedule_successfully_saved"),"reload()"));

     }


    /**
     * Update date format
     * 
     * @return date time
     */
        public function landaDate($string){
            $array = explode(" - ", $string);
            $date = date('Y-m-d', strtotime($array[0]));
            $time = date('H:i:s', strtotime($array[1]));
            $datetime = $date." ".$time;
            return $datetime;
        }


    /**
     * Fetch schedules
     * 
     * @return array
     */
        public function fetch(){


            error_reporting(0);
            $user = Auth::user();
            $table = Database::table('schedules');
            $schedules = $ready = $filter = array();
            $groups = array(
                "practical" => array(
                        "label" => "practical",
                        "class" => "success",
                        "title" => sch_translate("practical_classes"),
                        "data" => array()
                    ),
                "complete" => array(
                        "label" => "complete",
                        "class" => "muted",
                        "title" => sch_translate("completed_classes"),
                        "data" => array()
                    ),
                "missed" => array(
                        "label" => "missed",
                        "class" => "danger",
                        "title" => sch_translate("missed_classes"),
                        "data" => array()
                    ),
                "theory" => array(
                        "label" => "theory",
                        "class" => "primary",
                        "title" => sch_translate("theory_classes"),
                        "data" => array()
                    )
            );
            $type = input("type");

            $date = input("date");

            if (input("filter") != "none") {
                $filter = array(input("filter") => input("filterid"));
            }

            if ($type == "month") {
                $month = date('m', strtotime($date));
                $year = date('Y', strtotime($date));
                $base = array_merge($filter, array('branch'=>$user->branch, 'MONTH(`start`)' => $month, 'YEAR(`start`)' => $year));

                $groups['missed']['data'] = Database::table('schedules')->where(array_merge($base, array('status'=>"Missed")))->get("start");
              
                $groups['complete']['data'] = Database::table('schedules')->where(array_merge($base, array('branch'=>$user->branch,'status'=>"Complete")))->get("start");
                $groups['theory']['data'] = Database::table('schedules')->where(array_merge($base, array('status'=>"New",'class_type'=>"Theory")))->get("start");
                $groups['practical']['data'] = Database::table('schedules')->where(array_merge($base, array('status'=>"New",'class_type'=>"Practical")))->get("start");

                foreach ($groups as $group) {
                    foreach ($group['data'] as $class) {
                        $schedules[date('Y-m-d', strtotime($class->start))][$group['label']]['count'] += 1;
                        $schedules[date('Y-m-d', strtotime($class->start))][$group['label']]['allDay'] = 1;
                        $schedules[date('Y-m-d', strtotime($class->start))][$group['label']]['id'] = 0;
                        $schedules[date('Y-m-d', strtotime($class->start))][$group['label']]['class'] = $group['class'];
                        $schedules[date('Y-m-d', strtotime($class->start))][$group['label']]['title'] = $group['title'];
                        $schedules[date('Y-m-d', strtotime($class->start))][$group['label']]['description'] = $group['title'];
                        $schedules[date('Y-m-d', strtotime($class->start))][$group['label']]['start'] = date('Y-m-d', strtotime($class->start));
                        $schedules[date('Y-m-d', strtotime($class->start))][$group['label']]['end'] = date('Y-m-d', strtotime($class->start));
                    }
                }
                
             
                foreach ($schedules as $schedule) {
                    foreach ($groups as $group) {
                        if (isset($schedule[$group['label']])) {
                            $class = $schedule[$group['label']];

                            if (isset($class['count'])) {
                                $class['title'] = $class['count']." ".$class['title'];
                                $class['description'] = $class['count']." ".$class['description'];
                            }
                            $ready[] = array( 'id' => $class['id'], 'title' => $class['title'], 'description' => $class['description'], 'start' => $class['start'], 'end' => $class['end'], 'className' => $class['class'], 'allDay' => $class['allDay']);
                        }
                    }
                }


            }else{
                if ($type == "agendaDay") {
                    $agendaDayStart = date('Y-m-d', strtotime($date))." 00:00:00";
                    $agendaDayEnd = date('Y-m-d', strtotime($date))." 23:59:59";
                    $range = $agendaDayStart."' AND '".$agendaDayEnd;
                }elseif ($type == "agendaWeek") {
                    $dateArrayOne = explode(", ", $date);
                    $year = $dateArrayOne[1];
                    $dateArrayTwo = explode(" – ", $dateArrayOne[0]);
                    $monthOverlap = explode(" ", $dateArrayTwo[1]);
                    if (isset($monthOverlap[1])) {
                        $firstDayOfWeek = $year."-".date('m-d', strtotime($dateArrayTwo[0]))." 00:00:00";
                        $lastDayOfWeek = $year."-".date('m-d', strtotime($dateArrayTwo[1]))." 23:59:59";
                    }else{
                        $month = date('m', strtotime($dateArrayTwo[0]));
                        $firstDayOfWeek = $year."-".$month."-".date('d', strtotime($dateArrayTwo[0]))." 00:00:00";
                        $lastDayOfWeek = $year."-".$month."-".$dateArrayTwo[1]." 23:59:59";
                    }
                    $range = $firstDayOfWeek."' AND '".$lastDayOfWeek;
                }
                 $groups['missed']['data'] = Database::table('schedules')->where('schedules`.`branch',$user->branch)
                                                                            ->where('start','BETWEEN',$range)
                                                                            ->where(array_merge($filter, array('schedules`.`status'=>"Missed")))     
                                                                            ->leftJoin("users", "users.id", "student")->get("`users.fname`", "`users.lname`", "`schedules.start`", "`schedules.end`", "`schedules.class_type`", "`schedules.id`");
                 $groups['complete']['data'] = Database::table('schedules')->where('schedules`.`branch',$user->branch)
                                                                            ->where('start','BETWEEN',$range)
                                                                            ->where(array_merge($filter, array('schedules`.`status'=>"Complete")))     
                                                                            ->leftJoin("users", "users.id", "student")->get("`users.fname`", "`users.lname`", "`schedules.start`", "`schedules.end`", "`schedules.class_type`", "`schedules.id`");
                 $groups['theory']['data'] = Database::table('schedules')->where('schedules`.`branch',$user->branch)
                                                                            ->where('start','BETWEEN',$range)
                                                                            ->where(array_merge($filter, array('schedules`.`status'=>"New",'class_type'=>"Theory")))     
                                                                            ->leftJoin("users", "users.id", "student")->get("`users.fname`", "`users.lname`", "`schedules.start`", "`schedules.end`", "`schedules.class_type`", "`schedules.id`");
                 $groups['practical']['data'] = Database::table('schedules')->where('schedules`.`branch',$user->branch)
                                                                            ->where('start','BETWEEN',$range)
                                                                            ->where(array_merge($filter, array('schedules`.`status'=>"New",'class_type'=>"Practical")))     
                                                                            ->leftJoin("users", "users.id", "student")->get("`users.fname`", "`users.lname`", "`schedules.start`", "`schedules.end`", "`schedules.class_type`", "`schedules.id`");

                

                foreach ($groups as $group) {
                    if (!empty($group['data'])) {
                        foreach($group['data'] as $class){
                            $allDay = false;
                            $title = $class->fname." ".$class->lname." - ".$class->class_type;
                            $description = sch_translate("has_a_class_from_to", [($class->fname." ".$class->lname), $class->class_type, date('h:ia l F, Y', strtotime($class->start)), date('h:i a l F, Y', strtotime($class->end))]);

                            $ready[] = array( 'id' => $class->id, 'title' => $title, 'description' => $description, 'start' => $class->start, 'end' => $class->end, 'className' => $group['class'], 'allDay' => $allDay);
    
                        }
                    }
                }
            }
            return response()->json($ready);
        }

    /**
     * Delete schedule
     * 
     * @return Json
     */
    public function delete() {
        $schedule = Database::table('schedules')->where('id',input("scheduleid"))->first();
        $school = Database::table('schools')->where('id',$schedule->school)->first();
        $student = Database::table('users')->where('id',$schedule->student)->first();
        $instructor = Database::table('users')->where('id',$schedule->instructor)->first();
        
        // delete on google calendar
        if(!empty(env("GOOGLE_CLIENT_ID")) && !empty(env("GOOGLE_CLIENT_SECRECT"))){
            if(!empty($student->google_access_token) && !empty($schedule->student_google_id)){
                try {
                    Google::DeleteCalendarEvent('primary', $schedule->student_google_id, $student->google_access_token);
                }
                catch(\Exception $e) {
                //  error
                }
            }
            if(!empty($instructor->google_access_token) && !empty($schedule->instructor_google_id)){
                try {
                //  Google::DeleteCalendarEvent('primary', $schedule->instructor_google_id, $instructor->google_access_token);
                    Google::DeleteCalendarEvent('primary', $schedule->instructor_google_id, "kkk");
                }
                catch(\Exception $e) {
                    // error
                }
            }
        }
        // end google calendar
        // exit();
            
        if ($school->class_sms_notifications == "Enabled") {
            $course = Database::table('courses')->where('id',$schedule->course)->first();
            $studentMessage = sch_translate("class_has_been_cancelled", [$student->fname, $schedule->class_type, $course->name, date('h:i a - d F Y', strtotime($schedule->start)), date('h:i a - d F Y', strtotime($schedule->end))]);

            if (!empty($student->phone)) {
                $send = Sms::africastalking($student->phone, $studentMessage);
                if ($send) { $status = "Sent"; } else { $status = "Failed"; }
                Database::table("usermessages")->insert(array(
                    "receiver" => $student->id, "type" => "sms", "contact" => $student->phone,
                    "message" => escape($studentMessage),
                    "school" => $student->school, "branch" => $student->branch, "status" => $status
                ));
            }
        }
        Database::table("schedules")->where("id", input("scheduleid"))->delete();
        return response()->json(responder("success", sch_translate("class_deleted"), sch_translate("class_successfully_removed_from_schedule"), "reload()"));
    }
    
    /**
     * Schedule update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        // var_dump(input('scheduleid'));
        // die;
        $user = Auth::user();
        $fleets = Database::table('fleet')->where('branch',$user->branch)->get();
        $courses = Database::table('courses')->where('school',$user->school)->where('status',"Available")->get();
        $instructors = Database::table('users')->where(['role'=>'instructor','branch'=>$user->branch,'school'=>$user->school])->get();
        $students = Database::table('users')->where(['role'=>'student','branch'=>$user->branch,'school'=>$user->school])->get();
        $schedule = Database::table("schedules")->where("id", input("scheduleid"))->first();
        return view('extras/updateschedule', compact("courses","instructors","schedule","students","fleets","user"));
    }
    
    /**
     * Update schedule
     * 
     * @return Json
     */
    public function update() {
        // var_dump(input());
        // die;
        $user = Auth::user();
        $school = Database::table('schools')->where('id', $user->school)->first();
        $course = Database::table('courses')->where('id',input('course'))->first();
        $startTime = self::landaDate(input('start'));
        $endTime = self::landaDate(input('end'));
        if ($endTime <= $startTime) {
            return response()->json(responder("warning", sch_translate("time_overlap"), sch_translate("class_end_time_must_be_greater_than_start_time")));
        }
        if (input("class_type") == "Practical" && $school->multibooking == "Disabled") {
            $overlap = Database::table('schedules')->where('branch',$user->branch)->where('id','!=',input("scheduleid"))
                                                    ->where('class_type',"Practical")->where('start','<',$startTime)->where('end','>',$startTime)
                                                    ->orWhere('branch',$user->branch)->where('id','!=',input("scheduleid"))
                                                    ->where('class_type',"Practical")->where('start','<',$endTime)->where('end','>',$endTime)
                                                    ->orWhere('branch',$user->branch)->where('id','!=',input("scheduleid"))
                                                    ->where('class_type',"Practical")->where('start',$startTime)->where('end',$endTime)
                                                    ->first();
            if (!empty($overlap)) {
                return response()->json(responder("warning", sch_translate("schedule_overlap"), sch_translate("another_class_is_scheduled_to_start_at___1____and_end_at___2__", [date('j M Y H:i:s', strtotime($overlap->start)), date('j M Y H:i:s', strtotime($overlap->end))])));
            }
        }
        $data = array(
            'start'=>$startTime,
            'end'=>$endTime,
            'course'=>input('course'),
            'student'=>input('student'),
            'instructor'=>input('instructor'),
            'class_type'=>input('class_type'),
            'car'=>input('car'),
            'status'=>input('status')
        );
        // end google calendar
        if(!empty(env("GOOGLE_CLIENT_ID")) && !empty(env("GOOGLE_CLIENT_SECRECT"))){
            $schedule = Database::table('schedules')->where('id',input("scheduleid"))->first();
            $student = Database::table('users')->where('id', input('student'))->first();
            $instructor = Database::table('users')->where('id', input('instructor'))->first();
            $classTime = array( 
                "start_time" => str_replace(" ", "T", $startTime), 
                "end_time" => str_replace(" ", "T", $endTime), 
                "event_date" => date('Y-m-d', strtotime($startTime)) );
                
            if(!empty($student->google_access_token) && !empty($schedule->student_google_id)){
                try {
                    Google::DeleteCalendarEvent('primary', $schedule->student_google_id, $student->google_access_token);
                    $data['student_google_id'] = '';
                }
                catch(\Exception $e) {
                //  error
                }
            }
            if(!empty($instructor->google_access_token) && !empty($schedule->instructor_google_id)){
                try {
                    Google::DeleteCalendarEvent('primary', $schedule->instructor_google_id, $instructor->google_access_token);
                    $data['instructor_google_id'] = '';
                }
                catch(\Exception $e) {
                    // error
                }
            }
            if(!empty($student->google_access_token)){
                try {
                    // Get event details
                    $calTitle = sch_translate("class_with_instructor", [input('class_type'), $instructor->fname, $instructor->lname]);
                    $userTimezone = Google::GetUserCalendarTimezone($student->google_access_token);
                    $data['student_google_id'] = Google::CreateCalendarEvent('primary', $calTitle, 0 , $classTime, $userTimezone, $student->google_access_token);
                }
                catch(\Exception $e) {
                    //  error
                }
            }
            if(!empty($instructor->google_access_token)){
                try {
                    // Get event details
                    $calTitle = sch_translate("class_with_student", [input('class_type'), $student->fname, $student->lname]);
                    $userTimezone = Google::GetUserCalendarTimezone($instructor->google_access_token);
                    $data['instructor_google_id'] = Google::CreateCalendarEvent('primary', $calTitle, 0 , $classTime, $userTimezone, $instructor->google_access_token);
                }
                catch(\Exception $e) {
                    //  error
                }
            }
        }
        // var_dump(input("scheduleid"));
        // die;
        // end google calendar
        Database::table('schedules')->where('id',input("scheduleid"))->update($data);

        if (input('status') == "Complete") {
            $timeline = sch_translate("completed_a_class_for", [input('class_type'), $course->name]);
            Landa::timeline(input('student'), $timeline);  
            $enrollment =  Database::table('coursesenrolled')->where('course', input('course'))->where('student', input('student'))->first();
            if (input('class_type') == "Theory" && !empty($enrollment)) {
                Database::table('coursesenrolled')->where('id', $enrollment->id)->update('completed_theory', $enrollment->completed_theory + 1);
            }elseif(input('class_type') == "Practical" && !empty($enrollment)){
                Database::table('coursesenrolled')->where('id', $enrollment->id)->update('completed_practical', $enrollment->completed_practical + 1);
            }
            if (!empty($enrollment)) {
                $total = $enrollment->total_theory + $enrollment->total_practical;
                $completed = $enrollment->completed_theory + $enrollment->completed_practical + 1;
                if ($total == $completed) {
                    $timeline = sch_translate("completed_a_named_course", [$course->name]);
                    Landa::timeline(input('student'), $timeline);  
                    $complete = array("completed_on" => date("Y-m-d"), "status" => "Complete");
                    Database::table('coursesenrolled')->where('id', $enrollment->id)->update($complete);
                }
            }
        }

        if ($school->class_sms_notifications == "Enabled" && input('status') == "New") {
            self::smsnotification($data, input("scheduleid"));
        }
        return response()->json(responder("success", sch_translate("schedule_updated"), sch_translate("class_schedule_successfully_updated"),"reload()"));
    }
    
    /**
     * Schedule sms notification
     * 
     * @return Json
     */
    public function smsnotification($scheduledata, $scheduleid) {
        $course = Database::table('courses')->where('id',$scheduledata['course'])->first();
        $student = Database::table('users')->where('id',$scheduledata['student'])->first();
        $schedule = Database::table('schedules')->where('id',$scheduleid)->first();
        $studentMessage = "Hello ".$student->fname.", a ".$schedule->class_type." class for your ".$course->name." course is scheduled for ".date('h:i a - d F Y', strtotime($schedule->start))." to ".date('h:i a - d F Y', strtotime($schedule->end));
        if (!empty($student->phone)) {
            $send = Sms::africastalking($student->phone, $studentMessage);
            if ($send) { $status = "Sent"; } else { $status = "Failed"; }
            Database::table("usermessages")->insert(array(
                "receiver" => $student->id, "type" => "sms", "contact" => $student->phone,
                "message" => escape($studentMessage),
                "school" => $student->school, "branch" => $student->branch, "status" => $status
            ));
        }
    }
    
    /**
     * Sync schedule with google calendar
     * 
     * @return Json
     */
    public function calendarSync() {
        $user = Auth::user();
        $schedules = Database::table('schedules')->where($user->role , $user->id)->get();
        
        if(!empty($schedules)){
            foreach($schedules as $schedule){
                $student = Database::table('users')->where('id', $schedule->student)->first();
                $instructor = Database::table('users')->where('id', $schedule->instructor)->first();
                $classTime = array( 
                    "start_time" => str_replace(" ", "T", $schedule->start), 
                    "end_time" => str_replace(" ", "T", $schedule->end), 
                    "event_date" => date('Y-m-d', strtotime($schedule->start)) );
                if($user->role == "student"){
                    try {
                        // Get event details
                        $calTitle = sch_translate("class_with_instructor", [$schedule->class_type, $instructor->fname, $instructor->lname]);
                        $userTimezone = Google::GetUserCalendarTimezone($user->google_access_token);
                        $data['student_google_id'] = Google::CreateCalendarEvent('primary', $calTitle, 0 , $classTime, $userTimezone, $user->google_access_token);
                    }
                    catch(\Exception $e) {
                        //  error
                    }
                }
                if($user->role == "instructor"){
                    try {
                        // Get event details
                        $calTitle = sch_translate("class_with_student", [$schedule->class_type, $student->fname, $student->lname]);
                        $userTimezone = Google::GetUserCalendarTimezone($user->google_access_token);
                        $data['instructor_google_id'] = Google::CreateCalendarEvent('primary', $calTitle, 0 , $classTime, $userTimezone, $user->google_access_token);
                    }
                    catch(\Exception $e) {
                        //  error
                    }
                }
            }
        }
        return true;
    }



}
