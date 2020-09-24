// var doc_types = [];
$(document).ready(function(){
       
    // $(':button').click(function () {
    //     if (this.id == 'text_search_button') {
    //         alert($('#text_search').val());
    //     }
    //     else if (this.id == 'filter_search_button') {
    //         var post_class = $('#filter_search_class').val();
    //         var post_subject = $('#filter_search_subject').val();
    //         var post_extra = $('#filter_search_extra').val();
    //         alert(post_class+','+post_subject+','+post_extra);
    //     }
    // });

    $('#text_search_button,#filter_search_button').click(function () { 
        var button_id = this.id;

        var search_box = $('#text_search').val();
        var post_class = $('#filter_search_class').val();
        var post_subject = $('#filter_search_subject').val();
        var post_extra = $('#filter_search_extra').val();
        var base_url = $("#text_search").attr('data-baseurl');

        $.ajax({
            url: $("#text_search").attr('data-url'),
            type: "GET",
            data: {'search_box':search_box, 'post_class':post_class, 'post_subject':post_subject, 'post_extra':post_extra },
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
                // console.log(data);
                // debugger;
                if(data.error === true){
                    // alert(data.error);
                    document.getElementById("createPostCommentForm").style.display = 'block';
                    document.getElementById("form_overlay").style.display = 'none'; 
                    Swal.fire({
                        title: '',
                        text: 'no Data Found',
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonText: 'okay',
                        cancelButtonText: 'false'
                    });  
                }else{ 
                  
                    // Swal.fire({
                    //     title: '',
                    //     text: 'Data Found',
                    //     icon: 'success',
                    //     showCancelButton: false,
                    //     confirmButtonText: 'okay',
                    //     cancelButtonText: 'false'
                    // });
                    
                    // if (data.status === 'commented') {
                        // document.getElementById("createPostCommentForm").style.display = 'block';
                        // document.getElementById("form_overlay").style.display = 'none';

                    var post_len = data.post.length;

                    if (post_len > 0){

                        var list_posts = '';    
                        for (var i = 0; i < post_len; i++) {

                            var html = '<div class="col-lg-4 col-md-4 col-sm-12"><div class="card single_post">';
                            html += '<div class="body">';
 
                            var img_len = data.post[i].images.length;
                            if (img_len > 0) {
                                html += '<img class="d-block img-fluid" src="'+base_url+'/uploads/'+data.post[i].images[0].image+'" alt="'+data.post[i].title+'">';     // display only first image
                            }
                            else{ 
                                html += '<img class="d-block img-fluid" src="'+base_url+'/assets/images/posts/econtent.jpg" alt="'+data.post[i].title+'">';
                            }

                            html += '<h3><a href="'+base_url+'/post-detail/'+data.post[i].qcode+'">'+data.post[i].title+'</a></h3><span class="badge badge-info">'+data.post[i].class_name+'</span><span class="badge badge-info">'+data.post[i].subject_name+'</span>';
                            html += '</div>';
                            html += '<div class="footer">';
                            html += '<div class="actions"><a href="'+base_url+'/post-detail/'+data.post[i].qcode+'" class="btn btn-outline-secondary">Read now</a></div>'; 
                            html += '<ul class="stats"><li><a href="javascript:void(0);" class="icon-heart">'+data.post[i].numLikes+'</a></li><li><a href="javascript:void(0);" class="icon-bubbles">'+data.post[i].numComments+'</a></li></ul>';
                            html += '</div>';
                            html += '</div></div>';

                            list_posts += html; 
                        }   

                        $(".allPosts").html(list_posts);  


                        var total_searched = '<p class="m-b-0">Searched Result For "<b>'+search_box+'</b>"</p>';
                        if (post_len > 1) {
                            total_searched += '<strong> About '+post_len+' posts found</strong>';
                        }
                        else{
                            total_searched += '<strong> About '+post_len+' post found</strong>';
                        }

                        $(".searched_result").html(total_searched);  

                    }
                    else{
                        $(".allPosts").html(''); 

                        var total_searched = '<p class="m-b-0">Search Result For "'+search_box+'"</p><strong> About '+post_len+' posts found</strong>';
                        $(".searched_result").html(total_searched);       
                    }

                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("Error code: "+xhr.status+" => "+thrownError);
                //alert(thrownError);
            }
        });


    });

 // $('.async_document_type').change(function() {
 //        if(this.checked) {
 //            //alert(this.value);
 //            //this.doc_types = selectDocs();
 //            //var returnVal = confirm("Are you sure?");
 //            //$(this).prop("checked", returnVal);
            
 //             $.each($("input[class='async_document_type']:checked"), function(){
 //                doc_types.push($(this).val());
 //            });
 //            //alert(doc_types);
 //        }else{
 //            doc_types = selectDocs();
 //        }
 //        //alert("Selected Doc Types are: " + doc_types.join(", "));
 //        //$('#textbox1').val(this.checked);        
 //    });
   
 
 // function selectDocs(){
 //     var favorite = [];
 //            $.each($("input[class='async_document_type']:checked"), function(){
 //                favorite.push($(this).val());
 //            });
 //            return favorite;
 // } 


});