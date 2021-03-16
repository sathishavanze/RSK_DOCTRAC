 <?php
 $logo = base_url("assets/img/isgnlogo.jpg");
 $mOrganization = $this->Common_Model->get_row('mOrganization',['mOrganization.OrganizationUID'=>1]);

 $getScrollingData = $this->Common_Model->getScrollingData();
 $Customers = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []); 
 $CustomerMenuWorkflow = $this->Common_Model->getCustomer_Workflows($this->parameters['DefaultClientUID']); 
 $CustomerWorkflowUIDs = array_column($CustomerMenuWorkflow, 'WorkflowModuleUID'); 

 $enam  =  $this->UserPermissions->ProfileColor;
 if($enam == 1)
 {
 	$custom_bg_color = '#9c27b0';  
 	$custom_box_shadow = "0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(156, 39, 176, 0.4)";
 	$badge_green  = '#66bb6a';
 	$badge_rose = '#f44336';
 	$badge_red = '#e91e63';
 	$badge_warning = '#fb8c00';
 }
 elseif($enam == 2)
 {
 	$custom_bg_color = '#00bcd4';
 	$custom_box_shadow = "0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(0, 188, 212, .4)";
 	$badge_green  = '#66bb6a';
 	$badge_rose = '#f44336';
 	$badge_red = '#e91e63';
 	$badge_warning = '#fb8c00';
 }
 elseif($enam == 3)
 {
 	$custom_bg_color = '#66bb6a';
 	$custom_box_shadow = "0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(76, 175, 80, 0.4)";
 	$badge_green  = '#9c27b0';
 	$badge_rose = '#f44336';
 	$badge_red = '#e91e63';
 	$badge_warning = '#fb8c00';
 }
 elseif($enam == 4)
 {
 	$custom_bg_color = '#fb8c00';
 	$custom_box_shadow = "0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(255, 152, 0, 0.4)";
 	$badge_green  = '#66bb6a';
 	$badge_rose = '#f44336';
 	$badge_red = '#e91e63';
 	$badge_warning = '#9c27b0';
 }
 elseif($enam == 5)
 {
 	$custom_bg_color = '#f44336';
 	$custom_box_shadow = "0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(244, 67, 54, 0.4)";
 	$badge_green  = '#66bb6a';   
 	$badge_rose = '#fb8c00';
 	$badge_red = '#00bcd4';
 	$badge_warning = '#9c27b0';
 }
 elseif($enam == 6)
 {
 	$custom_bg_color = '#e91e63';
 	$custom_box_shadow = "0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(233, 30, 99, 0.4)";
 	$badge_green  = '#66bb6a';
 	$badge_rose = '#fb8c00';
 	$badge_red = '#9c27b0';
 	$badge_warning = '#fb8c00';
 }
 else
 {
 	$custom_bg_color = '#00bcd4';
 	$custom_box_shadow = "0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(0, 188, 212, .4)";
 	$badge_green  = '#66bb6a';
 	$badge_rose = '#f44336';
 	$badge_red = '#e91e63';
 	$badge_warning = '#fb8c00';
 }
 ?>
 <style type="text/css">
 	.custom_nav_menu span.active a{
 		background-color: #E94441;
 		color:#fff;
 		box-shadow: 0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(115, 114, 114, 0.4);

 	} 
 	.custom_nav_menu{
 		text-align: center!important;
 		background:#efefef;
 		margin:0;
 	} 
 	.custom_nav_menu span{
 		background-color: transparent!important;

 	}
 	.custom_nav_menu span:hover a{
 		background-color: #E94441;
 		color:#fff;
 		box-shadow: 0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(115, 114, 114, 0.4);
 	}
 	.custom_nav_menu span a{
 		padding: 13px 15px;
 		border-radius:4px;
 		font-weight: bold;
 		font-size: 12px;
 		color: #555;
 	}

 	.custom_nav_menu span{
 		margin-right:3px;
 		line-height: 25px;
 	}

 	.custom_nav_menu span a .count{
 		border-radius: 15px;
 		border: 1px solid #DDD;
 		margin-left: 3px;
 		line-height: 0;
 		padding: 0px 2px 0px 2px;
 		font-size: 10px;
 		background-color: #FFFFFF!important;
 	}

 	.navbar-count span.count {
 		border-radius: 15px;
 		border: 1px solid #DDD;
 		margin-left: 3px;
 		padding: 2px 5px 0px 5px;
 		font-size: 10px;
 		background-color: #FFFFFF!important;
 	}
 	.navbar-brand {
 		padding: 5px 0;
 		font-size: 20px;
 		line-height: 20px;
 		font-weight: 300;
 		text-transform: uppercase;
 		letter-spacing: .1em;
 		color: #fff;
 		font-family: "Roboto","Helvetica Neue",Helvetica,Arial,sans-serif;
 	}
 	.navbar-brand .logo {
 		display: inline-block;
 		margin-right: 10px;
 		width: 30px;
 		height: 30px;
 		font-size: 18px;
 		line-height: 30px;
 		text-align: center;
 		letter-spacing: normal;
 		color: #f5f5f5;
 		font-weight: 400;
 		background-color: #E94441;
 		border-radius: 50%;
 	}
 	#navigation-example {
 		height: 45px !important;
 		padding: 0px !important;
 	}
 	.top_hraduser_img{
 		width: 38px;
 		height: 38px;
 		border-radius: 50%;
 		border: 1px solid #eee;
 		margin-right: 10px;
 	}
 	.togglebutton label input[type=checkbox]:checked+.toggle {
 		background: <?php echo $custom_bg_color;  ?> !important; 
 	}

 	.togglebutton label input[type=checkbox]:checked+.toggle:after {
 		left: 15px;
 		border-color:  <?php echo $custom_bg_color;  ?> !important;
 	}
 	#nprogress .peg{
 		box-shadow: unset !important;
 	}
 	#nprogress .bar {
 		background: <?php echo $custom_bg_color;  ?> !important; 
 	}
 	.hightlightcolor{
 		background: <?php echo $custom_bg_color;  ?> !important; 
 	}
 	.form-control, .is-focused .form-control{
 		background-image : linear-gradient(to top, <?php echo $custom_bg_color; ?> 2px, rgba(156, 39, 176, 0) 2px), linear-gradient(to top, #d2d2d2 1px, rgba(210, 210, 210, 0) 1px)
 	}
 	.card_theme_color .card-icon{
 		background: <?php echo $custom_bg_color;  ?> !important; 
 		box-shadow:  <?php echo $custom_box_shadow; ?> !important;
 	}
 	.nav_theme_color .nav-link.active{
 		background: <?php echo $custom_bg_color;  ?> !important; 
 		box-shadow:  <?php echo $custom_box_shadow; ?> !important;
 	}

 	.card_theme_color .card-text {
 		background: <?php echo $custom_bg_color;  ?> !important; 
 		box-shadow:  <?php echo $custom_box_shadow; ?> !important;
 	}
 	.form-check .form-check-input:checked+.form-check-sign .check{
 		background: <?php echo $custom_bg_color;  ?> !important; 
 	}

 	.form-check .form-check-input:checked+ .circle{
 		border-color: <?php echo $custom_bg_color;  ?> !important; 
 	}
 	.form-check .form-check-input:checked+ .circle .check{
 		background-color: <?php echo $custom_bg_color;  ?> !important; 
 	}
 	#navbarNavDropdown li a:hover, #navbarNavDropdown li a:active, #navbarNavDropdown li a:focus{
 		color:<?php echo $custom_bg_color;  ?> !important; 
 	}
 	.listactive{
 		color:<?php echo $custom_bg_color;  ?> !important; 
 	}
 	.calendar-icon{
 		cursor: pointer;
 	}

 	.card-wizard .moving-tab{
 		background-color:<?php echo $custom_bg_color;  ?> !important; 
 	}
 	.scrollup{
 		background-color:<?php echo $custom_bg_color;  ?> !important; 
 	}
 	.pagination>.page-item.active>a, .pagination>.page-item.active>a:focus, .pagination>.page-item.active>a:hover, .pagination>.page-item.active>span, .pagination>.page-item.active>span:focus, .pagination>.page-item.active>span:hover{
 		background-color: <?php echo $custom_bg_color;  ?> !important; 
 		border-color:<?php echo $custom_bg_color;  ?> !important; 
 	}
 	.filtericon{
 		color:<?php echo $custom_bg_color;  ?> !important; 
 	}
 	.checkbox input[type="checkbox"]:checked + label::after, .checkbox input[type="radio"]:checked + label::after{
 		color:<?php echo $custom_bg_color;  ?> !important; 
 	}
 	.bootstrap-datetimepicker-widget table td.active:hover>div, .bootstrap-datetimepicker-widget table td.active>div{
 		background-color: <?php echo $custom_bg_color;  ?> !important; 
 		box-shadow : <?php echo $custom_box_shadow;  ?> !important;  
 	}
 	.excel-expo-btn{
		right: 19px !important;
	}
 	.excel-exportN-btn {
		position: absolute;
	    right: 0px;
	    /*top: 9px;*/
	}
	.excel-exportN-btn i{
		font-size:15px;
		color:#0B781C;
		cursor: pointer;
		margin-top: 14px;
	}
