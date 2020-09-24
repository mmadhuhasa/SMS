{% extends 'applayout.twig' %}
{% block header_scripts %}
<link rel="stylesheet" href="{{ base_url() }}/vendor/bootstrap-multiselect/bootstrap-multiselect.css">
<link rel="stylesheet" href="{{ base_url() }}/vendor/multi-select/css/multi-select.css">
<link rel="stylesheet" href="{{ base_url() }}/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css">
<link rel="stylesheet" href="{{ base_url() }}/vendor/select2/select2.css" />

<link rel="stylesheet" href="{{ base_url() }}/vendor/dropify/css/dropify.min.css">
<link rel="stylesheet" href="{{ base_url() }}/vendor/sweetalert2/dist/sweetalert2.css"/>
<link rel="stylesheet" href="{{ base_url() }}/vendor/summernote/dist/summernote.css"/>

<link rel="stylesheet" href="{{ base_url() }}/vendor/parsleyjs/css/parsley.css">

<link rel="stylesheet" href="{{ base_url() }}/assets/css/multi_upload.css">

{% endblock %}
{% block header %}
{% include 'partials/top-header.twig' %}
{% endblock %}

{% block appnav %}
{% include 'partials/appnav.twig' %}
{% endblock %}

{% block content %}
       <div id="main-content">
        <div class="container">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-sm-12">                        
                        <h2>Update the E-Content</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ base_url() }}/admin-dashboard"><i class="icon-home"></i></a></li>                            
                             <li class="breadcrumb-item"><a href="{{ base_url() }}/manage-assignments"> Manage E-Contents</a></li>
                            <li class="breadcrumb-item active">Existing E-Content</li>
                        </ul>
                    </div>            
                    <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                        
                    </div>
                </div>
            </div>
 <form name="createPostForm" id="createPostForm" method="post" novalidate action="{{ base_url() }}/apis/posts/create" enctype="multipart/form-data" data-url="{{ base_url() }}" style="display:block;">
    {% if page.post %}
        <div class="card clearfix">
			     <div class="body">
              <div class="form-group">
                   <label>Post Title</label>
                  <input name="post_title" type="text" class="form-control" placeholder="Enter title" value="{{page.post.title}}"/>
              </div>
                   
               <div class="row">
                    <div class="col-lg-4 col-md-12 col-sm-12">                        
                      <div class="form-group">
                              <label>Select Class</label>
                               <select class="form-control show-tick ms select2" name="post_class" id="post_class" data-placeholder="Select" dd="{{ base_url() }}/apis/posts/subjects/">
                                  <option value="" >Select a class</option>
                                  {% if page.classes %}
                                    {% for row in page.classes %}
                                      {% if page.post.class_id == row.id %} 
                                        <option value="{{row.id}}" selected="selected">{{row.name}}</option> 
                                      {% else %}
                                        <option value="{{row.id}}">{{row.name}}</option> 
                                      {% endif %}  
                                    {% endfor %}
                                  {% endif %}
                              </select> 
                          </div>
                    </div>          
                    <div class="col-lg-4 col-md-12 col-sm-12">
                         <div class="form-group">
                                    <label>Select Subject</label>
                                    <select class="form-control show-tick ms select2" name="post_subject" id="post_subject" data-placeholder="Select" dd="{{ base_url() }}/apis/posts/topics/">                                          
                                        <option value="" >Select a subject</option>
                                          {% if page.subjects %}
                                            {% for row in page.subjects %}
                                              {% if page.post.subject_id == row.id %} 
                                                <option value="{{row.id}}" selected="selected">{{row.title}}</option> 
                                              {% else %}
                                                <option value="{{row.id}}">{{row.title}}</option> 
                                              {% endif %}  
                                            {% endfor %}
                                          {% endif %}
                                    </select>
                                </div>
                    </div>
					 <div class="col-lg-4 col-md-12 col-sm-12">
                         <div class="form-group">
                                    <label>Select Topic</label>
                                    <select class="form-control show-tick ms select2" name="post_topic" id="post_topic" data-placeholder="Select">    
                                        <option value="" >Select a topic</option>
                                          {% if page.topics %}
                                            {% for row in page.topics %}
                                              {% if page.post.topic_id == row.id %} 
                                                <option value="{{row.id}}" selected="selected">{{row.title}}</option> 
                                              {% else %}
                                                <option value="{{row.id}}">{{row.title}}</option> 
                                              {% endif %}  
                                            {% endfor %}
                                          {% endif %}
                                    </select>
                                </div>
                    </div>
                </div>
				
			<div class="row clearfix">
			 <div class="col-lg-12 col-md-12 col-sm-12">
			 <div class="form-group">
                    <label>Post Type {{page.post.post_type}}</label>
                      <select class="form-control show-tick" name="post_type" id="post_type">
                          <option value="">select a category</option> 
                             {% if page.postTypes %}
                                  {% for row in page.postTypes %}
                                      {% if page.post.post_type == row.id %} 
                                        <option value="{{row.id}}" selected="selected">{{row.name}}</option> 
                                      {% else %}
                                        <option value="{{row.id}}">{{row.name}}</option> 
                                      {% endif %}  
                                  {% endfor %}
                              {% endif %}
                            </select>
							</div>
							



      <div class="form-group">
          <label>Add Tags</label>
          <small id="fileHelp" class="form-text text-muted">Hit enter to add new tags.</small>
          <div class="input-group demo-tagsinput-area" style="border: 1px solid #ced4da; border-radius: .25rem;">
              
              <div class="bootstrap-tagsinput">   
                {% for row in page.post.tags %}
                  <span class="tag badge badge-info previous-tags">{{row.tag}}<span data-role="remove"></span></span> 
                {% endfor %}  
                <!-- <input type="text" placeholder="" size="1"> -->
              </div>
              <input name="post_tags" id="post_tags" type="text" class="form-control" data-role="tagsinput" value="" style="display: none;">

          </div>

      </div>
                    
      <div class="form-group">
                  <label>Short Description</label>
                  <textarea name="post_description" id="post_description" class="form-control" rows="5" cols="30" required="">{{page.post.description}}</textarea>
              </div>

                
                          <!--   <div class="form-group m-t-20 m-b-20">
              <label>Content Banner</label>
                                <input type="file" class="form-control-file" id="exampleInputFile" name="post_content_file" aria-describedby="fileHelp">
                            </div> -->
              
              <label>Compose Content Body</label>
               <small id="fileHelp" class="form-text text-muted">Enter formatted content description here.</small>
                            <div class="summernote" id="formatted_body"></div>
              
            
              
              <div class="form-group" style="display: none;">
                                    <label>Post Content</label>
                  <textarea class="form-control" name="post_body" cols="40" rows="4" id="post_body" spellcheck="true" placeholder="Write post body" ></textarea>
                                </div>
              
              </div>
       </div>         
       </div>
     </div>			


 

         <!--      <div class="card clearfix">
                        <div class="header">
                            <h2>UPLOAD IMAGE(S) <small>Remove the image</small></h2>
                        </div>
                        <div class="body">
                        
                            <input type="file" id="dropify-event" name='post_image[]' multiple >
                            <input type="hidden" name="post_image_editpage[]" value="{% for row in page.post.images %} {{row.image}} {% endfor %}">
                        </div>
                    </div> -->
  





