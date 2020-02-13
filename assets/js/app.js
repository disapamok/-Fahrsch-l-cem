// Jquery start
$(document).ready(function () {
  let app_base_url = $('#app_base_url').val();
  // Notification toggle

  let urlParams = new URLSearchParams(window.location.search);

  // Check if exist then trigger click noCheckCircle
  if(urlParams.has('read_this') && $.isNumeric(urlParams.get('read_this'))){

    let index = urlParams.get('read_this');

    // do ajax
    var formData = new FormData();
    formData.append('csrf-token', Cookies.get('CSRF-TOKEN'));
    formData.append('id', index);

    $.ajax({
      url : app_base_url + 'notifications/mark-as-read',             
      type : "POST",
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      error : function(error) {
         console.log(error);
      },
      success : function(data) {
        // Replace url
        window.location.href = window.location.origin + window.location.pathname;
      }
    });
  }

  $('#notificationToggler').on('click', function () {

    let specific_url = '';

    $.ajax({
      type: "GET",
      // url: "/notifications/json",
      url: app_base_url + "notifications/json",
      success: function (data) {
        $('.notification-list').empty();
        // Hide badge in notification
        $("#notiBadge").addClass('d-none');
        if (data.length === 0) {
          $("<div class='col-md-12'>" +
            "<div class='empty'>" +
            "<i class='mdi mdi-alert-circle-outline'></i>" +
            "<h3>{{sch_translate('empty_here')}}</h3>" +
            "</div>" +
            "</div>").appendTo('.notification-list');
        } else {
          $.each(JSON.parse(data), function (index, item) {
            $('<div class="single-notification">').appendTo('.notification-list');

            if (item.type === 'message') {
              specific_url = app_base_url+ 'communication';
              $("<div class='mt-2 ml-1 notification-icon bg-purple'><a href='"+specific_url+"' class='text-white'> <i class='mdi mdi-message-text-outline'></i></a></div>").appendTo('.notification-list');
            } else if (item.type === 'delete') {
              specific_url = app_base_url+ 'invoices';
              $("<div class='mt-2 ml-1 notification-icon bg-danger'><a href='"+specific_url+"' class='text-white'> <i class='mdi mdi-delete'></i></a> </div>").appendTo('.notification-list');
            } else if (item.type === 'calendar') {
              specific_url = app_base_url+ 'scheduling';
              $("<div class='mt-2 ml-1 notification-icon bg-secondary'><a href='"+specific_url+"' class='text-white'> <i class='mdi mdi-calendar'></i></a> </div>").appendTo('.notification-list');
            } else if (item.type === 'newaccount') {
              specific_url = app_base_url+ 'students';
              $("<div class='mt-2 ml-1 notification-icon bg-warning'><a href='"+specific_url+"' class='text-white'> <i class='mdi mdi-account-plus'></i></a> </div>").appendTo('.notification-list');
            } else if (item.type === 'payment') {
              specific_url = app_base_url+ 'invoices';
              $("<div class='mt-2 ml-1 notification-icon bg-success'><a href='"+specific_url+"' class='text-white'> <i class='mdi mdi-credit-card'></i></a> </div>").appendTo('.notification-list');
            }

            if(item.is_read ==0 ){
              $("<div class='p-2 bg-gray notification-div notification-div_"+item.id+" hoverMe' unread index='"+item.id+"'>"+
                  "<div class='notification-message mt-0'>" +
                    "<div class='hideIconCheck hideIconCheck_"+item.id+" d-none'>"+
                      "<i index='"+item.id+"' class='d-none checkCircle checkCircle_"+item.id+" float-right fa fa-check-circle text-secondary mt-3' title='Mark as Unread'></i>" +
                      "<i index='"+item.id+"' class='noCheckCircle noCheckCircle_"+item.id+" float-right fa fa-circle text-secondary mt-3' title='Mark as Read'></i>"+
                    "</div>" +
                    "<a href='"+specific_url+"?read_this="+item.id+"'> <div class='notification-dat'>" + item.message + "</div>" + item.created_at + "</a>"+
                  "</div>"+
                "</div>"
                ).appendTo('.notification-list');
            }
            else{
              $("<div class='p-2 notification-div notification-div_"+item.id+" hoverMe' index='"+item.id+"'>"+
                  "<div class='notification-message mt-0'>"+
                    "<div class='hideIconCheck hideIconCheck_"+item.id+" d-none'>"+
                      "<i index='"+item.id+"' class='checkCircle checkCircle_"+item.id+" float-right fa fa-check-circle text-secondary mt-3' title='Mark as Unread'></i>" +
                      "<i index='"+item.id+"' class='d-none noCheckCircle noCheckCircle_"+item.id+" float-right fa fa-circle text-secondary mt-3' title='Mark as Read'></i>"+
                    "</div>" +
                      "<a href='"+specific_url+"'> <div class='notification-dat'>" + item.message + "</div>" + item.created_at + "</a>"+
                  "</div>"+
                "</div>"
                ).appendTo('.notification-list');
            }
            

            $('<div>').appendTo('.notification-list');
          });
        }
      },
      complete: function (data) {
        // console.log('Done');
      },
      error: function (request, status, err) {
        // console.log('error');
      }
    });
  });

  $(document).on('mouseover', '.notification-div', function(){

    let index = $(this).attr('index');

    // check if unread the remove bg-gray
    let unread = $('.notification-div_'+index).attr('unread');

    if (typeof unread !== typeof undefined && unread !== false) {
      $('.notification-div_'+index).removeClass('bg-gray');
    }

    // Remove removeAtt to avoid close the tab
    $('#notificationToggler').removeAttr('data-toggle');

    // Hide all
    $('.hideIconCheck').addClass('d-none');

    // Show icon check
    $('.hideIconCheck_'+index).removeClass('d-none');

  });

  $(document).on('mouseleave','.notification-div', function(){
    let index = $(this).attr('index');
    // check if unread then add class bg-gray
    let unread = $('.notification-div_'+index).attr('unread');

    if (typeof unread !== typeof undefined && unread !== false) {
      $('.notification-div_'+index).addClass('bg-gray');
    }

  });

  $(document).on('mouseover', '#notificationToggler', function(){
    // Back toggle-data attribute
    let dataToggle = $(this).attr("data-toggle");
    if (typeof dataToggle == typeof undefined || dataToggle == false){
      $(this).attr("data-toggle", "dropdown");
    }
  });

  // Put post check
  $(document).on('mouseover','.noCheckCircle', function(e){
    let index = e.currentTarget.getAttribute("index");
    $('.noCheckCircle_'+index).addClass('fa-check-circle');
  });
  // Remove post check
  $(document).on('mouseleave','.noCheckCircle', function(){
    $('.noCheckCircle').addClass('fa-circle');
  });

  // Mark as read
  $(document).on('click', '.noCheckCircle', function(e){

    let index = e.currentTarget.getAttribute("index");

    if(urlParams.get('read_this') != null){
      index = urlParams.get('read_this');
    }

    // do ajax
    var formData = new FormData();
    formData.append('csrf-token', Cookies.get('CSRF-TOKEN'));
    formData.append('id', index);

    $.ajax({
      url : app_base_url + 'notifications/mark-as-read',             
      type : "POST",
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      error : function(error) {
         console.log(error);
      },
      success : function(data) {
         // Hide itselt
        $('.noCheckCircle_'+index).addClass('d-none');
        // Show check icon
        $('.checkCircle_'+index).removeClass('d-none');
        // Remove bg gray
        $('.notification-div_'+index).removeClass('bg-gray');

        $('.notification-div_'+index).removeAttr('unread');
      }
    });

  });

  // Mark as unread
  $(document).on('click', '.checkCircle', function(e){

    let index = e.currentTarget.getAttribute("index");
    // do ajax
    var formData = new FormData();
    formData.append('csrf-token', Cookies.get('CSRF-TOKEN'));
    formData.append('id', index);

    $.ajax({
      url : app_base_url + 'notifications/mark-as-unread',             
      type : "POST",
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      error : function(error) {
         console.log(error);
      },
      success : function(data) {
        // Hide itselt
        $('.checkCircle_'+index).addClass('d-none');
        // Show check icon
        $('.noCheckCircle_'+index).removeClass('d-none');
        // Remove bg gray
        $('.notification-div_'+index).addClass('bg-gray');

        $('.notification-div_'+index).attr('unread','yow');
      }
    });
  });

  $(document).on('click', function(e){
    // $('.notification-list').empty();
  });

  // sidebar - scroll container
  $('.slimscroll-menu').slimscroll({
    height: 'auto',
    position: 'right',
    size: "3px",
    color: '#9ea5ab',
    wheelStep: 5,
    touchScrollStep: 50
  });


  $('aside a').each(function () {
    if ($(this).attr('href') == window.location.pathname) {
      $(this).addClass('active');
    }
  });


  // close humbager
  $(".main-content").click(function () {
    if ($("aside").hasClass("open-menu")) {
      $("aside").removeClass("open-menu");
    }
  });
  $(".close-aside").click(function (event) {
    event.preventDefault();
    $("aside").removeClass("open-menu");
  });

  // humbager
  $(".humbager").click(function (event) {
    event.preventDefault();
    if ($("aside").hasClass("open-menu")) {
      $("aside").removeClass("open-menu");
    } else {
      $("aside").addClass("open-menu");
    }
  });

  // tooltip
  $('[data-toggle="tooltip"]').tooltip();

});

