<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;
use Simcify\File;
use Simcify\Str;

class Exam {

    
    /**
     * Create an exam
     * 
     * @return Json
     */
    public function create() {
        $user  = Auth::user();
        $data = array(
            'school' => $user->school,
            'branch' => $user->branch,
            'name' => escape(input('name')),
            'course' => escape(input('course')),
            'retakes' => escape(input('retakes')),
            'description' => escape(input('description'))
        );
        Database::table('exams')->insert($data);
        $examid = Database::table('exams')->insertId();
        return response()->json(responder("success", "", "", "redirect('".url('Exam@builder',['examid'=>$examid])."', true)", false));
    }
    

    /**
     * Update exam details
     * 
     * @return Json
     */
    public function update() {
        $data = array(
            'name' => escape(input('name')),
            'retakes' => escape(input('retakes')),
            'description' => escape(input('description'))
        );
        Database::table("exams")->where("id", input("examid"))->update($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("exam_successfully_updated")));
    }
    
    /**
     * Delete online curriculum
     * 
     * @return Json
     */
    public function delete() {
        $exam = Database::table("exams")->where("id", input("examid"))->first();
        Database::table("exams")->where("id", input("examid"))->delete();
        return response()->json(responder("success", "", "", "redirect('".url('Course@preview',['courseid'=>$exam->course])."', true)", false));
    }
    
    /**
     * Delete online curriculum
     * 
     * @return Json
     */
    public function deletequestion() {
        Database::table("questions")->where("id", input("itemId"))->delete();
        return true;
    }



    
    /**
     * publish exam
     * 
     * @return Json
     */
    public function publish() {
        $exam = Database::table("exams")->where("id", input("examid"))->first();
        if (empty(input('status'))) {
            $status = "Unpublished";
        }else {
            $status = "Published";
        }
        $data = array(
            'status' => $status
        );
        Database::table("exams")->where("id", input("examid"))->update($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("exam_successfully_updated")));
    }
    
    
    /**
     * Exam builder
     * 
     * @return \Pecee\Http\Response
     */
    public function builder($examid) {
        $user   = Auth::user();
        $exam = Database::table('exams')->where('id', $examid)->first();
        if (empty($exam)) {
            return view("error/404");
        }
        $course = Database::table('courses')->where('id', $exam->course)->first();
        $total = Database::table('coursesenrolled')->where('course', $course->id)->count("id","total")[0]->total;
        $question = new \StdClass();
        $questions = Database::table('questions')->where('exam',$exam->id)->orderBy("indexing", true)->get(); 
        foreach ($questions as $key => $question) {
            foreach (array_combine(json_decode($question->answers), json_decode($question->correct)) as $answer => $correct){
                $question->choices[] = (object) array("answer" => $answer, "correct" => $correct);
            }
        }
        $instructors = Database::table('users')->where(['role'=>'instructor','branch'=>$user->branch,'school'=>$user->school])->get();
        $courseinstructors = Database::table('courseinstructor')->where('courseinstructor`.`course', $course->id)->leftJoin("users", "users.id", "instructor")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`");
        $enrolledstudents = Database::table('coursesenrolled')->where('coursesenrolled`.`course', $course->id)->leftJoin("users", "users.id", "student")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`", "`coursesenrolled.created_at`", "`coursesenrolled.total_practical`", "`coursesenrolled.total_theory`", "`coursesenrolled.completed_theory`", "`coursesenrolled.completed_practical`", "`coursesenrolled.completed_on`");
        return view('exambuilder', compact("user", "course", "courses", "courseinstructors", "instructors","exam","total","enrolledstudents","questions"));
        
    }
    
    
    /**
     * Exam builder
     * 
     * @return \Pecee\Http\Response
     */
    public function takeexam($examid) {
        $user   = Auth::user();
        $exam = Database::table('exams')->where('id', $examid)->first();
        if (empty($exam)) {
            return view("error/404");
        }
        $course = Database::table('courses')->where('id', $exam->course)->first();
        $question = new \StdClass();
        $questions = Database::table('questions')->where('exam',$exam->id)->orderBy("indexing", true)->get(); 
        $takes = Database::table('examsreports')->where('student', $user->id)->where('exam', $exam->id)->count("id","total")[0]->total;
        if ($takes > 0) {
            $lastTake = Database::table('examsreports')->where('student', $user->id)->where('exam', $exam->id)->last();
        }
        foreach ($questions as $key => $question) {
            foreach (array_combine(json_decode($question->answers), json_decode($question->correct)) as $answer => $correct){
                $question->choices[] = (object) array("answer" => $answer, "correct" => $correct);
            }
        }
        return view('examroom', compact("user", "course","exam","total","questions","takes","lastTake"));
        
    }


    
    
    /**
     * Save exam answers from students 
     * 
     * @return Json
     */
    public function save() {
        $user  = Auth::user();
        $isCorrect = "no";
        $answersGiven = $correctlyAnswered = array();
        $exam = Database::table('exams')->where('id', input("examid"))->first(); 
        $totalQuestions = count($_POST['question']);
        foreach($_POST['question'] as $index => $questionid){
            $question = Database::table('questions')->where('id',$questionid)->first(); 
            $allAnswers = json_decode($question->answers);
            $correctAnswers = json_decode($question->correct);
            $studentAnswer = $_POST['answer'.$questionid];
            foreach ($correctAnswers as $key => $correct){
                if ($correct == '1' && $correct == $studentAnswer[$key]) {
                    $isCorrect = $correctlyAnswered[] = "yes";
                    break;
                }else{
                    $isCorrect = "no";
                }
            }
            foreach ($studentAnswer as $answerkey => $answer) {
                if ($answer == '1') {
                    $answersGiven[] = $allAnswers[$answerkey];
                }
            }
            $data = array(
                'school' => $question->school,
                'branch' => $question->branch,
                'course' => $question->course,
                'exam' => $question->exam,
                'student' => $user->id,
                'question' => $question->question,
                'indexing' => $question->indexing,
                'answer' => json_encode($answersGiven),
                'correct' => $isCorrect
            );
            Database::table('studentexamresults')->insert($data);
        }

        if (count($correctlyAnswered) > 0) {
            $score = round((count($correctlyAnswered) / $totalQuestions) * 100);
        }else{
            $score = 0;
        }

        $report = array(
            'school' => $exam->school,
            'student' => $user->id,
            'branch' => $exam->branch,
            'course' => $exam->course,
            'exam' => $exam->id,
            'totalQuestions' => $totalQuestions,
            'correctlyAnswered' => count($correctlyAnswered),
            'score' => $score
        );
        Database::table('examsreports')->insert($report);
        return response()->json(responder("success", sch_translate("you_scored___1__", [$score]), sch_translate("your_exam_was_marked_and_you_answered___1___out_of___2___questions_correctly", [count($correctlyAnswered), $totalQuestions]), "reload()"));
    }
    
    /**
     * Update online class content
     * 
     * @return Json
     */
    public function updatequestions() {
        $user  = Auth::user();
        $questions = range(1, count($_POST['question']));
        foreach($questions as $index => $question){
            // save / update question details
            $questionIndex = $index + 1;
            $data = array(
                'school' => $user->school,
                'branch' => $user->branch,
                'course' => escape(input('course')),
                'exam' => escape(input('exam')),
                'question' => escape($_POST['question'][$index]),
                'indexing' => escape($_POST['indexing'][$index]),
                'required' => escape($_POST['required'][$index]),
                'type' => escape($_POST['type'][$index]),
                'answers' => json_encode($_POST['answer'.$questionIndex]),
                'correct' => json_encode($_POST['correct'.$questionIndex])
            );

            if (!empty($_POST['questionid'][$index])) {
                Database::table('questions')->where("id", $_POST['questionid'][$index])->update($data);
            }else{
                Database::table('questions')->insert($data);
            }

        }
        return response()->json(responder("success", sch_translate("questions_updated"), sch_translate("questions_successfully_updated"), "reload()"));
    }

    
    
    /**
     * exam students
     * 
     * @return \Pecee\Http\Response
     */
    public function examstudents($examid) {
        $user   = Auth::user();
        $exam = Database::table('exams')->where('id', $examid)->first();
        if (empty($exam)) {
            return view("error/404");
        }
        $questions = Database::table('questions')->where('exam', $exam->id)->count("id","total")[0]->total;
        $students = Database::table('examsreports')->where('examsreports`.`exam', $exam->id)->where('users`.`role', "student")->leftJoin("users", "users.id", "examsreports.student")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`", "`examsreports.correctlyAnswered`", "`examsreports.totalQuestions`", "`examsreports.score`");
        return view('examstudents', compact("user", "exam","students","questions"));
        
    }

    
    /**
     * Curriculum manager
     * 
     * @return \Pecee\Http\Response
     */
    public function sections() {
        $json = '{
            "choice": "<div class=\"col-md-6 single-answer\"> <a href=\"\" class=\"delete-choice\" title=\"Delete Choice\"><i class=\" mdi mdi-delete\"><\/i><\/a> <label> Choice #<span class=\"indexing\">1<\/span><\/label> <input type=\"text\" class=\"form-control\" name=\"answer[]\" original-name=\"answer\" placeholder=\"Choice\" required><div class=\"correct-answer-box\"><input type=\"checkbox\" class=\"hidden\" name=\"correct[]\" original-name=\"correct\" value=\"0\"> <input type=\"checkbox\" class=\"correct-answer\" id=\"choice\" name=\"correct[]\" original-name=\"correct\"  value=\"1\"> <label for=\"choice\" class=\"text-xs\">This is the correct answer<\/label><\/div><\/div>",
            "question": "<div class=\"panel panel-default chapter newly\"><div class=\"panel-heading\"><div class=\"chapter-drag\"><i class=\"mdi mdi-drag\"><\/i><\/div> <a href=\"\" class=\"btn btn-black btn-sm pull-right manage-class delete-item\" data-type=\"chapter\" title=\"Delete question\"><i class=\" mdi mdi-delete\"><\/i><\/a><h4 class=\"panel-title\"> <a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#chapter1\"> <span class=\"indexing\">1.)<\/span> <span class=\"panel-label\">New Question<\/span> <\/a><\/h4><\/div><div id=\"chapter1\" class=\"panel-collapse collapse chapter-body in show\"><div class=\"panel-body\"><div class=\"form-group\"><div class=\"row\"><div class=\"col-md-12\"> <label>Enter the question<\/label> <input type=\"text\" class=\"form-control chapter-title\" name=\"question[]\" placeholder=\"Enter the question\" required><input type=\"hidden\" class=\"question-indexing\" name=\"indexing[]\"> <input type=\"hidden\" name=\"questionid[]\" value=\"0\"><\/div><\/div><\/div><div class=\"form-group\"><div class=\"row\"><div class=\"col-md-6\"> <label>Question type<\/label> <select class=\"form-control\" name=\"type[]\" original-name=\"type\"><option value=\"multiple\">Multiple Answers<\/option><option value=\"single\">Single Answer<\/option> <\/select><\/div><div class=\"col-md-6\"> <label>Required Question<\/label> <select class=\"form-control\" name=\"required[]\" original-name=\"required\"><option value=\"yes\">Yes<\/option><option value=\"no\">No<\/option> <\/select><\/div><\/div><\/div><div class=\"divider\"><\/div><p>Below are question choices<\/p><div class=\"choices-holder row\"><div class=\"col-md-6 single-answer\"> <a href=\"\" class=\"delete-choice\" title=\"Delete Choice\"><i class=\" mdi mdi-delete\"><\/i><\/a> <label> Choice #<span class=\"indexing\">1<\/span><\/label> <input type=\"text\" class=\"form-control\" name=\"answer[]\" original-name=\"answer\" placeholder=\"Choice\" required><div class=\"correct-answer-box\"> <input type=\"checkbox\" class=\"hidden\" name=\"correct[]\" original-name=\"correct\" checked value=\"0\"> <input type=\"checkbox\" class=\"correct-answer\" id=\"choice\" name=\"correct[]\" original-name=\"correct\"  value=\"1\"> <label for=\"choice\" class=\"text-xs\">This is the correct answer<\/label><\/div><\/div><div class=\"col-md-6 single-answer\"> <a href=\"\" class=\"delete-choice\" title=\"Delete Choice\"><i class=\" mdi mdi-delete\"><\/i><\/a> <label> Choice #<span class=\"indexing\">2<\/span><\/label> <input type=\"text\" class=\"form-control\" name=\"answer[]\" original-name=\"answer\" placeholder=\"Choice\" required><div class=\"correct-answer-box\"> <input type=\"checkbox\" class=\"hidden\" name=\"correct[]\" original-name=\"correct\" checked value=\"0\"> <input type=\"checkbox\" class=\"correct-answer\" id=\"choice\" name=\"correct[]\" original-name=\"correct\"  value=\"1\"> <label for=\"choice\" class=\"text-xs\">This is the correct answer<\/label><\/div><\/div><div class=\"col-md-6 single-answer\"> <a href=\"\" class=\"delete-choice\" title=\"Delete Choice\"><i class=\" mdi mdi-delete\"><\/i><\/a> <label> Choice #<span class=\"indexing\">3<\/span><\/label> <input type=\"text\" class=\"form-control\" name=\"answer[]\" original-name=\"answer\" placeholder=\"Choice\" required><div class=\"correct-answer-box\"> <input type=\"checkbox\" class=\"hidden\" name=\"correct[]\" original-name=\"correct\" checked value=\"0\"> <input type=\"checkbox\" class=\"correct-answer\" id=\"choice\" name=\"correct[]\" original-name=\"correct\"  value=\"1\"> <label for=\"choice\" class=\"text-xs\">This is the correct answer<\/label><\/div><\/div><div class=\"col-md-6 single-answer\"> <a href=\"\" class=\"delete-choice\" title=\"Delete Choice\"><i class=\" mdi mdi-delete\"><\/i><\/a> <label> Choice #<span class=\"indexing\">4<\/span><\/label> <input type=\"text\" class=\"form-control\" name=\"answer[]\" original-name=\"answer\" placeholder=\"Choice\" required><div class=\"correct-answer-box\"> <input type=\"checkbox\" class=\"hidden\" name=\"correct[]\" original-name=\"correct\" checked value=\"0\"> <input type=\"checkbox\" class=\"correct-answer\" id=\"choice\" name=\"correct[]\" original-name=\"correct\"  value=\"1\"> <label for=\"choice\" class=\"text-xs\">This is the correct answer<\/label><\/div><\/div><\/div><div class=\"lecture-buttons-holder\"> <button class=\"btn btn-black btn-icon add-choice\" type=\"button\"><i class=\" mdi mdi-plus-circle-outline\"><\/i> Add another Choice<\/button><\/div><\/div><\/div><\/div>"
        }';
        return $json;
    }






    // ----------------------------------------------------------------------------------
    
    /**
     * Get courses view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user    = Auth::user();
        if ($user->role == "student") {
            $enrollments = Database::table('coursesenrolled')->where('student',$user->id)->orderBy('id', false)->get();
            $enrollmentsId = array();
            foreach ($enrollments as $enrollment) {
                $enrollmentsId[] = $enrollment->course;
            }
            $courses = Database::table('courses')->where('id','IN', "(".implode(",", $enrollmentsId).")")->get();
        }else{
            $courses = Database::table('courses')->where('school', $user->school)->get();
        }
        foreach ($courses as $course) {
            $instructors         = Database::table('courseinstructor')->where('courseinstructor`.`course', $course->id)->leftJoin("users", "users.id", "instructor")->get("`users.avatar`", "`users.fname`", "`users.lname`");
            $course->instructors = $instructors;
            $course->students = Database::table('coursesenrolled')->where('course', $course->id)->count("id","total")[0]->total;
            $course->online = Database::table('onlineclasses')->where('course', $course->id)->count("id","total")[0]->total;
        }
        $instructors = Database::table('users')->where('school', $user->school)->where("role", "instructor")->get();
        return view('courses', compact("user", "courses", "instructors"));
    }
    
    /**
     * Delete online class content
     * 
     * @return Json
     */
    public function deletecontent() {
        if (input("itemType") == "lecture") {
            $lecture = Database::table("lectures")->where("id", input("itemId"))->first();
            self::deleteUploads($lecture);
            Database::table("lectures")->where("id", input("itemId"))->delete();
        }else{
            $chapter = Database::table("onlineclasschapters")->where("id", input("itemId"))->get();
            $lectures = Database::table('lectures')->where('chapter',$chapter->id)->get();
            foreach ($lectures as $lecture) {
                self::deleteUploads($lecture);
            }

            Database::table("onlineclasschapters")->where("id", input("itemId"))->delete();
        }
    }
    
    /**
     * Load Lecture
     * 
     * @return Json
     */
    public function loadlecture() {
        $user = Auth::user();
        $lecture = Database::table("lectures")->where("id", input("lectureid"))->first();
        if (empty($lecture)) {
            return view("extras/lectures");
        }
        if ($user->role == "student") {
            Database::table('lectureprogress')->insert(array(
                        "student" => $user->id,
                        "lecture" => $lecture->id,
                        "school" => $lecture->school,
                        "branch" => $lecture->branch,
                        "course" => $lecture->course,
                        "class" => $lecture->class,
                        "chapter" => $lecture->chapter
                    ));
        }
        return view('extras/lectures', compact("lecture"));
    }
    
    /**
     * Delete online class uploaded content
     * 
     * @return boolean
     */
    public function deleteUploads($lecture) {
            if ($lecture->type == "pdf" || $lecture->type == "downloads" || $lecture->type == "video") {
                File::delete($lecture->content, "lectures");
            }
            return true;
    }
    
    /**
     * Course update view
     * 
     * @return Json
     */
    public function updateview() {
        $user           = Auth::user();
        $course         = Database::table("courses")->where("id", input("courseid"))->first();
        $instructors    = Database::table("courseinstructor")->where("course", input("courseid"))->get("instructor");
        $instructorsIds = array();
        foreach ($instructors as $instructor) {
            $instructorsIds[] = $instructor->instructor;
        }
        $instructors = Database::table('users')->where('school', $user->school)->where("role", "instructor")->get();
        return view('extras/updatecourse', compact("course", "instructors", "instructorsIds"));
    }
    
    
    
    
    /**
     * Course preview
     * 
     * @return \Pecee\Http\Response
     */
    public function preview($courseid) {
        $user   = Auth::user();
        $course = Database::table('courses')->where('id', $courseid)->first();
        if (empty($course)) {
            return view("error/404");
        }
        $chapter = $curriculum = new \StdClass();
        $curriculums = Database::table('onlineclasses')->where('course', $course->id)->get();
        foreach ($curriculums as $curriculum) {
            $curriculum->chapters = Database::table('onlineclasschapters')->where('class',$curriculum->id)->orderBy("indexing", true)->get();
            foreach ($curriculum->chapters as $chapter) {
                $chapter->lectures = Database::table('lectures')->where('chapter',$chapter->id)->orderBy("indexing", true)->get();
            }
        }
        $total = Database::table('coursesenrolled')->where('course', $course->id)->count("id","total")[0]->total;
        $fleets = Database::table('fleet')->where('branch',$user->branch)->get();
        $instructors = Database::table('users')->where(['role'=>'instructor','branch'=>$user->branch,'school'=>$user->school])->get();
        $students = Database::table('users')->where(['role'=>'student','branch'=>$user->branch,'school'=>$user->school])->get();
        $courseinstructors = Database::table('courseinstructor')->where('courseinstructor`.`course', $course->id)->leftJoin("users", "users.id", "instructor")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`");
        $enrolledstudents = Database::table('coursesenrolled')->where('coursesenrolled`.`course', $course->id)->leftJoin("users", "users.id", "student")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`", "`coursesenrolled.created_at`", "`coursesenrolled.total_practical`", "`coursesenrolled.total_theory`", "`coursesenrolled.completed_theory`", "`coursesenrolled.completed_practical`", "`coursesenrolled.completed_on`");
        return view('coursepreview', compact("user", "course", "courses", "courseinstructors", "instructors","fleets","students","total","enrolledstudents","curriculums"));
        
    }
    
    
    
    /**
     * Curriculum manager
     * 
     * @return \Pecee\Http\Response
     */
    public function curriculum($curriculumid) {
        $user   = Auth::user();
        $curriculum = Database::table('onlineclasses')->where('id', $curriculumid)->first();
        if (empty($curriculum)) {
            return view("error/404");
        }
        $course = Database::table('courses')->where('id', $curriculum->course)->first();
        $total = Database::table('coursesenrolled')->where('course', $course->id)->count("id","total")[0]->total;
        $fleets = Database::table('fleet')->where('branch',$user->branch)->get();
        $chapter = new \StdClass();
        $chapters = Database::table('onlineclasschapters')->where('class',$curriculum->id)->orderBy("indexing", true)->get();
        foreach ($chapters as $chapter) {
            $chapter->lectures = Database::table('lectures')->where('chapter',$chapter->id)->orderBy("indexing", true)->get();
        }
        $instructors = Database::table('users')->where(['role'=>'instructor','branch'=>$user->branch,'school'=>$user->school])->get();
        $students = Database::table('users')->where(['role'=>'student','branch'=>$user->branch,'school'=>$user->school])->get();
        $courseinstructors = Database::table('courseinstructor')->where('courseinstructor`.`course', $course->id)->leftJoin("users", "users.id", "instructor")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`");
        $enrolledstudents = Database::table('coursesenrolled')->where('coursesenrolled`.`course', $course->id)->leftJoin("users", "users.id", "student")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`", "`coursesenrolled.created_at`", "`coursesenrolled.total_practical`", "`coursesenrolled.total_theory`", "`coursesenrolled.completed_theory`", "`coursesenrolled.completed_practical`", "`coursesenrolled.completed_on`");
        return view('curriculum', compact("user", "course", "courses", "courseinstructors", "instructors","curriculum","students","total","enrolledstudents","chapters"));
        
    }
    
    
    
    /**
     * course learning portal
     * 
     * @return \Pecee\Http\Response
     */
    public function learn($curriculumid) {
        $user   = Auth::user();
        $curriculum = Database::table('onlineclasses')->where('id', $curriculumid)->first();
        if (empty($curriculum)) {
            return view("error/404");
        }
        $course = Database::table('courses')->where('id', $curriculum->course)->first();
        $total = Database::table('coursesenrolled')->where('course', $course->id)->count("id","total")[0]->total;
        $fleets = Database::table('fleet')->where('branch',$user->branch)->get();
        $chapter = new \StdClass();
        $chapters = Database::table('onlineclasschapters')->where('class',$curriculum->id)->orderBy("indexing", true)->get();
        foreach ($chapters as $chapter) {
            $chapter->lectures = Database::table('lectures')->where('chapter',$chapter->id)->orderBy("indexing", true)->get();
        }
        $learningprogress = Database::table("lectureprogress")->where("class", $curriculum->id)->where("student", $user->id)->get();
        $completedLectures = array();
        foreach ($learningprogress as $class) {
            $completedLectures[] = $class->lecture;
        }
        $instructors = Database::table('users')->where(['role'=>'instructor','branch'=>$user->branch,'school'=>$user->school])->get();
        $students = Database::table('users')->where(['role'=>'student','branch'=>$user->branch,'school'=>$user->school])->get();
        $courseinstructors = Database::table('courseinstructor')->where('courseinstructor`.`course', $course->id)->leftJoin("users", "users.id", "instructor")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`");
        $enrolledstudents = Database::table('coursesenrolled')->where('coursesenrolled`.`course', $course->id)->leftJoin("users", "users.id", "student")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`", "`coursesenrolled.created_at`", "`coursesenrolled.total_practical`", "`coursesenrolled.total_theory`", "`coursesenrolled.completed_theory`", "`coursesenrolled.completed_practical`", "`coursesenrolled.completed_on`");
        return view('learn', compact("user", "course", "courses", "courseinstructors", "instructors","curriculum","students","total","enrolledstudents","chapters","completedLectures"));
        
    }
    
    
    
    /**
     * class students
     * 
     * @return \Pecee\Http\Response
     */
    public function classstudents($curriculumid) {
        $user   = Auth::user();
        $curriculum = Database::table('onlineclasses')->where('id', $curriculumid)->first();
        if (empty($curriculum)) {
            return view("error/404");
        }
        $lectures = Database::table('lectures')->where('class', $curriculum->id)->count("id","total")[0]->total;
        $students = Database::table('lectureprogress')->where('lectureprogress`.`class', $curriculum->id)->leftJoin("users", "users.id", "student")->groupBy("lectureprogress.student")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`", "`lectureprogress.started_at`");
        foreach ($students as $student) {
            $student->completed = Database::table('lectureprogress')->where('class', $curriculum->id)->where('student', $student->id)->count("lecture","total", true)[0]->total;
        }
        return view('classstudents', compact("user", "curriculum","students","lectures"));
        
    }

    
    
    
}
 