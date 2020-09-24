{% extends 'applayout.twig' %}
{% block header_scripts %}
<link rel="stylesheet" href="{{ base_url() }}/vendor/jquery-datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ base_url() }}/vendor/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css">
<link rel="stylesheet" href="{{ base_url() }}/vendor/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css">
<link rel="stylesheet" href="{{ base_url() }}/vendor/sweetalert2/dist/sweetalert2.css"/>
<link rel="stylesheet" href="{{ base_url() }}/assets/css/blog.css">
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
               
            </div>
            
<!--- START CONTENT--->   
   <div class="row clearfix">
        {% if page.post %}
            <!-- <div>{{page.post.post_type_table.upload_content_banner}}</div> -->
                <div class="col-lg-8 col-md-12 left-box">
                    <div class="card single_post">
                        <div class="body">
                            <div class="img-post">
                                {% if page.post.images is empty %} 
                                    <img class="d-block img-fluid" src="{{ base_url() }}/assets/images/posts/econtent.jpg" alt="{{page.post.title}}">  
                                {% else %}
                                    {% set break = false %}
                                    {% for item in page.post.images if not break %}
                                        <img class="d-block img-fluid" src="{{ base_url() }}/uploads/{{item.image}}" alt="{{page.post.title}}">
                                        {% set break = true %} 
                                    {% endfor %}    
                                {% endif %}  
                            </div>
                            <h3><a href="#">{{page.post.title}}</a></h3>
							<span class="badge badge-info">{{page.post.class_name}}</span>
							<span class="badge badge-info">{{page.post.subject_name}}</span>
							<span class="badge badge-info">{{page.post.topic_name}}</span>
							 
							<p><small>Posted on &nbsp;{{page.post.date_created}}</small></p>
							
                            <p>{{page.post.description}}</p>
                        </div>  

                        <div class="footer">
                            <div class="actions">
                              {% if page.post.isLiked == 1 %}
								
								 <button type="button" id="post_dislike" class="btn btn-success" dd="{{ base_url() }}/apis/posts/like" dt="dislike"><i class="fa fa-heart"></i> <span>Like This</span></button>
                                 <button type="button" id="post_like" class="btn btn-outline-secondary" dd="{{ base_url() }}/apis/posts/like" dt="like" style="display: none;"><i class="fa fa-heart-o"></i> Favourite</button>
                              {% else %}
                                  <button type="button" id="post_dislike" class="btn btn-success" dd="{{ base_url() }}/apis/posts/like" dt="dislike" style="display: none;"><i class="fa fa-heart"></i> <span>Like This</span></button>
                                  <button type="button" id="post_like" class="btn btn-outline-secondary" dd="{{ base_url() }}/apis/posts/like" dt="like"><i class="fa fa-heart-o"></i> Favourite</button>
                                
                              {% endif %}

                                 <!-- processing button -->
                                <button type="button" id="process" class="btn btn-primary" disabled="disabled" style="display: none;"><i class="fa fa-spinner fa-spin"></i> <span>Please wait...</span></button>
								 
                            </div>
                            <ul class="stats">
                                <li><a href="javascript:void(0);" class="icon-eye">{{page.post.numViews}}</a></li>
                                <li><a href="javascript:void(0);" class="icon-heart">{{page.post.numLikes}}</a></li>
                                <li><a href="javascript:void(0);" class="icon-bubbles">{{page.post.numComments}}</a></li>
                            </ul>
                        </div>
                    </div>


<!---- Post Body Starts Here ----->
 <div class="card single_post">
<div class="body">
{{page.post.body|raw}}
<!-- <p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.</p> -->
 </div></div>
<!---- Post Body Ends Here ----->
						
 {% if page.post.comments|length > 0 %}
