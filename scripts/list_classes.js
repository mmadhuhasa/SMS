$(document).ready(function() {
	
  // alert(base_url);
   
  /*
   Swal.fire({
   position: 'top-end',
   icon: 'success',
   title: 'Your work has been saved',
   showConfirmButton: false,
   timer: 1500
 })
 
 
  Swal.fire({
   title: 'Are you sure?',
   text: 'You will not be able to recover this imaginary file!',
   icon: 'warning',
   showCancelButton: true,
   confirmButtonText: 'Yes, delete it!',
   cancelButtonText: 'No, keep it'
 }).then((result) => {
   if (result.value) {
     Swal.fire(
       'Deleted!',
       'Your imaginary file has been deleted.',
       'success'
     )
   // For more information about handling dismissals please visit
   // https://sweetalert2.github.io/#handling-dismissals
   } else if (result.dismiss === Swal.DismissReason.cancel) {
     Swal.fire(
       'Cancelled',
       'Your imaginary file is safe :)',
       'error'
     )
   }
 })
 */
 
 
 
 /*****************************************************
 DELETE CLASS
 ****************************************************/
$('.button-remove').click(function (e) {
  e.preventDefault();
var classId = $(this).attr("data-id");
Swal.fire({
title: 'Delete Class',
text: "Are you sure you want to delete this class?",
icon: 'warning',
showCancelButton: true,
confirmButtonText: '#3085d6',
cancelButtonColor: '#d33',
confirmButtonText: 'Confirm Delete'
}).then((result) => {
if (result.value) {
var formData = {'item_id':classId};
//alert( base_url+"apis/classes/delete");
  $.ajax({
      url: base_url+"apis/classes/delete",
      type: "POST",
        beforeSend: function(request) {
             request.setRequestHeader("Authorization", authorization);
           },
      data: formData,
  dataType: 'json',
      success: function(data) {
	alert(data.message);
  if(data.error){
  Swal.fire({
    title: 'Failed To Delete',
    text: data.message,
    icon: 'error',
    showCancelButton: false,
    confirmButtonText: '#3085d6',
                cancelButtonText: 'false'
});
  }else{
Swal.fire({
    title: ' Deleted',
    text: data.message,
    icon: 'success',
    showCancelButton: false,
    confirmButtonText: '#3085d6',
    cancelButtonText: 'false'
});
setTimeout(function(){ 
window.location.replace(base_url+'manage-classes');
}, 1600);
  }
      },
     error: function (xhr, ajaxOptions, thrownError) {
        //alert("Error code: "+xhr.responseText+" => "+thrownError);
        //alert(thrownError);
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
      //alert("Error => "+xhr.status+" => "+thrownError);
      alert(msg);
      }
  });
/********** End of  Operation *********/
}
});  
});
 
 });