</style>
<div class="main-panel">

	<!-- Navbar -->
	<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top " id="navigation-example">
		<div class="container-fluid" id="header_top">
			<div class="Main_logo">
			<div class="navbar-wrapper">
                <div class="navbar-minimize">
                  <button id="minimizeSidebar" style="border: none;background: #fff;">
                    <i class="icon-menu7 visible-on-sidebar-regular"></i>
                    <i class="icon-cross2 visible-on-sidebar-mini"></i>
                  </button>
                </div>
                <a class="navbar-brand ajaxload" href="javascript:void(0);" id="pagetitle"></a>
              </div>				
				<!-- <img src="<?php echo base_url(); ?>/assets/img/doctrack_00000.png" style="width: 200px; "/> -->
				<img src="<?php echo base_url(); ?>/assets/img/findocs.png" style="width: 150px; display: none;"/>
			</div>

			<div class="collapse navbar-collapse justify-content-end">

			<div class="input-group no-border" style="width: 200px;margin-right: 20px;">
				<select class="select2picker form-control"  id="adv_CustomerUID" name="CustomerUID" placeholder="Client">
					<option value=""></option>
					<?php foreach ($Customers as $key => $value) { 
						if($value->CustomerUID == $this->parameters['DefaultClientUID'])
						{ ?>
						  <option value="<?php echo $value->CustomerUID; ?>" selected><?php echo $value->CustomerName; ?></option>
						<?php }else{ 
						  	if(in_array($this->RoleType, $this->config->item('Super Admin'))) { ?>
						    	<option value="<?php echo $value->CustomerUID; ?>"><?php echo $value->CustomerName; ?></option>
							<?php } 
						} 
					} ?>							
				</select>
			</div>

		<!-- End -->
				<form id="form_search" name="navbar-form"  action="<?php echo base_url(); ?>Search" method="POST" class="form-inline">
					<div class="input-group no-border">
						<input type="text" name="search_value" value="" class="form-control" placeholder="Search..."  id="searchinput" />
						<button type="submit" class="btn btn-white btn-round btn-just-icon" id="searchbtn" type="submit">
							<i class="icon-search4"></i>
							<div class="ripple-container"></div>
						</button>
					</div>

				</form>

				<ul class="navbar-nav"> 
