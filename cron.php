<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once 'vendor/autoload.php';

use Simcify\Application;
use Simcify\Database;
use Simcify\Auth;
use Simcify\Mail;
use Simcify\Sms;

$app = new Application();

$today = date("Y-m-d");

/**
 * Update missed classes
 * 
 */
$schedules = Database::table("schedules")->where("status", "New")->where('start','<=',$today)->get();
if ( count($schedules) > 0 ) {
	foreach ($schedules as $schedule) {
		Database::table("schedules")->where("id", $schedule->id)->update(array("status" => "Missed"));
	}
}



/**
 * Send payment & class reminders
 * 
 */
$schools = Database::table("schools")->where("payment_reminders", "On")->orWhere("payment_reminders", "On")->get();
if ( count($schools) > 0 ) {
	foreach ($schools as $school) {
		$reminders = Database::table("reminders")->where("school", $school->id)->get();
		if ( count($reminders) > 0 ) {
			foreach ($reminders as $reminder) {
				if ($reminder->timing == "after_due") {
					$referenceDate = date('Y-m-d', strtotime($today. ' - '.$reminder->days.' days'));
				}else{
					$referenceDate = date('Y-m-d', strtotime($today. ' + '.$reminder->days.' days'));
				}
				$dayStart = $referenceDate." 00:00:00";
				$dayEnd = $referenceDate." 23:59:59";
				$range = $dayStart."' AND '".$dayEnd;
				if ($reminder->type == "payment") {
					$contents = Database::table("invoices")->where("amountpaid","<", "amount")->where('created_at','BETWEEN',$range)->get();
				}else{
					$contents = Database::table("schedules")->where("status", "New")->where('start','BETWEEN',$range)->get();
				}
				if ( count($contents) > 0 ) {
					foreach ($contents as $content) {
						$student = Database::table("users")->where("id", $content->student)->first();
						$course = Database::table("courses")->where("id", $content->course)->first();
						if ($reminder->type == "payment") {
							$search = array("[firstname]","[lastname]","[course]","[amountdue]");
							$replace = array($student->fname,$student->lname,$course->name, money($content->amount - $content->amountpaid));
						}else{
							$instructor = Database::table("users")->where("id", $content->instructor)->first();
							$search = array("[firstname]","[lastname]","[course]","[classdate]","[classtime]","[instructorname]");
							$replace = array($student->fname,$student->lname,$course->name, date('d F Y', strtotime($content->start)), date('h:i a', strtotime($content->start)), $instructor->fname." ".$instructor->lname);
						}
						$message = str_replace($search, $replace, $reminder->message);
						if ($reminder->send_via == "email") {
							Mail::send(
				                $student->email, $reminder->subject,
				                array(
				                    "message" => $message
				                ),
				                "basic"
				            );
						}else{
							if (!empty($student->phone)) {
								if (env("DEFAULT_SMS_GATEWAY") == "africastalking") {
									Sms::africastalking($student->phone, $message);
								}else{
									Sms::twilio($student->phone, $message);
								}
							}else{
								continue;
							}
						}
					}
				}else{
					continue;
				}
			}
		}else{
			continue;
		}
	}
}
