$('input[type="file"]').each(function(){

   var $file = $(this),
      $label = $file.next('label'),
      $labelText = $label.find('span'),
      labelDefault = $labelText.text();

   $file.on('change',function(event){

	    var fileName = $file.val().split('\\' ).pop(),
	        tmppath = URL.createObjectURL(event.target.files[0]);
	    if( fileName ){

	      $label.addClass('file-ok').css('background-image','url(' + tmppath +')');
	      $labelText.text(fileName);

	    }else{

	      $label.removeClass('file-ok');
	      $labelText.text(labelDefault);

	    }

  });

});


  // remove selected image
  $(".remover").click(function(){
      var image_id = $(this).attr("data-id");
      if ($(this).parent().hasClass("file-ok")) {
      		$(this).siblings("span").text("");
      		$(this).parent().removeAttr("style");
      		$(this).parent().removeAttr("class");

      }
  });

  // delete uploaded image
$('.deleteServiceItem').click(function (e) {
      e.preventDefault();
	  var blogID = $(this).attr("data-id");
	  Swal.fire({
		  title: 'Delete Photo',
		  text: "Are you sure you want to delete this Image from Post Listing?",
		  // type: 'warning',
	  	  icon: 'warning',
		  showCancelButton: true,
		  // confirmButtonColor: '#3085d6',
		  // cancelButtonColor: '#d33',
		  confirmButtonText: 'Confirm Delete',
	  	  cancelButtonText: 'No, keep it'
	  }).then((result) => {
	  	  if (result.value) {
		      var formData = {'item_id':blogID};
		      var url = $(this).attr("data-url")+"/post_images/delete";
    		  var base_url = $(this).attr('data-url');

	      	  $.ajax({
		          url: url,
		          type: "POST",
		          data: formData,
				  dataType: 'json',
				  beforeSend: function(request) {
	             	  request.setRequestHeader("Authorization", setheader.authorization);
	        	  },
		          success: function(data) {
						  // alert(data.postImages);
					  if(data.error){
						  Swal.fire({
						      // html: '<b>'+data.message+'</b>',
						      // confirmButtonColor: '#3085d6',
						      // focusConfirm: false
						      title: 'Failed To Delete Image',
					          text: data.message,
					          icon: 'error',
					          showCancelButton: false,
					          confirmButtonText: 'okay',
					  		  cancelButtonText: 'false'
						  });
					  }
					  else{
						  Swal.fire({
						      // html: '<b>'+data.message+'</b>',
						  	  // type: 'success',
						  	  // confirmButtonColor: '#3085d6',
						  	  // focusConfirm: false
						  	  title: 'Image Deleted',
					          text: data.message,
					          icon: 'success',
					          showCancelButton: false,
					          confirmButtonText: 'okay',
					  		  cancelButtonText: 'false'
						  });
						  // setTimeout(function(){
						  // 	  window.location.reload();
						  // }, 2200);


						  var len = data.postImages.length;
						  if (len > 0) {
						  		var images = '<p><label for="attachments">Attached Photos</label></p>';
						  		for (var i = len - 1; i >= 0; i--) {
						  			images += '<div class="wrap-custom-file"><label style="background-image: url('+base_url+'/uploads/'+data.postImages[i].image+');background-size: cover;background-position: center;"><div class="deleteServiceItem remover" data-url="'+base_url+'" data-id="'+data.postImages.id+'" style="display:block;"><i class="fa fa-trash" style="color:#fff;"></i></div></label></div>';
						  			// $(".postImages").html(h); 			
						  		}	

								var rem = 5 - len;
								for (var i = rem - 1; i >= 0; i--) {
									images += '<div class="wrap-custom-file"><input type="file" name="uploads[]" id="image'+i+'" accept=".gif, .jpg, .png" /><label for="image'+i+'"><span></span><div class="remover" data-id="image'+i+'"><i class="fa fa-trash" style="color:#fff;"></i></div></label></div>';
								}	

								$(".postImages").html(images); 	 
						  }

					  }
        	  	  },
	          	  error: function (xhr, ajaxOptions, thrownError) {
		        		alert("Error code: "+xhr.status+" => "+thrownError);	// alert(xhr.status);	//alert(thrownError);
      		      }
    		  });
	/********** End of  Operation *********/
  		  }
	  });
});