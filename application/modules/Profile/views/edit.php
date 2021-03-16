<!--BEGIN CONTENT-->
<script src="<?php echo base_url();?>assets/js/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
<style>
.user-display-avatar img {
	width: 150px;
	height: 150px;
	border-radius: 50%;
	border: 3px solid #ffffff;
}
.user-display-bg {
	max-height: 200px;
	overflow: hidden;
}
.user-display-avatar {
	position: absolute;
	left: 30px;
	top: 25px;
	margin-bottom: 30px;
	border-radius: 50%;
	background-color: #ffffff;
}
.profiletableclass{
	font-size: 25px;
	left: 0px;
	margin-left: 0px;
	text-align: left;
}

.table-responsive {
	border: 0;
	white-space: nowrap;
	background-color: #ffffff !important;
	border: none !important;
	border-top: none !important;
}

.size{
	font-size: 12px;
}

tbody > tr > td, .table > tfoot > tr > td {

	border-top: none !important;
}


.avatar-upload {
	position: relative;
	max-width: 205px;
	margin: 20px auto;
}
.avatar-upload .avatar-edit {
	position: absolute;
	right: 12px;
	z-index: 1;
	top: 10px;
}
.avatar-upload .avatar-edit input {
	display: none;
}
.avatar-upload .avatar-edit input + label {
	display: inline-block;
	width: 35px;
	height: 37px;
	margin-bottom: 0;
	border-radius: 100%;
	background: #FFFFFF;
	border: 1px solid transparent;
	box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
	cursor: pointer;
	font-weight: normal;
	transition: all 0.2s ease-in-out;
}
.avatar-upload .avatar-edit input + label:hover {
	background: #f1f1f1;
	border-color: #d6d6d6;
}
.avatar-upload .avatar-edit input + label:after {
	content: "\f040";
	font-family: 'FontAwesome';
	color: #757575;
	position: absolute;
	top: 10px;
	left: 0;
	right: 0;
	text-align: center;
	margin: auto;
}
.avatar-upload .avatar-preview {
	width: 192px;
	height: 192px;
	position: relative;
	border-radius: 100%;
	border: 6px solid #F8F8F8;
	box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
}
.avatar-upload .avatar-preview > div {
	width: 100%;
	height: 100%;
	border-radius: 100%;
	background-size: cover;
	background-repeat: no-repeat;
	background-position: center;
}
td {
  color: #3c3c3c;
}
.switch-button {
    display: inline-block;
    border-radius: 50px;
    background-color: #b3b3b3;
    width: 60px;
    height: 27px;
    padding: 4px;
    position: relative;
    overflow: hidden;
    vertical-align: middle;
}
.switch-button {
    display: inline-block;
    border-radius: 50px;
    background-color: #de6262;
    width: 60px;
    height: 27px;
    padding: 4px;
    position: relative;
    overflow: hidden;
    vertical-align: middle;
    text-align: left;
}
.switch-button.switch-button-xs {
    height: 20px;
    width: 53px;
    line-height: 16px;
    width: 50px;
}
.switch-button label:before {
    position: absolute;
    font-size: 11px;
    font-weight: 600;
    z-index: 0;
    content: "OFF";
    right: 0;
    display: block;
    width: 100%;
    height: 100%;
    line-height: 27px;
    top: 0;
    text-align: right;
    padding-right: 10px;
    color: #ffffff;
}
.switch-button.switch-button-xs label:before {
    line-height: 21px;
}
.switch-button input[type="checkbox"]:checked + span label {
    float: right;
    border-color: #2a75f3;
}
.switch-button input[type="checkbox"]:checked + span {
    background-color: #4285f4;
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 4px;
}
.switch-button input[type="checkbox"]:checked + span label:before {
    position: absolute;
    font-size: 11px;
    font-weight: 600;
    z-index: 0;
    content: "ON";
    color: #ffffff;
    left: 0;
    text-align: left;
    padding-left: 10px;
}
.switch-button label:before {
    position: absolute;
    font-size: 11px;
    font-weight: 600;
    z-index: 0;
    content: "OFF";
    right: 0;
    display: block;
    width: 100%;
    height: 100%;
    line-height: 27px;
    top: 0;
    text-align: right;
    padding-right: 10px;
    color: #ffffff;
}
.switch-button label {
    border-radius: 50%;
    box-shadow: 0 0 1px 1px #FFF inset;
    background-color: #fff;
    margin: 0;
    height: 19px;
    width: 19px;
    z-index: 1;
    display: inline-block;
    cursor: pointer;
    background-clip: padding-box;
}
.switch-button input[type="checkbox"] {
    display: none;
}
.switch-button label {
    border-radius: 50%;
    box-shadow: 0 0 1px 1px #FFF inset;
    background-color: #fff;
    margin: 0;
    height: 10px;
    width: 10px;
    z-index: 1;
    display: inline-block;
    cursor: pointer;
    background-clip: padding-box;
}
#EditProfile i{
  line-height:20px;
}
#cancelProfile i{
  line-height:20px;
}

