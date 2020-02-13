  /*!
   * Schedule.js
   * Version 1.0 - built Fri, Jan 25th 2019, 06:33 pm
   * https://simcycreative.com
   * Simcy Creative - <hello@simcycreative.com>
   * Private License
   */

  /*
   * initialize date time picker
   */
  $('input[name=start], input[name=end]').datetimepicker({
    weekStart: 1,
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
    language: 'de'
  });

  /*
   * Render schedules on calendar
   */
  function renderSchedules(type, date) {
    var currentDate = $('#calendar').fullCalendar('getDate');
    var currentMonth = $.fullCalendar.formatDate(currentDate, 'M');
    var currentYear = $.fullCalendar.formatDate(currentDate, 'YYYY');
    $('#calendar').fullCalendar('removeEvents');

    var posting = $.post(schedulesUrl, {
      "type": type,
      "date": date,
      "filter": filter,
      "filterid": filterid,
      "csrf-token": Cookies.get("CSRF-TOKEN")
    });

    posting.done(function(schedules) {

      schedules.forEach(function(schedule) {

        console.log(schedule);
        $('#calendar').fullCalendar('renderEvent', {
          id: schedule.id,
          title: schedule.title,
          description: schedule.description,
          start: schedule.start,
          className: schedule.className
        });
      });
    });
  }

  /*
   * initialize calender
   */
  $(document).ready(function() {
    $('#calendar').fullCalendar({
      selectable: true,
      header: {
        left: 'title',
        center: 'prev,next today',
        right: 'month,agendaWeek,agendaDay'
      },
      longPressDelay: 1,
      select: function(startDate, endDate) { 
        classStarts = $.fullCalendar.formatDate(startDate, 'DD MMMM YYYY - hh:mm t') + "m";
        $('input[name=start]').val(classStarts);
        classEnds = $.fullCalendar.formatDate(endDate, 'DD MMMM YYYY - hh:mm t') + "m";
        $('input[name=end]').val(classEnds);
        $("#scheduleclass").modal("show");
      },
      viewRender: function() {
        $("#calendar .loader-demo-box").remove();
        view = $('#calendar').fullCalendar('getView');
        renderSchedules(view.type, view.title);
      },
      eventRender: function(eventObj, $el) {
        $el.popover({
          title: eventObj.title,
          content: eventObj.description,
          trigger: 'hover',
          placement: 'top',
          container: 'body'
        });
      },
      eventClick: function(calEvent, jsEvent, view) {
        if (view.name === "month") {
          $(".popover").popover('hide');
          $('#calendar').fullCalendar('changeView', 'agendaDay', calEvent.start._i);
        } else {
          updateSchedule(calEvent.id);
        }
      }
    });
  });