/*!
 * player.js
 * Version 1.0 - built Sat, Oct 6th 2018, 01:12 pm
 * https://simcycreative.com
 * Simcy Creative - <hello@simcycreative.com>
 * Private License
 */


/*
 *  Load a lecture to player
 */
 function loadLecture(lectureid) {
 	$(".previous-lecture, .next-lecture").show();
 	$(".player-canvas").html('<div class="loader-box"><div class="circle-loader"></div></div>');
 	var posting = $.post(lectureUrl, {
					        "lectureid": lectureid,
					        "csrf-token": Cookies.get("CSRF-TOKEN")
					    });
	posting.done(function (response) {
		$(".player-canvas").html(response);
	});
	markComplete(lectureid);
 }


/*
 *  Mark Lecture has complete
 */
 function markComplete(lectureid) {
 	$(".lecture-item").removeClass("active");
 	$(".lecture-item[data-id="+lectureid+"]").addClass("active complete");
 }


/*
 *  Start Class
 */
 $(".start-class").click(function(){
 	var lectureId = $(".lecture-item").first().attr("data-id");
 	loadLecture(lectureId);
 })


/*
 *  Open a lecture
 */
 $(".lecture-item").click(function(){
 	var lectureId = $(this).attr("data-id");
 	loadLecture(lectureId);
 })


/*
 *  Next lecture
 */
 $(".next-lecture").click(function(){
 	var activeLecture = $(".lecture-item.active");
 	var activeChapter = $(".lecture-item.active").closest(".chapters-list");
 	if ($(".lecture-item.active").next().length) {
 		var lectureId = $(".lecture-item.active").next().attr("data-id");
 		loadLecture(lectureId);
 	}else if(activeChapter.next().length){
 		var nextChapter = activeChapter.next();
 		if (nextChapter.find(".lecture-item").length) {
	 		var lectureId = nextChapter.find(".lecture-item").first().attr("data-id");
	 		loadLecture(lectureId);
 		}
 	}else{
 		var totalLectures = $(".lecture-item").length;
 		var completedLectures = $(".lecture-item.complete").length;
 		if (totalLectures > completedLectures) {
 			notify(sch_TranslateJsWord('the_end'), sch_TranslateJsWord('you_have_reached_the_end_but_there_are_some_lectures_you_have_not_completed'), "warning", sch_TranslateJsWord('okay'));
 		}else{
 			notify(sch_TranslateJsWord('hooray'), sch_TranslateJsWord('you_have_successfully_completed_this_class'), "success", sch_TranslateJsWord('great'));
 		}
 	}
 });

/*
 *  Previous lecture
 */
 $(".previous-lecture").click(function(){
 	var activeLecture = $(".lecture-item.active");
 	var activeChapter = $(".lecture-item.active").closest(".chapters-list");
 	if ($(".lecture-item.active").prev().length) {
 		var lectureId = $(".lecture-item.active").prev().attr("data-id");
 		loadLecture(lectureId);
 	}else if(activeChapter.prev().length){
 		var nextChapter = activeChapter.prev();
 		if (nextChapter.find(".lecture-item").length) {
	 		var lectureId = nextChapter.find(".lecture-item").last().attr("data-id");
	 		loadLecture(lectureId);
 		}
 	}
 });



