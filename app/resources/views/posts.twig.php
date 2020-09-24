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
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-sm-12">                        
                        <h2>E-Learning Center</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ base_url() }}"><i class="icon-home"></i></a></li>                            
                            <li class="breadcrumb-item">List E-Contents</li>
                        </ul>
                    </div>
                    <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                        <a href="{{ base_url() }}/create-post"><button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> <span>Add New Post</span></button></a> 
                    </div>
                </div>
            </div>
 
<!--- Posts filter ---->
 <div class="row clearfix"> 
                <div class="col-md-12">
                    <div class="card">
                        <div class="body">
                            <div class="input-group" id="adv-search">
                                    <input type="text" class="form-control" id="text_search" placeholder="Search here..." data-url="{{ base_url() }}/postsFilter" data-baseurl="{{ base_url() }}" />
                                <div class="input-group-btn">
                                    <div class="btn-group" role="group">
                                        <div class="dropdown dropdown-lg">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
                                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                                <form class="form-horizontal" role="form" >
                                                    <div class="form-group">
                                                    <label for="filter">Filter by Class</label>
                                                    <select class="form-control" id="filter_search_class">
                                                        <option value="0" selected>All Classes</option>
                                                        {% if page.classes %}
                                                            {% for row in page.classes %}
                                                                <option value="{{row.id}}">{{row.name}}</option>
                                                            {% endfor %}
                                                        {% endif %}
                                                    </select>
                                                    </div>
                                                    <div class="form-group">                                                        
                                                    <label for="filter">Filter By Subject</label>
                                                    <select class="form-control" id="filter_search_subject">
                                                        <option value="0" selected>All Subjects</option>
                                                        {% if page.subjects %}
                                                          {% for row in page.subjects %}
                                                            <option value="{{row.id}}">{{row.title}}</option>
                                                          {% endfor %}
                                                        {% endif %}
                                                    </select>
                                                    </div>
                                                    <div class="form-group">                                                        
                                                     <label for="filter">More Filters</label>
                                                    <select class="form-control" id="filter_search_extra">
                                                        <option value="0" selected>All Posts</option>
                                                        <option value="1">Most Relevant</option>
                                                        <option value="2">Most popular</option>
                                                        <option value="3">Most commented</option>
                                                    </select>
                                                    </div>
                                                    <!-- <button type="submit" class="btn btn-primary btn-block">Search</button> -->
                                                    <button type="button" class="btn btn-primary btn-block" id="filter_search_button">Search</button>
                                                </form>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary" id="text_search_button"><span class="icon-magnifier" aria-hidden="true"></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="body searched_result">                                                   
                            <p class="m-b-0">Search Result Like "<b>Java</b>"</p>
                            <strong> About&nbsp;"{{ page.countAllPosts }}" posts found</strong>
                        </div>
                    </div>
                    </div>
                </div>
<!--- End of Posts filter ---->
				
            <div class="row clearfix allPosts">

            {% if page.posts %}
                {% for row in page.posts %}
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <!--- Start Post Card-->
                      <div class="card single_post">
                        <div class="body">
                            <div class="img-post">

                                {% if row.images is empty %} 
                                    <img class="d-block img-fluid" src="{{ base_url() }}/assets/images/posts/econtent.jpg" alt="{{row.title}}">  
                                {% else %}
                                    {% set break = false %}
                                    {% for item in row.images if not break %}
                                        <img class="d-block img-fluid" src="{{ base_url() }}/uploads/{{item.image}}" alt="{{row.title}}">
                                        {% set break = true %}
                                    {% endfor %}    
                                {% endif %} 
        
                            </div>
                            <h3><a href="{{ base_url() }}/post-detail/{{row.qcode}}">{{row.title}}</a></h3>
							<span class="badge badge-info">{{row.class_name}}</span>
							<span class="badge badge-info">{{row.subject_name}}</span>
							<!-- <span class="badge badge-info">{{row.topic_name}}</span> -->
                        </div>
                        <div class="footer">
						<div class="actions">
                                <a href="{{ base_url() }}/post-detail/{{row.qcode}}" class="btn btn-outline-secondary">Read now</a>
                            </div>
                           
                         <ul class="stats">
                                <li><a href="javascript:void(0);" class="icon-heart">{{row.numLikes}}</a></li>
                                <li><a href="javascript:void(0);" class="icon-bubbles">{{row.numComments}}</a></li>
                            </ul>
                            
                        </div>
                    </div>
                    <!--- End Post Card-->
                </div>
                {% endfor %}
            {% endif %}
            </div>     
				
		</div>	
      </div>
    </div>
{% endblock %}
{% block footer_scripts %}
<script src="{{ base_url() }}/scripts/app.js"></script>
<script src="{{ base_url() }}/scripts/list_topics.js"></script>
<script src="{{ base_url() }}/scripts/search_posts.js"></script>

<script src="{{ base_url() }}/assets/bundles/datatablescripts.bundle.js"></script>
<script src="{{ base_url() }}/vendor/jquery-datatable/buttons/dataTables.buttons.min.js"></script>
<script src="{{ base_url() }}/vendor/jquery-datatable/buttons/buttons.bootstrap4.min.js"></script>
<script src="{{ base_url() }}/vendor/jquery-datatable/buttons/buttons.colVis.min.js"></script>
<script src="{{ base_url() }}/vendor/jquery-datatable/buttons/buttons.html5.min.js"></script>
<script src="{{ base_url() }}/vendor/jquery-datatable/buttons/buttons.print.min.js"></script>
<script src="{{ base_url() }}/vendor/sweetalert2/dist/sweetalert2.min.js"></script> <!-- SweetAlert Plugin Js --> 
<script src="{{ base_url() }}/assets/js/pages/tables/jquery-datatable.js"></script>
{% endblock %}