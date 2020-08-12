$(document).ready(function() {
//alert("Hello "+authorization);    
//alert("Hello ");
$('#addPeriodForm').submit(function (e) {
    e.preventDefault();
	//alert(authorization+" => "+$(this).serialize());
	var form = $('#registrationForm');
    var data = new FormData(this);
    document.getElementById("addPeriodForm").style.display = 'none';
	document.getElementById("registerOverlay").style.display = 'block';
	document.getElementById("errorMsgBlock").style.display = 'none';
	$("#errorMsg").text('');
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
         beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		//processData: false, 
        //contentType: false, 
		dataType: 'json',
        success: function(data) {
		//alert("Hello "+data);
		if(data.error){
		document.getElementById("registerOverlay").style.display = 'none';
		document.getElementById("addPeriodForm").style.display = 'block';
		document.getElementById("errorMsgBlock").style.display = 'block';
	    $("#errorMsg").text(data.message);
		}else{
document.getElementById("errorMsgBlock").style.display = 'block';
$("#errorMsg").text(data.message);			
setTimeout(function(){
location = base_url+"/manage-periods";
//location.reload();
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});




$('.deletePeriod').click(function (e) {
    e.preventDefault();
	var doc_id = $(this).attr("data-id");
	///alert(doc_id);
	
	 swal({
        title: 'Delete Period',
  text: "Are you sure that you want to delete this Period?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Yes, delete it!"
            },  function(){
  if (true) {
	var formData = {'item_id':doc_id};
	alert(doc_id);
    $.ajax({
        url: base_url+"/apis/periods/delete",
        type: "POST",
        data: formData,
        beforeSend: function(request) {
               request.setRequestHeader("Authorization", authorization);
             },
		dataType: 'json',
        success: function(data) {
		if(data.error){
		    swal("Failed To Delete", data.message, "error");
		}else{
swal("Deleted!", data.message, "success");
setTimeout(function(){ 
window.location.replace(base_url+'/manage-periods');
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
		alert("There was an error.");
      }
    });
	/********** End of  Operation *********/
  }
});  
});


});