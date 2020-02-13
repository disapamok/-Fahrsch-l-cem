<?php
namespace Simcify\Controllers;

use Simcify\Database;

header("Access-Control-Allow-Origin: *");

class StudentApi{

    /**
     * Get students
     * 
     * @return Json
     */
    public function getAllStudents() {


        if (isset($_GET['id'])) 
            $id = $_GET['id'];
        else
            $id = null;

        if($id)
            $students = Database::table("users")->where("role", 'student')->where('id', $id)->get();
        else
            $students = Database::table("users")->where("role", 'student')->get();

        return response()->json($students);
    }

    public function getAllStudentsDetails() {

        if (isset($_GET['id'])) 
            $id = $_GET['id'];
        else
            $id = null;
        
        // Single result
        if($id)
            $query = "select * from (select * from users where role = 'student' AND id = ".$id.") as `users` LEFT JOIN userinfo on userinfo.`user` = users.id WHERE userinfo.id IS NOT NULL";   
        else
            $query = "select * from (select * from users where role = 'student') as `users` LEFT JOIN userinfo on userinfo.`user` = users.id WHERE userinfo.id IS NOT NULL";   

        $tbl = Database::table('userinfo');
        $students = $tbl->getResultsArray($query);

        return response()->json($students);
    }

    public function getAllStudentsClass() {

        if (isset($_GET['id'])) 
            $id = $_GET['id'];
        else
            $id = null;

        // Single result
        if($id)
            $query = "select * from (select * from users where role = 'student' AND id = ".$id.") as `users` LEFT JOIN schedules on schedules.`student` = users.id WHERE schedules.id IS NOT NULL";   
        else
            $query = "select * from (select * from users where role = 'student') as `users` LEFT JOIN schedules on schedules.`student` = users.id WHERE schedules.id IS NOT NULL";   

        $tbl = Database::table('schedules');
        $studentsClass = $tbl->getResultsArray($query);

        return response()->json($studentsClass);
    }

}
