<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

header("Access-Control-Allow-Origin: *"); 
class InstructorApi{


    /**
     * Get students
     * 
     * @return Json
     */
    public function getAllKalenderInstructor() {

        $user = (Auth::user()) ? Auth::user() : null;

        $instructor = null;

        if (isset($_GET['id'])) 
            $id = $_GET['id'];
        else
            $id = null;
        
        if(isset($_GET['code'])) {
            try {
                $data = Google::GetAccessToken($_GET['code']);
                if($user->id){
                    Database::table('users')->where('id',$user->id)->update(array("google_access_token" => $data['access_token']));
                    self::calendarSync();
                }
            }
            catch(\Exception $e) {
                //  error
                echo "<h1>oops!</h1><br>";
                echo 'Something went wrong<br><p>Please click <a href="'.url("Schedule@get").'">'.url("Schedule@get").'</a> to go back and try again</p>';
                exit();
            }
        }

        if($user){
            $fleets = Database::table('fleet')->where('branch', $user->branch)->get();
            $courses = Database::table('courses')->where('school',$user->school)->where('status',"Available")->get();

            if($id)
                $instructor = Database::table('users')->where(['id' => $id, 'role'=>'instructor','branch'=>$user->branch,'school'=>$user->school])->get();

        }
        else{
            $fleets = Database::table('fleet')->get();
            $courses = Database::table('courses')->where('status',"Available")->get();

            if($id)
                $instructor = Database::table('users')->where(['id' => $id, 'role'=>'instructor'])->get();

        }
        
        $googleCalendarUrl = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/calendar') . '&redirect_uri=' . urlencode(env("APP_URL")."/scheduling") . '&response_type=code&client_id=' . env("GOOGLE_CLIENT_ID") . '&access_type=online';

        if(!$id)
            $msg = array("msg" => "Provide valid id of instructor");
        else
            $msg = array("msg" => "Result not found");

        if($instructor)
            $result = array("fleets" => $fleets, "courses" => $courses, "instructor" => $instructor, "googleCalendarUrl" => $googleCalendarUrl);
        else
            $result = $msg;

        return response()->json($result);
    }

    public function getAllAssignedInstructor() {

        if (isset($_GET['id'])) 
            $id = $_GET['id'];
        else
            $id = null;

        // Get all instructors first base on class


        $query1 = "SELECT * FROM (SELECT * FROM schedules) as `schedules` LEFT JOIN users on users.`id` = schedules.instructor GROUP BY schedules.instructor";   

        $instructors = Database::table('users')->getResultsArray($query1);
    
        
        // Single result
        if($id)
            $query = "select * from (select * from schedules where id = ".$id.") as `schedules` LEFT JOIN users on users.`id` = schedules.student";   
        else
            $query = "select * from (select * from schedules) as `schedules` LEFT JOIN users on users.`id` = schedules.student";   

        $tbl = Database::table('users');
        $studentsClass = $tbl->getResultsArray($query);
        // Loop all students with details
        for ($i=0; $i < count($studentsClass); $i++) { 
            // Loop all instructors with details
            // If found then merge instructor details
            for ($ii=0; $ii < count($instructors); $ii++) { 
                if($studentsClass[$i]['instructor'] == $instructors[$ii]['instructor']){
                    $studentsClass[$i]['instructor'] = $instructors[$ii];
                    continue;
                }
            }
        }

        return response()->json($studentsClass);
    }

}
