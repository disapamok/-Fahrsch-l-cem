
$ = jQuery;
$(function(){
	$(".parsley-check").click(function(){
		setTimeout(function(){checkParsleyErrors();}, 120);
	})

	$("#fullname_register").change(function(){
		var fullname = $("#fullname_register").val();
		fullname = fullname.trim();
		var fullnamearray = fullname.split(" ");
		var fname = fullnamearray[0];
		var lastname = fullname.replace(fname+" ", "");
		$("#reg_fname").val(fname);
		$("#reg_lname").val(lastname);
	});

	$(".toggle-search").click(function(){
		$(".search-filter").removeClass("d-none");		
	})
})

$(".auth-separator").click(function(){
	// $(".auth-separator").removeClass("active");
	// $(this).addClass("active");
	$(".auth-separator").addClass("inactive");
	$(this).removeClass("inactive");
})

function checkParsleyErrors(){	
	$(".input-group-append").removeClass("border-danger");
	$(".input-group").each(function(){
		var thisholder = this;
		$(this).find(".parsley-error").each(function(){
			//if there is an error
			$(thisholder).find(".input-group-append").each(function(){
				$(this).addClass("border-danger");
			})
		})
	})
}