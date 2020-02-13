/*!
 * Builder.js
 * Version 1.0 - built Sat, Oct 6th 2018, 01:12 pm
 * https://simcycreative.com
 * Simcy Creative - <hello@simcycreative.com>
 * Private License
 */


/*
 *  when answer is selected by student
 */
  $(".answer-option").change(function (event) {
  	if ($(this).attr("type") === "radio") {
  		var question = $(this).closest(".form-group");
  		question.find(".hidden").prop("checked", true);
  	}
  	if ($(this).prop("checked")) {
  		$(this).prev().prop("checked", false);
  	}else{
  		$(this).prev().prop("checked", true);
  	}
 })


/*
 *  Retake exam
 */
  $(".retake-exam").click(function(){
  	$(".exam-complete").remove();
  	$(".exam-question-section").slideDown("slow");
  })