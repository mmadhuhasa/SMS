$(document).ready(function() {
// alert("Hello "+setheader.authorization);
/*****************************************************
REGISTER SCHOOL
****************************************************/
$('#registerSchoolForm').submit(function (e) {
    e.preventDefault();
	
    // alert($(this).serialize());
	var base_url = $(this).attr('data-url');
    var form = $('#registrationForm');
    var data = new FormData(this);
    document.getElementById("registerSchoolForm").style.display = 'none';
	document.getElementById("registerOverlay").style.display = 'block';
	document.getElementById("errorMsgBlock").style.display = 'none';
	$("#errorMsg").text('');
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: data, // $(this).serialize(),
        beforeSend: function(request) {
               request.setRequestHeader("Authorization", setheader.authorization);
             },
		processData: false, 
        contentType: false, 
		dataType: 'json',
        success: function(data) {
		// alert("Hello "+data);
    		if(data.error){
        		document.getElementById("registerOverlay").style.display = 'none';     
        		document.getElementById("registerSchoolForm").style.display = 'block';
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


});