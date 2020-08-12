$(document).ready(function() {
  alert(authorization);
 
  $('#deleteAssignmentForm').submit(function (e) {
     e.preventDefault();
     //alert($(this).serialize());
   
     deleteAssignment($(this));
   });
   

   function deleteAssignment($this){
    $.ajax({
      url: $this.attr('action'),
      type: "POST",
      data: $this.serialize(),
     dataType: 'json',
     beforeSend: function(request) {
             request.setRequestHeader("Authorization", authorization);
           },
      success: function(data) {
  if(data.error){
  Swal.fire({
    title: '',
    text: data.message,
    icon: 'error',
    showCancelButton: false,
    confirmButtonText: 'Okay',
cancelButtonText: 'false'
});
}else{  
//$("#responseMsg").text("Taking you to Dashboard");
Swal.fire({
title: '',
text: data.message,
icon: 'success',
showCancelButton: false,
confirmButtonText: 'Okay',
cancelButtonText: 'false'
});
setTimeout(function(){
window.location.replace(base_url+'manage-assignments');
}, 1800);
  }
      },
      error: function (xhr, ajaxOptions, thrownError) {
      alert("Error code: "+xhr.status+" => "+thrownError);
      //alert(thrownError);
    }
  });
   }
   /*****************************************************
   DELETE ASSIGNMENT
   ****************************************************/
  $('.button-remove').click(function (e) {
   e.preventDefault();
 var classID = $(this).attr("data-id");
 alert(classID+" => "+base_url+"apis/assignments/delete");
     /********** Start of  Operation *********/
     var formData = {'item_id':classID};
    //var base_url = "https://smartschoolautomation.com";
     $.ajax({
         url: base_url+"apis/assignments/delete",
         type: "POST",
         data: formData,
         //contentType: false,
         //
         processData: false,      
         beforeSend: function(request) {
                request.setRequestHeader("Authorization", authorization);
              },
          dataType: 'json',
         success: function(data) {
       alert("returned "+data.message);
     if(data.error){
       Swal.fire(
         'Delete Failed',
         data.message,
         'error')
     }else{
       Swal.fire(
         'Deleted!',
         data.message,
         'success')
     }
         },
         error: function (xhr, ajaxOptions, thrownError) {
       var msg = '';
       if (xhr.status === 0) {
           msg = 'Not connect.\n Verify Network.';
       } else if (xhr.status == 404) {
           msg = 'Requested page not found. [404]';
       } else if (xhr.status == 500) {
           msg = 'Internal Server Error [500].';
       } else if (thrownError === 'parsererror') {
           msg = 'Requested JSON parse failed.';
       } else if (thrownError === 'timeout') {
           msg = 'Time out error.';
       } else if (thrownError === 'abort') {
           msg = 'Ajax request aborted.';
       } else {
           msg = 'Uncaught Error.\n' + xhr.responseText;
       }
       alert("Error => "+xhr.status+" => "+thrownError);
       //alert(msg);
       }
     });
   /********** End of  Operation *********/
 });
 
 
   });