</style>
<div class="card">
  <div class="card-header card-header-rose card-header-icon">
    <div class="card-icon">
      <i class="icon-profile"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">My Profile</h4>
      </div>
    </div>
  </div>

  <div class="card-body">
   <div class="My_Profile">
    <div class="row">
      <div class="col-md-3">
        <div class="avatar-upload">
          <div class="avatar-preview hover14">
            <div class="imagePreview" style="background-image: url('<?php echo($UserDetails->Avatar ? $UserDetails->Avatar : "assets/img/avatar7.png"); ?>');">
            </div>
          </div>
          <h6 class="text-center" style="margin:5px;">
            <span class="name"><span class="mdi mdi-account"></span> <?php echo $UserDetails->UserName;?> </span><br>
            <span class="nick"  style="color: #66b2dd;"> ( <?php echo $RoleName->RoleName;?> )</span>
          </h6>
        </div>
      </div>

      <div class="col-md-8"> 
        <div class="table-responsive" style="margin-top:20px;">
          <table id="TaxExemptionTable" class="table" >
            <tr style="border-bottom: 1px solid #ccc;">
              <td width="10%" colspan="2">

                <div class="widget-head" style="margin-bottom: 0;">
                  <div class="tools">
                    <button class="btn btn-link btn-info btn-just-icon btn-xs pull-right" id="EditProfile">
                      <i class="fa fa-pencil-square-o " aria-hidden="true"></i>
                    </button>                 
                  </div>
                  <div ><strong><i class="fa fa-user-circle-o" aria-hidden="true"></i> About Me </strong></div>
                </div>

              </td>
            </tr>

            <tr>
              <td width="10%">
                <span class="fa fa-user" ></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Name <span style="color: red"> *</span></strong></label>
              </td>

              <td >
                <label class="size"><strong>: &nbsp;&nbsp;&nbsp;<?php echo $UserDetails->UserName?> </strong></label>
              </td>
            </tr>

            <tr>
              <td>
                <span class="fa fa-sign-in" ></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Login ID <span style="color: red"> *</span></strong></label>
              </td>

              <td >
                <label class="size"><strong>: &nbsp;&nbsp;&nbsp;<?php echo $UserDetails->LoginID?> </strong></label>
              </td>
            </tr>

            <tr>
              <td>
                <span class="fa fa-envelope"></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Email ID <span style="color: red"> *</span></strong></label>
              </td>

              <td >
                <label class="size"><strong>: &nbsp;&nbsp;&nbsp;<?php echo $UserDetails->EmailID?> </strong></label>
              </td>
            </tr>

            <tr>
              <td>
                <span class="fa fa-phone" style="font-size: 15px"></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Contact No </strong></label>
              </td>

              <td >
                <label class="size"><strong>: &nbsp;&nbsp;&nbsp;<?php echo $UserDetails->ContactNo?> </strong></label>
              </td>
            </tr>

            <tr>
              <td>
                <span class="fa fa-fax" ></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Fax No </strong></label>
              </td>

              <td >
                <label class="size"><strong>: &nbsp;&nbsp;&nbsp;<?php echo $UserDetails->FaxNo?> </strong></label>
              </td>
            </tr>
            
          </table>
        </div>
      </div>

    </div>
  </div>
  <div class="My_Profile_Edit" style="display: none;">
    <div class="panel panel-default panel-border-color panel-border-color-primary">
      <form action="#" name="frm_user" id="frm_user">
        <input type="hidden" name="UserUID" id="UserUID" value="<?php echo $UserDetails->UserUID?>" />
        <div class="row">
          <div class="col-md-3">

            <div class="avatar-upload">
              <div class="avatar-edit">
                <input type='file' id="imageUpload" name="image_upload" accept=".png, .jpg, .jpeg" />
                <label for="imageUpload"></label>
              </div>
              <div class="avatar-preview hover14">
                <div class="imagePreview" style="background-image: url('<?php echo($UserDetails->Avatar ? $UserDetails->Avatar : "assets/img/avatar7.png"); ?>');">
                </div>
              </div>
              <h6 class="text-center" style="margin:5px;">
                <span class="name"><span class="mdi mdi-account"></span> <?php echo $UserDetails->UserName;?> </span><br>
                <span class="nick"  style="color: #66b2dd;"> ( <?php echo $RoleName->RoleName;?> )</span>
              </h6>
            </div>


          </div>

          <div class="col-md-8"> 
            <div class="table-responsive" style="margin-top:20px;">
              <table id="TaxExemptionTable" class="table" >
                <tr style="border-bottom: 1px solid #ccc;">
                  <td width="10%" colspan="2">
                    <div class="widget-head" style="margin-bottom: 0;">
                      <div class="tools">
                        <button class="btn btn-link btn-danger btn-just-icon btn-xs pull-right" id="cancelProfile">
                          <i class="fa fa-times" aria-hidden="true"></i>
                        </button>                 
                      </div>
                      <div class=""><strong><i class="fa fa-user-circle-o" aria-hidden="true"></i> About Me </strong></div>
                    </div>


                  </td>
                </tr>

                <tr>
                  <td width="10%">
                    <span class="fa fa-user" ></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Name <span style="color: red"> *</span></strong></label>
                  </td>

                  <td class="bmd-form-group">
                    <input class="form-control" type="text" id="UserName" name="UserName" value="<?php echo $UserDetails->UserName?>">
                  </td>
                </tr>

                <tr>
                  <td>
                    <span class="fa fa-sign-in" ></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Login ID <span style="color: red"> *</span></strong></label> 
                  </td>

                  <td >
                     <input class="form-control" type="text" id="LoginID" name="LoginID" value="<?php echo $UserDetails->LoginID?>">
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="fa fa-envelope" ></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Email ID <span style="color: red"> *</span></strong></label> 
                  </td>

                  <td >
                     <input class="form-control" type="text" id="EmailID" name="EmailID" value="<?php echo $UserDetails->EmailID?>">
                  </td>
                </tr>

                <tr>
                  <td>
                    <span class="fa fa-phone" style="font-size: 15px" ></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Contact No</strong></label> 
                  </td>

                  <td >
                    <input class="form-control" type="text" id="ContactNo" name="ContactNo" data-inputmask="'mask': '([9{4}]) [9{4}]-[9{0,2}]'" value="<?php echo $UserDetails->ContactNo?>">
                  </td>
                </tr>

                <tr>
                  <td>
                    <span class="fa fa-fax"></span> &nbsp;&nbsp;&nbsp;<label class="size"><strong>Fax No </strong></label> : 
                  </td>

                  <td >
                     <input class="form-control" type="text" id="FaxNo" name="FaxNo" data-inputmask="'mask': '([9{4}]) [9{4}]-[9{0,2}]'" value="<?php echo $UserDetails->FaxNo?>">
                  </td>
                </tr>
               
              </table>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <div class="col-sm-6 pull-right">  
              <div class="col-sm-12 form-group">
                <p class="text-right">
                  <button class="btn btn-space btn-social btn-color btn-info" id="BtnCancel">Cancel</button>
                  <button class="btn btn-space btn-social btn-color btn-twitter" id="BtnUpdateUser" value="1">Save User</button> 
                </p>                 
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php if($RoleType == 15): $this->load->view('edit_abstractor',array($abstractor_login,$AbstractorDetails,$AbstractorDocumentDetails,$document,$subdocument,$States,$Cities,$Counties)); endif; ?>
</div>

