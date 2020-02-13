<?php
namespace Simcify;

use Simcify\Database;
use Simcify\Auth;

class Landa {
    
    /**
     * Delete file
     * 
     * @param   array|string $file
     * @return  true
     */
    public static function notify($message, $user, $type, $class = "branch") {
        $user = Database::table('users')->where('id',$user)->first();
        $data  = array(
            'user' => $user->id,
            'school' => $user->school,
            'branch' => $user->branch,
            'type' => $type,
            'class' => $class,
            'message' => $message
        );
        $insert = Database::table('notifications')->insert($data);

        // check if user exist in notificationindicator
        $checkIfExist = Database::table('notificationindicator')->where('user', $user->id)->first();

        if($checkIfExist){
            // update
            $data  = array('is_display' => 1);
            Database::table('notificationindicator')->where('user', $user->id)->update($data);
        }else{
            $data  = array('user' => $user->id);
            // Insert new
            Database::table('notificationindicator')->insert($data);
        }
    }

    /**
     * Add timeline activities 
     * 
     * @return true
     */
    public static function timeline($user, $message) {
        $activity = array(
            'user'=>$user,
            'activity'=>$message
        );
        Database::table('timeline')->insert($activity);
        return true;
    }   
    
}
