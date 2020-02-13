<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;
use Simcify\File;
use Simcify\Str;

class Course {
    
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
            $courses = Database::table('courses')->where('id','IN', "(".implode(",", $enrollmentsId).")")->orderBy('id', false)->get();
        }else{
            $courses = Database::table('courses')->where('school', $user->school)->orderBy('id', false)->get();
        }
        foreach ($courses as $course) {
            $instructors         = Database::table('courseinstructor')->where('courseinstructor`.`course', $course->id)->leftJoin("users", "users.id", "instructor")->get("`users.avatar`", "`users.fname`", "`users.lname`");
            $course->instructors = $instructors;
            $course->students = Database::table('coursesenrolled')->where('course', $course->id)->count("id","total")[0]->total;
            $course->online = Database::table('onlineclasses')->where('course', $course->id)->where('status', "Published")->count("id","total")[0]->total;
            $course->exams = Database::table('exams')->where('course', $course->id)->where('status', "Published")->count("id","total")[0]->total;
            $branch = Database::table('branches')->where('id', $course->branch)->get();
            $course->branchname = $branch[0]->name;
        }
        $instructors = Database::table('users')->where('school', $user->school)->where("role", "instructor")->get();
        return view('courses', compact("user", "courses", "instructors", "branch"));
    }
    
    
    /**
     * Add a course
     * 
     * @return Json
     */
    public function create() {
        $image = '';
        $user  = Auth::user();
        if (!empty(input("image"))) {
            $upload = File::upload(input("image"), "courses", array(
                "source" => "base64",
                "extension" => "png"
            ));
            if ($upload['status'] == "success") {
                $image = $upload['info']['name'];
            }
        }
        $data = array(
            'image' => $image,
            'school' => $user->school,
            'branch' => $user->branch,
            'name' => escape(input('name')),
            'price' => escape(floatval(str_replace(',','.',input('price')))),
            'duration' => escape(input('duration')),
            'period' => escape(input('period')),
            'practical_classes' => escape(input('practical_classes')),
            'theory_classes' => escape(input('theory_classes')),
            'status' => escape(input('status'))
        );
        Database::table('courses')->insert($data);
        $courseId = Database::table('courses')->insertId();
        if (!empty(input("instructors"))) {
            foreach (input("instructors") as $instructor) {
                Database::table('courseinstructor')->insert(array(
                    "course" => $courseId,
                    "instructor" => $instructor
                ));
            }
        }
        return response()->json(responder("success", sch_translate("course_added"), sch_translate("course_successfully_added"), "redirect('".url('Course@preview',['courseid'=>$courseId])."', true)"));
    }
    
    
    /**
     * Add a course online
     * 
     * @return Json
     */
    public function createonline() {
        $user  = Auth::user();
        $data = array(
            'school' => $user->school,
            'branch' => $user->branch,
            'name' => escape(input('name')),
            'course' => escape(input('course')),
            'description' => escape(input('description'))
        );
        Database::table('onlineclasses')->insert($data);
        $curriculumid = Database::table('onlineclasses')->insertId();
        return response()->json(responder("success", "", "", "redirect('".url('Course@curriculum',['curriculumid'=>$curriculumid])."', true)", false));
    }
    
    /**
     * Delete course
     * 
     * @return Json
     */
    public function delete() {
        $course = Database::table("courses")->where("id", input("courseid"))->get();
        if (!empty($course->image)) {
            File::delete($course->image, "courses");
        }
        Database::table("courses")->where("id", input("courseid"))->delete();
        return response()->json(responder("success", sch_translate("course_deleted"), sch_translate("course_successfully_deleted"), "redirect('" . url("Course@get") . "')"));
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
     * Delete online curriculum
     * 
     * @return Json
     */
    public function deletecurriculum() {
        $class = Database::table("onlineclasses")->where("id", input("curriculumid"))->first();
        $lectures = Database::table('lectures')->where('class',$class->id)->get();
        foreach ($lectures as $lecture) {
            self::deleteUploads($lecture);
        }
        Database::table("onlineclasses")->where("id", input("curriculumid"))->delete();
        return response()->json(responder("success", "", "", "redirect('".url('Course@preview',['courseid'=>$class->course])."', true)", false));
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
     * Update course
     * 
     * @return Json
     */
    public function update() {
        $course = Database::table("course")->where("id", input("courseid"))->first();
        if (!empty(input("image"))) {
            $upload = File::upload(input("image"), "courses", array(
                "source" => "base64",
                "extension" => "png"
            ));
            if ($upload['status'] == "success") {
                if (!empty($course->image)) {
                    File::delete($course->image, "courses");
                }
                Database::table("courses")->where("id", input("courseid"))->update(array(
                    "image" => $upload['info']['name']
                ));
            }
        }
        $data = array(
            'name' => escape(input('name')),
            'price' => escape(input('price')),
            'duration' => escape(input('duration')),
            'period' => escape(input('period')),
            'practical_classes' => escape(input('practical_classes')),
            'theory_classes' => escape(input('theory_classes')),
            'status' => escape(input('status'))
        );
        Database::table("courses")->where("id", input("courseid"))->update($data);
        Database::table("courseinstructor")->where("course", input("courseid"))->delete();
        if (!empty(input("instructors"))) {
            foreach (input("instructors") as $instructor) {
                Database::table('courseinstructor')->insert(array(
                    "course" => input("courseid"),
                    "instructor" => $instructor
                ));
            }
        }
        return response()->json(responder("success", sch_translate("alright"), sch_translate("course_successfully_updated"), "reload()"));
    }
    
    /**
     * Update online class
     * 
     * @return Json
     */
    public function editonlineclass() {
        $course = Database::table("onlineclasses")->where("id", input("curriculumid"))->first();
        $data = array(
            'name' => escape(input('name')),
            'description' => escape(input('description'))
        );
        Database::table("onlineclasses")->where("id", input("curriculumid"))->update($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("course_successfully_updated")));
    }
    
    /**
     * publish online class
     * 
     * @return Json
     */
    public function publishclass() {
        $course = Database::table("onlineclasses")->where("id", input("curriculumid"))->first();
        if (empty(input('status'))) {
            $status = "Unpublished";
        }else {
            $status = "Published";
        }
        $data = array(
            'status' => $status
        );
        Database::table("onlineclasses")->where("id", input("curriculumid"))->update($data);
        return response()->json(responder("success", sch_translate("alright"), sch_translate("course_successfully_updated")));
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
        if ($user->role == "student") {
            $filter = array("status" => "Published");
        }else{
            $filter = array();
        }
        $chapter = $curriculum = $exam = new \StdClass();
        $curriculums = Database::table('onlineclasses')->where('course', $course->id)->where($filter)->get();
        foreach ($curriculums as $curriculum) {
            $curriculum->chapters = Database::table('onlineclasschapters')->where('class',$curriculum->id)->orderBy("indexing", true)->get();
            foreach ($curriculum->chapters as $chapter) {
                $chapter->lectures = Database::table('lectures')->where('chapter',$chapter->id)->orderBy("indexing", true)->get();
            }
        }
        $question = new \StdClass();
        $exams = Database::table('exams')->where('course',$course->id)->where($filter)->orderBy("id", false)->get(); 
        foreach ($exams as $exam) {
            $exam->questions = Database::table('questions')->where('exam', $exam->id)->count("id","total")[0]->total;
            $exam->students = Database::table('examsreports')->where('examsreports`.`exam', $exam->id)->where('users`.`role', "student")->leftJoin("users", "users.id", "examsreports.student")->count("student","total", true)[0]->total;
        }
        $total = Database::table('coursesenrolled')->where('course', $course->id)->count("id","total")[0]->total;
        $fleets = Database::table('fleet')->where('branch',$user->branch)->get();
        $instructors = Database::table('users')->where(['role'=>'instructor','branch'=>$user->branch,'school'=>$user->school])->get();
        $students = Database::table('users')->where(['role'=>'student','branch'=>$user->branch,'school'=>$user->school])->get();
        $courseinstructors = Database::table('courseinstructor')->where('courseinstructor`.`course', $course->id)->leftJoin("users", "users.id", "instructor")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`");
        $enrolledstudents = Database::table('coursesenrolled')->where('coursesenrolled`.`course', $course->id)->leftJoin("users", "users.id", "student")->get("`users.avatar`", "`users.fname`", "`users.lname`", "`users.email`", "`users.id`", "`coursesenrolled.created_at`", "`coursesenrolled.total_practical`", "`coursesenrolled.total_theory`", "`coursesenrolled.completed_theory`", "`coursesenrolled.completed_practical`", "`coursesenrolled.completed_on`");
        return view('coursepreview', compact("user", "course", "courses", "courseinstructors", "instructors","fleets","students","total","enrolledstudents","curriculums","exams"));
        
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

    
    
    /**
     * Update online class content
     * 
     * @return Json
     */
    public function updatecontent() {
        $user  = Auth::user();
        $chapters = range(1, count($_POST['chaptertitle']));
        foreach($chapters as $index => $chapter){
            // save / update chapter details
            $data = array(
                'school' => $user->school,
                'branch' => $user->branch,
                'course' => escape(input('course')),
                'class' => escape(input('class')),
                'title' => escape($_POST['chaptertitle'][$index]),
                'indexing' => escape($_POST['indexing'][$index]),
                'description' => escape($_POST['description'][$index])
            );
            if (!empty($_POST['chapterid'][$index])) {
                Database::table('onlineclasschapters')->where("id", $_POST['chapterid'][$index])->update($data);
                $chapterId = $_POST['chapterid'][$index];
            }else{
                Database::table('onlineclasschapters')->insert($data);
                $chapterId = Database::table('onlineclasschapters')->insertId();
            }

            // save / update chapter lectures
            $chapterIndex = $index + 1;
            if (isset($_POST['title'.$chapterIndex])) {
                $lectureCount = range(1, count($_POST['title'.$chapterIndex]));
                foreach($lectureCount as $key => $singleLecture){
                    $type = $_POST['type'.$chapterIndex][$key];
                    if ($type == "link" || $type == "text") {
                        $content = $_POST['content'.$chapterIndex][$key];
                    }else{
                        if (isset($_FILES['content'.$chapterIndex]['tmp_name'][$key])) {
                            $upload = File::upload(
                                $_FILES['content'.$chapterIndex]['tmp_name'][$key], 
                                "lectures",
                                array(
                                    "extension" => Str::lower(pathinfo(basename($_FILES['content'.$chapterIndex]['name'][$key]),PATHINFO_EXTENSION))
                                )
                            );
                            $content = $upload['info']['name'];
                        }
                    }
                    $lecture = array(
                        'type' => escape($type),
                        'chapter' => $chapterId,
                        'school' => $user->school,
                        'branch' => $user->branch,
                        'class' => escape(input('class')),
                        'course' => escape(input('course')),
                        'title' => escape($_POST['title'.$chapterIndex][$key]),
                        'indexing' => escape($_POST['lectureindexing'.$chapterIndex][$key]),
                        'description' => escape($_POST['description'.$chapterIndex][$key]),
                        'content' => escape($content)
                    );
                    if (!empty($_POST['lectureid'.$chapterIndex][$key])) {
                        unset($lecture['chapter']);
                        if ($type == "pdf" || $type == "downloads" || $type == "video") {
                            unset($lecture['content']);
                        }
                        Database::table('lectures')->where("id", $_POST['lectureid'.$chapterIndex][$key])->update($lecture);
                    }else{
                        Database::table('lectures')->insert($lecture);
                    }

                }
            }

        }
        return response()->json(responder("success", sch_translate("content_updated"), sch_translate("content_successfully_updated"), "reload()"));
    }
    
    /**
     * Curriculum manager
     * 
     * @return \Pecee\Http\Response
     */
    public function sections() {
        $json = '{
            "chapter": "<div class=\"panel panel-default chapter newly\"> <div class=\"panel-heading\"> <div class=\"chapter-drag\"><i class=\"mdi mdi-drag\"><\/i><\/div><a href=\"\" class=\"btn btn-black btn-sm pull-right manage-class delete-item\" data-type=\"chapter\" title=\"Delete chapter\"><i class=\" mdi mdi-delete\"><\/i><\/a> <h4 class=\"panel-title\"> <a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#chapter1\"> <span class=\"indexing\">1.)<\/span>  <span class=\"panel-label\">New Chapter<\/span> <\/a> <\/h4> <\/div><div id=\"chapter1\" class=\"panel-collapse collapse chapter-body in show\"> <div class=\"panel-body\"> <div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Chapter Title<\/label> <input type=\"text\" class=\"form-control chapter-title\" name=\"chaptertitle[]\" placeholder=\"Chapter Title\" required><input type=\"hidden\" class=\"chapter-indexing\" name=\"indexing[]\"> <input type=\"hidden\" name=\"chapterid[]\" value=\"0\"> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Chapter Description<\/label> <textarea class=\"form-control\" name=\"description[]\" placeholder=\"Chapter Description\" rows=\"3\" required><\/textarea> <\/div><\/div><\/div><div class=\"divider\"><\/div><p>Below are lectures of this chapter<\/p><div class=\"chapter-lecture-holder\"> <div class=\"empty-section\"> <i class=\"mdi mdi-clipboard-text\"><\/i> <h5>No lectures here, add a new one below!<\/h5> <\/div><\/div><div class=\"lecture-buttons-holder\"> <p class=\"text-thin text-muted mb-5\">Add another lecture<\/p><div class=\"btn-group btn-group-justified\"> <a href=\"\" class=\"btn btn-black add-lecture\" data-type=\"video\"><i class=\" mdi mdi-play-circle\"><\/i> Video<\/a> <a href=\"\" class=\"btn btn-black add-lecture\" data-type=\"downloads\"><i class=\" mdi mdi-cloud-download\"><\/i> Downloads<\/a> <a href=\"\" class=\"btn btn-black add-lecture\" data-type=\"link\"><i class=\" mdi mdi-link-variant\"><\/i> Link<\/a> <a href=\"\" class=\"btn btn-black add-lecture\" data-type=\"pdf\"><i class=\" mdi mdi-file-pdf-box\"><\/i> PDF<\/a> <a href=\"\" class=\"btn btn-black add-lecture\" data-type=\"text\"><i class=\" mdi mdi-note-text\"><\/i> Text<\/a> <\/div><\/div><\/div><\/div><\/div>",
            "lecture": {
                "link": "<div class=\"panel panel-default lecture newly\"> <div class=\"panel-heading\"> <div class=\"chapter-drag\"><i class=\"mdi mdi-drag\"><\/i><\/div><a href=\"\" class=\"btn btn-black btn-sm pull-right manage-class delete-item\" data-type=\"lecture\" title=\"Delete Lecture\"><i class=\" mdi mdi-delete\"><\/i><\/a> <h4 class=\"panel-title\"> <a data-toggle=\"collapse\" data-parent=\"#lecture-accordion\" href=\"#lecture1\"> <div class=\"lecture-type\"><i class=\" mdi mdi-link-variant\"><\/i> <\/div><span class=\"indexing\">1.)<\/span>   <span class=\"panel-label\">New Lecture<\/span><\/a> <\/h4> <\/div><div id=\"lecture1\" class=\"panel-collapse collapse lecture-body in show\"> <div class=\"panel-body\"> <div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Title<\/label> <input type=\"text\" class=\"form-control chapter-title\" name=\"title[]\" original-name=\"title\" placeholder=\"Lecture Title\" required><input type=\"hidden\" class=\"lecture-indexing\" name=\"lectureindexing[]\" original-name=\"lectureindexing\"> <input type=\"hidden\" name=\"lectureid[]\" original-name=\"lectureid\" value=\"0\"> <input type=\"hidden\" name=\"type[]\" original-name=\"type\" value=\"link\"><input type=\"file\" name=\"content[]\" value=\"z:\/null.null\" class=\"hidden\" original-name=\"content\"> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Description<\/label> <textarea class=\"form-control\" name=\"description[]\" original-name=\"description\" placeholder=\"Lecture Description\" rows=\"3\" required><\/textarea> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Enter link\/URL<\/label> <input type=\"url\" class=\"form-control\" name=\"content[]\" original-name=\"content\" placeholder=\"Enter link\/URL\" required> <\/div><\/div><\/div><\/div><\/div><\/div>",
                "text": "<div class=\"panel panel-default lecture newly\"> <div class=\"panel-heading\"> <div class=\"chapter-drag\"><i class=\"mdi mdi-drag\"><\/i><\/div><a href=\"\" class=\"btn btn-black btn-sm pull-right manage-class delete-item\" data-type=\"lecture\" title=\"Delete Lecture\"><i class=\" mdi mdi-delete\"><\/i><\/a> <h4 class=\"panel-title\"> <a data-toggle=\"collapse\" data-parent=\"#lecture-accordion\" href=\"#lecture2\"> <div class=\"lecture-type\"><i class=\" mdi mdi-note-text\"><\/i> <\/div><span class=\"indexing\">1.)<\/span>   <span class=\"panel-label\">New Lecture<\/span><\/a> <\/h4> <\/div><div id=\"lecture2\" class=\"panel-collapse collapse lecture-body in show\"> <div class=\"panel-body\"> <div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Title<\/label> <input type=\"text\" class=\"form-control chapter-title\" name=\"title[]\" original-name=\"title\" placeholder=\"Lecture Title\" required><input type=\"hidden\" class=\"lecture-indexing\" name=\"lectureindexing[]\" original-name=\"lectureindexing\"> <input type=\"hidden\" name=\"lectureid[]\" original-name=\"lectureid\" value=\"0\"> <input type=\"hidden\" name=\"type[]\" original-name=\"type\" value=\"text\"><input type=\"file\" name=\"content[]\" class=\"hidden\" value=\"z:\/null.null\" original-name=\"content\">  <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Description<\/label> <textarea class=\"form-control\" name=\"description[]\" original-name=\"description\" placeholder=\"Lecture Description\" rows=\"3\" required><\/textarea> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Text body<\/label> <textarea class=\"form-control\" name=\"content[]\" original-name=\"content\" placeholder=\"Lecture Description\" rows=\"6\" required><\/textarea> <\/div><\/div><\/div><\/div><\/div><\/div>",
                "downloads": "<div class=\"panel panel-default lecture newly\"> <div class=\"panel-heading\"> <div class=\"chapter-drag\"><i class=\"mdi mdi-drag\"><\/i><\/div><a href=\"\" class=\"btn btn-black btn-sm pull-right manage-class delete-item\" data-type=\"lecture\" title=\"Delete Lecture\"><i class=\" mdi mdi-delete\"><\/i><\/a> <h4 class=\"panel-title\"> <a data-toggle=\"collapse\" data-parent=\"#lecture-accordion\" href=\"#lecture4\"> <div class=\"lecture-type\"><i class=\" mdi mdi-cloud-download\"><\/i> <\/div><span class=\"indexing\">1.)<\/span>   <span class=\"panel-label\">New Lecture<\/span><\/a> <\/h4> <\/div><div id=\"lecture4\" class=\"panel-collapse collapse lecture-body in show\"> <div class=\"panel-body\"> <div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Title<\/label> <input type=\"text\" class=\"form-control chapter-title\" name=\"title[]\" original-name=\"title\" placeholder=\"Lecture Title\" required><input type=\"hidden\" class=\"lecture-indexing\" name=\"lectureindexing[]\" original-name=\"lectureindexing\"> <input type=\"hidden\" name=\"lectureid[]\" original-name=\"lectureid\" value=\"0\"> <input type=\"hidden\" name=\"type[]\" original-name=\"type\" value=\"downloads\"><input type=\"hidden\" name=\"content[]\" value=\"null\" original-name=\"content\">  <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Description<\/label> <textarea class=\"form-control\" name=\"description[]\" original-name=\"description\" placeholder=\"Lecture Description\" rows=\"3\" required><\/textarea> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Upload resource to download<\/label> <input type=\"file\" class=\"dropify\" name=\"content[]\" original-name=\"content\" placeholder=\"Upload resource to download\" data-allowed-file-extensions=\"pdf png doc docx jpg\" required> <span class=\"text-xs\">Only <strong>pdf, png, doc, docx & jpg<\/strong> extensions allowed <\/span> <\/div><\/div><\/div><\/div><\/div><\/div>",
                "pdf": "<div class=\"panel panel-default lecture newly\"> <div class=\"panel-heading\"> <div class=\"chapter-drag\"><i class=\"mdi mdi-drag\"><\/i><\/div><a href=\"\" class=\"btn btn-black btn-sm pull-right manage-class delete-item\" data-type=\"lecture\" title=\"Delete Lecture\"><i class=\" mdi mdi-delete\"><\/i><\/a> <h4 class=\"panel-title\"> <a data-toggle=\"collapse\" data-parent=\"#lecture-accordion\" href=\"#lecture3\"> <div class=\"lecture-type\"><i class=\" mdi mdi-file-pdf-box\"><\/i> <\/div><span class=\"indexing\">1.)<\/span>   <span class=\"panel-label\">New Lecture<\/span><\/a> <\/h4> <\/div><div id=\"lecture3\" class=\"panel-collapse collapse lecture-body in show\"> <div class=\"panel-body\"> <div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Title<\/label> <input type=\"text\" class=\"form-control chapter-title\" name=\"title[]\" original-name=\"title\" placeholder=\"Lecture Title\" required> <input type=\"hidden\" class=\"lecture-indexing\" name=\"lectureindexing[]\" original-name=\"lectureindexing\"> <input type=\"hidden\" name=\"lectureid[]\" original-name=\"lectureid\" value=\"0\"> <input type=\"hidden\" name=\"type[]\" original-name=\"type\" value=\"pdf\"><input type=\"hidden\" name=\"content[]\" original-name=\"content\" value=\"null\"> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Description<\/label> <textarea class=\"form-control\" name=\"description[]\" original-name=\"description\" placeholder=\"Lecture Description\" rows=\"3\" required><\/textarea> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Upload PDF<\/label> <input type=\"file\" class=\"dropify\" name=\"content[]\" original-name=\"content\" data-allowed-file-extensions=\"pdf\" accept=\"application\/pdf\" placeholder=\"Upload PDF\" required>  <span class=\"text-xs\">Only <strong>pdf<\/strong> extensions allowed <\/span> <\/div><\/div><\/div><\/div><\/div><\/div>",
                "video": "<div class=\"panel panel-default lecture newly\"> <div class=\"panel-heading\"> <div class=\"chapter-drag\"><i class=\"mdi mdi-drag\"><\/i><\/div><a href=\"\" class=\"btn btn-black btn-sm pull-right manage-class delete-item\" data-type=\"lecture\" title=\"Delete Lecture\"><i class=\" mdi mdi-delete\"><\/i><\/a> <h4 class=\"panel-title\"> <a data-toggle=\"collapse\" data-parent=\"#lecture-accordion\" href=\"#lecture5\"> <div class=\"lecture-type\"><i class=\" mdi mdi-play-circle\"><\/i> <\/div><span class=\"indexing\">1.)<\/span>   <span class=\"panel-label\">New Lecture<\/span><\/a> <\/h4> <\/div><div id=\"lecture5\" class=\"panel-collapse collapse lecture-body in show\"> <div class=\"panel-body\"> <div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Title<\/label> <input type=\"text\" class=\"form-control chapter-title\" name=\"title[]\" original-name=\"title\" placeholder=\"Lecture Title\" required><input type=\"hidden\" class=\"lecture-indexing\" name=\"lectureindexing[]\" original-name=\"lectureindexing\"> <input type=\"hidden\" name=\"lectureid[]\" original-name=\"lectureid\" value=\"0\"> <input type=\"hidden\" name=\"type[]\" original-name=\"type\" value=\"video\"><input type=\"hidden\" name=\"content[]\" original-name=\"content\" value=\"null\"> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Lecture Description<\/label> <textarea class=\"form-control\" name=\"description[]\" original-name=\"description\" placeholder=\"Lecture Description\" rows=\"3\" required><\/textarea> <\/div><\/div><\/div><div class=\"form-group\"> <div class=\"row\"> <div class=\"col-md-12\"> <label>Upload Video<\/label> <input type=\"file\" class=\"dropify\" name=\"content[]\" original-name=\"content\" placeholder=\"Upload Video\" data-allowed-file-extensions=\"mp4\" accept=\"video\/*\" required> <span class=\"text-xs\">Only <strong>mp4<\/strong> extensions allowed <\/span>  <\/div><\/div><\/div><\/div><\/div><\/div>"
            }
        }';
        return $json;
    }
    
    
    
}
 