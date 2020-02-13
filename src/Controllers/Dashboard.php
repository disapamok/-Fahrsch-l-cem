<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;

class Dashboard{

    /**
     * Get dashboard view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->role == "student" || $user->role == "instructor") {
            redirect(url("Schedule@get"));
        }
        $school = Database::table('schools')->where('id',$user->school)->first();
        $totals = self::totals($user);
        $current = self::current($user);
        $previous = self::previous($user);
        $growth = self::growth($current, $previous);
        $students = self::students($user);
        $invoices = self::invoices($user);
        $notifications = self::notifications($user);
        $course = self::courses($user);

        return view('dashboard', compact("user","school","totals","growth","students","invoices","notifications","course"));
    }

    /**
     * Count totals
     * 
     * @return array
     */
    public function totals($user) {
        // totals
        $students = Database::table('users')->where('branch',$user->branch)->where('role', 'student')->count("id","total")[0]->total;
        $income = Database::table('payments')->where('branch',$user->branch)->sum("amount","total")[0]->total;
        $invoiceTotal = Database::table('invoices')->where('branch',$user->branch)->sum("amount","total")[0]->total;
        $invoicePaid = Database::table('invoices')->where('branch',$user->branch)->sum("amountpaid","total")[0]->total;
        $unpaidInvoices = $invoiceTotal - $invoicePaid;
        $completeClasses = Database::table('schedules')->where('school',$user->school)->where('status', 'Complete')->count("id","total")[0]->total;
        $missedClasses = Database::table('schedules')->where('school',$user->school)->where('status', 'Missed')->count("id","total")[0]->total;
        $complePlusMissed = $completeClasses + $missedClasses;
        if ($complePlusMissed > 0) {
            $attendance = round(( $completeClasses / $complePlusMissed ) * 100 );
        }else{
            $attendance = 0;
        }
        $totals = compact("students", "income", "unpaidInvoices", "attendance");

        return $totals;
    }

    /**
     * Count last month totals
     * 
     * @return array
     */
    public function previous($user) {
        // previous
        $lastMonth = date('Y-m', strtotime('first day of last month'));
        $month = date('m', strtotime($lastMonth));
        $year = date('Y', strtotime($lastMonth));
        $base = array( 'MONTH(`created_at`)' => $month, 'YEAR(`created_at`)' => $year );
        $students = Database::table('users')->where(array_merge($base, array('branch' => $user->branch, 'role' => 'student' )))->count("id","total")[0]->total;
        $income = Database::table('payments')->where(array_merge($base, array('branch' => $user->branch )))->sum("amount","total")[0]->total;
        $invoiceTotal = Database::table('invoices')->where(array_merge($base, array('branch' => $user->branch )))->sum("amount","total")[0]->total;
        $invoicePaid = Database::table('invoices')->where(array_merge($base, array('branch' => $user->branch )))->sum("amountpaid","total")[0]->total;
        $unpaidInvoices = $invoiceTotal - $invoicePaid;
        $completeClasses = Database::table('schedules')->where(array_merge($base, array('school' => $user->school, 'status' => 'Complete' )))->count("id","total")[0]->total;
        $missedClasses = Database::table('schedules')->where(array_merge($base, array('school' => $user->school, 'status' => 'Missed' )))->count("id","total")[0]->total;
        $complePlusMissed = $completeClasses + $missedClasses;
        if ($complePlusMissed > 0) {
            $attendance = round(( $completeClasses / $complePlusMissed ) * 100 );
        }else{
            $attendance = 0;
        }
        

        $previous = compact("students", "income", "unpaidInvoices", "attendance");

        return $previous;
    }

    /**
     * Count current month totals
     * 
     * @return array
     */
    public function current($user) {
        // current month
        $month = date('m');
        $year = date('Y');
        $base = array( 'MONTH(`created_at`)' => $month, 'YEAR(`created_at`)' => $year );
        $students = Database::table('users')->where(array_merge($base, array('branch' => $user->branch, 'role' => 'student' )))->count("id","total")[0]->total;
        $income = Database::table('payments')->where(array_merge($base, array('branch' => $user->branch )))->sum("amount","total")[0]->total;
        $invoiceTotal = Database::table('invoices')->where(array_merge($base, array('branch' => $user->branch )))->sum("amount","total")[0]->total;
        $invoicePaid = Database::table('invoices')->where(array_merge($base, array('branch' => $user->branch )))->sum("amountpaid","total")[0]->total;
        $unpaidInvoices = $invoiceTotal - $invoicePaid;
        $completeClasses = Database::table('schedules')->where(array_merge($base, array('school' => $user->school, 'status' => 'Complete' )))->count("id","total")[0]->total;
        $missedClasses = Database::table('schedules')->where(array_merge($base, array('school' => $user->school, 'status' => 'Missed' )))->count("id","total")[0]->total;
        $complePlusMissed = $completeClasses + $missedClasses;
        if ($complePlusMissed > 0) {
            $attendance = round(( $completeClasses / $complePlusMissed ) * 100 );
        }else{
            $attendance = 0;
        }

        $current = compact("students", "income", "unpaidInvoices", "attendance");

        return $current;
    }

