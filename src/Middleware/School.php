<?php
namespace Simcify\Middleware;

use Simcify\Auth;
use Simcify\Database;

class School {

    /**
     * Get settings view
     * 
     * @return \Pecee\Http\Response
     */
    public static function setup() {
            $user = Auth::user();
            $school = Database::table("schools")->where("id", $user->school)->first();
            return $school;
    }

}