<li class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" style="display:none;">
                <span class="navbar-toggler-icon"></span>
              </li>
					<div class="collapse navbar-collapse" id="navbarNavDropdown" style="display:none;">
						<ul class="navbar-nav navbar-count">
							<li class="nav-item dropdown has-mega-menu" style="position:static;">
								<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="true" aria-expanded="false"><i class="icon-grid5"></i></a>
								<div class="dropdown-menu" style="width:100%;">
									<div class="px-0 ">
										<div class="col-md-12">
											<div class="form-group">
												<input type="text" id="myInput" class="form-control" placeholder="Search...">
											</div>
										</div>
										<div class="col-md-12">
											<div class="row">

												<?php $fieldsections = $this->Common_Model->get_definedFieldSection(['common','jumbobar']); ?>

												<?php foreach ($fieldsections as $fieldkey => $fieldsection) {  ?>

													<?php if ($fieldsection->FieldSection == "WORKFLOW") { 
														$MenuLinks = $this->Common_Model->get_definedWorkflowDynamicMenu_options($fieldsection->FieldSection,array('common','jumbobar'));
													} 
													else{
														$MenuLinks = $this->Common_Model->get_definedDynamicMenu_options($fieldsection->FieldSection,array('common','jumbobar')); 			
													}

													?>

													
													<?php if (!empty($MenuLinks)) {  ?>

														<div class="col-sm-2 menufilter" style="border-right:1px solid  #eaeaea">
															<h4 ><i class="<?php echo $MenuLinks[0]->IconClass; ?>" style="padding-right: 10px; color: #4b669e"></i><?php echo $MenuLinks[0]->FieldSection; ?></h4>

															<?php foreach ($MenuLinks as $key => $value) {

																if($fieldsection->FieldSection == 'WORKFLOW' && !empty($value->WorkflowModuleUID) && in_array($value->WorkflowModuleUID, $CustomerWorkflowUIDs)) { ?>

																	<a class="dropdown-item menufilter ajaxload link-<?php echo $value->controller; ?>" href="<?php echo base_url().$value->controller . (!empty($value->Parameters) ? '/'.$value->Parameters : ''); ?>"><?php echo $value->FieldName; ?></a>

																<?php } elseif($fieldsection->FieldSection == 'WORKFLOW' && empty($value->WorkflowModuleUID)) { ?>

																	<a class="dropdown-item menufilter ajaxload link-<?php echo $value->controller; ?>" href="<?php echo base_url().$value->controller . (!empty($value->Parameters) ? '/'.$value->Parameters : ''); ?>"><?php echo $value->FieldName; ?></a>

																<?php } elseif($fieldsection->FieldSection != 'WORKFLOW') { ?>

																	<a class="dropdown-item menufilter ajaxload link-<?php echo $value->controller; ?>" href="<?php echo base_url().$value->controller . (!empty($value->Parameters) ? '/'.$value->Parameters : ''); ?>"><?php echo $value->FieldName; ?></a>

																<?php }

															} ?>
														</div>

													<?php } ?>

												<?php  } ?>


												
											</div>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>

	<!-- 				<li class="nav-item">
						<a class="nav-link" title = "Help" href="<?php echo base_url();?>Help" style="margin: 0 auto;" >
							<i class="icon-help" style="font-size:20px"></i>                    
						</a>
					</li> -->
					<?php  $url = $this->uri->segment(1); if($url == 'Ordersummary'){
						?>
						<div id="Doc_show">

						</div>

					<?php  } ?>



					<li class="nav-item upload-notify" style="display: none;">
						<a class="nav-link ajaxload"  data-toggle="dropdown" title = "Help" aria-haspopup="true" aria-expanded="false" href="<?php echo base_url();?>Help" style="margin: 0 auto;" >
							<i class="icon-help" style="font-size:20px"></i>                    
						</a>



						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
							<div class="col-md-12">
								<p>File Uploading...<span class="text-right">54%</span></p>
								<div class="progress progress-line-info" id="progressupload" style=" height: 22px;">
									<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width:0%; height: 21px;">
										<span class="sr-only">0% Complete</span>
									</div>
								</div>
							</div>
						</div>
					</li>
					<li class="nav-item" style="padding-left:20px">
						<img src="" class="img-responsive" style="height:40px;" />
					</li>
				</ul>
				<div class="logo">
					<a href="javascript:void(0);" class="simple-text logo-normal text-center">
						<?php 


						if (!empty($mOrganization) && file_exists($mOrganization->OrganizationLogo)) {
							$logo = base_url($mOrganization->OrganizationLogo);
						}
						?>
						<img alt="LOGO" src = "https://www.ordersportal.sourcepointmortgage.com/assets/img/sourcepoint.png" style="height:45px;" />
					</a>
				</div>
				<div class="nav-item dropdown">
					<a class="nav-link" href="#." id="account_setting_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<!-- <i class="icon-user" style="font-size: 20px;color: #555;"></i> -->
						<?php
						
						if (empty($this->UserPermissions->Avatar)) { ?>
							<img class="top_hraduser_img" src="assets/img/profile.jpg">
						<?php } else { ?>
							<img class="top_hraduser_img" src="<?php echo $this->UserPermissions->Avatar; ?>">
						<?php } ?>
					</a>
					<div class="dropdown-menu dropdown-menu-right account_setting_menu
					account_setting_menu_list" style="right: 0;left: auto;" aria-labelledby="account_setting_menu">
					<a class="dropdown-item ajaxload" href="<?php echo base_url('Profile'); ?>">Profile </a>
					<!-- <a class="dropdown-item ajaxload" href="<?php echo base_url('Help'); ?>">Help </a> -->
					<a class="dropdown-item ajaxload" href="<?php echo base_url('Login/changepasswordpage'); ?>">Change Password </a>
					<a class="dropdown-item" href="<?php echo base_url('Login/Logout'); ?>">Logout </a>
				</div>
			</div>
		</div>
	</div>
