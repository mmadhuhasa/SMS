
$(document).ready(function() {
	 
$('#loginForm').submit(function (e) {
    e.preventDefault();
	document.getElementById("loginForm").style.display = 'none';
	document.getElementById("login_overlay").style.display = 'block';
    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
		dataType: 'json',
        success: function(data) {
		if(data.error){
		document.getElementById("loginForm").style.display = 'block';
	    document.getElementById("login_overlay").style.display = 'none';	
		 Swal.fire({
  title: '',
  text: data.message,
  icon: 'warning',
  showCancelButton: false
});
}else{
//document.getElementById("loginForm").style.display = 'none';    
 Swal.fire({
  title: 'Login Successful',
  text: data.message,
  icon: 'success',
  showCancelButton: false
});
$("#responseMsg").text("Taking you to Dashboard");
setTimeout(function(){
window.location.replace(base_url+'/authenticating');
}, 2200);
		}
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        //alert(thrownError);
      }
    });
});



/*
 $('#fuelConsumptionOn').change(function() {
        if(this.checked) {
           $('#consumptionCalc').css("display", "block");   
           $('#distanceCalc').css("display", "none");   
        }else{
            $('#distanceCalc').css("display", "block");
            $('#consumptionCalc').css("display", "none");   
        }
    });*/

$(document).on('click', '.distanceQuickr', function () {
    $("#calcDistance").val($(this).text());
});

$(document).on('click', '#switchToConsumption', function () {
     $('#consumptionCalc').css("display", "block");   
    $('#distanceCalc').css("display", "none"); 
});

$(document).on('click', '#switchToDistance', function () {
    $('#distanceCalc').css("display", "block");
    $('#consumptionCalc').css("display", "none");
});

});