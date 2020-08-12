$(document).ready(function() {
	alert(("hello");
/*****************************************************
DELETE SECTION
****************************************************/
$('.button-remove').click(function (e) {
    e.preventDefault();
	var classID = $(this).attr("data-id");
	alert(classID);
Swal.fire({
  title: 'Delete Section ',
  text: 'Are you sure you want to delete this Section permanently?',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Confirm Delete',
  cancelButtonText: 'No, keep it'
}).then((result) => {
  if (result.value) {
	var formData = {'item_id':classID};
    $.ajax({
        url: base_url+"/apis/sections/delete",
        type: "POST",
        data: formData,
        beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		dataType: 'json',
        success: function(data) {
			alert(data.message);
		if(data.error){
		 Swal.fire(
        'Deleted!',
        data.message,
        'error');
		}else{
  Swal.fire(
        'Deleted!',
        data.message,
        'success');
setTimeout(function(){
window.location.replace(base_url+'/manage-sections');
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