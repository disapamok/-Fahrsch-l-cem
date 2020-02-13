<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;
use Simcify\File;

class Fleet {
    
    /**
     * Get fleet view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user        = Auth::user();
        $instructors = Database::table('users')->where(array(
            'branch' => $user->branch,
            'role' => 'instructor'
        ))->get();
        $fleet       = Database::table('fleet')->where('branch', $user->branch)->get();
        foreach ($fleet as $car) {
            if (empty($car->instructor)) {
                $car->instructor = "Un-Assigned";
            } else {
                $instructor = Database::table('users')->where('id', $car->instructor)->first();
                if (!empty($instructor)) {
                    $car->instructor = $instructor->fname . " " . $instructor->lname;
                } else {
                    $car->instructor = "Un-Assigned";
                }
            }
        }
        return view('fleet', compact("user", "instructors", "fleet"));
    }
    
    /**
     * Add fleet
     * 
     * @return Json
     */
    public function add() {
        $user = Auth::user();
        $data = array(
            'carno_' => escape(input('carno')),
            'carplate' => escape(input('carplate')),
            'make' => escape(input('make')),
            'model' => escape(input('model')),
            'modelyear' => escape(input('modelyear')),
            'instructor' => escape(input('instructor')),
            'branch' => $user->branch,
            'school' => $user->school
        );
        Database::table('fleet')->insert($data);
        return response()->json(responder("success", sch_translate("car_added"), sch_translate("car_successfully_added_to_fleet"), "reload()"));
    }
    
    
    /**
     * Delete car
     * 
     * @return Json
     */
    public function delete() {
        Database::table("fleet")->where("id", input("carid"))->delete();
        return response()->json(responder("success", sch_translate("car_deleted"), sch_translate("car_successfully_deleted_flom_fleet"), "reload()"));
    }
    
    /**
     * Car update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        $user        = Auth::user();
        $instructors = Database::table('users')->where(array(
            'branch' => $user->branch,
            'role' => 'instructor'
        ))->get();
        $car         = Database::table("fleet")->where("id", input("carid"))->first();
        return view('extras/updatecar', compact("car", "instructors"));
    }
    
    /**
     * Update Car
     * 
     * @return Json
     */
    public function update() {
        $data = array(
            'carno_' => escape(input('carno')),
            'carplate' => escape(input('carplate')),
            'make' => escape(input('make')),
            'model' => escape(input('model')),
            'instructor' => escape(input('instructor')),
            'modelyear' => escape(input('modelyear'))
        );
        Database::table("fleet")->where("id", input("carid"))->update($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("car_successfully_updated"), "reload()"));
    }
    
}