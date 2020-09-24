$(document).ready(function() {
//alert("Hello "+authorization);
/*****************************************************
UPDATE USER
****************************************************/
$('#updateUserForm').submit(function (e) {
    e.preventDefault();
	// alert(authorization+" => "+$(this).serialize());
  var base_url = $(this).attr('data-url');
	var form = $('#updateUserForm');
    var data = new FormData(this);
    document.getElementById("updateUserForm").style.display = 'none';
	document.getElementById("registerOverlay").style.display = 'block';
	document.getElementById("errorMsgBlock").style.display = 'none';
	$("#errorMsg").text('');
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: data,    //  $(this).serialize(),
         beforeSend: function(request) { 
               request.setRequestHeader("Authorization", setheader.authorization);
             },
		processData: false, 
        contentType: false, 
		dataType: 'json',
        success: function(data) {
		//alert("Hello "+data);
		if(data.error){
		document.getElementById("registerOverlay").style.display = 'none';     
		document.getElementById("updateUserForm").style.display = 'block';
		document.getElementById("errorMsgBlock").style.display = 'block';
	    $("#errorMsg").text(data.message);
		}else{
document.getElementById("errorMsgBlock").style.display = 'block';
$("#errorMsg").text(data.message);			
setTimeout(function(){
location = base_url+"/manage-schools";
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});

$('#updateStudentForm').submit(function (e) {
    e.preventDefault();
	// alert(authorization+" => "+$(this).serialize());
	var form = $('#updateStudentForm');
    var data = new FormData(this);
    document.getElementById("updateStudentForm").style.display = 'none';
	document.getElementById("studentOverlay").style.display = 'block';
	document.getElementById("studentErrorMsgBlock").style.display = 'none';
	$("#studentErrorMsg").text('');
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: data,     // $(this).serialize(),
         beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		processData: false, 
        contentType: false, 
		dataType: 'json',
        success: function(data) {
		//alert("Hello "+data);
		if(data.error){
		document.getElementById("studentOverlay").style.display = 'none';     
		document.getElementById("updateStudentForm").style.display = 'block';
		document.getElementById("studentErrorMsgBlock").style.display = 'block';
	    $("#studentErrorMsg").text(data.message);
		}else{
document.getElementById("studentErrorMsgBlock").style.display = 'block';
$("#studentErrorMsg").text(data.message);			
setTimeout(function(){
location = base_url+"/manage-schools";
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});


$('#updatePassForm').submit(function (e) {
    e.preventDefault();
	// alert(authorization+" => "+$(this).serialize());
  var form = $('#updatePassForm');
    var data = new FormData(this);
    document.getElementById("updatePassForm").style.display = 'block';
	document.getElementById("accountOverlay").style.display = 'block';
	document.getElementById("accountErrorMsgBlock").style.display = 'none';
	$("#accountErrorMsg").text('');
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: data,   // $(this).serialize(),
         beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		dataType: 'json',
        success: function(data) {
		//alert("Hello "+data);
		if(data.error){
		document.getElementById("accountOverlay").style.display = 'none';     
		document.getElementById("updatePassForm").style.display = 'block';
		document.getElementById("accountErrorMsgBlock").style.display = 'block';
	    $("#accountErrorMsg").text(data.message);
		}else{
document.getElementById("accountErrorMsgBlock").style.display = 'block';
$("#accountErrorMsg").text(data.message);		
setTimeout(function(){
//location = base_url+"/manage-schools";
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});


$('#addUserImageForm').submit(function (e) {
    e.preventDefault();
	var form = $('#addUserImageForm');
    var data = new FormData(this);
    //alert("authorization: "+authorization);
    document.getElementById("user_image_overlay").style.display = 'block';
	document.getElementById("addUserImageForm").style.display = 'none';
    //alert(data);
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
        data: data,
		processData: false, 
        contentType: false, 
		dataType: 'json',
        success: function(data) {
		if(data.error){
    document.getElementById("user_image_overlay").style.display = 'none';
	document.getElementById("addUserImageForm").style.display = 'block';
			swal({
  title: 'Failed',
  html: '<b>'+data.message+'</b>',
  type: 'error',
  showCancelButton: false,
  allowOutsideClick: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
		}else{
setTimeout(function(){
//location = base_url+"/edit-document/"+data.qcode;
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        //alert(xhr.status);
        //alert(thrownError);
      }
    });
});

});