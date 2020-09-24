// const base_url = "https://smartschoolautomation.com/";
$(document).ready(function() {
/*
var editor = CKEDITOR.replace('body', {
     height: 200
});

editor.on('change', function( evt ) {
	var formMessages = $('#body');
    $(formMessages).val(evt.editor.getData());
});
if(authorization){
    alert(authorization+"  <= Auth");
}else{
    alert(" No Auth  ");
}
*/

$('.launchToastr').on('click', function() {
                $context = $(this).data('context');
                $message = $(this).data('message');
                $position = $(this).data('position');
    
                if ($context === '') {
                    $context = 'info';
                }
    
                if ($position === '') {
                    $positionClass = 'toast-bottom-right';
                } else {
                    $positionClass = 'toast-' + $position;
                }
    
                toastr.remove();
                toastr[$context]($message, '', {
                    positionClass: $positionClass
                });
            });
                
/*****************************************************
REGISTER USER
****************************************************/
$('#registrationForm').submit(function (e) {
    e.preventDefault();
	//alert(authorization+" "+$(this).serialize());
	var form = $('#registrationForm');
    var data = new FormData(this);
    document.getElementById("registrationForm").style.display = 'none';
	document.getElementById("registerOverlay").style.display = 'block';
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
         beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		dataType: 'json',
        success: function(data) {
         // alert("Received: "+data.message);
		if(data.error){
		document.getElementById("registerOverlay").style.display = 'none';        
		document.getElementById("registrationForm").style.display = 'block';
		document.getElementById("errorMsgBlock").style.display = 'block';
	    $("#errorMsg").text(data.message);
		}else{
setTimeout(function(){
location = base_url+"/manage-users";
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});


$('#parentRegistrationForm').submit(function (e) {
    e.preventDefault();
	alert(authorization);
	var form = $('#parentRegistrationForm');
    var data = new FormData(this);
    document.getElementById("parentRegistrationForm").style.display = 'none';
	//document.getElementById("projectOverlay").style.display = 'block';
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: data,
         beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		processData: false, 
        contentType: false, 
		dataType: 'json',
        success: function(data) {
		if(data.error){
		document.getElementById("parentRegistrationForm").style.display = 'block';
	    //document.getElementById("projectOverlay").style.display = 'none';    
		swal({
  title: 'Update Not Saved',
  html: '<b>'+data.message+'</b>',
  type: 'error',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
		}else{
setTimeout(function(){ 
location = base_url+"/manage-users";
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});



$('#profileForm').submit(function (e) {
    e.preventDefault();
	//alert($(this).serialize());
	document.getElementById("profileForm").style.display = 'none';
	document.getElementById("profileOverlay").style.display = 'block';
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
         beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
        data: $(this).serialize(),
		dataType: 'json',
        success: function(data) {
		if(data.error){
		document.getElementById("profileForm").style.display = 'none';
	    document.getElementById("profileOverlay").style.display = 'block';    
		swal({
  title: 'Update Not Saved',
  html: '<b>'+data.message+'</b>',
  type: 'error',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
		}else{
swal({
  title: 'Profile Updated',
  html: '<b>'+data.message+'</b>',
  type: 'success',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
setTimeout(function(){ 
    if(data.admin_mode == 1){
        location = base_url+"/manage-users";
    }else{
        location = base_url+"/profile";
    }
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});

$('#profileCredForm').submit(function (e) {
    e.preventDefault();
	//alert($(this).serialize());
	document.getElementById("profileCredForm").style.display = 'none';
	document.getElementById("pass_overlay").style.display = 'block';
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
		dataType: 'json',
        success: function(data) {
		if(data.error){
			document.getElementById("profileCredForm").style.display = 'block';
	        document.getElementById("pass_overlay").style.display = 'none';    
		swal({
  title: '<h4>ERROR</h4>',
  html: '<b>'+data.message+'</b>',
  type: 'error',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
		}else{
swal({
  title: 'Password Updated',
  html: '<b>'+data.message+'</b>',
  type: 'success',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
setTimeout(function(){ 
location = base_url+"/profile";
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});

	$('#editUserForm').submit(function (e) {
    e.preventDefault();
	//alert($(this).serialize());
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
         beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		dataType: 'json',
        success: function(data) {
		if(data.error){
		swal({
  title: '',
  html: '<b>'+data.message+'</b>',
  type: 'error',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
		}else{
		/*swal({
  title: 'User Updated',
  html: '<b>'+data.message+'</b>',
  type: 'success',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});	*/
setTimeout(function(){ 
location = base_url+"/manage-users";
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        //alert(xhr.status);
        //alert(thrownError);
      }
    });
});

$('.deleteUser').click(function (e) {
    e.preventDefault();
	var blogID = $(this).attr("data-id");
  swal({
  title: 'Delete Account',
  text: "Are you sure that you want to delete this account completely?",
  type: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Confirm Delete'
}).then((result) => {
  if (result.value) {
	var formData = {'user_id':blogID};
    $.ajax({
        url: base_url+"/users/delete",
        type: "POST",
          beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
        data: formData,
		dataType: 'json',
        success: function(data) {
		if(data.error){
		swal({
  title: 'Failed To Delete Account',
  html: '<b>'+data.message+'</b>',
  type: 'error',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
		}else{
/*
swal({
  title: 'Account deleted',
  html: '<b>'+data.message+'</b>',
  type: 'success',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
*/
setTimeout(function(){ 
window.location.replace(base_url+'/manage-users');
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        //alert("Operation failed. Please try again.");
        alert("There was an error."+xhr.responseText);
      }
    });
	/********** End of  Operation *********/
  }
});  
});

$('.deleteMessage').click(function (e) {
    e.preventDefault();
	var messageID = $(this).attr("data-id");
  swal({
  title: 'Delete Message',
  text: "Are you sure you want to delete this inquiry message received from website?",
  type: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Confirm Delete'
}).then((result) => {
  if (result.value) {
	var formData = {'message_id':messageID};
    $.ajax({
        url: base_url+"/messages/delete",
        type: "POST",
        data: formData,
          beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		dataType: 'json',
        success: function(data) {
		if(data.error){
		swal({
  title: 'Failed To Delete',
  html: '<b>'+data.message+'</b>',
  type: 'error',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
		}else{
swal({
  title: 'Inquiry deleted',
  html: '<b>'+data.message+'</b>',
  type: 'success',
  showCancelButton: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
setTimeout(function(){ 
window.location.replace(base_url+'/messages');
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
	/********** End of  Operation *********/
  }
});  
});	
});