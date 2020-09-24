$(document).ready(function() {
	//alert("Hello ");
  
	$('#formatted_body').summernote({
	  	placeholder: 'Enter the description here.. ',
	  	tabsize: 2,
	    height: 120,
	});

  // remove selected tag
  $(".previous-tags").click(function(){
      $(this).remove();
  });
	   
	/*****************************************************
	CREATE POST
	****************************************************/
	$('#createPostForm').submit(function (e) {
		e.preventDefault();
		document.getElementById("createPostForm").style.display = 'none';
		document.getElementById("form_overlay").style.display = 'block';

    // get previous body content or new if present
	  var textarea = $('#formatted_body').summernote('code');
    if (textarea != '<p><br></p>'){
        document.getElementById("post_body").value = textarea;
        //document.getElementById("post_body").html = textarea;
    }

    if ($("#post_page").val() === 'edit-post'){ 
         var p_tags = []; 
         var n_tags = []; 
         var tagAll = [];
         var getTag = '';
        if($('.tag').length > 0){

            // get previous tags into array  
            $('.previous-tags').each(function(){ 
                if ($(this).text() != null) {
                  p_tags.push($(this).text());   
                  getTag += $(this).text()+',';          // store previous tag as string   
                }   
            });
            console.log(p_tags);
            // get new tags into array
            $('.bootstrap-tagsinput:nth-child(2) > .tag').each(function(){
                if ($(this).text() != null) {
                  n_tags.push($(this).text());      
                }    
            });
            console.log(n_tags);
            // compare new tags with previous tags
            for(let i=0; i<n_tags.length; i++){
              if (p_tags.indexOf(n_tags[i]) == -1) {
                  tagAll.push(n_tags[i].toString());    // store as array 
                  getTag += n_tags[i]+',';              // store new tag as string
              }   
            }  
            // combine previous and new tags
            // tagAll = p_tags.concat(tagAll);
            // debugger;
        }
        var finalTag = getTag.slice(0, -1);   // remove last character
        $("#post_tags").val(finalTag);        // set tags to element

    }  


    // get previous and/or new Files
    // if( document.getElementById("dropify-event").files.length === 0 ){
    //     console.log("no files selected");
    // }

		var form = $('#createPostForm');
		var data = new FormData(this);
    var base_url = $(this).attr('data-url');

	    $.ajax({
	        url: $(this).attr('action'),
	        type: "POST",
	        data: data,
	        processData: false, 
        	contentType: false,
      		//crossDomain: true,
		    //crossOrigin: true,
		    //async: true,
	        dataType: 'json',  
	        beforeSend: function(request) {
	             request.setRequestHeader("Authorization", setheader.authorization);
	        },
	        success: function(data) {
	      		// alert("I am returned from API => "+data.error);
            // debugger;
	      		if(data.error){
	      			// alert(data.error);
	      			document.getElementById("createPostForm").style.display = 'block';
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
					//timeout: 3000;
					//window.location.replace(base_url+'/manage-posts');
		  			setTimeout(function(){
		  				window.location.replace(base_url+'/manage-posts');
		  			}, 1800);
		  				
		    	}
			},
		    error: function (xhr, ajaxOptions, thrownError) {
		        alert("Error code: "+xhr.status+" => "+thrownError);
		        //alert(thrownError);
		    }
		});
	});



	/*****************************************************
	GET SUBJECTS
	****************************************************/

  $('#post_class').on('change',function(){
        var selected_class = $(this).val();
          var form_action = $('#createPostForm').attr('action');
          var subject_url = $(this).attr("dd") + selected_class;
          // console.log(subject_url);
        if(selected_class){
            $.ajax({
                type:'GET',
                url: subject_url,
                data:'class_id='+selected_class,
                success:function(data){
                  console.log(data);

                  var obj = JSON.parse(data);
                  var sel = document.getElementById('post_subject');
                  $(sel).empty();
                  $(sel).append($('<option>').text("select a subject").attr('value',''));  //.attr('disabled', 'disabled'));
                  for(var i = 0; i < obj.length; i++) {
                    var opt = document.createElement('option');
                    opt.innerHTML = obj[i].title;
                    opt.value = obj[i].id;
                    sel.appendChild(opt);
                  }  
                  // var options = '';
                  // for (var i = 0; i < data.length; i++) {
                  //   options += '<option value="' + data[i].id + '">' + data[i].title + '</option>';
                  // }
                  // $("select#post_create_subject").html(options);

                  // $.each(data, function(i, value) {
                  //     console.log(value);
                  //     $('#post_create_subject').empty();
                  //     $('#post_create_subject').append($('<option>').text("Select"));
                  //     $('#post_create_subject').append($('<option>').text('k').attr('value', 'ko'));
                  // });

                    // $('#post_create_subject').html(data); 
                    // $('#post_create_subject').html('<option value="">'data'</option>');
                     // <option value="{{row.id}}">{{row.title}}</option> 
                },
                error: function (data) {
                    console.log(data);
                    $('#post_subject').html('<option value="">no subject</option>');
                }
            }); 
        }else{
            $('#post_subject').html('<option value="">Select subject first</option>');
            // $('#city').html('<option value="">Select state first</option>'); 
        }
    });



	/*****************************************************
	GET TOPICS
	****************************************************/

  $('#post_subject').on('change',function(){
        var selected_class = $(this).val();
          var form_action = $('#createPostForm').attr('action');
          var topic_url = $(this).attr("dd") + selected_class;
          // console.log(selected_class);
        if(selected_class){
            $.ajax({
                type:'GET',
                url: topic_url,
                data:'subject_id='+selected_class,
                success:function(data){
                  console.log(data);

                  var obj = JSON.parse(data);
                  var sel = document.getElementById('post_topic');
                  $(sel).empty();
                  $(sel).append($('<option>').text("select a topic").attr('value',''));  //.attr('disabled', 'disabled'));
                  for(var i = 0; i < obj.length; i++) {
                    var opt = document.createElement('option');
                    opt.innerHTML = obj[i].title;
                    opt.value = obj[i].id;
                    sel.appendChild(opt);
                  }  
                    // $('#post_create_topic').html(data);
                    // $('#city').html('<option value="">Select state first</option>'); 
                },
                error: function (data) {
                    console.log(data);
                    $('#post_topic').html('<option value="">no topic</option>');
                }
            }); 
        }else{
            $('#post_topic').html('<option value="">Select topic first</option>');
            // $('#city').html('<option value="">Select state first</option>'); 
        }
    });





});