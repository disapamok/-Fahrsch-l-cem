<?php

use Simcify\Router;
use Simcify\Exceptions\Handler;
use Simcify\Middleware\Authenticate;
use Pecee\Http\Middleware\BaseCsrfVerifier;
use Simcify\Middleware\RedirectIfAuthenticated;

/**
 * ,------,
 * | NOTE | CSRF Tokens are checked on all PUT, POST and GET requests. It
 * '------' should be passed in a hidden field named "csrf-token" or a header
 *          (in the case of AJAX without credentials) called "X-CSRF-TOKEN"
 *  */
Router::csrfVerifier(new BaseCsrfVerifier());

Router::group(array(
    //'prefix' => env("APP_ROUTE_DIRECTORY")
), function()
{
    Router::group(array(
        'exceptionHandler' => Handler::class
    ), function()
    {
        
        Router::group(array(
            'middleware' => Simcify\Middleware\Authenticate::class
        ), function()
        {
            // Router::get('/test', function(){
            //     echo view("test");                
            // });
            
            //Dashboard
            Router::get('/', 'Dashboard@get');
            Router::get('/', 'Dashboard@get');

            Router::get('/hidden', 'Notes@hidden');

            //hinweise
            $route_regex ='[A-Za-z\_0-9]+';
            Router::get('/notes/{notetype}', 'Notes@get')->where(['notetype' => $route_regex]);
            Router::get('/complexnotes/{notetype}', 'Notes@get_complex')->where(['notetype' => $route_regex]);
            Router::get('/editnote/{notetype}/{noteid}', 'Notes@update')->where(['notetype' => $route_regex, "noteid"=> '[0-9]+']);
            Router::get('/createnote/{notetype}/{parentnoteid}', 'Notes@create')->where(['notetype' => $route_regex, "parentnoteid"=> '[0-9]+']);
            Router::get('/viewcomplexnote/{notetype}', 'Notes@viewcomplexnote')->where(['notetype' => $route_regex]);
            Router::get('/viewsimplenote/{notetype}', 'Notes@viewsimplenote')->where(['notetype' => $route_regex]);
            Router::get('/deletenote/{notetype}/{noteid}', 'Notes@delete')->where(['notetype' => $route_regex, "noteid"=> '[0-9]+']);
            Router::get('/confirmdeletenote/{notetype}/{noteid}', 'Notes@confirmdelete')->where(['notetype' => $route_regex, "noteid"=> '[0-9]+']);
            Router::post('/savenote', 'Notes@save');
            Router::get('/jsoncomplexnote/{notetype}', 'Notes@jsoncomplexnote')->where(['notetype' => $route_regex]);
            Router::get('/jsonsimplenote/{notetype}', 'Notes@jsonsimplenote')->where(['notetype' => $route_regex]);

            //Schools
            Router::get('/schools', 'School@get');
            Router::post('/schools/delete', 'School@delete');
            Router::post('/school/create', 'School@create');
            Router::post('/school/update', 'School@update');
            Router::post('/school/update/view', 'School@updateview');
            Router::post('/schools/sendemail', 'School@sendemail');
            Router::post('/schools/sendsms', 'School@sendsms');

            //Branches
            Router::get('/branches', 'Branch@get');
            Router::post('/branch/switch', 'Branch@switcher');
            Router::post('/branch/delete', 'Branch@delete');
            Router::post('/branch/update', 'Branch@update');
            Router::post('/branch/update/view', 'Branch@updateview');
            Router::post('/branch/create', 'Branch@create');
            Router::post('/branch/sendemail', 'Branch@sendemail');
            Router::post('/branch/sendsms', 'Branch@sendsms');

            // settings
            Router::get('/settings', 'Settings@get');
            Router::post('/settings/update/profile', 'Settings@updateprofile');
            Router::post('/settings/update/company', 'Settings@updatecompany');
            Router::post('/settings/update/system', 'Settings@updatesystem');
            Router::post('/settings/update/reminders', 'Settings@updatereminders');
            Router::post('/settings/update/password', 'Settings@updatepassword');

            //Staff
            Router::get('/staff', 'Staff@get');
            Router::post('/staff/create', 'Staff@create');

            //Students
            Router::get('/students', 'Student@get');
            Router::post('/students/search', 'Student@searchStudent');
            Router::post('/student/create', 'Student@create');
            Router::post('/student/enroll/course', 'Student@addcourse');
            Router::post('/student/delete/enrollment', 'Student@deleteenrollment');

            Router::get('/editstudent/{studentid}', 'Student@update')->where(["studentid" => '[0-9]+']);

            //Scheduling
            Router::get('/scheduling', 'Schedule@get');
            Router::post('/schedule/create', 'Schedule@create');
            Router::post('/schedule/events/fetch', 'Schedule@fetch');
            Router::post('/schedule/update', 'Schedule@update');
            Router::post('/schedule/delete', 'Schedule@delete');
            Router::post('/schedule/update/view', 'Schedule@updateview');

            //Profile
            Router::get('/profile/{userid}', 'Profile@get', array(
                'as' => 'userid'
            ));
            Router::post('/profile/update', 'Profile@update');
            Router::post('/profile/delete', 'Profile@delete');
            Router::post('/profile/send/sms', 'Profile@sendsms');
            Router::post('/profile/send/email', 'Profile@sendemail');
            Router::post('/profile/payment/add', 'Profile@addpayment');
            Router::post('/profile/note/add', 'Profile@addnote');
            Router::post('/profile/note/read', 'Profile@readnote');
            Router::post('/profile/note/delete', 'Profile@deletenote');
            Router::post('/profile/note/update', 'Profile@updatenote');
            Router::post('/profile/note/update/view', 'Profile@updatenoteview');
            Router::post('/profile/attachment/upload', 'Profile@uploadattachment');
            Router::post('/profile/attachment/delete', 'Profile@deleteattachment');
            Router::post('/profile/disconnect/google/calendar', 'Profile@disconnectgoogle');

            //Notification
            Router::get('/notifications/json', 'Notification@get_json_format');
            Router::get('/notifications', 'Notification@get');
            Router::post('/notifications/read', 'Notification@read');
            Router::post('/notifications/mark-as-read', 'Notification@markAsRead');
            Router::post('/notifications/mark-as-unread', 'Notification@markAsUnread');
            

            //Send SMS/Email
            Router::post('/send/sms', 'Communication@sendSMS');
            Router::post('/send/email', 'Communication@sendEmail');

            //Invoices
            Router::get('/invoices', 'Invoice@get');
            Router::post('/invoice/delete', 'Invoice@delete');
            Router::get('/invoice/preview/{invoiceid}', 'Invoice@preview', array(
                'as' => 'invoiceid'
            ));
            Router::get('/invoice/download/{invoiceid}', 'Invoice@download', array(
                'as' => 'invoiceid'
            ));
            Router::post('/invoice/update', 'Invoice@update');
            Router::post('/invoice/update/view', 'Invoice@updateview');
            Router::post('/invoice/delete', 'Invoice@delete');
            Router::post('/invoices/payment/add', 'Invoice@addpayment');
            Router::post('/invoices/payment/delete', 'Invoice@deletepayment');
            Router::post('/invoices/payments/view', 'Invoice@viewpayments');
            Router::post('/invoices/search', 'Invoice@searchInvoice');

            //Fleet
            Router::get('/fleet', 'Fleet@get');
            Router::post('/fleet/add', 'Fleet@add');
            Router::post('/fleet/delete', 'Fleet@delete');
            Router::post('/fleet/update', 'Fleet@update');
            Router::post('/fleet/update/view', 'Fleet@updateview');

            //Exams
            Router::post('/exam/create', 'Exam@create');
            Router::post('/exam/update', 'Exam@update');
            Router::post('/exam/delete', 'Exam@delete');
            Router::post('/exam/publish', 'Exam@publish');
            Router::post('/exam/delete/question', 'Exam@deletequestion');
            Router::get('/exam/sections', 'Exam@sections');
            Router::post('/exam/update/questions', 'Exam@updatequestions');
            Router::post('/exam/save/answers', 'Exam@save');
            Router::get('/exam/{examid}/build', 'Exam@builder', array(
                'as' => 'examid'
            ));
            Router::get('/exam/{examid}/take', 'Exam@takeexam', array(
                'as' => 'examid'
            ));
            Router::get('/exam/{examid}/students', 'Exam@examstudents', array(
                'as' => 'examid'
            ));

            //Courses
            Router::get('/courses', 'Course@get');
            Router::get('/course/sections', 'Course@sections');
            Router::get('/course/{courseid}', 'Course@preview', array(
                'as' => 'courseid'
            ));
            Router::get('/course/{curriculumid}/curriculum', 'Course@curriculum', array(
                'as' => 'curriculumid'
            ));
            Router::get('/class/{curriculumid}/learn', 'Course@learn', array(
                'as' => 'curriculumid'
            ));
            Router::get('/class/{curriculumid}/students', 'Course@classstudents', array(
                'as' => 'learn'
            ));
            Router::post('/course/create', 'Course@create');
            Router::post('/course/create/online', 'Course@createonline');
            Router::post('/course/delete', 'Course@delete');
            Router::post('/course/update', 'Course@update');
            Router::post('/course/load/lecture', 'Course@loadlecture');
            Router::post('/course/publish/online/class', 'Course@publishclass');
            Router::post('/course/update/online/class', 'Course@editonlineclass');
            Router::post('/course/update/online/content', 'Course@updatecontent');
            Router::post('/course/delete/online/content', 'Course@deletecontent');
            Router::post('/course/delete/curriculum', 'Course@deletecurriculum');
            Router::post('/course/update/view', 'Course@updateview');

            //Communication
            Router::get('/communication', 'Communication@get');
            Router::post('/communication/delete', 'Communication@delete');
            Router::post('/communication/sms/send', 'Communication@sms');
            Router::post('/communication/email/send', 'Communication@email');
            Router::post('/communication/read', 'Communication@read');

            //Instructors
            Router::get('/instructors', 'Instructor@get');
            Router::post('/instructors', 'Instructor@get');
            Router::post('/instructor/create', 'Instructor@create');

            Router::post('/instructor/addassignstudent', 'Instructor@addAssingStudent');
            Router::post('/instructor/removeassignstudent', 'Instructor@removeAssignStudent');
            Router::post('/instructor/search-instructor', 'Instructor@searchInstructor');
            

            Router::post('/instructor/addtheorylesson', 'Instructor@addTheoryLesson');
            Router::post('/instructor/updatetheorylesson', 'Instructor@updateTheoryLesson');
            Router::post('/instructor/deletetheorylesson', 'Instructor@deleteTheoryLesson');

            Router::post('/instructor/adddrivinglesson', 'Instructor@addDrivingLesson');
            Router::post('/instructor/deletedrivinglesson', 'Instructor@deleteDrivingLesson');
            Router::post('/instructor/updatedrivinglesson', 'Instructor@updateDrivingLesson');
            

            //Signout
            Router::get('/signout', 'Auth@signout');

            // update
            Router::get('/update', 'Update@get');
            Router::post('/update/scan', 'Update@scan');

            // Export to excel
            Router::get('/payments-exportexcel', 'Invoice@exportToExcel');
            
            // Export to csv
            Router::get('/payments-exportcsv', 'Invoice@exportToCsv');

            // Export to pdf
            Router::get('/payments-exportpdf', 'Invoice@exportToPdf');
            
            // PDF Preview
            Router::get('/payments-pdf-preview', 'Invoice@paymentPdfPreview');
                     
        });
        
        Router::group(array(
            'middleware' => Simcify\Middleware\RedirectIfAuthenticated::class
        ), function()
        {
            /**
             * No login Required pages
             **/
            Router::get('/signin', 'Auth@get');
            Router::post('/reset', 'Auth@reset');
            Router::post('/forgot', 'Auth@forgot');
            Router::get('/reset/{token}', 'Auth@resetview', array(
                'as' => 'token'
            ));
            Router::post('/signin/authenticate', 'Auth@signin');
            Router::post('/signup', 'Auth@signup');

            // Students
            Router::get('/api-all-students', 'StudentApi@getAllStudents');
            Router::get('/api-students-details', 'StudentApi@getAllStudentsDetails');

            // Students class
            Router::get('/api-students-class', 'StudentApi@getAllStudentsClass');

            // Instructor
            Router::get('/api-kalender-instructor', 'InstructorApi@getAllKalenderInstructor');

            // Details of assigned instructor to students
            Router::get('api-assigned-instructors', 'InstructorApi@getAllAssignedInstructor');
            
        });


        //error pages
        Router::get('/404/', 'Schleier@error404');
        Router::get('/405/', 'Schleier@error405');
        
    });

    
});

    // error pages
    Router::get('/404', 'Schleier@error404');
    Router::get('/405', 'Schleier@error405');


    /*
    * API Routes
    */
    Router::group(['prefix' => '/api'], function() {
        Router::get('/fetch-user', 'API@fetchUser');
    });