<!-- Post comments --> 
                    <div class="card">
                            <div class="header" id="count_comments">
                                <h2>Comments ({{page.post.numComments}})</h2>
                            </div>

                            <div class="body">
                                <ul class="comment-reply list-unstyled" id="comment-lists">
                                  {% for row in page.post.comments %}
                                    <li class="row clearfix">
                                        <div class="icon-box col-md-2 col-4">
                                            {% if row.user_image is empty %} 
                                                <img class="img-fluid img-thumbnail" src="{{ base_url() }}/assets/images/avatar.jpg" alt="Awesome Image">  
                                            {% else %}
                                                <img class="d-block img-fluid" src="{{row.user_image}}" alt="First slide">
                                                <!-- <img class="d-block img-fluid" src="{{ base_url() }}/uploads/{{page.post.post_type_table.upload_content_banner}}" alt="First slide"> -->
                                            {% endif %}  
                                        </div>
                                        <div class="text-box col-md-10 col-8 p-l-0 p-r0">
                                            {% if row.name is not empty %} 
                                                <h5 class="m-b-0">{{row.name}}</h5>
                                            {% endif %}    
                                            <p>{{row.comment}} </p>
                                            <ul class="list-inline">
                                                <li><a href="javascript:void(0);">{{row.timestamp}}</a></li>
                                                <li><a href="javascript:void(0);">Reply</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                  {% endfor %}    
                                  <!--   <li class="row clearfix">
                                        <div class="icon-box col-md-2 col-4"><img class="img-fluid img-thumbnail" src="{{ base_url() }}/assets/images/avatar.jpg" alt="Awesome Image"></div>
                                        <div class="text-box col-md-10 col-8 p-l-0 p-r0">
                                            <h5 class="m-b-0">Christian Louboutin</h5>
                                            <p>Great tutorial but few issues with it? If i try open post i get following errors. Please can you help me?</p>
                                            <ul class="list-inline">
                                                <li><a href="javascript:void(0);">Mar 12 2018</a></li>
                                                <li><a href="javascript:void(0);">Reply</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="row clearfix">
                                        <div class="icon-box col-md-2 col-4"><img class="img-fluid img-thumbnail" src="{{ base_url() }}/assets/images/avatar.jpg" alt="Awesome Image"></div>
                                        <div class="text-box col-md-10 col-8 p-l-0 p-r0">
                                            <h5 class="m-b-0">Kendall Jenner</h5>
                                            <p>Very nice and informative article. In all the years I've done small and side-projects as a freelancer, I've ran into a few problems here and there.</p>
                                            <ul class="list-inline">
                                                <li><a href="javascript:void(0);">Mar 20 2018</a></li>
                                                <li><a href="javascript:void(0);">Reply</a></li>
                                            </ul>
                                        </div>
                                    </li> -->
                                </ul>                                        
                            </div>
                        </div>
{% endif %}  						


 {% if page.showStats %}
<div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card text-center">
                        <div class="body">
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-6">
                                    <div class="body">
                                        <i class="fa fa-eye fa-2x"></i>
                                        <h4>{{page.post.numViews}}</h4>
                                        <span>Total Views</span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-6">
                                    <div class="body" id="list_count_likes">
                                        <i class="fa fa-thumbs-o-up fa-2x"></i>
                                        <h4>{{page.post.numLikes}}</h4>
                                        <span>Likes</span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-6">
                                    <div class="body" id="list_count_comments">
                                        <i class="fa fa-comments fa-2x"></i>
                                        <h4>{{page.post.numComments}}</h4>
                                        <span>Comments</span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-6">
                                    <div class="body">
                                        <i class="fa fa-user fa-2x"></i>
                                        <h4>{{page.post.numUniqueViews}}</h4>
                                        <span>Unique Views</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{% endif %}
			
                        <div class="card">
                            <div class="header">
                                <h2>Leave a comment <small>Want to say something about this post?</small></h2>
                            </div>
                            <div class="body">
                                <div class="comment-form">

                                    <form class="row clearfix" name="createPostCommentForm" id="createPostCommentForm" method="GET">
                                        <div class="col-sm-12">
										    <input type="hidden" name="user_id" id="user_id" class="form-control" value="{{session.userID}}">
                                            <input type="hidden" name="post_id" id="post_id" class="form-control" value="{{page.post.id}}"/>
                                            <div class="form-group">
                                                <textarea rows="4" name="comment_box" id="comment_box" class="form-control no-resize" placeholder="Please type what you want..." dd="{{ base_url() }}/apis/posts/comment"></textarea>
                                            </div>
                                            <input type="hidden" id="post_page" name="post_page" value="detail-post"/>
                                            <input type="submit" class="btn btn-block btn-primary" value="SUBMIT"/>  
                                        </div>                                
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
                        </div>

                      
                </div>
                <div class="col-lg-4 col-md-12 right-box">				
					{% if (page.post.author_id == session.userID) and (session.userID == 1) %} 
    					<div class="card">	
    					<a href="{{ base_url() }}/edit-post/{{page.post.qcode}}"><input type="button" class="btn btn-block btn-lg btn-primary" value="Edit Post"/></a></div>
                     {% endif %}
					 
	<!-- 				 <div class="card">	<button type="button" class="btn btn-block btn-primary" disabled="disabled"><i class="fa fa-spinner fa-spin"></i> <span>Please wait...</span></button></div>
					 
					 <div class="card">	
                      <button type="button" class="btn btn-block btn-lg btn-success"><i class="fa fa-heart"></i> <span>Like This</span></button>
                    </div> -->
					