    /**
     * Calculate growth
     * 
     * @return array
     */
    public function growth($current, $previous) {

        if ($previous['students'] > 0) {
            $students = round( ( $current['students'] / $previous['students'] ) * 100 );
        }else{
            $students = 100;
        }
        if ($previous['income'] > 0) {
            $income = round( ( $current['income'] / $previous['income'] ) * 100 );
        }else{
            $income = 100;
        }
        if ($previous['unpaidInvoices'] > 0) {
            $unpaidInvoices = round( ( $current['unpaidInvoices'] / $previous['unpaidInvoices'] ) * 100 );
        }else{
            $unpaidInvoices = 100;
        }
        if ($previous['attendance'] > 0) {
            $attendance = round( ( $current['attendance'] / $previous['attendance'] ) * 100 );
        }else{
            $attendance = 100;
        }
        
        $growth = compact("students", "income", "unpaidInvoices", "attendance");

        return $growth;

    }

    /**
     * Calculate students growth
     * 
     * @return array
     */
    public function students($user) {
        $filter = array('branch' => $user->branch, 'role' => 'student' );
        $new = Database::table('users')->where($filter)->where("created_at", '>=', date('Y-m-d', strtotime('today - 30 days')).' 23:59:59')->count("id","total")[0]->total;

        $now = new \DateTime( "6 days ago");
        $interval = new \DateInterval( 'P1D'); 
        $period = new \DatePeriod( $now, $interval, 6); 
        $sevenDays = array("label" => array(), "count" => array());
        foreach( $period as $day) {
            $date = $day->format( 'Y-m-d');
            $range = $date." 00:00:00' AND '".$date." 23:59:59";
            $sevenDays['label'][] = sch_translate($day->format( 'D'));
            $sevenDays['count'][] = Database::table('users')->where($filter)->where('created_at','BETWEEN',$range)->count("id", "total")[0]->total;
        }

        $students = compact("new","sevenDays");

        return $students;
    }

    /**
     * Invoices last 12 Months
     * 
     * @return array
     */
    public function invoices($user) {
        $filter = array('branch' => $user->branch );

        $now = new \DateTime( "11 months ago");
        $interval = new \DateInterval( 'P1M'); 
        $period = new \DatePeriod( $now, $interval, 11); 
        $invoices = array("label" => array(), "paid" => array(), "unpaid" => array());
        foreach( $period as $theMonth) {
            $month = $theMonth->format( 'm');
            $year = $theMonth->format( 'Y');
            $filter = array_merge($filter, array( 'MONTH(`created_at`)' => $month, 'YEAR(`created_at`)' => $year ));
            $invoiceTotal = Database::table('invoices')->where($filter)->sum("amount","total")[0]->total;
            $invoicePaid = Database::table('invoices')->where($filter)->sum("amountpaid","total")[0]->total;
            $unpaidInvoices = $invoiceTotal - $invoicePaid;
            $invoices['paid'][] = $invoicePaid;
            $invoices['unpaid'][] = $unpaidInvoices;
            $invoices['label'][] = sch_translate($theMonth->format( 'M'));
        }

        return $invoices;
    }

    /**
     * Dashboard notification
     * 
     * @return array
     */
    public function notifications($user) {
        if ($user->role == "student" || $user->role == "instructor") {
            $notifications = Database::table('notifications')->where("user", $user->id)->orderBy('id', false)->limit(6)->get();
        } elseif ($user->role == "admin" || $user->role == "staff" || $user->role == "superadmin") {
            $notifications = Database::table('notifications')->where("branch", $user->branch)->where("class", "branch")->orWhere("user", $user->id)->orderBy('id', false)->limit(6)->get();
        }

        return $notifications;
    }

    /**
     * course sales
     * 
     * @return array
     */
    public function courses($user) {

        $filter = array('coursesenrolled`.`branch' => $user->branch );
        $courses = array("list" => array(), "sales" => array() );
        $allcourses = Database::table('courses')->where('school',$user->school)->where('status',"Available")->get();
        foreach ($allcourses as $course) {
            $courses['list'][] = $course->name;
            $courses['sales'][] = Database::table('coursesenrolled')->where(array_merge($filter, array("course" => $course->id)))->leftJoin("courses", "courses.id", "course")->sum("price","total")[0]->total;

        }

        return $courses;
    }

}
