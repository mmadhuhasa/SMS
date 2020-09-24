$(document).ready(function() {

/*****************************************************
	CREATE COMMENT
	****************************************************/
	$('#createPostCommentForm').submit(function (e) {
		 e.preventDefault(); 
		 // alert("Hello "+$(this).serialize()); 
		 
		document.getElementById("createPostCommentForm").style.display = 'none';
		document.getElementById("form_overlay").style.display = 'block';

		var form = $('#comment_box');
        var data = $(this).serialize();		// new FormData(this); 
		
	    $.ajax({
	        url: $(form).attr('dd'),
	        type: "POST",
	        data: data,
	        // processData: false, 
        	// contentType: false,
      		//crossDomain: true,
		    //crossOrigin: true,
		    //async: true,
	        dataType: 'json', 
	        beforeSend: function(request) {
	             request.setRequestHeader("Authorization", setheader.authorization);
	        },
	        success: function(data) {
	      		// alert("I am returned from API => "+data.author_image);
	      		if(data.error){
	      			// alert(data.error);
	      			document.getElementById("createPostCommentForm").style.display = 'block';
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
		  			//alert(data.message);
				  	//$("#responseMsg").text("Taking you to Dashboard");
				   	Swal.fire({
					    title: '',
					    text: data.message,
					    icon: 'success',
					    showCancelButton: false,
					    confirmButtonText: 'okay',
						cancelButtonText: 'false'
					});
					// alert(base_url+'manage-posts');
					// timeout: 1500;
					//window.location.replace(base_url+'/manage-posts');
					// window.location.reload();
					if (data.status === 'commented') {
						document.getElementById("createPostCommentForm").style.display = 'block';
	        			document.getElementById("form_overlay").style.display = 'none';

	        			var html = '<li class="row clearfix"><div class="icon-box col-md-2 col-4"><img class="d-block img-fluid" src="'+data.author_image+'" alt="First slide"></div><div class="text-box col-md-10 col-8 p-l-0 p-r0"><h5 class="m-b-0">'+data.author_name+'</h5><p>'+data.comment+'</p><ul class="list-inline"><li><a href="javascript:void(0);">'+data.date_created+'</a></li><li><a href="javascript:void(0);">Reply</a></li></ul></div></li>';
                        $("#comment-lists").prepend(html);
                        $("#comment_box").val('');
                        $("#count_comments").html('<h2>Comments ('+data.numComments+')</h2>');
                        $("#list_count_comments").html('<i class="fa fa-comments fa-2x"></i><h4>'+data.numComments+'</h4><span>Comments</span>');

	    			}
		  				
		    	}
			},
		    error: function (xhr, ajaxOptions, thrownError) {
		        alert("Error code: "+xhr.status+" => "+thrownError);
		        //alert(thrownError);
		    }
		});
	});



	/*****************************************************
	CREATE Like
	****************************************************/

  	$("button").click(function(e) {
	// $('#post_like').click(function (e) {
		 e.preventDefault(); 
		 

		var button_id = '#'+this.id;
		var button_name = $(button_id).attr('dt');

		if (button_name === 'dislike') {
			console.log('disliked');
			document.getElementById("post_dislike").style.display = 'none';
			document.getElementById("process").style.display = 'block';
		}
		else if (button_name === 'like') {
			console.log('liked');
			document.getElementById("post_like").style.display = 'none';
			document.getElementById("process").style.display = 'block';
		}
		else{
			//
		}

		var post_page = $('#post_page').val();
		var user_id = $('#user_id').val();
		var post_id = $('#post_id').val();
        // var data = 'user_id='+user_id+'&post_id='+post_id+'&post_page='+post_page+'&button_name='+button_name;		// new FormData(this); 		// for GET METHOD
        var data = {'user_id':user_id, 'post_id':post_id, 'post_page':post_page, 'button_name':button_name};	// for POST METHOD
		// alert(data.user_id);
	    $.ajax({
	        url: $(button_id).attr('dd'),
	        type: "POST",
	        data: data,
	        // processData: false, 
        	// contentType: false,
      		//crossDomain: true,
		    //crossOrigin: true,
		    //async: true,
	        dataType: 'json', 
	        beforeSend: function(request) {
	             request.setRequestHeader("Authorization", setheader.authorization);
	        },
	        success: function(data) {
	      		// alert("I am returned from API => "+data);
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
		  			//alert(data.message);
					// timeout: 8500;
					// window.location.reload();
					if (data.status === 'liked') {
						document.getElementById("post_dislike").style.display = 'block';
						document.getElementById("post_like").style.display = 'none';	
						document.getElementById("process").style.display = 'none';
					}
					else if(data.status === 'disliked'){
		  				document.getElementById("post_dislike").style.display = 'none';
						document.getElementById("post_like").style.display = 'block';	
						document.getElementById("process").style.display = 'none';
		    		}
		    		else{
		    			//
		    		}
                    
                    $("#list_count_likes").html('<i class="fa fa-thumbs-o-up fa-2x"></i><h4>'+data.numLikes+'</h4><span>Likes</span>');

				}
			},
		    error: function (xhr, ajaxOptions, thrownError) {
		        alert("Error code: "+xhr.status+" => "+thrownError);
		        //alert(thrownError);
		    }
		});
	});




});