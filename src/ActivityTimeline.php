<?php 
namespace Simcify;

use Simcify\Auth;
use Simcify\Database;

class ActivityTimeline{
	
	public static 
	$LOG_CREATE_PROFILE = 'log_create_profile',
	$ADD_THEORY_LESSON= 'log_thory_lesson', 
	$ADD_PRACTICAL_LESSON = 'log_add_practical_lesson',
	$LOG_UPD_THEORY_LESSON = 'log_update_theory_lesson', 
	$LOG_UPD_PRACTICAL_LESSON = 'log_update_practical_lesson', 
	$LOG_DEL_THEORY_LESSON = 'log_delete_theory_lesson', 
	$LOG_DEL_PRACTICAL_LESSON = 'log_delete_practical_lesson';

	public static function logActivity($user,$activity){
		$done_by = Auth::user()->id;
		$data = array(
			'user_id' => $user,
			'activity' => $activity,
			'done_by' => $done_by
		);
		Database::table('activities')->insert($data);
		return true;
	}
	

	public static function getLog($user_id){
		$logs = array();
		$log = Database::table('activities')->where('user_id',$user_id)->where('active',1)->orderBy('created_at',false)->get();
		foreach ($log as $singleLog) {
			array_push($logs, array(
				'id' => $singleLog->id,
				'activity' => sch_translate($singleLog->activity),
				'done_by' => Database::table('users')->where('id',$singleLog->done_by)->first(),
				'created_at' => $singleLog->created_at
			));
		}
		return $logs;
	} 
}