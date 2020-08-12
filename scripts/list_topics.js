$(document).ready(function() {
/*****************************************************
DELETE TOPIC
****************************************************/
 $('.button-remove').click(function (e) {
  e.preventDefault();
var classID = $(this).attr("data-id");
//alert(base_url);
Swal.fire({
  title: 'Delete Topic ',
  text: 'Are you sure you want to delete this Topic permanently?',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Confirm Delete',
  cancelButtonText: 'No, keep it'
}).then((result) => {
  if (result.value) {
    alert(base_url);
    /********** Start of  Operation *********/
    var formData = {'item_id':classID};
   //var base_url = "https://smartschoolautomation.com";
    $.ajax({
        url: base_url+"apis/topics/delete",
        type: "POST",
        data: formData,
        beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
         //dataType: 'json',
        success: function(data) {
      alert("returned "+data.message);
    if(data.error){
      Swal.fire(
        'Deleted!',
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
      //alert("Error => "+xhr.status+" => "+thrownError);
      alert(msg);
      }
    });
  /********** End of  Operation *********/

  } else if (result.dismiss === Swal.DismissReason.cancel) {
    Swal.fire(
      'Cancelled',
      'Your file will not be deleted :)',
      'error'
    )
  }
});
});


  });