<div class="be-content">
	<div class="main-content container-fluid">
		

		


  </div>
</div>

<div class="fixed-plugin">
  <div class="dropdown show-dropdown pd-10">
    <a href="JavaScript:Void(0);" data-toggle="dropdown">
      <i class="ion-android-settings" style="color:#fff"> </i>
    </a>
    <ul class="dropdown-menu">
      <li class="header-title"> Sidebar Filters</li>
      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger active-color">
          <div class="badge-colors ml-auto mr-auto">
            <span class="badge filter badge-purple" data-color="purple" data-id="1"></span>
            <span class="badge filter badge-azure active" data-color="azure" data-id="2"></span>
            <span class="badge filter badge-green" data-color="green" data-id="3"></span>
            <span class="badge filter badge-warning" data-color="orange" data-id="4"></span>
            <span class="badge filter badge-danger" data-color="danger"  data-id="5"></span>
            <span class="badge filter badge-rose " data-color="rose"  data-id="6"></span>
          </div>
          <div class="clearfix"></div>
        </a>
      </li>
      <li class="header-title">Sidebar Background</li>
      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="ml-auto mr-auto">
            <span class="badge filter badge-black active" data-background-color="black" data-id="1"></span>
            <span class="badge filter badge-white" data-background-color="white" data-id="2"></span>
            <span class="badge filter badge-red" data-background-color="red" data-id="3"></span>
          </div>
          <div class="clearfix"></div>
        </a>
      </li>
      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger">
          <p>Sidebar Mini</p>
          <label class="ml-auto">
            <div class="togglebutton switch-sidebar-mini">
              <label>
                <input type="checkbox">
                <span class="toggle"></span>
              </label>
            </div>
          </label>
          <div class="clearfix"></div>
        </a>
      </li>

      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger">
          <p>Sidebar Images</p>
          <label class="switch-mini ml-auto">
            <div class="togglebutton switch-sidebar-image">
              <label>
                <input type="checkbox" checked="">
                <span class="toggle"></span>
              </label>
            </div>
          </label>
          <div class="clearfix"></div>
        </a>
      </li>
      <li class="header-title">Images</li>
      <li class="active backgroundimage" data-background="1">
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-1.jpg" alt="" data-id="1">
        </a>
      </li>
      <li data-background="2" class="backgroundimage">
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-2.jpg" alt="" data-id="2">
        </a>
      </li>
      <li data-background="3" class="backgroundimage">
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-3.jpg" alt="" data-id="3">
        </a>
      </li>
      <li data-background="4" class="backgroundimage">
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-4.jpg" alt="" data-id="4">
        </a>
      </li>
    </ul>
  </div>
