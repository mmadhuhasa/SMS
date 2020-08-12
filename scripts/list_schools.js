//const base_url = "https://smartschoolautomation.com/";
$(document).ready(function() {
/*****************************************************
DELETE SCHOOL
****************************************************/
$('.button-remove').click(function (e) {
    e.preventDefault();
	var schoolID = $(this).attr("data-id");
	
  Swal.fire({
    title: 'Delete School ',
    text: 'Are you sure you want to delete this class permanently?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Confirm Delete',
    cancelButtonText: 'No, keep it'
}).then((result) => {
  if (result.value) {
	var formData = {'item':schoolID};
    $.ajax({
        url: base_url+"/apis/schools/delete",
        type: "POST",
        data: formData,
        beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		dataType: 'json',
        success: function(data) {
			alert(data.message);
		if(data.error){
// 		swal({
//   title: 'Failed To Delete',
//   html: '<b>'+data.message+'</b>',
//   type: 'error',
//   showCancelButton: false,
//   confirmButtonColor: '#3085d6',
//   focusConfirm: false
// });
		}else{
// swal({
//   title: 'Account Deleted',
//   html: '<b>'+data.message+'</b>',
//   type: 'success',
//   showCancelButton: false,
//   confirmButtonColor: '#3085d6',
//   focusConfirm: false
// });
// setTimeout(function(){
// window.location.replace(base_url+'/manage-schools');
// }, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        //alert("Error=> "+xhr.status+"  "+xhr.responseText);
        alert(xhr.responseText);
        //alert(thrownError);
      }
    });
	/********** End of  Operation *********/
  }
});
});

});