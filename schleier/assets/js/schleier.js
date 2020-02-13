$(function(){		
	//handles changing of language
	$(document).on("change", ".sch-language-selector", function(){
		var language_choosen = $(this).val();
		changeLanguage(language_choosen);
	})
	$(document).on("click", ".sch-language-selector-word", function(){
		var language_choosen = $(this).attr("lang-value");
		changeLanguage(language_choosen);
	})
	
});

function changeLanguage(language_choosen){
	//save
	document.cookie = "sch_website_language="+language_choosen+"; expires=Thu, 18 Dec 2200 12:00:00 UTC; path=/";
	// reload  
	// window.location.href = window.location.href;
    location.reload();
}


$(function(){

    //translate all days in full calender
    var toBeChecked = [".fc-button", ".fc-today-button", ".fc-month-button", ".fc-agendaWeek-button", ".fc-agendaDay-button"];
    for (var i = 0; i < toBeChecked.length; i++) {
        $(document).on("click", toBeChecked[i], function(){
            resetFullCalender();
        })
    }
    resetFullCalender();
})

function resetFullCalender(){
    var ttoday = sch_TranslateJsWord("today");
    $(".fc-today-button").text(ttoday);  
    $(".fc-month-button").text(sch_TranslateJsWord("month"));  
    $(".fc-agendaWeek-button").text(sch_TranslateJsWord("Week"));  
    $(".fc-agendaDay-button").text(sch_TranslateJsWord("Day"));

    //translate all days in full calender
    $(".fc-day-header.fc-mon > span").text(sch_TranslateJsWord("mon"));  
    $(".fc-day-header.fc-tue > span").text(sch_TranslateJsWord("tue"));  
    $(".fc-day-header.fc-wed > span").text(sch_TranslateJsWord("wed"));  
    $(".fc-day-header.fc-thu > span").text(sch_TranslateJsWord("thu"));  
    $(".fc-day-header.fc-fri > span").text(sch_TranslateJsWord("fri"));  
    $(".fc-day-header.fc-sat > span").text(sch_TranslateJsWord("sat"));  
    $(".fc-day-header.fc-sun > span").text(sch_TranslateJsWord("sun"));
    $("#calendar > div.fc-view-container > div > table > tbody > tr > td > div.fc-day-grid.fc-unselectable > div > div.fc-bg > table > tbody > tr > td.fc-axis.fc-widget-content > span").text(sch_TranslateJsWord("all_day"))

    var toBechanged = [".fc-left > h2"];

    for (var i = 0; i < toBechanged.length; i++) {
        translateWordbeforeSpace(toBechanged[i]);
    }
}


//translate word b4 space in selector
function translateWordbeforeSpace(selector){
    $(document).find(selector).each(function(){
        var word = $(this).text();
        //get the first word before the space
        var word_ = (word.split(" "))[0];  
        var c_day = sch_TranslateJsWord(word_.toLowerCase());
        //replace occurence
        var word__ = word.replace(word_, c_day);
        $(this).text(word__);               
    })
}