// toogle search
$(".toggle-search").click(function () {
  $(".search-filter").slideToggle();
});

$("body").on("click", ".remove-parent", function () {
  $(this).closest($(this).attr("parent")).remove();
})

// delete an item
$(".delete").click(function (event) {
  event.preventDefault();
  swal({
    title: sch_TranslateJsWord('are_you_sure'),
    text: sch_TranslateJsWord('this_item_will_be_deleted_and_will_not_recovered'),
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ff1a1a",
    confirmButtonText: "Yes, delete it!",
    closeOnConfirm: true
  }, function () {
    toastr.success(sch_TranslateJsWord('successfully_deleted'), sch_TranslateJsWord('successful'));
  });
});

// schedule class on profile
$(".scheduleclass").submit(function (event) {
  event.preventDefault();
  var title = $("#scheduleclass").find("input[name=studentname]").val() + " for a " + $("#scheduleclass").find("select[name=class]").val() + " with " + $("#scheduleclass").find("select[name=instructor]").val();
  calendar.fullCalendar('renderEvent', {
      title: title,
      start: $("#scheduleclass").find("input[name=start]").val(),
      end: $("#scheduleclass").find("input[name=end]").val(),
      className: 'primary',
      allDay: false
    },
    true // make the event "stick"
  );
  calendar.fullCalendar('unselect');
  $("#scheduleclass").modal("hide");
});

