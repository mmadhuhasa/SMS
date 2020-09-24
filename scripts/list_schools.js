//const base_url = "https://smartschoolautomation.com/";
$(document).ready(function() {
/*****************************************************
DELETE SCHOOL
****************************************************/
$('.button-remove').click(function (e) {
    e.preventDefault();
	var schoolID = $(this).attr("data-id");
	var base_url = $(this).attr('data-url');
  Swal.fire({
    title: 'Delete School ',
    text: 'Are you sure that you want to delete this School profile permanently?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Confirm Delete',
    cancelButtonText: 'No, keep it'
}).then((result) => {
  if (result.value) {
	//alert(setheader.authorization);
	var formData = {'item':schoolID};
    $.ajax({
        url: base_url+"/apis/schools/delete",
        type: "POST",
        data: formData,
        beforeSend: function(request) {
               request.setRequestHeader("Authorization", setheader.authorization);
             },
		dataType: 'json',
        success: function(data) {
		//alert(data.message);
		if(data.error){
Swal.fire({
				        title: '',
				        text: data.message,
				        icon: 'error',
				        showCancelButton: false,
				        confirmButtonText: 'okay',
				  		cancelButtonText: 'false'
					});  
		}else{
Swal.fire({
				        title: '',
				        text: data.message,
				        icon: 'success',
				        showCancelButton: false,
				        confirmButtonText: 'okay',
				  		cancelButtonText: 'false'
					});  
 setTimeout(function(){
 window.location.replace(base_url+'/manage-schools');
 }, 2200);
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