{% if page.post.tags|length > 0 %}
                    <div class="card">
                        <div class="header">
                            <h2>Tags</h2>
                        </div>
                        <div class="body widget">
                            <ul class="list-unstyled categories-clouds m-b-0">
                               {% for row in page.post.tags %}
                                <li><a href="javascript:void(0);">{{row.tag}}</a></li>
								{% endfor %}
                            </ul>
                        </div>
                    </div>
{% endif %}

<!----- SIMILAR POSTS WIDGET ----->	
{% if page.post.recent_posts|length > 0 %}					
                    <div class="card">
                        <div class="header">
                            <h2>Related Posts</h2>                        
                        </div>
                        <div class="body widget popular-post">
                            <div class="row">
                                <div class="col-lg-12">
								
                                        {% for row in page.post.recent_posts %}  								
                                            <div class="media mleft"><div class="media-left"> <a href="{{ base_url() }}/post-detail/{{row.qcode}}"> <img class="media-object" src="{{ base_url() }}/uploads/{{row.image}}" width="64" height="64" alt=""> </a> </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading">{{row.title}}</h4>
                                                    {{row.description|slice(0, 35)}} ..
                                                </div>
                                            </div>
                                        {% endfor %}							
                                </div>
                            </div>
                        </div>
                    </div>
{% endif %}					
<!----- ENDS SIMILAR POSTS WIDGET ----->
	
<!----- STARTS LATEST POSTS WIDGET ----->		
{% if page.post.latest_images|length > 0 %}			
                    <div class="card">
                        <div class="header">
                            <h2>Latest Posts</h2>
                        </div>
                        <div class="grid-container" style=" display: grid; grid-template-columns: 150px 150px; grid-gap: 1em; padding: 0px 15px 15px 15px;">
                            <!-- <div class="body widget"> -->
                            <!-- <ul class="list-unstyled instagram-plugin m-b-0"> -->
                                {% for row in page.post.latest_images %}
                                    <a href="{{ base_url() }}/post-detail/{{row.qcode}}"><img style="width: 100%; height: auto; padding: 0px;" src="{{ base_url() }}/uploads/{{row.image}}" alt="image description" ></a>
                                {% endfor %}
                            <!-- </ul> -->
                        </div>
                    </div>
{% endif %}                    
 <!----- ENDS LATEST POSTS WIDGET ----->                   
					
					<!---- Author Details --->
					<div class="card member-card">
                        <div class="header bg-info">
                            <h4 class="m-t-10 text-light">Author Details</h4>
                        </div>
                        <div class="member-img" style="margin-top: -30px;">
                                {% if page.post.author_image is empty %} 
                                    <a href="javascript:void(0);"><img class="rounded-circle" src="{{ base_url() }}/assets/images/avatar.png" alt="Author Photo" style="width:72px;height:72px;"></a>  
                                {% else %}
                                    <a href="javascript:void(0);"><img src="{{page.post.author_image}}" class="rounded-circle" alt="{{page.post.author_name}}" style="width:72px;height:72px;"></a>
                                {% endif %}
                        </div>
                        <div class="body">
                            <div class="col-12">
							 <h4 class="text-dark">{{page.post.author_name}}</h4>
                                <p class="text-muted">{{page.post.author_role}} <br/> {{page.post.author_school}}</p>
                            </div>
                        </div>
                    </div>
					<!---- End of Author Details --->
					
                </div>
            </div>
<!--- END CONTENT -->           

                {% endif %}				
			</div>	
        </div>
    </div>
{% endblock %}
{% block footer_scripts %}
<script src="{{ base_url() }}/scripts/app.js"></script>
<script src="{{ base_url() }}/scripts/list_topics.js"></script>
<script src="{{ base_url() }}/vendor/sweetalert2/dist/sweetalert2.min.js"></script> <!-- SweetAlert Plugin Js --> 
<script src="{{ base_url() }}/scripts/detail_post.js"></script>
{% endblock %}