<!-- ############ Starts Attachments Row ############ --> 
{% if page.post.images|length > 0 %}
<div class="row clearfix">
<div class="col-md-12 postImages">
<p><label for="attachments">Attached Photos</label> </p>      
  {% for port_item in page.post.images %}
  
  <div class="wrap-custom-file">
  <label style="background-image: url('{% if port_item.image %}{{base_url()}}/uploads/{{port_item.image}}{% else %}{{base_url()}}/assets/images/posts/econtent.jpg{% endif %}');background-size: cover;
    background-position: center;">
  <div class="deleteServiceItem remover" data-url="{{base_url()}}" data-id="{{port_item.id}}" style="display:block;"><i class="fa fa-trash" style="color:#fff;"></i></div>
  </label>
  </div>                      
  {% endfor %}
  
  {% set remaining = (5 - page.post.images|length) %}
  {% for i in 1..remaining %}
<div class="wrap-custom-file">
  <input type="file" name="uploads[]" id="image{{i}}" accept=".gif, .jpg, .png" />
  <label for="image{{i}}">
    <span></span>
  <div class="remover" data-id="image{{i}}"><i class="fa fa-trash" style="color:#fff;"></i></div>
  </label>
</div>
{% endfor %}


  </div>
</div>  
{% else %}              
<!-- Ends Row --> 

