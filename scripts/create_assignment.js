$(document).ready(function() { 
 // alert(authorization);
 
  $('#createAssignmentForm').submit(function (e) {
    e.preventDefault();
    alert($(this).serialize());
    document.getElementById("createAssignmentForm").style.display = 'none';
    document.getElementById("form_overlay").style.display = 'block';
      $.ajax({
          url: $(this).attr('action'),
          type: "POST",
          data: $(this).serialize(),
          dataType: 'json',
          beforeSend: function(request) {
                 request.setRequestHeader("Authorization", authorization);
               },
          success: function(data) {
      //alert("I am returned from API "+data.message);
      if(data.error){
      document.getElementById("createAssignmentForm").style.display = 'block';
        document.getElementById("form_overlay").style.display = 'none';	
         Swal.fire({
        title: '',
        text: data.message,
        icon: 'error',
        showCancelButton: false,
        confirmButtonText: 'okay',
  cancelButtonText: 'false'
});  
  }else{ 
  //$("#responseMsg").text("Taking you to Dashboard");
   Swal.fire({
    title: '',
    text: data.message,
    icon: 'success',
    showCancelButton: false,
    confirmButtonText: 'okay',
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
  });
  
  $('#updateAssignmentForm').submit(function (e) {
    e.preventDefault();
    //alert($(this).serialize());
    document.getElementById("updateAssignmentForm").style.display = 'none';
    //document.getElementById("formTitle").style.display = 'none';
    document.getElementById("form_overlay").style.display = 'block';
      $.ajax({
          url: $(this).attr('action'),
          type: "POST",
          data: $(this).serialize(),
         dataType: 'json',
         beforeSend: function(request) {
                 request.setRequestHeader("Authorization", authorization);
               },
          success: function(data) {
      if(data.error){
      //	document.getElementById("formTitle").style.display = 'block';  
      document.getElementById("updateAssignmentForm").style.display = 'block';
      document.getElementById("form_overlay").style.display = 'none';	
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
  });
  
  });