</div>

<script type="text/javascript">
 
var CurrentURL = window.location.href;

 $('.My_Profile_Edit').hide();
 $('.My_Profile').show();


 $('#EditProfile').click(function(event) {
  $('.My_Profile_Edit').show();
  $('.My_Profile').hide();

});
 $(document).ready(function() {
 Inputmask().mask(document.querySelectorAll("input"));
  md.initFormExtendedDatetimepickers();
  // $('#upload_file').dropify();
});
 $('#BtnCancel').click(function(event) {

  event.preventDefault();

  $('.My_Profile_Edit').hide();
  $('.My_Profile').show();

});

 $('#cancelProfile').click(function(event) {

  event.preventDefault();

  $('.My_Profile_Edit').hide();
  $('.My_Profile').show();

});

 $('#BtnUpdateUser').click(function(event) {
  button = $(this);
  button_val = $(this).val();
  button_text = $(this).html();
  /*form*/
  var data = new FormData();
  var form_data = $('#frm_user').serializeArray();
  $.each(form_data, function (key, input) {
   data.append(input.name, input.value);
 });
			//File data
     var abs_upload_file =  $('input[name="image_upload"]')[0].files;
     for (var i = 0; i < abs_upload_file.length; i++) {
      data.append("image_upload[]", abs_upload_file[i]);
    }
    $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Profile/update_user',
     data: data, 
     dataType:'json',
     processData: false,
     contentType: false,
     cache: false,
     beforeSend: function(){
      button.attr("disabled", true);
      button.html('Loading ...'); 
    },
    success: function(data)
    {
      console.log(data);
      if(data['validation_error'] == 0){
        $('.imagePreview').css('background-image', 'url('+data['ImageURL']+')');
         $('.imagePreview1').attr('src', data['ImageURL']);
        $.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:1000 });
        setTimeout(function(){ 
                    triggerpage(CurrentURL);
                  }, 3000);
             
     
     }else{
     
       $.notify({icon:"icon-bell-check",message:data['message']},{type:"danger",delay:1000 });
        setTimeout(function(){ 
                    triggerpage(CurrentURL);
                  }, 3000);
       button.html(button_text);
       button.removeAttr("disabled");
       $.each(data, function(k, v) {
        $('#'+ k +'.select2').next().find('span.select2-selection').addClass('errordisplay');
        $('#'+k).closest('div.is_error').addClass('is-invalid');
      });
     }
   },
   error: function (jqXHR, textStatus, errorThrown) {
    console.log(errorThrown);
  },
  failure: function (jqXHR, textStatus, errorThrown) {
    console.log(errorThrown);
  },
});
    event.preventDefault();
    return false;
  });



 function readURL(input) {
  if (input.files && input.files[0]) {
   var reader = new FileReader();
   reader.onload = function(e) {
    $('.imagePreview').css('background-image', 'url('+e.target.result +')');
    $('.imagePreview').hide();
    $('.imagePreview').fadeIn(650);
  }
  reader.readAsDataURL(input.files[0]);
}
}
$("#imageUpload").change(function() {
  readURL(this);
});