<div class="row">
<div class="col-md-12">
<p><label for="description">Upload New Project Photos</label></p>

<div class="wrap-custom-file">
  <input type="file" name="uploads[]" id="image1" accept=".gif, .jpg, .png" />
  <label for="image1">
    <span></span>
  <div class="remover" data-id="image1"><i class="fa fa-trash" style="color:#fff;"></i></div>
  </label>
</div>

<div class="wrap-custom-file">
  <input type="file" name="uploads[]" id="image2" accept=".gif, .jpg, .png" />
  <label  for="image2">
    <span></span>
  <div class="remover" data-id="image1"><i class="fa fa-trash" style="color:#fff;"></i></div>
  </label>
</div>

<div class="wrap-custom-file">
  <input type="file" name="uploads[]" id="image3" accept=".gif, .jpg, .png" />
  <label  for="image3">
    <span></span>
  <div class="remover" data-id="image1"><i class="fa fa-trash" style="color:#fff;"></i></div>
  </label>
</div>

<div class="wrap-custom-file">
  <input type="file" name="uploads[]" id="image4" accept=".gif, .jpg, .png" />
  <label  for="image4">
    <span></span>
  <div class="remover" data-id="image1"><i class="fa fa-trash" style="color:#fff;"></i></div>
  </label>
</div>

<div class="wrap-custom-file">
  <input type="file" name="uploads[]" id="image5" accept=".gif, .jpg, .png" />
  <label for="image5">
    <span></span>
  <div class="remover" data-id="image1"><i class="fa fa-trash" style="color:#fff;"></i></div>
  </label>
</div>

</div></div>            
<!-- Ends Row -->
{% endif %}   









                <div class="card clearfix">
                        <div class="header">
                            <h2>VIDEO LINK <small>Enter the link of the video below.</small></h2>
                        </div>
                        <div class="body">
                             <input type="text" class="form-control" placeholder="Enter Video URL e.g. https://www.youtube.com/897656711" name='link' id='link' value="{{page.post.link}}">
                        </div>
                    </div>  
 <div class="card clearfix">
                        <div class="header">
                            <h2>POST PREFERENCES <small>Set up post publishing preferences .</small></h2>
                        </div>
                        <div class="body">
                          <div class="form-group">
                            {% for row in page.post.prefs %}
                                    <label>Check all that apply {{page.post.prefs.is_private}}</label>
                            {% endfor %}        
                                    <br/>
<div class="custom-control custom-checkbox">
  {% if page.post.status == 'Active' %}
    <input type="checkbox" class="custom-control-input" id="is_published" name="is_published">
  {% else %}
    <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" checked="checked">
  {% endif %}  
    <label class="custom-control-label" for="is_published">Save this post as Draft</label>
  </div>

