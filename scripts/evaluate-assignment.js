// const base_url = "https://smartschoolautomation.com/";
$(document).ready(function() {
//alert("Hello "+authorization);
alert("Hello from Inside Page. "+authorization);
$('#evaluateAssignmentForm').submit(function (e) {
    e.preventDefault();
	var form = $('#submitAssignmentForm');
    var data = new FormData(this);
    //alert("authorization: "+authorization);
    document.getElementById("formOverlay").style.display = 'block';
	document.getElementById("evaluateAssignmentForm").style.display = 'none';
	document.getElementById("errorMsgBlock").style.display = 'none';
	$("#errorMsg").text('');
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
    document.getElementById("formOverlay").style.display = 'none';
	document.getElementById("evaluateAssignmentForm").style.display = 'block';
	document.getElementById("errorMsgBlock").style.display = 'block';
	$("#errorMsg").text(data.message);
			swal({
  title: '',
  html: '<b>'+data.message+'</b>',
  type: 'error',
  showCancelButton: false,
  allowOutsideClick: false,
  confirmButtonColor: '#3085d6',
  focusConfirm: false
});
}else{
document.getElementById("errorMsgBlock").style.display = 'block';
$("#errorMsg").text(data.message);		    
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