// auth page switch pages
$(".auth-switch").click(function (event) {
  event.preventDefault();
  $(".register, .forgot, .reset, .login").hide();
  $($(this).attr("show")).show();
});

//Disable every first option
$('option[value="0"]').attr('disabled', true);



/*
 * Toogle SMS Gateway
 */
$("select[name=DEFAULT_SMS_GATEWAY]").change(function () {
  gateway = $(this).val();
  if (gateway === "africastalking") {
    $(".twilio").hide();
    $(".africastalking").show();
  } else if (gateway === "twilio") {
    $(".twilio").show();
    $(".africastalking").hide();
  }
});

/*
 * Toogle reminder SMS & Email
 */
$(".reminders-holder").on("change", ".send_via", function () {
  send_via = $(this).val();
  subject = $(this).closest(".remider-item").find(".email-subject");
  if (send_via === "sms") {
    subject.hide();
    subject.find("input").val('');
    subject.find("input").attr("required", false);
  } else if (send_via === "email") {
    subject.show();
    subject.find("input").attr("required", true);
  }
});



/*
 * add reminder
 */
$(".add-reminder").click(function () {
  $('.collapse').collapse('hide');
  var reminderKey = random(),
    reminderNumber = parseInt($(".reminders-holder").find('.panel').length) + 1;
  $(".reminders-holder").append(`
                                            <!-- reminder -->
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <span class="delete-reminder remove-parent" parent=".panel" title="Delete reminder"><i class="mdi mdi-delete"></i></span>
                                                    <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse` + reminderKey + `">Reminder #<span class="count">` + reminderNumber + `</span></a></h4>
                                                </div>
                                                <div class="panel-collapse collapse in show" id="collapse` + reminderKey + `">
                                                    <div class="panel-body m-15">
                                                        <div class="remider-item">
                                                            <div class="form-group">
                                                                <div class="row">
                                                                  <div class="col-md-6">
                                                                      <label>` + sch_TranslateJsWord('reminder_type') + `</label> 
                                                                      <select class="form-control" name="type[]" required="">
                                                                          <option value="Payment">` + sch_TranslateJsWord('payment') + `</option>
                                                                          <option value="Class">` + sch_TranslateJsWord('class') + `</option>
                                                                      </select>
                                                                  </div>
                                                                  <div class="col-md-6">
                                                                      <label>` + sch_TranslateJsWord('send_via') + `</label> 
                                                                      <select class="form-control send_via" name="send_via[]" required="">
                                                                          <option value="email">` + sch_TranslateJsWord('email') + `</option>
                                                                          <option value="sms">` + sch_TranslateJsWord('sms') + `</option>
                                                                      </select>
                                                                  </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label>` + sch_TranslateJsWord('days') + `</label>
                                                                        <input type="number" class="form-control"  name="days[]" placeholder="` + sch_TranslateJsWord('days') + `" value="1" required="" min="0" required="">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label>Timing</label> 
                                                                        <select class="form-control" name="timing[]" required="">
                                                                            <option value="before_due">` + sch_TranslateJsWord('before_due_date') + `</option>
                                                                            <option value="after_due">` + sch_TranslateJsWord('after_due_date') + `</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group email-subject">
                                                                  <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label>` + sch_TranslateJsWord('email_subject') + `</label> 
                                                                        <input class="form-control" name="subject[]" placeholder="` + sch_TranslateJsWord('email_subject') + `" required type="text" value="Payment reminder">
                                                                    </div>
                                                               </div>
                                                            </div>
                                                            <div class="form-group">
                                                                  <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label>` + sch_TranslateJsWord('message') + `</label> 
                                                                        <textarea class="form-control" name="message[]" required rows="10">` + sch_TranslateJsWord("payment_reminder_text") + `` + school + `
                                    </textarea>
                                                                    <p class="help">` + sch_TranslateJsWord('supported_tags__for_payment') + `: <code>[firstname]</code>, <code>[lastname]</code>, <code>[amountdue]</code>, <code>[duedate]</code> & <code>[course]</code>. Class: <code>[firstname]</code>, <code>[lastname]</code>, <code>[course]</code>, <code>[class]</code>, <code>[classdate]</code>, <code>[classtime]</code> & <code>[instructorname]</code> </p>
                                                                  </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`);
  reminderIndexing();
})