</nav>


<!-- End Navbar -->

<!-- Holist Modal -->
<div id="appendholidaylistmodal" class="" role="dialog"></div>

<script type="text/javascript">
	$(document).ready(function(){

		$("#myInput").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$(".menufilter").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
		/*
		* @throws no exception
		* @author Santhiya M <santhiya.m@avanzegroup.com>
		* @return file
		* @since Augest 12th 2020
		*
		*/
		$(document).off('click', '.ExcelSDownload').on('click', '.ExcelSDownload', function(){
			var segment = '<?php echo $this->uri->segment(1); ?>';
		      var formdata = $('#advancedsearchdata').serialize();
		      var search = $('input[type=search]').val();

		     

		       $.ajax({
		        type: "POST",
		        url: "CommonController/ExcelDownload", 
		        xhrFields: {
		          responseType: 'blob',
		        },    
		        data: formdata+'&search='+search+'&segment='+segment,
		        
		        success: function(data) {
		        	
		             
		              var filename = segment+'.xlsx';
		              if (typeof window.chrome !== 'undefined') {
		                //Chrome version
		                var link = document.createElement('a');
		                link.href = window.URL.createObjectURL(data);
		                link.download = filename;
		                link.click();
		              } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
		                //IE version
		                var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
		                window.navigator.msSaveBlob(blob, filename);
		              } else {
		                //Firefox version
		                var file = new File([data], filename, { type: 'application/octet-stream' });
		                window.open(URL.createObjectURL(file));
		              }

	            },
	            error: function (jqXHR, textStatus, errorThrown) {
	              console.log(jqXHR);
	            },
	            failure: function (jqXHR, textStatus, errorThrown) {
	              console.log(errorThrown);
	            },
		      });
		});
	});
</script>



<div class="content" id="containerstart">
<?php if ($getScrollingData[0]->Active == 1) { ?>
 			<aside class="d-playbox">

 				<!-- DEMO begin -->
 				<div class="d-demo-wrap">

 					<!-- Plugin HTML begin -->
 					<div class="js-conveyor-1">
 						<ul>
							 <?php for($i=0;$i<10;$i++){ ?>
								<li>
 								<span><?php echo ($getScrollingData[0]->Description); ?></span>
 							</li>
							<?php } ?>

 						</ul>
 					</div>
 					<!-- Plugin HTML end -->

 				</div>
 				<!-- DEMO end -->

 			</aside>

 		<?php } ?>

	<div class="content">
		<div class="container-fluid">
			<div class="row" id="loadcontent">