jQuery(document).ready(function($) {
  $(document).ready(function() {

    $('.contactnum').mask('(999) 999-9999');
    $("body").on("keyup" , ".contactnum" , function(e){
      if(46==e.keyCode || 8==e.keyCode || 9==e.keyCode){
        var $this = $(this);
        if($this.val() == "(___)___-____")
          $this.val("");
      }
    });
    // Sidebar
    $sidebar = $('.sidebar');
    $sidebar_img_container = $sidebar.find('.sidebar-background');
    $full_page = $('.full-page');
    $sidebar_responsive = $('body > .navbar-collapse');
    window_width = $(window).width();
    fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();
    $('.fixed-plugin a').click(function(event) {
      // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
      if ($(this).hasClass('switch-trigger')) {
        if (event.stopPropagation) {
          event.stopPropagation();
        } else if (window.event) {
          window.event.cancelBubble = true;
        }
      }
    });

    $.ajax({
      type : "GET",
      url : "<?php echo base_url();?>Profile/getuserDetails",
      async :  false,
      success :  function(data){
       profiledata =  JSON.parse(data);
       ProfileColor = profiledata.ProfileColor;
       ProfileBackground = profiledata.ProfileBackground;
       SidebarActive = profiledata.SidebarActive;
       SidebarBackgroundActive = profiledata.SidebarBackgroundActive;
       SidebarBackground = profiledata.SidebarBackground;
       $(".active-color span").removeClass("active");
       $(".backgroundimage").removeClass("active");
       $(".active-color span[data-id="+ProfileColor+"]").addClass("active");
       $(".background-color span").removeClass("active");
       $(".background-color span[data-id="+ProfileBackground+"]").addClass("active");
       $(".backgroundimage[data-background="+SidebarBackground+"]").addClass("active");
       if(ProfileBackground == "1"){
        activeProfileBackground = "black";
      }else if(ProfileBackground == "2"){
        activeProfileBackground = "white";
      }else if(ProfileBackground == "3"){
        activeProfileBackground = "red";
      }
      if(SidebarBackground == "1"){
        activeSidebarBackground = "assets/img/sidebar-1.jpg";
      }else if(SidebarBackground == "2"){
        activeSidebarBackground = "assets/img/sidebar-2.jpg";
      }else if(SidebarBackground == "3"){
        activeSidebarBackground = "assets/img/sidebar-3.jpg";
      }else if(SidebarBackground == "4"){
        activeSidebarBackground = "assets/img/sidebar-4.jpg";
      }


      if(SidebarBackgroundActive == "0"){
        $(".switch-sidebar-image input").attr("checked" , false);
      }else{
        $(".switch-sidebar-image input").attr("checked" , true);
      }
      if(SidebarActive == "1"){
        $(".switch-sidebar-mini input").attr("checked" , false);
      }else{
        $(".switch-sidebar-mini input").attr("checked" , true); 
        $("body").addClass("sidebar-mini");
      }
      if(SidebarBackgroundActive == "1"){
        $(".sidebar-background").css('background-image' , 'url("' + activeSidebarBackground + '")');
        $(".sidebar").attr("data-image" , activeSidebarBackground);
      }else{
        $(".sidebar").removeAttr("data-image");
        $("sidebar-background").hide();
      }
    }
  });


    $('.fixed-plugin .active-color span').click(function() {      
      $full_page_background = $('.full-page-background');
      $(this).siblings().removeClass('active');
      $(this).addClass('active');
      var new_color = $(this).data('color');     
      var profileColor = $(this).attr('data-id');   
      $.ajax({
        type : "POST",
        url : "<?php echo  base_url();?>Profile/ProfileSettingsUpdate",
        async : false,
        cache : false,
        data : {"ProfileColor" : profileColor},
        success : function(data){           
          parseresponse  = JSON.parse(data);
          if(parseresponse.Status  == 1){
            $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"success",delay:1000 });
            //loadbackground();
            // setTimeout(function() {
            //   window.location.href = "<?php echo base_url();?>Profile";
            // }, 1000);

          }else{
            $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"danger",delay:1000 });
          }          
        }
      });
      if ($sidebar.length != 0) {
        $sidebar.attr('data-color', new_color);
        $("#headers").attr("class" , "");
        $("#headers").addClass(new_color);
      }
      if ($full_page.length != 0) {
        $full_page.attr('filter-color', new_color);
      }
      if ($sidebar_responsive.length != 0) {
        $sidebar_responsive.attr('data-color', new_color);
      }
    });






    $('.fixed-plugin .background-color .badge').click(function() {       
      $(this).siblings().removeClass('active');
      $(this).addClass('active');
      var new_color = $(this).data('background-color');  
      var ProfileBackground = $(this).attr('data-id');        
      $.ajax({
        type : "POST",
        url : "<?php echo  base_url();?>Profile/ProfileSettingsUpdate",
        async : false,
        cache : false,
        data : {"ProfileBackground" : ProfileBackground},
        success : function(data){
          parseresponse  = JSON.parse(data);
          if(parseresponse.Status  == 1){
            $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"success",delay:1000 });
          }else{
            $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"danger",delay:1000 });
          }          
        }
      });
      if ($sidebar.length != 0) {
        $sidebar.attr('data-background-color', new_color);
      }
    });




    $('.fixed-plugin .img-holder').click(function() {
      $full_page_background = $('.full-page-background');
      $(this).parent('li').siblings().removeClass('active');
      $(this).parent('li').addClass('active');
      var new_image = $(this).find("img").attr('src');
      var image_id = $(this).find("img").attr('data-id');
      // alert(image_id);
      $full_page_background = $('.full-page-background');
      $input = $(".switch-sidebar-image input");
      if ($input.is(':checked')) {
       $(".sidebar").attr("data-image" , new_image);
       $(".sidebar-background").css("background-image" , 'url("' + new_image + '")');
       $.ajax({
        type : "POST",
        url : "<?php echo  base_url();?>Profile/ProfileSettingsUpdate",
        async : false,
        cache : false,
        data : {"SidebarBackground" : image_id},
        success : function(data){
          parseresponse  = JSON.parse(data);
          if(parseresponse.Status  == 1){
            $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"success",delay:1000 });
          }else{
            $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"danger",delay:1000 });
          }          
        }
      });
     }
   });


    $('.switch-sidebar-image input').change(function() {
      $full_page_background = $('.full-page-background');
      $input = $(this);
      var SidebarBackgroundActive = '';
      if ($input.is(':checked')) {
        if ($sidebar_img_container.length != 0) {
          $sidebar_img_container.fadeIn('fast');
          $sidebar.attr('data-image', '#');
        }
        if ($full_page_background.length != 0) {
          $full_page_background.fadeIn('fast');
          $full_page.attr('data-image', '#');
        }
        background_image = true;
        SidebarBackgroundActive = "1";
      } else {
        if ($sidebar_img_container.length != 0) {
          $sidebar.removeAttr('data-image');
          $sidebar_img_container.fadeOut('fast');
        }
        if ($full_page_background.length != 0) {
          $full_page.removeAttr('data-image', '#');
          $full_page_background.fadeOut('fast');
        }
        background_image = false;
        SidebarBackgroundActive = "0";
      } 
      $.ajax({
        type : "POST",
        url : "<?php echo  base_url();?>Profile/ProfileSettingsUpdate",
        async : false,
        cache : false,
        data : {"SidebarBackgroundActive" : SidebarBackgroundActive},
        success : function(data){
          parseresponse  = JSON.parse(data);
          if(parseresponse.Status  == 1){
            $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"success",delay:1000 });

          }else{
            $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"danger",delay:1000 });
          }          
        }
      }); 
    });




    $('.switch-sidebar-mini input').change(function() {
      //alert("switch")
      var SidebarActive = '';
      $body = $('body');
      $input = $(this);
      if ($input.is(':checked')) {
        SidebarActive = "0";
      }else{
        SidebarActive = "1";
      }
      if (SidebarActive == '1') {
        $('body').removeClass('sidebar-mini');
        $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();
      } else {
       $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');
       setTimeout(function() {
        $('body').addClass('sidebar-mini');
      }, 300);
     }
     //  alert(SidebarActive);
     $.ajax({
      type : "POST",
      url : "<?php echo  base_url();?>Profile/ProfileSettingsUpdate",
      async : false,
      cache : false,
      data : {"SidebarActive" : SidebarActive},
      success : function(data){
        parseresponse  = JSON.parse(data);
        if(parseresponse.Status  == 1){
          $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"success",delay:1000 });
        }else{
          $.notify({icon:"icon-bell-check",message:parseresponse.Message},{type:"danger",delay:1000 });
        }          
      }
    }); 
     // we simulate the window Resize so the charts will get updated in realtime.
     var simulateWindowResize = setInterval(function() {
      window.dispatchEvent(new Event('resize'));
    }, 180);
     // we stop the simulation of Window Resize after the animations are completed
     setTimeout(function() {
      clearInterval(simulateWindowResize);
    }, 1000);
   });
    // $('#upload_file').dropify();
  });


});

</script>