/*
 * delete reminder
 */
$(".reminders-holder").on("click", ".delete-reminder", function () {
  $(this).closest(".panel").remove();
  reminderIndexing();
});

/*
 * Number reminder cards
 */
function reminderIndexing() {
  $(".reminders-holder").find("span.count").each(function (index) {
    $(this).text(index + 1);
  });
}

/*
 * Pass sigle variable to input & launch modal
 */
$(".pass-data").click(function (event) {
  event.preventDefault();
  inputName = $(this).attr("input");
  inputValue = $(this).attr("value");
  modal = $(this).attr("modal");
  $("input[name=" + inputName + "]").val(inputValue);
  $(modal).modal({
    show: true,
    backdrop: 'static',
    keyboard: false
  });
})



/*
 * Mark notifications as read
 */
function readNotifications(url) {
  server({
    url: url,
    data: {
      "csrf-token": Cookies.get("CSRF-TOKEN")
    },
    loader: false
  });
}

/*
 * When course is selected when adding new student
 */
$("select[name=newcourse]").change(function () {
  course = $(this).val();
  if (course !== '') {
    $(".newamount").show();
  } else {
    $(".newamount").hide();
  }
})

/*
 * When course is selected when adding new student
 */
$(".newamount input").keyup(function () {
  var amountpaid = $(this).val();

  if (amountpaid.length > 0) {
    $(".newmethod").show();
  } else {
    $(".newmethod").hide();
  }
})

/*
 * Edit schedule
 */
function updateSchedule(scheduleid) {
  $(".scheduleupdate-holder").html('<div class="loader-box mt-40"><div class="circle-loader"></div></div>');
  $("#scheduleupdate").modal("show");
  var posting = $.post(schedulesUpdateView, {
    "scheduleid": scheduleid,
    "csrf-token": Cookies.get("CSRF-TOKEN")
  });
  posting.done(function (response) {
    $(".scheduleupdate-holder").html(response);
  });
}