{% for row in page.post.prefs %}

 <div class="custom-control custom-checkbox">
  {% if row.is_private is same as(0) %}
    <input type="checkbox" class="custom-control-input" id="is_private" name="is_private">
  {% else %}
    <input type="checkbox" class="custom-control-input" id="is_private" name="is_private" checked="checked">
  {% endif %}  
    <label class="custom-control-label" for="is_private">Do not display outside school members.</label>
  </div>
   <div class="custom-control custom-checkbox">
  {% if row.is_restricted == 0 %} 
    <input type="checkbox" class="custom-control-input" id="is_restricted" name="is_restricted">
  {% else %}
    <input type="checkbox" class="custom-control-input" id="is_restricted" name="is_restricted" checked="checked">
  {% endif %} 
    <label class="custom-control-label" for="is_restricted">Do not display outside specified class</label>
  </div>
   <div class="custom-control custom-checkbox">
  {% if row.display_author == 0 %} 
    <input type="checkbox" class="custom-control-input" id="display_author" name="display_author">
  {% else %}
    <input type="checkbox" class="custom-control-input" id="display_author" name="display_author" checked="checked">
  {% endif %}    
    <label class="custom-control-label" for="display_author">Display author name in post</label>
  </div>
   <div class="custom-control custom-checkbox">
  {% if row.display_organization == 0 %} 
    <input type="checkbox" class="custom-control-input" id="display_organization" name="display_organization">
  {% else %}
    <input type="checkbox" class="custom-control-input" id="display_organization" name="display_organization" checked="checked">
  {% endif %}     
    <label class="custom-control-label" for="display_organization">Display organization name in post</label>
  </div>
     <div class="custom-control custom-checkbox">
  {% if row.allow_likes == 0 %} 
    <input type="checkbox" class="custom-control-input" id="allow_likes" name="allow_likes">
  {% else %}
    <input type="checkbox" class="custom-control-input" id="allow_likes" name="allow_likes" checked="checked">
  {% endif %}      
    <label class="custom-control-label" for="allow_likes">Allow people to like this post</label>
  </div>
     <div class="custom-control custom-checkbox">
  {% if row.allow_comments == 0 %} 
    <input type="checkbox" class="custom-control-input" id="allow_comments" name="allow_comments" >
  {% else %}
    <input type="checkbox" class="custom-control-input" id="allow_comments" name="allow_comments" checked="checked">
  {% endif %}      
    <label class="custom-control-label" for="allow_comments">Allow people to comment on this post</label>
  </div>
 
 {% endfor %}  

                                </div>
                             
                        </div>
                    </div> 
                      
			<div class="row clearfix">
        <input type="hidden" name="post_page" id="post_page" value="edit-post"/>
        <input type="hidden" name="post_qcode" value="{{page.post.qcode}}"/>
			<div class="col-lg-12 col-md-12 col-sm-12" style="margin-bottom:30px;">
			<button type="submit" class="btn btn-primary m-t-20">Update Content</button>
			 </div></div>
        <!-- </div></div>  -->
          {% endif %}
			</form>
<div id="form_overlay" style="display:none;">
          <div style="margin: 68px; text-align: center;">
          <img src="{{ base_url() }}/images/loading.gif" style="width:32px;margin:10px auto;"/>
          <h5 id="responseMsg" style="margin-bottom: 2px;"><strong>Processing Request...</strong></h5>
          <p>Please wait...</p>
          </div>          
          </div>


        </div>
    </div>
{% endblock %}
{% block footer_scripts %}
<script src="{{ base_url() }}/scripts/app.js"></script>
<script src="{{ base_url() }}/vendor/select2/select2.min.js"></script>    
<script src="{{ base_url() }}/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="{{ base_url() }}/vendor/parsleyjs/js/parsley.min.js"></script>
<script src="{{ base_url() }}/vendor/multi-select/js/jquery.multi-select.js"></script> <!-- Multi Select Plugin Js -->
<script src="{{ base_url() }}/vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script src="{{ base_url() }}/assets/js/pages/forms/advanced-form-elements.js"></script>
<script src="{{ base_url() }}/vendor/sweetalert2/dist/sweetalert2.min.js"></script> <!-- SweetAlert Plugin Js --> 
<script src="{{ base_url() }}/vendor/dropify/js/dropify.min.js"></script>
<script src="{{ base_url() }}/assets/js/pages/forms/dropify.js"></script>
<script src="{{ base_url() }}/vendor/summernote/dist/summernote.js"></script>
<script src="{{ base_url() }}/scripts/create_post.js"></script>

<script>
    $(function() {
       //$('.select2').select2();
        $('#createPostForm').parsley();
    });

    var postBodyHtml = $('<p />').html('{{page.post.body}}').text(); 
    console.log(postBodyHtml);
    $('#formatted_body').summernote({
      placeholder: postBodyHtml,
      tabsize: 2,
      height: 120
    }).summernote('code', postBodyHtml);
  
</script>

<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous">
</script> -->
<script src="{{ base_url() }}/scripts/multi_upload.js"></script>

{% endblock %}